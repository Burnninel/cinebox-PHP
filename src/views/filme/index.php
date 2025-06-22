<?php if ($validacoes = flash()->getMensagem('error')) : ?>
    <?php foreach ($validacoes as $erro): ?>
        <div>
            <p class="errosFormulario"><?= htmlspecialchars($erro['mensagem']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php if ($validacoes = flash()->getMensagem('success')) : ?>
    <?php foreach ($validacoes as $erro): ?>
        <div>
            <p class="errosFormulario"><?= htmlspecialchars($erro['mensagem']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<h1 style="margin-top: 10px;"><?= $filme->titulo ?></h1>

<form action="/filme/favoritarFilme?id=<?= $filme->id ?>" method="POST">
    <button type="submit">Favoritar</button>
</form>

<form action="/filme/desfavoritarFilme?id=<?= $filme->id ?>" method="POST">
    <button type="submit">Desfavoritar</button>
</form>

<form action="/avaliacoes/criarAvaliacao?id=<?= $filme->id ?>" method="POST" style="margin-top: 20px;">
    <div class="campo">
        <label for="nota">Nota:</label>
        <input type="text" id="nota" name="nota">
    </div>
    <div class="campo">
        <label for="comentario">Comentario:</label>
        <textarea type="text" id="comentario" name="comentario"></textarea>
    </div>
    <button type="submit">Avaliar</button>
</form>

<?php foreach ($avaliacoes as $item): ?>
    <div class="card">
        <h2><?= $item->usuario ?></h2>
        <p><?= $item->comentario ?></p>
        <p><?= $item->nota ?></p>
    </div>
    <form action="/filme/excluirAvaliacao?id=<?= $item->id ?>" method="POST">
        <button type="submit">Excluir Avaliação</button>
    </form>
<?php endforeach; ?>