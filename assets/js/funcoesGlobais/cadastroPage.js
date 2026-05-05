import { sweetAlertSucess} from "./sweetAlerts/sweetAlerts.js";


const cadastroForm = document.getElementById('cadastroProduto');

if (cadastroForm) {
    const imagemInput = document.getElementById('formFileSm');
    const submitButton = cadastroForm.querySelector('button[type="submit"]');
    const existingImages = imagemInput
        ? JSON.parse(imagemInput.dataset.existingImages || '[]')
        : [];

    const validarNomeImagem = () => {
        if (!imagemInput || !imagemInput.files.length) {
            if (imagemInput) {
                imagemInput.setCustomValidity('');
                imagemInput.classList.remove('is-invalid');
            }

            if (submitButton) {
                submitButton.disabled = false;
            }

            return true;
        }

        const nomeImagem = imagemInput.files[0].name.toLowerCase();
        const imagemJaExiste = existingImages.includes(nomeImagem);

        imagemInput.setCustomValidity(imagemJaExiste ? 'Imagem duplicada' : '');
        imagemInput.classList.toggle('is-invalid', imagemJaExiste);

        if (submitButton) {
            submitButton.disabled = imagemJaExiste;
        }

        return !imagemJaExiste;
    };

    if (imagemInput) {
        imagemInput.addEventListener('change', validarNomeImagem);
    }

    cadastroForm.addEventListener('submit', (event) => {
        event.preventDefault();

        if (!validarNomeImagem() || !cadastroForm.checkValidity()) {
            cadastroForm.reportValidity();
            return;
        }

        sweetAlertSucess();

        setTimeout(() => {
            cadastroForm.submit();
        }, 1500);
    });
}

