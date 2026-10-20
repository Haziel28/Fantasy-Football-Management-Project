<?php
require_once __DIR__ . '/../src/league_context.php';

$league = current_league();
$leagueId = $league['id'];

// Sample data. A player exists once; who owns them depends on the league.
// name, position, NFL team, season points, owners (league id => fantasy team; missing = available)
$players = [
    ['Patrick Mahomes',   'QB', 'KC',  198.4, [1 => 'Gridiron Gang',  2 => 'Touchdown Tom']],
    ['Jalen Hurts',       'QB', 'PHI', 176.0, [1 => 'Gridiron Gang']],
    ['Josh Allen',        'QB', 'BUF', 189.2, [2 => 'Blitz Brothers']],
    ['Lamar Jackson',     'QB', 'BAL', 201.7, [1 => 'Blitz Rivals', 2 => 'Gridiron Eight']],
    ['Saquon Barkley',    'RB', 'PHI', 145.6, [1 => 'Gridiron Gang']],
    ['Derrick Henry',     'RB', 'BAL', 120.3, [1 => 'Gridiron Gang', 2 => 'Blitz Brothers']],
    ['Josh Jacobs',       'RB', 'GB',   98.1, [1 => 'Gridiron Gang']],
    ['Jahmyr Gibbs',      'RB', 'DET', 132.9, [2 => 'Blitz Brothers']],
    ['James Cook',        'RB', 'BUF', 118.4, []],
    ['Kyren Williams',    'RB', 'LAR', 124.0, [1 => 'Blitz Rivals']],
    ["Ja'Marr Chase",     'WR', 'CIN', 161.2, [1 => 'Gridiron Gang', 2 => 'Touchdown Tom']],
    ['CeeDee Lamb',       'WR', 'DAL', 139.5, [1 => 'Gridiron Gang']],
    ['Amon-Ra St. Brown', 'WR', 'DET', 143.8, [1 => 'Gridiron Gang', 2 => 'Blitz Brothers']],
    ['Tyreek Hill',       'WR', 'MIA', 101.7, [1 => 'Gridiron Gang']],
    ['Tee Higgins',       'WR', 'CIN', 104.3, [2 => 'Gridiron Eight']],
    ['Jaylen Waddle',     'WR', 'MIA',  92.6, []],
    ['Travis Kelce',      'TE', 'KC',   88.4, [1 => 'Gridiron Gang']],
    ['Sam LaPorta',       'TE', 'DET',  79.8, [2 => 'Blitz Brothers']],
    ['Dalton Kincaid',    'TE', 'BUF',  64.2, []],
    ['Brock Bowers',      'TE', 'LV',  112.5, [1 => 'Blitz Rivals']],
];

// Pick out the owner for the selected league only.
$players = array_map(function ($p) use ($leagueId) {
    return [$p[0], $p[1], $p[2], $p[3], $p[4][$leagueId] ?? null];
}, $players);

// Filter: available (default), rostered, all
$validFilters = ['available' => 'Available (not on a team)', 'rostered' => 'On a team', 'all' => 'All players'];
$filter = $_GET['status'] ?? 'available';
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