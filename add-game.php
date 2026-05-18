<?php 
include 'baglan.php'; // Oturum ve veritabanı bağlantısı
include 'igdb_config.php'; // API fonksiyon dosyası

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 
$arama_sonuclari = [];
$aranan_kelime = "";

if (isset($_GET['oyun_ara_sorgu'])) {
    $aranan_kelime = $_GET['oyun_ara_sorgu'];
    if (!empty($aranan_kelime)) {
        $body = "search \"{$aranan_kelime}\"; fields name, cover.url; limit 100;";
        $arama_sonuclari = igdb_sorgu_at("games", $body);
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta author="Ece Yilmaz" />
    <title>GameDiary - Oyun Ekle</title>
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
    <h2 id="icerik">Oyun Ara</h2>
    <p class="subtitle"> <i> Eklemek istediğiniz oyunu aratarak bulabilirsiniz.</i> </p>

    <div style="text-align: center; margin-bottom: 40px;">
        <form action="add-game.php" method="GET">
            <input type="text" name="oyun_ara_sorgu" placeholder="Örn: Witcher 3, Cyberpunk, GTA V..." value="<?php echo htmlspecialchars($aranan_kelime); ?>" required
                   style="padding: 12px; width: 50%; border-radius: 6px; border: 1px solid #1ba098; background: #0b1d2a; color: white;">
            <button type="submit" class="buton yes-buton" style="padding: 12px 25px; margin-left: 10px; width: auto; display: inline-block;">Veritabanında Ara</button>
        </form>
    </div>

    <div class="oyunlarigruplama">
        <?php if (!empty($arama_sonuclari) && !isset($arama_sonuclari['error'])): ?>
            <?php foreach ($arama_sonuclari as $oyun): 
                $resim_url = "images/games.png";
                if (isset($oyun['cover']['url'])) {
                    $resim_url = str_replace("t_thumb", "t_cover_big", $oyun['cover']['url']);
                    if (substr($resim_url, 0, 2) == "//") {
                        $resim_url = "https:" . $resim_url;
                    }
                }
            ?>
                <div class="oyunlarinresimleri">
                    <img src="<?php echo $resim_url; ?>" alt="<?php echo $oyun['name']; ?>" style="border-radius: 8px; max-height: 250px; object-fit: cover;">
                    <h3><?php echo $oyun['name']; ?></h3>
                    
                    <form action="islem.php" method="POST">
                        <input type="hidden" name="game_name" value="<?php echo htmlspecialchars($oyun['name']); ?>">
                        <input type="hidden" name="cover_url" value="<?php echo $resim_url; ?>">
                        
                        <label>Durum:</label>
                        <select name="played" class="secim-kutusu" style="margin-bottom: 10px;">
                            <option value="Oynandı">Oynandı</option>
                            <option value="İstek Listesi">İstek Listesi</option>
                            <option value="İzlendi">İzlendi</option>
                        </select>
                        
                        <button type="submit" name="api_oyun_ekle_butonu" class="buton yes-buton">Arşive Ekle</button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php elseif(isset($_GET['oyun_ara_sorgu'])): ?>
            <p style="text-align: center; color: #ff4d4d;">
                <?php echo isset($arama_sonuclari['error']) ? $arama_sonuclari['error'] : "Hiçbir oyun bulunamadı."; ?>
            </p>
        <?php endif; ?>
    </div>
</section>
</main>

<footer>
    <p>GameDiary | Kişisel Oyun Arşivi | Tüm Hakları Saklıdır.</p>
</footer>

<script src="js/games.js"></script>
</body>
</html>