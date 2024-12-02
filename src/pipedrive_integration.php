<?php

// Pipedrive API-konfigurasjon
$api_key = "ef78012985ae0e9275a58c5a0e424bc0dd902c2e";
$base_url = "https://nettbureauasdevelopmentteam.pipedrive.com/v1";

// Funksjon for API-kall
function send_request($url, $method = 'POST', $data = []) {
    global $api_key;
    $url .= "?api_token=" . $api_key;
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    // Konverter data til JSON og sett Content-Type-header
    if (!empty($data)) {
        $json_data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data)
        ]);
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        log_error('Curl error: ' . curl_error($ch));
        throw new Exception('Curl error: ' . curl_error($ch));
    }

    curl_close($ch);

    return json_decode($response, true);
}

// Funksjon for logging
function log_error($message) {
    $log_file = __DIR__ . '/../logs/error.log';
    file_put_contents($log_file, date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

// Les testdata
$test_data = json_decode(file_get_contents(__DIR__ . '/../test/test_data.json'), true);
if (!$test_data) {
    log_error("Failed to read test data.");
    die("Failed to read test data.");
}

// Definer en oversikt over felt-ID-er
$field_map = [
    'housing_type' => '9cbbad3c5d83d6d258ef27db4d3784b5e0d5fd32',
    'property_size' => '7a275c324d7fbe5ab62c9f05bfbe87dad3acc3ba',
    'deal_type' => 'cebe4ad7ce36c3508c3722b6e0072c6de5250586',
    'contact_type' => 'fd460d099264059d975249b20e071e05392f329d'
];

//funksjon for å sjekke om organisasjonen allerede finnes
function find_organization_by_name($name) {
    global $base_url, $api_key;

    // Søk etter organisasjoner basert på navn
    $url = "$base_url/organizations/search?term=" . urlencode($name) . "&fields=name&api_token=$api_key";
    $response = send_request($url, 'GET');

    // Returner organisasjonens ID hvis den finnes
    if (isset($response['data']['items']) && count($response['data']['items']) > 0) {
        return $response['data']['items'][0]['item']['id'];
    }

    return null; // Organisasjonen finnes ikke
}

try {
    // Sjekk om organisasjonen allerede finnes
    $org_name = $test_data['organization']['name'];
    $organization_id = find_organization_by_name($org_name);

    if (!$organization_id) {
        // Hvis organisasjonen ikke finnes, opprett en ny
        $org_data = $test_data['organization'];
        $response_org = send_request("$base_url/organizations", 'POST', $org_data);

        if (!$response_org['success'] || !isset($response_org['data']['id'])) {
            throw new Exception("Failed to create organization: " . json_encode($response_org));
        }
        $organization_id = $response_org['data']['id'];
        echo "Organization created successfully. Organization ID: $organization_id\n";
    } else {
        echo "Organization already exists. Organization ID: $organization_id\n";
    }
} catch (Exception $e) {
    log_error($e->getMessage());
    die($e->getMessage());

// Funksjon for å sjekke om personen allerede finnes
function find_person_by_email($email) {
    global $base_url, $api_key;

    // Søk etter personer basert på e-post
    $url = "$base_url/persons/search?term=$email&fields=email&api_token=$api_key";
    $response = send_request($url, 'GET');

    // Returner personens ID hvis den finnes
    if (isset($response['data']['items']) && count($response['data']['items']) > 0) {
        return $response['data']['items'][0]['item']['id'];
    }

    return null; // Personen finnes ikke
}

// Søk etter eksisterende person basert på e-post
$person_email = $test_data['person']['email'];
$person_id = find_person_by_email($person_email);

if (!$person_id) {
    $person_data = $test_data['person'];
    $person_data['org_id'] = $organization_id;

    // Fjern contact_type og bruk feltkartet
    unset($person_data['contact_type']);

    // Legg til contact_type med felt-ID
    if (isset($test_data['person']['contact_type'])) {
        $contact_type = $test_data['person']['contact_type'];
        $valid_contact_types = [30, 31, 32];
        if (in_array($contact_type, $valid_contact_types, true)) {
            $person_data[$field_map['contact_type']] = $contact_type;
        } else {
            throw new Exception("Invalid contact_type value: $contact_type. Allowed values: " . implode(", ", $valid_contact_types));
        }
    }

    error_log("Payload for person creation: " . json_encode($person_data));

    $response_person = send_request("$base_url/persons", 'POST', $person_data);

    error_log("Response for person creation: " . json_encode($response_person));

    if (!$response_person['success'] || !isset($response_person['data']['id'])) {
        throw new Exception("Failed to create person: " . json_encode($response_person));
    }
    $person_id = $response_person['data']['id'];
} else {
    echo "Person already exists. Person ID: $person_id\n";
}

// Opprette lead med egendefinerte felter
$lead_data = [
    "title" => $test_data['lead']['title'],
    "person_id" => $person_id,
    "organization_id" => $organization_id,
    $field_map['housing_type'] => $test_data['lead']['housing_type'], 
    $field_map['property_size'] => $test_data['lead']['property_size'],
    $field_map['deal_type'] => $test_data['lead']['deal_type']
];

$response_lead = send_request("$base_url/leads", 'POST', $lead_data);
if (!$response_lead['success']) {
    throw new Exception("Failed to create lead: " . json_encode($response_lead));
}
error_log("Payload: " . json_encode($person_data));
error_log("Response: " . json_encode($response_person));

echo "Lead created successfully. Lead ID: " . $response_lead['data']['id'] . "\n";
} catch (Exception $e) {
    log_error($e->getMessage());
    die($e->getMessage());
}
