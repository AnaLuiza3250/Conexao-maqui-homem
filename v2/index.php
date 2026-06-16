<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home - Login</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fa-solid fa-house-chimney home-icon"></i>
            <h1>Smart Home</h1>
            <p>Painel de controle residencial</p>
        </div>
        
        <form class="login-form" action="login.php" method="POST">
            
            <?php if(isset($_GET['erro'])): ?>
                <p style="color: #ff4a4a; text-align: center; font-size: 0.9rem; margin-bottom: 15px; font-weight: 600;">
                    Usuário ou senha incorretos!
                </p>
            <?php endif; ?>

            <?php if(isset($_GET['sucesso']) && $_GET['sucesso'] == 1): ?>
                <p style="color: #00bfa5; text-align: center; font-size: 0.9rem; margin-bottom: 15px; font-weight: 600;">
                    Cadastro realizado com sucesso! Faça o login.
                </p>
            <?php endif; ?>

            <div class="input-group">
                <label for="username">Usuário</label>
                <input type="text" id="username" name="username" placeholder="Digite seu usuário" required>
            </div>
            
            <div class="input-group">
                <label for="password">Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite sua senha" required>
            </div>
            
            <button type="submit" class="btn-login">ENTRAR</button>
        </form>
        
        <div class="login-footer" style="display: flex; justify-content: space-between; margin-top: 1.5rem;">
            <a href="recuperar.php">Esqueceu a senha?</a> <a href="cadastro.php" style="color: #00bfa5; font-weight: bold;">Cadastre-se aqui</a>
        </div>
        
    </div>
</body>
</html>