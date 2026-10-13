<?php
// Creating the sidebar for all the pages to use
$links = [
    'dashboard' => ['/index.php',          'fa-house',         'Dashboard'],
    'team'      => ['/team.php',           'fa-users',         'My Team'],
    'players'   => ['/players.php',        'fa-user',          'Players'],
    'leagues'   => ['/leagues.php',        'fa-trophy',        'Leagues'],
    'matchups'  => ['/matchups.php',       'fa-calendar-days', 'Matchups'],
    'standings' => ['/standings.php',      'fa-chart-simple',  'Standings'],
    'trades'    => ['/reports/trades.php', 'fa-right-left',    'Trade History'],
];
?>
<nav class="sidebar">

    <div class="logo">
        <i class="fa-solid fa-football"></i>
        <span>Fantasy Football<br>Manager</span>
    </div>

    <ul class="nav-links">
        <?php foreach ($links as $key => [$href, $icon, $label]): ?>
            <li>
                <a href="<?= $href ?>"<?= ($activePage ?? '') === $key ? ' class="active"' : '' ?>>
                    <i class="fa-solid <?= $icon ?>"></i>
                    <span><?= $label ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="sidebar-bottom">
        <a href="#">
            <i class="fa-solid fa-user-gear"></i>
            <span>Profile</span>
        </a>
        <a href="/logout.php">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span>Logout</span>
        </a>
    </div>

</nav>