<?php

declare(strict_types=1);

require_once __DIR__ . '/../src/db.php';

try {
    $pdo = db();



    $check = $pdo->query("
    SELECT
        current_database() AS database_name,
        current_schema() AS schema_name,
        inet_server_addr() AS server_address,
        inet_server_port() AS server_port
");

    print_r($check->fetch(PDO::FETCH_ASSOC));


    // CSV stored in the project's database folder
    $csvPath = __DIR__ . '/../database/stats_player_week_2026.csv';

    if (!is_file($csvPath)) {
        throw new RuntimeException(
            "CSV file not found: $csvPath"
        );
    }

    $file = fopen($csvPath, 'r');

    if ($file === false) {
        throw new RuntimeException('Cannot open CSV file.');
    }

    $headers = fgetcsv($file);

    if ($headers === false) {
        throw new RuntimeException('CSV file is empty.');
    }

    // Remove possible UTF-8 BOM
    $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', $headers[0]);

    $required = [
        'player_id',
        'season',
        'week',
        'season_type',
        'fantasy_points_ppr',
        'fumbles_lost_total',
        'receptions',
        'rushing_tds',
        'rushing_yards',
        'receiving_tds',
        'receiving_yards',
        'passing_yards',
        'passing_tds',
        'passing_interceptions'
    ];

    foreach ($required as $column) {
        if (!in_array($column, $headers, true)) {
            throw new RuntimeException(
                "Missing CSV column: $column"
            );
        }
    }

    // Match GSIS IDs to our database player IDs
    $playerStmt = $pdo->query("
        SELECT player_id, gsis_id
        FROM player
        WHERE gsis_id IS NOT NULL
    ");

    $playerMap = [];

    foreach ($playerStmt->fetchAll(PDO::FETCH_ASSOC) as $player) {
        $playerMap[$player['gsis_id']] = (int)$player['player_id'];
    }

    // Insert or update weekly player statistics
    $sql = "
        INSERT INTO player_game_stat (
            player_id,
            season_year,
            week_number,
            fantasy_points,
            fumbles_lost,
            receptions,
            rushing_tds,
            rushing_yrds,
            receiving_tds,
            passing_yards,
            passing_tds,
            receiving_yds,
            interceptions
        )
        VALUES (
            :player_id,
            :season_year,
            :week_number,
            :fantasy_points,
            :fumbles_lost,
            :receptions,
            :rushing_tds,
            :rushing_yrds,
            :receiving_tds,
            :passing_yards,
            :passing_tds,
            :receiving_yds,
            :interceptions
        )
        ON CONFLICT (player_id, season_year, week_number)
        DO UPDATE SET
            fantasy_points = EXCLUDED.fantasy_points,
            fumbles_lost = EXCLUDED.fumbles_lost,
            receptions = EXCLUDED.receptions,
            rushing_tds = EXCLUDED.rushing_tds,
            rushing_yrds = EXCLUDED.rushing_yrds,
            receiving_tds = EXCLUDED.receiving_tds,
            passing_yards = EXCLUDED.passing_yards,
            passing_tds = EXCLUDED.passing_tds,
            receiving_yds = EXCLUDED.receiving_yds,
            interceptions = EXCLUDED.interceptions
    ";

    $stmt = $pdo->prepare($sql);

    $imported = 0;
    $unmatched = 0;
    $skipped = 0;

    $pdo->beginTransaction();

    while (($values = fgetcsv($file)) !== false) {
        if (count($values) !== count($headers)) {
            $skipped++;
            continue;
        }

        $row = array_combine($headers, $values);

        // Import regular-season games only
        if ($row['season_type'] !== 'REG') {
            $skipped++;
            continue;
        }

        $gsisId = trim($row['player_id']);

        if (!isset($playerMap[$gsisId])) {
            $unmatched++;
            continue;
        }

        $number = static function (string $value): float {
            return is_numeric($value) ? (float)$value : 0.0;
        };

        $integer = static function (string $value) use ($number): int {
            return (int)$number($value);
        };

        $stmt->execute([
            ':player_id' => $playerMap[$gsisId],
            ':season_year' => $integer($row['season']),
            ':week_number' => $integer($row['week']),
            ':fantasy_points' => $number($row['fantasy_points_ppr']),
            ':fumbles_lost' => $integer($row['fumbles_lost_total']),
            ':receptions' => $integer($row['receptions']),
            ':rushing_tds' => $integer($row['rushing_tds']),
            ':rushing_yrds' => $integer($row['rushing_yards']),
            ':receiving_tds' => $integer($row['receiving_tds']),
            ':passing_yards' => $integer($row['passing_yards']),
            ':passing_tds' => $integer($row['passing_tds']),
            ':receiving_yds' => $integer($row['receiving_yards']),
            ':interceptions' => $integer($row['passing_interceptions'])
        ]);

        $imported++;
    }

    $pdo->commit();
    fclose($file);

    echo "Successfully imported $imported weekly stat records!" . PHP_EOL;
    echo "Unmatched player records: $unmatched" . PHP_EOL;
    echo "Skipped records: $skipped" . PHP_EOL;
} catch (Throwable $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    if (isset($file) && is_resource($file)) {
        fclose($file);
    }

    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}
