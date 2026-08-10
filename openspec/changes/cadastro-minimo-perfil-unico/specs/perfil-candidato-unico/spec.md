## Purpose

Define o perfil do candidato como fonte única e viva dos seus dados pessoais, acadêmicos, de contato e de currículo — o que o compõe, o que significa estar completo, como é editado a partir de qualquer contexto do portal, como o currículo é versionado e qual o efeito da exclusão da conta sobre tudo isso.

## ADDED Requirements

### Requirement: Perfil como fonte única dos dados do candidato

Os dados pessoais, de contato, acadêmicos, de endereço e de currículo de um candidato SHALL existir em um único lugar, vinculado à sua conta. Nenhum outro registro do portal SHALL manter cópia própria desses dados.

Toda leitura desses dados — por qualquer tela, processo ou destinatário — SHALL refletir o valor atual do perfil no momento da leitura.

#### Scenario: Mesmo dado visto a partir de vagas diferentes

- **WHEN** um candidato possui candidaturas em duas vagas de coordenadores diferentes
- **THEN** ambos os coordenadores veem exatamente os mesmos dados e o mesmo currículo do candidato

#### Scenario: Atualização se propaga para candidaturas anteriores

- **WHEN** o candidato atualiza um dado do perfil após já ter se candidatado a uma ou mais vagas
- **THEN** as candidaturas existentes passam a exibir o valor atualizado, sem exigir nova inscrição

### Requirement: Perfil incompleto é um estado válido da conta

Uma conta SHALL poder existir com o perfil incompleto por tempo indeterminado. O portal SHALL NOT exigir o preenchimento dos dados do perfil como condição para criar a conta, autenticar-se, navegar pelas vagas ou consultar candidaturas anteriores.

O portal SHALL informar ao candidato, desde o primeiro acesso à conta, quais dados ainda faltam e para que eles serão necessários.

#### Scenario: Conta recém-criada sem dados de perfil

- **WHEN** um candidato acaba de criar a conta e ainda não preencheu nenhum dado do perfil
- **THEN** ele consegue navegar pelo portal e acessar sua conta normalmente
- **AND** o portal indica quais dados ainda faltam

#### Scenario: Candidato retorna depois para completar

- **WHEN** o candidato volta ao portal em uma sessão posterior com o perfil ainda incompleto
- **THEN** os dados já preenchidos anteriormente estão preservados
- **AND** ele pode preencher apenas os campos restantes

### Requirement: Definição de perfil completo

Um perfil SHALL ser considerado completo quando estiverem preenchidos: nome, nacionalidade, telefone, nível de escolaridade, situação do curso, curso, instituição, previsão de conclusão, resposta sobre necessidade de acessibilidade e currículo. O semestre SHALL ser exigido apenas quando a situação do curso for "cursando".

Nome social, LinkedIn, endereço, pretensão salarial e disponibilidade SHALL NOT ser exigidos para que o perfil seja considerado completo.

A avaliação de completude SHALL ser feita pelo servidor, e a interface SHALL refletir esse resultado em vez de aplicar critério próprio.

#### Scenario: Perfil com todos os campos exigidos

- **WHEN** o candidato preencheu todos os campos exigidos e enviou um currículo
- **THEN** o portal considera o perfil completo

#### Scenario: Perfil sem telefone

- **WHEN** o candidato preencheu todos os demais campos exigidos mas não informou telefone
- **THEN** o portal considera o perfil incompleto e indica o telefone como pendente

#### Scenario: Curso concluído dispensa semestre

- **WHEN** o candidato informa situação do curso como "concluído" e não preenche o semestre
- **THEN** o portal não trata o semestre como pendência

#### Scenario: Campos opcionais em branco não impedem a completude

- **WHEN** o candidato preencheu os campos exigidos mas deixou endereço, LinkedIn e pretensão salarial em branco
- **THEN** o portal considera o perfil completo

### Requirement: Edição do perfil a partir de qualquer contexto grava na conta

