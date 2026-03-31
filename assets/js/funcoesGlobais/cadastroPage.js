import { sweetAlertSucess} from "./sweetAlerts/sweetAlerts.js";


const cadastroForm = document.getElementById('cadastroProduto');

if (cadastroForm) {
    cadastroForm.addEventListener('submit', (event) => {
        event.preventDefault();

        sweetAlertSucess();

        setTimeout(() => {
            cadastroForm.submit();
        }, 1500);
    });
}

