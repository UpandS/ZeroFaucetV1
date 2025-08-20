<?php
include("classes/Database.php");
include("classes/Config.php");

$db = Database::getInstance();
$mysqli = $db->getConnection();

// Adatbázis kapcsolat (feltételezve, hogy már létezik $mysqli)
$config = new Config($mysqli);
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// Ellenőrizzük, hogy a felhasználó be van-e jelentkezve
session_start();

$messages = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $mysqli->real_escape_string($_POST['username']);
        $password = $_POST['password'];

        // Ellenőrizd a felhasználónév és jelszó párost
        $checkUserSQL = "SELECT * FROM admin_users WHERE username = '$username' LIMIT 1";
        $result = $mysqli->query($checkUserSQL);

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $storedPasswordHash = $row['password_hash'];

            if (password_verify($password, $storedPasswordHash)) {
                // Sikeres belépés
                $_SESSION['admin_username'] = $username;
                // Ha sikeres a bejelentkezés, akkor a lap újratöltése az admin.php-ra
                header("Location: admin.php?page=dashboard");
                exit;
            } else {
                $login_error = "Incorrect username or password.";
            }
        } else {
            $login_error = "Incorrect username or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin panel</title>
    <link rel="icon" href="favicon.png" type="image/png">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <!-- JavaScript könyvtárak betöltése -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: "Lato", sans-serif;
            padding-top: 56px; /* Mivel navbar van, a tartalom legyen feljebb */
        }

        .sidebar {
            height: 100%;
            width: 250px;
            position: fixed;
            z-index: 1;
            top: 0;
            left: 0;
            background-color: #111;
            overflow-x: hidden;
            transition: 0.5s;
            padding-top: 60px;
        }

        .sidebar a {
            padding: 5px 15px;
            text-decoration: none;
            font-size: 14px;
            color: #f1f1f1;
            display: block;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background-color: #ddd;
            color: #111;
        }

        .sidebar .closebtn {
            position: absolute;
            top: 0;
            right: 25px;
            font-size: 36px;
            margin-left: 50px;
        }

        #main {
            margin-left: 250px; /* Sidebar szélessége */
            padding: 20px;
        }

        @media screen and (max-height: 450px) {
            .sidebar {padding-top: 15px;}
            .sidebar a {font-size: 16px;}
        }
    </style>
</head>
<body>

<?php
   
