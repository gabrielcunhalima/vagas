## 1. Rótulo condicional no formulário de perfil

- [ ] 1.1 Em `resources/js/Pages/Candidato/Perfil/Edit.jsx`, tornar o `label` do `Field` do campo `previsao_conclusao` condicional a `perfil.data.situacao_curso`: "Concluído em" quando `concluido`, "Previsão de conclusão" nos demais casos (`cursando` ou vazio).
- [ ] 1.2 Conferir visualmente o formulário nos três estados (situação vazia, "Cursando", "Concluído") para confirmar o rótulo correto e que o restante do campo (input de data, obrigatoriedade, mensagem de erro) continua funcionando sem alteração.

## 2. Validação

- [ ] 2.1 Rodar a suíte de testes existente relacionada a perfil (ex.: `tests/Unit/PerfilCompletudeTest.php`) para confirmar que nada de comportamento de completude foi afetado, já que a mudança é apenas de rótulo.
