<?php
// Pipedrive API-konfigurasjon
$api_key = "c4c70b4c5cafa8cbc489a8da6e028153be8c949a";

//Funksjon for å gjøre HTTP POST-request
function send_post_request($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    }
    curl_close($ch);
    return json_decode($response, true);
}

?>