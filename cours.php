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
    $host = 'localhost';
$db   = 'ecole';
$user = 'colin';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$pdo = new PDO($dsn, $user, $pass, $options);
    
    // Récupérer les informations de l'utilisateur
    if ($user_type === 'etudiant') {
        $stmt = $pdo->prepare("SELECT nom, prenom, email FROM etudiants WHERE id_etudiant = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer les matières de l'étudiant
        $query = "
            SELECT 
                m.id_matiere,
                m.intitule,
                COUNT(c.id_cours) as cours_count,
                SUM(CASE WHEN c.date < CURDATE() THEN 1 ELSE 0 END) as completed_cours,
                SUM(TIME_TO_SEC(c.duree)/3600) as total_hours,
                'active' as status
            FROM matieres m
            INNER JOIN cours c ON m.id_matiere = c.id_matiere
            INNER JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
            WHERE ce.id_etudiant = ?
            GROUP BY m.id_matiere, m.intitule
            ORDER BY m.intitule
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
        $matieres_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } else if ($user_type === 'prof') {
        $stmt = $pdo->prepare("SELECT nom FROM profs WHERE id_prof = ?");
        $stmt->execute([$user_id]);
        $user_info = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Récupérer les matières du professeur
        $query = "
            SELECT 
                m.id_matiere,
                m.intitule,
                COUNT(c.id_cours) as cours_count,
                SUM(CASE WHEN c.date < CURDATE() THEN 1 ELSE 0 END) as completed_cours,
                SUM(TIME_TO_SEC(c.duree)/3600) as total_hours,
                'active' as status
            FROM matieres m
            INNER JOIN cours c ON m.id_matiere = c.id_matiere
            WHERE c.id_prof = ?
            GROUP BY m.id_matiere, m.intitule
            ORDER BY m.intitule
        ";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id]);
        $matieres_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Calculer les statistiques globales
    $total_matieres = count($matieres_data);
    $matieres_actives = 0;
    $matieres_completes = 0;
    
    foreach ($matieres_data as $matiere) {
        if ($matiere['completed_cours'] == $matiere['cours_count'] && $matiere['cours_count'] > 0) {
            $matieres_completes++;
        } else {
            $matieres_actives++;
        }
    }
    
} catch (PDOException $e) {
    die("Erreur lors de la récupération des données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Matières - École</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
  <?php include 'header.php'; ?>
 
    <style>
        :root {
            --primary: #6c5ce7;
            --secondary: #a29bfe;
            --success: #00b894;
            --warning: #fdcb6e;
            --danger: #ff7675;
            --light: #f8f9fa;
            --dark: #2d3436;
            --gray: #636e72;
            --light-gray: #dfe6e9;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            --hover-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--dark);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 25px 30px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            background: linear-gradient(135deg, #6c5ce7 0%, #a29bfe 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.2;
        }

        .header h1 {
            font-size: 2.4rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 5px;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.2);
            padding: 12px 20px;
            border-radius: 15px;
            backdrop-filter: blur(5px);
        }

        .user-info span {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            height: 8px;
            width: 100%;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
        }

        .stat-card.total { --color-start: #6c5ce7; --color-end: #a29bfe; }
        .stat-card.active { --color-start: #00b894; --color-end: #55efc4; }
        .stat-card.completed { --color-start: #fdcb6e; --color-end: #ffeaa7; }

        .stat-number {
            font-size: 3.2rem;
            font-weight: 800;
            margin: 15px 0 10px;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .stat-label {
            color: var(--gray);
            font-weight: 500;
            font-size: 1.1rem;
        }

        .stat-icon {
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--color-start), var(--color-end));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .search-filter {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 35px;
            gap: 25px;
            flex-wrap: wrap;
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 18px 25px 18px 60px;
            border: 2px solid var(--light-gray);
            border-radius: 50px;
            font-size: 1.1rem;
            transition: var(--transition);
            background: var(--light);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.15);
        }

        .search-icon {
            position: absolute;
            left: 25px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 1.4rem;
        }

        .filter-select {
            padding: 17px 25px;
            border: 2px solid var(--light-gray);
            border-radius: 50px;
            font-size: 1.1rem;
            background: var(--light);
            cursor: pointer;
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23636e72' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 20px center;
            background-size: 18px;
            padding-right: 60px;
        }

        .filter-select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(108, 92, 231, 0.15);
        }

        .matieres-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .matiere-card {
            background: white;
            border-radius: 25px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            border-top: 8px solid;
        }

        .matiere-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--hover-shadow);
        }

        .matiere-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
        }

        .matiere-card.mathematiques { --color-start: #ff6b6b; --color-end: #ee5a24; border-top-color: #ff6b6b; }
        .matiere-card.physique { --color-start: #4ecdc4; --color-end: #44a08d; border-top-color: #4ecdc4; }
        .matiere-card.chimie { --color-start: #45b7d1; --color-end: #96c93d; border-top-color: #45b7d1; }
        .matiere-card.informatique { --color-start: #96ceb4; --color-end: #ffeaa7; border-top-color: #96ceb4; }
        .matiere-card.litterature { --color-start: #ffeaa7; --color-end: #fab1a0; border-top-color: #ffeaa7; }
        .matiere-card.histoire { --color-start: #fd79a8; --color-end: #fdcb6e; border-top-color: #fd79a8; }
        .matiere-card.philosophie { --color-start: #a29bfe; --color-end: #6c5ce7; border-top-color: #a29bfe; }
        .matiere-card.economie { --color-start: #fd8c00; --color-end: #ff7675; border-top-color: #fd8c00; }
        .matiere-card.biologie { --color-start: #00b894; --color-end: #55a3ff; border-top-color: #00b894; }
        .matiere-card.droit { --color-start: #636e72; --color-end: #4a4a4a; border-top-color: #636e72; }

        .matiere-header {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
            gap: 18px;
        }

        .matiere-icon {
            font-size: 2.8rem;
            background: linear-gradient(135deg, var(--color-start), var(--color-end));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            flex-shrink: 0;
        }

        .matiere-title {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--dark);
            line-height: 1.3;
        }

        .matiere-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 25px;
            padding: 20px;
            background: rgba(240, 242, 245, 0.6);
            border-radius: 18px;
            backdrop-filter: blur(5px);
        }

        .matiere-stat {
            text-align: center;
            padding: 10px;
        }

        .matiere-stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 5px;
        }

        .matiere-stat-label {
            font-size: 0.95rem;
            color: var(--gray);
            font-weight: 500;
        }

        .progress-container {
            margin-bottom: 25px;
        }

        .progress-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: var(--gray);
            font-weight: 500;
        }

        .progress-bar {
            width: 100%;
            height: 12px;
            background: var(--light-gray);
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--color-start), var(--color-end));
            border-radius: 10px;
            transition: width 0.8s ease;
            position: relative;
        }

        .progress-fill::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 2s infinite;
        }

        .matiere-actions {
            display: flex;
            gap: 15px;
            margin-top: auto;
            padding-top: 15px;
        }

        .action-btn {
            flex: 1;
            padding: 16px;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-weight: 600;
            font-size: 1.05rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .btn-secondary {
            background: white;
            color: var(--dark);
            border: 2px solid var(--light-gray);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(108, 92, 231, 0.35);
        }

        .btn-secondary:hover {
            background: var(--light);
            border-color: var(--primary);
            color: var(--primary);
        }

        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 8px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            z-index: 2;
        }

        .status-active {
            background: rgba(0, 184, 148, 0.15);
            color: #00b894;
        }

        .status-completed {
            background: rgba(253, 203, 110, 0.2);
            color: #e17055;
        }

        .no-results {
            grid-column: 1 / -1;
            text-align: center;
            padding: 50px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
        }

        .no-results i {
            font-size: 4rem;
            color: #dfe6e9;
            margin-bottom: 20px;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        @media (max-width: 1200px) {
            .matieres-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .user-info {
                align-items: flex-start;
            }
            
            .search-filter {
                flex-direction: column;
                align-items: stretch;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .matiere-card {
                padding: 25px;
            }
        }

        @media (max-width: 480px) {
            .matieres-grid {
                grid-template-columns: 1fr;
            }
            
            .matiere-stats {
                grid-template-columns: 1fr;
            }
            
            .header {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-book-open"></i> Mes Matières</h1>
            <div class="user-info">
                <span>
                    <i class="fas fa-user"></i>
                    <?php 
                    if ($user_type === 'etudiant') {
                        echo htmlspecialchars($user_info['prenom'] . ' ' . $user_info['nom']);
                    } else {
                        echo 'Prof. ' . htmlspecialchars($user_info['nom']);
                    }
                    ?>
                </span>
                <span>
                    <i class="fas fa-<?php echo $user_type === 'etudiant' ? 'user-graduate' : 'chalkboard-teacher'; ?>"></i>
                    <?php echo ucfirst($user_type); ?>
                </span>
            </div>
        </div>

        <div class="stats-overview">
            <div class="stat-card total">
                <div class="stat-icon"><i class="fas fa-book"></i></div>
                <div class="stat-number total" id="totalMatieres"><?php echo $total_matieres; ?></div>
                <div class="stat-label">Matières au total</div>
            </div>
            <div class="stat-card active">
                <div class="stat-icon"><i class="fas fa-running"></i></div>
                <div class="stat-number active" id="matieresActives"><?php echo $matieres_actives; ?></div>
                <div class="stat-label">Matières actives</div>
            </div>
            <div class="stat-card completed">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number completed" id="matieresCompletes"><?php echo $matieres_completes; ?></div>
                <div class="stat-label">Matières complétées</div>
            </div>
        </div>

        <div class="search-filter">
            <div class="search-box">
                <span class="search-icon"><i class="fas fa-search"></i></span>
                <input type="text" class="search-input" placeholder="Rechercher une matière..." id="searchInput">
            </div>
            <select class="filter-select" id="filterSelect">
                <option value="all">Toutes les matières</option>
                <option value="active">Matières actives</option>
                <option value="completed">Matières complétées</option>
            </select>
        </div>

        <div class="matieres-grid" id="matieresGrid">
            <?php if (empty($matieres_data)): ?>
                <div class="no-results">
                    <i class="fas fa-book"></i>
                    <h3>Aucune matière trouvée</h3>
                    <p>Vous n'avez pas encore de matières assignées.</p>
                </div>
            <?php else: ?>
                <?php foreach ($matieres_data as $matiere): ?>
                    <?php 
                    $progress = ($matiere['cours_count'] > 0) ? ($matiere['completed_cours'] / $matiere['cours_count']) * 100 : 0;
                    $statusClass = ($matiere['completed_cours'] == $matiere['cours_count'] && $matiere['cours_count'] > 0) ? 'status-completed' : 'status-active';
                    $statusText = ($matiere['completed_cours'] == $matiere['cours_count'] && $matiere['cours_count'] > 0) ? 'Terminée' : 'En cours';
                    $matiereClass = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $matiere['intitule']));
                    ?>
                    <div class="matiere-card <?php echo $matiereClass; ?>">
                        <div class="status-badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
                        <div class="matiere-header">
                            <div class="matiere-icon">
                                <?php 
                                // Icônes correspondant aux matières
                                $icons = [
                                    'mathématique' => '🧮',
                                    'physique' => '⚛️',
                                    'chimie' => '🧪',
                                    'informatique' => '💻',
                                    'littérature' => '📚',
                                    'histoire' => '🏛️',
                                    'philosophie' => '🤔',
                                    'économie' => '💰',
                                    'biologie' => '🧬',
                                    'droit' => '⚖️'
                                ];
                                
                                $icon = '📘'; // Icône par défaut
                                foreach ($icons as $key => $value) {
                                    if (stripos($matiere['intitule'], $key) !== false) {
                                        $icon = $value;
                                        break;
                                    }
                                }
                                echo $icon;
                                ?>
                            </div>
                            <div class="matiere-title"><?php echo htmlspecialchars($matiere['intitule']); ?></div>
                        </div>
                        
                        <div class="matiere-stats">
                            <div class="matiere-stat">
                                <div class="matiere-stat-number"><?php echo $matiere['cours_count']; ?></div>
                                <div class="matiere-stat-label">Cours</div>
                            </div>
                            <div class="matiere-stat">
                                <div class="matiere-stat-number"><?php echo $matiere['completed_cours']; ?></div>
                                <div class="matiere-stat-label">Complétés</div>
                            </div>
                            <div class="matiere-stat">
                                <div class="matiere-stat-number"><?php echo number_format($matiere['total_hours'], 1); ?>h</div>
                                <div class="matiere-stat-label">Total</div>
                            </div>
                        </div>
                        
                        <div class="progress-container">
                            <div class="progress-header">
                                <span>Progression</span>
                                <span><?php echo number_format($progress, 0); ?>%</span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?php echo $progress; ?>%"></div>
                            </div>
                        </div>
                        
                        <div class="matiere-actions">
                            <button class="action-btn btn-primary" onclick="accessMatiere(<?php echo $matiere['id_matiere']; ?>)">
                                <i class="fas fa-play-circle"></i> Continuer
                            </button>
                            <button class="action-btn btn-secondary" onclick="showMatiereDetails(<?php echo $matiere['id_matiere']; ?>)">
                                <i class="fas fa-info-circle"></i> Détails
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Fonction de filtrage des matières
        function filterMatieres() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const filterValue = document.getElementById('filterSelect').value;
            const matiereCards = document.querySelectorAll('.matiere-card');
            let visibleCount = 0;
            
            matiereCards.forEach(card => {
                const title = card.querySelector('.matiere-title').textContent.toLowerCase();
                const status = card.querySelector('.status-badge').textContent.toLowerCase();
                
                const matchesSearch = title.includes(searchTerm);
                const matchesFilter = filterValue === 'all' || 
                                     (filterValue === 'active' && status.includes('en cours')) ||
                                     (filterValue === 'completed' && status.includes('terminée'));
                
                if (matchesSearch && matchesFilter) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Afficher le message si aucun résultat
            const noResults = document.querySelector('.no-results');
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? 'block' : 'none';
            }
        }
        
        // Écouteurs d'événements
        document.getElementById('searchInput').addEventListener('input', filterMatieres);
        document.getElementById('filterSelect').addEventListener('change', filterMatieres);
        
        // Fonctions d'accès aux matières
        function accessMatiere(matiereId) {
            alert(`Accès à la matière ID: ${matiereId} - Cette fonctionnalité est en cours de développement`);
            // Redirection vers la page des cours de la matière
            // window.location.href = `cours.php?matiere=${matiereId}`;
        }
        
        function showMatiereDetails(matiereId) {
            alert(`Détails de la matière ID: ${matiereId} - Cette fonctionnalité est en cours de développement`);
            // Afficher une modale avec les détails de la matière
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            // Animation au chargement
            const cards = document.querySelectorAll('.matiere-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
    
<?php include 'footer.php'; ?>

</body>
</html>