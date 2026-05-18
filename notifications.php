<?php 
include 'baglan.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
mysqli_query($baglanti, "UPDATE notifications SET is_read = 1 WHERE user_id = '$user_id'");
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta author="Ece Yilmaz" />
    <title>Bildirimlerim - GameDiary</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/games.png">
</head>
<body>

<header class="navigasyonkismi">
    <div class="logo-alani">
        <h1><a href="index.php" style="color: inherit; text-decoration: none;">GameDiary</a></h1>
        <div class="user-welcome">Hoş geldin, <?php echo $_SESSION['username']; ?></div>
    </div>
    <nav>
        <a href="index.php">Ana Sayfa</a>
        <a href="add-game.php">Oyun Ekle</a> 
        <a href="games.php">Yorum ve Puanlama</a>
        <a href="archive.php">Oyun Arşivim</a>
        <a href="profile.php">Profilim</a>
        
        <?php
        $okunmamis_sorgu = mysqli_query($baglanti, "SELECT COUNT(id) as adet FROM notifications WHERE user_id = '$user_id' AND is_read = 0");
        $okunmamis_adet = mysqli_fetch_assoc($okunmamis_sorgu)['adet'];
        ?>
        <a href="notifications.php" style="position: relative;">
            Bildirimlerim
            <?php if($okunmamis_adet > 0): ?>
                <span style="background: #ff4d4d; color: white; border-radius: 50%; padding: 2px 6px; font-size: 0.75rem; font-weight: bold; position: absolute; top: -8px; right: -12px;">
                    <?php echo $okunmamis_adet; ?>
                </span>
            <?php endif; ?>
        </a>
        
        <a href="about.php">Bu Sayfa Hakkında</a>
    </nav>
    <div class="sag-butonlar">
        <button id="Tema-toggle">Koyu Modu Aç/Kapat</button>
        <a href="cikis.php" class="cikis-alt-link">Çıkış Yap</a>
    </div>
</header>

<main>
    <section class="arananoyunlar">
        <h2 style="text-align: center; margin-bottom: 30px;">🔔 Bildirim Merkezim</h2>
        
        <div style="max-width: 600px; margin: 0 auto;">
            <?php
            $bildirim_listesi = mysqli_query($baglanti, "SELECT * FROM notifications WHERE user_id = '$user_id' ORDER BY id DESC");
            
            if (mysqli_num_rows($bildirim_listesi) > 0) {
                while($satir = mysqli_fetch_assoc($bildirim_listesi)) {
                    $detay_linki = "index.php#yorum-" . $satir['game_id'];
                    ?>
                    <a href="<?php echo $detay_linki; ?>" style="text-decoration: none; display: block; margin-bottom: 12px;">
                        <div style="background: #0b1d2a; border: 1px solid rgba(27, 160, 152, 0.3); padding: 15px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: 0.2s;" 
                             onmouseover="this.style.borderColor='#1ba098'; this.style.background='rgba(27, 160, 152, 0.05)';" 
                             onmouseout="this.style.borderColor='rgba(27, 160, 152, 0.3)'; this.style.background='#0b1d2a';">
                            
                            <div style="font-size: 0.95rem; color: white;">
                                <span>🔔 <?php echo $satir['message']; ?></span>
                            </div>
                            <div style="font-size: 0.8rem; opacity: 0.5; font-style: italic; color: white;">
                                <?php echo date("H:i", strtotime($satir['created_at'])); ?>
                            </div>
                        </div>
                    </a>
                    <?php
                }
            } else {
                echo "<p style='text-align: center; opacity: 0.6; font-style: italic; margin-top: 50px;'>Henüz hiç bildiriminiz bulunmuyor.</p>";
            }
            ?>
        </div>
    </section>
</main>

<footer>
    <p>GameDiary | Kişisel Oyun Arşivi | Tüm Hakları Saklıdır.</p>
</footer>

<script src="js/games.js"></script>
</body>
</html>