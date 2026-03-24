<?php
  require '../../config/conexao.php';

  $stmt = $pdo->query("SELECT * FROM produtos where situacao = 1 ORDER BY id DESC");
  $produtos = $stmt->fetchAll();

?>


<H1>Produtos</H1>
<table class="table">
    <thead>
      <tr>                      
        <th>Nome</th>
        <th>Descrição</th>
        <th>Preço(R$)</th>
        <th>Estoque</th>           
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
            <td>
              <a href="form.php?id=<?= htmlspecialchars($produto['id'])?>" class="btn btn-outline-warning btn-sm">Editar</a>
              <a href="deletar.php?id=<?= htmlspecialchars($produto['id'])?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Certeza que deseja excluir?')">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5">Nenhum produto cadastrado</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<a href="form.php" class="btn btn-outline-primary">Novo produto</a>


