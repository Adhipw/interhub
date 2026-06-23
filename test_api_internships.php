<?php
$ch = curl_init('https://internhub.my.id/api/v1/internships');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
echo "HTTP $httpCode\n";
echo $response;
curl_close($ch);
