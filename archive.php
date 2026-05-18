<?php 
include 'baglan.php'; // Veritabanı bağlantısı

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} 
$user_id = $_SESSION['user_id']; 
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>GameDiary - Oyun Arşivim</title>
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
        <h2>Kişisel Oyun Arşivim</h2>
        <div class="oyunlarigruplama">
            <?php 
            $sorgu = mysqli_query($baglanti, "SELECT * FROM games WHERE user_id = '$user_id' ORDER BY id DESC");
            if (mysqli_num_rows($sorgu) > 0) {
                while($satir = mysqli_fetch_assoc($sorgu)) {
                    $kapak_resmi = !empty($satir['cover_url']) ? $satir['cover_url'] : 'images/games.png';
                    ?>
                    <div class="oyunlarinresimleri" style="padding: 20px; text-align: center;">
                        <img src="<?php echo $kapak_resmi; ?>" alt="<?php echo $satir['game_name']; ?>" style="border-radius: 8px; max-height: 220px; object-fit: cover; margin-bottom: 15px; width: 100%;">
                        <h3><?php echo $satir['game_name']; ?></h3>
                        <p style="margin: 5px 0;">Durum: <b style="color: #1ba098;"><?php echo $satir['status']; ?></b></p>
                        
                        <?php if($satir['playtime'] > 0): ?>
                            <p style="margin: 5px 0; font-size: 0.9rem;">⏱️ Süre: <b style="color: #00d4ff;"><?php echo $satir['playtime']; ?> Saat</b></p>
                        <?php endif; ?>

                        <?php if($satir['rating'] > 0): ?>
                            <p style="margin: 5px 0;">Puanım: <b style="color: #ffcc00;"><?php echo $satir['rating']; ?>/10</b></p>
                        <?php endif; ?>

                        <?php if(!empty($satir['review'])): ?>
                            <div style="background: rgba(5, 22, 34, 0.6); padding: 10px; border-radius: 6px; margin-top: 10px; font-style: italic; font-size: 0.85rem; text-align: left;">
                                "<?php echo htmlspecialchars($satir['review']); ?>"
                            </div>
                        <?php endif; ?>

                        <div style="margin-top: 15px;">
                            <a href="islem.php?sil=<?php echo $satir['id']; ?>" class="buton no-buton" style="text-decoration: none; font-size: 0.85rem; padding: 6px 12px; display: inline-block; width: auto;" onclick="return confirm('Bu oyunu arşivinizden silmek istediğinize emin misiniz?');">Oyunu Sil</a>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1 / -1; text-align: center; opacity: 0.7;'>Arşivinizde henüz oyun bulunmuyor.</p>";
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