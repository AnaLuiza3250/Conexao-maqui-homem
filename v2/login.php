<?php
include('conexao.php');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['username']);
    $senha = $conn->real_escape_string($_POST['password']);

    // Busca o usuário exatamente como foi digitado
    $sql = "SELECT id FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $_SESSION['logado'] = true;
        $_SESSION['usuario'] = $usuario;
        
        header("Location: tela.php");
        exit();
    } else {
        header("Location: index.php?erro=1");
        exit();
    }
}
?>