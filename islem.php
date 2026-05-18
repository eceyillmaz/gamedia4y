<?php
include 'baglan.php'; // Veritabanı bağlantısı

// --- 1. KAYIT OLMA İŞLEMİ ---
if (isset($_POST['kayit_ol_butonu'])) {
    $kullanici_adi = $_POST['username'];
    $sifre = $_POST['password'];

    if (empty($kullanici_adi) || empty($sifre)) {
        header("Location: register.php?durum=bosalan");
        exit();
    }

    $sorgu = "INSERT INTO users (username, password) VALUES ('$kullanici_adi', '$sifre')";
    
    if (mysqli_query($baglanti, $sorgu)) {
        header("Location: login.php?kayit=basarili"); 
    } else {
        header("Location: register.php?durum=hata");
    }
}

// --- 2. GİRİŞ YAPMA İŞLEMİ ---
if (isset($_POST['giris_yap_butonu'])) {
    $kullanici_adi = $_POST['username'];
    $sifre = $_POST['password'];

    if (empty($kullanici_adi) || empty($sifre)) {
        header("Location: login.php?hata=bosalan");
        exit();
    }

    $sorgu = mysqli_query($baglanti, "SELECT * FROM users WHERE username = '$kullanici_adi' AND password = '$sifre'");
    $kullanici_verisi = mysqli_fetch_assoc($sorgu);

    if ($kullanici_verisi) {
        $_SESSION['user_id'] = $kullanici_verisi['id'];
        $_SESSION['username'] = $kullanici_verisi['username'];
        header("Location: index.php?giris=basarili");
    } else {
        header("Location: login.php?hata=hatalisifre");
    }
}

// --- 3. FORM İLE OYUN EKLEME İŞLEMİ ---
if (isset($_POST['oyun_ekle_butonu'])) {
    $game_name = $_POST['game_name'];
    $game_type = $_POST['game_type'];
    $played    = $_POST['played'];
    $user_id   = $_SESSION['user_id']; 

    if (empty($game_name) || empty($game_type)) {
        header("Location: add-game.php?hata=bosalan");
        exit();
    }

    $sorgu = "INSERT INTO games (user_id, game_name, genre, status) VALUES ('$user_id', '$game_name', '$game_type', '$played')";
    
    if (mysqli_query($baglanti, $sorgu)) {
        header("Location: games.php?durum=eklendi"); 
    } else {
        echo "Hata: " . mysqli_error($baglanti);
    }
}

// --- 4. OYUN SİLME İŞLEMİ ---
if (isset($_GET['sil'])) {
    $silinecek_id = $_GET['sil'];
    $user_id = $_SESSION['user_id'];

    $sil_sorgu = "DELETE FROM games WHERE id = '$silinecek_id' AND user_id = '$user_id'";
    
    if (mysqli_query($baglanti, $sil_sorgu)) {
        header("Location: archive.php?silme=basarili");
    } else {
        echo "Silme hatası: " . mysqli_error($baglanti);
    }
}

// --- 5. PUAN, YORUM VE SÜRE GÜNCELLEME ---
if (isset($_POST['puan_kaydet'])) {
    $game_id   = $_POST['game_id'];
    $rating    = $_POST['rating'];
    $review    = $_POST['review'];
    $playtime  = intval($_POST['playtime']); // Süre bilgisini tam sayı olarak alıyoruz

    if (empty($rating) || empty($review)) {
        header("Location: games.php?hata=bosalan");
        exit();
    }

    $guncelle = "UPDATE games SET rating = '$rating', review = '$review', playtime = '$playtime' WHERE id = '$game_id'";

    if (mysqli_query($baglanti, $guncelle)) {
        header("Location: archive.php?guncelleme=basarili");
    } else {
        echo "Hata: " . mysqli_error($baglanti);
    }
}

// --- 6. ANA SAYFADAN SEÇİMLİ HIZLI OYUN EKLEME ---
if (isset($_POST['hizli_ekle_buton'])) {
    $game_name = $_POST['game_name'];
    $status    = $_POST['played']; 
    $user_id   = $_SESSION['user_id']; 

    $sorgu = "INSERT INTO games (user_id, game_name, status) VALUES ('$user_id', '$game_name', '$status')";
    
    if (mysqli_query($baglanti, $sorgu)) {
        header("Location: games.php?durum=eklendi");
    } else {
        echo "Hata: " . mysqli_error($baglanti);
    }
}

