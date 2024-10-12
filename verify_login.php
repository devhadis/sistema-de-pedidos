<?php

$servername = "";
$username = "";  
$password = ""; 
$dbname = "";  

$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Falha na conexÃ£o: " . $conn->connect_error);
}


$username = $_POST['username'];
$password = $_POST['password'];


$sql = "SELECT * FROM usuarios WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    
    if ($password == $row['password']) {
        
        session_start();
        $_SESSION['username'] = $username;
        
        
        header("Location: index.php");
        exit();
    } else {
        
        header("Location: login.php?error=1");
        exit();
    }
} else {
    
    header("Location: login.php?error=1");
    exit();
}

$conn->close();
