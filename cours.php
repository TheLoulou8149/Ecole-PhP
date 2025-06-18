<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];

try {
    $pdo = getDBConnection();
    
    // Récupération des informations utilisateur
    if ($user_type === 'etudiant') {
        $stmt = $pdo->prepare("SELECT nom, prenom, photo FROM etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = $user_info['prenom'] . ' ' . $user_info['nom'];
        $user_photo = $user_info['photo'] ?: 'default-avatar.jpg';
    } else if ($user_type === 'prof') {
        $stmt = $pdo->prepare("SELECT nom, photo FROM profs WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = 'Prof. ' . $user_info['nom'];
        $user_photo = $user_info['photo'] ?: 'default-avatar.jpg';
    }

    // Récupération des cours
    if ($user_type === 'etudiant') {
        $query = "SELECT c.id_cours, c.intitule, c.date, c.plateforme, 
                         m.intitule AS matiere, p.nom AS prof, m.couleur
                  FROM cours c
                  INNER JOIN matieres m ON c.id_matiere = m.id_matiere
                  INNER JOIN profs p ON c.id_prof = p.id_prof
                  INNER JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
                  WHERE ce.id_etudiant = ?";
    } else if ($user_type === 'prof') {
        $query = "SELECT c.id_cours, c.intitule, c.date, c.plateforme, 
                         m.intitule AS matiere, p.nom AS prof, m.couleur
                  FROM cours c
                  INNER JOIN matieres m ON c.id_matiere = m.id_matiere
                  INNER JOIN profs p ON c.id_prof = p.id_prof
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

    // Dates pour la semaine en cours
    $startOfWeek = date('Y-m-d', strtotime('monday this week'));
    $endOfWeek = date('Y-m-d', strtotime('sunday this week'));
    $startOfMonth = date('Y-m-01');
    $endOfMonth = date('Y-m-t');

    foreach ($cours as $c) {
        if ($c['date'] == $today) {
            $coursToday++;
        }
        if ($c['date'] >= $startOfWeek && $c['date'] <= $endOfWeek) {
            $coursThisWeek++;
        }
        if ($c['date'] >= $startOfMonth && $c['date'] <= $endOfMonth) {
            $coursThisMonth++;
        }
    }

} catch (PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - École</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --primary-light: #4895ef;
            --secondary: #3f37c9;
            --accent: #f72585;
            --success: #4cc9f0;
            --warning: #f8961e;
            --danger: #e63946;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --border-radius: 12px;
            --box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            --box-shadow-hover: 0 12px 30px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f1 100%);
            min-height: 100vh;
            color: var(--dark);
            line-height: 1.6;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 25px;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px 20px;
            box-shadow: var(--box-shadow);
            height: fit-content;
        }

        .user-card {
            text-align: center;
            padding-bottom: 20px;
            margin-bottom: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            border: 3px solid var(--primary-light);
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.2);
        }

        .user-name {
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .user-role {
            display: inline-block;
            background: var(--primary-light);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
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
            padding: 12px 15px;
            border-radius: 8px;
            color: var(--gray);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
        }

        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        /* Main content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .header {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px 30px;
            box-shadow: var(--box-shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header h1 i {
            color: var(--primary);
            background: rgba(67, 97, 238, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .date-display {
            font-size: 1.1rem;
            color: var(--gray);
            font-weight: 500;
        }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
        }

        .icon-total { background: var(--primary); }
        .icon-week { background: var(--success); }
        .icon-today { background: var(--accent); }
        .icon-month { background: var(--warning); }

        .stat-title {
            font-size: 1rem;
            color: var(--gray);
            font-weight: 500;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
            margin: 5px 0;
        }

        .stat-footer {
            font-size: 0.9rem;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Content area */
        .content-area {
            background: white;
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--box-shadow);
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .content-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
        }

        .view-toggle {
            display: flex;
            background: #f0f3ff;
            border-radius: 10px;
            padding: 5px;
        }

        .toggle-btn {
            padding: 8px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
            background: transparent;
            color: var(--gray);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toggle-btn.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 10px rgba(67, 97, 238, 0.3);
        }

        .search-container {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            background: #f0f3ff;
            transition: var(--transition);
            font-family: 'Poppins', sans-serif;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
        }

        /* Calendar */
        .calendar-view {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #f0f3ff;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .calendar-header {
            background: var(--primary);
            color: white;
            padding: 15px 10px;
            text-align: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .calendar-day {
            background: white;
            min-height: 140px;
            padding: 15px 10px;
            position: relative;
            transition: var(--transition);
            cursor: pointer;
        }

        .calendar-day:hover {
            background: #f8f9ff;
        }

        .day-number {
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .today .day-number {
            display: inline-block;
            background: var(--primary);
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
        }

        .course-item {
            color: white;
            padding: 6px 8px;
            margin: 4px 0;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .course-item:hover {
            transform: translateX(3px);
        }

        /* List view */
        .list-view {
            display: none;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .course-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--primary);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--box-shadow-hover);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .course-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.3;
        }

        .course-subject {
            background: var(--primary);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .course-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
            color: var(--gray);
        }

        .meta-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(67, 97, 238, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .course-actions {
            display: flex;
            gap: 12px;
            margin-top: 10px;
        }

        .action-btn {
            flex: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-primary:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .btn-outline:hover {
            background: rgba(67, 97, 238, 0.1);
        }

        /* Floating button */
        .floating-add {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--accent);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(247, 37, 133, 0.4);
            transition: var(--transition);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-add:hover {
            transform: scale(1.1) rotate(90deg);
            box-shadow: 0 8px 25px rgba(247, 37, 133, 0.5);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none; /* On cache la sidebar en version mobile */
            }
        }

        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr;
            }
            
            .calendar-view {
                grid-template-columns: 1fr;
            }
            
            .search-container {
                width: 100%;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="user-card">
                <img src="<?php echo $user_photo; ?>" alt="Avatar" class="avatar">
                <div class="user-name"><?php echo htmlspecialchars($user_name); ?></div>
                <div class="user-role"><?php echo ucfirst($user_type); ?></div>
            </div>
            
            <ul class="sidebar-menu">
                <li><a href="#" class="active"><i class="fas fa-home"></i> Tableau de bord</a></li>
                <li><a href="#"><i class="fas fa-calendar-alt"></i> Calendrier</a></li>
                <li><a href="#"><i class="fas fa-book"></i> Mes cours</a></li>
                <li><a href="#"><i class="fas fa-graduation-cap"></i> Diplômes</a></li>
                <li><a href="#"><i class="fas fa-users"></i> Étudiants</a></li>
                <li><a href="#"><i class="fas fa-chalkboard-teacher"></i> Professeurs</a></li>
                <li><a href="#"><i class="fas fa-cog"></i> Paramètres</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1><i class="fas fa-graduation-cap"></i> Tableau de bord</h1>
                <div class="date-display"><?php echo date('l j F Y'); ?></div>
            </div>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Cours au total</div>
                        <div class="stat-icon icon-total"><i class="fas fa-book"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $totalCours; ?></div>
                    <div class="stat-footer"><i class="fas fa-arrow-up"></i> 12% depuis le mois dernier</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Cette semaine</div>
                        <div class="stat-icon icon-week"><i class="fas fa-calendar-week"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $coursThisWeek; ?></div>
                    <div class="stat-footer"><i class="fas fa-check-circle"></i> <?php echo $coursThisWeek; ?> programmés</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Aujourd'hui</div>
                        <div class="stat-icon icon-today"><i class="fas fa-clock"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $coursToday; ?></div>
                    <div class="stat-footer"><i class="fas fa-bell"></i> Prochain cours à 10h30</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-title">Ce mois</div>
                        <div class="stat-icon icon-month"><i class="fas fa-calendar"></i></div>
                    </div>
                    <div class="stat-value"><?php echo $coursThisMonth; ?></div>
                    <div class="stat-footer"><i class="fas fa-chart-line"></i> 25% d'augmentation</div>
                </div>
            </div>
            
            <div class="content-area">
                <div class="content-header">
                    <h2>Mes cours</h2>
                    <div style="display: flex; gap: 20px;">
                        <div class="view-toggle">
                            <button class="toggle-btn active" id="calendarBtn">
                                <i class="fas fa-calendar-alt"></i> Calendrier
                            </button>
                            <button class="toggle-btn" id="listBtn">
                                <i class="fas fa-list"></i> Liste
                            </button>
                        </div>
                        
                        <div class="search-container">
                            <input type="text" class="search-input" placeholder="Rechercher un cours..." id="searchInput">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                </div>
                
                <div id="calendarView" class="calendar-view">
                    <div class="calendar-header">Lundi</div>
                    <div class="calendar-header">Mardi</div>
                    <div class="calendar-header">Mercredi</div>
                    <div class="calendar-header">Jeudi</div>
                    <div class="calendar-header">Vendredi</div>
                    <div class="calendar-header">Samedi</div>
                    <div class="calendar-header">Dimanche</div>
                    <!-- Les jours seront ajoutés dynamiquement par JavaScript -->
                </div>
                
                <div id="listView" class="list-view">
                    <!-- Les cours seront chargés ici dynamiquement -->
                </div>
            </div>
        </div>
    </div>
    
    <button class="floating-add" onclick="addNewCourse()">
        <i class="fas fa-plus"></i>
    </button>
    
    <script>
        // Données des cours
        const coursData = <?php echo json_encode($cours); ?>;
        const today = new Date();
        const currentMonth = today.getMonth();
        const currentYear = today.getFullYear();
        
        // Couleurs pour les matières
        const colors = {
            'Mathématiques': '#4361ee',
            'Physique': '#4895ef',
            'Chimie': '#3f37c9',
            'Informatique': '#4cc9f0',
            'Littérature': '#f72585',
            'Histoire': '#7209b7',
            'Philosophie': '#560bad',
            'Économie': '#b5179e',
            'Biologie': '#2ec4b6',
            'Droit': '#ff9f1c'
        };

        function initializeCalendar() {
            const calendarView = document.getElementById('calendarView');
            // Conserver les en-têtes
            const headers = calendarView.querySelectorAll('.calendar-header');
            calendarView.innerHTML = '';
            headers.forEach(header => calendarView.appendChild(header));

            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            
            // Ajouter les jours vides du début
            const startOffset = firstDay === 0 ? 6 : firstDay - 1;
            for (let i = 0; i < startOffset; i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day';
                calendarView.appendChild(emptyDay);
            }
            
            // Ajouter les jours du mois
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day';
                
                // Marquer aujourd'hui
                if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
                    dayDiv.classList.add('today');
                }
                
                const dayNumber = document.createElement('div');
                dayNumber.className = 'day-number';
                dayNumber.textContent = day;
                dayDiv.appendChild(dayNumber);
                
                // Vérifier s'il y a des cours ce jour-là
                const currentDate = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const coursThisDay = coursData.filter(cours => cours.date === currentDate);
                
                coursThisDay.forEach(cours => {
                    const courseDiv = document.createElement('div');
                    courseDiv.className = 'course-item';
                    courseDiv.style.backgroundColor = colors[cours.matiere] || '#4361ee';
                    courseDiv.innerHTML = `<i class="fas fa-book"></i> ${cours.intitule.substring(0, 15)}`;
                    courseDiv.title = `${cours.intitule} - ${cours.prof}`;
                    courseDiv.onclick = () => showCourseDetails(cours.id_cours);
                    dayDiv.appendChild(courseDiv);
                });
                
                calendarView.appendChild(dayDiv);
            }
        }

        function loadCoursList() {
            const listView = document.getElementById('listView');
            listView.innerHTML = '';
            
            coursData.forEach((cours, index) => {
                const courseCard = document.createElement('div');
                courseCard.className = `course-card`;
                courseCard.style.borderLeftColor = colors[cours.matiere] || '#4361ee';
                
                courseCard.innerHTML = `
                    <div class="course-header">
                        <div class="course-title">${cours.intitule}</div>
                        <div class="course-subject">${cours.matiere}</div>
                    </div>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <div class="meta-icon"><i class="fas fa-calendar"></i></div>
                            <div>${formatDate(cours.date)}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-icon"><i class="fas fa-user-tie"></i></div>
                            <div>${cours.prof}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-icon"><i class="fas fa-laptop"></i></div>
                            <div>${cours.plateforme}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-icon"><i class="fas fa-clock"></i></div>
                            <div>${getRandomTime()}</div>
                        </div>
                    </div>
                    
                    <div class="course-actions">
                        <button class="action-btn btn-primary" onclick="accessCourse(${cours.id_cours})">
                            <i class="fas fa-play"></i> Accéder
                        </button>
                        <button class="action-btn btn-outline" onclick="showCourseDetails(${cours.id_cours})">
                            <i class="fas fa-info-circle"></i> Détails
                        </button>
                    </div>
                `;
                
                listView.appendChild(courseCard);
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
        
        function getRandomTime() {
            const hours = Math.floor(Math.random() * 4) + 9; // 9h à 12h
            const minutes = Math.random() > 0.5 ? '00' : '30';
            return `${hours}:${minutes}`;
        }

        function showCalendar() {
            document.getElementById('calendarView').style.display = 'grid';
            document.getElementById('listView').style.display = 'none';
            document.getElementById('calendarBtn').classList.add('active');
            document.getElementById('listBtn').classList.remove('active');
        }

        function showList() {
            document.getElementById('calendarView').style.display = 'none';
            document.getElementById('listView').style.display = 'grid';
            document.getElementById('calendarBtn').classList.remove('active');
            document.getElementById('listBtn').classList.add('active');
        }

        function addNewCourse() {
            alert('Fonctionnalité d\'ajout de cours à implémenter');
        }
        
        function accessCourse(courseId) {
            alert(`Accès au cours ID: ${courseId} - Cette fonctionnalité est en cours de développement`);
        }
        
        function showCourseDetails(courseId) {
            const course = coursData.find(c => c.id_cours == courseId);
            if (course) {
                alert(`Détails du cours:\n\nTitre: ${course.intitule}\nMatière: ${course.matiere}\nDate: ${formatDate(course.date)}\nProfesseur: ${course.prof}\nPlateforme: ${course.plateforme}`);
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            loadCoursList();
            
            // Écouteurs d'événements pour les boutons
            document.getElementById('calendarBtn').addEventListener('click', showCalendar);
            document.getElementById('listBtn').addEventListener('click', showList);
        });
    </script>
</body>
</html>