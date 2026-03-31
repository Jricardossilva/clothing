import { sweetAlertSucess, sweetAlertError } from "./sweetAlerts/sweetAlerts.js";


const loginForm = document.getElementById('loginForm');

if (loginForm) {
    loginForm.addEventListener('submit', (event) => {
        const senhaLogin = document.getElementById('passwordInput').value;
        const senhaConfirmadaLogin = document.getElementById('confirmPasswordInput').value;

        if (senhaLogin !== senhaConfirmadaLogin) {
            event.preventDefault();
            sweetAlertError();
            return;
        }

        sweetAlertSucess();
    });
}