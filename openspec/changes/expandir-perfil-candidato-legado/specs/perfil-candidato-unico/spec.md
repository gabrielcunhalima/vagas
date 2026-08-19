## MODIFIED Requirements

### Requirement: Definição de perfil completo

Um perfil SHALL ser considerado completo quando estiverem preenchidos: nome, nacionalidade, telefone celular, nível de escolaridade, situação do curso, curso, instituição, previsão de conclusão, resposta sobre necessidade de acessibilidade e currículo. O semestre SHALL ser exigido apenas quando a situação do curso for "cursando".

Nome social, LinkedIn, telefone fixo, endereço (incluindo tipo de endereço e tipo de bairro), pretensão salarial e disponibilidade SHALL NOT ser exigidos para que o perfil seja considerado completo. Nenhuma marcação de deficiência específica SHALL ser exigida.

A avaliação de completude SHALL ser feita pelo servidor, e a interface SHALL refletir esse resultado em vez de aplicar critério próprio.

#### Scenario: Perfil com todos os campos exigidos

- **WHEN** o candidato preencheu todos os campos exigidos, incluindo o telefone celular, e enviou um currículo
- **THEN** o portal considera o perfil completo

#### Scenario: Perfil sem telefone

- **WHEN** o candidato preencheu todos os demais campos exigidos mas não informou telefone celular
- **THEN** o portal considera o perfil incompleto e indica o telefone celular como pendente

#### Scenario: Telefone fixo em branco não impede a completude

- **WHEN** o candidato preencheu todos os campos exigidos, incluindo o telefone celular, mas não informou telefone fixo
- **THEN** o portal considera o perfil completo

#### Scenario: Curso concluído dispensa semestre

- **WHEN** o candidato informa situação do curso como "concluído" e não preenche o semestre
- **THEN** o portal não trata o semestre como pendência

#### Scenario: Campos opcionais em branco não impedem a completude

- **WHEN** o candidato preencheu os campos exigidos mas deixou endereço, tipo de endereço, tipo de bairro, LinkedIn e pretensão salarial em branco, e não marcou nenhuma deficiência
- **THEN** o portal considera o perfil completo

## ADDED Requirements

### Requirement: Telefone fixo e telefone celular são dados distintos

O perfil SHALL guardar telefone fixo e telefone celular como dois campos independentes. O telefone celular SHALL ser o campo exigido para a completude do perfil; o telefone fixo SHALL ser opcional.

#### Scenario: Candidato informa os dois telefones

- **WHEN** o candidato preenche telefone fixo e telefone celular
- **THEN** o portal grava os dois valores separadamente
- **AND** ambos aparecem na conferência de dados da candidatura

#### Scenario: Candidato informa apenas o celular

- **WHEN** o candidato preenche apenas o telefone celular
- **THEN** o portal considera o dado de telefone completo para fins de perfil
- **AND** o telefone fixo permanece em branco sem gerar pendência

### Requirement: Deficiência é um conjunto de marcações específicas

O perfil SHALL permitir declarar, de forma independente e combinável, as seguintes condições: deficiência física, auditiva, de fala, visual, mental, intelectual, reabilitação conforme a Resolução INSS/PRES Nº 118, de 04/11/2010, e autismo. Mais de uma marcação SHALL poder estar ativa ao mesmo tempo para o mesmo candidato.

O portal SHALL derivar um indicador agregado de "pessoa com deficiência" a partir dessas marcações — verdadeiro quando qualquer uma delas estiver marcada. Esse indicador SHALL NOT ser editável diretamente pelo candidato; ele reflete as marcações específicas.

#### Scenario: Candidato marca mais de uma condição

- **WHEN** o candidato marca deficiência auditiva e também autismo
- **THEN** o portal grava as duas marcações de forma independente
- **AND** o indicador agregado de pessoa com deficiência passa a ser verdadeiro

#### Scenario: Nenhuma marcação ativa

- **WHEN** o candidato não marca nenhuma das condições de deficiência
- **THEN** o indicador agregado de pessoa com deficiência é falso
- **AND** isso SHALL NOT ser tratado como pendência do perfil

#### Scenario: Candidato desmarca a única condição declarada

- **WHEN** um candidato que havia marcado uma condição de deficiência a desmarca
- **THEN** o indicador agregado de pessoa com deficiência volta a ser falso

### Requirement: Endereço pode informar tipo de logradouro e tipo de bairro

O endereço do perfil SHALL permitir, opcionalmente, um tipo de logradouro (ex.: Rua, Avenida, Rodovia) e um tipo de bairro (ex.: Bairro, Distrito, Zona Rural), preenchidos como texto livre.

#### Scenario: Candidato detalha o tipo de logradouro e de bairro

- **WHEN** o candidato preenche o endereço informando também tipo de logradouro e tipo de bairro
- **THEN** o portal grava os dois valores junto com o restante do endereço

#### Scenario: Tipos em branco não impedem a completude nem o salvamento do endereço

- **WHEN** o candidato preenche o restante do endereço mas deixa tipo de logradouro e tipo de bairro em branco
- **THEN** o portal salva o endereço normalmente
- **AND** isso SHALL NOT ser tratado como pendência do perfil
