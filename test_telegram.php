<?php
// --- A SIMPLE SCRIPT TO TEST ONLY THE TELEGRAM CONNECTION ---

// Your configuration from the other script
$botToken = '';
$chatId = '';

// A simple, hardcoded message. We use 'HTML' parse_mode because it's more forgiving.
$message = "<b>This is a test message.</b>\nIf you see this, your PHP script and Telegram connection are working.";

echo "Attempting to send a test message to Telegram...<br><br>";

// Prepare the API URL and data
$telegramApiUrl = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
$post_fields = [
    'chat_id'    => $chatId,
    'text'       => $message,
    'parse_mode' => 'HTML'
];

// Initialize cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute and get the response
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

// --- DISPLAY THE RESULTS ---

echo "<b>HTTP Status Code:</b> " . $http_code . "<br>";
echo "<b>Telegram API Response:</b><br>";
echo "<pre>";
print_r($response);
echo "</pre>";

if ($curl_error) {
    echo "<b>cURL Error:</b> " . $curl_error . "<br>";
    echo "<br><b>This means your server cannot connect to Telegram. The cURL extension might be missing or blocked by a firewall.</b>";
}

?>
