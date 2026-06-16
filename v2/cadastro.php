<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Home - Cadastro</title>
    <link rel="stylesheet" href="style2.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <i class="fa-solid fa-house-chimney home-icon"></i>
            <h1>Smart Home</h1>
            <p>Criar nova conta de acesso</p>
        </div>
        
        <form class="login-form" action="processa_cadastro.php" method="POST">
            
            <?php if(isset($_GET['erro'])): ?>
                <p style="color: #ff4a4a; text-align: center; font-size: 0.9rem; margin-bottom: 15px; font-weight: 600;">
                    <?php 
                        if($_GET['erro'] == 'existe') echo "Este usuário já está cadastrado!";
                        if($_GET['erro'] == 'banco') echo "Erro ao salvar no banco de dados.";
                    ?>
                </p>
            <?php endif; ?>

            <div class="input-group">
                <label for="username">Escolha um Usuário</label>
                <input type="text" id="username" name="username" placeholder="Ex: morador1" required>
            </div>
            
            <div class="input-group">
                <label for="password">Escolha uma Senha</label>
                <input type="password" id="password" name="password" placeholder="Digite uma senha segura" required>
            </div>
            
            <button type="submit" class="btn-login">CADASTRAR</button>
        </form>
        
        <div class="login-footer" style="text-align: center; margin-top: 1.5rem;">
            <a href="index.php" style="color: #6c6c80;">Já tem uma conta? Voltar para o Login</a>
        </div>
    </div>
</body>
</html>