if (!isset($_SESSION['admin_username'])) {   

?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Admin Login</div>
                    <div class="card-body">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="username">Admin username:</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <?php if(isset($login_error)) { ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $login_error; ?>
                                </div>
                            <?php } ?>
                            <button type="submit" class="btn btn-primary">Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php

} else {
	

    // Az oldal kiválasztása az URL alapján
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<div class="sidebar">
    <a href="admin.php?page=dashboard">Dashboard</a>
    <a href="admin.php?page=passch">Admin Password</a>
    <a href="admin.php?page=manual_withdraw">Manual Withdraw</a>
    <a href="admin.php?page=wallet_addresses">Deposit wallet addresses</a>
    <a href="admin.php?page=faucetpay_settings">FaucetPay Settings</a>
    <div style="border-top: 1px solid #888; margin-top: 10px; margin-bottom: 10px;"></div>
    <span style="color: #888; padding: 0 15px;">Earn</span>
    <a href="admin.php?page=features">Features</a>
    <a href="admin.php?page=faucet">Faucet</a>
    <a href="admin.php?page=coingecko">Coingecko</a>
    <a href="admin.php?page=autofaucet">Autofaucet</a>
    <a href="admin.php?page=energyshop">EnergyShop</a>
    <a href="admin.php?page=level_system">Level settings</a>
    <a href="admin.php?page=offerwalls">Offerwalls</a>
    <a href="admin.php?page=ptczerads">PTC (zerads)</a>
    <a href="admin.php?page=shortlink">Shortlink</a>
    <a href="admin.php?page=achievements">Achievements</a>
    <a href="admin.php?page=dailybonus">Daily bonus</a>
    <a href="admin.php?page=ptc_settings">PTC Settings</a>
    <a href="admin.php?page=ptc_packages">PTC Packages</a>
    <a href="admin.php?page=smtp_settings">SMTP Settings</a>
    <!-- Új menüpont hozzáadása -->
    <div style="border-top: 1px solid #888; margin-top: 10px; margin-bottom: 10px;"></div>
    <span style="color: #888; padding: 0 15px;">Users</span>
    <!-- Új menüpont hozzáadása -->
    <a href="admin.php?page=banned_usernames">Banned Usernames</a>
    <a href="admin.php?page=duplicate_check">Duplicate Check</a>
    <a href="admin.php?page=whitelist">White IP List</a>
    <a href="admin.php?page=user_list">User List</a>
    <div style="border-top: 1px solid #888; margin-top: 10px; margin-bottom: 10px;"></div>
    <a href="admin.php?page=logout">Logout</a>
</div>

<div id="main">

    <?php
    // Az oldal tartalmának megjelenítése a kiválasztott oldalnak megfelelően
    switch ($page) {
        case 'dashboard':
            // Users oldal tartalma
            echo "<h1>Base Settings</h1>";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  $faucet_name = $mysqli->real_escape_string($_POST['faucet_name']);
  $referral = $mysqli->real_escape_string($_POST['referral_percent']);
  $hcaptcha_pub_key = $mysqli->real_escape_string($_POST['hcaptcha_pub_key']);
  $hcaptcha_sec_key = $mysqli->real_escape_string($_POST['hcaptcha_sec_key']);
  $zerochain_api = $mysqli->real_escape_string($_POST['zerochain_api']);
  $zerochain_privatekey = $mysqli->real_escape_string($_POST['zerochain_privatekey']);
  $maintenance = $mysqli->real_escape_string($_POST['maintenance']);
  $manual_withdraw = $mysqli->real_escape_string($_POST['manual_withdraw']);
  $website_url = $mysqli->real_escape_string($_POST['website_url']);
  // Frissítsd a settings táblát a beállításokkal
  $updatefaucet_name = "UPDATE settings SET value = '$faucet_name' WHERE name = 'faucet_name'";
  $updatereferral_percent = "UPDATE settings SET value = '$referral' WHERE name = 'referral_percent'";
  $updatehcaptcha_pub_key = "UPDATE settings SET value = '$hcaptcha_pub_key' WHERE name = 'hcaptcha_pub_key'";
  $updatehcaptcha_sec_key = "UPDATE settings SET value = '$hcaptcha_sec_key' WHERE name = 'hcaptcha_sec_key'";
  $updatezerochain_api = "UPDATE settings SET value = '$zerochain_api' WHERE name = 'zerochain_api'";
  $updatezerochain_privatekey = "UPDATE settings SET value = '$zerochain_privatekey' WHERE name = 'zerochain_privatekey'";
  $updatemaintenance = "UPDATE settings SET value = '$maintenance' WHERE name = 'maintenance'";
  $updatemanual_withdraw = "UPDATE settings SET value = '$manual_withdraw' WHERE name = 'manual_withdraw'";
  $updatewebsite_url = "UPDATE settings SET value = '$website_url' WHERE name = 'website_url'";
  
  if (
      $mysqli->query($updatefaucet_name) === TRUE &&
      $mysqli->query($updatereferral_percent) === TRUE &&
      $mysqli->query($updatehcaptcha_pub_key) === TRUE &&
      $mysqli->query($updatehcaptcha_sec_key) === TRUE &&
      $mysqli->query($updatezerochain_api) === TRUE &&
      $mysqli->query($updatezerochain_privatekey) === TRUE &&
      $mysqli->query($updatemaintenance) === TRUE &&
      $mysqli->query($updatemanual_withdraw) === TRUE &&
      $mysqli->query($updatewebsite_url) === TRUE
  ) {
      // Sikeres frissítés után visszairányítás a dashboard.php oldalra
      echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
  } else {
    //  echo "Error updating settings: " . $mysqli->error;
  }

}
$getfaucet_name = $mysqli->query("SELECT value FROM settings WHERE name = 'faucet_name' LIMIT 1")->fetch_assoc()['value'];
$getreferral_percent = $mysqli->query("SELECT value FROM settings WHERE name = 'referral_percent' LIMIT 1")->fetch_assoc()['value'];
$gethcaptcha_pub_key = $mysqli->query("SELECT value FROM settings WHERE name = 'hcaptcha_pub_key' LIMIT 1")->fetch_assoc()['value'];
$gethcaptcha_sec_key = $mysqli->query("SELECT value FROM settings WHERE name = 'hcaptcha_sec_key' LIMIT 1")->fetch_assoc()['value'];
$getzerochain_api = $mysqli->query("SELECT value FROM settings WHERE name = 'zerochain_api' LIMIT 1")->fetch_assoc()['value'];
$getzerochain_privatekey = $mysqli->query("SELECT value FROM settings WHERE name = 'zerochain_privatekey' LIMIT 1")->fetch_assoc()['value'];
$getmaintenance = $mysqli->query("SELECT value FROM settings WHERE name = 'maintenance' LIMIT 1")->fetch_assoc()['value'];
$getmanual_withdraw = $mysqli->query("SELECT value FROM settings WHERE name = 'manual_withdraw' LIMIT 1")->fetch_assoc()['value'];
$getwebsite_url = $mysqli->query("SELECT value FROM settings WHERE name = 'website_url' LIMIT 1")->fetch_assoc()['value'];
?>


<div class="container mt-5">
    <form method="post" action="?page=dashboard">
        <div class="form-group">
            <label for="faucet_name">Site name:</label>
            <input type="text" class="form-control" id="faucet_name" name="faucet_name" value="<?php echo $getfaucet_name; ?>">
        </div>
        <div class="form-group">
            <label for="website_url">Website URL: (example: https://yourdomain.com/)</label>
            <input type="text" class="form-control" id="website_url" name="website_url" value="<?php echo $getwebsite_url; ?>">
        </div>
        <div class="form-group">
            <label for="referral_percent">Referral reward (%) :</label>
            <input type="text" class="form-control" id="referral_percent" name="referral_percent" value="<?php echo $getreferral_percent; ?>">
        </div>
        <div class="form-group">
            <label for="hcaptcha_pub_key">Hcaptcha public key :</label>
            <input type="text" class="form-control" id="hcaptcha_pub_key" name="hcaptcha_pub_key" value="<?php echo $gethcaptcha_pub_key; ?>">
        </div>
        <div class="form-group">
            <label for="hcaptcha_sec_key">Hcaptcha private key :</label>
            <input type="text" class="form-control" id="hcaptcha_sec_key" name="hcaptcha_sec_key" value="<?php echo $gethcaptcha_sec_key; ?>">
        </div>
        <div class="form-group">
            <label for="zerochain_api">Zerochain API :</label>
            <input type="text" class="form-control" id="zerochain_api" name="zerochain_api" value="<?php echo $getzerochain_api; ?>">
        </div>
        <div class="form-group">
            <label for="zerochain_privatekey">Zerochain private key :</label>
            <input type="text" class="form-control" id="zerochain_privatekey" name="zerochain_privatekey" value="<?php echo $getzerochain_privatekey; ?>">
        </div>
               <div class="form-group">
                <label for="manual_withdraw">Manual widthdraw :</label>
                <select class="form-control" id="manual_withdraw" name="manual_withdraw">
                    <option value="on" <?php if($getmanual_withdraw == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getmanual_withdraw == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
        <div class="form-group">
                <label for="maintenance">Maintenance :</label>
                <select class="form-control" id="maintenance" name="maintenance">
                    <option value="on" <?php if($getmaintenance == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getmaintenance == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
<?php
            break;

            case 'banned_usernames':
                echo "<h1>Banned Usernames Management</h1>";
            
                // Új felhasználónév tiltása
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ban_username'])) {
                    $username = $mysqli->real_escape_string($_POST['ban_username']);
            
                    // Ellenőrizzük, hogy a felhasználónév már tiltva van-e
                    $checkSQL = "SELECT * FROM banned_username WHERE username = '$username' LIMIT 1";
                    $result = $mysqli->query($checkSQL);
            
                    if ($result->num_rows == 0) {
                        // Ha nincs tiltva, adjuk hozzá a táblához
                        $insertSQL = "INSERT INTO banned_username (username) VALUES ('$username')";
                        if ($mysqli->query($insertSQL) === TRUE) {
                            echo "<div class='alert alert-success'>Username '$username' successfully banned.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Error banning username: " . $mysqli->error . "</div>";
                        }
                    } else {
                        echo "<div class='alert alert-warning'>Username '$username' is already banned.</div>";
                    }
                }
            
                // Felhasználónév eltávolítása a tiltott listáról
                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['unban_username'])) {
                    $username = $mysqli->real_escape_string($_POST['unban_username']);
            
                    $deleteSQL = "DELETE FROM banned_username WHERE username = '$username' LIMIT 1";
                    if ($mysqli->query($deleteSQL) === TRUE) {
                        echo "<div class='alert alert-success'>Username '$username' successfully unbanned.</div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error unbanning username: " . $mysqli->error . "</div>";
                    }
                }
            
                // Tiltott felhasználónevek listázása
                $bannedSQL = "SELECT * FROM banned_username ORDER BY banned_at DESC";
                $bannedResult = $mysqli->query($bannedSQL);
            
                echo '<div class="container mt-4">';
                echo '<h3>Ban a Username</h3>';
                echo '<form method="post" action="?page=banned_usernames">';
                echo '<div class="form-group">';
                echo '<label for="ban_username">Username:</label>';
                echo '<input type="text" class="form-control" id="ban_username" name="ban_username" required>';
                echo '</div>';
                echo '<button type="submit" class="btn btn-danger">Ban</button>';
                echo '</form>';
                echo '<hr>';
            
                echo '<h3>Banned Usernames</h3>';
                if ($bannedResult->num_rows > 0) {
                    echo '<table class="table table-bordered">';
                    echo '<thead><tr><th>ID</th><th>Username</th><th>Banned At</th><th>Action</th></tr></thead><tbody>';
                    while ($row = $bannedResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                        echo '<td>' . $row['banned_at'] . '</td>';
                        echo '<td>';
                        echo '<form method="post" action="?page=banned_usernames" style="display:inline;">';
                        echo '<input type="hidden" name="unban_username" value="' . htmlspecialchars($row['username']) . '">';
                        echo '<button type="submit" class="btn btn-success btn-sm">Unban</button>';
                        echo '</form>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody></table>';
                } else {
                    echo '<div class="alert alert-info">No banned usernames found.</div>';
                }
                echo '</div>';
                break;

            case 'smtp_settings':
                echo "<h1>SMTP Settings</h1>";
            
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    $smtp_server = $mysqli->real_escape_string($_POST['smtp_server']);
                    $smtp_port = $mysqli->real_escape_string($_POST['smtp_port']);
                    $smtp_user = $mysqli->real_escape_string($_POST['smtp_user']);
                    $smtp_pass = $mysqli->real_escape_string($_POST['smtp_pass']);
                    $smtp_ssl = $mysqli->real_escape_string($_POST['smtp_ssl']);
            
                    $mysqli->query("UPDATE settings SET value = '$smtp_server' WHERE name = 'smtp_server'");
                    $mysqli->query("UPDATE settings SET value = '$smtp_port' WHERE name = 'smtp_port'");
                    $mysqli->query("UPDATE settings SET value = '$smtp_user' WHERE name = 'smtp_user'");
                    $mysqli->query("UPDATE settings SET value = '$smtp_pass' WHERE name = 'smtp_pass'");
                    $mysqli->query("UPDATE settings SET value = '$smtp_ssl' WHERE name = 'smtp_ssl'");
            
                    echo '<div class="alert alert-success">SMTP settings updated successfully.</div>';
                }
            
                $smtp_server = $mysqli->query("SELECT value FROM settings WHERE name = 'smtp_server'")->fetch_assoc()['value'];
                $smtp_port = $mysqli->query("SELECT value FROM settings WHERE name = 'smtp_port'")->fetch_assoc()['value'];
                $smtp_user = $mysqli->query("SELECT value FROM settings WHERE name = 'smtp_user'")->fetch_assoc()['value'];
                $smtp_pass = $mysqli->query("SELECT value FROM settings WHERE name = 'smtp_pass'")->fetch_assoc()['value'];
                $smtp_ssl = $mysqli->query("SELECT value FROM settings WHERE name = 'smtp_ssl'")->fetch_assoc()['value'];
                ?>
            
                <form method="post" action="?page=smtp_settings">
                    <div class="form-group">
                        <label for="smtp_server">SMTP Server:</label>
                        <input type="text" class="form-control" id="smtp_server" name="smtp_server" value="<?php echo $smtp_server; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="smtp_port">SMTP Port:</label>
                        <input type="number" class="form-control" id="smtp_port" name="smtp_port" value="<?php echo $smtp_port; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="smtp_user">SMTP User:</label>
                        <input type="text" class="form-control" id="smtp_user" name="smtp_user" value="<?php echo $smtp_user; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="smtp_pass">SMTP Password:</label>
                        <input type="password" class="form-control" id="smtp_pass" name="smtp_pass" value="<?php echo $smtp_pass; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="smtp_ssl">SMTP SSL:</label>
                        <select class="form-control" id="smtp_ssl" name="smtp_ssl">
                            <option value="on" <?php echo $smtp_ssl == 'on' ? 'selected' : ''; ?>>On</option>
                            <option value="off" <?php echo $smtp_ssl == 'off' ? 'selected' : ''; ?>>Off</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
                <?php
                break;
                
                    case 'wallet_addresses':
                        echo "<h1>Wallet Addresses Management</h1>";
                
                        if ($_SERVER["REQUEST_METHOD"] == "POST") {
                            if (isset($_POST['action'])) {
                                $action = $_POST['action'];
                
                                if ($action === "add") {
                                    $address = $mysqli->real_escape_string($_POST['address']);
                                    $status = $mysqli->real_escape_string($_POST['status']);
                
                                    $insert_query = "INSERT INTO wallet_addresses (address, status) VALUES ('$address', '$status')";
                                    if ($mysqli->query($insert_query)) {
                                        echo "<div class='alert alert-success'>Address added successfully!</div>";
                                    } else {
                                        echo "<div class='alert alert-danger'>Error adding address: " . $mysqli->error . "</div>";
                                    }
                                } elseif ($action === "edit") {
                                    $id = (int)$_POST['id'];
                                    $address = $mysqli->real_escape_string($_POST['address']);
                                    $status = $mysqli->real_escape_string($_POST['status']);
                
                                    $update_query = "UPDATE wallet_addresses SET address = '$address', status = '$status' WHERE id = $id";
                                    if ($mysqli->query($update_query)) {
                                        echo "<div class='alert alert-success'>Address updated successfully!</div>";
                                    } else {
                                        echo "<div class='alert alert-danger'>Error updating address: " . $mysqli->error . "</div>";
                                    }
                                } elseif ($action === "delete") {
                                    $id = (int)$_POST['id'];
                
                                    $delete_query = "DELETE FROM wallet_addresses WHERE id = $id";
                                    if ($mysqli->query($delete_query)) {
                                        echo "<div class='alert alert-danger'>Address deleted successfully!</div>";
                                    } else {
                                        echo "<div class='alert alert-danger'>Error deleting address: " . $mysqli->error . "</div>";
                                    }
                                }
                            }
                        }
                
                        $addresses = $mysqli->query("SELECT * FROM wallet_addresses");
                        ?>
                
                        <div class="container mt-4">
                            <h3>Add New Address</h3>
                            <form method="post" action="?page=wallet_addresses">
                                <input type="hidden" name="action" value="add">
                                <div class="form-group">
                                    <label for="address">Wallet Address:</label>
                                    <input type="text" class="form-control" id="address" name="address" required>
                                </div>
                                <div class="form-group">
                                    <label for="status">Status:</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success mt-2">Add Address</button>
                            </form>
                
                            <hr>
                
                            <h3>Existing Addresses</h3>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Updated At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $addresses->fetch_assoc()): ?>
                                        <tr>
                                            <form method="post" action="?page=wallet_addresses">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="action" value="edit">
                                                <td><?= $row['id'] ?></td>
                                                <td><input type="text" class="form-control" name="address" value="<?= $row['address'] ?>"></td>
                                                <td>
                                                    <select class="form-control" name="status">
                                                        <option value="active" <?= $row['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                                        <option value="inactive" <?= $row['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                                    </select>
                                                </td>
                                                <td><?= $row['created_at'] ?></td>
                                                <td><?= $row['updated_at'] ?></td>
                                                <td>
                                                    <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                            </form>
                                            <form method="post" action="?page=wallet_addresses" style="display:inline;">
                                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                                </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php
                        break;

case 'manual_withdraw':
    // Lekérdezzük az ID-t és az akciót (approve vagy reject)
    if (isset($_GET['id']) && isset($_GET['action'])) {
        $withdrawId = $_GET['id'];
        $action = $_GET['action'];

        // Lekérdezzük a kifizetés adatait
        $stmt = $mysqli->prepare("SELECT user_id, amount, txid, status, currency, zer_value FROM withdrawals WHERE id = ?");
        $stmt->bind_param("i", $withdrawId);
        $stmt->execute();
        $stmt->bind_result($userid, $amount, $txid, $currentStatus, $currency, $zerValue);
        $stmt->fetch();
        $stmt->close();

        if ($currentStatus == 'Pending') {
            if ($action == "approve") {
                $status = "Paid";

                if ($currency === "ZER") {
                    // ZeroChain API hívás
                    $stmt = $mysqli->prepare("SELECT address FROM users WHERE id = ?");
                    $stmt->bind_param("i", $userid);
                    $stmt->execute();
                    $stmt->bind_result($address);
                    $stmt->fetch();
                    $stmt->close();

                    $stmt = $mysqli->prepare("SELECT value FROM settings WHERE name = 'zerochain_api' LIMIT 1");
                    $stmt->execute();
                    $stmt->bind_result($ZC_API_Key);
                    $stmt->fetch();
                    $stmt->close();

                    $stmt = $mysqli->prepare("SELECT value FROM settings WHERE name = 'zerochain_privatekey' LIMIT 1");
                    $stmt->execute();
                    $stmt->bind_result($privateKey);
                    $stmt->fetch();
                    $stmt->close();

                    $result = file_get_contents("https://zerochain.info/api/rawtxbuild/{$privateKey}/{$address}/{$amount}/0/1/{$ZC_API_Key}");
                    $TxID = "";
                    if (strpos($result, '"txid":"') !== false) {
                        $pieces = explode('"txid":"', $result);
                        $TxID = explode('"', $pieces[1])[0];
                    }

                    if ($TxID != "") {
                        $stmt = $mysqli->prepare("UPDATE withdrawals SET status = ?, txid = ? WHERE id = ?");
                        $stmt->bind_param("ssi", $status, $TxID, $withdrawId);
                        $stmt->execute();
                        $stmt->close();

                        $stmt = $mysqli->prepare("UPDATE users SET total_withdrawals = total_withdrawals + ? WHERE id = ?");
                        $stmt->bind_param("di", $amount, $userid);
                        $stmt->execute();
                        $stmt->close();

                        echo "Withdrawal approved and payment sent. TXID: " . $TxID;
                    } else {
                        echo "Error occurred while processing the transaction.";
                    }
                } else {
                    // FaucetPay API hívás
                    $stmt = $mysqli->prepare("SELECT fp_address FROM users WHERE id = ?");
                    $stmt->bind_param("i", $userid);
                    $stmt->execute();
                    $stmt->bind_result($fpAddress);
                    $stmt->fetch();
                    $stmt->close();

                    $stmt = $mysqli->prepare("SELECT value FROM settings WHERE name = 'faucetpay_api_key' LIMIT 1");
                    $stmt->execute();
                    $stmt->bind_result($FP_API_Key);
                    $stmt->fetch();
                    $stmt->close();

                    $amountInSatoshis = $amount * 100000000;

                    $postData = [
                        'api_key' => $FP_API_Key,
                        'amount' => $amountInSatoshis,
                        'to' => $fpAddress,
                        'currency' => $currency,
                    ];

                    $ch = curl_init("https://faucetpay.io/api/v1/send");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                    $response = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);

                    if ($httpCode === 200) {
                        $responseData = json_decode($response, true);
                        if ($responseData['status'] === 200) {
                            $stmt = $mysqli->prepare("UPDATE withdrawals SET status = ?, txid = ? WHERE id = ?");
                            $stmt->bind_param("ssi", $status, $responseData['data']['txid'], $withdrawId);
                            $stmt->execute();
                            $stmt->close();

                            // Frissítsük a total_withdrawals mezőt a zer_value alapján
                            $stmt = $mysqli->prepare("SELECT zer_value FROM withdrawals WHERE id = ?");
                            $stmt->bind_param("i", $withdrawId);
                            $stmt->execute();
                            $stmt->bind_result($zerValue);
                            $stmt->fetch();
                            $stmt->close();

                            $stmt = $mysqli->prepare("UPDATE users SET total_withdrawals = total_withdrawals + ? WHERE id = ?");
                            $stmt->bind_param("di", $zerValue, $userid);
                            $stmt->execute();
                            $stmt->close();

                            echo "FaucetPay withdrawal approved and payment sent. TXID: " . $responseData['data']['txid'];
                        } else {
                            echo "FaucetPay API Error: " . $responseData['message'];
                        }
                    } else {
                        echo "FaucetPay API request failed.";
                    }
                }
            } elseif ($action == "reject") {
                $status = "Rejected";

                // Módosított rész: `zer_value` frissítése a felhasználó egyenlegében
                $stmt = $mysqli->prepare("UPDATE withdrawals SET status = ? WHERE id = ?");
                $stmt->bind_param("si", $status, $withdrawId);
                $stmt->execute();
                $stmt->close();

                // Lekérdezzük a `zer_value` mezőt
                $stmt = $mysqli->prepare("SELECT zer_value FROM withdrawals WHERE id = ?");
                $stmt->bind_param("i", $withdrawId);
                $stmt->execute();
                $stmt->bind_result($zerValue);
                $stmt->fetch();
                $stmt->close();

                // A felhasználó egyenlegét a `zer_value` értékkel frissítjük
                $stmt = $mysqli->prepare("SELECT balance FROM users WHERE id = ?");
                $stmt->bind_param("i", $userid);
                $stmt->execute();
                $stmt->bind_result($balance);
                $stmt->fetch();
                $stmt->close();

                $newBalance = $balance + $zerValue;
                $stmt = $mysqli->prepare("UPDATE users SET balance = ? WHERE id = ?");
                $stmt->bind_param("di", $newBalance, $userid);
                $stmt->execute();
                $stmt->close();

                echo "Withdrawal rejected and the amount has been refunded to the user's balance.";
            } else {
                die("Invalid action");
            }
        } else {
            echo "This withdrawal has already been processed.";
        }
    }

    // Lekérdezzük a függőben lévő kifizetéseket
    $stmt = $mysqli->prepare("
        SELECT w.id, w.amount, w.currency, w.requested_at, u.username 
        FROM withdrawals w 
        JOIN users u ON w.user_id = u.id 
        WHERE w.status = 'Pending' 
        ORDER BY w.requested_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo '<h2>Pending Withdrawals</h2>';
        echo '<div class="table-responsive">';
        echo '<table class="table table-bordered">';
        echo '<thead>';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Username</th>';
        echo '<th>Amount</th>';
        echo '<th>Currency</th>';
        echo '<th>Date</th>';
        echo '<th>Action</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        while ($row = $result->fetch_assoc()) {
            $id = $row['id'];
            $username = htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8');
            $amount = $row['amount'];
            $currency = $row['currency'];
            $timestamp = date("d-m-Y H:i:s", strtotime($row['requested_at']));
            echo '<tr>';
            echo "<td>{$id}</td>";
            echo "<td>{$username}</td>";
            echo "<td>{$amount} {$currency}</td>";
            echo "<td>{$currency}</td>";
            echo "<td>{$timestamp}</td>";
            echo "<td>
                    <a href=\"admin.php?page=manual_withdraw&id={$id}&action=approve\" class=\"btn btn-success\">Approve</a>
                    <a href=\"admin.php?page=manual_withdraw&id={$id}&action=reject\" class=\"btn btn-danger\">Reject</a>
                  </td>";
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
        echo '</div>';

        $stmt->close();
    } else {
        echo "No pending withdrawals found.";
    }

    break;

