<?php
require_once(__DIR__ . '/session_init.php');
if (empty($user) || !is_object($user) || !$user->getUserData('id')) {
    header("Location: home");
    exit;
}

// Inicializálás
$dailyBonus = new DailyBonus($mysqli, $user, $config);

// Ha a felhasználó claimelni akar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claim_bonus'])) {
    Core::checkCsrfToken(); // ✅ CSRF védelem

    $claimResult = $dailyBonus->claimBonus(); // Input sanitization
    $_SESSION['claim_message'] = $claimResult; // Üzenet mentése SESSION-be

    header("Location: daily_bonus");
    exit();
}

// 📌 A napi bónusz állapotának lekérése
$bonusStatus = $dailyBonus->getBonusStatus();

// Claim sikeressége vagy hibaüzenet
$claimMessage = $_SESSION['claim_message'] ?? null;
unset($_SESSION['claim_message']); // Törlés, hogy frissítéskor ne ismétlődjön

include("header.php");
?>

<div class="container text-center">
    <h3>Claim Daily Bonus</h3>

<div class="row">
<div class="col-12 col-md-3 text-center p-3">

       </div>
        <div class="col-12 col-md-6 text-center p-3">
    <?php if ($claimMessage): ?>
        <div class="alert alert-<?= $claimMessage['success'] ? 'success' : 'danger' ?>">
            <?= Core::sanitizeOutput($claimMessage['message']) ?>
        </div>
    <?php endif; ?>

    <?php if ($bonusStatus['already_claimed']): ?>
        <div class='alert alert-success'>
            You have already claimed today's bonus.<br>
            <div id='countdown'></div>
        </div>
    <?php elseif (!$bonusStatus['can_claim']): ?>
        <div class='alert alert-warning'>
            You need at least <?= Core::sanitizeOutput($bonusStatus['required_faucet']) ?> Faucet transactions to claim the bonus.
        </div>
    <?php else: ?>
        <p>Reward: <?= Core::sanitizeOutput($bonusStatus['reward']) ?> ZER, <?= Core::sanitizeOutput($bonusStatus['xp']) ?> XP</p>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= Core::generateCsrfToken(); ?>"> <!-- ✅ CSRF védelem -->
            <button type="submit" class="btn btn-primary" name="claim_bonus">Claim Bonus</button>
        </form>
    <?php endif; ?>
        </div>
        <div class="col-12 col-md-3 text-center p-3">

        </div>
    </div>

    <div class="text-center">

    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const now = new Date();
        const midnight = new Date();
        midnight.setHours(24, 0, 0, 0);
        let timeUntilMidnight = midnight - now;

        const countdown = document.getElementById('countdown');
        function updateCountdown() {
            const hours = Math.floor(timeUntilMidnight / 3600000);
            const minutes = Math.floor((timeUntilMidnight % 3600000) / 60000);
            const seconds = Math.floor((timeUntilMidnight % 60000) / 1000);
            countdown.innerHTML = `Next bonus available in: ${hours}h ${minutes}m ${seconds}s`;
            timeUntilMidnight -= 1000;

            if (timeUntilMidnight > 0) {
                setTimeout(updateCountdown, 1000);
            }
        }
        if (countdown) {
            updateCountdown();
        }
    });
</script>

<?php include("footer.php"); ?>
