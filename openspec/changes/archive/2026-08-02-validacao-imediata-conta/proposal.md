## Why

Na etapa "Conta" do cadastro de candidato, o formulário só revela problemas quando a pessoa clica em **Continuar**: a verificação de CPF roda no `onBlur` e os estados `invalido`/`duplicado` são calculados mas nunca aparecem na tela por conta própria — o erro fica escondido atrás de `tentou[etapa]`. O resultado é o da captura de tela: um CPF já digitado por inteiro, sem nenhum retorno visual. Quem já tem conta preenche as 6 etapas inteiras para só então descobrir que deveria ter entrado pelo login.

## What Changes

- **Verificação de CPF assim que o campo é preenchido**, e não apenas ao sair dele: com 11 dígitos digitados, o portal valida os dígitos verificadores e, se válido, consulta o servidor (com debounce) para saber se já existe conta.
- **Retorno imediato e explícito por estado do CPF**, sem depender de tentativa de avanço:
  - CPF inválido → mensagem de erro no campo;
  - CPF já cadastrado → mensagem com atalho para **Entrar** e para **recuperar senha**, levando o CPF já preenchido;
  - CPF válido e livre → confirmação de que pode seguir.
- **Bloqueio do avanço** enquanto o CPF estiver inválido, duplicado ou com verificação em andamento.
- **Conferência de senha forte em tempo real**: as 5 regras já exibidas pelo `PasswordStrengthMeter` passam a valer como impedimento visível de avanço, com mensagem assim que a pessoa sai do campo com uma senha fraca.
- **Conferência de igualdade entre as duas senhas** exibida assim que a confirmação é digitada — hoje só existe o retorno positivo ("As senhas coincidem"), o negativo fica silencioso.
- **Proteção do endpoint de verificação de CPF** com `throttle`, já que ele responde se um CPF tem ou não conta e hoje está aberto sem limite de requisições.

## Capabilities

### New Capabilities
- `cadastro-candidato-conta`: validação da etapa de credenciais (CPF, e-mail, senha e confirmação) do cadastro público de candidato — regras de validação, momento em que cada retorno aparece e condições para avançar de etapa.

### Modified Capabilities
<!-- Nenhuma. Não existe spec anterior cobrindo o cadastro de candidato. -->

## Impact

- `resources/js/Pages/Candidato/Auth/Registro.jsx` — estado e renderização da etapa 0 (`cpfStatus`, `validarEtapa`, `erro()`, `avancar()`).
- `resources/js/components/Field.jsx` — precisa distinguir mensagem de sucesso de mensagem neutra (`hint`).
- `resources/js/lib/cpf.js` — reuso de `validarCpf`/`onlyDigits`, sem mudança de contrato.
- `app/Http/Controllers/Auth/CandidatoRegistroController.php` — `verificarCpf()` passa a responder também para CPF malformado/ inválido.
- `routes/web.php` — `throttle` na rota `candidato.registro.verificar-cpf`.
- Sem mudança de banco, de modelo ou das regras server-side de `CandidatoRegistroRequest`, que permanecem como validação final e autoritativa.
