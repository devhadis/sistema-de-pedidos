<?php

$servername = "";
$username = "";
$password = "";
$dbname = "****";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falt " . $conn->connect_error);
}


$data_atual = date('Y-m-d');
$sql_balanco = "SELECT total_vendas, total_arrecadado FROM balanco_diario WHERE data = '$data_atual'";
$result_balanco = $conn->query($sql_balanco);

$total_vendas = 0;
$total_arrecadado = 0;

if ($result_balanco->num_rows > 0) {
    $row_balanco = $result_balanco->fetch_assoc();
    $total_vendas = $row_balanco['total_vendas'];
    $total_arrecadado = $row_balanco['total_arrecadado'];
}


$sql_vendas = "SELECT p.id, p.forma_pagamento, p.valor_total, p.horario_pagamento 
               FROM pedidos p 
               WHERE DATE(p.horario_pagamento) = '$data_atual'";
$result_vendas = $conn->query($sql_vendas);


echo "<!DOCTYPE html>
<html lang='pt-BR'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Balanço Diário</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;500&display=swap' rel='stylesheet'>
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
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .total {
            font-weight: bold;
            font-size: 18px;
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
        <h2>Balanço Diário - $data_atual</h2>
        <table>
            <thead>
                <tr>
                    <th>ID do Pedido</th>
                    <th>Forma de Pagamento</th>
                    <th>Valor</th>
                    <th>Horário</th>
                </tr>
            </thead>
            <tbody>";


if ($result_vendas->num_rows > 0) {
    while ($row_venda = $result_vendas->fetch_assoc()) {
        echo "<tr>
                <td>" . $row_venda['id'] . "</td>
                <td>" . $row_venda['forma_pagamento'] . "</td>
                <td>R$ " . number_format($row_venda['valor_total'], 2, ',', '.') . "</td>
                <td>" . $row_venda['horario_pagamento'] . "</td>
              </tr>";
    }
    
    
    $sql_total_vendas = "SELECT SUM(valor_total) AS total_arrecadado, COUNT(id) AS total_vendas FROM pedidos WHERE DATE(horario_pagamento) = '$data_atual'";
    $result_total_vendas = $conn->query($sql_total_vendas);
    
    if ($result_total_vendas->num_rows > 0) {
        $row_total_vendas = $result_total_vendas->fetch_assoc();
        echo "<tr>
                <td colspan='2' style='text-align:right;'>Total de Vendas:</td>
                <td colspan='2'>" . $row_total_vendas['total_vendas'] . " vendas</td>
              </tr>
              <tr>
                <td colspan='2' style='text-align:right;'>Total Arrecadado:</td>
                <td colspan='2'>R$ " . number_format($row_total_vendas['total_arrecadado'], 2, ',', '.') . "</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='4'>Nenhuma venda registrada hoje.</td></tr>";
}

echo "      </tbody>
        </table>
    </div>
    <footer>
        <p>&copy; 2024 Meu Cardápio - Balanço Diário</p>
    </footer>
</body>
</html>";

$conn->close();
?>
