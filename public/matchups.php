<?php
require_once __DIR__ . '/../src/league_context.php';

$league = current_league();
$week = max(1, min(18, (int) ($_GET['week'] ?? 8)));

// Temporary matchup data until connected to PostgreSQL
$sampleTeams = $league['id'] === 1
    ? ['Haziels team', 'Davids team', 'Matts team',
       'Ethans team', 'Abels team', 'Lukes team',
       'Zays team', 'Alfredos team',
       'bennys team', 'faysals team']
    : ['Haziels team', 'Davids team', 'Matts team',
       'Ethans team', 'Abels team', 'Lukes team',
       'Zays team', 'Alfredos team'];

$matchups = [];

for ($i = 0; $i < count($sampleTeams); $i += 2) {
    $matchups[] = [
        'home' => $sampleTeams[$i],
        'away' => $sampleTeams[$i + 1],
        'home_score' => 112.4 + ($i * 2.1),
        'away_score' => 108.7 + ($i * 1.4),
    ];
}

function matchup_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fantasy Football Manager | Matchups</title>

    <link rel="stylesheet" href="/assets/css/styles.css">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="dashboard-page">

    <?php
    $activePage = 'matchups';
    include __DIR__ . '/../src/sidebar.php';
    ?>

    <div class="main-content">

        <main class="dashboard">

            <!-- Page Heading -->
            <section class="welcome-section matchups-heading">

                <h1>Matchups</h1>

                <div class="team-controls">

                    <?php league_select(['week' => $week]); ?>

                    <!-- Week Navigation -->
                    <div class="week-nav">

                        <a href="?<?= http_build_query(['week' => max(1, $week - 1)]) ?>">
                            <i class="fa-solid fa-chevron-left"></i>
                        </a>

                        <span>Week <?= $week ?></span>

                        <a href="?<?= http_build_query(['week' => min(18, $week + 1)]) ?>">
                            <i class="fa-solid fa-chevron-right"></i>
                        </a>

                    </div>

                </div>

            </section>

            <!-- Matchups Panel -->
            <section class="panel matchups-panel">

                <div class="panel-header">

                    <h2>Week <?= $week ?> Matchups</h2>

                    <p class="panel-total">
                        <?= count($matchups) ?> matchups
                    </p>

                </div>

                <p class="matchups-note">
                    Preview scores — sample data until the database is connected.
                </p>

                <!-- Matchup Cards -->
                <div class="matchups-grid">

                    <?php foreach ($matchups as $matchup): ?>

                        <?php
                        $isMine = $matchup['home'] === $league['team']
                            || $matchup['away'] === $league['team'];
                        ?>

                        <article class="matchup-card<?= $isMine ? ' my-matchup' : '' ?>">

                            <div class="matchup-card-header">

                                <span>
                                    <i class="fa-solid fa-football"></i>
                                    Week <?= $week ?>
                                </span>

                                <?php if ($isMine): ?>
                                    <span class="matchup-my-label">
                                        My Matchup
                                    </span>
                                <?php endif; ?>

                            </div>

                            <!-- Home Team -->
                            <div class="matchup-team-row">

                                <span class="matchup-team-name">
                                    <?= matchup_escape($matchup['home']) ?>
                                </span>

                                <strong>
                                    <?= number_format($matchup['home_score'], 1) ?>
                                </strong>

                            </div>

                            <!-- Away Team -->
                            <div class="matchup-team-row">

                                <span class="matchup-team-name">
                                    <?= matchup_escape($matchup['away']) ?>
                                </span>

                                <strong>
                                    <?= number_format($matchup['away_score'], 1) ?>
                                </strong>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            </section>

        </main>

    </div>

</body>
</html>