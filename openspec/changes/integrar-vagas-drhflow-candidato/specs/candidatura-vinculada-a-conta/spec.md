## ADDED Requirements

### Requirement: Repartição dos dados da inscrição entre origem e portal

Os dados de uma inscrição SHALL ser gravados no DRHFlow sempre que a tabela de origem tiver campo correspondente. Os dados próprios da inscrição que o DRHFlow não comporta — carta de apresentação, detalhamento do conflito de interesse e a identificação da versão de currículo vigente no envio — SHALL ser guardados pelo portal, associados ao mesmo par CPF e vaga.

O portal SHALL NOT manter cópia própria de dado que já tenha campo no DRHFlow.

#### Scenario: Aceite do código de conduta vai para a origem

- **WHEN** o candidato envia uma inscrição aceitando o código de conduta
- **THEN** o aceite é gravado no registro do DRHFlow

#### Scenario: Carta de apresentação fica no portal

- **WHEN** o candidato envia uma inscrição com carta de apresentação
- **THEN** a carta é guardada pelo portal, associada ao CPF e à vaga
- **AND** continua sendo apresentada ao candidato quando ele consulta a inscrição

#### Scenario: Consulta reúne as duas origens

- **WHEN** o candidato abre uma inscrição sua
- **THEN** o portal apresenta em uma única tela o andamento vindo do DRHFlow e os dados próprios que ele guardou

## MODIFIED Requirements

### Requirement: Uma candidatura por vaga por candidato

O portal SHALL impedir que o mesmo CPF registre mais de uma candidatura para a mesma vaga. A verificação SHALL considerar o registro existente no DRHFlow, e não apenas o histórico da conta no portal — uma inscrição feita fora do portal para o mesmo CPF e a mesma vaga já ocupa esse par.

#### Scenario: Segunda tentativa na mesma vaga

- **WHEN** um candidato que já se inscreveu em uma vaga aciona novamente a candidatura dessa vaga
- **THEN** o portal informa que ele já se candidatou
- **AND** o conduz à sua candidatura existente

#### Scenario: Inscrição preexistente na origem

- **WHEN** um candidato aciona a candidatura de uma vaga para a qual já existe registro do seu CPF no DRHFlow, feito fora do portal
- **THEN** o portal informa que já existe inscrição dele nessa vaga
- **AND** não cria um segundo registro

#### Scenario: Envio concorrente

- **WHEN** dois envios da mesma inscrição chegam ao mesmo tempo
- **THEN** apenas um registro passa a existir no DRHFlow para aquele par de CPF e vaga

### Requirement: Registro do envio da candidatura

O envio de uma candidatura SHALL ser registrado com a data e hora da submissão e a identificação da versão de currículo vigente naquele momento. A data e hora da submissão SHALL ser gravada no registro do DRHFlow; a identificação da versão de currículo SHALL ser guardada pelo portal, que é quem versiona o arquivo.

O registro SHALL NOT copiar dados de identidade do candidato.

#### Scenario: Envio registra a versão de currículo

- **WHEN** o candidato envia uma inscrição
- **THEN** o portal registra qual versão do currículo estava vigente na submissão

#### Scenario: Currículo substituído após o envio

- **WHEN** o candidato envia uma nova versão de currículo depois de já ter se candidatado
- **THEN** o registro da submissão continua identificando a versão que estava vigente no envio

#### Scenario: Data de submissão na origem

- **WHEN** o candidato envia uma inscrição
- **THEN** o registro criado no DRHFlow carrega a data e hora daquele envio
