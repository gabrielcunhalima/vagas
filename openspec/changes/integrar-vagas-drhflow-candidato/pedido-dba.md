# Pedido ao DBA — usuário restrito para o Portal de Vagas

> Rascunho para envio. Tarefa 1.5 da mudança `integrar-vagas-drhflow-candidato`.

## Mensagem

Assunto: **Portal de Vagas — criação de usuário de banco com permissão restrita em DB_DRHFLOW_TESTE**

Olá,

O Portal de Vagas passou a ler as vagas e gravar as inscrições dos candidatos
diretamente no `DB_DRHFLOW_TESTE`. Hoje a aplicação se conecta com a credencial
`sa`, que é membro de `sysadmin` — ou seja, o portal tem permissão para apagar
qualquer coisa em qualquer banco daquela instância.

Isso é mais poder do que a aplicação precisa e mais risco do que queremos correr.
O portal faz exatamente três coisas:

1. **Lê** vagas, views e tabelas de domínio;
2. **Insere** uma linha em `EN_CANDIDATO_VAGA_EMPREGO` quando um candidato se
   inscreve;
3. **Atualiza** as colunas de dados do candidato dessa mesma linha (reenvio de
   inscrição e anonimização por exclusão de conta).

Ele **não apaga nada**, **não altera esquema** e **não escreve em nenhuma outra
tabela**. Gostaríamos de um usuário cujas permissões reflitam isso, para que a
garantia não dependa só do código da aplicação.

Segue o script sugerido. Se preferirem outro nome de login ou outra política de
senha, é só ajustar.

Uma observação que simplifica: **não precisamos de acesso ao banco `CorporeRM`**.
As views `VW_GRAU_INSTRUCAO_RM` e `VW_MUNICIPIO_RM`, dentro do próprio
`DB_DRHFLOW_TESTE`, já entregam os dados do RM de que precisamos.

Obrigado,
Gabriel Lima — gabriel.lima@fapeu.org.br

## Script sugerido

```sql
USE [master];
GO

-- Trocar a senha antes de executar.
CREATE LOGIN [portal_vagas] WITH PASSWORD = N'<SENHA_FORTE_AQUI>',
    DEFAULT_DATABASE = [DB_DRHFLOW_TESTE],
    CHECK_POLICY = ON;
GO

USE [DB_DRHFLOW_TESTE];
GO

CREATE USER [portal_vagas] FOR LOGIN [portal_vagas];
GO

-- 1) Leitura de todo o banco: vagas, views e tabelas de domínio.
ALTER ROLE [db_datareader] ADD MEMBER [portal_vagas];
GO

-- 2) Escrita restrita à tabela de inscrição, e só INSERT/UPDATE.
GRANT INSERT, UPDATE ON [dbo].[EN_CANDIDATO_VAGA_EMPREGO] TO [portal_vagas];
GO

-- 3) Nada de remoção, em lugar nenhum. DENY vence qualquer GRANT herdado.
DENY DELETE TO [portal_vagas];
GO

-- 4) Nada de alteração de esquema.
DENY ALTER, CREATE TABLE, CREATE VIEW, CREATE PROCEDURE,
     REFERENCES, TAKE OWNERSHIP TO [portal_vagas];
GO
```

### Conferência depois de aplicar

```sql
EXECUTE AS USER = 'portal_vagas';

SELECT TOP 1 CD_VAGA_EMPREGO FROM EN_VAGA_EMPREGO;          -- deve funcionar
SELECT TOP 1 CODCLIENTE      FROM VW_GRAU_INSTRUCAO_RM;     -- deve funcionar
DELETE FROM EN_CANDIDATO_VAGA_EMPREGO WHERE 1 = 0;          -- deve FALHAR
UPDATE EN_VAGA_EMPREGO SET FG_ATIVA = FG_ATIVA WHERE 1 = 0; -- deve FALHAR

REVERT;
```

## Depois de receber a credencial

Trocar no `.env` do ambiente:

```
DRHFLOW_USERNAME=portal_vagas
DRHFLOW_PASSWORD=<senha>
```

E rodar a suíte, que verifica pelo lado da aplicação que nenhum caminho emite
`DELETE` ou escreve fora de `EN_CANDIDATO_VAGA_EMPREGO`:

```
php artisan test --filter=PreservacaoDoDrhflow
```
