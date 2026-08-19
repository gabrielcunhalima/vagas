# Comunicado ao RH — antes do deploy

> Rascunho para envio. Cobre as tarefas 8.1 (decisão a confirmar) e 9.5 (avisos).

---

Assunto: **Portal de Vagas passa a usar o DRHFlow — uma decisão a confirmar e três avisos**

Olá,

O Portal de Vagas foi ligado ao DRHFlow. A partir do deploy, o que o candidato vê
e o que ele envia deixam de viver num cadastro separado:

- **As vagas publicadas no portal passam a ser as do DRHFlow.** Não é mais preciso
  cadastrar a mesma vaga duas vezes. O portal mostra as vagas com situação
  "Vaga em Aberto" cujo prazo de inscrição ainda não passou — exatamente o mesmo
  critério da consulta que vocês nos passaram.
- **As inscrições passam a cair direto em `EN_CANDIDATO_VAGA_EMPREGO`**, chaveadas
  por CPF + vaga, entrando no fluxo de seleção, entrevista e avaliação que vocês
  já usam.
- **O acompanhamento do candidato reflete o que vocês registram.** Quando vocês
  preenchem data, horário e local de entrevista no DRHFlow, o candidato passa a
  ver isso na conta dele, sem nenhuma ação de ninguém.

O portal **nunca apaga nada** do DRHFlow e **só escreve** em
`EN_CANDIDATO_VAGA_EMPREGO`. Não toca em `EN_VAGA_EMPREGO`, nas views, nas tabelas
de domínio, nem nas colunas de entrevista, notas e média — essas são de vocês.

## 1. Uma decisão que precisamos confirmar com vocês

**Quando um candidato exclui a conta, o que fazemos com as inscrições dele no
DRHFlow?**

Temos duas exigências que se chocam: vocês pediram que nada seja apagado daquele
banco, e a LGPD dá ao titular o direito de ter os dados pessoais removidos.

**O que implementamos** (e queremos confirmar): a linha **não é apagada**. Os
campos de identificação — nome, nome social, e-mail, telefone e endereço — são
substituídos por marcadores, e **tudo o mais permanece**: o CPF, o código da vaga,
as datas de entrevista, as notas e a média. O registro do processo continua
existindo e auditável; só a identidade sai.

Na prática, a linha fica assim:

| Coluna | Antes | Depois |
| --- | --- | --- |
| `NU_CPF` | 00000000191 | 00000000191 *(mantido)* |
| `CD_VAGA_EMPREGO` | 698 | 698 *(mantido)* |
| `NM_CANDIDATO` | Maria da Silva | Candidato excluído |
| `DE_EMAIL` | maria@... | excluido_…@removido.invalid |
| `NU_TELEFONE_CELULAR` | 48999990000 | *(vazio)* |
| endereço | Rua…, Florianópolis/SC | *(vazio)* |
| `DT_ENTREVISTA`, notas, média | preenchidos | preenchidos *(mantidos)* |

Além disso, a exclusão continua sendo **recusada enquanto o candidato tiver
processo em aberto** — ou seja, inscrição sem avaliação concluída.

**Se vocês preferirem que o portal não altere nenhum registro de processo por
iniciativa própria**, a alternativa é bloquear a exclusão de conta para quem já
enviou qualquer inscrição ao DRHFlow. É uma restrição bem mais dura para o
candidato e tem implicação jurídica, por isso preferimos perguntar.

## 2. Links antigos de vaga podem levar ao lugar errado

O endereço de uma vaga no portal passa a usar o **código da vaga no DRHFlow**
(`CD_VAGA_EMPREGO`). Antes ele usava o código do cadastro próprio do portal.

Os dois são numéricos e vão coincidir por acaso. Um link antigo — divulgado em
e-mail, WhatsApp ou rede social — vai levar à vaga de mesmo número no DRHFlow
(provavelmente outra vaga) ou a "vaga não encontrada".

**O que isso significa na prática:** se vocês divulgaram links de vagas do portal
antigo, vale reenviar os links novos depois do deploy. O volume é pequeno, mas é
melhor avisar do que alguém abrir a vaga errada.

## 3. Os alertas por e-mail não disparam para as vagas do DRHFlow

O portal tem um recurso de "alerta de vagas": o candidato cadastra o perfil de
interesse e recebe e-mail quando surge vaga compatível.

Esses alertas são disparados pela autorização da vaga no painel do coordenador do
portal — que continua funcionando sobre o cadastro antigo. **Vagas cadastradas no
DRHFlow não vão disparar alerta** até que a próxima etapa (a migração do painel
do coordenador) seja feita.

Ninguém deixa de ver as vagas: elas aparecem normalmente na listagem. O que não
acontece é o e-mail proativo.

## 4. Como as inscrições do portal aparecem para vocês

As linhas criadas pelo portal são identificáveis pela coluna `ID_USUARIO_CAD`,
que recebe o valor **`PORTALVAGAS`**. Hoje essa coluna está vazia nas 4.469 linhas
existentes, então ela serve bem para separar o que veio do portal.

**Se vocês preferirem outro identificador** (algo que apareça melhor nos
relatórios de vocês), é só dizer — é um valor de configuração, mudamos em um
minuto.

Vale saber também que **o portal não preenche todos os campos da ficha**. Ele
grava o que o formulário do candidato coleta hoje: nome, nome social, e-mail,
telefone, endereço completo, país, grau de instrução, cursos superiores, PcD e o
aceite do código de conduta. Campos como RG, CTPS, PIS/PASEP, naturalidade,
cor/raça e os sinalizadores de parentesco **ficam em branco** — nada é inventado
para preencher coluna. Expandir o formulário para cobri-los é uma etapa seguinte,
e podemos priorizá-la se isso atrapalhar o trabalho de vocês.

## 5. Uma pergunta

Existe um endereço de e-mail ou uma rotina que deva ser avisada a cada inscrição
recebida? Hoje o portal notifica o coordenador da vaga no cadastro antigo; com o
DRHFlow, não sabemos para onde esse aviso deve ir — ou se ele é necessário, já que
a inscrição já aparece no sistema de vocês.

Qualquer dúvida, estou à disposição.

Gabriel Lima — gabriel.lima@fapeu.org.br
