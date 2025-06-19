<?php
session_start();
require_once 'config.php';
require_once 'header.php';

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
        $stmt = $pdo->prepare("SELECT nom, prenom FROM etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $user_name = $user_info['prenom'] . ' ' . $user_info['nom'];
    } else if ($user_type === 'prof') {
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
 } else if ($user_type === 'prof') {
    $query = "SELECT c.id_cours, c.intitule, c.date, c.plateforme, 
                     m.intitule AS matiere,
                     'Vous' AS prof  // Colonne ajoutée ici
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
    <br>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            min-height: 100vh;
            color: #333;
            line-height: 1.6;
        }

        .container-cours {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 20px;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: 8px;
            padding: 20px 15px;
            height: fit-content;
        }

        .user-card {
            text-align: center;
            padding-bottom: 15px;
            margin-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .user-name {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .user-role {
            display: inline-block;
            background: #4361ee;
            color: white;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 0.8rem;
        }

        .sidebar-menu {
            list-style: none;
        }

        .sidebar-menu li {
            margin-bottom: 5px;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 10px 12px;
            border-radius: 6px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #f0f3ff;
            color: #4361ee;
        }

        .sidebar-menu i {
            margin-right: 8px;
            font-size: 1rem;
            width: 20px;
            text-align: center;
        }

        /* Main content */
        .main-content {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .header {
            background: white;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .date-display {
            font-size: 1rem;
            color: #666;
        }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .icon-total { background: #4361ee; }
        .icon-week { background: #4cc9f0; }
        .icon-today { background: #f72585; }
        .icon-month { background: #f8961e; }

        .stat-title {
            font-size: 0.9rem;
            color: #666;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 5px 0;
        }

        /* Content area */
        .content-area {
            background: white;
            border-radius: 8px;
            padding: 20px;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .content-header h2 {
            font-size: 1.3rem;
            font-weight: 600;
        }

        .view-toggle {
            display: flex;
            background: #f0f3ff;
            border-radius: 8px;
            padding: 4px;
        }

        .toggle-btn {
            padding: 6px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.85rem;
            background: transparent;
            color: #666;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .toggle-btn.active {
            background: #4361ee;
            color: white;
        }

        .search-container {
            position: relative;
            width: 250px;
        }

        .search-input {
            width: 100%;
            padding: 10px 15px 10px 35px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            background: #f0f3ff;
            font-family: inherit;
        }

        .search-icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        /* Calendar */
        .calendar-view {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #f0f3ff;
            border-radius: 8px;
            overflow: hidden;
        }

        .calendar-header {
            background: #4361ee;
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: 600;
            font-size: 0.8rem;
        }

        .calendar-day {
            background: white;
            min-height: 120px;
            padding: 12px 8px;
            position: relative;
        }

        .day-number {
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        .today .day-number {
            display: inline-block;
            background: #4361ee;
            color: white;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            text-align: center;
            line-height: 26px;
        }

        .course-item {
            color: white;
            padding: 5px 6px;
            margin: 3px 0;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            background: #4361ee;
        }

        /* List view */
        .list-view {
            display: none;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .no-course {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }

        .course-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            border-left: 4px solid #4361ee;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .course-title {
            font-size: 1.1rem;
            font-weight: 600;
        }

        .course-subject {
            background: #4361ee;
            color: white;
            padding: 3px 10px;
            border-radius: 16px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .course-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 12px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .meta-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(67, 97, 238, 0.1);
            color: #4361ee;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .course-actions {
            display: flex;
            gap: 10px;
            margin-top: 8px;
        }

        .action-btn {
            flex: 1;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-family: inherit;
        }

        .btn-primary {
            background: #4361ee;
            color: white;
        }

        .btn-outline {
            background: transparent;
            color: #4361ee;
            border: 1px solid #4361ee;
        }

        /* Floating button */
        .floating-add {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 50px;
            height: 50px;
            background: #f72585;
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .container {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                display: none;
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
        }
    </style>
</head>
<body>
    <div class="container-cours">
        <!-- Sidebar simplifiée -->
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
            
            <div class="content-area">
                <div class="content-header">
                    <h2>Mes cours</h2>
                    <div style="display: flex; gap: 15px;">
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
                    courseDiv.innerHTML = `<i class="fas fa-book"></i> ${cours.intitule.substring(0, 15)}`;
                    
                    // Adaptation pour les professeurs
                    const profName = cours.prof ? cours.prof : "Vous";
                    courseDiv.title = `${cours.intitule} - ${profName}`;
                    
                    courseDiv.onclick = () => showCourseDetails(cours.id_cours);
                    dayDiv.appendChild(courseDiv);
                });
                
                calendarView.appendChild(dayDiv);
            }
        }

        function loadCoursList() {
            const listView = document.getElementById('listView');
            listView.innerHTML = '';

            // Message si aucun cours
            if (coursData.length === 0) {
                listView.innerHTML = '<div class="no-course">Aucun cours disponible</div>';
                return;
            }
            
            coursData.forEach((cours, index) => {
                const courseCard = document.createElement('div');
                courseCard.className = `course-card`;
                
                // Adaptation pour les professeurs
                const profName = cours.prof ? cours.prof : "Vous";
                
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
                            <div>${profName}</div>
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
            const hours = Math.floor(Math.random() * 4) + 9;
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
                // Adaptation pour les professeurs
                const profName = course.prof ? course.prof : "Vous";
                alert(`Détails du cours:\n\nTitre: ${course.intitule}\nMatière: ${course.matiere}\nDate: ${formatDate(course.date)}\nProfesseur: ${profName}\nPlateforme: ${course.plateforme}`);
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            loadCoursList();
            
            // Écouteurs d'événements pour les boutons
            document.getElementById('calendarBtn').addEventListener('click', showCalendar);
            document.getElementById('listBtn').addEventListener('click', showList);
            
            // Écouteurs pour la sidebar
            document.getElementById('sidebarCalendar').addEventListener('click', function(e) {
                e.preventDefault();
                showCalendar();
            });
            
            document.getElementById('sidebarList').addEventListener('click', function(e) {
                e.preventDefault();
                showList();
            });
            
            // Recherche de cours
            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const courseCards = document.querySelectorAll('.course-card');
                
                courseCards.forEach(card => {
                    const title = card.querySelector('.course-title').textContent.toLowerCase();
                    const subject = card.querySelector('.course-subject').textContent.toLowerCase();
                    
                    if (title.includes(searchTerm) || subject.includes(searchTerm)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
    <br>
    <?php include 'footer.php'; ?>

</body>
</html>