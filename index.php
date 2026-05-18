<?php 
include 'baglan.php'; // Oturum ve veritabanı bağlantısı

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta author="Ece Yilmaz" />
    <title>GameDiary - Kişisel Oyun Arşivi</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/games.png">
    <style>
        html {
            scroll-behavior: smooth;
        }
    </style>
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
    
    <h2>Son Eklediğin Oyunlar Galerisi</h2>
    <p class="subtitle"> <i>Arşivine en son eklediğin oyunlar burada listelenir.</i> </p>

    <div class="oyunlarigruplama" style="margin-bottom: 50px;">
        <?php 
        $sorgu = mysqli_query($baglanti, "SELECT * FROM games WHERE user_id = '$user_id' ORDER BY id DESC LIMIT 5");
        
        if (mysqli_num_rows($sorgu) > 0) {
            while($satir = mysqli_fetch_assoc($sorgu)) {
                $kapak_resmi = !empty($satir['cover_url']) ? $satir['cover_url'] : 'images/games.png';
                ?>
                <div class="oyunlarinresimleri">
                    <img src="<?php echo $kapak_resmi; ?>" alt="<?php echo $satir['game_name']; ?>" style="border-radius: 8px; max-height: 250px; object-fit: cover;">
                    <h3><?php echo $satir['game_name']; ?></h3>
                    <p>Durum: <b style="color: #1ba098;"><?php echo $satir['status']; ?></b></p>
                    
                    <?php if($satir['rating'] > 0): ?>
                        <p>Puanım: <b style="color: #ffcc00;"><?php echo $satir['rating']; ?>/10</b></p>
                    <?php else: ?>
                        <p style="font-size: 0.85rem; font-style: italic; opacity: 0.7;">Henüz puanlanmadı</p>
                    <?php endif; ?>

                    <div style="margin-top: 15px;">
                        <a href="games.php" class="buton yes-buton" style="text-decoration: none; font-size: 0.85rem; padding: 8px 12px; display: inline-block; width: auto;">Düzenle</a>
                    </div>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="oyunlarinresimleri" style="grid-column: 1 / -1; padding: 40px; text-align: center; width: 100%;">
                <h3>Kütüphanen Henüz Boş 🎮</h3>
                <p style="margin: 15px 0; opacity: 0.8;">Hemen dünyadaki yüz binlerce oyun arasından ilk oyununu eklemek ister misin?</p>
                <a href="add-game.php" class="buton yes-buton" style="text-decoration: none; display: inline-block; width: auto; padding: 10px 25px;">İlk Oyununu Ekle</a>
            </div>
            <?php
        }
        ?>
    </div> 

    <hr style="border: 0; height: 1px; background: #1ba098; margin: 40px 0; opacity: 0.3;">

    <h2>Oyuncu Topluluğundan Son Yorumlar 💬</h2>
    <p class="subtitle"> <i>GameDiary kullanıcılarının oyunlar hakkında yaptığı en son yorumlar ve puanlamalar!</i> </p>

    <div class="oyunlarigruplama">
        <?php 
        $sosyal_sorgu = mysqli_query($baglanti, "
            SELECT games.*, users.username 
            FROM games 
            INNER JOIN users ON games.user_id = users.id 
            WHERE games.review != '' 
            ORDER BY games.id DESC 
            LIMIT 5
        ");
        
        if (mysqli_num_rows($sosyal_sorgu) > 0) {
            while($sosyal_satir = mysqli_fetch_assoc($sosyal_sorgu)) {
                $sosyal_kapak = !empty($sosyal_satir['cover_url']) ? $sosyal_satir['cover_url'] : 'images/games.png';
                
                if ($sosyal_satir['user_id'] == $user_id) {
                    $yazar_profil_linki = "profile.php";
                } else {
                    $yazar_profil_linki = "user-profile.php?id=" . $sosyal_satir['user_id'];
                }
                ?>
                <div class="oyunlarinresimleri" id="yorum-<?php echo $sosyal_satir['id']; ?>" style="border: 1px solid rgba(27, 160, 152, 0.4); background: rgba(11, 29, 42, 0.8);">
                    <img src="<?php echo $sosyal_kapak; ?>" alt="<?php echo $sosyal_satir['game_name']; ?>" style="border-radius: 8px; max-height: 200px; object-fit: cover; opacity: 0.9;">
                    
                    <h3 style="margin-top: 10px;"><?php echo $sosyal_satir['game_name']; ?></h3>
                    
                    <p style="font-size: 0.9rem; margin: 5px 0; color: white;">
                        ✍️ Yazan: <a href="<?php echo $yazar_profil_linki; ?>" style="color: #deb992; text-decoration: none; font-weight: bold; border-bottom: 1px solid rgba(222, 185, 146, 0.4); transition: 0.2s;" onmouseover="this.style.color='#1ba098'; this.style.borderColor='#1ba098';" onmouseout="this.style.color='#deb992'; this.style.borderColor='rgba(222, 185, 146, 0.4)';">
                            <?php echo htmlspecialchars($sosyal_satir['username']); ?>
                        </a>
                    </p>
                    
                    <p>Durum: <b><?php echo $sosyal_satir['status']; ?></b></p>
                    <p>Verilen Puan: <b style="color: #ffcc00;"><?php echo $sosyal_satir['rating']; ?>/10</b></p>
                    
                    <div style="background: rgba(5, 22, 34, 0.6); padding: 10px; border-radius: 6px; margin-top: 10px; font-style: italic; min-height: 50px; font-size: 0.9rem;">
                        "<?php echo htmlspecialchars($sosyal_satir['review']); ?>"
                    </div>

                    <div class="alt-yorumlar-alani" style="margin-top: 15px; border-top: 1px solid rgba(27, 160, 152, 0.2); padding-top: 10px;">
                        <?php
                        $mevcut_game_id = $sosyal_satir['id'];
                        $alt_yorum_sorgu = mysqli_query($baglanti, "
                            SELECT comments.*, users.username 
                            FROM comments 
                            INNER JOIN users ON comments.user_id = users.id 
                            WHERE comments.game_id = '$mevcut_game_id' 
                            ORDER BY comments.id ASC
                        ");
                        
                        while($alt_satir = mysqli_fetch_assoc($alt_yorum_sorgu)) {
                            if ($alt_satir['user_id'] == $user_id) {
                                $cevap_profil_linki = "profile.php";
                            } else {
                                $cevap_profil_linki = "user-profile.php?id=" . $alt_satir['user_id'];
                            }
                            ?>
                            <div style="background: rgba(255,255,255,0.05); padding: 8px; border-radius: 4px; margin-bottom: 8px; font-size: 0.85rem; border-left: 3px solid #deb992; text-align: left; color: white;">
                                <a href="<?php echo $cevap_profil_linki; ?>" style="color: #deb992; text-decoration: none; font-weight: bold; transition: 0.2s;" onmouseover="this.style.color='#1ba098';" onmouseout="this.style.color='#deb992';">
                                    <?php echo htmlspecialchars($alt_satir['username']); ?>:
                                </a> 
                                <span><?php echo htmlspecialchars($alt_satir['comment_text']); ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <form action="islem.php" method="POST" style="margin-top: 15px; display: table; width: 100%; border-collapse: separate; border-spacing: 8px 0; box-sizing: border-box;">
                        <input type="hidden" name="game_id" value="<?php echo $sosyal_satir['id']; ?>">
                        
                        <div style="display: table-cell; vertical-align: middle;">
                            <input type="text" name="comment_text" placeholder="Bu yoruma cevap yaz..." required 
                                   style="width: 100%; padding: 10px 12px; background: #051622; border: 1px solid #1ba098; color: white; border-radius: 4px; font-size: 0.85rem; height: 38px; box-sizing: border-box; display: block; margin: 0;">
                        </div>
                        
                        <div style="display: table-cell; vertical-align: middle; width: 10px; white-space: nowrap;">
                            <button type="submit" name="cevap_yaz_butonu" 
                                    style="padding: 0 20px; font-size: 0.85rem; height: 38px; background: #1ba098; color: #0b1d2a; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; box-sizing: border-box; display: block; margin: 0; line-height: 38px;">
                                Gönder
                            </button>
                        </div>
                    </form>
                </div>
                <?php
            }
        } else {
            ?>
            <p style="grid-column: 1 / -1; text-align: center; opacity: 0.7; font-style: italic;">Henüz toplulukta yapılmış bir oyun yorumu bulunmuyor.</p>
            <?php
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