case 'energyshop':
    echo "<h1>Energy Shop Settings</h1>";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
        if ($_POST['action'] == "add") {
            $name = $mysqli->real_escape_string($_POST['name']);
            $energy_cost = $mysqli->real_escape_string($_POST['energy_cost']);
            $zero_amount = $mysqli->real_escape_string($_POST['zero_amount']);

            $insert_query = "INSERT INTO energyshop_packages (name, energy_cost, zero_amount) VALUES ('$name', '$energy_cost', '$zero_amount')";
            $mysqli->query($insert_query);
            echo "<div class='alert alert-success'>Package added successfully!</div>";
        }

        if ($_POST['action'] == "edit") {
            $id = $mysqli->real_escape_string($_POST['id']);
            $name = $mysqli->real_escape_string($_POST['name']);
            $energy_cost = $mysqli->real_escape_string($_POST['energy_cost']);
            $zero_amount = $mysqli->real_escape_string($_POST['zero_amount']);

            $update_query = "UPDATE energyshop_packages SET name='$name', energy_cost='$energy_cost', zero_amount='$zero_amount' WHERE id='$id'";
            $mysqli->query($update_query);
            echo "<div class='alert alert-success'>Package updated successfully!</div>";
        }

        if ($_POST['action'] == "delete") {
            $id = $mysqli->real_escape_string($_POST['id']);
            $delete_query = "DELETE FROM energyshop_packages WHERE id='$id'";
            $mysqli->query($delete_query);
            echo "<div class='alert alert-danger'>Package deleted successfully!</div>";
        }
    }

    // Csomagok lekérése
    $packages = $mysqli->query("SELECT * FROM energyshop_packages");
?>

<div class="container mt-4">
    <h3>Add New Package</h3>
    <form method="post" action="?page=energyshop">
        <input type="hidden" name="action" value="add">
        <div class="form-group">
            <label for="name">Package Name:</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="energy_cost">Energy Cost:</label>
            <input type="number" class="form-control" id="energy_cost" name="energy_cost" required>
        </div>
        <div class="form-group">
            <label for="zero_amount">Zero Reward:</label>
            <input type="text" class="form-control" id="zero_amount" name="zero_amount" required>
        </div>
        <button type="submit" class="btn btn-success mt-2">Add Package</button>
    </form>

    <hr>

    <h3>Existing Packages</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Energy Cost</th>
                <th>Zero Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $packages->fetch_assoc()): ?>
                <tr>
                    <form method="post" action="?page=energyshop">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="edit">
                        <td><input type="text" class="form-control" name="name" value="<?= $row['name'] ?>"></td>
                        <td><input type="number" class="form-control" name="energy_cost" value="<?= $row['energy_cost'] ?>"></td>
                        <td><input type="text" class="form-control" name="zero_amount" value="<?= $row['zero_amount'] ?>"></td>
                        <td>
                            <button type="submit" class="btn btn-primary btn-sm">Save</button>
                    </form>
                    <form method="post" action="?page=energyshop" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php
    break;

