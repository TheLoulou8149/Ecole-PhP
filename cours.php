<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - École</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <?php include 'header.php'; ?>
   <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --accent-gradient: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
            --success-color: #00b894;
            --warning-color: #fdcb6e;
            --danger-color: #ff7675;
            --text-primary: #2d3436;
            --text-secondary: #636e72;
            --background-light: #f8f9fa;
            --white: #ffffff;
            --shadow-light: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-medium: 0 8px 30px rgba(0, 0, 0, 0.12);
            --shadow-heavy: 0 15px 50px rgba(0, 0, 0, 0.15);
            --border-radius: 16px;
            --border-radius-large: 24px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
            color: var(--text-primary);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-large);
            padding: 40px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-gradient);
        }

        .header h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            font-weight: 700;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
        }

        .user-info {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            font-size: 1.1rem;
            color: var(--text-secondary);
            flex-wrap: wrap;
        }

        .user-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .main-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius-large);
            padding: 40px;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: var(--border-radius);
            text-align: center;
            box-shadow: var(--shadow-light);
            transition: var(--transition);
            border: 1px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .stat-card:hover::before {
            transform: scaleX(1);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 1.1rem;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .view-toggle {
            display: flex;
            background: var(--background-light);
            border-radius: 12px;
            padding: 4px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .toggle-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 600;
            font-size: 0.95rem;
            background: transparent;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .toggle-btn.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: var(--shadow-light);
        }

        .search-container {
            position: relative;
            max-width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 12px 20px 12px 45px;
            border: 2px solid transparent;
            border-radius: 12px;
            font-size: 1rem;
            background: var(--background-light);
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
        }

        .calendar-view {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 2px;
            background: var(--background-light);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-light);
        }

        .calendar-header {
            background: var(--primary-gradient);
            color: white;
            padding: 20px 10px;
            text-align: center;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .calendar-day {
            background: white;
            min-height: 140px;
            padding: 15px 10px;
            position: relative;
            transition: var(--transition);
            border-radius: 4px;
            cursor: pointer;
        }

        .calendar-day:hover {
            background: #f8f9fa;
            transform: scale(1.02);
            box-shadow: var(--shadow-light);
        }

        .day-number {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .course-item {
            background: var(--primary-gradient);
            color: white;
            padding: 8px;
            margin: 3px 0;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .course-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .list-view {
            display: none;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 25px;
        }

        .course-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            padding: 30px;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .course-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
        }

        .course-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-heavy);
        }

        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .course-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .course-subject {
            background: var(--primary-gradient);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .course-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .meta-icon {
            color: #667eea;
        }

        .course-actions {
            display: flex;
            gap: 10px;
        }

        .action-btn {
            flex: 1;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-secondary {
            background: var(--background-light);
            color: var(--text-primary);
            border: 2px solid #e9ecef;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        @media (max-width: 1024px) {
            .list-view {
                grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header, .main-content {
                padding: 25px;
            }

            .calendar-view {
                grid-template-columns: 1fr;
            }

            .calendar-day {
                min-height: auto;
                padding: 20px;
            }

            .list-view {
                grid-template-columns: 1fr;
            }

            .controls {
                flex-direction: column;
                align-items: stretch;
            }

            .view-toggle {
                justify-content: center;
            }

            .stats {
                grid-template-columns: 1fr;
            }
        }

        .floating-add {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            background: var(--accent-gradient);
            border: none;
            border-radius: 50%;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: var(--shadow-medium);
            transition: var(--transition);
            z-index: 1000;
        }

        .floating-add:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-heavy);
        }

        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-graduation-cap"></i> Mes Cours</h1>
            <div class="user-info">
                <span><i class="fas fa-user"></i> Utilisateur connecté</span>
                <span class="user-badge"><i class="fas fa-student"></i> Étudiant</span>
            </div>
        </div>

        <div class="main-content">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book"></i></div>
                    <div class="stat-number" id="totalCours">12</div>
                    <div class="stat-label">Cours au total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                    <div class="stat-number" id="coursSemaine">8</div>
                    <div class="stat-label">Cette semaine</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-number" id="coursAujourdhui">3</div>
                    <div class="stat-label">Aujourd'hui</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-number" id="coursCompletes">5</div>
                    <div class="stat-label">Complétés</div>
                </div>
            </div>

            <div class="controls">
                <div class="view-toggle">
                    <button class="toggle-btn active" onclick="showCalendar()">
                        <i class="fas fa-calendar-alt"></i> Calendrier
                    </button>
                    <button class="toggle-btn" onclick="showList()">
                        <i class="fas fa-list"></i> Liste
                    </button>
                </div>
                
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Rechercher un cours..." id="searchInput">
                    <i class="fas fa-search search-icon"></i>
                </div>
            </div>

            <div id="calendarView" class="calendar-view">
                <div class="calendar-header">Lun</div>
                <div class="calendar-header">Mar</div>
                <div class="calendar-header">Mer</div>
                <div class="calendar-header">Jeu</div>
                <div class="calendar-header">Ven</div>
                <div class="calendar-header">Sam</div>
                <div class="calendar-header">Dim</div>
            </div>

            <div id="listView" class="list-view">
                <!-- Les cours seront chargés ici dynamiquement -->
            </div>
        </div>
    </div>

    <button class="floating-add" onclick="addNewCourse()">
        <i class="fas fa-plus"></i>
    </button>

    <script>
        // Données des cours enrichies
        const coursData = [
            {
                id: 1,
                intitule: "Algèbre linéaire avancée",
                date: "2024-03-10",
                plateforme: "Moodle",
                matiere: "Mathématiques",
                prof: "Prof. Durand",
                duree: "2h30",
                salle: "A301",
                description: "Étude des espaces vectoriels et transformations linéaires"
            },
            {
                id: 2,
                intitule: "Analyse mathématique",
                date: "2024-04-15",
                plateforme: "Google Classroom",
                matiere: "Mathématiques",
                prof: "Prof. Martin",
                duree: "2h",
                salle: "B205",
                description: "Limites, dérivées et intégrales"
            },
            {
                id: 3,
                intitule: "Mécanique classique",
                date: "2024-05-20",
                plateforme: "Google Classroom",
                matiere: "Physique",
                prof: "Prof. Dubois",
                duree: "3h",
                salle: "Lab 1",
                description: "Cinématique et dynamique des systèmes"
            },
            {
                id: 4,
                intitule: "Physique quantique",
                date: "2024-06-25",
                plateforme: "EdX",
                matiere: "Physique",
                prof: "Prof. Leroy",
                duree: "2h15",
                salle: "C102",
                description: "Introduction aux principes quantiques"
            },
            {
                id: 5,
                intitule: "Chimie organique",
                date: "2024-07-12",
                plateforme: "Khan Academy",
                matiere: "Chimie",
                prof: "Prof. Leroy",
                duree: "2h45",
                salle: "Lab 2",
                description: "Réactions et mécanismes organiques"
            },
            {
                id: 6,
                intitule: "Programmation Python",
                date: "2024-08-05",
                plateforme: "Udemy",
                matiere: "Informatique",
                prof: "Prof. Morel",
                duree: "4h",
                salle: "Info 1",
                description: "Développement d'applications Python"
            }
        ];

        function initializeCalendar() {
            const calendarView = document.getElementById('calendarView');
            // Conserver les en-têtes
            const headers = calendarView.querySelectorAll('.calendar-header');
            calendarView.innerHTML = '';
            headers.forEach(header => calendarView.appendChild(header));

            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();
            
            const firstDay = new Date(currentYear, currentMonth, 1).getDay();
            const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
            
            // Ajouter les jours vides du début
            for (let i = 0; i < (firstDay === 0 ? 6 : firstDay - 1); i++) {
                const emptyDay = document.createElement('div');
                emptyDay.className = 'calendar-day';
                calendarView.appendChild(emptyDay);
            }
            
            // Ajouter les jours du mois
            for (let day = 1; day <= daysInMonth; day++) {
                const dayDiv = document.createElement('div');
                dayDiv.className = 'calendar-day fade-in';
                
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
                    courseDiv.innerHTML = `<i class="fas fa-book"></i> ${cours.intitule.substring(0, 12)}...`;
                    courseDiv.title = `${cours.intitule} - ${cours.prof}`;
                    dayDiv.appendChild(courseDiv);
                });
                
                // Marquer aujourd'hui
                if (day === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear()) {
                    dayDiv.style.background = 'linear-gradient(135deg, #667eea22, #764ba222)';
                    dayDiv.style.border = '2px solid #667eea';
                }
                
                calendarView.appendChild(dayDiv);
            }
        }

        function loadCoursList() {
            const listView = document.getElementById('listView');
            listView.innerHTML = '';
            
            coursData.forEach((cours, index) => {
                const courseCard = document.createElement('div');
                courseCard.className = `course-card fade-in`;
                courseCard.style.animationDelay = `${index * 0.1}s`;
                
                courseCard.innerHTML = `
                    <div class="course-header">
                        <div class="course-title">${cours.intitule}</div>
                        <div class="course-subject">${cours.matiere}</div>
                    </div>
                    
                    <div class="course-meta">
                        <div class="meta-item">
                            <i class="fas fa-calendar meta-icon"></i>
                            ${formatDate(cours.date)}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock meta-icon"></i>
                            ${cours.duree}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt meta-icon"></i>
                            ${cours.salle}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-user-tie meta-icon"></i>
                            ${cours.prof}
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-laptop meta-icon"></i>
                            ${cours.plateforme}
                        </div>
                    </div>
                    
                    <p style="color: var(--text-secondary); margin-bottom: 20px; font-style: italic;">
                        ${cours.description}
                    </p>
                    
                    <div class="course-actions">
                        <button class="action-btn btn-primary">
                            <i class="fas fa-play"></i> Accéder
                        </button>
                        <button class="action-btn btn-secondary">
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
                weekday: 'short',
                day: 'numeric',
                month: 'short'
            });
        }

        function showCalendar() {
            document.getElementById('calendarView').style.display = 'grid';
            document.getElementById('listView').style.display = 'none';
            document.querySelectorAll('.toggle-btn')[0].classList.add('active');
            document.querySelectorAll('.toggle-btn')[1].classList.remove('active');
        }

        function showList() {
            document.getElementById('calendarView').style.display = 'none';
            document.getElementById('listView').style.display = 'grid';
            document.querySelectorAll('.toggle-btn')[0].classList.remove('active');
            document.querySelectorAll('.toggle-btn')[1].classList.add('active');
        }

        function addNewCourse() {
            alert('Fonctionnalité d\'ajout de cours à implémenter');
        }

        // Recherche en temps réel
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const courseCards = document.querySelectorAll('.course-card');
            
            courseCards.forEach(card => {
                const title = card.querySelector('.course-title').textContent.toLowerCase();
                const subject = card.querySelector('.course-subject').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || subject.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            loadCoursList();
            
            // Animation d'entrée pour les stats
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.2}s`;
                card.classList.add('fade-in');
            });
        });
    </script>
    <?php include 'footer.php'; ?>
</body>
</html>