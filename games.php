<?php 
include 'baglan.php'; // Veritabanı bağlantısı

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} 
$user_id = $_SESSION['user_id']; 

// --- ZARİF SAYFALAMA AYARLARI ---
$sayfa_basina_oyun = 5; // Yan yana grid yapın bozulmasın diye 12 idealdir
$mevcut_sayfa = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
if ($mevcut_sayfa < 1) { $mevcut_sayfa = 1; }

// Toplam oyun adetini hesapla
$toplam_oyun_sorgu = mysqli_query($baglanti, "SELECT COUNT(DISTINCT game_name) as toplam FROM games");
$toplam_oyun_adet = mysqli_fetch_assoc($toplam_oyun_sorgu)['toplam'];
$toplam_sayfa = ceil($toplam_oyun_adet / $sayfa_basina_oyun);

$baslangic_limiti = ($mevcut_sayfa - 1) * $sayfa_basina_oyun;
// ---------------------------------
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>GameDiary - Yorum ve Puanlama</title>
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

<main style="min-height: 75vh; display: flex; flex-direction: column; justify-content: space-between;">
    <section class="arananoyunlar" style="flex-grow: 1;">
        <h2>Oyunlarını Puanla & Süre Ekle</h2>
        <div class="oyunlarigruplama">
            <?php 
            $sorgu = mysqli_query($baglanti, "SELECT * FROM games GROUP BY game_name ORDER BY id DESC LIMIT $baslangic_limiti, $sayfa_basina_oyun");
            while($satir = mysqli_fetch_assoc($sorgu)) {
                ?>
                <div class="oyunlarinresimleri" style="padding: 20px; text-align: left; min-height: 380px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <h3><?php echo $satir['game_name']; ?></h3>
                        
                        <?php if(!empty($satir['cover_url'])): ?>
                            <img src="<?php echo $satir['cover_url']; ?>" alt="<?php echo $satir['game_name']; ?>" style="width: 100%; max-height: 180px; object-fit: cover; border-radius: 6px; margin-bottom: 10px; display: block;">
                        <?php endif; ?>
                    </div>
                    
                    <form action="islem.php" method="POST" style="margin-top: auto;">
                        <input type="hidden" name="game_id" value="<?php echo $satir['id']; ?>">
                        
                        <div style="margin-bottom: 10px; display: flex; gap: 15px;">
                            <div>
                                <label style="display:block; margin-bottom:5px;">Puanın (1-10):</label>
                                <input type="number" name="rating" min="1" max="10" value="<?php echo $satir['rating']; ?>" style="width: 60px; padding: 5px; background-color: #051622 !important; color: #ffffff !important; border: 1px solid rgba(27, 160, 152, 0.6) !important; border-radius: 4px;">
                            </div>
                            <div>
                                <label style="display:block; margin-bottom:5px;">Oynama Süresi (Saat):</label>
                                <input type="number" name="playtime" min="0" value="<?php echo $satir['playtime']; ?>" style="width: 80px; padding: 5px; background-color: #051622 !important; color: #ffffff !important; border: 1px solid rgba(27, 160, 152, 0.6) !important; border-radius: 4px;">
                            </div>
                        </div>
                        
                        <label>Yorumun:</label>
                        <textarea name="review" style="width: 100%; height: 60px; padding: 5px; box-sizing: border-box; background-color: #051622 !important; color: #ffffff !important; border: 1px solid rgba(27, 160, 152, 0.6) !important; border-radius: 4px; resize: vertical;"><?php echo $satir['review']; ?></textarea>
                        
                        <button type="submit" name="puan_kaydet" class="buton yes-buton" style="margin-top:10px;">Kaydet</button>
                    </form>
                </div>
                <?php
            }
            ?>
        </div>
    </section>

    <?php if($toplam_sayfa > 1): ?>
        <div style="display: flex; width: 100%; max-width: 500px; margin: 50px auto 20px auto; font-family: inherit; color: #ffffff; user-select: none;">
            
            <div style="flex: 0 0 35%; text-align: right; box-sizing: border-box;">
                <?php if($mevcut_sayfa > 1): ?>
                    <a href="?sayfa=<?php echo $mevcut_sayfa - 1; ?>" style="color: #1ba098; text-decoration: none; padding: 5px 10px; display: inline-block;">[ Önceki ]</a>
                <?php else: ?>
                    <span style="color: #444; padding: 5px 10px; display: inline-block; cursor: default;">[ Önceki ]</span>
                <?php endif; ?>
            </div>

            <div style="flex: 0 0 30%; text-align: center; font-weight: bold; box-sizing: border-box; white-space: nowrap;">
                Sayfa <?php echo $mevcut_sayfa; ?> / <?php echo $toplam_sayfa; ?>
            </div>

            <div style="flex: 0 0 35%; text-align: left; box-sizing: border-box;">
                <?php if($mevcut_sayfa < $toplam_sayfa): ?>
                    <a href="?sayfa=<?php echo $mevcut_sayfa + 1; ?>" style="color: #1ba098; text-decoration: none; padding: 5px 10px; display: inline-block;">[ Sonraki ]</a>
                <?php else: ?>
                    <span style="color: #444; padding: 5px 10px; display: inline-block; cursor: default;">[ Sonraki ]</span>
                <?php endif; ?>
            </div>

        </div>
    <?php endif; ?>
    </main>

<script src="js/games.js"></script>
</body>
</html>