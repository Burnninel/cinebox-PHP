<?php if (auth()) :?>
    <p> <?= auth()->nome ?> </p>
<?php endif; ?>

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

    <form method="post" action="/login/registrar">
        <div class="formulario">
            <div class="campo">
                <label for="username">Nome:</label>
                <input type="text" id="nome" name="nome">
            </div>
            <div class="campo">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email">
            </div>
            <div class="campo">
                <label for="senha">Senha:</label>
                <input type="text" id="senha" name="senha">
            </div>
            <div class="campo">
                <label for="confirmar_senha">Confirmar senha:</label>
                <input type="text" id="confirmar_senha" name="confirmar_senha">
            </div>
            <button type="submit">Enviar</button>
        </div>
    </form>


    <form method="post" action="/login/autenticar">
        <div class="formulario">
            <div class="campo">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email">
            </div>
            <div class="campo">
                <label for="senha">Senha:</label>
                <input type="text" id="senha" name="senha">
            </div>
            <button type="submit">Enviar</button>
        </div>
    </form>

</div>
