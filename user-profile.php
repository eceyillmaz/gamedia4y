<?php 
include 'baglan.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$target_user_id = mysqli_real_escape_string($baglanti, $_GET['id']);

if ($target_user_id == $user_id) {
    header("Location: profile.php");
    exit();
}

$kullanici_sorgu = mysqli_query($baglanti, "SELECT username, profile_pic FROM users WHERE id = '$target_user_id'");
$kullanici_veri = mysqli_fetch_assoc($kullanici_sorgu);

if (!$kullanici_veri) {
    header("Location: index.php");
    exit();
}

$target_username = htmlspecialchars($kullanici_veri['username']);
$target_pic = !empty($kullanici_veri['profile_pic']) ? $kullanici_veri['profile_pic'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta author="Ece Yilmaz" />
    <title><?php echo $target_username; ?> Profili - GameDiary</title>
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
        
        <div style="text-align: center; margin-top: 20px; margin-bottom: 15px;">
            <img src="images/profiles/<?php echo $target_pic; ?>" alt="<?php echo $target_username; ?> Profil Resmi" 
                 style="width: 140px; height: 140px; border-radius: 50%; object-fit: cover; border: 3px solid #1ba098; box-shadow: 0 0 15px rgba(27,160,152,0.2);">
        </div>

        <h2 style="text-align: center; margin-bottom: 25px;">🎮 <?php echo $target_username; ?> Kullanıcısının Profili</h2>
        
        <?php
        $toplam_oyun_sorgu = mysqli_query($baglanti, "SELECT count(id) as toplam FROM games WHERE user_id = '$target_user_id'");
        $toplam_oyun = mysqli_fetch_assoc($toplam_oyun_sorgu)['toplam'];

        $ortalama_puan_sorgu = mysqli_query($baglanti, "SELECT AVG(rating) as ortalama FROM games WHERE user_id = '$target_user_id' AND rating > 0");
        $ortalama_puan = mysqli_fetch_assoc($ortalama_puan_sorgu)['ortalama'];
        ?>

        <div class="oyunlarigruplama" style="margin-bottom: 40px;">
            <div class="oyunlarinresimleri" style="padding: 20px; text-align: center;">
                <h3 style="color: var(--vurgu-rengi);">Toplam Oyun</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #00d4ff; margin: 10px 0;">
                    <?php echo $toplam_oyun; ?>
                </p>
            </div>

            <div class="oyunlarinresimleri" style="padding: 20px; text-align: center;">
                <h3 style="color: var(--vurgu-rengi);">Ortalama Puanı</h3>
                <p style="font-size: 2rem; font-weight: bold; color: #ffcc00; margin: 10px 0;">
                    <?php echo $ortalama_puan > 0 ? number_format($ortalama_puan, 1) : "0"; ?> / 10
                </p>
            </div>
        </div>

        <hr style="border: 0; height: 1px; background: #1ba098; margin: 40px 0; opacity: 0.3;">

        <h3 style="text-align: center; margin-bottom: 30px; color: #1ba098; font-size: 1.5rem;"><?php echo $target_username; ?> Tarafından Eklenen Oyunlar</h3>
        
        <div class="oyunlarigruplama">
            <?php 
            $arşiv_sorgu = mysqli_query($baglanti, "SELECT * FROM games WHERE user_id = '$target_user_id' ORDER BY id DESC");
            
            if (mysqli_num_rows($arşiv_sorgu) > 0) {
                while($satir = mysqli_fetch_assoc($arşiv_sorgu)) {
                    $kapak_resmi = !empty($satir['cover_url']) ? $satir['cover_url'] : 'images/games.png';
                    ?>
                    <div class="oyunlarinresimleri">
                        <img src="<?php echo $kapak_resmi; ?>" alt="<?php echo $satir['game_name']; ?>" style="border-radius: 8px; max-height: 220px; object-fit: cover;">
                        <h3><?php echo $satir['game_name']; ?></h3>
                        <p>Durum: <b style="color: #1ba098;"><?php echo $satir['status']; ?></b></p>

                        <?php if($satir['rating'] > 0): ?>
                            <p>Puanı: <b style="color: #ffcc00;"><?php echo $satir['rating']; ?>/10</b></p>
                        <?php endif; ?>

                        <?php if(!empty($satir['review'])): ?>
                            <div style="background: rgba(5, 22, 34, 0.6); padding: 10px; border-radius: 6px; margin-top: 10px; font-style: italic; font-size: 0.85rem;">
                                "<?php echo htmlspecialchars($satir['review']); ?>"
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                echo "<p style='grid-column: 1 / -1; text-align: center; opacity: 0.7;'>Bu kullanıcı henüz kütüphanesine oyun eklememiş.</p>";
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