<?php
// --- CONFIGURATION ---
$botToken = '';
$chatId = '';
$secretKey = '';

// --- HELPER FUNCTION TO FIX MARKDOWN ERRORS ---
// We will only use this for text NOT inside backticks if needed.
function escapeMarkdownV2($text) {
    $escape_chars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
    foreach ($escape_chars as $char) {
        $text = str_replace($char, '\\' . $char, $text);
    }
    return $text;
}

// --- HEADERS ---
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') { exit(); }

// --- API LOGIC ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { exit(); }

$json_payload = file_get_contents('php://input');
$data = json_decode($json_payload, true);

if ($data === null || !isset($data['secretKey']) || $data['secretKey'] !== $secretKey) {
    http_response_code(401);
    exit();
}

// --- PARSE ALL THE NEW DATA (with defaults for safety) ---
$visitorIP     = $_SERVER['REMOTE_ADDR'];
$wrongDomain   = $data['wrongDomain'] ?? 'N/A';
$correctDomain = $data['correctDomain'] ?? 'N/A';
$fullUrl       = $data['location']['fullUrl'] ?? 'N/A';
$referrer      = $data['location']['referrer'] ?? 'N/A';
$userAgent     = $data['browser']['userAgent'] ?? 'N/A';
$language      = $data['browser']['language'] ?? 'N/A';
$cookies       = ($data['browser']['cookiesEnabled'] ?? false) ? 'Yes' : 'No';
$platform      = $data['device']['platform'] ?? 'N/A';
$screenRes     = $data['device']['screenResolution'] ?? 'N/A';
$windowSize    = $data['device']['windowSize'] ?? 'N/A';
$timezone      = $data['time']['timezone'] ?? 'N/A';
$localTime     = $data['time']['localTime'] ?? 'N/A';

// --- FORMAT THE NEW, DETAILED TELEGRAM MESSAGE (WITH CORRECTIONS) ---

// ** THE FIX IS HERE: The period after "domain" is escaped with a backslash. **
$message  = "🚨 *Domain Mismatch Alert* 🚨\n";
$message .= "A site was accessed from an unauthorized domain\.\n\n";

// Information inside backticks does not need escaping.
$message .= "🌐 *Domain Info*\n";
$message .= "Accessed From: `" . $wrongDomain . "`\n";
$message .= "Should Be: `" . $correctDomain . "`\n";
$message .= "Full URL: `" . $fullUrl . "`\n\n";

$message .= "👤 *Visitor Info*\n";
$message .= "IP Address: `" . $visitorIP . "`\n";
$message .= "Referrer: `" . $referrer . "`\n\n";

$message .= "💻 *Browser & OS*\n";
$message .= "Platform: `" . $platform . "`\n";
$message .= "Language: `" . $language . "`\n";
$message .= "Cookies Enabled: `" . $cookies . "`\n";
$message .= "User Agent: `" . $userAgent . "`\n\n";

$message .= "🖥️ *Screen Info*\n";
$message .= "Resolution: `" . $screenRes . "`\n";
$message .= "Window Size: `" . $windowSize . "`\n\n";

$message .= "⏰ *Time Info*\n";
$message .= "Timezone: `" . $timezone . "`\n";
$message .= "Visitor's Time: `" . $localTime . "` \n";

// Use cURL to send the request
$telegramApiUrl = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
$post_fields = ['chat_id' => $chatId, 'text' => $message, 'parse_mode' => 'MarkdownV2'];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $telegramApiUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close($ch);

// Send success response
http_response_code(200);
echo json_encode(['status' => 'success']);

?>
