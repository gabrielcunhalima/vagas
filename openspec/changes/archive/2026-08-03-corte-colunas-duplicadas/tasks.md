## 1. Pré-requisito

- [x] 1.1 Confirmar em produção (ou no ambiente de destino) a contagem de candidaturas com `candidato_id` nulo e decidir o destino delas antes de aplicar — em desenvolvimento local são 4, dado de teste, apagadas sem estratégia de resgate conforme design.md

## 2. Remoção do caminho de adoção retroativa

- [x] 2.1 Remover `CandidatoRegistroController::adotarCandidaturasAnteriores()` e a chamada em `store()`
- [x] 2.2 Remover a mensagem de "candidaturas anteriores incorporadas" em `store()`, que dependia do retorno desse método
- [x] 2.3 Ajustar `Registro.jsx` / tela de confirmação, se exibirem algo relacionado ao aviso de candidaturas incorporadas

## 3. Migrations destrutivas

- [x] 3.1 Migration: apagar `candidaturas` com `candidato_id` nulo
- [x] 3.2 Migration: `candidaturas.candidato_id` vira `NOT NULL`
- [x] 3.3 Migration: trocar `unique(vaga_id, cpf)` por `unique(vaga_id, candidato_id)` em `candidaturas`
- [x] 3.4 Migration: remover de `candidaturas` as 22 colunas duplicadas — `nome`, `email`, `cpf`, `telefone`, `linkedin`, `curso`, `instituicao`, `semestre`, `previsao_conclusao`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `estado`, `pais`, `pretensao_salarial`, `disponibilidade`, `pcd`, `pcd_tipo`, `curriculo_path`, `curriculo_nome_original`
- [x] 3.5 Migration: remover de `candidatos` as colunas `conflito_interesse`, `conflito_interesse_detalhe`, `codigo_conduta_aceito_em`

## 4. Limpeza de código morto

- [x] 4.1 Remover `conflito_interesse` / `conflito_interesse_detalhe` / `codigo_conduta_aceito_em` do `$fillable` de `App\Models\Candidato`, se estiverem lá
- [x] 4.2 Verificar que nenhuma seeder (`CenariosTesteSeeder`, `VagasSeeder`) grava valores nas colunas removidas de `candidatos` ou nas 22 colunas removidas de `candidaturas`
- [x] 4.3 Verificar que `Candidatura::CAMPOS_DO_PERFIL` e o `getAttribute` que o acompanha continuam corretos sem as colunas físicas por baixo (não fazem `parent::getAttribute` cair em coluna inexistente)

## 5. Verificação

- [x] 5.1 `php artisan migrate` roda limpo do zero (`migrate:fresh --seed`) com as migrations desta change
- [x] 5.2 Cadastro de conta nova funciona sem erro após a remoção de `adotarCandidaturasAnteriores()`
- [x] 5.3 Candidatura a uma vaga grava `candidato_id` e respeita a nova constraint única — segunda tentativa na mesma vaga é bloqueada
- [x] 5.4 Ficha de candidatura no painel do coordenador (`www/coordenador_c`) continua exibindo nome, e-mail, formação e currículo corretamente, lidos via `candidato_id`
- [x] 5.5 Exportação de dados do candidato (`/meus-dados`) continua funcionando, incluindo `conflito_interesse` e `codigo_conduta_aceito_em` por candidatura
- [x] 5.6 Teste automatizado cobrindo: candidatura antiga sem `candidato_id` é removida pela migration de limpeza, e um cadastro novo após o deploy não encontra mais candidaturas para incorporar
