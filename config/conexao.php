<?php
    $host = 'projetointegrador.com.br';
    $db   = 'u115459815_clothing';
    $user = 'u115459815_site_clothing';
    $pass = 'Websenac123';
    $charset = 'utf8mb4';
    $config = "mysql:host=$host;dbname=$db;charset=$charset";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($config, $user, $pass, $options);
    } catch (PDOException $e) {
        echo "Erro ao conectar ao banco de dados: " . $e->getMessage();
    }

?>