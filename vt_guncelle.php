<?php
include 'baglan.php'; // Senin veritabanı bağlantı dosyan

// games tablosunda cover_url sütunu var mı kontrol ediyoruz
$kontrol = mysqli_query($baglanti, "SHOW COLUMNS FROM `games` LIKE 'cover_url'");
$ekli_mi = mysqli_num_rows($kontrol);

if ($ekli_mi == 0) {
    // Eğer sütun yoksa, otomatik olarak ekliyoruz
    $sql = "ALTER TABLE `games` ADD `cover_url` VARCHAR(255) NOT NULL AFTER `game_name`";
    
    if (mysqli_query($baglanti, $sql)) {
        echo "<h2 style='color: green; font-family: sans-serif;'>Başarılı! 'cover_url' sütunu veritabanına otomatik eklendi.</h2>";
        echo "<p>Artık bu dosyayı silebilir ve Adım 2'ye geçebilirsin.</p>";
    } else {
        echo "<h2 style='color: red; font-family: sans-serif;'>Hata oluştu: " . mysqli_error($baglanti) . "</h2>";
    }
} else {
    echo "<h2 style='color: orange; font-family: sans-serif;'>Zaten Ekli! 'cover_url' sütunu veritabanında zaten mevcut.</h2>";
}
?>