# CI/CD

Dois workflows no GitHub Actions:

| Workflow | Arquivo | Onde roda | Quando |
| --- | --- | --- | --- |
| CI | `.github/workflows/ci.yml` | runner do GitHub (Ubuntu) | todo push e PR no `master` |
| CD | `.github/workflows/deploy.yml` | runner self-hosted no servidor | quando o CI passa no `master`, ou manualmente |

O CD só dispara se o CI daquele commit terminou verde, e entrega exatamente o commit aprovado — não "o que estiver no master agora".

## Como o servidor recebe as mudanças

A pasta do WAMP é um clone do repositório. O deploy roda no próprio servidor e faz `git fetch` + `git reset --hard` no commit aprovado, seguido de dependências, migrations e caches.

Quem dispara isso é o **runner self-hosted**: um agente instalado no servidor que fica conectado ao GitHub e recebe os jobs. Ele **só abre conexão de saída** — não é preciso liberar nenhuma porta de entrada no firewall nem dar IP público ao servidor. É o que viabiliza CD em máquina interna.

O deploy usa `reset --hard` em vez de `pull` de propósito: não gera merge, não trava por alteração local no servidor e garante que o que está no ar é bit a bit o que passou no CI.

> **Atenção no primeiro deploy:** `reset --hard` descarta qualquer alteração feita à mão nos arquivos versionados do servidor. Se hoje existe algo editado direto lá, copie a pasta antes e compare. `.env`, `storage/`, `vendor/` e `node_modules/` estão no `.gitignore` e são preservados.

## Setup, uma vez só

### 1. Pré-requisitos no servidor

No `PATH` da máquina, acessíveis pela conta que vai rodar o runner:

- Git
- PHP 8.3 CLI (o do WAMP serve, basta adicionar ao `PATH`)
- Composer
- Node.js 22 + npm

Conferir com `git --version`, `php -v`, `composer -V`, `node -v`.

O servidor também precisa de saída HTTPS para `github.com`, `packagist.org` e `registry.npmjs.org`.

### 2. Transformar a pasta do WAMP em um clone

O repositório é público, então não é preciso credencial. No servidor, em PowerShell:

```powershell
cd C:\wamp\www\vagas          # ajuste para o caminho real
git init -b master
git remote add origin https://github.com/gabrielcunhalima/vagas.git
git fetch origin master
git reset --hard origin/master
```

Se o repositório virar privado depois, trocar por uma *deploy key* (SSH) ou um PAT com escopo de leitura.

### 3. `.env` de produção

O `.env` **nunca** é versionado — mora no servidor e sobrevive a todo deploy. Antes do primeiro deploy, garantir `C:\wamp\www\vagas\.env` com `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` gerada e as credenciais reais de banco e SMTP. O workflow falha de propósito se não encontrar esse arquivo.

### 4. Instalar o runner

Em **Settings → Actions → Runners → New self-hosted runner**, escolher Windows. O GitHub mostra os comandos com o token preenchido. No servidor, PowerShell como administrador:

```powershell
mkdir C:\actions-runner ; cd C:\actions-runner
# baixar e extrair conforme a tela do GitHub, depois:
.\config.cmd --url https://github.com/gabrielcunhalima/vagas --token <TOKEN_DA_TELA> --labels windows,vagas
.\svc.cmd install
.\svc.cmd start
```

As labels `windows` e `vagas` são obrigatórias — o `deploy.yml` seleciona o runner por elas. Instalado como serviço, ele sobe junto com o Windows. A conta do serviço precisa de permissão de escrita na pasta do site.

### 5. Variável do repositório

Em **Settings → Secrets and variables → Actions → Variables**:

| Nome | Valor |
| --- | --- |
| `DEPLOY_PATH` | caminho do clone, ex. `C:\wamp\www\vagas` |

O deploy aborta se ela estiver vazia, se a pasta não existir, se não for um repositório git, se o remote apontar para outro lugar ou se faltar o `.env`.

### 6. Aprovação antes de publicar (opcional)

O job usa o environment `producao`. Em **Settings → Environments → producao** dá para exigir revisor — aí todo deploy fica parado esperando aprovação manual.

## O que o deploy faz

1. Valida servidor, clone, remote e `.env`
2. `php artisan down` (entra em manutenção **antes** de trocar o código)
3. `git fetch` + `git reset --hard` no commit aprovado pelo CI
4. `composer install --no-dev --optimize-autoloader`
5. `npm ci && npm run build`
6. `php artisan migrate --force`
7. `php artisan optimize`
8. `php artisan queue:restart`
9. `php artisan up` — roda mesmo se algo acima falhar, para não deixar o site fora do ar

## Apache: o `.htaccess` da raiz

A pasta do app fica dentro do `www/` do WAMP, então tudo nela é alcançável por HTTP. Com um clone git ali, `http://servidor/vagas/.git/` entregaria o código-fonte e todo o histórico.

O `.htaccess` na raiz do projeto bloqueia qualquer caminho fora de `public/`. Verificado no WAMP: o app responde 200 e `.git/config`, `.env` e `app/` retornam 403.

O ideal mesmo é um VirtualHost com `DocumentRoot` apontando direto para `vagas\public` — aí nada fora do `public/` é sequer alcançável, e o `.htaccess` da raiz vira redundante.

## Rodar o CI localmente

```bash
npm run build      # obrigatório antes dos testes: o root view usa @vite
php artisan test
```

O build vem antes porque `resources/views/app.blade.php` chama `@vite`, e sem o manifest gerado toda renderização de página quebra.

## Case-sensitivity

O CI roda em Linux, que diferencia maiúsculas de minúsculas em caminhos — o Windows não. Um import como `@/components/Botao` com a pasta chamada `Components` funciona local e quebra no CI. Como o build roda no CI, esse tipo de erro é pego antes do deploy.

## Formatação (Pint)

O Pint está instalado mas **não** faz parte do CI: o preset padrão reescreveria ~65 arquivos, inclusive o alinhamento de `=>` que o projeto usa de propósito. Para adotá-lo depois, criar um `pint.json` ajustado a esse estilo e só então incluir `./vendor/bin/pint --test` no `ci.yml`.
