<?php
include('conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['username']);
    $senha = $conn->real_escape_string($_POST['password']);

    // 1. Verifica se o usuário já existe no banco de dados
    $sql_check = "SELECT id FROM usuarios WHERE usuario = '$usuario'";
    $result_check = $conn->query($sql_check);

    if ($result_check && $result_check->num_rows > 0) {
        // Usuário já cadastrado, manda de volta com erro
        header("Location: cadastro.php?erro=existe");
        exit();
    } else {
        // 2. Insere o novo usuário (Atenção: em produção o ideal é usar password_hash)
        $sql_insert = "INSERT INTO usuarios (usuario, senha) VALUES ('$usuario', '$senha')";
        
        if ($conn->query($sql_insert) === TRUE) {
            // Sucesso! Redireciona para o login informando o sucesso
            header("Location: index.php?sucesso=1");
            exit();
        } else {
            header("Location: cadastro.php?erro=banco");
            exit();
        }
    }
}
?>