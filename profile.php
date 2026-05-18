<?php 
include 'baglan.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$kullanici_cek = mysqli_query($baglanti, "SELECT * FROM users WHERE id = '$user_id'");
$kullanici_bilgi = mysqli_fetch_assoc($kullanici_cek);

$profil_resmi = !empty($kullanici_bilgi['profile_pic']) ? $kullanici_bilgi['profile_pic'] : 'default.png';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Profilim - GameDiary</title>
    <link rel="stylesheet" href="css/style.css">
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
        <h2 style="text-align: center; margin-bottom: 20px;">Merhaba, <?php echo $_SESSION['username']; ?>! 🎮</h2>
        
        <div style="text-align: center; margin-bottom: 30px;">
            <img src="images/profiles/<?php echo $profil_resmi; ?>" alt="Profil Resmi" 
                 style="width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 3px solid #1ba098; box-shadow: 0 0 15px rgba(27,160,152,0.3);">
            
            <form action="islem.php" method="POST" enctype="multipart/form-data" style="margin-top: 20px;">
                <input type="file" name="avatar" id="avatar-input" accept="image/*" required style="display: none;">
                
                <label for="avatar-input" class="buton" style="display: inline-block; width: auto; padding: 10px 20px; background: #0b1d2a; border: 1px solid #1ba098; color: #1ba098; border-radius: 6px; cursor: pointer; font-weight: bold; font-size: 0.85rem; transition: 0.2s; margin-bottom: 10px;">
                    📂 Bilgisayardan Fotoğraf Seç
                </label>
                
                <div id="dosya-adi-alani" style="font-size: 0.8rem; opacity: 0.7; margin-bottom: 15px; min-height: 15px;">Henüz bir dosya seçilmedi</div>

                <button type="submit" name="profil_resmi_yukle" class="buton yes-buton" 
                        style="padding: 10px 20px; width: auto; display: inline-block; font-size: 0.85rem; margin-top: 0;">
                    ✨ Fotoğrafı Güncelle
                </button>
            </form>
            
            <?php if(isset($_GET['yukleme'])): ?>
                <?php if($_GET['yukleme'] == "basarili"): ?>
                    <p style="color: #00d4ff; font-size: 0.9rem; margin-top: 10px;">Profil fotoğrafın başarıyla güncellendi!</p>
                <?php elseif($_GET['yukleme'] == "hata"): ?>
                    <p style="color: #ff4d4d; font-size: 0.9rem; margin-top: 10px;">Dosya yüklenirken bir hata oluştu.</p>
                <?php elseif($_GET['yukleme'] == "gecersizuzanti"): ?>
                    <p style="color: #ff4d4d; font-size: 0.9rem; margin-top: 10px;">Lütfen sadece JPG, JPEG veya PNG formatında resim yükleyin!</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <?php
        $toplam_oyun_sorgu = mysqli_query($baglanti, "SELECT count(id) as toplam FROM games WHERE user_id = '$user_id'");
        $toplam_oyun = mysqli_fetch_assoc($toplam_oyun_sorgu)['toplam'];

        $ortalama_puan_sorgu = mysqli_query($baglanti, "SELECT AVG(rating) as ortalama FROM games WHERE user_id = '$user_id' AND rating > 0");
        $ortalama_puan = mysqli_fetch_assoc($ortalama_puan_sorgu)['ortalama'];
        ?>

        <div class="oyunlarigruplama">
            <div class="oyunlarinresimleri" style="padding: 30px; text-align: center;">
                <h3 style="color: var(--vurgu-rengi);">Toplam Oyun</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: #00d4ff; margin: 10px 0;">
                    <?php echo $toplam_oyun; ?>
                </p>
            </div>

            <div class="oyunlarinresimleri" style="padding: 30px; text-align: center;">
                <h3 style="color: var(--vurgu-rengi);">Ortalama Puanın</h3>
                <p style="font-size: 2.5rem; font-weight: bold; color: #ffcc00; margin: 10px 0;">
                    <?php echo $ortalama_puan > 0 ? number_format($ortalama_puan, 1) : "0"; ?> / 10
                </p>
            </div>
        </div>
    </section>
</main>

<footer>
    <p>GameDiary | Kişisel Oyun Arşivi | Tüm Hakları Saklıdır.</p>
</footer>

<script>
    document.getElementById('avatar-input').addEventListener('change', function() {
        var dosyaAdi = this.files[0] ? this.files[0].name : "Henüz bir dosya seçilmedi";
        document.getElementById('dosya-adi-alani').innerText = "Seçilen Dosya: " + dosyaAdi;
    });
</script>
<script src="js/games.js"></script>
</body>
</html>