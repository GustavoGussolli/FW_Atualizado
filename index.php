<?php
$servidor = $_POST['servidor'] ?? '';
$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';
$bancos = [];
$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conectar'])) {
    try {
        $conn = new PDO("mysql:host=$servidor", $usuario, $senha);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->query("SHOW DATABASES");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bancos[] = $row['Database'];
        }
    } catch (PDOException $e) {
        $erro = "Erro ao conectar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Conectar ao Banco</title>
    <link rel="stylesheet" href="estilos.css">
</head>
<body>
    <form method="post">
        <div class="container">
            <h2>Informações de Conexão</h2>

            <?php if ($erro): ?>
                <div class="mensagem_erro"><?= $erro ?></div>
            <?php endif; ?>

            <label for="servidor">Servidor:</label>
            <input type="text" id="servidor" name="servidor" value="<?= htmlspecialchars($servidor) ?>" required>

            <label for="usuario">Usuário:</label>
            <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($usuario) ?>" required>

            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" value="<?= htmlspecialchars($senha) ?>">

            <?php if (!empty($bancos)): ?>
                <label for="banco">Escolha o Banco de Dados:</label>
                <select name="banco" id="banco" required>
                    <option value="" disabled selected>Selecione uma base</option>
                    <?php foreach ($bancos as $banco): ?>
                        <option value="<?= htmlspecialchars($banco) ?>"><?= htmlspecialchars($banco) ?></option>
                    <?php endforeach; ?>
                </select>
                <br>
                <button type="submit" formaction="creator.php">Avançar</button>
            <?php else: ?>
                <button type="submit" name="conectar">Conectar</button>
            <?php endif; ?>
        </div>
    </form>
</body>
</html>
