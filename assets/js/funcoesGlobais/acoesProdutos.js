import { sweetAlertError, sweetAlertSucessAction, sweetAlertConfirmDelete, sweetAlertSucess } from "./sweetAlerts/sweetAlerts.js";

console.log('cadastrou');
const cadastroProduto = document.getElementById('cadastroProduto');

if (cadastroProduto){
    sweetAlertSucess();
};

const editarProduto = document.getElementById('editarProduto');

if (editarProduto){
    sweetAlertSucessAction();
};

function deletar(event, url) {
    event.preventDefault();

    Swal.fire({
        title: "Tem certeza?",
        text: "Não será possível reverter essa ação!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, deletar!"
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    });
}

window.deletar = deletar;















