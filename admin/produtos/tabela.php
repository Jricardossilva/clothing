<?php
  require '../../config/conexao.php';

  $stmt = $pdo->query("SELECT * FROM produtos where situacao = 1 ORDER BY id DESC");
  $produtos = $stmt->fetchAll();

?>



<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Produtos</h1>
    <a href="form.php" class="btn btn-primary">Cadastrar</a>
</div>
<table class="table table-hover">
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
              <a href="form.php?id=<?= htmlspecialchars($produto['id'])?>" class="btn btn-warning">Editar</a>
              <a href="deletar.php?id=<?= htmlspecialchars($produto['id'])?>" class="btn btn-danger" onclick="deletar(event, this.href)">Deletar</a>
            </td>
          </tr>
        <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="5">Nenhum produto cadastrado</td></tr>
    <?php endif; ?>
    </tbody>
</table>

<script type="module" src="../../assets/js/funcoesGlobais/acoesProdutos.js"></script>