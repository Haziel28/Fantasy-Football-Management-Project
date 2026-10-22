<?php
// Shared "which league am I in?" logic. Include this at the top of any page
// that depends on the selected league (before any HTML output):
//
//     require_once __DIR__ . '/../src/league_context.php';
//     $league = current_league();
//
// The choice is stored in the session, so it follows the user from page to page.
// Sample data for now; later replace $LEAGUES with the user's real leagues.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// id => details
$LEAGUES = [
    1 => ['id' => 1, 'name' => 'League 1',  'team' => 'League 1',  'teams' => 10, 'record' => '5-2', 'rank' => 2],
    2 => ['id' => 2, 'name' => 'League 2', 'team' => 'League 2', 'teams' => 8,  'record' => '3-4', 'rank' => 6],
];

// A page can switch league with ?league=ID (used by the dropdowns).
if (isset($_GET['league']) && isset($LEAGUES[(int) $_GET['league']])) {
    $_SESSION['league_id'] = (int) $_GET['league'];
}

// Returns the selected league, defaulting to the first one.
function current_league(): array
{
    global $LEAGUES;
    $id = $_SESSION['league_id'] ?? array_key_first($LEAGUES);
    return $LEAGUES[$id] ?? reset($LEAGUES);
}

// Prints a league dropdown that reloads the page when changed.
// $keep = other query params to preserve, e.g. ['status' => 'available'].
function league_select(array $keep = []): void
{
    global $LEAGUES;
    $current = current_league()['id'];
?>
    <form method="get">
        <?php foreach ($keep as $k => $v): ?>
            <input type="hidden" name="<?= htmlspecialchars($k) ?>" value="<?= htmlspecialchars((string) $v) ?>">
        <?php endforeach; ?>
        <select class="team-select" name="league" aria-label="Choose league" onchange="this.form.submit()">
            <?php foreach ($LEAGUES as $l): ?>
                <option value="<?= $l['id'] ?>" <?= $l['id'] === $current ? 'selected' : '' ?>>
                    <?= htmlspecialchars($l['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <noscript><button type="submit" class="btn-small">Go</button></noscript>
    </form>
<?php
}
