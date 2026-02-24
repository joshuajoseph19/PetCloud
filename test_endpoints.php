<?php
// Test script to identify which API endpoint is returning HTML
// Run this from the command line: php test_endpoints.php

$baseUrl = "http://localhost/PetCloud/api/";
$endpoints = [
    "get_adoption_listings.php",
    "get_adoption_listings.php?pet_type=dog",
    "get_adoption_listings.php?page=1&limit=5",
    "get_pet_types.php",
    "get_breeds.php",
    "get_breeds.php?pet_type_id=1",
    "get_adoption_data.php",
    "get_adoption_data.php?type=dog"
];

echo "Testing API Endpoints for non-JSON output...\n\n";

foreach ($endpoints as $endpoint) {
    $url = $baseUrl . $endpoint;
    echo "Checking: $endpoint ... ";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode != 200) {
        echo "[FAILED - HTTP $httpCode]\n";
        echo "Response excerpt: " . substr($response, 0, 100) . "\n";
        continue;
    }

    // Check if JSON
    json_decode($response);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "[FAILED - NON-JSON DETECTED!]\n";
        echo "Error: " . json_last_error_msg() . "\n";
        echo "First 100 chars: " . substr($response, 0, 100) . "\n";
        // Check for specific HTML tags
        if (strpos($response, '<') !== false) {
            echo "HTML detected in response.\n";
        }
    } else {
        echo "[OK - Valid JSON]\n";
    }
}
?>