Quando o portal exibir os dados do perfil para conferência ou edição em qualquer contexto — inclusive durante uma candidatura — as alterações SHALL ser gravadas no perfil da conta. O portal SHALL NOT criar uma versão paralela dos dados restrita àquele contexto.

O candidato SHALL ser informado de que a alteração vale para a conta e não apenas para aquele contexto.

#### Scenario: Edição durante a candidatura

- **WHEN** o candidato altera um dado do perfil na tela de candidatura e envia a inscrição
- **THEN** o valor alterado passa a ser o valor do perfil da conta
- **AND** aparece também nas demais candidaturas do candidato

#### Scenario: Aviso do alcance da edição

- **WHEN** o portal exibe os dados do perfil para edição dentro de uma candidatura
- **THEN** informa que as alterações serão salvas nos dados da conta

### Requirement: Currículo versionado com uma versão vigente

Cada envio de currículo SHALL criar uma nova versão, e o portal SHALL NOT sobrescrever nem descartar a versão anterior. O perfil SHALL apontar para exatamente uma versão vigente, que é a versão lida por qualquer consumidor dos dados do candidato.

Versões anteriores SHALL ser retidas para permitir identificar qual currículo estava vigente quando uma decisão de processo seletivo foi tomada.

#### Scenario: Envio de currículo substituto

- **WHEN** o candidato envia um novo currículo tendo já um currículo no perfil
- **THEN** o novo arquivo passa a ser a versão vigente
- **AND** a versão anterior permanece armazenada
- **AND** todas as candidaturas do candidato passam a apresentar a nova versão

#### Scenario: Remoção do currículo do perfil

- **WHEN** o candidato remove o currículo do perfil
- **THEN** o perfil deixa de ter versão vigente e volta a ser considerado incompleto

### Requirement: Exclusão da conta remove o dado em toda parte

Quando o candidato excluir a conta, os seus dados pessoais SHALL ser removidos ou anonimizados de forma que nenhuma cópia permaneça acessível em qualquer tela do portal, incluindo todas as versões de currículo armazenadas.

O portal SHALL NOT depender de percorrer registros derivados para cumprir a exclusão.

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

### Requirement: Retenção de contas inativas

O portal SHALL anonimizar contas de candidato que permaneçam sem atividade por dois anos, precedendo a anonimização de um aviso por e-mail com trinta dias de antecedência.

SHALL contar como atividade o acesso à conta, a alteração do perfil e o envio de candidatura. A escrita feita pelo próprio portal — inclusive o registro do aviso — SHALL NOT ser tratada como atividade do titular.

Qualquer atividade posterior ao aviso SHALL cancelar a anonimização.

O portal SHALL NOT anonimizar conta que possua candidatura sem desfecho, por servir a processo seletivo em andamento.

#### Scenario: Conta inativa recebe aviso antes da anonimização

- **WHEN** uma conta completa dois anos sem atividade
- **THEN** o portal envia ao titular um aviso informando o prazo e como manter a conta
- **AND** a conta não é anonimizada nesse momento

#### Scenario: Anonimização após a carência

- **WHEN** transcorrem trinta dias do aviso sem qualquer atividade do titular
- **THEN** o portal anonimiza a conta pelas mesmas regras da exclusão a pedido

#### Scenario: Titular retorna após o aviso

- **WHEN** o candidato acessa a conta, altera o perfil ou se candidata depois de avisado
- **THEN** a anonimização é cancelada
- **AND** um novo aviso só ocorre após novo período completo de inatividade

#### Scenario: Aviso não é reenviado a cada verificação

- **WHEN** a rotina de retenção é executada repetidamente sobre uma conta já avisada
- **THEN** o aviso não é reenviado

#### Scenario: Candidatura em andamento protege a conta

- **WHEN** uma conta inativa possui candidatura ainda sem desfecho
- **THEN** o portal não a avisa nem a anonimiza

#### Scenario: Candidatura encerrada não protege a conta

- **WHEN** uma conta inativa possui apenas candidaturas com desfecho
- **THEN** a conta segue o fluxo normal de aviso e anonimização
