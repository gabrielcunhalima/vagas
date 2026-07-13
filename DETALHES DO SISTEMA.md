Objetivo do sistema:

●	Permitir ao coordenador cadastrar novas vagas de emprego/bolsa/estágio;
●	Exibir vagas em uma página pública na web;
●	Gerenciar candidaturas com etapas de triagem, entrevista e contratação;
●	Integrar com o portal do coordenador para facilitar o preenchimento das solicitações.


3 tipos de usuários: Coordenador, Candidato e Gestor do Projeto.

Tabela sobre os perfis:

Perfil - Descrição - Requer Login?
Coordenador	- Gerencia vagas e candidaturas no portal interno - Sim
Candidato - Visualiza vagas e se candidata via formulário publico - Não
Gestor do Projeto - Permite a divulgação da vaga - Sim

Requisitos funcionais do sistema:

RF01	Coordenador efetua login no portal do coordenador existente;
RF02	Coordenador acessa formulário de cadastro de vagas;
RF03	Coordenador cadastra/edita/desativa vagas com os dados necessários;
RF04	Gestor analisa a vaga;
RF05	Candidato visualiza os detalhes da vaga;
RF06	Candidato preenche o formulário e anexa os documentos necessários;
RF07	Sistema envia e-mail confirmando o recebimento da candidatura;
RF08	Coordenador visualiza os candidatos;
RF09	Coordenador seleciona os candidatos para entrevista, informando data/hora e local;
RF10	Sistema envia e-mail informando os candidatos selecionados;
RF11	Coordenador registra o resultado da entrevista;
RF12	Coordenador seleciona o(s) candidatos aprovados para vaga;
RF13	Sistema envia e-mail de aprovação/reprovação do candidato;
RF14	Coordenador pode reprovar um candidato a qualquer momento.


Requisitos não funcionais do sistema:

RNF01	Sistema deve funcionar em navegadores modernos (Chrome, Firefox, Edge)	Compatibilidade;
RNF02	Upload de documentos suporta PDF, máx. 5MB	Restrição;
RNF03	E-mails enviados via SMTP configurável (Gmail)	Integração;
RNF04	Formulário público protegido com CAPTCHA para evitar spam	Segurança;
RNF05	Dados de candidatos armazenados conforme LGPD	Conformidade;
RNF06	Layout responsivo para acesso via dispositivos móveis	Usabilidade.

Descrição dos casos de uso do sistema:

UC01 – Cadastrar Vaga
Tabela 4- UC01
Campo	Descricao
Ator	Coordenador
Pré-condição	Coordenador autenticado no portal
Descrição	O coordenador acessa o menu de vagas, clica em Nova Vaga e preenche o formulário com título, descrição, requisitos, área, tipo de contrato, remuneração, local de trabalho e data de encerramento das inscrições entre outros dados.
Fluxo Principal	
1. Coordenador acessa menu Vagas > Nova Vaga
2. Preenche todos os campos obrigatórios
3. Clica em Publicar
4. Sistema salva e exibe a vaga na página publica
Fluxo Alternativo	3a. Coordenador clica em Salvar Rascunho: vaga salva com status Inativa, não aparece na página publica
Pós-condição	Vaga publicada e listada na página publica
Regras de Negócio	Data de encerramento deve ser futura. 
Todos os campos são obrigatórios preenchimento, conforme categoria de contratação.

UC02 - Visualizar Vagas
Tabela 5 – UC02
Campo	Descrição
Ator	Candidato (qualquer usuário)
Pré-condição	Nenhuma - pagina aberta ao público
Descrição	O usuário acessa o link público da página de vagas onde irá visualizar todas as vagas ativas. Pode filtrar por área, tipo de contrato ou palavra-chave.
Fluxo Principal	
1. Usuário acessa/vagas
2. Sistema exibe lista de vagas ativas
3. Usuário clica em uma vaga
4. Sistema exibe detalhes completos e botão Candidatar-se
Fluxo Alternativo	Nenhuma
Pós-condição	Apenas vagas com status Ativa e dentro do prazo são exibidas

UC03 - Candidatar-se a uma Vaga
Tabela 6- UC03
Campo	Descrição
Ator	Candidato
Pré-condição	Vaga ativa e dentro do prazo
Descrição	Candidato preenche o formulário de candidatura com seus dados pessoais e faz upload do currículo.
Fluxo Principal	
1. Candidato clica em Candidatar-se
2. Preenche os dados necessários conforme tipo de contratação
3. Resolve CAPTCHA
4. Clica em Enviar Candidatura
5. Sistema salva e envia e-mail de confirmação
Fluxo Alternativo	4a. CPF já cadastrado para essa vaga: sistema exibe mensagem “Você já se candidatou a esta vaga”
Pós-condição	Candidatura salva com status Recebida. 
E-mail enviado ao candidato.
Regras de Negócio	Um candidato (por cpf) pode se candidatar apenas uma vez por vaga.

UC04 - Gerenciar Candidaturas
Tabela 7- UC04
Campo	Descrição
Ator	Coordenador
Pré-condição	Coordenador autenticado, vaga com candidaturas
Descrição	O coordenador acessa a lista de candidatos de uma vaga e altera o status de cada um.
Fluxo Principal	
1. Coordenador acessa Vagas > [Vaga] > Ver Candidatos
2. Visualiza lista com nome, e-mail, data e status
3. Clica em um candidato para ver detalhes e baixar currículo
4. Seleciona candidatos para Entrevista
5. Sistema atualiza status e envia e-mail ao candidato
Fluxo Alternativo	Status do candidato atualizado. E-mail enviado.
Pós-condição	Fluxo de Status: Recebida > Em Analise > Entrevista > Aprovado / Reprovado

