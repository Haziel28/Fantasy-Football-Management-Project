
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Fantasy Football Manager | Dashboard</title>

    
    <link rel="stylesheet" href="assets/css/styles.css">

    <!--Icons -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>

<body class="dashboard-page">

    <!-- Sidebar -->
    <nav class="sidebar">

        <div class="logo">
            <i class="fa-solid fa-football"></i>
            <span>Fantasy Football<br>Manager</span>
        </div>

        <ul class="nav-links">
            <li>
                <a href="index.html" class="active">
                    <i class="fa-solid fa-house"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-users"></i>
                    <span>My Team</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-user"></i>
                    <span>Players</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-trophy"></i>
                    <span>Leagues</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-calendar-days"></i>
                    <span>Matchups</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-chart-simple"></i>
                    <span>Standings</span>
                </a>
            </li>

            <li>
                <a href="#">
                    <i class="fa-solid fa-right-left"></i>
                    <span>Trade History</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-bottom">
            <a href="#">
                <i class="fa-solid fa-user-gear"></i>
                <span>Profile</span>
            </a>

            <a href="#">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </div>

    </nav>

    <!-- Main body -->
    <div class="main-content">

        <main class="dashboard">

            
            <section class="welcome-section">
                <h1>Dashboard</h1>
            </section>

           
            <section class="stats-grid">

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-solid fa-users"></i>
                    </div>

                    <div>
                        <p>My Teams</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon yellow">
                        <i class="fa-solid fa-trophy"></i>
                    </div>

                    <div>
                        <p>Leagues</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fa-solid fa-chart-column"></i>
                    </div>

                    <div>
                        <p>Total Points</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fa-regular fa-calendar"></i>
                    </div>

                    <div>
                        <p>Next Matchup</p>
                    </div>
                </div>

            </section>

            <!-- Dashboard Panels -->
            <section class="dashboard-grid">

                <!-- My Teams -->
                <div class="panel">
                    <div class="panel-header">
                        <h2>My Teams</h2>
                        <a href="#">View All →</a>
                    </div>

                   
                </div>

                <!-- Current Matchup -->
                <div class="panel">
                    <div class="panel-header">
                        <h2>Current Matchup</h2>
                        <a href="#">View Matchup →</a>
                    </div>

                    
                </div>

                <!-- League Standings -->
                <div class="panel">
                    <div class="panel-header">
                        <h2>League Standings</h2>
                        <a href="#">View Full Standings →</a>
                    </div>

                    <div class="table-wrapper">
                        <table>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Team</th>
                                    <th>W - L</th>
                                    <th>Points For</th>
                                </tr>
                            </thead>

                            <tbody>
                                
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>

        </main>
    </div>

</body>
</html>