case 'faucetpay_settings':
    echo "<h1>Settings</h1>";


     // Ha formot küldenek el a currency beállítások frissítésére
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Currency beállítások frissítése
        if (isset($_POST['currency_id'])) {
            $currency_id = $mysqli->real_escape_string($_POST['currency_id']);
            $minimum_withdrawal = $mysqli->real_escape_string($_POST['minimum_withdrawal']);
            $status = $mysqli->real_escape_string($_POST['status']);

            // Currency beállítások frissítése
            $update_currency = "UPDATE currencies SET minimum_withdrawal = '$minimum_withdrawal', status = '$status' WHERE id = '$currency_id'";

            $currency_update_success = $mysqli->query($update_currency) === TRUE;
        }

        // Új currency hozzáadása
        if (isset($_POST['currency_select'])) {
            // A kiválasztott currency code és name kinyerése a form értékéből
            list($currency_code, $currency_name) = explode("|", $_POST['currency_select']);
            
            $minimum_withdrawal = $mysqli->real_escape_string($_POST['minimum_withdrawal']);
            $status = $mysqli->real_escape_string($_POST['status']);

            // Ellenőrzés, hogy a currency már létezik-e
            $check_existing_currency = $mysqli->query("SELECT id FROM currencies WHERE code = '$currency_code' LIMIT 1");
            if ($check_existing_currency->num_rows == 0) {
                // Új currency hozzáadása
                $insert_currency = "INSERT INTO currencies (currency_name, code, price, wallet, minimum_withdrawal, status)
											VALUES ('$currency_name', '$currency_code', 0, 'faucetpay', '$minimum_withdrawal', '$status')";


                $currency_insert_success = $mysqli->query($insert_currency) === TRUE;
                if ($currency_insert_success) {
                    echo "<div class='alert alert-success'>Currency added successfully!</div>";
                } else {
                    echo "<div class='alert alert-danger'>Error adding currency!</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>Currency already exists!</div>";
            }
        }

        // FaucetPay settings mentése
        if (isset($_POST['faucetpay_mode']) && isset($_POST['faucetpay_api_key'])) {
            $faucetpay_mode = $mysqli->real_escape_string($_POST['faucetpay_mode']);
            $faucetpay_api_key = $mysqli->real_escape_string($_POST['faucetpay_api_key']);

            // FaucetPay mode és API key frissítése
            $update_faucetpay_mode = "UPDATE settings SET value = '$faucetpay_mode' WHERE name = 'faucetpay_mode'";
            $update_faucetpay_api_key = "UPDATE settings SET value = '$faucetpay_api_key' WHERE name = 'faucetpay_api_key'";

            $faucetpay_mode_success = $mysqli->query($update_faucetpay_mode) === TRUE;
            $faucetpay_api_key_success = $mysqli->query($update_faucetpay_api_key) === TRUE;

            if ($faucetpay_mode_success && $faucetpay_api_key_success) {
                echo "<div class='alert alert-success'>FaucetPay settings saved successfully!</div>";
            } else {
                echo "<div class='alert alert-danger'>Error saving FaucetPay settings!</div>";
            }
        }
    }

    // Currency lista lekérése
    $currencies = $mysqli->query("SELECT * FROM currencies");

    // FaucetPay settings lekérése
    $faucetpay_mode = $mysqli->query("SELECT value FROM settings WHERE name = 'faucetpay_mode' LIMIT 1")->fetch_assoc()['value'];
    $faucetpay_api_key = $mysqli->query("SELECT value FROM settings WHERE name = 'faucetpay_api_key' LIMIT 1")->fetch_assoc()['value'];

    echo "<div class='container mt-4'>";
    ?>

    <h2>Currencies Settings</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Currency Name / Code</th>
                <th>Minimum Withdrawal</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        while ($currency = $currencies->fetch_assoc()) {
            ?>
            <tr>
                <form method="post" action="?page=faucetpay_settings">
                    <input type="hidden" name="currency_id" value="<?= $currency['id']; ?>">
                    <td><?= $currency['code']; ?></td>
                    <td>
                        <input type="text" class="form-control" name="minimum_withdrawal" value="<?= $currency['minimum_withdrawal']; ?>">
                    </td>
                    <td>
                        <select class="form-control" name="status">
                            <option value="on" <?= $currency['status'] == "on" ? "selected" : "" ?>>On</option>
                            <option value="off" <?= $currency['status'] == "off" ? "selected" : "" ?>>Off</option>
                        </select>
                    </td>
                    <td>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </td>
                </form>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>

<h2>Add New Currency</h2>
<form method="post" action="?page=faucetpay_settings">
    <div class="form-group">
        <label for="currency_select">Select Currency:</label>
        <select class="form-control" id="currency_select" name="currency_select">
            <option value="">-- Select Currency --</option>
            <?php
            // Definiált statikus valuták (currency_name, code, display_name)
            $static_currencies = [
                ['currency_name' => 'bitcoin', 'code' => 'BTC', 'display_name' => 'Bitcoin (BTC)'],
                ['currency_name' => 'ethereum', 'code' => 'ETH', 'display_name' => 'Ethereum (ETH)'],
                ['currency_name' => 'litecoin', 'code' => 'LTC', 'display_name' => 'Litecoin (LTC)'],
                // Add more static currencies as needed
            ];

            // Lekérjük az adatbázisban lévő valuták kódját
            $existing_currencies_query = $mysqli->query("SELECT code FROM currencies");
            $existing_currencies = [];
            while ($row = $existing_currencies_query->fetch_assoc()) {
                $existing_currencies[] = $row['code'];  // Tároljuk el azokat a valutákat, amik már léteznek
            }

            // Statikus valuták megjelenítése, ha még nincs az adatbázisban
            foreach ($static_currencies as $currency) {
                if (!in_array($currency['code'], $existing_currencies)) {
                    echo "<option value=\"{$currency['code']}|{$currency['currency_name']}\">{$currency['display_name']}</option>";
                }
            }

            // Dinamikusan hozzáadott valuták az adatbázisból
            $dynamic_currencies_query = $mysqli->query("SELECT code, currency_name FROM currencies");
            while ($currency = $dynamic_currencies_query->fetch_assoc()) {
                $currency_code = $currency['code'];
                $currency_name = $currency['currency_name'];
                // Ne jelenítse meg az adatbázisban lévő valutát
                if (!in_array($currency_code, $existing_currencies)) {
                    echo "<option value=\"$currency_code|$currency_name\">$currency_name ($currency_code)</option>";
                }
            }
            ?>
        </select>
    </div>

    <div class="form-group">
        <label for="minimum_withdrawal">Minimum Withdrawal:</label>
        <input type="text" class="form-control" id="minimum_withdrawal" name="minimum_withdrawal" value="0.01" required>
    </div>
    <div class="form-group">
        <label for="status">Status:</label>
        <select class="form-control" id="status" name="status">
            <option value="on">On</option>
            <option value="off">Off</option>
        </select>
    </div>
    <button type="submit" class="btn btn-success">Add Currency</button>
</form>


    <h2>FaucetPay Settings</h2>
    <form method="post" action="?page=faucetpay_settings">
        <div class="form-group">
            <label for="faucetpay_mode">FaucetPay Mode:</label>
            <select class="form-control" id="faucetpay_mode" name="faucetpay_mode">
                <option value="on" <?= $faucetpay_mode == "on" ? "selected" : "" ?>>On</option>
                <option value="off" <?= $faucetpay_mode == "off" ? "selected" : "" ?>>Off</option>
            </select>
        </div>
        <div class="form-group">
            <label for="faucetpay_api_key">FaucetPay API Key:</label>
            <input type="text" class="form-control" id="faucetpay_api_key" name="faucetpay_api_key" value="<?= $faucetpay_api_key; ?>">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Save FaucetPay Settings</button>
    </form>
    </div>
    <?php
    break;

case 'autofaucet':
    echo "<h1>AutoFaucet Settings</h1>";
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $autofaucet_reward = $mysqli->real_escape_string($_POST['autofaucet_reward']);
        $autofaucet_interval = $mysqli->real_escape_string($_POST['autofaucet_interval']);
        $rewardEnergy = $mysqli->real_escape_string($_POST['rewardEnergy']);
        $autofocus = $mysqli->real_escape_string($_POST['autofocus']);

        // Frissítés az adatbázisban
        $update_autofaucet_reward = "UPDATE settings SET value = '$autofaucet_reward' WHERE name = 'autofaucet_reward'";
        $update_autofaucet_interval = "UPDATE settings SET value = '$autofaucet_interval' WHERE name = 'autofaucet_interval'";
        $update_rewardEnergy = "UPDATE settings SET value = '$rewardEnergy' WHERE name = 'rewardEnergy'";
        $update_autofocus = "UPDATE settings SET value = '$autofocus' WHERE name = 'autofocus'";

        if (
            $mysqli->query($update_autofaucet_reward) === TRUE &&
            $mysqli->query($update_autofaucet_interval) === TRUE &&
            $mysqli->query($update_rewardEnergy) === TRUE &&
            $mysqli->query($update_autofocus) === TRUE
        ) {
            echo "<div class='alert alert-success'>Successful update!</div>";
        } 
    }

    // Aktuális beállítások lekérése
    $autofaucet_reward = $mysqli->query("SELECT value FROM settings WHERE name = 'autofaucet_reward' LIMIT 1")->fetch_assoc()['value'];
    $autofaucet_interval = $mysqli->query("SELECT value FROM settings WHERE name = 'autofaucet_interval' LIMIT 1")->fetch_assoc()['value'];
    $rewardEnergy = $mysqli->query("SELECT value FROM settings WHERE name = 'rewardEnergy' LIMIT 1")->fetch_assoc()['value'];
    $autofocus = $mysqli->query("SELECT value FROM settings WHERE name = 'autofocus' LIMIT 1")->fetch_assoc()['value'];
?>

<div class="container mt-4">
    <form method="post" action="?page=autofaucet">
        <div class="form-group">
            <label for="autofaucet_reward">AutoFaucet Reward:</label>
            <input type="text" class="form-control" id="autofaucet_reward" name="autofaucet_reward" value="<?= $autofaucet_reward; ?>">
        </div>
        <div class="form-group">
            <label for="autofaucet_interval">AutoFaucet Interval (seconds):</label>
            <input type="text" class="form-control" id="autofaucet_interval" name="autofaucet_interval" value="<?= $autofaucet_interval; ?>">
        </div>
        <div class="form-group">
            <label for="rewardEnergy">Energy Reward:</label>
            <input type="text" class="form-control" id="rewardEnergy" name="rewardEnergy" value="<?= $rewardEnergy; ?>">
        </div>
        <div class="form-group">
            <label for="autofocus">Focus Mode:</label>
            <select class="form-control" id="autofocus" name="autofocus">
                <option value="on" <?= $autofocus == "on" ? "selected" : "" ?>>On</option>
                <option value="off" <?= $autofocus == "off" ? "selected" : "" ?>>Off</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Save</button>
    </form>
</div>

<?php
    break;


            case 'dailybonus':
                // Users oldal tartalma
                echo "<h1>Daily bonus Settings</h1>";
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
            
            $bonus_reward_coin = $mysqli->real_escape_string($_POST['bonus_reward_coin']);
            $bonus_reward_xp = $mysqli->real_escape_string($_POST['bonus_reward_xp']);
            $bonus_faucet_require = $mysqli->real_escape_string($_POST['bonus_faucet_require']);
            // Frissítsd a settings táblát a beállításokkal
            $updatebonus_reward_coin = "UPDATE settings SET value = '$bonus_reward_coin' WHERE name = 'bonus_reward_coin'";
            $updatebonus_reward_xp = "UPDATE settings SET value = '$bonus_reward_xp' WHERE name = 'bonus_reward_xp'";
            $updatebonus_faucet_require = "UPDATE settings SET value = '$bonus_faucet_require' WHERE name = 'bonus_faucet_require'";
            
            if (
            $mysqli->query($updatebonus_reward_coin) === TRUE &&
            $mysqli->query($updatebonus_reward_xp) === TRUE &&
            $mysqli->query($updatebonus_faucet_require) === TRUE
            ) {
            // Sikeres frissítés után visszairányítás a dashboard.php oldalra
            echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
            } else {
            //  echo "Error updating settings: " . $mysqli->error;
            }
            
            }
            $bonus_reward_coin = $mysqli->query("SELECT value FROM settings WHERE name = 'bonus_reward_coin' LIMIT 1")->fetch_assoc()['value'];
            $bonus_reward_xp = $mysqli->query("SELECT value FROM settings WHERE name = 'bonus_reward_xp' LIMIT 1")->fetch_assoc()['value'];
            $bonus_faucet_require = $mysqli->query("SELECT value FROM settings WHERE name = 'bonus_faucet_require' LIMIT 1")->fetch_assoc()['value'];
            ?>
            
            
            <div class="container mt-5">
            <form method="post" action="?page=dailybonus">
            <div class="form-group">
                <label for="bonus_reward_coin">Reward Zero:</label>
                <input type="text" class="form-control" id="bonus_reward_coin" name="bonus_reward_coin" value="<?php echo $bonus_reward_coin; ?>">
            </div>
            <div class="form-group">
                <label for="bonus_reward_xp">Reward XP:</label>
                <input type="text" class="form-control" id="bonus_reward_xp" name="bonus_reward_xp" value="<?php echo $bonus_reward_xp; ?>">
            </div>
            <div class="form-group">
                <label for="bonus_faucet_require">Faucet count require claim daily bonus:</label>
                <input type="text" class="form-control" id="bonus_faucet_require" name="bonus_faucet_require" value="<?php echo $bonus_faucet_require; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
            </form>
            </div>
            <?php
                break;	

            case 'achievements':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    if (isset($_POST['add'])) {
                        // Hozzáadás logika
                        $type = $mysqli->real_escape_string($_POST['type']);
                        $condition = (int)$_POST['condition'];
                        $reward = (float)$_POST['reward'];
                        
                        $insert_query = "INSERT INTO achievements (`type`, `condition`, `reward`) 
                                         VALUES ('$type', $condition, $reward)";
                        
                        if ($mysqli->query($insert_query) === TRUE) {
                            echo '<div class="alert alert-success" role="alert">Sikeresen hozzáadva.</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Hiba történt: ' . $mysqli->error . '</div>';
                        }
                    } elseif (isset($_POST['delete'])) {
                        // Törlés logika
                        $id = (int)$_POST['id'];
                        
                        $delete_query = "DELETE FROM achievements WHERE id = $id";
                        
                        if ($mysqli->query($delete_query) === TRUE) {
                            echo '<div class="alert alert-success" role="alert">Sikeresen törölve.</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Hiba történt: ' . $mysqli->error . '</div>';
                        }
                    }
                }
            ?>
    
    <div class="container mt-4">
        <h2>Achievements</h2>
    
        <!-- Hozzáadás form -->
        <form method="post" action="?page=achievements">
            <div class="form-group">
                <label for="type">Type:</label>
                <select class="form-control" id="type" name="type" required>
                    <option value="Faucet">Faucet</option>
                    <option value="Shortlink">Shortlink</option>
                </select>
            </div>
            <div class="form-group">
                <label for="condition">Condition:</label>
                <input type="number" class="form-control" id="condition" name="condition" required>
            </div>
            <div class="form-group">
                <label for="reward">Reward:</label>
                <input type="number" class="form-control" id="reward" name="reward" step="0.000001" required>
            </div>
            <button type="submit" name="add" class="btn btn-primary">Add</button>
        </form>
    
        <hr>
    
        <!-- Törlés form -->
        <h3>Achievements lists</h3>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Type</th>
                    <th scope="col">Condition</th>
                    <th scope="col">Reward</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $select_query = "SELECT * FROM achievements";
                $result = $mysqli->query($select_query);
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $row['type'] . '</td>';
                        echo '<td>' . $row['condition'] . '</td>';
                        echo '<td>' . $row['reward'] . '</td>';
                        echo '<td><form method="post" action="?page=achievements">
                                  <input type="hidden" name="id" value="' . $row['id'] . '">
                                  <button type="submit" name="delete" class="btn btn-danger">Delete</button>
                              </form></td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="5">Nincsenek teljesítmények.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
