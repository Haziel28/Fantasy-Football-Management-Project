<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fantasy Football Manager | Trade History</title>

    <link rel="stylesheet" href="../assets/css/styles.css">

    <!-- Icons -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="dashboard-page trade-history-page">

    <?php
    // Reuse the existing sidebar
    $activePage = 'trades';
    include __DIR__ . '/../../src/sidebar.php';
    ?>

    <div class="main-content">
        <main class="dashboard">

            <!-- Page Heading -->
            <section class="welcome-section">
                <div>
                    <h1>Trade History</h1>
                    <p class="trade-subtitle">
                        View previous trades and track player transactions.
                    </p>
                </div>
            </section>

            <!-- Summary Cards -->
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-right-left"></i>
                    </div>
                    <div>
                        <p>Total Trades</p>
                        <h2>12</h2>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <p>Completed Trades</p>
                        <h2>8</h2>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon yellow">
                        <i class="fa-solid fa-clock"></i>
                    </div>
                    <div>
                        <p>Pending Trades</p>
                        <h2>3</h2>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-ban"></i>
                    </div>
                    <div>
                        <p>Rejected Trades</p>
                        <h2>1</h2>
                    </div>
                </div>

            </section>

            <!-- Filters -->
            <section class="panel trade-filter-panel">

                <div>
                    <h2>Trade Transactions</h2>
                    <p>Browse trades across your fantasy leagues.</p>
                </div>

                <div class="trade-filters">
                    <select class="trade-select" aria-label="Select league">
                        <option>All Leagues</option>
                        <option>Fantasy Football League</option>
                        <option>League 2</option>
                    </select>

                    <select class="trade-select" aria-label="Filter trade status">
                        <option>All Statuses</option>
                        <option>Completed</option>
                        <option>Pending</option>
                        <option>Rejected</option>
                    </select>
                </div>

            </section>

            <!-- Trade History Table -->
            <section class="panel trade-history-panel">

                <div class="panel-header">
                    <h2>Recent Trades</h2>
                    <span class="trade-count">12 Total Trades</span>
                </div>

                <div class="trade-table-wrapper">
                    <table class="trade-table">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>League</th>
                                <th>Team Sending</th>
                                <th>Team Receiving</th>
                                <th>Players Exchanged</th>
                                <th>Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>Oct 22, 2026</td>
                                <td>Fantasy Football League</td>
                                <td><strong>Haziels team</strong></td>
                                <td><strong>Davids team</strong></td>
                                <td>
                                    <div class="trade-players">
                                        <span>Justin Jefferson</span>
                                        <i class="fa-solid fa-right-left"></i>
                                        <span>Ja'Marr Chase</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="trade-status completed">
                                        Completed
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>Oct 19, 2026</td>
                                <td>Fantasy Football League</td>
                                <td><strong>Zays team</strong></td>
                                <td><strong>Omars team</strong></td>
                                <td>
                                    <div class="trade-players">
                                        <span>Josh Allen</span>
                                        <i class="fa-solid fa-right-left"></i>
                                        <span>Lamar Jackson</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="trade-status pending">
                                        Pending
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>Oct 15, 2026</td>
                                <td>League 2</td>
                                <td><strong>Matts team</strong></td>
                                <td><strong>Ethans team</strong></td>
                                <td>
                                    <div class="trade-players">
                                        <span>Christian McCaffrey</span>
                                        <i class="fa-solid fa-right-left"></i>
                                        <span>Saquon Barkley</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="trade-status completed">
                                        Completed
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>Oct 10, 2026</td>
                                <td>Fantasy Football League</td>
                                <td><strong>Alfredos Down</strong></td>
                                <td><strong>Lukes team</strong></td>
                                <td>
                                    <div class="trade-players">
                                        <span>Patrick Mahomes</span>
                                        <i class="fa-solid fa-right-left"></i>
                                        <span>Jalen Hurts</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="trade-status rejected">
                                        Rejected
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td>Oct 5, 2026</td>
                                <td>League 2</td>
                                <td><strong>Bennys team</strong></td>
                                <td><strong>Abels  team</strong></td>
                                <td>
                                    <div class="trade-players">
                                        <span>CeeDee Lamb</span>
                                        <i class="fa-solid fa-right-left"></i>
                                        <span>Amon-Ra St. Brown</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="trade-status completed">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                        </tbody>

                    </table>
                </div>

            </section>

        </main>
    </div>

</body>

</html>