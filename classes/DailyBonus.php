<?php

class DailyBonus {
    private $mysqli;
    private $user;
    private $config;
    private $currentDate;

    public function __construct($mysqli, $user, $config) {
        $this->mysqli = $mysqli;
        $this->user = $user;
        $this->config = $config;
        $this->currentDate = date("Y-m-d");
    }

    // 🔹 Lekéri, hogy a felhasználó már claimelte-e a napi bónuszt
    private function hasClaimedBonus() {
        $stmt = $this->mysqli->prepare("SELECT 1 FROM bonus_history WHERE user_id = ? AND bonus_date = ?");
        $userId = $this->user->getUserData('id');
        $stmt->bind_param("is", $userId, $this->currentDate);
        $stmt->execute();
        $claimed = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return $claimed;
    }

    // 🔹 Felhasználó faucet tranzakcióinak száma
    private function getFaucetCount() {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) AS faucet_count FROM transactions WHERE userid = ? AND type = 'Faucet' AND DATE(FROM_UNIXTIME(timestamp)) = ?");
        $userId = $this->user->getUserData('id');
        $stmt->bind_param("is", $userId, $this->currentDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return intval($result['faucet_count'] ?? 0);
    }

    // 🔹 Napi bónusz állapotának lekérése
    public function getBonusStatus() {
        $alreadyClaimed = $this->hasClaimedBonus();
        $faucetCount = $this->getFaucetCount();
        $requiredFaucet = (int) $this->config->get('bonus_faucet_require');
        $reward = (float) $this->config->get('bonus_reward_coin');
        $xpReward = (int) $this->config->get('bonus_reward_xp');

        return [
            'already_claimed' => $alreadyClaimed,
            'can_claim' => ($faucetCount >= $requiredFaucet && !$alreadyClaimed),
            'faucet_count' => $faucetCount,
            'required_faucet' => $requiredFaucet,
            'reward' => $reward,
            'xp' => $xpReward
        ];
    }

    // 🔹 Napi bónusz claimelése
    public function claimBonus() {
        if (!isset($_SESSION['user_id'])) {
            return ["success" => false, "message" => "You must be logged in to claim the bonus."];
        }

        $userId = $_SESSION['user_id'];
        
        if (!$this->canClaimBonus($userId)) {
            return ["success" => false, "message" => "You can only claim the bonus once per day."];
        }

        $reward = $this->calculateReward($userId);
        $xp = $this->calculateXP($userId);
        $claimTime = date('Y-m-d H:i:s');

        // Start transaction
        $this->mysqli->begin_transaction();

        try {
            // Record the claim
            $stmt = $this->mysqli->prepare("INSERT INTO daily_bonus_claims (user_id, reward, xp_earned, claim_time) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("idis", $userId, $reward, $xp, $claimTime);
            $stmt->execute();

            // Update user balance
            $stmt = $this->mysqli->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->bind_param("di", $reward, $userId);
            $stmt->execute();

            // Update user XP
            $stmt = $this->mysqli->prepare("UPDATE users SET xp = xp + ? WHERE id = ?");
            $stmt->bind_param("ii", $xp, $userId);
            $stmt->execute();

            // Commit transaction
            $this->mysqli->commit();

            return [
                "success" => true,
                "message" => "Successfully claimed your daily bonus of " . $reward . " " . $this->config->get('currency_name') . " and " . $xp . " XP!",
                "reward" => $reward,
                "xp" => $xp
            ];
        } catch (Exception $e) {
            // Rollback transaction on error
            $this->mysqli->rollback();
            return ["success" => false, "message" => "An error occurred while claiming your bonus. Please try again."];
        }
    }

    private function canClaimBonus($userId) {
        $stmt = $this->mysqli->prepare("SELECT 1 FROM bonus_history WHERE user_id = ? AND bonus_date = ?");
        $stmt->bind_param("is", $userId, $this->currentDate);
        $stmt->execute();
        $claimed = $stmt->get_result()->num_rows > 0;
        $stmt->close();

        return !$claimed;
    }

    private function calculateReward($userId) {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) AS faucet_count FROM transactions WHERE userid = ? AND type = 'Faucet' AND DATE(FROM_UNIXTIME(timestamp)) = ?");
        $stmt->bind_param("is", $userId, $this->currentDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $faucetCount = intval($result['faucet_count'] ?? 0);
        $requiredFaucet = (int) $this->config->get('bonus_faucet_require');
        $reward = (float) $this->config->get('bonus_reward_coin');

        return $faucetCount >= $requiredFaucet ? $reward : 0;
    }

    private function calculateXP($userId) {
        $stmt = $this->mysqli->prepare("SELECT COUNT(*) AS faucet_count FROM transactions WHERE userid = ? AND type = 'Faucet' AND DATE(FROM_UNIXTIME(timestamp)) = ?");
        $stmt->bind_param("is", $userId, $this->currentDate);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $faucetCount = intval($result['faucet_count'] ?? 0);
        $requiredFaucet = (int) $this->config->get('bonus_faucet_require');
        $xpReward = (int) $this->config->get('bonus_reward_xp');

        return $faucetCount >= $requiredFaucet ? $xpReward : 0;
    }
}
?>
