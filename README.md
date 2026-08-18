# Portal de Vagas FAPEU

## Integração com o DRHFlow

As vagas apresentadas ao candidato e as inscrições que ele envia vivem no
**DRHFlow** (SQL Server), não no MySQL do portal. A conexão `drhflow` é
configurada pelas variáveis `DRHFLOW_*` do `.env` (ver `.env.example`).

Exige as extensões PHP `sqlsrv` e `pdo_sqlsrv`.

### Permissão de banco exigida do ambiente

O usuário da conexão `drhflow` precisa de:

| Permissão | Escopo |
| --- | --- |
| `SELECT` | todo o banco `DB_DRHFLOW_TESTE` |
| `INSERT`, `UPDATE` | **apenas** `EN_CANDIDATO_VAGA_EMPREGO` |
| `DELETE` | **nenhum**, em tabela nenhuma |
| DDL (`CREATE`/`ALTER`/`DROP`) | **nenhum** |

O acesso ao banco `CorporeRM` **não é necessário**: `VW_GRAU_INSTRUCAO_RM` e
`VW_MUNICIPIO_RM`, dentro do próprio `DB_DRHFLOW_TESTE`, entregam os dados do RM.

> **Pendência de infraestrutura.** O ambiente atual usa a credencial `sa`, que é
> `sysadmin` e não tem nenhuma dessas restrições. Enquanto o DBA não fornecer o
> usuário restrito, a única barreira contra escrita indevida no banco do RH é o
> código — verificada por `tests/Feature/Drhflow/PreservacaoDoDrhflowTest.php`,
> que observa o SQL efetivamente emitido na conexão.

### E-mails em teste e homologação

`MAIL_ALWAYS_TO` desvia **todo** e-mail da aplicação para um único endereço,
ignorando o destinatário original. É obrigatório em teste e homologação, onde o
banco carrega e-mails de candidatos reais, e deve ficar **vazio em produção**.

### Currículos

`CURRICULOS_ROOT` define a raiz dos PDFs, organizados em uma pasta por CPF
(`{cpf}/{uuid}.pdf`) — o layout que o DRHFlow espera. Precisa apontar para um
diretório **fora** de qualquer caminho servido pela web; em produção,
`/home/Curriculos`. O download passa sempre pelo portal, que confere quem pede.

### Testes

Nenhum teste alcança o DRHFlow real: `phpunit.xml` aponta as variáveis
`DRHFLOW_*` para um host inalcançável, e quem precisa da origem usa o trait
`Tests\Concerns\UsaDrhflowFalso`, que troca a conexão por um SQLite em memória
com o esquema real.

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
