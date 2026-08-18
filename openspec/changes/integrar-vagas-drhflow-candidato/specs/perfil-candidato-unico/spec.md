## ADDED Requirements

### Requirement: Currículo armazenado em pasta por CPF

Os arquivos de currículo SHALL ser gravados em uma pasta própria por candidato, nomeada pelo CPF somente com dígitos, dentro de uma raiz definida por configuração de ambiente. A raiz SHALL ser configurável sem alteração de código, de modo que o mesmo portal grave em um caminho local em desenvolvimento e no caminho esperado pelo RH em produção.

O portal SHALL criar a pasta do candidato quando ela ainda não existir, e SHALL preservar os arquivos de versões anteriores dentro dela.

A pasta e os arquivos SHALL NOT ser alcançáveis por acesso web direto: o download SHALL passar pelo portal, que verifica quem está pedindo.

#### Scenario: Primeiro currículo de um candidato

- **WHEN** um candidato envia seu primeiro currículo
- **THEN** o arquivo é gravado em uma pasta nomeada pelo CPF dele, sob a raiz configurada
- **AND** a pasta é criada se ainda não existia

#### Scenario: Versões convivem na mesma pasta

- **WHEN** o candidato envia uma nova versão de currículo
- **THEN** o novo arquivo é gravado na mesma pasta do CPF
- **AND** o arquivo da versão anterior continua lá, sem ser sobrescrito

#### Scenario: Raiz muda por ambiente

- **WHEN** o portal é executado em um ambiente cuja raiz de currículos está configurada para outro caminho
- **THEN** os arquivos passam a ser gravados sob esse caminho, sem alteração de código

#### Scenario: Arquivo não acessível diretamente pela web

- **WHEN** alguém tenta acessar o arquivo de currículo pelo endereço do arquivo no servidor
- **THEN** o acesso é negado
- **AND** o único caminho de leitura é o download autenticado oferecido pelo portal

## MODIFIED Requirements

### Requirement: Exclusão da conta remove o dado em toda parte

Quando o candidato excluir a conta, os seus dados pessoais SHALL ser removidos ou anonimizados de forma que nenhuma cópia permaneça acessível em qualquer tela do portal, incluindo todas as versões de currículo armazenadas.

O portal SHALL NOT depender de percorrer registros derivados para cumprir a exclusão.

Os registros de inscrição que vivem no DRHFlow SHALL NOT ser apagados pelo portal — eles pertencem ao processo seletivo conduzido pelo RH. O portal SHALL anonimizar os campos de identificação desses registros correspondentes ao CPF excluído, mantendo o registro do processo existente. A exclusão SHALL ser recusada enquanto o candidato tiver processo seletivo em aberto, como já ocorre hoje.

#### Scenario: Exclusão com candidaturas em vagas de coordenadores diferentes

- **WHEN** um candidato com candidaturas em várias vagas exclui a conta
- **THEN** nenhum coordenador consegue mais acessar seus dados pessoais ou qualquer versão do seu currículo

#### Scenario: Dado sensível não sobrevive à exclusão

- **WHEN** um candidato que informou necessidade de acessibilidade exclui a conta
- **THEN** essa informação deixa de estar acessível em qualquer parte do portal

#### Scenario: Registro do processo permanece sem os dados pessoais

- **WHEN** um candidato exclui a conta e existiam candidaturas suas em processos seletivos
- **THEN** as candidaturas continuam existindo como registro de processo
- **AND** não expõem os dados pessoais do candidato excluído

#### Scenario: Registro do DRHFlow não é apagado

- **WHEN** um candidato sem processo em aberto exclui a conta e existiam inscrições dele no DRHFlow
- **THEN** os registros de inscrição continuam existindo no DRHFlow
- **AND** seus campos de identificação pessoal são anonimizados

#### Scenario: Arquivos de currículo removidos da pasta do CPF

- **WHEN** um candidato exclui a conta
- **THEN** todas as versões de currículo dele são removidas da pasta do seu CPF
