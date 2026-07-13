# /atualizar — Atualizar memória ao fim de uma conversa

Você é um assistente especializado em manter a documentação de memória do projeto atualizada.

## O que fazer

O usuário acabou de terminar uma sessão de desenvolvimento. Analise **o que foi feito nesta conversa** e atualize os arquivos de memória em `C:\Users\gabriel.lima\.claude\projects\c--wamp-www-vagas\memory\` para que a próxima conversa tenha o contexto correto.

## Passo 1 — Identifique o que mudou

Revise a conversa atual e liste tudo que foi criado ou alterado:
- Novos arquivos (controllers, models, views, migrations, rotas)
- Campos adicionados em tabelas
- Comportamentos novos em controllers
- Rotas adicionadas ou removidas
- Dependências adicionadas ao composer.json
- Configurações novas no .env
- Qualquer decisão arquitetural importante

## Passo 2 — Leia os arquivos de memória relevantes

Leia apenas os arquivos que precisam ser atualizados:
- `memory/arch_coordenador_c.md` — se houve mudanças em www/coordenador_c
- `memory/arch_vagas.md` — se houve mudanças em www/vagas
- `memory/schema_banco_vagas.md` — se houve mudanças no banco de dados
- `memory/project_vagas_coordenador.md` — se houve mudanças no módulo de integração

## Passo 3 — Atualize com precisão cirúrgica

Para cada arquivo relevante:
- **Adicione** o que é novo (novos campos, novos controllers, novas rotas, novas tabelas)
- **Corrija** o que mudou (campos renomeados, comportamentos alterados)
- **Remova** o que foi deletado (arquivos removidos, colunas dropadas)
- **Não** reescreva seções que não mudaram
- Se a mudança for pequena, use `Edit` para editar apenas o trecho relevante
- Se a mudança for grande, reescreva o arquivo inteiro com `Write`

## Passo 4 — Atualize o MEMORY.md se necessário

Se criou um arquivo de memória **novo**, adicione uma linha no `memory/MEMORY.md`.
Se renomeou ou removeu um arquivo, corrija o índice.
Não mexa no MEMORY.md se apenas editou arquivos existentes.

## Regras

- Seja específico: "adicionou campo `linkedin` em candidaturas" é melhor que "atualizou candidaturas"
- Datas absolutas: se algo tem prazo, converta para data absoluta (ex: "até 2026-07-01")
- Não salve código: salve apenas estrutura, campos, comportamentos e decisões
- Não salve o que já está no código e pode ser lido: salve o que é não-óbvio ou de contexto
- Se nada relevante mudou na sessão, diga isso ao usuário e não altere nada

## Ao terminar

Informe ao usuário:
1. Quais arquivos de memória foram atualizados
2. Um resumo bullet de 3-5 itens do que foi registrado
