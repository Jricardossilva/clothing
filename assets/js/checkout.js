async function buscaCEP() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length !== 8) return;

    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();

        if (data.erro) {
            Swal.fire({
                icon: "error",
                title: "CEP não encontrado",
                text: "Verifique o CEP informado e tente novamente."
            });
            return;
        }

        document.getElementById('logradouro').value = data.logradouro;
        document.getElementById('bairro').value = data.bairro;
        document.getElementById('localidade').value = data.localidade;
        document.getElementById('uf').value = data.uf;
        
    } catch (error) {
        console.error("Erro ao buscar o CEP:", error);
        Swal.fire({
            icon: "error",
            title: "Erro",
            text: "Não foi possível buscar o CEP."
        });
    }
}

document.addEventListener("DOMContentLoaded", function() {
    const modal = document.getElementById("modalPixContainer");
    const pixRadio = document.getElementById("pix-Radio");

    const spanClose = document.querySelector(".close-btn") || document.querySelector(".close");

    if (pixRadio) {
        pixRadio.addEventListener("change", function() {
            if (this.checked) {
                modal.style.display = "block";
                document.body.style.overflow = "hidden"; 
            }
        });
    }

    if (spanClose) {
        spanClose.onclick = function() {
            fecharModal();
        }
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            fecharModal();
        }
    }
});

function fecharModal() {
    const modal = document.getElementById("modalPixContainer");
    modal.style.display = "none";
    document.body.style.overflow = "auto";
}

function enviarDados() {
    const arquivoInput = document.getElementById('comprovante');
    const arquivo = arquivoInput.files[0];
    
    if (!arquivo) {
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Por favor, selecione o comprovante antes de confirmar."
        });
        return;
    }

    Swal.fire({
        title: "Pagamento enviado!",
        text: "O comprovante " + arquivo.name + " foi enviado para análise.",
        icon: "success"
    });

    arquivoInput.value = "";
    fecharModal();
}