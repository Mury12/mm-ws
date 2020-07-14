<html>

<body style="max-width: 650px;">
    <div class="m-box">
        <h3 class="m-title">Olá. {{name}}!</h3>
        <h4>Sua nova senha</h4>
        <p>
            Recebemos uma solicitação para mudança de senha para seu usuário {{name}}. <br />
            Em anexo, você encontra uma senha segura. Mantenha-a guardada em um local seguro.
        </p>
        <label class="p-box"><small>Sua senha</small><br /><br />
            <span>
                {{msg}}
            </span>
        </label>
        <p style="text-indent: 0">
            Atenciosamente,<br />
            <strong style="margin-left: 10px">Suporte Money Right.</strong>
        </p>
    </div>
</body>

</html>

<style>
    body {
        max-width: 650px;
        margin: 0 auto;
        text-indent: 1em;
    }

    .m-box {
        font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        border: 1px solid rgba(0, 0, 0, 0.225);
        border-radius: 5px;
        padding: 20px
    }

    .p-box {
        display: inline-block;
        font-size: 2em;
        text-align: center;
        width: 100%;
        text-indent: 0;
    }

    .p-box span {

        border-radius: 3px;
        background-color: rgba(0, 0, 0, 0.225);
        box-shadow: inset 0 0 0.1em gray;
        padding: 10px;
    }
</style>