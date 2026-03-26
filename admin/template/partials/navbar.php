<?php
$nome_completo = $_SESSION['nome'];
$primeiro_nome = explode(" ", $nome_completo);
?>

<header class="navbar navbar-expand-md navbar-light sticky-top bg-primary py-3">
    <div class="container-xl d-flex justify-content-between align-items-center">

        <!-- Logo à esquerda -->
        <a href="<?= BASE_URL ?>/index.php" class="navbar-brand">
            <img src="<?= BASE_URL ?>/../assets/img/logo_clothing.svg" alt="Logo" class="navbar-brand-image" style="height: 40px;">
        </a>

        <!-- Botão do colapso (mobile) -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu centralizado -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarMenu">
            <ul class="navbar-nav">

                <!-- Produtos -->
                <li class="nav-item">
                    <a class="nav-link active" style="color: white;" href="<?= BASE_URL ?>/produtos/index.php">Produtos</a>
                </li>
                <!-- Categorias -->
                <li class="nav-item">
                     <a class="nav-link active" style="color: white;" href="<?= BASE_URL ?>/categorias/index.php">Categorias</a>
                </li>

            </ul>
        </div>

        <!-- Perfil à direita -->
        <div class="nav-item dropdown ms-3">
            <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($primeiro_nome[0])?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="perfil.php#senha">Alterar senha</a></li>
                <li>
                    <form method="POST" action="logout.php">
                        <button type="submit" class="dropdown-item">Sair</button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</header>