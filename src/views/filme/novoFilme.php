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
            <p><?= htmlspecialchars($erro['mensagem']) ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div class="container">
    <form method="post" action="/filme/novoFilme">
        <div class="formulario">
            <div class="campo">
                <label for="titulo">Titulo:</label>
                <input type="text" id="titulo" name="titulo">
            </div>
            <div class="campo">
                <label for="diretor">Diretor:</label>
                <input type="text" id="diretor" name="diretor">
            </div>
            <div class="campo">
                <label for="ano_de_lancamento">Ano:</label>
                <input type="number" min="1900" max="2100" step="1" id="ano_de_lancamento" name="ano_de_lancamento">
            </div>
            <div class="campo">
                <label for="categoria">Categoria:</label>
                <input type="text" id="categoria" name="categoria">
            </div>
            <div class="campo">
                <label for="sinopse">Sinopse:</label>
                <textarea type="text" id="sinopse" name="sinopse"></textarea>
            </div>
            <button type="submit">Enviar</button>
        </div>
    </form>
</div>