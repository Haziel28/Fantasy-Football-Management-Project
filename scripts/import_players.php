<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/db.php';

try {
    // Connect to our existing PostgreSQL database
    $pdo = db();

    // Sleeper API: active NFL players
    $url = 'https://api.sleeper.app/v1/players/nfl?active=true';

    // Request the player data
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_CONNECTTIMEOUT => 10,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        throw new RuntimeException(
            "Sleeper API request failed: HTTP $httpCode"
        );
    }

    // Convert JSON into a PHP array
    $players = json_decode(
        $response,
        true,
        512,
        JSON_THROW_ON_ERROR
    );

    if (!is_array($players)) {
        throw new RuntimeException('Invalid player data.');
    }

    // Insert new players or update existing imports
    $sql = "
        INSERT INTO player (
            sleeper_player_id,
            player_name,
            position,
            nfl_team,
            jersey_number
        )
        VALUES (
            :sleeper_id,
            :player_name,
            :position,
            :nfl_team,
            :jersey_number
        )
        ON CONFLICT (sleeper_player_id)
        DO UPDATE SET
            player_name = EXCLUDED.player_name,
            position = EXCLUDED.position,
            nfl_team = EXCLUDED.nfl_team,
            jersey_number = EXCLUDED.jersey_number
    ";

    $stmt = $pdo->prepare($sql);

    $allowedPositions = ['QB', 'RB', 'WR', 'TE', 'K'];
    $count = 0;

    $pdo->beginTransaction();

    foreach ($players as $sleeperId => $player) {
        if (!is_array($player)) {
            continue;
        }

        $position = $player['position'] ?? null;

        // Only import supported fantasy positions
        if (!in_array($position, $allowedPositions, true)) {
            continue;
        }

        $name = $player['full_name'] ?? null;
        $name = is_string($name) ? trim($name) : '';

        if ($name === '') {
            continue;
        }

        $number = $player['number'] ?? null;

        if (!is_numeric($number) ||
            (int)$number < 0 ||
            (int)$number > 99) {
            $number = null;
        } else {
            $number = (int)$number;
        }

        $team = $player['team'] ?? null;
        $team = is_string($team) && $team !== ''
            ? $team
            : null;

        $stmt->execute([
            ':sleeper_id' => (string)$sleeperId,
            ':player_name' => $name,
            ':position' => $position,
            ':nfl_team' => $team,
            ':jersey_number' => $number
        ]);

        $count++;
    }

    $pdo->commit();

    echo "Successfully processed $count NFL players!" . PHP_EOL;

} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
