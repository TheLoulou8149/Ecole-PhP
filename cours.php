<?php
session_start();
require_once 'config.php';

// Vérification de la connexion
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

// Récupération des informations utilisateur
try {
    $pdo = getDBConnection();

    if ($user_type === 'etudiant') {
        $stmt = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = $user_info['prenom'] . ' ' . $user_info['nom'];
    } elseif ($user_type === 'prof') {
        $stmt = $pdo->prepare("SELECT nom FROM profs WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = 'Prof. ' . $user_info['nom'];
    }

    // Récupération des cours
    if ($user_type === 'etudiant') {
        $query = "SELECT c.id_cours, c.intitule, c.date, c.plateforme, 
                  m.intitule AS matiere, p.nom AS prof
                  FROM cours c
                  INNER JOIN matieres m ON c.id_matiere = m.id_matiere
                  INNER JOIN profs p ON c.id_prof = p.id_prof
                  INNER JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
                  WHERE ce.id_etudiant = ?";
    } else {
        $query = "SELECT c.id_cours, c.intitule, c.date, c.plateforme,
                  m.intitule AS matiere, 'Vous' AS prof
                  FROM cours c
                  INNER JOIN matieres m ON c.id_matiere = m.id_matiere
                  WHERE c.id_prof = ?";
    }

    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calcul des statistiques
    $totalCours = count($cours);
    $today = date('Y-m-d');
    $coursToday = 0;
    $coursThisWeek = 0;
    $coursThisMonth = 0;

    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
    $startOfMonth = date('Y-m-01');
    $endOfMonth = date('Y-m-t');

    foreach ($cours as $c) {
        if ($c['date'] == $today) $coursToday++;
        if ($c['date'] >= $startOfWeek && $c['date'] <= $endOfWeek) $coursThisWeek++;
        if ($c['date'] >= $startOfMonth && $c['date'] <= $endOfMonth) $coursThisMonth++;
    }

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}

require_once 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - École</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container-cours {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 25px;
            padding: 25px;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 25px;
            height: fit-content;
            position: sticky;
            top: 25px;
        }

        .user-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            margin-bottom: 25px;
            color: white;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .user-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #667eea, #764ba2, #667eea);
            border-radius: 17px;
            z-index: -1;
            animation: borderGlow 3s ease-in-out infinite alternate;
        }

        @keyframes borderGlow {
            0% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .user-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-role {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 8px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            text-decoration: none;
            color: white;
            border-radius: 12px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .sidebar-menu a:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.1);
        }

        .sidebar-menu a.active {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 255, 255, 0.2);
        }

        .sidebar-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }

        /* Main Content */
        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .header-dashboard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f1f5f9;
        }

        .header-dashboard h1 {
            font-size: 2.2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .date-display {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 20px;
            border-radius: 25px;
            font-weight: 500;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        /* Statistics Cards */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 0.9rem;
            font-weight: 500;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .icon-total { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
        .icon-week { background: linear-gradient(135deg, #10b981, #059669); }
        .icon-today { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .icon-month { background: linear-gradient(135deg, #ef4444, #dc2626); }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Course Views */
        .view-container {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }

        .view-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }

        .view-header h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .view-toggle {
            display: flex;
            background: #f1f5f9;
            padding: 4px;
            border-radius: 12px;
        }

        .view-toggle button {
            padding: 10px 20px;
            border: none;
            background: transparent;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .view-toggle button.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        /* Course List */
        .course-list {
            display: grid;
            gap: 15px;
        }

        .course-item {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));
            border-radius: 12px;
            padding: 20px;
            border-left: 4px solid transparent;
            background-clip: padding-box;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            position: relative;
            color: white;
        }

        .course-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border-radius: 2px 0 0 2px;
        }

        .course-item:hover {
            transform: translateX(5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .course-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            margin-bottom: 5px;
        }

        .course-subject {
            color: #e2e8f0;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .course-date {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .course-details {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 10px;
            font-size: 0.9rem;
            color: #e2e8f0;
        }

        .course-platform {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .course-prof {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Calendar Styles */
        .calendar-container {
            display: none;
        }

        .calendar-container.active {
            display: block;
        }

        .calendar {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f1f5f9;
        }

        .calendar-nav {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .calendar-nav button {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .calendar-nav button:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .calendar-month {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1e293b;
            min-width: 180px;
            text-align: center;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            margin-bottom: 20px;
        }

        .calendar-day-header {
            background: #f8fafc;
            padding: 12px;
            text-align: center;
            font-weight: 600;
            color: #64748b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: white;
            border: 1px solid #f1f5f9;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            font-weight: 500;
        }

        .calendar-day:hover {
            background: #f8fafc;
            transform: scale(1.05);
        }

        .calendar-day.other-month {
            color: #cbd5e1;
            background: #f8fafc;
        }

        .calendar-day.today {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-weight: 700;
        }

        .calendar-day.has-course {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            font-weight: 600;
        }

        .calendar-day.has-course::after {
            content: '';
            position: absolute;
            bottom: 3px;
            right: 3px;
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 50%;
        }

        .calendar-day.selected {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(245, 158, 11, 0.3);
        }

        .calendar-events {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 2px solid #f1f5f9;
        }

        .calendar-events h3 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .calendar-event {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-left: 4px solid #667eea;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .calendar-event:hover {
            transform: translateX(5px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }

        .calendar-event-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .calendar-event-details {
            font-size: 0.9rem;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .calendar-legend {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            flex-wrap: wrap;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .legend-today .legend-color {
            background: linear-gradient(135deg, #667eea, #764ba2);
        }

        .legend-course .legend-color {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .legend-selected .legend-color {
            background: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* Responsive Design */
        @media (max-width: 968px) {
            .container-cours {
                grid-template-columns: 1fr;
                gap: 20px;
                padding: 20px;
            }

            .sidebar {
                position: relative;
                top: 0;
            }

            .stats {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }

            .header-dashboard {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header-dashboard h1 {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 640px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .course-header {
                flex-direction: column;
                gap: 10px;
            }

            .course-details {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(102, 126, 234, 0.3);
            border-radius: 50%;
            border-top-color: #667eea;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Smooth transitions */
        * {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body>
    <div class="container-cours">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="user-card">
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-role"><?php echo ucfirst($user_type); ?></div>
            </div>

            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-home"></i> Tableau de bord</a></li>
                <li><a href="#" id="sidebarCalendar"><i class="fas fa-calendar-alt"></i> Calendrier</a></li>
                <li><a href="#" id="sidebarList"><i class="fas fa-book"></i> Mes cours</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header-dashboard">
                <h1><i class="fas fa-graduation-cap"></i> Mes Cours</h1>
                <div class="date-display"><?php 
                    setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'french');
                    echo date('l j F Y'); 
                ?></div>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Cours au total</div>
                        <div class="stat-icon icon-total"><i class="fas fa-book"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $totalCours; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Cette semaine</div>
                        <div class="stat-icon icon-week"><i class="fas fa-calendar-week"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $coursThisWeek; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Aujourd'hui</div>
                        <div class="stat-icon icon-today"><i class="fas fa-clock"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $coursToday; ?></div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Ce mois</div>
                        <div class="stat-icon icon-month"><i class="fas fa-calendar"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $coursThisMonth; ?></div>
                </div>
            </div>

            <!-- Course List View -->
            <div class="view-container">
                <div class="view-header">
                    <h2><i class="fas fa-list"></i> Liste des cours</h2>
                    <div class="view-toggle">
                        <button class="active" id="listView"><i class="fas fa-list"></i> Liste</button>
                        <button id="calendarView"><i class="fas fa-calendar"></i> Calendrier</button>
                    </div>
                </div>

                <!-- Liste des cours -->
                <div class="course-list" id="courseList">
                    <?php if (empty($cours)): ?>
                        <div style="text-align: center; padding: 50px; color: #64748b;">
                            <i class="fas fa-book-open" style="font-size: 3rem; margin-bottom: 20px; opacity: 0.3;"></i>
                            <p>Aucun cours trouvé</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cours as $c): ?>
                            <div class="course-item">
                                <div class="course-header">
                                    <div>
                                        <div class="course-title"><?php echo htmlspecialchars($c['intitule']); ?></div>
                                        <div class="course-subject"><?php echo htmlspecialchars($c['matiere']); ?></div>
                                    </div>
                                    <div class="course-date">
                                        <i class="fas fa-calendar-day"></i>
                                        <?php echo date('d/m/Y', strtotime($c['date'])); ?>
                                    </div>
                                </div>
                                <div class="course-details">
                                    <div class="course-platform">
                                        <i class="fas fa-desktop"></i>
                                        <?php echo htmlspecialchars($c['plateforme']); ?>
                                    </div>
                                    <div class="course-prof">
                                        <i class="fas fa-user-tie"></i>
                                        <?php echo htmlspecialchars($c['prof']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Vue Calendrier -->
                <div class="calendar-container" id="calendarContainer">
                    <div class="calendar">
                        <div class="calendar-header">
                            <div class="calendar-nav">
                                <button id="prevMonth"><i class="fas fa-chevron-left"></i></button>
                                <div class="calendar-month" id="currentMonth"></div>
                                <button id="nextMonth"><i class="fas fa-chevron-right"></i></button>
                            </div>
                            <div class="calendar-legend">
                                <div class="legend-item legend-today">
                                    <div class="legend-color"></div>
                                    <span>Aujourd'hui</span>
                                </div>
                                <div class="legend-item legend-course">
                                    <div class="legend-color"></div>
                                    <span>Cours</span>
                                </div>
                                <div class="legend-item legend-selected">
                                    <div class="legend-color"></div>
                                    <span>Sélectionné</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="calendar-grid" id="calendarGrid">
                            <!-- Le calendrier sera généré par JavaScript -->
                        </div>

                        <div class="calendar-events" id="calendarEvents">
                            <h3><i class="fas fa-calendar-check"></i> Cours du jour sélectionné</h3>
                            <div id="selectedDayEvents">
                                <p style="color: #64748b; font-style: italic;">Cliquez sur une date pour voir les cours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div


<?php
    require_once 'footer.php';
?>