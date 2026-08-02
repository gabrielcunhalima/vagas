# CI/CD

O pipeline tem dois workflows no GitHub Actions:

| Workflow | Arquivo | Onde roda | Quando |
| --- | --- | --- | --- |
| CI | `.github/workflows/ci.yml` | runner do GitHub (Ubuntu) | todo push e PR no `master` |
| CD | `.github/workflows/deploy.yml` | runner self-hosted no servidor | quando o CI passa no `master`, ou manualmente |

O CD só dispara se o CI daquele commit terminou verde, e faz checkout exatamente do commit aprovado.

## Por que o CD precisa de um runner self-hosted

O servidor da FAPEU é interno, sem IP público — o GitHub não consegue abrir conexão até ele. A saída é o contrário: um agente instalado no servidor que fica conectado ao GitHub e recebe os jobs. É o runner self-hosted.

## Setup, uma vez só

### 1. Instalar o runner no servidor

Em **Settings → Actions → Runners → New self-hosted runner**, escolher Windows. O GitHub mostra os comandos com o token já preenchido. No servidor, em PowerShell como administrador:

```powershell
mkdir C:\actions-runner ; cd C:\actions-runner
# baixar e extrair conforme a tela do GitHub, depois:
.\config.cmd --url https://github.com/gabrielcunhalima/vagas --token <TOKEN_DA_TELA> --labels windows,vagas
.\svc.cmd install
.\svc.cmd start
```

As labels `windows` e `vagas` são obrigatórias — o `deploy.yml` seleciona o runner por elas.

Instalar o runner como serviço (`svc.cmd install`) faz ele subir junto com o Windows. A conta que roda o serviço precisa de permissão de escrita na pasta do site.

### 2. Pré-requisitos no servidor

O runner usa o que estiver no `PATH` da máquina. Precisam estar instalados e acessíveis:

- PHP 8.3 CLI (o mesmo do WAMP serve, basta adicionar ao `PATH`)
- Composer
- Node.js 22 + npm

Conferir com `php -v`, `composer -V` e `node -v` na mesma conta do serviço.

### 3. Variável do repositório

Em **Settings → Secrets and variables → Actions → Variables**, criar:

| Nome | Valor |
| --- | --- |
| `DEPLOY_PATH` | caminho da pasta servida pelo Apache, ex. `C:\wamp\www\vagas` |

O deploy aborta se ela estiver vazia, apontar para raiz de disco, ou se a pasta não existir.

### 4. `.env` de produção

O `.env` **nunca** é versionado nem copiado pelo deploy — ele mora no servidor e é preservado a cada entrega. Antes do primeiro deploy, criar `%DEPLOY_PATH%\.env` com `APP_ENV=production`, `APP_DEBUG=false`, `APP_KEY` gerada e as credenciais reais de banco e SMTP. O workflow falha de propósito se não encontrar esse arquivo.

### 5. Aprovação antes de publicar (opcional)

O job usa o environment `producao`. Em **Settings → Environments → producao** dá para exigir revisor: aí todo deploy fica parado esperando aprovação manual.

## O que o deploy faz

1. Valida `DEPLOY_PATH` e a existência do `.env`
2. `composer install --no-dev --optimize-autoloader` e `npm ci && npm run build`
3. `php artisan down` (modo manutenção)
4. `robocopy /MIR` do build para a pasta do site
5. `php artisan migrate --force`
6. `php artisan optimize` (config, rotas, views, eventos)
7. `php artisan queue:restart`
8. `php artisan up` — roda mesmo se algo acima falhar, para não deixar o site fora do ar

### Sobre o `robocopy /MIR`

O espelhamento remove do servidor os arquivos que saíram do repositório, evitando resto de versão antiga. Ficam de fora da cópia **e da remoção**: `.env`, `storage/`, `public/storage`, `.git`, `node_modules` e `tests`.

## Rodar o CI localmente

```bash
npm run build      # obrigatório antes dos testes: o root view usa @vite
php artisan test
```

O build vem antes porque `resources/views/app.blade.php` chama `@vite`, e sem o manifest gerado toda renderização de página quebra.

## Formatação (Pint)

O Pint está instalado mas **não** faz parte do CI: o preset padrão reescreveria ~65 arquivos, inclusive o alinhamento de `=>` que o projeto usa de propósito. Para adotá-lo depois, criar um `pint.json` ajustado a esse estilo e só então incluir `./vendor/bin/pint --test` no `ci.yml`.
