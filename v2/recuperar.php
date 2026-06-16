<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home - Recuperar Senha</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fa-solid fa-key home-icon"></i>
            <h1>Recuperar Acesso</h1>
            <p>Digite seu usuário para criar uma nova senha</p>
        </div>
        
        <form class="login-form" action="processa_recuperar.php" method="POST">
            
            <?php if(isset($_GET['erro'])): ?>
                <p style="color: #ff4a4a; text-align: center; font-size: 0.9rem; margin-bottom: 15px; font-weight: 600;">
                    <?php 
                        if($_GET['erro'] == 'nao_existe') echo "Usuário não encontrado no sistema!";
                        if($_GET['erro'] == 'banco') echo "Erro ao atualizar a senha no banco.";
                    ?>
                </p>
            <?php endif; ?>

            <div class="input-group">
                <label for="username">Seu Usuário</label>
                <input type="text" id="username" name="username" placeholder="Digite seu usuário cadastrado" required>
            </div>
            
            <div class="input-group">
                <label for="password">Nova Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite a nova senha segura" required>
            </div>
            
            <button type="submit" class="btn-login">ALTERAR SENHA</button>
        </form>
        
        <div class="login-footer" style="text-align: center; margin-top: 1.5rem;">
            <a href="index.php" style="color: #6c6c80;">Voltar para o Login</a>
        </div>
    </div>
</body>
</html>