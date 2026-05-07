import { sweetAlertSucess} from "./sweetAlerts/sweetAlerts.js";


const cadastroForm = document.getElementById('cadastroProduto');

if (cadastroForm) {
    cadastroForm.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!cadastroForm.checkValidity()) {
            cadastroForm.reportValidity();
            return;
        }

        sweetAlertSucess();

        setTimeout(() => {
            cadastroForm.submit();
        }, 1500);
    });
}

