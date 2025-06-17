<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Matières - École</title>
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
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a24 100%);
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

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border-left: 5px solid;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card.total { border-left-color: #6c5ce7; }
        .stat-card.active { border-left-color: #00b894; }
        .stat-card.completed { border-left-color: #fdcb6e; }

        .stat-number {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .stat-number.total { color: #6c5ce7; }
        .stat-number.active { color: #00b894; }
        .stat-number.completed { color: #fdcb6e; }

        .stat-label {
            color: #636e72;
            font-weight: 500;
        }

        .matieres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .matiere-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }

        .matiere-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
        }

        .matiere-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .matiere-card.mathematiques {
            --color-start: #ff6b6b;
            --color-end: #ee5a24;
        }

        .matiere-card.physique {
            --color-start: #4ecdc4;
            --color-end: #44a08d;
        }

        .matiere-card.chimie {
            --color-start: #45b7d1;
            --color-end: #96c93d;
        }

        .matiere-card.informatique {
            --color-start: #96ceb4;
            --color-end: #ffeaa7;
        }

        .matiere-card.litterature {
            --color-start: #ffeaa7;
            --color-end: #fab1a0;
        }

        .matiere-card.histoire {
            --color-start: #fd79a8;
            --color-end: #fdcb6e;
        }

        .matiere-card.philosophie {
            --color-start: #a29bfe;
            --color-end: #6c5ce7;
        }

        .matiere-card.economie {
            --color-start: #fd8c00;
            --color-end: #ff7675;
        }

        .matiere-card.biologie {
            --color-start: #00b894;
            --color-end: #55a3ff;
        }

        .matiere-card.droit {
            --color-start: #636e72;
            --color-end: #4a4a4a;
        }

        .matiere-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .matiere-icon {
            font-size: 2.5em;
            margin-right: 15px;
            opacity: 0.8;
        }

        .matiere-title {
            font-size: 1.4em;
            font-weight: 600;
            color: #2d3436;
        }

        .matiere-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .matiere-stat {
            text-align: center;
        }

        .matiere-stat-number {
            font-size: 1.5em;
            font-weight: 700;
            color: #2d3436;
        }

        .matiere-stat-label {
            font-size: 0.8em;
            color: #636e72;
            margin-top: 2px;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        .progress-text {
            font-size: 0.9em;
            color: #636e72;
            text-align: center;
        }

        .matiere-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .action-btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: #f8f9fa;
            color: #495057;
            border: 2px solid #e9ecef;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary:hover {
            background: #e9ecef;
        }

        .search-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 250px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1em;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #636e72;
            font-size: 1.2em;
        }

        .filter-select {
            padding: 15px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 1em;
            background: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-select:focus {
            outline: none;
            border-color: #667eea;
        }

        @media (max-width: 768px) {
            .matieres-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
            
            .search-filter {
                flex-direction: column;
                align-items: stretch;
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
            <h1>📚 Mes Matières</h1>
            <div class="user-info">
                <span id="userName">Utilisateur connecté</span> • 
                <span id="userType">Étudiant</span>
            </div>
        </div>

        <div class="main-content">
            <div class="stats-overview">
                <div class="stat-card total">
                    <div class="stat-number total" id="totalMatieres">10</div>
                    <div class="stat-label">Matières au total</div>
                </div>
                <div class="stat-card active">
                    <div class="stat-number active" id="matieresActives">8</div>
                    <div class="stat-label">Matières actives</div>
                </div>
                <div class="stat-card completed">
                    <div class="stat-number completed" id="matieresCompletes">2</div>
                    <div class="stat-label">Matières complétées</div>
                </div>
            </div>

            <div class="search-filter">
                <div class="search-box">
                    <span class="search-icon">🔍</span>
                    <input type="text" class="search-input" placeholder="Rechercher une matière..." id="searchInput">
                </div>
                <select class="filter-select" id="filterSelect">
                    <option value="all">Toutes les matières</option>
                    <option value="active">Matières actives</option>
                    <option value="completed">Matières complétées</option>
                </select>
            </div>

            <div class="matieres-grid" id="matieresGrid">
                <!-- Les matières seront chargées ici dynamiquement -->
            </div>
        </div>
    </div>

    <script>
        // Simulation des données de matières basées sur votre base de données
        const matieresData = [
            {
                id: 1,
                intitule: "Mathématiques",
                icon: "🔢",
                coursCount: 3,
                completedCours: 2,
                totalHours: 45,
                status: "active"
            },
            {
                id: 2,
                intitule: "Physique",
                icon: "⚛️",
                coursCount: 2,
                completedCours: 1,
                totalHours: 32,
                status: "active"
            },
            {
                id: 3,
                intitule: "Chimie",
                icon: "🧪",
                coursCount: 1,
                completedCours: 1,
                totalHours