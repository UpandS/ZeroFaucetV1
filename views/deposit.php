<?php
require_once(__DIR__ . '/session_init.php');
if (empty($user) || !is_object($user) || !$user->getUserData('id')) {
    header("Location: home");
    exit;
}

// Inicializálás
$userId = $user->getUserData('id'); // Javítva: Az id lekérése az objektumból

$deposit = new Deposit($mysqli, $config->get('api_key'));
$activeDeposit = $deposit->getActiveDepositAddress($userId);
$depositAddress = $activeDeposit['address'] ?? null;

$allDeposits = $deposit->getUserDeposits($userId);

// FaucetPay deposit beállítások ellenőrzése
//$faucetpayStatus = $settings['faucetpay_deposit_status'] ?? 'off'; // Ha engedélyezve van, akkor 'on'
$faucetpayStatus = 'off';

// Új deposit address kérés
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!$depositAddress || $activeDeposit['status'] !== 'Pending')) {
    try {
        $depositAddress = $deposit->generateNewAddress($userId);
        $_SESSION['success_message'] = "New deposit address generated: $depositAddress";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }
    header("Location: deposit");
    exit;
}

include("header.php");
?>

<div class="container mt-4">
    <h3>Deposit</h3>

    <?php if (!empty($_SESSION['success_message'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success_message'] ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (!empty($_SESSION['error_message'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error_message'] ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if ($depositAddress): ?>
        <p>Your deposit address is:</p>
        <div class="alert alert-info"><?= htmlspecialchars($depositAddress) ?></div>
        <p>This address is valid until: <?= htmlspecialchars($activeDeposit['expires_at']) ?></p>
        <p><strong>Note:</strong> If no deposit is made within 24 hours, the address will become available for others.</p>
    <?php else: ?>
        <div class="alert alert-info">
            Click the "Request Deposit Address" button to receive a deposit address for sending Zero Coin to the platform. There is no minimum or maximum limit for deposits. The amount you send will be credited to your deposit balance.<br>
            <strong>Important:</strong> We reuse wallet addresses, so never send funds to an address you have previously used out of habit. Always request a new deposit address before making a deposit. Each requested address can only be used for one deposit.<br>
            <strong>Note:</strong> It is possible to receive a wallet address that you have already used for a previous deposit. This happens because, after processing the deposited amount, we make the wallet address reusable.
        </div>
        <form method="POST">
            <button type="submit" class="btn btn-primary">Request Deposit Address</button>
        </form>
    <?php endif; ?>

    <!-- FaucetPay Deposit -->
    <?php if ($faucetpayStatus == 'on'): ?>
        <h4>FaucetPay Deposit</h4>
        <div class="alert alert-danger" role="alert">
    FaucetPay deposit is currently not working and is under development. Once it is operational, I will notify you. Until then, please do not attempt to deposit via FaucetPay, as your deposit will be lost.
</div>

        <form action="https://faucetpay.io/merchant/webscr" method="POST" target="_blank" autocomplete="off">
            <div class="form-group">
                <label>Amount (USD):</label>
                <input type="text" name="amount1" class="form-control" min="1" step="0.01" required>
            </div>
            <input type="hidden" name="merchant_username" value="linux1986">
            <input type="hidden" name="item_description" value="Deposit to CoolFaucet">
            <input type="hidden" name="currency1" value="USD">
            <input type="hidden" name="currency2" value="LTC">
            <input type="hidden" name="custom" value="<?= $userId ?>">
 			   <input type="hidden" name="callback_url" value="https://coolscript.hu/devel05/post/faucetpay_callback.php">
 			   <input type="hidden" name="success_url" value="https://coolfaucet.hu/deposit">
   		   <input type="hidden" name="cancel_url" value="https://coolfaucet.hu//deposit">

            <div class="mt-4">
                <button type="submit" class="btn btn-success">Deposit via FaucetPay</button>
            </div>
        </form>
    <?php endif; ?>

    <h4>Your Deposit History</h4>
    <table class="table">
        <thead>
            <tr>
                <th>Address</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($allDeposits as $deposit): ?>
                <tr>
                    <td><?= htmlspecialchars($deposit['address']) ?></td>
                    <td><?= htmlspecialchars($deposit['amount']) ?> ZER</td>
                    <td><?= htmlspecialchars($deposit['status']) ?></td>
                    <td><?= htmlspecialchars($deposit['created_at']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include("footer.php"); ?>