UC05 - Selecionar Candidatos para Contratação
Tabela 8- UC05
Campo	Descrição
Ator	Coordenador
Pré-condição	Candidatos com status Entrevista realizada
Descrição	Após as entrevistas, o coordenador marca os candidatos aprovados para contratação e o sistema notifica todos.
Fluxo Principal	1. Coordenador acessa lista de candidatos em etapa de entrevista
2. Seleciona os aprovados e clica em Aprovar para Contratação
3. Para os demais, clica em Reprovar
4. Sistema envia e-mail personalizado para cada candidato
Fluxo Alternativo	Candidatos com status final: Aprovado ou Reprovado. E-mails enviados a todos.
Pós-condição	Obrigatório dar retorno (aprovado ou reprovado) para todos os candidatos entrevistados.

UC06 – Autorização de vagas
Tabela 9- UC06
Campo	Descrição
Ator	Gestor
Pré-condição	Vaga cadastrada
Descrição	O gestor irá avaliar se o projeto possui recurso e previsão para realizar a contratação
Fluxo Principal	1. Gestor acessa sistema 
2. Irá analisar a vaga divulgada
3. Aprovará ou não a vaga.
Fluxo Alternativo	Alteração do status da vaga (Autorizada/Recusada) enviar e-mail ao coordenador
Pós-condição	Obrigatório dar retorno (Autorizada ou recusada) para o coordenador

Fluxograma completo do sistema:

Coordenador faz login
Acessa formulário de nova vaga
Preenche e publica a vaga

Candidato acessa página publica /vagas
Seleciona uma vaga
Preenche formulário de candidatura
Sistema salva e envia EMAIL 1 (Confirmação)

Coordenador acessa lista de candidatos
<Candidato selecionado para entrevista?>

NÃO - Muda status para Reprovado (triagem)
Sistema envia EMAIL 4 (Reprovação)
FIM.

SIM - Muda status para Em Entrevista
Sistema envia EMAIL 2 (Convite entrevista)
Entrevista realizada
<Candidato aprovado?>

NÃO - Muda status para Reprovado
Sistema envia EMAIL 4 (Reprovação)
FIM.

SIM - Muda status para Aprovado
Sistema envia EMAIL 3 (Aprovação)
FIM.

O sistema deve fazer integração com a API ViaCEP, para que seja possivel localizar pelo CEP a RUA, BAIRRO, CIDADE, ESTADO e PAIS.

Descrição das rotas Laravel para o sistema:

Rotas
Método	URI	Controller@Metodo	Descrição
GET	/vagas	VagaPublicaController@index	Lista vagas publicas
GET	/vagas/{vaga}	VagaPublicaController@show	Detalhe da vaga
GET	/candidatura/{vaga}	InscricaoController@create	Formulário de candidatura
POST	/candidatura/{vaga}	InscricaoController@store	Salvar candidatura
GET	/coord/dashboard	DashboardController@index	Painel coordenador
GET	/coord/vagas	VagaController@index	Lista vagas administrativo
GET	/coord/vagas/create	VagaController@create	Formulário nova vaga
POST	/coord/vagas	VagaController@store	Salvar nova vaga
GET	/coord/vagas/{vaga}/edit	VagaController@edit	Formulário edição vaga
PUT	/coord/vagas/{vaga}	VagaController@update	Atualizar vaga
GET	/coord/vagas/{v}/candidaturas	CandidaturaController@index	Listar candidatos
PATCH	/coord/.../status	CandidaturaController@updateStatus	Alterar status


Fluxo de emails do sistema:

Fluxo de E-mails
#	Gatilho	Classe Mail	Assunto	Destinatario
1	Candidatura recebida	CandidaturaRecebidaMail	Candidatura Recebida - [Titulo da Vaga]	Candidato
2	Status -> Entrevista	ConviteEntrevistaMail	Voce foi selecionado para entrevista!	Candidato
3	Status -> Aprovado	AprovacaoMail	Parabens! Voce foi aprovado(a)	Candidato
4	Status -> Reprovado	ReprovacaoMail	Retorno do Processo Seletivo	Candidato
5	Status -> Vaga Autorizada	VagaAutorizada	Sua vaga foi publicada com sucesso	Coordenador
6	Status -> Vaga Rejeitada	VagaRejeitada	Sua vaga foi recusada	Coordenador

Arquivos criados pelo sistema:

Tipo	Nome	Local
Controller	InscricaoController	App/Http/Controllers/Vagas/
Controller	DashboardController	App/Http/Controllers/Vagas/
Controller	VagaController	App/Http/Controllers/Vagas/
Controller	CandidaturaController	App/Http/Controllers/Vagas/
Model	VagaModel	App/Models/Vagas/
View	listaVagas	Resources/views/vagas/
View	CadastrarNovaVaga	Resources/views/vagas/
View	CadastrarNovoCandidato	Resources/views/vagas/
View	ListaCandidados	Resources/views/vagas/