<?php
    break;
            case 'features':
                // Users oldal tartalma
                echo "<h1>Features settings</h1>";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $faucet = $mysqli->real_escape_string($_POST['faucet']);
        $level_system = $mysqli->real_escape_string($_POST['level_system']);
        $offerwalls_status = $mysqli->real_escape_string($_POST['offerwalls_status']);
        $shortlink_status = $mysqli->real_escape_string($_POST['shortlink_status']);
        $achievements_status = $mysqli->real_escape_string($_POST['achievements_status']);
        $dailybonus_status = $mysqli->real_escape_string($_POST['dailybonus_status']);
        $autofaucet_status = $mysqli->real_escape_string($_POST['autofaucet_status']);
        $energyshop_status = $mysqli->real_escape_string($_POST['energyshop_status']);
        $deposit_status = $mysqli->real_escape_string($_POST['deposit_status']); // Added

        // Frissítsd a settings táblát a beállításokkal
        $updatefaucet = "UPDATE settings SET value = '$faucet' WHERE name = 'claim_enabled'";
        $updatelevel_system = "UPDATE settings SET value = '$level_system' WHERE name = 'level_system'";
        $updateofferwalls_status = "UPDATE settings SET value = '$offerwalls_status' WHERE name = 'offerwalls_status'";
        $updateshortlink_status = "UPDATE settings SET value = '$shortlink_status' WHERE name = 'shortlink_status'";
        $updateachievements_status = "UPDATE settings SET value = '$achievements_status' WHERE name = 'achievements_status'";
        $updatedailybonus_status = "UPDATE settings SET value = '$dailybonus_status' WHERE name = 'dailybonus_status'";
        $updateautofaucet_status = "UPDATE settings SET value = '$autofaucet_status' WHERE name = 'autofaucet_status'";
        $updateenergyshop_status = "UPDATE settings SET value = '$energyshop_status' WHERE name = 'energyshop_status'";
        $updatedeposit_status = "UPDATE settings SET value = '$deposit_status' WHERE name = 'deposit_status'"; // Added

        if (
            $mysqli->query($updatefaucet) === TRUE &&
            $mysqli->query($updatelevel_system) === TRUE &&
            $mysqli->query($updateofferwalls_status) === TRUE &&
            $mysqli->query($updateshortlink_status) === TRUE &&
            $mysqli->query($updateachievements_status) === TRUE &&
            $mysqli->query($updatedailybonus_status) === TRUE &&
            $mysqli->query($updateautofaucet_status) === TRUE &&
            $mysqli->query($updateenergyshop_status) === TRUE &&
            $mysqli->query($updatedeposit_status) === TRUE // Added
        ) {
            echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
        } else {
            echo "Hiba a beállítások frissítése közben: " . $mysqli->error;
        }
    }

    $getlevelsystem = $mysqli->query("SELECT value FROM settings WHERE name = 'level_system' LIMIT 1")->fetch_assoc()['value'];
    $getofferwalls_status = $mysqli->query("SELECT value FROM settings WHERE name = 'offerwalls_status' LIMIT 1")->fetch_assoc()['value'];
    $getshortlink_status = $mysqli->query("SELECT value FROM settings WHERE name = 'shortlink_status' LIMIT 1")->fetch_assoc()['value'];
    $claimStatus = $mysqli->query("SELECT value FROM settings WHERE name = 'claim_enabled' LIMIT 1")->fetch_assoc()['value'];
    $getachievements_status = $mysqli->query("SELECT value FROM settings WHERE name = 'achievements_status' LIMIT 1")->fetch_assoc()['value'];
    $getdailybonus_status = $mysqli->query("SELECT value FROM settings WHERE name = 'dailybonus_status' LIMIT 1")->fetch_assoc()['value'];
    $getautofaucet_status = $mysqli->query("SELECT value FROM settings WHERE name = 'autofaucet_status' LIMIT 1")->fetch_assoc()['value'];
    $getenergyshop_status = $mysqli->query("SELECT value FROM settings WHERE name = 'energyshop_status' LIMIT 1")->fetch_assoc()['value'];
    $getdeposit_status = $mysqli->query("SELECT value FROM settings WHERE name = 'deposit_status' LIMIT 1")->fetch_assoc()['value']; // Added
    ?>
    <div class="container mt-5">
         <form method="post" action="?page=features">
         <div class="form-group">
                <label for="faucet">Faucet Status :</label>
                <select class="form-control" id="faucet" name="faucet">
                    <option value="on" <?php if($claimStatus == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($claimStatus == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="level_system">Level System Status :</label>
                <select class="form-control" id="level_system" name="level_system">
                    <option value="on" <?php if($getlevelsystem == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getlevelsystem == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="offerwall_status">Offerwall Status :</label>
                <select class="form-control" id="offerwalls_status" name="offerwalls_status">
                    <option value="on" <?php if($getofferwalls_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getofferwalls_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="shortlink_status">Shortlink Status :</label>
                <select class="form-control" id="shortlink_status" name="shortlink_status">
                    <option value="on" <?php if($getshortlink_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getshortlink_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="achievements_status">Achievements Status :</label>
                <select class="form-control" id="achievements_status" name="achievements_status">
                    <option value="on" <?php if($getachievements_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getachievements_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="dailybonus_status">Daily bonus Status :</label>
                <select class="form-control" id="dailybonus_status" name="dailybonus_status">
                    <option value="on" <?php if($getdailybonus_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getdailybonus_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="autofaucet_status">Autofaucet Status :</label>
                <select class="form-control" id="autofaucet_status" name="autofaucet_status">
                    <option value="on" <?php if($getautofaucet_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getautofaucet_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="energyshop_status">Energy Shop Status :</label>
                <select class="form-control" id="energyshop_status" name="energyshop_status">
                    <option value="on" <?php if($getenergyshop_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getenergyshop_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <div class="form-group">
                <label for="deposit_status">Deposit Status :</label>
                <select class="form-control" id="deposit_status" name="deposit_status">
                    <option value="on" <?php if($getdeposit_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getdeposit_status == "off") echo "selected"; ?>>Off</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
        
    <?php
                break;


                case 'ptczerads':
                    // Users oldal tartalma
                    echo "<h1>PTC Settings</h1>";
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
          
          $zerads_id = $mysqli->real_escape_string($_POST['zerads_id']);
          $ptcStatus = $mysqli->real_escape_string($_POST['zeradsptc_status']);
          // Frissítsd a settings táblát a beállításokkal
          $updatezerads_id = "UPDATE settings SET value = '$zerads_id' WHERE name = 'zerads_id'";
          $updateptcStatus = "UPDATE settings SET value = '$ptcStatus' WHERE name = 'zeradsptc_status'";
          
          if (
              $mysqli->query($updatezerads_id) === TRUE &&
              $mysqli->query($updateptcStatus) === TRUE
          ) {
              // Sikeres frissítés után visszairányítás a dashboard.php oldalra
              echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
          } else {
            //  echo "Error updating settings: " . $mysqli->error;
          }
        
        }
        $ptcStatus = $mysqli->query("SELECT value FROM settings WHERE name = 'zeradsptc_status' LIMIT 1")->fetch_assoc()['value'];
        $zerads_id = $mysqli->query("SELECT value FROM settings WHERE name = 'zerads_id' LIMIT 1")->fetch_assoc()['value'];
        ?>
        
        
        <div class="container mt-5">
            <form method="post" action="?page=ptczerads">
                <div class="form-group">
                    <label for="zerads_id">zerads.com id :</label>
                    <input type="text" class="form-control" id="zerads_id" name="zerads_id" value="<?php echo $zerads_id; ?>">
                </div>
                <div class="form-group">
                    <label for="ptc_status">PTC Status :</label>
                    <select class="form-control" id="ptc_status" name="ptc_status">
                <option value="on" <?php if($ptcStatus == "on") echo "selected"; ?>>On</option>
                <option value="off" <?php if($ptcStatus == "off") echo "selected"; ?>>Off</option>
            </select>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
            
            <b>Postback url example: https://yourdomain.com/post/zeradsptc.php?pwd=xxxxxx</b>
        </div>
        <?php
                    break;	
                case 'offerwalls':
                    // Users oldal tartalma
                    echo "<h1>Offerwalls settings</h1>";
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
            $bitcotasks_status = $mysqli->real_escape_string($_POST['bitcotasks_status']);
            $bitcotasks_ptc_status = $mysqli->real_escape_string($_POST['bitcotasks_ptc_status']);
            $bitcotasks_shortlink_status = $mysqli->real_escape_string($_POST['bitcotasks_shortlink_status']);
				$bitcotasks_bearer_token = $mysqli->real_escape_string($_POST['bitcotasks_bearer_token']);            
            $bitcotasks_api_key = $mysqli->real_escape_string($_POST['bitcotasks_api_key']);
            $bitcotasks_secret_key = $mysqli->real_escape_string($_POST['bitcotasks_secret_key']);
                    
            // Frissítsd a settings táblát a beállításokkal
        
            $updatebitcotasks_status = "UPDATE settings SET value = '$bitcotasks_status'  WHERE name = 'bitcotasks_status'";
            $updatebitcotasks_ptc_status = "UPDATE settings SET value = '$bitcotasks_ptc_status'  WHERE name = 'bitcotasks_ptc_status'";
            $updatebitcotasks_shortlink_status = "UPDATE settings SET value = '$bitcotasks_shortlink_status'  WHERE name = 'bitcotasks_shortlink_status'";
            $updatebitcotasks_bearer_token = "UPDATE settings SET value = '$bitcotasks_bearer_token'  WHERE name = 'bitcotasks_bearer_token'";
            $updatebitcotasks_api_key = "UPDATE settings SET value = '$bitcotasks_api_key'  WHERE name = 'bitcotasks_api_key'";
            $updatebitcotasks_secret_key = "UPDATE settings SET value = '$bitcotasks_secret_key'  WHERE name = 'bitcotasks_secret_key'";
        
            if (
                $mysqli->query($updatebitcotasks_status) === TRUE &&
                $mysqli->query($updatebitcotasks_ptc_status) === TRUE &&
                $mysqli->query($updatebitcotasks_shortlink_status) === TRUE &&
                $mysqli->query($updatebitcotasks_bearer_token) === TRUE &&
                $mysqli->query($updatebitcotasks_api_key) === TRUE &&
                $mysqli->query($updatebitcotasks_secret_key) === TRUE
            ) {
                // Sikeres frissítés után visszairányítás a dashboard.php oldalra
                echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
                
            } else {
                echo "Hiba a beállítások frissítése közben: " . $mysqli->error;
            }
        }
        
        
        
        $getbitcotasks_status = $mysqli->query("SELECT value FROM settings WHERE name = 'bitcotasks_status' LIMIT 1")->fetch_assoc()['value'];
        
        $getbitcotasks_ptc_status = $mysqli->query("SELECT value FROM settings WHERE name = 'bitcotasks_ptc_status' LIMIT 1")->fetch_assoc()['value'];
        $getbitcotasks_shortlink_status = $mysqli->query("SELECT value FROM settings WHERE name = 'bitcotasks_shortlink_status' LIMIT 1")->fetch_assoc()['value'];
        
        // Lekérdezés a max_reward értékének lekérésére
        $getbitcotasks_api_key = $mysqli->query("SELECT value FROM settings WHERE name = 'bitcotasks_api_key' LIMIT 1")->fetch_assoc()['value'];
        
        // Lekérdezés a max_reward értékének lekérésére
        $getbitcotasks_secret_key = $mysqli->query("SELECT value FROM settings WHERE name = 'bitcotasks_secret_key' LIMIT 1")->fetch_assoc()['value'];
        $getbitcotasks_bearer_token = $mysqli->query("SELECT value FROM settings WHERE name = 'bitcotasks_bearer_token' LIMIT 1")->fetch_assoc()['value'];
        
        
        ?>
        <div class="container mt-5">

             <form method="post" action="?page=offerwalls">
        <div class="card text-center">
  <div class="card-header">
    Bitcotasks settings
  </div>
            <div class="card-body">


                <div class="form-group">
                <label for="bitcotasks_status">Bitcotasks Status :</label>
                <select class="form-control" id="bitcotasks_status" name="bitcotasks_status">
                    <option value="on" <?php if($getbitcotasks_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getbitcotasks_status == "off") echo "selected"; ?>>Off</option>
                </select>
                </div>
                <div class="form-group">
                <label for="bitcotasks_ptc_status">Bitcotasks PTC Status :</label>
                <select class="form-control" id="bitcotasks_ptc_status" name="bitcotasks_ptc_status">
                    <option value="on" <?php if($getbitcotasks_ptc_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getbitcotasks_ptc_status == "off") echo "selected"; ?>>Off</option>
                </select>
                </div>
                <div class="form-group">
                <label for="bitcotasks_shortlink_status">Bitcotasks Shortlink Status :</label>
                <select class="form-control" id="bitcotasks_shortlink_status" name="bitcotasks_shortlink_status">
                    <option value="on" <?php if($getbitcotasks_shortlink_status == "on") echo "selected"; ?>>On</option>
                    <option value="off" <?php if($getbitcotasks_shortlink_status == "off") echo "selected"; ?>>Off</option>
                </select>
                </div>
                <div class="form-group">
                    <label for="bitcotasks_bearer_token">Bitcotasks bearer token:</label>
                    <input type="text" class="form-control" id="bitcotasks_bearer_token" name="bitcotasks_bearer_token" value="<?php echo $getbitcotasks_bearer_token; ?>">
                </div>
                <div class="form-group">
                    <label for="bitcotasks_api_key">Bitcotasks api key</label>
                    <input type="text" class="form-control" id="bitcotasks_api_key" name="bitcotasks_api_key" value="<?php echo $getbitcotasks_api_key; ?>">
                </div>
                <div class="form-group">
                    <label for="bitcotasks_secret_key">Bitcotasks secret key</label>
                    <input type="text" class="form-control" id="bitcotasks_secret_key" name="bitcotasks_secret_key" value="<?php echo $getbitcotasks_secret_key; ?>">
                </div>
                <b>Postback url example: https://yourdomain.com/post/bitcotasks.php</b>
            </div>
        </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
            
        <?php
                    break;

case 'whitelist':
    // Hozzáadás a whitelisthez
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_ip_address'])) {
        $addIpAddress = $mysqli->real_escape_string($_POST['add_ip_address']);
        
        // Lekérdezés az IP-cím ellenőrzésére a whitelistben
        $checkWhiteListSQL = "SELECT * FROM white_list WHERE ip_address = '$addIpAddress' LIMIT 1";
        $whiteListResult = $mysqli->query($checkWhiteListSQL);

        if ($whiteListResult->num_rows == 0) {
            $insertWhiteListSQL = "INSERT INTO white_list (ip_address) VALUES ('$addIpAddress')";
            if ($mysqli->query($insertWhiteListSQL) === TRUE) {
                $whiteListMessages[] = "IP address $addIpAddress successfully added to the whitelist.";
            } else {
                $whiteListMessages[] = "Error adding IP address $addIpAddress to the whitelist: " . $mysqli->error;
            }
        } else {
            $whiteListMessages[] = "IP address $addIpAddress is already in the whitelist.";
        }
    }

    // Eltávolítás a whitelistből
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove_ip_address'])) {
        $removeIpAddress = $mysqli->real_escape_string($_POST['remove_ip_address']);
        
        $deleteWhiteListSQL = "DELETE FROM white_list WHERE ip_address = '$removeIpAddress' LIMIT 1";
        if ($mysqli->query($deleteWhiteListSQL) === TRUE) {
            $whiteListMessages[] = "IP address $removeIpAddress successfully removed from the whitelist.";
        } else {
            $whiteListMessages[] = "Error removing IP address $removeIpAddress from the whitelist: " . $mysqli->error;
        }
    }
    ?>

    <div class="container mt-5">
        <h1>Manage White List</h1>
        <?php
        if (isset($whiteListMessages)) {
            foreach ($whiteListMessages as $whiteListMessage) {
                echo '<div class="alert alert-success">' . $whiteListMessage . '</div>';
            }
        }

        // Lekérdezés az összes whitelist IP-cím lekérdezéséhez
        $whiteListSQL = "SELECT * FROM white_list";
        $whiteListResult = $mysqli->query($whiteListSQL);

        if ($whiteListResult->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>ID</th><th>IP Address</th><th>Action</th></tr></thead><tbody>';
            while($row = $whiteListResult->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . $row['id'] . '</td>';
                echo '<td>' . $row['ip_address'] . '</td>';
                echo '<td>';
                echo '<form method="post" action="">';
                echo '<input type="hidden" name="remove_ip_address" value="' . $row['ip_address'] . '">';
                echo '<button type="submit" class="btn btn-danger">Remove</button>';
                echo '</form>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info">No IP addresses in the whitelist.</div>';
        }
        ?>

        <h2>Add IP Address to White List</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="add_ip_address">IP Address:</label>
                <input type="text" class="form-control" id="add_ip_address" name="add_ip_address" required>
            </div>
            <button type="submit" class="btn btn-primary">Add</button>
        </form>
    </div>
<?php
    break;	
	
        case 'passch':
            // Users oldal tartalma
            echo "<h1>Admin password change</h1>";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];

    // Ellenőrizze a jelenlegi jelszót az adatbázisban
    $username = $_SESSION['admin_username'];

    $sql = "SELECT password_hash FROM admin_users WHERE username = '$username' LIMIT 1";
    $result = $mysqli->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $storedPasswordHash = $row['password_hash'];

        // Ellenőrizze a jelenlegi jelszót
        if (password_verify($currentPassword, $storedPasswordHash)) {
            // A jelenlegi jelszó helyes, cserélje le az új jelszóra
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE admin_users SET password_hash = '$newPasswordHash' WHERE username = '$username'";
            $mysqli->query($updateSql);

            $messages = "<div class='alert alert-success' role='alert'>Password successfully changed!</div>";
        } else {
            echo "Current password is incorrect!";
        }
    } else {
        echo "An error occurred while verifying the password.";
    }
}
?>
    <?php echo $messages; ?>
    <form method="post" action="?page=passch">
        <div class="form-group">
            <label for="current_password">Current password:</label>
            <input type="password" class="form-control" id="current_password" name="current_password" required>
        </div>
        <div class="form-group">
            <label for="new_password">New password:</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Change</button>
    </form>
<?php
            break;
		case 'faucet':
            // Users oldal tartalma
            echo "<h1>Faucet settings</h1>";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $daily_limit = $mysqli->real_escape_string($_POST['daily_limit']);
    $reward = $mysqli->real_escape_string($_POST['reward']);
    $faucetTimer = $mysqli->real_escape_string($_POST['timer']);
    $minWithdrawalGateway = $mysqli->real_escape_string($_POST['min-withdrawal-gateway']);


    // Frissítsd a settings táblát a beállításokkal
    $updatedaily_limit = "UPDATE settings SET value = '$daily_limit' WHERE id = 6";
    $updateRewardQuery = "UPDATE settings SET value = '$reward' WHERE id = 7";
    $updateTimerQuery = "UPDATE settings SET value = '$faucetTimer' WHERE id = 5";
    $updateMinWithdrawalGatewayQuery = "UPDATE settings SET value = '$minWithdrawalGateway' WHERE id = 23";


    if (
        $mysqli->query($updatedaily_limit) === TRUE &&
		  $mysqli->query($updateRewardQuery) === TRUE &&
        $mysqli->query($updateTimerQuery) === TRUE &&
        $mysqli->query($updateMinWithdrawalGatewayQuery) === TRUE
    ) {
        // Sikeres frissítés után visszairányítás a dashboard.php oldalra
        echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
        
    } else {
        echo "Hiba a beállítások frissítése közben: " . $mysqli->error;
    }
}

// Lekérdezés a min_reward értékének lekérésére
$getReward = $mysqli->query("SELECT value FROM settings WHERE name = 'reward' LIMIT 1")->fetch_assoc()['value'];


// Lekérdezés a max_reward értékének lekérésére
$getTimer = $mysqli->query("SELECT value FROM settings WHERE name = 'timer' LIMIT 1")->fetch_assoc()['value'];

// Lekérdezés a min_withdrawal_gateway értékének lekérésére
$getMinWithdrawalGateway = $mysqli->query("SELECT value FROM settings WHERE name = 'min_withdrawal_gateway' LIMIT 1")->fetch_assoc()['value'];

$getDailyLimit = $mysqli->query("SELECT value FROM settings WHERE name = 'daily_limit' LIMIT 1")->fetch_assoc()['value'];

?>
<div class="container mt-5">
     <form method="post" action="?page=faucet">
        <div class="form-group">
            <label for="daily_limit">Daily Limit: </label>
            <input type="text" class="form-control" id="daily_limit" name="daily_limit" value="<?php echo $getDailyLimit; ?>">
        </div>
        <div class="form-group">
            <label for="reward">Min Reward: (ZER)</label>
            <input type="text" class="form-control" id="reward" name="reward" value="<?php echo $getReward; ?>">
        </div>
        <div class="form-group">
            <label for="timer">Faucet Timer(seconds):</label>
            <input type="text" class="form-control" id="timer" name="timer" value="<?php echo $getTimer; ?>">
        </div>
        <div class="form-group">
            <label for="min-withdrawal-gateway">Min Withdrawal (Zatoshi):</label>
            <input type="text" class="form-control" id="min-withdrawal-gateway" name="min-withdrawal-gateway" value="<?php echo $getMinWithdrawalGateway; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
    
<?php
            break;
			
            case 'coingecko':
                // Users oldal tartalma
                echo "<h1>Coingecko settings</h1>";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $coingecko_status = $mysqli->real_escape_string($_POST['coingecko_status']);
        $currency_value = $mysqli->real_escape_string($_POST['currency_value']);
    
        // Frissítsd a settings táblát a beállításokkal

        $updatecoingecko_status = "UPDATE settings SET value = '$coingecko_status' WHERE id = 42";
        $updatecurrency_value = "UPDATE settings SET value = '$currency_value' WHERE id = 29";
    
        if (
            $mysqli->query($updatecoingecko_status) === TRUE &&
            $mysqli->query($updatecurrency_value) === TRUE
        ) {
            // Sikeres frissítés után visszairányítás a dashboard.php oldalra
            echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
            
        } else {
            echo "Hiba a beállítások frissítése közben: " . $mysqli->error;
        }
    }
    
  
    
    $coingecko_status = $mysqli->query("SELECT value FROM settings WHERE name = 'coingecko_status' LIMIT 1")->fetch_assoc()['value'];
    $getcurrency_value = $mysqli->query("SELECT value FROM settings WHERE name = 'currency_value' LIMIT 1")->fetch_assoc()['value'];
    ?>

    <div class="container mt-5">

    <div class="alert alert-info" role="alert">
  Starting from version 1.40.0, the converter has been removed. From now on, you need to define the reward amount for users based on the revenue from offerwalls. Example: 1 cent equals 1 ZER. In this case, for Bitcotasks offerwalls, if you want to give 50% of the revenue to the user, you need to set the reward to 50.00. On the Bitcotasks site, the decimal value in the settings should be set to 5.
</div>
    <form method="post" action="?page=coingecko">

            <div class="form-group">
                    <label for="coingecko_status">Coingecko Status :</label>
                    <select class="form-control" id="coingecko_status" name="coingecko_status">
                        <option value="on" <?php if($coingecko_status == "on") echo "selected"; ?>>On</option>
                        <option value="off" <?php if($coingecko_status == "off") echo "selected"; ?>>Off</option>
                    </select>
                </div>
    <?php 
    if($coingecko_status == "off"){
    ?>
            <div class="form-group">
                <label for="currency_value">Zerocoin currency value (manual settings, only coingecko off status):</label>
                <input type="text" class="form-control" id="currency_value" name="currency_value" value="<?php echo $getcurrency_value; ?>">
            </div>
            <?php } else {
    ?>
            <div class="form-group">
                <label for="currency_value">Zerocoin currency value (coingecko on status):</label>
                <input type="text" class="form-control" id="currency_value" name="currency_value" value="<?php echo $getcurrency_value; ?>" readonly/>
            </div>
    
    <?php        } ?>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
        
    <?php
                break;

		case 'level_system':
            // Users oldal tartalma
            echo "<h1>Level settings</h1>";
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $bonusmaxlevel = $mysqli->real_escape_string($_POST['bonusmaxlevel']);
    $bonuslevelxp = $mysqli->real_escape_string($_POST['bonuslevelxp']);
    $bonuslevelvalue = $mysqli->real_escape_string($_POST['bonuslevelvalue']);
	$xpreward = $mysqli->real_escape_string($_POST['xpreward']);

    // Frissítsd a settings táblát a beállításokkal

    $updatebonusmaxlevel = "UPDATE settings SET value = '$bonusmaxlevel' WHERE id = 33";
    $updatebonuslevelxp = "UPDATE settings SET value = '$bonuslevelxp' WHERE id = 34";
    $updatebonuslevelvalue = "UPDATE settings SET value = '$bonuslevelvalue' WHERE id = 35";
	$updatexpreward = "UPDATE settings SET value = '$xpreward' WHERE id = 36";

    if (
        $mysqli->query($updatebonusmaxlevel) === TRUE &&
        $mysqli->query($updatebonuslevelxp) === TRUE &&
        $mysqli->query($updatebonuslevelvalue) === TRUE &&
        $mysqli->query($updatexpreward) === TRUE
    ) {
        // Sikeres frissítés után visszairányítás a dashboard.php oldalra
        echo "<div class='alert alert-success' role='alert'>Successful update!</div>";
        
    } else {
        echo "Hiba a beállítások frissítése közben: " . $mysqli->error;
    }
}



