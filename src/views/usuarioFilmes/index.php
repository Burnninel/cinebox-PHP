<?php foreach ($filmes as $filme): ?>
    <div class="card">
        <h2><?= $filme->titulo ?></h2>
        <p><?= $filme->sinopse ?></p>
        <p><strong>Diretor:</strong> <?= $filme->diretor ?></p>
        <p><strong>Ano de Lançamento:</strong> <?= $filme->ano_de_lancamento ?></p>
        <p><strong>Categoria:</strong> <?= $filme->categoria ?></p>
    </div>
<?php endforeach; ?>