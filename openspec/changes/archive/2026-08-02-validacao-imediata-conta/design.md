## Context

Ver `proposal.md` — Why. O que importa para o desenho é o estado atual do código:

- [Registro.jsx:146](resources/js/Pages/Candidato/Auth/Registro.jsx#L146) já mantém `cpfStatus` com os cinco estados (`idle | checking | ok | invalido | duplicado`), e [Registro.jsx:219-231](resources/js/Pages/Candidato/Auth/Registro.jsx#L219-L231) já consulta o endpoint. A infraestrutura existe; o que falta é *quando* ela roda e *se* o resultado aparece.
- O gargalo real é [Registro.jsx:192-194](resources/js/Pages/Candidato/Auth/Registro.jsx#L192-L194): `erro()` só devolve o erro local se `tentou[etapa]` for verdadeiro, e `tentou` só é marcado dentro de `avancar()`. Antes do primeiro clique em "Continuar", **nenhum** erro local da etapa aparece — inclusive `invalido` e `duplicado`, que são calculados e descartados.
- `verificarCpfDisponivel` está ligado ao `onBlur` do campo ([Registro.jsx:310](resources/js/Pages/Candidato/Auth/Registro.jsx#L310)). Na captura de tela o CPF está completo e válido e não há retorno algum, porque o foco nunca saiu do campo.
- [Field.jsx:14-18](resources/js/components/Field.jsx#L14-L18) só tem dois canais: `error` (destructive) e `hint` (muted). "CPF disponível." e "As senhas coincidem." são sucessos renderizados em cinza de texto de apoio.
- `PasswordStrengthMeter` e `REGRAS_SENHA` já cobrem os cinco critérios e espelham exatamente `Password::min(8)->mixedCase()->numbers()->symbols()` do [CandidatoRegistroRequest.php:34](app/Http/Requests/Auth/CandidatoRegistroRequest.php#L34).
- `verificarCpf()` no controller responde `{existe: bool}` e não tem `throttle` na rota.
- Login e recuperação de senha identificam por **e-mail**, não por CPF — não há como levar o CPF digitado para essas telas.

## Goals / Non-Goals

**Goals:**

- Trocar o gatilho da validação de "tentou avançar" para "campo preenchido", por campo, sem transformar o formulário em um mar de vermelho durante a digitação.
- Fazer os estados de CPF já existentes (`invalido`, `duplicado`) chegarem à tela.
- Dar ao `Field` um terceiro canal de mensagem para que sucesso não pareça texto de apoio.

**Non-Goals:**

- Não mexer nas etapas 2 a 6 nem no `tentou[etapa]` que as governa — a mudança de gatilho fica restrita à etapa 0.
- Não alterar as regras de validação server-side, que continuam sendo a autoridade final.
- Não unificar login por CPF, nem propagar CPF para telas de login/recuperação.
- Não trocar a biblioteca de formulário nem introduzir dependência de validação (Zod, react-hook-form).

## Decisions

### 1. Gatilho por campo: "sujo e completo", em vez de `tentou[etapa]`

O critério para exibir erro passa a ser por campo, não por etapa. Um campo mostra erro quando **já foi tocado** (perdeu o foco ao menos uma vez) **ou** quando o valor está semanticamente completo — para o CPF, 11 dígitos.

Estado novo: `tocado`, um `{ [campo]: true }` preenchido no `onBlur` de cada campo da etapa 0. A função `erro()` ganha uma variante para a etapa 0:

```
erroCampo(campo) = serverErrors[campo] ?? ((tocado[campo] || tentou[etapa]) ? locais[campo] : undefined)
```

`tentou[etapa]` continua no OR para que o clique em "Continuar" ainda revele tudo de uma vez.

O CPF é a exceção que justifica o "ou completo": com 11 dígitos digitados o veredito é definitivo e esperar o blur é exatamente o defeito relatado. A verificação local roda no `onChange`; o erro "CPF inválido." aparece sem esperar `tocado`.

**Alternativas consideradas:** (a) validar tudo no `onChange` desde o primeiro caractere — rejeitado, marca "e-mail inválido" na primeira letra digitada; (b) manter só `onBlur` — rejeitado, é o comportamento atual que o usuário pediu para mudar; (c) `tentou` global por campo em todas as etapas — rejeitado por ampliar o escopo para telas que não foram reportadas.

### 2. Debounce de 400 ms para a consulta de CPF, disparada por efeito

A consulta ao servidor sai do `onBlur` e passa para um `useEffect` que observa os dígitos do CPF. O efeito:

1. Se `< 11` dígitos → `cpfStatus = 'idle'`, sem consulta (mantém o campo silencioso enquanto incompleto).
2. Se 11 dígitos e `validarCpf` falha → `cpfStatus = 'invalido'`, sem consulta — não se gasta requisição com CPF que nem passa nos dígitos verificadores.
3. Se 11 dígitos e válido → `cpfStatus = 'checking'`, `setTimeout` de 400 ms, depois `fetch`.

O cleanup do efeito cancela o timer e marca a resposta como obsoleta, para que uma resposta antiga não sobrescreva o estado de um CPF já editado (o clássico race de autocomplete). O `AbortController` acompanha o `fetch` pelo mesmo motivo.

400 ms é o intervalo usual para "parou de digitar" sem parecer travado; como o gatilho só arma com 11 dígitos válidos, na prática dispara uma vez por CPF.

O `onBlur` é mantido como gatilho redundante para o caso de o CPF ser colado e o campo perder o foco antes dos 400 ms.

**Alternativa considerada:** debounce dentro do `onChange` com `useRef` de timer — funciona, mas o cleanup do `useEffect` já dá cancelamento correto de graça, sem gerenciar o ref na mão.

### 3. `Field` ganha `success`, um terceiro canal

Precedência: `error` > `success` > `hint`. Sucesso em `text-emerald-600 dark:text-emerald-400` — o mesmo par já usado pelo `PasswordStrengthMeter` para regra cumprida, mantendo a leitura consistente entre os dois componentes.

`hint` fica para informação neutra ("Verificando CPF…", "Buscando endereço…"). Nenhum uso atual de `Field` quebra: a prop é opcional e a ordem existente `error → hint` é preservada.

**Alternativa considerada:** passar um nó React pronto em `hint` com a cor embutida — rejeitado, espalha decisão de cor pelas telas e deixa a precedência implícita.

### 4. Aviso de CPF duplicado com os dois atalhos dentro da mensagem

`duplicado` não é um erro de digitação: a informação útil é o caminho de saída. Em vez do texto simples de `Field`, um bloco próprio abaixo do campo com a mensagem e os dois links (Entrar / Esqueci minha senha).

Login e recuperação pedem e-mail, e quem esqueceu que já tem conta provavelmente não lembra qual e-mail usou — por isso a recuperação de senha aparece ao lado do login, e não escondida atrás dele. O CPF não é propagado (Decisão: não há campo de CPF nessas telas).

O `Field` do CPF recebe `error` vazio nesse caso para não duplicar a mensagem; o bloco assume a sinalização e o `aria-describedby` do input aponta para ele.

### 5. Senha: erro no blur, checklist sempre; confirmação: erro no blur, sucesso ao vivo

A senha **não** ganha erro no `onChange`: o `PasswordStrengthMeter` já dá retorno contínuo por critério, e marcar "senha fraca" em vermelho no terceiro caractere de uma senha em construção é ruído. A mensagem agregada "A senha não atende a todos os requisitos." entra pela regra de `tocado` da Decisão 1 — ou seja, ao sair do campo.

A confirmação segue a mesma regra para o erro, mas o sucesso ("As senhas coincidem.") continua ao vivo no `onChange`, porque confirmação correta é informação de conclusão, não de correção.

`forcaSenha` já é recalculado a cada render sobre `data.password`, e `locais` já depende de `data` — editar a senha depois de confirmar reavalia a igualdade sem trabalho adicional.

### 6. `throttle:30,1` na rota e resposta explícita para CPF inválido

A rota `candidato.registro.verificar-cpf` é um oráculo de "este CPF tem conta?" aberto sem limite. Isso já existe hoje e não foi introduzido aqui, mas como esta mudança aumenta o tráfego do endpoint (passa a disparar durante a digitação), o `throttle:30,1` entra junto — 30 consultas por minuto por IP é folgado para uso legítimo, já que o formulário faz ~1 por cadastro, e corta enumeração em massa.

Vale registrar o trade-off: **qualquer** aviso de "CPF já cadastrado" na tela de cadastro é, por construção, divulgação de existência de conta. O comportamento foi pedido explicitamente e é o padrão do mercado brasileiro para cadastro por CPF; o `throttle` limita o abuso, não o elimina. Anonimizar (ex.: "prossiga; se já tiver conta, enviaremos um e-mail") eliminaria o vazamento, mas destruiria justamente a usabilidade que a mudança busca.

O controller passa a validar os dígitos verificadores antes de consultar a base, respondendo `{existe: false, valido: false}` para CPF malformado — evita ida ao banco e alinha servidor e cliente na mesma regra.

## Risks / Trade-offs

- **Resposta obsoleta sobrescreve estado de CPF já editado** → cleanup do `useEffect` invalida a resposta em voo e `AbortController` cancela o `fetch`; o `setCpfStatus` só ocorre se o efeito ainda estiver vigente.
- **`throttle` disparando para usuário legítimo em rede compartilhada (NAT institucional)** → o catch do `fetch` já trata falha como "não bloquear" (`cpfStatus` não vira `duplicado`), então o candidato prossegue e a unicidade é garantida no envio. 30/min por IP foi escolhido com folga por isso.
- **Divulgação de existência de conta por CPF** → mitigado por `throttle`, não eliminado; decisão consciente registrada na Decisão 6.
- **Validação client-side dessincronizar da server-side** → `REGRAS_SENHA` e `Password::min(8)->mixedCase()->numbers()->symbols()` são hoje equivalentes mas mantidos em lugares distintos; a spec fixa a equivalência como requisito e a tarefa de teste cobre os dois lados.
- **Ruído visual durante a digitação** → mitigado por não marcar erro antes do blur em e-mail/senha, e por manter o campo silencioso enquanto o CPF tem menos de 11 dígitos.

## Migration Plan

Mudança puramente de front-end mais uma linha de rota; sem migration, sem alteração de dados, sem breaking change de API (`verificarCpf` só ganha um campo adicional na resposta). Rollback é reverter o commit — nenhum estado persistido é afetado.
