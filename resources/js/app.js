import './bootstrap';
import { initTema } from './tema';
import { initSenhaForca } from './senha-forca';
import { initCurriculoDropzone } from './curriculo-dropzone';
import { initSheets } from './ui/sheet';
import { initFlash } from './flash';
import { initVagasSelecao } from './vagas-selecao';
import { initVagasFiltro } from './vagas-filtro';
import { initCopiarLink } from './copiar-link';
import { initCandidatura } from './candidatura';
import { initSenhaVisivel } from './senha-visivel';
import { initCadastroCpf } from './cadastro-cpf';
import { initDialogs } from './ui/dialog';
import { initCamposCondicionais } from './campo-condicional';
import { initFormacoes } from './formacoes';
import { initCepAutofill } from './cep-autofill';
import { initTelefoneMask } from './telefone-mask';

/*
 * Sem SPA, cada página é um documento novo — DOMContentLoaded cobre o caso
 * comum. `initWidgets` também fica exposta em `window` para telas que trocam
 * um trecho do DOM via fetch (ex.: filtros de /vagas) religarem os widgets
 * do fragmento recém-inserido sem duplicar listener no resto da página.
 */
function initWidgets(root = document) {
    initTema(root);
    initSenhaForca(root);
    initCurriculoDropzone(root);
    initSheets(root);
    initFlash(root);
    initVagasSelecao(root);
    initVagasFiltro(root);
    initCopiarLink(root);
    initCandidatura(root);
    initSenhaVisivel(root);
    initCadastroCpf(root);
    initDialogs(root);
    initCamposCondicionais(root);
    initFormacoes(root);
    initCepAutofill(root);
    initTelefoneMask(root);
}

window.initWidgets = initWidgets;

document.addEventListener('DOMContentLoaded', () => initWidgets());
