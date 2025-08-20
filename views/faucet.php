<?php
require_once(__DIR__ . '/session_init.php');
if (empty($user) || !is_object($user) || !$user->getUserData('id')) {
    header("Location: home");
    exit;
}

// Inicializálás
$faucet = new Faucet($mysqli, $config, $user);

$timer = $config->get('timer');
$dailyLimit = $config->get('daily_limit');

// Ha a felhasználó claimelni akar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    Core::checkCsrfToken(); // ✅ CSRF védelem

    $claimResult = $faucet->claimReward();
    $_SESSION['claim_message'] = $claimResult; // Üzenet mentése SESSION-be

    header("Location: faucet");
    exit();
}

// 🔹 **A User példány már frissült, így a `last_claim` helyes értéket ad vissza**
$lastClaim = (int) $user->getUserData('last_claim');
$wait = max(0, $lastClaim + $timer - time()); // Ha negatív lenne, 0-ra állítjuk
$claimCountToday = $faucet->getDailyClaims();
$claimAvailable = $claimCountToday < $dailyLimit;

// Claim sikeressége vagy hibaüzenet
$claimMessage = $_SESSION['claim_message'] ?? null;
unset($_SESSION['claim_message']); // Üzenetet töröljük, hogy ne ismétlődjön frissítéskor

$csrfToken = Core::generateCsrfToken();

include("header.php");
?>

<?php if ($claimMessage): ?>
    <div class="alert alert-<?= $claimMessage['success'] ? 'success' : 'danger' ?>" role="alert">
        <?= Core::sanitizeOutput($claimMessage['message']) ?>
    </div>
<?php endif; ?>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">Timer</div>
                <div class="card-body">
                    <p class='card-text' id="timerDisplay"><?= $wait > 0 ? Core::sanitizeOutput($wait) . " seconds" : "Ready" ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">Reward</div>
                <div class="card-body">
                    <?php
                    $rewardDetails = $faucet->calculateRewardDetails();
                    $levelSystemEnabled = $config->get('level_system') === "on"; // Ellenőrizzük, hogy be van-e kapcsolva
                    ?>

                    <p class="card-text">
                        <?= Core::sanitizeOutput(number_format($rewardDetails['base'], 8)) ?> ZER
                        <?php if ($levelSystemEnabled && $rewardDetails['bonus'] > 0): ?>
                            <span style="color: green;">+ <?= Core::sanitizeOutput(number_format($rewardDetails['bonus'], 8)) ?> ZER bonus</span>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card bg-light">
                <div class="card-header">Daily Limit</div>
                <div class="card-body">
                    <p class="card-text"><?= Core::sanitizeOutput(($dailyLimit - $claimCountToday) . " / $dailyLimit"); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
        <div class="col-12 col-md-3 text-center p-3">

        </div>
        <div class="col-12 col-md-6 text-center p-3">
<!-- Claim Button Section -->
<div class="text-center">
    <?php if (!$claimAvailable): ?>
        <div class="alert alert-warning">Daily limit reached. You can claim again tomorrow.</div>
    <?php else: ?>
        <form method="POST" action="faucet" id="claimForm" <?= $wait > 0 ? 'style="display:none;"' : '' ?>>
            <input type="hidden" name="csrf_token" value="<?= $csrfToken; ?>"> <!-- ✅ CSRF védelem -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary btn-lg" id="claimButton">Claim Free <?= $config->get('currency_name') ?></button>
            </div>
        </form>
    <?php endif; ?>
</div>
        </div>
        <div class="col-12 col-md-3 text-center p-3">

    </div>

    <div class="text-center">

 </div>


</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let waitTime = <?= json_encode($wait) ?>;
    let timerDisplay = document.getElementById("timerDisplay");
    let claimForm = document.getElementById("claimForm");
    let claimButton = document.getElementById("claimButton");

    function updateTimer() {
        if (waitTime > 0) {
            timerDisplay.textContent = waitTime + " seconds";
            waitTime--;
            setTimeout(updateTimer, 1000);
        } else {
            timerDisplay.textContent = "Ready";
            claimForm.style.display = "block";
            claimButton.removeAttribute("disabled");
        }
    }

    updateTimer();
});
</script>

<?php include "footer.php"; ?>
