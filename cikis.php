<?php
session_start(); // Oturumu başlat
session_destroy(); // Tüm oturum verilerini sil
header("Location: login.php"); // Kullanıcıyı giriş sayfasına gönder
exit();
?>