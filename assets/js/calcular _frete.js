<script>
const form = document.getElementById('freteForm');
const resultado = document.getElementById('resultadoFrete');
const loading = document.getElementById('loading');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    resultado.style.display = 'none';
    loading.style.display = 'block';

    const formData = new FormData(this);

    fetch('', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        loading.style.display = 'none';

        if (data.erro) {
            resultado.innerHTML = `<p class="text-danger">${data.erro}</p>`;
        } else {
            resultado.innerHTML = `
                <ul>
                    <li><strong>Estado:</strong> ${data.estado}</li>
                    <li><strong>Tipo:</strong> ${data.tipo}</li>
                    <li><strong>Valor:</strong> R$ ${data.valor}</li>
                    <li><strong>Prazo:</strong> ${data.prazo} dias</li>
                </ul>
            `;
        }

        resultado.style.display = 'block';
    });
});
</script>