<?php
// IGDB API Ayarları - Kendi aldığın kodları buraya yapıştır
define('IGDB_CLIENT_ID', 'ht9dzlvbz6k9q11r2gnvw50fm6s7ln');
define('IGDB_CLIENT_SECRET', 'n30ti7kopnmlsa7d6gwy5i1i2sunh2');

function igdb_sorgu_at($endpoint, $body) {
    // 1. Aşama: Twitch üzerinden Access Token alıyoruz
    $token_url = "https://id.twitch.tv/oauth2/token?client_id=" . IGDB_CLIENT_ID . "&client_secret=" . IGDB_CLIENT_SECRET . "&grant_type=client_credentials";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $token_response = curl_exec($ch);
    $token_data = json_decode($token_response, true);
    
    if (!isset($token_data['access_token'])) {
        return ["error" => "Twitch Token Alınamadı. Lütfen Client ID ve Secret kodlarını kontrol et."];
    }
    
    $access_token = $token_data['access_token'];
    
    // 2. Aşama: Alınan Token ile IGDB veritabanına asıl sorguyu atıyoruz
    $igdb_url = "https://api.igdb.com/v4/" . $endpoint;
    $headers = [
        "Client-ID: " . IGDB_CLIENT_ID,
        "Authorization: Bearer " . $access_token,
        "Content-Type: text/plain"
    ];
    
    curl_setopt($ch, CURLOPT_URL, $igdb_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
?>