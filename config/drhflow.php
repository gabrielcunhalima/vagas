<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Conexão
    |--------------------------------------------------------------------------
    |
    | Nome da conexão declarada em config/database.php que aponta para o
    | DRHFlow. Centralizado aqui para que os repositórios e os testes que
    | verificam as garantias de escrita usem a mesma referência.
    |
    */

    'conexao' => 'drhflow',

    /*
    |--------------------------------------------------------------------------
    | Identificação do portal na auditoria
    |--------------------------------------------------------------------------
    |
    | Valor gravado em EN_CANDIDATO_VAGA_EMPREGO.ID_USUARIO_CAD e
    | ID_USUARIO_ALT para que o RH reconheça, nos relatórios do DRHFlow, quais
    | inscrições vieram do portal. A coluna é varchar(25).
    |
    */

    'id_usuario_cad' => env('DRHFLOW_ID_USUARIO_CAD', 'PORTALVAGAS'),

    /*
    |--------------------------------------------------------------------------
    | Única tabela em que o portal escreve
    |--------------------------------------------------------------------------
    |
    | Toda escrita do portal no DRHFlow acontece nesta tabela e apenas na linha
    | do CPF autenticado. Ver a capacidade `vagas-drhflow`.
    |
    */

    'tabela_inscricao' => 'EN_CANDIDATO_VAGA_EMPREGO',

    /*
    |--------------------------------------------------------------------------
    | Cache dos domínios
    |--------------------------------------------------------------------------
    |
    | Segundos que as listas de domínio (grau de instrução, UF, município, tipo
    | de experiência, tipo de admissão, projeto) ficam memorizadas. Mudam
    | raramente.
    |
    */

    'cache_dominios_segundos' => (int) env('DRHFLOW_CACHE_DOMINIOS', 6 * 3600),

];
