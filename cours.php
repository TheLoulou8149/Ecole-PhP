<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Cours - École</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            padding: 30px;
            text-align: center;
            color: white;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .user-info {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .main-content {
            padding: 30px;
        }

        .view-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
            gap: 10px;
        }

        .toggle-btn {
            padding: 12px 24px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            background: #f8f9fa;
            color: #666;
        }

        .toggle-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .calendar-view {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #e9ecef;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 30px;
        }

        .calendar-header {
            background: #495057;
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: 600;
        }

        .calendar-day {
            background: white;
            min-height: 120px;
            padding: 10px;
            position: relative;
            transition: all 0.3s ease;
        }

        .calendar-day:hover {
            background: #f8f9fa;
            transform: scale(1.02);
        }

        .day-number {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .course-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px;
            margin: 2px 0;
            border-radius: 8px;
            font-size: 0.8em;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .course-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .list-view {
            display: none;
        }

        .course-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            border-left: 5px solid;
            transition: all 0.3s ease;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .course-card.mathematiques { border-left-color: #ff6b6b; }
        .course-card.physique { border-left-color: #4ecdc4; }
        .course-card.chimie { border-left-color: #45b7d1; }
        .course-card.informatique { border-left-color: #96ceb4; }
        .course-card.litterature { border-left-color: #ffeaa7; }
        .course-card.histoire { border-left-color: #fd79a8; }
        .course-card.philosophie { border-left-color: #a29bfe; }
        .course-card.economie { border-left-color: #fd8c00; }
        .course-card.biologie { border-left-color: #00b894; }

        .course-title {
            font-size: 1.4em;
            font-weight: 600;
            color: #2d3436;
            margin-bottom: 10px;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
        }

        .course-date {
            background: #e17055;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .course-platform {
            background: #74b9ff;
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .course-prof {
            color: #636e72;
            font-style: italic;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-label {
            color: #636e72;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .calendar-view {
                grid-template-columns: 1fr;
            }
            
            .course-meta {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📚 Mes Cours</h1>
            <div class="user-info">
                <span id="userName">Utilisateur connecté</span> • 
                <span id="userType">Étudiant</span>
            </div>
        </div>

        <div class="main-content">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number" id="totalCours">12</div>
                    <div class="stat-label">Cours au total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="coursSemaine">8</div>
                    <div class="stat-label">Cette semaine</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="coursAujourdhui">3</div>
                    <div class="stat-label">Aujourd'hui</div>
                </div>
            </div>

            <div class="view-toggle">
                <button class="toggle-btn active" onclick="showCalendar()">📅 Vue Calendrier</button>
                <button class="toggle-btn" onclick="showList()">📋 Vue Liste</button>
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

    <script>
        // Simulation des données de cours basées sur votre base de données
        const coursData = [
            {
                id: 1,
                intitule: "Algèbre linéaire",
                date: "2024-03-10",
                plateforme: "Moodle",
                matiere: "Mathématiques",
                prof: "Durand"
            },
            {
                id: 2,
                intitule: "Analyse mathématique",
                date: "2024-04-15",
                plateforme: "Google Classroom",
                matiere: "Mathématiques",
                prof: "Martin"
            },
            {
                id: 3,
                intitule: "Mécanique classique",
                date: "2024-05-20",
                plateforme: "Google Classroom",
                matiere: "Physique",
                prof: "Dubois"
            },
            {
                id: 4,
                intitule: "Physique quantique",
                date: "2024-06-25",
                plateforme: "EdX",
                matiere: "Physique",
                prof: "Leroy"
            },
            {
                id: 5,
                intitule: "Chimie organique",
                date: "2024-07-12",
                plateforme: "Khan Academy",
                matiere: "Chimie",
                prof: "Leroy"
            },
            {
                id: 6,
                intitule: "Programmation Python",
                date: "2024-08-05",
                plateforme: "Udemy",
                matiere: "Informatique",
                prof: "Morel"
            },
            {
                id: 7,
                intitule: "Analyse littéraire",
                date: "2024-09-18",
                plateforme: "Coursera",
                matiere: "Littérature",
                prof: "Rousseau"
            },
            {
                id: 8,
                intitule: "Histoire médiévale",
                date: "2024-10-22",
                plateforme: "EdX",
                matiere: "Histoire",
                prof: "Lemoine"
            },
            {
                id: 9,
                intitule: "Introduction à la philosophie",
                date: "2024-11-11",
                plateforme: "FutureLearn",
                matiere: "Philosophie",
                prof: "Rousseau"
            },
            {
                id: 10,
                intitule: "Économie politique",
                date: "2024-12-01",
                plateforme: "OpenClassrooms",
                matiere: "Économie",
                prof: "Lemoine"
            },
            {
                id: 11,
                intitule: "Biologie cellulaire",
                date: "2025-01-07",
                plateforme: "EdX",
                matiere: "Biologie",
                prof: "Durand"
            },
            {
                id: 12,
                intitule: "Droit international",
                date: "2025-02-14",
                plateforme: "Google Classroom",
                matiere: "Droit",
                prof: "Fournier"
            }
        ];

        function initializeCalendar() {
            const calendarView = document.getElementById('calendarView');
            const today = new Date();
            const currentMonth = today.getMonth();
            const currentYear = today.getFullYear();
            
            // Obtenir le premier jour du mois et le nombre de jours
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
                dayDiv.className = 'calendar-day';
                
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
                    courseDiv.textContent = cours.intitule.substring(0, 15) + '...';
                    courseDiv.title = cours.intitule;
                    dayDiv.appendChild(courseDiv);
                });
                
                calendarView.appendChild(dayDiv);
            }
        }

        function loadCoursList() {
            const listView = document.getElementById('listView');
            listView.innerHTML = '';
            
            coursData.forEach(cours => {
                const courseCard = document.createElement('div');
                courseCard.className = `course-card ${cours.matiere.toLowerCase()}`;
                
                courseCard.innerHTML = `
                    <div class="course-title">${cours.intitule}</div>
                    <div class="course-meta">
                        <div class="course-date">📅 ${formatDate(cours.date)}</div>
                        <div class="course-platform">💻 ${cours.plateforme}</div>
                        <div class="course-prof">👨‍🏫 Prof. ${cours.prof}</div>
                    </div>
                    <div style="color: #636e72; font-weight: 500;">📚 ${cours.matiere}</div>
                `;
                
                listView.appendChild(courseCard);
            });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('fr-FR', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
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
            document.getElementById('listView').style.display = 'block';
            document.querySelectorAll('.toggle-btn')[0].classList.remove('active');
            document.querySelectorAll('.toggle-btn')[1].classList.add('active');
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();
            loadCoursList();
        });
    </script>
</body>
</html>