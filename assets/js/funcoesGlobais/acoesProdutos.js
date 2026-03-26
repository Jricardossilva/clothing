import { sweetAlertError, sweetAlertSucessAction, sweetAlertSucessAction, sweetAlertConfirmDelete, sweetAlertSucess } from "./sweetAlerts/sweetAlerts.js";

console.log('cadastrou');
const cadastroProduto = document.getElementById('cadastroProduto');

if (cadastroProduto){
    sweetAlertSucess();
} else {
    sweetAlertError();
};

const editarProduto = document.getElementById('editarProduto');

if (editarProduto){
    sweetAlertSucessAction();
} else {
    sweetAlertError();
}














