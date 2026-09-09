<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        /*
        | PDFs de currículo, uma pasta por CPF — o layout que o DRHFlow espera.
        |
        | O driver é configurável por ambiente: 'sftp' aponta para o servidor de
        | arquivos onde o DRHFlow lê /home/Curriculos; 'local' fica no disco da
        | própria máquina, para desenvolvimento. Nos dois casos a raiz precisa
        | ficar FORA de qualquer diretório servido pela web — o download passa
        | pelo portal, que confere quem pede.
        |
        | As opções de SFTP são ignoradas quando o driver é 'local', e vice-versa.
        |
        | throw => true de propósito: uma falha silenciosa de gravação deixaria o
        | perfil apontando para um arquivo que não existe.
        */
        'curriculos' => [
            'driver' => env('CURRICULOS_DRIVER', 'local'),
            'root' => env('CURRICULOS_ROOT') ?: (env('CURRICULOS_DRIVER') === 'sftp'
                ? '/home/Curriculos'
                : storage_path('app/private/Curriculos')),

            'host' => env('CURRICULOS_SFTP_HOST'),
            'username' => env('CURRICULOS_SFTP_USERNAME'),
            'password' => env('CURRICULOS_SFTP_PASSWORD'),
            'privateKey' => env('CURRICULOS_SFTP_PRIVATE_KEY'),
            'passphrase' => env('CURRICULOS_SFTP_PASSPHRASE'),
            'port' => (int) env('CURRICULOS_SFTP_PORT', 22),
            'timeout' => (int) env('CURRICULOS_SFTP_TIMEOUT', 30),
            'maxTries' => (int) env('CURRICULOS_SFTP_MAX_TRIES', 3),

            // 'private' aqui quer dizer "não é servido pela web", não 0600: quem
            // lê os PDFs do outro lado é o DRHFlow, com outro usuário do sistema,
            // então o mapa abaixo traduz private para 0644/0755. A proteção é o
            // diretório ficar fora da web, não a permissão do arquivo.
            'visibility' => 'private',
            'permissions' => [
                'file' => ['public' => 0644, 'private' => 0644],
                'dir' => ['public' => 0755, 'private' => 0755],
            ],

            'serve' => false,
            'throw' => true,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
