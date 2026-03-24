<!-- deletar categorias -->
<?php
require '../../config/conexao.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM categorias WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>