$getbonusmaxlevel = $mysqli->query("SELECT value FROM settings WHERE name = 'bonusmaxlevel' LIMIT 1")->fetch_assoc()['value'];

// Lekérdezés a max_reward értékének lekérésére
$getbonuslevelxp = $mysqli->query("SELECT value FROM settings WHERE name = 'bonuslevelxp' LIMIT 1")->fetch_assoc()['value'];

// Lekérdezés a max_reward értékének lekérésére
$getbonuslevelvalue = $mysqli->query("SELECT value FROM settings WHERE name = 'bonuslevelvalue' LIMIT 1")->fetch_assoc()['value'];

$getxpreward = $mysqli->query("SELECT value FROM settings WHERE name = 'xpreward' LIMIT 1")->fetch_assoc()['value'];

?>
<div class="container mt-5">
     <form method="post" action="?page=level_system">

        <div class="form-group">
            <label for="bonusmaxlevel">Maximum Level: </label>
            <input type="text" class="form-control" id="bonusmaxlevel" name="bonusmaxlevel" value="<?php echo $getbonusmaxlevel; ?>">
        </div>
        <div class="form-group">
            <label for="bonuslevelxp">XP/Level Up: (example: 20)</label>
            <input type="text" class="form-control" id="bonuslevelxp" name="bonuslevelxp" value="<?php echo $getbonuslevelxp; ?>">
        </div>
        <div class="form-group">
            <label for="bonuslevelvalue">Bonus % value every level up (example: 0.1):</label>
            <input type="text" class="form-control" id="bonuslevelvalue" name="bonuslevelvalue" value="<?php echo $getbonuslevelvalue; ?>">
        </div>
		        <div class="form-group">
            <label for="xpreward">Reward XP every Faucet:</label>
            <input type="text" class="form-control" id="xpreward" name="xpreward" value="<?php echo $getxpreward; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>
