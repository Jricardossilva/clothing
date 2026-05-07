<?php
    require '../../config/conexao.php'; 

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];

        $sql = ("UPDATE produtos SET situacao = 0 WHERE id = :id");
        $stmt = $pdo->prepare($sql);
      
        $stmt->execute(['id' => $id]);

    } else {
        echo "ID de produto inválido.";
    }

    header("Location: index.php");
    exit();
?>
