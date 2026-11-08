<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantasy Football Manager | Standings</title>

    <link rel="stylesheet" href="assets/css/styles.css">

    <!-- Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="dashboard-page standings-page">

    <?php
    // Reuse the sidebar from the other pages
    $activePage = 'standings';
    include __DIR__ . '/../src/sidebar.php';
    ?>

    <div class="main-content">
        <main class="dashboard">

            <!-- Page Heading -->
            <section class="welcome-section">
                <div>
                    <h1>League Standings</h1>
                    <p class="standings-subtitle">
                        View team rankings and season performance.
                    </p>
                </div>
            </section>

            <!-- League Selection -->
            <section class="panel standings-filter">
                <div>
                    <h2>Select League</h2>
                    <p>Choose a league to view its standings.</p>
                </div>

                <select class="standings-select">
                    <option>Fantasy Football League</option>
                    <option>League 2</option>
                    <option>League 3</option>
                </select>
            </section>

            <!-- Summary Cards -->
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div>
                        <p>Total Teams</p>
                        <h2>8</h2>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon yellow">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div>
                        <p>League Leader</p>
                        <h3>Haziels team</h3>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fa-solid fa-calendar-week"></i>
                    </div>
                    <div>
                        <p>Current Week</p>
                        <h2>8</h2>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-football"></i>
                    </div>
                    <div>
                        <p>Season Status</p>
                        <h3>In Progress</h3>
                    </div>
                </div>

            </section>

            <!-- Standings Table -->
            <section class="panel standings-panel">

                <div class="panel-header">
                    <h2>Team Rankings</h2>
                    <span class="standings-week">Week 8</span>
                </div>

                <div class="table-wrapper">
                    <table class="standings-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Team</th>
                                <th>Wins</th>
                                <th>Losses</th>
                                <th>Ties</th>
                                <th>Win %</th>
                                <th>Points For</th>
                                <th>Points Against</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr class="first-place">
                                <td><span class="rank first">1</span></td>
                                <td><strong>Haziels team</strong></td>
                                <td>7</td>
                                <td>1</td>
                                <td>0</td>
                                <td>87.5%</td>
                                <td>1120.5</td>
                                <td>890.2</td>
                            </tr>

                            <tr>
                                <td><span class="rank second">2</span></td>
                                <td><strong>Davids team</strong></td>
                                <td>6</td>
                                <td>2</td>
                                <td>0</td>
                                <td>75.0%</td>
                                <td>1085.3</td>
                                <td>945.6</td>
                            </tr>

                            <tr>
                                <td><span class="rank third">3</span></td>
                                <td><strong>Alfredos team</strong></td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>62.5%</td>
                                <td>1020.8</td>
                                <td>980.4</td>
                            </tr>

                            <tr>
                                <td><span class="rank">4</span></td>
                                <td><strong>Abels team</strong></td>
                                <td>5</td>
                                <td>3</td>
                                <td>0</td>
                                <td>62.5%</td>
                                <td>990.2</td>
                                <td>965.1</td>
                            </tr>

                            <tr>
                                <td><span class="rank">5</span></td>
                                <td><strong>Lukes team</strong></td>
                                <td>4</td>
                                <td>4</td>
                                <td>0</td>
                                <td>50.0%</td>
                                <td>950.7</td>
                                <td>960.3</td>
                            </tr>

                            <tr>
                                <td><span class="rank">6</span></td>
                                <td><strong>Matts team</strong></td>
                                <td>3</td>
                                <td>5</td>
                                <td>0</td>
                                <td>37.5%</td>
                                <td>910.4</td>
                                <td>1010.6</td>
                            </tr>



                        </tbody>
                    </table>
                </div>

            </section>

        </main>
    </div>

</body>

</html>