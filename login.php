<?php
session_start();
require './config/conexao.php';

$basePath = str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '')));
$basePath = ($basePath === '/' || $basePath === '.') ? '' : rtrim($basePath, '/');
$loginCssPath = __DIR__ . '/assets/css/login.css';
$loginCssHref = $basePath . '/assets/css/login.css';

if (file_exists($loginCssPath)) {
    $loginCssHref .= '?v=' . filemtime($loginCssPath);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $sql = "SELECT * FROM usuarios WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute(); 

    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['nome'] = $usuario['nome']; 
            header("Location: admin/index.php");
            exit;
        } else {
            die("Senha ou Email não encontrado!");
        }
    } 
}
?>



<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= htmlspecialchars($loginCssHref, ENT_QUOTES, 'UTF-8') ?>">
</head> 
<body style="background-color: rgb(46, 49, 49);" >
    <!-- From Uiverse.io by Yaya12085 --> 
    <form method="post" class="form">
       <p class="form-title">Fazer login</p>
        <div class="input-container">
          <input name="email" placeholder="E-mail" type="email">
            <span>
              <svg stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
              </svg>
            </span>
      </div>
      <div class="input-container">
          <input name="senha" placeholder="Senha" type="password">
            <span>
              <svg stroke="currentColor" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
              <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"></path>
              </svg>
            </span>
        </div>
        <button id=entrarAdm class="submit" type="submit">Entrar</button>
        <div class = "text-center mt-5">      
        <a href="#" class="signup-link">Esqueceu a senha?
        <b></b>
        <a href="#" onclick="alert('Um link de recuperação será enviado para seu e-mail cadastrado.');"> Recuperar</a>
        </a>
        </div>
        
        
    </form>













    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
