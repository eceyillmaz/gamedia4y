<?php
session_start(); // Oturum yönetimini başlatır
$baglanti = mysqli_connect("localhost", "root", "", "db_oyunlar");

if (!$baglanti) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
?>