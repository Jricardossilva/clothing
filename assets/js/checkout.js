async function buscaCEP() {
    const cep = document.getElementById('cep').value.replace(/\D/g, '');
    
    if (cep.length !== 8) return;

    try {
        const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
        const data = await response.json();

        if (data.erro) {
            alert("CEP não encontrado!");
            return;
        }

        document.getElementById('logradouro').value = data.logradouro;
        document.getElementById('bairro').value = data.bairro;
        document.getElementById('localidade').value = data.localidade;
        document.getElementById('uf').value = data.uf;
        
    } catch (error) {
        console.error("Erro ao buscar o CEP:", error);
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
        alert("Por favor, selecione o arquivo do comprovante antes de confirmar.");
        return;
    }

    alert("🚀 Sucesso! O comprovante de " + arquivo.name + " foi enviado para análise.");
    arquivoInput.value = "";
    fecharModal();
}