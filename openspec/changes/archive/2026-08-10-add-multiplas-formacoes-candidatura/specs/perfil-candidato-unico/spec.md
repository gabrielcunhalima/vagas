## MODIFIED Requirements

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

## ADDED Requirements

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
