<?php
// Iniciar a sessão
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['username'])) {
    // Se não estiver, redirecionar para a página de login
    header("Location: login.php");
    exit();
}

// Acesso permitido para usuários logados
echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Página Inicial</title>
    <style>
        /* Estilo da página inicial */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .welcome-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            text-align: center;
        }
        .logout {
            text-align: center;
            margin-top: 20px;
        }
        .logout a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            border-radius: 5px;
        }
        .logout a:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class='welcome-container'>
        <h2>Bem-vindo(a)</h2>
        <p>Olá, " . $_SESSION['username'] . "!</p>
        <div class='logout'>
            <a href='logout.php'>Sair</a>
        </div>
    </div>
</body>
</html>";
?>


<?php

$servername = "localhost";
$username = "username";
$password = "****************";
$dbname = "******** ";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("falt: " . $conn->connect_error);
}


$sql = "SELECT * FROM produtos";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Pedidos</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Sistema de Pedidos</h2>

        <form method="POST" action="finalizar_pedido.php">
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="cardapio-item">
                        <label><?php echo $row['nome']; ?> - R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></label>
                        <input type="number" name="produtos[<?php echo $row['id']; ?>]" value="0" min="0">
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
            
            <h3>Forma de Pagamento</h3>
            <select name="forma_pagamento">
                <option value="Pix">Pix</option>
                <option value="Dinheiro">Dinheiro</option>
                <option value="Cartao">Cartão</option>
            </select>

            <button type="submit">Finalizar Pedido</button>
        </form>

        <h3>Visualizar Balanço Diário</h3>
        <a href="balanco_diario.php" class="btn-balanco">Ver Balanço Diário</a>
    </div>

    <footer>
        <p>&copy; 2024 Sistema de Pedidos</p>
    </footer>
</body>
</html>

<?php $conn->close(); ?>
