import './bootstrap';
import { initTema } from './tema';
import { initSenhaForca } from './senha-forca';
import { initCurriculoDropzone } from './curriculo-dropzone';
import { initSheets } from './ui/sheet';
import { initFlash } from './flash';

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
}

window.initWidgets = initWidgets;

document.addEventListener('DOMContentLoaded', () => initWidgets());