</div>
    
<?php
            break;
			
			
case 'duplicate_check':
    // Banned address hozzáadása

    ?>

    <div class="container mt-5">
        <h1>Check Duplicate Registrations</h1>
        <?php
        if (isset($banMessages)) {
            foreach ($banMessages as $banMessage) {
                echo '<div class="alert alert-success">' . $banMessage . '</div>';
            }
        }

        $currentTime = time();
        $time24HoursAgo = $currentTime - 86400;

        $duplicateSQL = "SELECT ip_address, COUNT(*) as count FROM users WHERE last_activity >= $time24HoursAgo GROUP BY ip_address HAVING count > 1";
        $duplicateResult = $mysqli->query($duplicateSQL);

        if ($duplicateResult->num_rows > 0) {
            echo '<table class="table table-bordered">';
            echo '<thead><tr><th>IP Address</th><th>Count</th><th>Users</th></tr></thead><tbody>';
            while($row = $duplicateResult->fetch_assoc()) {
                $ipAddress = $row['ip_address'];
                $userSQL = "SELECT id, address FROM users WHERE ip_address = '$ipAddress'";
                $userResult = $mysqli->query($userSQL);
                $users = '';
                $allBanned = true;
                while ($userRow = $userResult->fetch_assoc()) {
                    $userAddress = $userRow['address'];
                    $checkBanSQL = "SELECT * FROM banned_address WHERE address = '$userAddress' LIMIT 1";
                    $banResult = $mysqli->query($checkBanSQL);

                    if ($banResult->num_rows > 0) {
                        $users .= 'ID: ' . $userRow['id'] . ', Address: ' . $userAddress . ' <span style="color: red;">BANNED</span><br>';
                    } else {
                        $users .= 'ID: ' . $userRow['id'] . ', Address: ' . $userAddress . '<br>';
                        $allBanned = false;
                    }
                }
                if (!$allBanned) {
                    echo '<tr>';
                    echo '<td>' . $row['ip_address'] . '</td>';
                    echo '<td>' . $row['count'] . '</td>';
                    echo '<td>' . $users . '</td>';
                    echo '</tr>';
                }
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info">No duplicate registrations found.</div>';
        }
        ?>
    </div>
<?php
    break;
            
case 'user_list':
    // Pagination settings
    $limit = 25; // Number of entries per page
    $page = isset($_GET['pages']) ? (int)$_GET['pages'] : 1;
    $page = max($page, 1); // Ensure page is at least 1
    $start = ($page - 1) * $limit;

    // Sorting settings
    $validColumns = ['id', 'username', 'balance', 'last_activity'];
    $sortColumn = isset($_GET['sort']) && in_array($_GET['sort'], $validColumns) ? $_GET['sort'] : 'id';
    $sortOrder = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';

    // Retrieve users with pagination and sorting
    $userSQL = "SELECT id, username, address, ip_address, balance, joined, last_activity
                FROM users
                ORDER BY $sortColumn $sortOrder
                LIMIT $start, $limit";

    // Execute the query and check for errors
    if ($userResult = $mysqli->query($userSQL)) {
        // Count total users for pagination
        $countSQL = "SELECT COUNT(*) as total FROM users";
        $countResult = $mysqli->query($countSQL);
        $total = $countResult->fetch_assoc()['total'];
        $pages = ceil($total / $limit);

        // Determine the opposite sort order for toggling
        $toggleOrder = $sortOrder === 'asc' ? 'desc' : 'asc';
        ?>

        <div class="container mt-5">
            <h1>User List</h1>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th><a href="?page=user_list&sort=id&order=<?= $toggleOrder ?>">ID</a></th>
                        <th><a href="?page=user_list&sort=username&order=<?= $toggleOrder ?>">Username</a></th>
                        <th>Address</th>
                        <th>IP Address</th>
                        <th><a href="?page=user_list&sort=balance&order=<?= $toggleOrder ?>">Balance</a></th>
                        <th>Joined</th>
                        <th><a href="?page=user_list&sort=last_activity&order=<?= $toggleOrder ?>">Last Activity</a></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($userRow = $userResult->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $userRow['id']; ?></td>
                            <td><?php echo htmlspecialchars($userRow['username']); ?></td>
                            <td><?php echo $userRow['address']; ?></td>
                            <td><?php echo $userRow['ip_address']; ?></td>
                            <td><?php echo number_format($userRow['balance'], 8); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $userRow['joined']); ?></td>
                            <td><?php echo date('Y-m-d H:i:s', $userRow['last_activity']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php for ($i = 1; $i <= $pages; $i++): ?>
                        <li class="page-item <?php if ($page == $i) echo 'active'; ?>">
                            <a class="page-link" href="?page=user_list<?php if ($i > 1) echo "&pages=$i"; ?>&sort=<?= $sortColumn ?>&order=<?= $sortOrder ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>

    <?php
    } else {
        // Log SQL error and display a user-friendly message
        echo "Error: " . $mysqli->error;
    }
    break;

    case 'shortlinkedit':
        // Szerkesztés
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)$_POST['id'];
            $name = $mysqli->real_escape_string($_POST['name']);
            $url = $mysqli->real_escape_string($_POST['url']);
            $reward = (float)$_POST['reward'];
            $limit_view = (int)$_POST['limit_view'];
            
            $timer = 86400;
            $update_query = "UPDATE shortlinks_list 
                             SET name='$name', url='$url', timer=$timer, reward=$reward, limit_view=$limit_view 
                             WHERE id=$id";
            
            if ($mysqli->query($update_query) === TRUE) {
                echo '<div class="alert alert-success" role="alert">Successfully updated.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error: ' . $mysqli->error . '</div>';
            }
        }
        
        // Szerkesztés űrlap megjelenítése
        $id = (int)$_GET['id'];
        $select_query = "SELECT * FROM shortlinks_list WHERE id=$id";
        $result = $mysqli->query($select_query);
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo '
                <div class="container mt-4">
                    <h2>Szerkesztés</h2>
                    <form method="post" action="?page=shortlinkedit">
                        <input type="hidden" name="id" value="' . $row['id'] . '">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="' . htmlspecialchars($row['name']) . '" required>
                        </div>
                        <div class="form-group">
                            <label for="url">URL:</label>
                            <input type="text" class="form-control" id="url" name="url" value="' . htmlspecialchars($row['url']) . '" required>
                        </div>
                        <div class="form-group">
                            <label for="reward">Reward:</label>
                            <input type="number" class="form-control" id="reward" name="reward" step="0.00000001" value="' . $row['reward'] . '" required>
                        </div>
                        <div class="form-group">
                            <label for="limit_view">Views/24 hour:</label>
                            <input type="number" class="form-control" id="limit_view" name="limit_view" value="' . $row['limit_view'] . '" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            ';
        }
            break;

    case 'shortlinkdelete':
        // Törlés
        $id = (int)$_GET['id'];
        $delete_query = "DELETE FROM shortlinks_list WHERE id=$id";
        
        if ($mysqli->query($delete_query) === TRUE) {
            echo "Deleted successfully.";
        } else {
            echo "Error: " . $mysqli->error;
        }
        break;

    case 'shortlink':

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $mysqli->real_escape_string($_POST['name']);
            $url = $mysqli->real_escape_string($_POST['url']);
            $reward = (float)$_POST['reward'];
            $limit_view = (int)$_POST['limit_view'];
            
            $timer = 86400;

            $insert_query = "INSERT INTO shortlinks_list (name, url, timer, reward, limit_view) 
                             VALUES ('$name', '$url', $timer, $reward, $limit_view)";
            
            if ($mysqli->query($insert_query) === TRUE) {
                echo '<div class="alert alert-success" role="alert">Added successfully.</div>';
            } else {
                echo '<div class="alert alert-danger" role="alert">Error: ' . $mysqli->error . '</div>';
            }
        }
        
        // Hozzáadás űrlap megjelenítése
        echo '
            <div class="container mt-4">
                <h2>Add shortlink</h2>
                <form method="post" action="?page=shortlink">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="url">URL:</label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="Format: https://abc.xyz/api?api=qwerty&url={url}" required>
                    </div>
                    <div class="form-group">Reward (ZER):</label>
                        <input type="number" class="form-control" id="reward" name="reward" step="0.00000001" required>
                    </div>
                    <div class="form-group">
                        <label for="limit_view">Views/24 hour:</label>
                        <input type="number" class="form-control" id="limit_view" name="limit_view" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        ';
        

        $select_query = "SELECT * FROM shortlinks_list";
        $result = $mysqli->query($select_query);
        
        if ($result->num_rows > 0) {
            echo '
                <div class="container mt-4">
                    <h2>Shortlink lists</h2>
                    <table class="table table-bordered">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Reward</th>
                                <th>Views/24 hour</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
            ';
            
            while ($row = $result->fetch_assoc()) {
                echo '
                    <tr>
                        <td>' . $row['id'] . '</td>
                        <td>' . htmlspecialchars($row['name']) . '</td>
                        <td>' . htmlspecialchars($row['url']) . '</td>
                        <td>' . number_format($row['reward'], 8, '.', '') . '</td>
                        <td>' . $row['limit_view'] . '</td>
                        <td>
                            <a href="?page=shortlinkedit&id=' . $row['id'] . '" class="btn btn-sm btn-primary">Edit</a>
                            <a href="?page=shortlinkdelete&id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete it?\')">Delete</a>
                        </td>
                    </tr>
                ';
            }
            
            echo '
                        </tbody>
                    </table>
                </div>
            ';
        } else {
            echo '<div class="alert alert-warning" role="alert">Not found.</div>';
        }
        break;

