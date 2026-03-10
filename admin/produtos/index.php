<?php
  require '../../config/conexao.php';

  $stmt = $pdo->query("SELECT * FROM produtos where situacao = 1 ORDER BY id DESC");
  $produtos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <div class="container">
    <H1>Produtos</H1>
    <table class="table">
        <thead>
          <tr>                      
            <th>Nome</th>
            <th>Descrição</th>
            <th>Preço(R$)</th>
            <th>Estoque</th>
            <th>Categoria</th>            
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if($produtos): ?>
            <?php foreach($produtos as $produto): ?>
              <tr>               
                <td><?= htmlspecialchars($produto['nome'])?></td>
                <td><?= htmlspecialchars($produto['descricao'])?></td>
                <td><?= htmlspecialchars($produto['preco'])?></td>
                <td><?= htmlspecialchars($produto['estoque'])?></td> 
                <td><?= htmlspecialchars($produto['categoria'])?></td> 
                <td>
                  <a href="editar.php?id=<?= htmlspecialchars($produto['id'])?>" class="btn btn-outline-warning btn-sm">Editar</a>
                  <a href="apagar.php?id=<?= htmlspecialchars($produto['id'])?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Certeza que deseja excluir?')">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">Nenhum produto cadastrado</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
      <a href="cadastrar.php" class="btn btn-outline-primary">Novo produto</a>
    </div>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>

