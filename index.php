<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Formulário de Conexão</title>
    <link rel="stylesheet" href="estilos.css">
</head>

<body>
    <div id="mensagem"></div>
    <form action="creator.php" method="POST">

        <?php
        $msg = $_GET['msg'] ?? null;
        include 'erros.php';
        if (isset($_GET[$msg])){
            echo "<div id='mensagem'>" . ($mensagens[$msg] ?? "Erro desconhecido") . "</div>";
        }
        ?>
        <h1>EasyMVC</h1>
        <h2>Configuração</h2>
        <label for="servidor">Servidor:</label>
        <input type="text" id="servidor" name="servidor" required>

        <label for="banco">Banco de Dados:</label>
        <input type="text" id="banco" name="banco" required>

        <label for="usuario">Usuário:</label>
        <input type="text" id="usuario" name="usuario" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha">

        <button type="submit">Enviar</button>
    </form>

</body>

</html>