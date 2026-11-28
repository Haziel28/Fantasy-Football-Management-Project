<?php
require_once __DIR__ . '/../src/league_context.php';
require_once __DIR__ . '/../src/db.php';

$userId = (int) $_SESSION['user_id'];
$errors = [];

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Create a new fantasy league
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['create_league'])) {

    $leagueName = trim($_POST['league_name'] ?? '');
    $teamName = trim($_POST['team_name'] ?? '');
    $seasonYear = 2026;

    if (!hash_equals($_SESSION['csrf_token'],
        $_POST['csrf_token'] ?? '')) {
        $errors[] = "Invalid form submission.";
    }

    if ($leagueName === '' || strlen($leagueName) > 100) {
        $errors[] = "Enter a valid league name.";
    }

    if ($teamName === '' || strlen($teamName) > 100) {
        $errors[] = "Enter a valid team name.";
    }

    if (!$errors) {
        $pdo = db();

        try {
            $pdo->beginTransaction();

            // Create the league
            $stmt = $pdo->prepare("
                INSERT INTO league
                    (league_name, season_year, user_id)
                VALUES
                    (:league_name, :season_year, :user_id)
                RETURNING league_id
            ");

            $stmt->execute([
                'league_name' => $leagueName,
                'season_year' => $seasonYear,
                'user_id' => $userId
            ]);

            $leagueId = (int) $stmt->fetchColumn();

            // Create the owner's fantasy team
            $stmt = $pdo->prepare("
                INSERT INTO team
                    (team_name, league_id, user_id)
                VALUES
                    (:team_name, :league_id, :user_id)
            ");

            $stmt->execute([
                'team_name' => $teamName,
                'league_id' => $leagueId,
                'user_id' => $userId
            ]);

            $pdo->commit();

            header('Location: leagues.php?created=1');
            exit;

        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log($e->getMessage());
            $errors[] = "Unable to create league.";
        }
    }
}

// Existing league selection
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !isset($_POST['create_league'])
    && isset($LEAGUES[(int) ($_POST['league_id'] ?? 0)])) {

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


<div class="panel" style="margin-bottom: 20px;">

    <div class="panel-header">
        <h2>Create a League</h2>
    </div>

    <?php if (isset($_GET['created'])): ?>
        <p style="color: green;">
            League created successfully!
        </p>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endforeach; ?>

    <form method="POST">

        <input type="hidden" name="csrf_token"
            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <div class="input-box">
            <input type="text"
                name="league_name"
                class="input-field"
                placeholder="League Name"
                maxlength="100"
                required>
        </div>

        <div class="input-box">
            <input type="text"
                name="team_name"
                class="input-field"
                placeholder="Your Team Name"
                maxlength="100"
                required>
        </div>

        <p>Season: 2026</p>
        <p>Scoring: Full PPR</p>

        <button type="submit"
            name="create_league"
            class="btn-small primary">
            Create League
        </button>

    </form>

</div>


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