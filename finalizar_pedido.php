<?php

$servername = "";
$username = "";
$password = "";
$dbname = "********";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falt " . $conn->connect_error);
}


$produtos = $_POST['produtos'];
$forma_pagamento = $_POST['forma_pagamento'];
$data_hora = date('Y-m-d H:i:s');


$total = 0;
foreach ($produtos as $id => $quantidade) {
    if ($quantidade > 0) {
        $sql = "SELECT preco FROM produtos WHERE id = $id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $preco = $row['preco'];
        $total += $preco * $quantidade;

        
        $sql_insert_item = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco) VALUES (LAST_INSERT_ID(), $id, $quantidade, $preco)";
        $conn->query($sql_insert_item);
    }
}


$sql_insert_pedido = "INSERT INTO pedidos (forma_pagamento, horario_pagamento, valor_total) VALUES ('$forma_pagamento', '$data_hora', $total)";
$conn->query($sql_insert_pedido);


$data_atual = date('Y-m-d');
$sql_balanco = "SELECT * FROM balanco_diario WHERE data = '$data_atual'";
$result_balanco = $conn->query($sql_balanco);

if ($result_balanco->num_rows > 0) {
    
    $row_balanco = $result_balanco->fetch_assoc();
    $novo_total_vendas = $row_balanco['total_vendas'] + 1;
    $novo_total_arrecadado = $row_balanco['total_arrecadado'] + $total;
    $sql_update_balanco = "UPDATE balanco_diario SET total_vendas = $novo_total_vendas, total_arrecadado = $novo_total_arrecadado WHERE data = '$data_atual'";
    $conn->query($sql_update_balanco);
} else {
    
    $sql_insert_balanco = "INSERT INTO balanco_diario (data, total_vendas, total_arrecadado) VALUES ('$data_atual', 1, $total)";
    $conn->query($sql_insert_balanco);
}


echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Pedido Finalizado</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f4f4f9;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f4f4f9;
            font-weight: 600;
            color: #333;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }

        h3 {
            text-align: center;
            font-size: 20px;
            color: #4CAF50;
            margin-top: 20px;
        }

        p {
            text-align: center;
            font-size: 16px;
            margin-top: 10px;
            color: #555;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #aaa;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h2>Pedido Finalizado</h2>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço</th>
                </tr>
            </thead>
            <tbody>";

foreach ($produtos as $id => $quantidade) {
    if ($quantidade > 0) {
        $sql = "SELECT nome, preco FROM produtos WHERE id = $id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        echo "<tr>
                <td>" . $row['nome'] . "</td>
                <td>$quantidade</td>
                <td>R$ " . number_format($row['preco'], 2, ',', '.') . "</td>
              </tr>";
    }
}

echo "        </tbody>
            </table>
            <h3>Total: R$ " . number_format($total, 2, ',', '.') . "</h3>
            <p>Forma de Pagamento: $forma_pagamento</p>
            <p>Horário do Pedido: $data_hora</p>
        </div>
    </body>
</html>";

$conn->close();
?>
