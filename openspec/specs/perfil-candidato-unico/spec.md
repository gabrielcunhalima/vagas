# perfil-candidato-unico Specification

## Purpose

Define o perfil do candidato como fonte única e viva dos seus dados pessoais, acadêmicos, de contato e de currículo — o que o compõe, o que significa estar completo, como é editado a partir de qualquer contexto do portal, como o currículo é versionado e qual o efeito da exclusão da conta sobre tudo isso.

## Requirements

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

Um perfil SHALL ser considerado completo quando estiverem preenchidos: nome, nacionalidade, telefone, resposta sobre necessidade de acessibilidade, currículo, e pelo menos uma formação completa.

Uma formação SHALL ser considerada completa quando tiver nível de escolaridade, situação do curso, curso, instituição e previsão/data de conclusão preenchidos. O semestre dessa formação SHALL ser exigido apenas quando a situação do curso dessa formação for "cursando". Uma formação com apenas parte desses campos preenchidos SHALL ser tratada como pendência, e não contar como a formação completa exigida.

Nome social, LinkedIn, endereço, pretensão salarial, disponibilidade, outras formações reconhecidas pelo MEC e outros cursos/palestras SHALL NOT ser exigidos para que o perfil seja considerado completo.

A avaliação de completude SHALL ser feita pelo servidor, e a interface SHALL refletir esse resultado em vez de aplicar critério próprio.

#### Scenario: Perfil com uma formação completa

- **WHEN** o candidato preencheu todos os demais campos exigidos e cadastrou uma formação com todos os seus campos preenchidos, e enviou um currículo
- **THEN** o portal considera o perfil completo

#### Scenario: Perfil sem nenhuma formação cadastrada

- **WHEN** o candidato preencheu os demais campos exigidos mas não cadastrou nenhuma formação
- **THEN** o portal considera o perfil incompleto e indica a formação como pendente

#### Scenario: Formação cadastrada pela metade não conta como completa

- **WHEN** o candidato cadastra uma formação preenchendo apenas o curso, sem instituição nem previsão de conclusão
- **THEN** o portal não considera essa formação como a formação completa exigida
- **AND** indica os campos daquela formação como pendentes

#### Scenario: Curso concluído dispensa semestre

- **WHEN** uma formação do candidato tem situação do curso "concluído" e o semestre dessa formação não é preenchido
- **THEN** o portal não trata o semestre dessa formação como pendência

#### Scenario: Segunda formação incompleta não invalida a primeira

- **WHEN** o candidato já tem uma formação completa e adiciona uma segunda formação apenas com o curso preenchido
- **THEN** o portal continua considerando o perfil completo, com base na primeira formação
- **AND** indica os campos que faltam na segunda formação, para o caso de o candidato querer completá-la

#### Scenario: Campos opcionais em branco não impedem a completude

- **WHEN** o candidato preencheu os campos exigidos mas deixou endereço, LinkedIn, pretensão salarial, outras formações reconhecidas pelo MEC e outros cursos/palestras em branco
- **THEN** o portal considera o perfil completo

### Requirement: Formação acadêmica é uma lista

O perfil SHALL permitir zero ou mais formações, cada uma cadastrada, editada e removida de forma independente das demais. Cada formação SHALL guardar, isoladamente: nível de escolaridade, situação do curso, curso, instituição, semestre (relevante apenas quando aquela formação estiver "cursando") e previsão/data de conclusão.

Remover uma formação da lista SHALL NOT afetar as demais formações do mesmo candidato.

Toda tela do portal que exibir a formação do candidato — conferência de candidatura, candidatura já enviada (visão do candidato e do coordenador) — SHALL exibir todas as formações cadastradas, na mesma ordem em que foram cadastradas.

#### Scenario: Candidato adiciona uma segunda formação

- **WHEN** um candidato que já tem uma formação cadastrada adiciona uma nova formação
- **THEN** a formação anterior permanece intacta
- **AND** a nova formação pode ser preenchida de forma independente

#### Scenario: Candidato remove uma formação

- **WHEN** um candidato com duas ou mais formações remove uma delas
- **THEN** as demais formações permanecem no perfil sem alteração
- **AND** a formação removida deixa de aparecer em qualquer tela do portal

#### Scenario: Coordenador vê todas as formações do candidato

- **WHEN** um candidato com múltiplas formações cadastradas se candidata a uma vaga
- **THEN** o coordenador que avalia a candidatura vê todas as formações cadastradas, não apenas uma

### Requirement: Outras qualificações em texto livre

O perfil SHALL disponibilizar dois campos de texto livre, opcionais, distintos da lista de formações:
- um para outras formações superiores reconhecidas pelo MEC (ex.: especialização, mestrado, doutorado, com nome e ano);
- outro para outros cursos, palestras e capacitações (com nome e data).

Esses campos SHALL aceitar texto livre, sem exigir uma estrutura fixa de nome e data separados.

#### Scenario: Candidato relata pós-graduação em texto livre

- **WHEN** o candidato preenche o campo de outras formações reconhecidas pelo MEC descrevendo uma especialização concluída
- **THEN** o portal salva o texto informado no perfil
- **AND** ele aparece junto com as demais informações de formação em qualquer tela que exiba o perfil do candidato

#### Scenario: Campos de texto livre vazios não bloqueiam nada

- **WHEN** o candidato não preenche os campos de outras formações ou de outros cursos/palestras
- **THEN** o portal não trata isso como pendência do perfil nem impede a candidatura

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
