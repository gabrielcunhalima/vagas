## Purpose

Garante que, em botões que combinam ícone e rótulo de texto, o rótulo apareça centralizado no botão — e não o bloco ícone+texto como conjunto — para que o texto do CTA fique visualmente alinhado ao centro do controle em qualquer tamanho ou posição de ícone.

## ADDED Requirements

### Requirement: Texto centralizado em botão com ícone

Quando um botão (`Button`) contém um ícone (`data-icon="inline-start"` ou `data-icon="inline-end"`) junto de um rótulo de texto, o rótulo de texto SHALL aparecer centralizado no centro horizontal do botão. O ícone SHALL permanecer ao lado indicado (início ou fim) sem deslocar o centro visual do texto.

#### Scenario: Ícone à esquerda do texto

- **WHEN** um botão é renderizado com ícone em `data-icon="inline-start"` seguido do rótulo de texto
- **THEN** o rótulo de texto fica centralizado no centro horizontal do botão, não o bloco ícone+texto

#### Scenario: Ícone à direita do texto

- **WHEN** um botão é renderizado com o rótulo de texto seguido de ícone em `data-icon="inline-end"`
- **THEN** o rótulo de texto fica centralizado no centro horizontal do botão, não o bloco texto+ícone

#### Scenario: Troca de ícone mantém o texto parado

- **WHEN** o ícone de um botão é trocado por outro de largura diferente (ex.: `Save` por `Loader2` durante o envio de um formulário)
- **THEN** a posição horizontal do rótulo de texto não se desloca

#### Scenario: Todos os tamanhos de botão

- **WHEN** um botão com ícone e texto é renderizado em qualquer tamanho suportado (`xs`, `sm`, `default`, `lg`)
- **THEN** o rótulo de texto aparece centralizado no centro horizontal do botão nesse tamanho

### Requirement: Botões sem ícone continuam centralizados

Botões que não possuem `data-icon` SHALL continuar com o texto centralizado no botão exatamente como antes desta mudança — nenhuma regressão de alinhamento para o caso sem ícone.

#### Scenario: Botão somente texto

- **WHEN** um botão é renderizado sem nenhum ícone, apenas com rótulo de texto
- **THEN** o rótulo de texto aparece centralizado no botão, sem alteração de comportamento em relação ao estado anterior
