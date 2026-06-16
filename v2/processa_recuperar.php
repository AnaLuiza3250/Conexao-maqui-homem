<?php
include('conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $conn->real_escape_string($_POST['username']);
    $nova_senha = $conn->real_escape_string($_POST['password']);

    // 1. Verifica se esse usuário realmente existe no banco
    $sql_check = "SELECT id FROM usuarios WHERE usuario = '$usuario'";
    $result_check = $conn->query($sql_check);

    if ($result_check && $result_check->num_rows > 0) {
        // 2. Se existe, atualiza a senha antiga para a nova
        $sql_update = "UPDATE usuarios SET senha = '$nova_senha' WHERE usuario = '$usuario'";
        
        if ($conn->query($sql_update) === TRUE) {
            // Sucesso! Manda de volta para a tela de login com aviso de sucesso
            header("Location: index.php?sucesso=1"); 
            exit();
        } else {
            header("Location: recuperar.php?erro=banco");
            exit();
        }
    } else {
        // Usuário digitado não existe no banco de dados
        header("Location: recuperar.php?erro=nao_existe");
        exit();
    }
}
?>