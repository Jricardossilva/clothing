
export function sweetAlertSucess() {
    Swal.fire({
            title: 'Cadastro realizado com sucesso!',
            icon: "success",
            draggable: true,
        });
};

export function sweetAlertError() {
    Swal.fire({
            icon: "error",
            title: "Algo deu errado!",
            text: "Tente novamente",
        });
};


export function sweetAlertConfirmDelete () {
        Swal.fire({
        title: "Tem certeza?",
        text: "Não será possível reverter essa ação!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sim, delete isso!"
        }).then((result) => {
                if (result.isConfirmed) {
                        Swal.fire({
                        title: "Deletado!",
                        text: "Seu arquivo foi excluído.",
                        icon: "success"
                        });
                }
        });
};

export function sweetAlertSucessAction () {
    Swal.fire({
            title: 'Ação realizada com sucesso!',
            icon: "success",
            draggable: true,
        });
};