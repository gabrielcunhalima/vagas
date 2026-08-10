## 1. Extrair conteúdo compartilhado da Política de Privacidade

- [x] 1.1 Criar `resources/js/components/PoliticaPrivacidadeConteudo.jsx` movendo para lá o JSX de conteúdo (cabeçalho com ícone, tópicos e texto) hoje em [PoliticaPrivacidade.jsx](resources/js/Pages/Publico/PoliticaPrivacidade.jsx), sem alterar texto nem estrutura visual
- [x] 1.2 Atualizar `PoliticaPrivacidade.jsx` para montar `PoliticaPrivacidadeConteudo` dentro do `PublicLayout`, mantendo o comportamento e a aparência atuais da página `/politica-privacidade`

## 2. Pop-up no formulário de criação de conta

- [x] 2.1 Em [Registro.jsx](resources/js/Pages/Candidato/Auth/Registro.jsx), substituir o `Link` para `route('politica.privacidade')` por um `Dialog`/`DialogTrigger` (de [components/ui/dialog.jsx](resources/js/components/ui/dialog.jsx)) que abre um pop-up com `PoliticaPrivacidadeConteudo`, preservando a aparência do texto "Política de Privacidade" (cor primária, sublinhado)
- [x] 2.2 Ajustar o `DialogContent` do pop-up para largura maior (ex.: `sm:max-w-2xl`) e rolagem interna (ex.: `max-h-[80vh] overflow-y-auto`), adequado ao texto longo da política
- [x] 2.3 Confirmar que fechar o pop-up (botão de fechar, Esc, clique fora) não altera nem submete os valores já preenchidos no formulário (CPF, e-mail, senha, confirmação, checkbox de consentimento)

## 3. Verificação

- [x] 3.1 Testar manualmente: preencher parcialmente o formulário, abrir o pop-up, fechar, e confirmar que todos os campos preenchidos permanecem intactos
- [x] 3.2 Testar manualmente em mobile e desktop que o pop-up é legível (rolagem, largura, títulos dos tópicos) e que o foco retorna ao formulário ao fechar
- [x] 3.3 Confirmar que a página pública `/politica-privacidade` (acessada pelo rodapé/menu) continua funcionando normalmente, sem regressão visual
