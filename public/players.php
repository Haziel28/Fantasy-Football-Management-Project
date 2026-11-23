<?php
require_once __DIR__ . '/../src/league_context.php';

$league = current_league();
$leagueId = $league['id'];


require_once __DIR__ . '/../src/db.php';

// Get real NFL players from PostgreSQL
$stmt = db()->query("
    SELECT
        player_id,
        player_name,
        position,
        nfl_team
    FROM player
    WHERE sleeper_player_id IS NOT NULL
    ORDER BY player_name
");

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert database rows into the format
// expected by the existing HTML table
$players = [];

foreach ($rows as $row) {
    $players[] = [
        $row['player_name'],
        $row['position'],
        $row['nfl_team'] ?? 'FA',
        0.0,
        null
    ];
}


// Filter: available (default), rostered, all
$validFilters = ['available' => 'Available (not on a team)', 'rostered' => 'On a team', 'all' => 'All players'];
$filter = $_GET['status'] ?? 'all';
if (!array_key_exists($filter, $validFilters)) {
    $filter = 'available';
}

$shown = array_values(array_filter($players, function ($p) use ($filter) {
    return match ($filter) {
        'available' => $p[4] === null,
        'rostered'  => $p[4] !== null,
        default     => true,
    };
}));

// Sort by season points, highest first
usort($shown, fn($a, $b) => $b[3] <=> $a[3]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fantasy Football Manager | Players</title>

    <link rel="stylesheet" href="/assets/css/styles.css">

    <!-- Icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="dashboard-page">

    <?php
    $activePage = 'players';
    include __DIR__ . '/../src/sidebar.php';
    ?>

    <!-- Main body -->
    <div class="main-content">

        <main class="dashboard">

            <section class="welcome-section">
                <h1>Players</h1>

                <div class="team-controls">
                    <!-- League picker (keeps the current status filter) -->
                    <?php league_select(['status' => $filter]); ?>

                    <!-- Status filter (league comes from the session) -->
                    <form method="get">
                        <select class="team-select" name="status" aria-label="Filter players"
                                onchange="this.form.submit()">
                            <?php foreach ($validFilters as $value => $label): ?>
                                <option value="<?= $value ?>" <?= $value === $filter ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <noscript><button type="submit" class="btn-small">Apply</button></noscript>
                    </form>
                </div>
            </section>

            <section class="lineup-stack">

                <div class="panel">
                    <div class="panel-header">
                        <h2><?= htmlspecialchars($validFilters[$filter]) ?></h2>
                        <p class="panel-total">
                            <?= htmlspecialchars($league['name']) ?> &middot;
                            <strong><?= count($shown) ?></strong> players
                        </p>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>Pos</th>
                                    <th>Status</th>
                                    <th class="num">Season pts</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$shown): ?>
                                    <tr>
                                        <td class="empty-slot" colspan="5">No players match this filter.</td>
                                    </tr>
                                <?php endif; ?>

                                <?php foreach ($shown as [$name, $pos, $nfl, $points, $owner]): ?>
                                    <tr>
                                        <td>
                                            <div class="player-name"><?= htmlspecialchars($name) ?></div>
                                            <small><?= htmlspecialchars($nfl) ?></small>
                                        </td>
                                        <td>
                                            <span class="pos-badge pos-<?= strtolower($pos) ?>"><?= htmlspecialchars($pos) ?></span>
                                        </td>
                                        <td><?= $owner === null ? 'Available' : htmlspecialchars($owner) ?></td>
                                        <td class="num"><?= number_format($points, 1) ?></td>
                                        <td class="action">
                                            <?php if ($owner === null): ?>
                                                <button type="button" class="btn-small primary">Add player</button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

        </main>
    </div>

</body>
</html>