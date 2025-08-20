<?php

//$url_parts = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
//$possible_token = end($url_parts);

error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Raw POST data: " . file_get_contents('php://input'));


require_once("../classes/User.php");
require_once("../classes/Config.php");
require_once("../classes/Core.php");
require_once("../classes/Database.php");

$db = Database::getInstance();
$mysqli = $db->getConnection();

$core = new Core($mysqli);

// Token kinyerése POST-ból vagy URL-ből
$token = $_POST['token'] ?? null;

if (!$token) {
    // Próbáljuk meg URL-ből kiszedni, ha POST nem tartalmazza
    $url_parts = explode('/', $_SERVER['REQUEST_URI']);
    $possible_token = end($url_parts);

    // Ha hexadecimálisnak tűnik, elfogadjuk
    if (preg_match('/^[a-f0-9]{40}$/i', $possible_token)) {
        $token = $possible_token;
        error_log("Token recovered from URL: $token");
    }
}

if (!$token) {
    http_response_code(400);
    error_log("No token found in POST or URL.");
    exit("No token provided.");
}

// A POST adatokat naplózzuk
error_log("POST data received: " . print_r($_POST, true));

// Az URL paraméterek
error_log("Full request URL: " . $_SERVER['REQUEST_URI']);

// Ellenőrizzük, hogy a token megvan-e
if (!isset($_POST['token'])) {
    error_log("No token found in POST.");
}

// FaucetPay API hívás a token ellenőrzésére
$json = file_get_contents("https://faucetpay.io/merchant/get-payment/" . urlencode($token));
$data = json_decode($json, true);

if (!$data || !isset($data['valid']) || $data['valid'] !== true) {
    http_response_code(400);
    error_log("Invalid token or malformed response. Data: " . json_encode($data));
    exit("Invalid token or malformed response.");
}

// Fontos mezők
$merchant_username = $data['merchant_username'] ?? '';
$amount = $data['amount1'] ?? 0;
$currency = $data['currency1'] ?? '';
$userId = $data['custom'] ?? null;

// Jogosultság ellenőrzése
if ($merchant_username !== 'linux1986') {
    http_response_code(403);
    error_log("Unauthorized merchant: $merchant_username");
    exit("Unauthorized merchant.");
}

// Felhasználó ellenőrzése
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(404);
    error_log("User not found: ID $userId");
    exit("User not found.");
}

// Befizetés hozzáadása
$user = $result->fetch_assoc();
$newDeposit = $user['deposit'] + floatval($amount);

$update = $mysqli->prepare("UPDATE users SET deposit = ? WHERE id = ?");
$update->bind_param("di", $newDeposit, $userId);

if ($update->execute()) {
    error_log("Deposit of $amount credited to user ID $userId");
    echo "Deposit successful";
} else {
    http_response_code(500);
    error_log("Failed to update deposit for user ID $userId");
    echo "Deposit update failed";
}

