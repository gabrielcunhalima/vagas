## 1. Backend: endpoint de verificação de CPF

- [x] 1.1 Em `app/Http/Controllers/Auth/CandidatoRegistroController.php`, extrair a validação de dígitos verificadores para uso no `verificarCpf()` — reaproveitar o trait `App\Http\Requests\Concerns\ValidaCpf` em vez de reimplementar o algoritmo
- [x] 1.2 Fazer `verificarCpf()` responder `{existe: false, valido: false}` quando o CPF não tiver 11 dígitos ou falhar nos dígitos verificadores, sem consultar a tabela `candidatos`
- [x] 1.3 Fazer `verificarCpf()` responder `{existe: bool, valido: true}` para CPF bem formado, mantendo a chave `existe` já consumida pelo front
- [x] 1.4 Aplicar `->middleware('throttle:30,1')` na rota `candidato.registro.verificar-cpf` em `routes/web.php`

## 2. Backend: testes

- [x] 2.1 Adicionar teste de feature: CPF válido sem conta → `{existe: false, valido: true}`
- [x] 2.2 Adicionar teste de feature: CPF de candidato existente → `{existe: true, valido: true}`
- [x] 2.3 Adicionar teste de feature: CPF com dígitos verificadores errados e CPF com menos de 11 dígitos → `{valido: false}` e nenhuma consulta positiva
- [x] 2.4 Adicionar teste de feature: consultas acima do limite na janela retornam HTTP 429
- [x] 2.5 Adicionar teste de feature: `POST /minha-conta/cadastro` com CPF já cadastrado retorna erro de validação no campo `cpf` (garante que o servidor segue sendo a autoridade final)
- [x] 2.6 Rodar `php artisan test --filter=Auth` e confirmar que a suíte existente segue verde

## 3. Componente `Field`: canal de sucesso

- [x] 3.1 Adicionar a prop opcional `success` em `resources/js/components/Field.jsx`
- [x] 3.2 Implementar a precedência `error` > `success` > `hint`, com `success` em `text-xs font-medium text-emerald-600 dark:text-emerald-400`
- [x] 3.3 Verificar que os usos atuais de `Field` (Login, EsqueciSenha, RedefinirSenha, Registro, Candidatura) seguem renderizando como antes

## 4. Registro: gatilho de exibição de erro por campo

- [x] 4.1 Adicionar o estado `tocado` (`{ [campo]: true }`) em `Registro.jsx` e preenchê-lo no `onBlur` dos campos da etapa 0 (`cpf`, `email`, `password`, `password_confirmation`)
- [x] 4.2 Criar `erroCampo(campo)` com a regra `serverErrors[campo] ?? ((tocado[campo] || tentou[etapa]) ? locais[campo] : undefined)` e usá-la nos campos da etapa 0
- [x] 4.3 Manter `erro()` inalterada nas etapas 1 a 5 — nenhuma outra etapa muda de comportamento
- [x] 4.4 Limpar `tocado[campo]` do campo de senha quando a senha for editada novamente, para não manter erro obsoleto durante a correção

## 5. Registro: verificação de CPF durante a digitação

- [x] 5.1 Substituir a chamada de `verificarCpfDisponivel` no `onBlur` por um `useEffect` que observa `onlyDigits(data.cpf)`
- [x] 5.2 No efeito: menos de 11 dígitos → `cpfStatus = 'idle'` e nenhuma consulta
- [x] 5.3 No efeito: 11 dígitos com `validarCpf` falhando → `cpfStatus = 'invalido'` e nenhuma consulta ao servidor
- [x] 5.4 No efeito: 11 dígitos válidos → `cpfStatus = 'checking'` e consulta após debounce de 400 ms
- [x] 5.5 Implementar o cleanup do efeito cancelando o timer e abortando o `fetch` em voo (`AbortController`), garantindo que resposta obsoleta não sobrescreva o estado de um CPF já editado
- [x] 5.6 Manter falha de rede/HTTP não-2xx como não-bloqueante: não marcar `duplicado`, deixar a unicidade para o envio final
- [x] 5.7 Manter o `onBlur` como gatilho redundante para CPF colado com saída de foco antes dos 400 ms

## 6. Registro: exibição dos estados de CPF

- [x] 6.1 Exibir "CPF inválido." como erro do campo quando `cpfStatus === 'invalido'`
- [x] 6.2 Exibir "Verificando CPF…" como `hint` neutro quando `cpfStatus === 'checking'`
- [x] 6.3 Exibir "CPF disponível." pelo novo canal `success` quando `cpfStatus === 'ok'`
- [x] 6.4 Renderizar, para `cpfStatus === 'duplicado'`, um bloco abaixo do campo com a mensagem "Este CPF já está cadastrado." e os links **Entrar** (`candidato.login`) e **Esqueci minha senha** (`candidato.senha.request`)
- [x] 6.5 Ligar o bloco de duplicado ao input via `aria-describedby` e não duplicar a mensagem no `error` do `Field`

## 7. Registro: senha e confirmação

- [x] 7.1 Exibir "A senha não atende a todos os requisitos." assim que o campo de senha perder o foco com `forcaSenha < REGRAS_SENHA.length` (via regra da tarefa 4.2), sem marcar erro durante a digitação
- [x] 7.2 Exibir "As senhas não coincidem." assim que o campo de confirmação perder o foco com valor divergente
- [x] 7.3 Migrar "As senhas coincidem." do canal `hint` para o canal `success`, mantendo a exibição ao vivo no `onChange`
- [x] 7.4 Confirmar que editar a senha após preencher a confirmação reavalia a igualdade e atualiza a mensagem exibida

## 8. Registro: bloqueio de avanço

- [x] 8.1 Incluir `cpfStatus === 'invalido'` e `cpfStatus === 'duplicado'` como bloqueios explícitos em `validarEtapa(0)`, junto do `validarCpf` local já existente
- [x] 8.2 Manter `cpfStatus === 'checking'` bloqueando o avanço em `avancar()`, preservando a indicação de verificação em andamento
- [x] 8.3 Garantir que, ao bloquear, a razão fique visível no campo correspondente (marcar `tocado` dos campos da etapa ao clicar em "Continuar")

## 9. Verificação

- [x] 9.1 Rodar `npm run build` e confirmar que não há erro de build
- [x] 9.2 Percorrer manualmente a etapa "Conta" cobrindo os cenários da spec: CPF incompleto, CPF inválido, CPF novo válido, CPF já cadastrado, senha fraca, senhas divergentes, senha editada após confirmação
- [x] 9.3 Confirmar com o DevTools que digitar um CPF completo gera no máximo uma requisição a `verificar-cpf`
- [x] 9.4 Confirmar que, com o endpoint indisponível (bloqueado no DevTools), o candidato ainda consegue avançar
- [x] 9.5 Conferir contraste e legibilidade das mensagens de sucesso em tema claro e escuro
