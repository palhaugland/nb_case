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

 //  Eksempeldata
 $lead_data = [
    "name" => "Pål Haugland",
    "phone" => "12345678",
    "email" => "paal@haugland.no",
    "housing_type" => "Enebolig",
    "property_size" => "160",
    "deal_type" => "Spotpris",
    "contact_type" => "Privat"
 ];

// Opprette organisasjonen
$org_data = [
    "name" => "Hauglands kodebureau",
    "api_token" => $api_key
];

$response_org = send_post_request("$base_url/organizations", $org_data);
if (!$response_org['success']) {
    die("Failed to create organization: " . $response_org['error']);
}
$organization_id = $response_org['data']['id'];

// Opprette person og knytte til organisasjon
$person_data = [
    "name" => $lead_data['name'],
    "email" => $lead_data['email'],
    "phone" => $lead_data['phone'],
    "org_id"  => $lead_data['organization_id'],
    "api_token" => $api_key
];
$response_person = send_post_request("$base_url/persons", $person_data);
if (!$response_person['success']) {
    die("Dailed to create person: " . $response_person['error']);
}
$person_id = $response_person['data']['id'];



?>