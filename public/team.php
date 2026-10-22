<?php
// Current Static layout for My Team

$week = max(1, min(18, (int) ($_GET['week'] ?? 8)));

$teams = ['League 1', 'League 2'];

// slot, player name, position, NFL team, points
$starters = [
    ['QB',   'Patrick Mahomes',   'QB', 'KC',  24.6],
    ['RB1',  'Saquon Barkley',    'RB', 'PHI', 18.2],
    ['RB2',  null,                null, null,  null],
    ['WR1',  "Ja'Marr Chase",     'WR', 'CIN', 21.8],
    ['WR2',  'CeeDee Lamb',       'WR', 'DAL', 14.0],
    ['TE',   'Travis Kelce',      'TE', 'KC',   9.3],
    ['FLEX', 'Amon-Ra St. Brown', 'WR', 'DET', 16.1],
];

$bench = [
    ['BN1', 'Jalen Hurts',   'QB', 'PHI', 22.0],
    ['BN2', 'Derrick Henry', 'RB', 'BAL', 11.4],
    ['BN3', 'Josh Jacobs',   'RB', 'GB',   8.7],
    ['BN4', 'Tyreek Hill',   'WR', 'MIA', 12.5],
    ['BN5', null, null, null, null],
    ['BN6', null, null, null, null],
    ['BN7', null, null, null, null],
];

$weekTotal = array_sum(array_map(fn($s) => $s[4] ?? 0, $starters));

// Prints one table row for a roster slot.
function slot_row(array $slot): void
{
    [$label, $name, $pos, $nfl, $points] = $slot;
    ?>
    <tr>
        <td class="slot-label"><?= htmlspecialchars($label) ?></td>

        <?php if ($name === null): ?>
            <td class="empty-slot" colspan="2">Empty</td>
            <td class="num">-</td>
            <td class="action"><button type="button" class="btn-small primary">Add player</button></td>
        <?php else: ?>
            <td>
                <div class="player-name"><?= htmlspecialchars($name) ?></div>
                <small><?= htmlspecialchars($nfl) ?></small>
            </td>
            <td><span class="pos-badge pos-<?= strtolower($pos) ?>"><?= htmlspecialchars($pos) ?></span></td>
            <td class="num"><?= number_format($points, 1) ?></td>
            <td class="action"><button type="button" class="btn-small">Change</button></td>
        <?php endif; ?>
    </tr>
    <?php
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fantasy Football Manager | My Team</title>

    <link rel="stylesheet" href="/assets/css/styles.css">

    <!-- Icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="dashboard-page">
            <!--Reusing the sidebar-->
    <?php
    $activePage = 'team';
    include __DIR__ . '/../src/sidebar.php';
    ?>

    <!-- Main body -->
    <div class="main-content">

        <main class="dashboard">

            <section class="welcome-section">
                <h1>My Team</h1>

                <div class="team-controls">
                    <select class="team-select" aria-label="Choose team">
                        <?php foreach ($teams as $t): ?>
                            <option><?= htmlspecialchars($t) ?></option>
                        <?php endforeach; ?>
                    </select>

                    <div class="week-nav">
                        <a href="?week=<?= max(1, $week - 1) ?>" aria-label="Previous week">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>
                        <span>Week <?= $week ?></span>
                        <a href="?week=<?= min(18, $week + 1) ?>" aria-label="Next week">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            </section>

            <section class="lineup-stack">

                <!-- Starters -->
                <div class="panel">
                    <div class="panel-header">
                        <h2>Starters</h2>
                        <p class="panel-total">Week total: <strong><?= number_format($weekTotal, 1) ?></strong></p>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Slot</th>
                                    <th>Player</th>
                                    <th>Pos</th>
                                    <th class="num">Points</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($starters as $slot) slot_row($slot); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bench -->
                <div class="panel">
                    <div class="panel-header">
                        <h2>Bench</h2>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>Slot</th>
                                    <th>Player</th>
                                    <th>Pos</th>
                                    <th class="num">Points</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bench as $slot) slot_row($slot); ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

        </main>
    </div>

</body>
</html>