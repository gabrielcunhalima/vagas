# /abrir — Inicializar contexto completo dos sistemas

Você é um assistente especializado nestes dois sistemas Laravel da FAPEU. Ao executar este comando, carregue o contexto completo para estar pronto para trabalhar.

## O que fazer

### 1. Leia TODOS os arquivos de memória

Leia em paralelo todos os arquivos abaixo:

- `C:\Users\gabriel.lima\.claude\projects\c--wamp-www-vagas\memory\project_vagas_coordenador.md`
- `C:\Users\gabriel.lima\.claude\projects\c--wamp-www-vagas\memory\arch_coordenador_c.md`
- `C:\Users\gabriel.lima\.claude\projects\c--wamp-www-vagas\memory\arch_vagas.md`
- `C:\Users\gabriel.lima\.claude\projects\c--wamp-www-vagas\memory\schema_banco_vagas.md`

### 2. Verifique se os arquivos críticos existem (opcional, só se suspeitar de mudança)

Se o usuário indicou que houve mudanças desde a última sessão, verifique rapidamente se as migrations mais recentes ainda batem com o schema em memória:

```
c:\wamp\www\vagas\database\migrations\
c:\wamp\www\coordenador_c\database\migrations\
```

### 3. Apresente o resumo ao usuário

Responda com um resumo conciso em markdown:

---

## Contexto carregado ✓

### www/coordenador_c
- Stack: Laravel X.X / PHP X.X / SQL Server + MySQL (vagas)
- Módulos ativos: [lista dos principais]
- Última alteração registrada: [o que foi feito por último]

### www/vagas
- Stack: Laravel X.X / PHP X.X / MySQL
- Módulos ativos: [lista dos principais]
- Última alteração registrada: [o que foi feito por último]

### Banco vagas (MySQL compartilhado)
- Tabelas principais: vagas, candidaturas, users, vaga_alertas
- Últimas colunas adicionadas: [lista]

**Pronto! O que vamos fazer hoje?**

---

## Regras

- Leia os arquivos de memória antes de responder qualquer coisa
- Não invente informações que não estão na memória
- Se a memória estiver desatualizada em relação ao código atual, sinalize isso
- Seja direto: o resumo deve ser lido em 30 segundos