// --- 7. IGDB API'DEN GELEN DİNAMİK OYUNU VERİTABANINA KAYDETME (DÜZELTİLDİ) ---
if (isset($_POST['api_oyun_ekle_butonu'])) { // HATA BURADAYDI, PARANTEZLER DÜZELTİLDİ
    $game_name = $_POST['game_name'];
    $cover_url = $_POST['cover_url'];
    $status    = $_POST['played']; 
    $user_id   = $_SESSION['user_id']; 

    $sorgu = "INSERT INTO games (user_id, game_name, cover_url, status) VALUES ('$user_id', '$game_name', '$cover_url', '$status')";
    
    if (mysqli_query($baglanti, $sorgu)) {
        header("Location: games.php?durum=eklendi");
    } else {
        echo "Veritabanı Kayıt Hatası: " . mysqli_error($baglanti);
    }
}

// --- 8. PROFİL FOTOĞRAFI YÜKLEME ---
if (isset($_POST['profil_resmi_yukle'])) {
    $user_id = $_SESSION['user_id'];
    
    $dosya_adi = $_FILES['avatar']['name'];
    $dosya_gecici_yol = $_FILES['avatar']['tmp_name'];
    $dosya_hatasi = $_FILES['avatar']['error'];
    
    if ($dosya_hatasi === 0) {
        $dosya_uzantisi = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));
        $izin_verilen_uzantilar = ['jpg', 'jpeg', 'png'];
        
        if (in_array($dosya_uzantisi, $izin_verilen_uzantilar)) {
            $yeni_dosya_adi = "user_" . $user_id . "_" . time() . "." . $dosya_uzantisi;
            $hedef_klasor_yolu = "images/profiles/" . $yeni_dosya_adi;
            
            if (move_uploaded_file($dosya_gecici_yol, $hedef_klasor_yolu)) {
                $guncelle_sorgu = "UPDATE users SET profile_pic = '$yeni_dosya_adi' WHERE id = '$user_id'";
                if (mysqli_query($baglanti, $guncelle_sorgu)) {
                    header("Location: profile.php?yukleme=basarili");
                    exit();
                }
            }
        } else {
            header("Location: profile.php?yukleme=gecersizuzanti");
            exit();
        }
    }
    header("Location: profile.php?yukleme=hata");
    exit();
}

// --- 9. TOPLULUK YORUMUNA ALT CEVAP YAZMA VE BİLDİRİM GÖNDERME ---
if (isset($_POST['cevap_yaz_butonu'])) {
    $game_id = $_POST['game_id'];
    $comment_text = $_POST['comment_text'];
    $user_id = $_SESSION['user_id'];

    if (empty($comment_text)) {
        header("Location: index.php?hata=bosyorum");
        exit();
    }

    $sorgu = "INSERT INTO comments (game_id, user_id, comment_text) VALUES ('$game_id', '$user_id', '$comment_text')";
    
    if (mysqli_query($baglanti, $sorgu)) {
        $oyun_sahibi_sorgu = mysqli_query($baglanti, "SELECT user_id, game_name FROM games WHERE id = '$game_id'");
        $oyun_sahibi_veri = mysqli_fetch_assoc($oyun_sahibi_sorgu);
        
        if ($oyun_sahibi_veri) {
            $bildirimi_alacak_kisi = $oyun_sahibi_veri['user_id'];
            $oyun_adi = $oyun_sahibi_veri['game_name'];
            $gonderen_adi = $_SESSION['username'];
            
            if ($bildirimi_alacak_kisi != $user_id) {
                $mesaj = "<b>{$gonderen_adi}</b>, senin <b>{$oyun_adi}</b> yorumuna bir cevap yazdı.";
                mysqli_query($baglanti, "INSERT INTO notifications (user_id, sender_id, game_id, message) VALUES ('$bildirimi_alacak_kisi', '$user_id', '$game_id', '$mesaj')");
            }
        }
        header("Location: index.php?yorum=eklendi");
        exit();
    } else {
        echo "Cevap yüklenirken hata oluştu: " . mysqli_error($baglanti);
    }
}
?>