case 'logout':
    // Munkamenet törlése a felhasználó kijelentkeztetésekor
    session_unset();
    session_destroy();
    // JavaScript kód beillesztése PHP blokkban
    echo '<script type="text/javascript">
    // Az átirányítási funkció
    function redirectToAdmin() {
        window.location.href = "admin.php";
    }

    // Azonnali átirányítás a script betöltésekor
    window.onload = function() {
        redirectToAdmin();
    };
    </script>';
    break;

        case 'ptc_settings':
            echo "<h1>PTC Settings</h1>";
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $credit_value = $mysqli->real_escape_string($_POST['credit_value']);
                $ptc_status = $mysqli->real_escape_string($_POST['ptc_status']);

                $mysqli->query("UPDATE settings SET value = '$credit_value' WHERE name = 'credit_value'");
                $mysqli->query("UPDATE settings SET value = '$ptc_status' WHERE name = 'ptc_status'");

                echo '<div class="alert alert-success">Settings updated successfully.</div>';
            }

            $credit_value_result = $mysqli->query("SELECT value FROM settings WHERE name = 'credit_value'");
            $credit_value = $credit_value_result->fetch_assoc()['value'];

            $ptc_status_result = $mysqli->query("SELECT value FROM settings WHERE name = 'ptc_status'");
            $ptc_status = $ptc_status_result->fetch_assoc()['value'];
            ?>

            <form method="post" action="">
                <div class="form-group">
                    <label for="credit_value">Credit Value (ZER):</label>
                    <input type="number" step="0.001" class="form-control" id="credit_value" name="credit_value" value="<?php echo $credit_value; ?>" required>
                </div>
                <div class="form-group">
                    <label for="ptc_status">PTC Status:</label>
                    <select class="form-control" id="ptc_status" name="ptc_status">
                        <option value="on" <?php echo $ptc_status == 'on' ? 'selected' : ''; ?>>On</option>
                        <option value="off" <?php echo $ptc_status == 'off' ? 'selected' : ''; ?>>Off</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
            <?php
            break;

        case 'ptc_packages':
            echo "<h1>PTC Packages Management</h1>";

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                if (isset($_POST['action'])) {
                    $action = $_POST['action'];

                    if ($action === "add") {
                        $name = $mysqli->real_escape_string($_POST['name']);
                        $duration_seconds = (int)$_POST['duration_seconds'];
                        $zer_cost = (float)$_POST['zer_cost'];
                        $reward = (float)$_POST['reward'];

                        $mysqli->query("INSERT INTO ptc_packages (name, duration_seconds, zer_cost, reward) VALUES ('$name', $duration_seconds, $zer_cost, $reward)");
                        echo "<div class='alert alert-success'>Package added successfully!</div>";
                    } elseif ($action === "edit") {
                        $id = (int)$_POST['id'];
                        $name = $mysqli->real_escape_string($_POST['name']);
                        $duration_seconds = (int)$_POST['duration_seconds'];
                        $zer_cost = (float)$_POST['zer_cost'];
                        $reward = (float)$_POST['reward'];

                        $mysqli->query("UPDATE ptc_packages SET name = '$name', duration_seconds = $duration_seconds, zer_cost = $zer_cost, reward = $reward WHERE id = $id");
                        echo "<div class='alert alert-success'>Package updated successfully!</div>";
                    } elseif ($action === "delete") {
                        $id = (int)$_POST['id'];

                        $mysqli->query("DELETE FROM ptc_packages WHERE id = $id");
                        echo "<div class='alert alert-danger'>Package deleted successfully!</div>";
                    }
                }
            }

            $packages = $mysqli->query("SELECT * FROM ptc_packages");
            ?>

            <div class="container mt-4">
                <h3>Add New Package</h3>
                <form method="post" action="?page=ptc_packages">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="name">Package Name:</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="duration_seconds">Duration (seconds):</label>
                        <input type="number" class="form-control" id="duration_seconds" name="duration_seconds" required>
                    </div>
                    <div class="form-group">
                        <label for="zer_cost">ZER Cost:</label>
                        <input type="number" step="0.00000001" class="form-control" id="zer_cost" name="zer_cost" required>
                    </div>
                    <div class="form-group">
                        <label for="reward">Reward (ZER):</label>
                        <input type="number" step="0.00000001" class="form-control" id="reward" name="reward" required>
                    </div>
                    <button type="submit" class="btn btn-success mt-2">Add Package</button>
                </form>

                <hr>

                <h3>Existing Packages</h3>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Duration (seconds)</th>
                            <th>ZER Cost</th>
                            <th>Reward (ZER)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $packages->fetch_assoc()): ?>
                            <tr>
                                <form method="post" action="?page=ptc_packages">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="action" value="edit">
                                    <td><input type="text" class="form-control" name="name" value="<?= $row['name'] ?>"></td>
                                    <td><input type="number" class="form-control" name="duration_seconds" value="<?= $row['duration_seconds'] ?>"></td>
                                    <td><input type="number" step="0.00000001" class="form-control" name="zer_cost" value="<?= $row['zer_cost'] ?>"></td>
                                    <td><input type="number" step="0.00000001" class="form-control" name="reward" value="<?= $row['reward'] ?>"></td>
                                    <td>
                                        <button type="submit" class="btn btn-primary btn-sm">Save</button>
                                </form>
                                <form method="post" action="?page=ptc_packages" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php
            break;

        default:
            // Ha az URL nem egyezik meg semmelyik oldal nevével
            echo "<h1>404 - Page not found</h1>";
            break;
    }
	

	
}
 ?>
</div>

</body>
</html>


