<?php 
include 'baglan.php'; 

if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap - GameDiary</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { 
            background-color: #051622; 
            color: white; 
            transition: 0.3s; 
            margin: 0;
            font-family: sans-serif;
        }
        
        body.light-mode { 
            background-color: #ffffff; 
            color: #051622; 
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            border-bottom: 2px solid #1ba098;
            background: rgba(0,0,0,0.2);
        }

        .logo-text {
            color: #1ba098;
            font-size: 28px;
            font-weight: bold;
            margin: 0;
            text-decoration: none;
        }
        .login-card {
            background: #0b1d2a;
            border: 2px solid #1ba098;
            padding: 40px;
            border-radius: 12px;
            width: 380px;
            margin-top: 80px;
            box-shadow: 0 0 20px rgba(27, 160, 152, 0.2);
        }

        body.light-mode .login-card {
            background: #f9f9f9;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #1ba098;
            font-weight: bold;
        }

        .input-group input {
            width: 100%;
            padding: 12px;
            background: #051622;
            border: 1px solid #1ba098;
            color: white;
            border-radius: 6px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        body.light-mode .input-group input {
            background: white;
            color: #051622;
        }

        .btn-main {
            width: 100%;
            background: #1ba098;
            color: #051622;
            border: none;
            padding: 14px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-main:hover {
            background: #14817a;
        }

        .mode-btn {
            background: #1ba098;
            color: #051622;
            border: none;
            padding: 8px 18px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<header class="header-container">
    <div class="left-section">
        <a href="index.php" class="logo-text">GameDiary</a>
    </div>
    
    <div class="right-section">
        <button onclick="toggleTheme()" class="mode-btn">
            Koyu Modu Aç/Kapat
        </button>
    </div>
</header>

<main style="display: flex; justify-content: center;">
    <div class="login-card">
        <h2 style="text-align: center; color: #1ba098; margin-bottom: 30px; font-size: 24px;">Giriş Yap</h2>

        <form action="islem.php" method="POST">
            <div class="input-group">
                <label>Kullanıcı Adı</label>
                <input type="text" name="username" placeholder="Kullanıcı adınızı girin" required>
            </div>

            <div class="input-group">
                <label>Şifre</label>
                <input type="password" name="password" placeholder="Şifrenizi girin" required>
            </div>

            <button type="submit" name="giris_yap_butonu" class="btn-main">Giriş Yap</button>
        </form>

        <p style="text-align: center; margin-top: 25px; font-size: 14px;">
            Henüz hesabın yok mu? <a href="register.php" style="color: #deb992; text-decoration: none; font-weight: bold;">Kayıt Ol</a>
        </p>
    </div>
</main>

<script>
    function toggleTheme() {
        document.body.classList.toggle('light-mode');
    }
</script>

</body>
</html>