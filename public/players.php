<?php
// Static layout for Players. Everything below is sample data for now;
// later this array gets replaced by a real query joining players to rosters.

// name, position, NFL team, season points, fantasy team that owns them (null = available)
$players = [
    ['Patrick Mahomes',   'QB', 'KC',  'Gridiron Gang',  198.4],
    ['Jalen Hurts',       'QB', 'PHI', 'Gridiron Gang',  176.0],
    ['Josh Allen',        'QB', 'BUF', null,             189.2],
    ['Lamar Jackson',     'QB', 'BAL', 'Blitz Brothers', 201.7],
    ['Saquon Barkley',    'RB', 'PHI', 'Gridiron Gang',  145.6],
    ['Derrick Henry',     'RB', 'BAL', 'Gridiron Gang',  120.3],
    ['Josh Jacobs',       'RB', 'GB',  'Gridiron Gang',   98.1],
    ['Jahmyr Gibbs',      'RB', 'DET', null,             132.9],
    ['James Cook',        'RB', 'BUF', null,             118.4],
    ['Kyren Williams',    'RB', 'LAR', 'Blitz Brothers', 124.0],
    ['Ja\'Marr Chase',    'WR', 'CIN', 'Gridiron Gang',  161.2],
    ['CeeDee Lamb',       'WR', 'DAL', 'Gridiron Gang',  139.5],
    ['Amon-Ra St. Brown', 'WR', 'DET', 'Gridiron Gang',  143.8],
    ['Tyreek Hill',       'WR', 'MIA', 'Gridiron Gang',  101.7],
    ['Tee Higgins',       'WR', 'CIN', null,             104.3],
    ['Jaylen Waddle',     'WR', 'MIA', null,              92.6],
    ['Travis Kelce',      'TE', 'KC',  'Gridiron Gang',   88.4],
    ['Sam LaPorta',       'TE', 'DET', null,              79.8],
    ['Dalton Kincaid',    'TE', 'BUF', null,              64.2],
    ['Brock Bowers',      'TE', 'LV',  'Blitz Brothers', 112.5],
];

// Filter: available (default), rostered, all
$validFilters = ['available' => 'Available (not on a team)', 'rostered' => 'On a team', 'all' => 'All players'];
$filter = $_GET['status'] ?? 'available';
if (!array_key_exists($filter, $validFilters)) {
    $filter = 'available';
}

$shown = array_values(array_filter($players, function ($p) use ($filter) {
    return match ($filter) {
        'available' => $p[3] === null,
        'rostered'  => $p[3] !== null,
        default     => true,
    };
}));

// Sort by season points, highest first
usort($shown, fn($a, $b) => $b[4] <=> $a[4]);
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
                        <p class="panel-total">Showing <strong><?= count($shown) ?></strong> players</p>
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

                                <?php foreach ($shown as [$name, $pos, $nfl, $owner, $points]): ?>
                                    <tr>
                                        <td>
                                            <div class="player-name"><?= htmlspecialchars($name) ?></div>
                                            <small><?= htmlspecialchars($nfl) ?></small>
                                        </td>
                                        <td>
                                            <span class="pos-badge pos-<?= strtolower($pos) ?>"><?= htmlspecialchars($pos) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($owner === null): ?>
                                                Available
                                            <?php else: ?>
                                                <?= htmlspecialchars($owner) ?>
                                            <?php endif; ?>
                                        </td>
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