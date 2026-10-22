<?php
require_once __DIR__ . '/../src/league_context.php';

// Selecting a league: POST, save to session, then redirect so a refresh
// doesn't resubmit the form.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($LEAGUES[(int) ($_POST['league_id'] ?? 0)])) {
    $_SESSION['league_id'] = (int) $_POST['league_id'];
    header('Location: leagues.php');
    exit;
}

$current = current_league();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fantasy Football Manager | Leagues</title>

    <link rel="stylesheet" href="/assets/css/styles.css">

    <!-- Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body class="dashboard-page">

    <?php
    $activePage = 'leagues';
    include __DIR__ . '/../src/sidebar.php';
    ?>

    <!-- Main body -->
    <div class="main-content">

        <main class="dashboard">

            <section class="welcome-section">
                <h1>Leagues</h1>
            </section>

            <section class="leagues-layout">

                <!-- Left: the selected league -->
                <div class="panel">
                    <div class="panel-header">
                        <h2><?= htmlspecialchars($current['name']) ?></h2>
                        <p class="panel-total">My team: <strong><?= htmlspecialchars($current['team']) ?></strong></p>
                    </div>

                    <div class="league-facts">
                        <div><small>Record</small><strong><?= htmlspecialchars($current['record']) ?></strong></div>
                        <div><small>Rank</small><strong>#<?= (int) $current['rank'] ?></strong></div>
                        <div><small>Teams</small><strong><?= (int) $current['teams'] ?></strong></div>
                    </div>

                    <div class="league-actions">
                        <a href="team.php" class="btn-small primary">My Team</a>
                        <a href="players.php" class="btn-small">Players</a>
                    </div>
                </div>

                <!-- Right: every league the user is in -->
                <div class="panel">
                    <div class="panel-header">
                        <h2>My Leagues</h2>
                        <p class="panel-total"><strong><?= count($LEAGUES) ?></strong> total</p>
                    </div>

                    <ul class="league-list">
                        <?php foreach ($LEAGUES as $l): ?>
                            <?php $isCurrent = $l['id'] === $current['id']; ?>
                            <li>
                                <form method="post">
                                    <input type="hidden" name="league_id" value="<?= $l['id'] ?>">
                                    <button type="submit"
                                        class="league-item<?= $isCurrent ? ' selected' : '' ?>"
                                        <?= $isCurrent ? 'aria-current="true"' : '' ?>>
                                        <span>
                                            <div class="player-name"><?= htmlspecialchars($l['name']) ?></div>
                                            <small><?= htmlspecialchars($l['team']) ?> &middot; <?= htmlspecialchars($l['record']) ?></small>
                                        </span>
                                        <?php if ($isCurrent): ?>
                                            <span class="pos-badge pos-te">Selected</span>
                                        <?php else: ?>
                                            <i class="fa-solid fa-chevron-right"></i>
                                        <?php endif; ?>
                                    </button>
                                </form>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </section>

        </main>
    </div>

</body>

</html>