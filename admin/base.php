<?php
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.php");
    exit();
}
// Defina a base do seu projeto (ajuste se necessário)
define('BASE_URL', 'http://localhost/clothing/admin');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- usa BASE_URL aqui também -->
    <link rel="stylesheet" href="http://localhost/clothing/assets/css/style.css">
</head>

<body class="d-flex flex-column min-vh-100">

<?php include __DIR__ . '/template/partials/navbar.php'; ?>

<main class="flex-fill">
    <div class="container py-4">
        <?php
        if (isset($content) && file_exists($content)) {
            include $content;
        } else {
            echo "<p>Página não encontrada!</p>";
        }
        ?>
    </div>
</main>

<?php include __DIR__ . '/template/partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>