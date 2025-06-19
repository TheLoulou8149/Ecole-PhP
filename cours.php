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
'Vous' AS prof // Colonne ajoutée ici
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

et profil.php :
<?php
// Démarrer la session : doit être la toute première instruction
session_start();

// Vérifier si l'utilisateur est connecté AVANT d'inclure le header
if (empty($_SESSION['user_type']) || empty($_SESSION['user_id'])) {
header('Location: login.php');
exit();
}

// Vérifier les types d'utilisateur possibles (plus flexible)
$valid_user_types = ['etudiant', 'prof', 'professeur', 'teacher', 'student'];
if (!in_array(strtolower($_SESSION['user_type']), $valid_user_types)) {
header('Location: login.php');
exit();
}

// Inclure la config
require_once 'config.php';

// Vérifier la fonction de connexion
if (!function_exists('getDBConnection')) {
die("Erreur : La fonction getDBConnection() est absente du fichier config.php.");
}

$pdo = getDBConnection();
if (!$pdo instanceof PDO) {
die("Erreur : La connexion à la base de données a échoué.");
}

// Récupérer l'ID utilisateur et le type depuis la session
$user_id = (int) $_SESSION['user_id'];
$user_type = strtolower($_SESSION['user_type']);

// Normaliser le type d'utilisateur pour la logique
if (in_array($user_type, ['professeur', 'teacher'])) {
$user_type = 'prof';
} elseif (in_array($user_type, ['student'])) {
$user_type = 'etudiant';
}

// Variables pour l'affichage
$user_data = [];
$cours = [];
$success_message = '';
$error_message = '';

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
try {
if ($user_type === 'etudiant') {
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$date_naissance = $_POST['date_naissance'] ?? '';
$nouveau_password = trim($_POST['nouveau_password'] ?? '');

// Validation basique
if (empty($nom) || empty($prenom) || empty($email)) {
throw new Exception("Tous les champs obligatoires doivent être remplis.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
throw new Exception("L'adresse email n'est pas valide.");
}

// Vérifier si l'email existe déjà pour un autre utilisateur
$stmt = $pdo->prepare("SELECT id_etudiant FROM etudiants WHERE email = ? AND id_etudiant != ?");
$stmt->execute([$email, $user_id]);
if ($stmt->fetch()) {
throw new Exception("Cette adresse email est déjà utilisée par un autre compte.");
}

// Préparer la requête de mise à jour
if (!empty($nouveau_password)) {
$password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ?, date_naissance = ?, password = ? WHERE id_etudiant = ?");
$stmt->execute([$nom, $prenom, $email, $date_naissance, $password_hash, $user_id]);
} else {
$stmt = $pdo->prepare("UPDATE etudiants SET nom = ?, prenom = ?, email = ?, date_naissance = ? WHERE id_etudiant = ?");
$stmt->execute([$nom, $prenom, $email, $date_naissance, $user_id]);
}

} else { // Professeur
$nom = trim($_POST['nom'] ?? '');
$email = trim($_POST['email'] ?? '');
$specialite = trim($_POST['specialite'] ?? '');
$nouveau_password = trim($_POST['nouveau_password'] ?? '');

if (empty($nom) || empty($email)) {
throw new Exception("Le nom et l'email sont obligatoires.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
throw new Exception("L'adresse email n'est pas valide.");
}

// Vérifier si l'email existe déjà pour un autre utilisateur
$stmt = $pdo->prepare("SELECT id_prof FROM profs WHERE email = ? AND id_prof != ?");
$stmt->execute([$email, $user_id]);
if ($stmt->fetch()) {
throw new Exception("Cette adresse email est déjà utilisée par un autre compte.");
}

// Préparer la requête de mise à jour
if (!empty($nouveau_password)) {
$password_hash = password_hash($nouveau_password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE profs SET nom = ?, email = ?, specialite = ?, password = ? WHERE id_prof = ?");
$stmt->execute([$nom, $email, $specialite, $password_hash, $user_id]);
} else {
$stmt = $pdo->prepare("UPDATE profs SET nom = ?, email = ?, specialite = ? WHERE id_prof = ?");
$stmt->execute([$nom, $email, $specialite, $user_id]);
}
}

$success_message = "Vos informations ont été mises à jour avec succès !";

} catch (Exception $e) {
$error_message = $e->getMessage();
}
}

// Récupération des données utilisateur et cours (code existant)
try {
if ($user_type === 'etudiant') {
$stmt = $pdo->prepare("SELECT * FROM etudiants WHERE id_etudiant = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
die("Étudiant non trouvé pour l'ID: $user_id");
}

$stmt = $pdo->prepare("
SELECT c.*, m.intitule AS matiere_nom, p.nom AS prof_nom
FROM cours c
INNER JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
INNER JOIN matieres m ON c.id_matiere = m.id_matiere
INNER JOIN profs p ON c.id_prof = p.id_prof
WHERE ce.id_etudiant = ?
ORDER BY c.date DESC
");
$stmt->execute([$user_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($user_type === 'prof') {
$stmt = $pdo->prepare("SELECT * FROM profs WHERE id_prof = ?");
$stmt->execute([$user_id]);
$user_data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user_data) {
die("Professeur non trouvé pour l'ID: $user_id");
}

$stmt = $pdo->prepare("
SELECT c.*, m.intitule AS matiere_nom,
COUNT(ce.id_etudiant) as nombre_etudiants
FROM cours c
INNER JOIN matieres m ON c.id_matiere = m.id_matiere
LEFT JOIN cours_etudiants ce ON c.id_cours = ce.id_cours
WHERE c.id_prof = ?
GROUP BY c.id_cours
ORDER BY c.date DESC
");
$stmt->execute([$user_id]);
$cours = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

} catch (PDOException $e) {
die("Erreur : " . $e->getMessage());
}

// Fonctions utilitaires (code existant)
function getFullName($user_data, $user_type) {
if ($user_type === 'etudiant') {
return $user_data['prenom'] . ' ' . $user_data['nom'];
} else {
return $user_data['nom'];
}
}

function getInitials($user_data, $user_type) {
if ($user_type === 'etudiant') {
return strtoupper(substr($user_data['prenom'], 0, 1) . substr($user_data['nom'], 0, 1));
} else {
$nom_parts = explode(' ', $user_data['nom']);
if (count($nom_parts) >= 2) {
return strtoupper(substr($nom_parts[0], 0, 1) . substr($nom_parts[1], 0, 1));
} else {
return strtoupper(substr($user_data['nom'], 0, 2));
}
}
}

require_once 'header.php';
?>

<style>
* {
margin: 0;
padding: 0;
box-sizing: border-box;
}

body {
font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #8b5cf6 100%);
min-height: 100vh;
padding-top: 0;
}

.main-content {
padding: 20px;
margin-bottom: 40px;
}

.container {
max-width: 1200px;
margin: 0 auto;
}

/* Messages de notification */
.alert {
padding: 16px 20px;
border-radius: 12px;
margin-bottom: 20px;
font-weight: 500;
display: flex;
align-items: center;
gap: 10px;
}

.alert-success {
background: rgba(16, 185, 129, 0.15);
color: #10b981;
border: 1px solid rgba(16, 185, 129, 0.3);
}

.alert-error {
background: rgba(239, 68, 68, 0.15);
color: #ef4444;
border: 1px solid rgba(239, 68, 68, 0.3);
}

.welcome-section {
background: rgba(255, 255, 255, 0.15);
backdrop-filter: blur(20px);
border-radius: 24px;
padding: 40px;
margin-bottom: 30px;
text-align: center;
color: white;
border: 1px solid rgba(255, 255, 255, 0.2);
}

.profile-avatar {
width: 100px;
height: 100px;
border-radius: 50%;
background: rgba(255, 255, 255, 0.25);
display: flex;
align-items: center;
justify-content: center;
font-size: 36px;
font-weight: 700;
margin: 0 auto 20px;
border: 3px solid rgba(255, 255, 255, 0.3);
}

.welcome-title {
font-size: 2.5rem;
font-weight: 700;
margin-bottom: 12px;
text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.welcome-subtitle {
font-size: 1.2rem;
opacity: 0.9;
font-weight: 400;
}

.user-type-badge {
display: inline-block;
background: linear-gradient(135deg, #10b981 0%, #059669 100%);
color: white;
padding: 8px 16px;
border-radius: 12px;
font-size: 0.9rem;
font-weight: 600;
text-transform: uppercase;
letter-spacing: 0.5px;
margin-top: 10px;
}

.profile-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
gap: 24px;
margin-bottom: 30px;
}

.profile-card {
background: rgba(255, 255, 255, 0.15);
backdrop-filter: blur(20px);
border-radius: 24px;
padding: 32px;
border: 1px solid rgba(255, 255, 255, 0.2);
transition: all 0.3s ease;
}

.profile-card:hover {
transform: translateY(-4px);
box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
background: rgba(255, 255, 255, 0.2);
}

.card-icon {
width: 60px;
height: 60px;
border-radius: 16px;
background: rgba(255, 255, 255, 0.25);
display: flex;
align-items: center;
justify-content: center;
margin-bottom: 20px;
font-size: 24px;
}

.card-title {
font-size: 1.4rem;
font-weight: 700;
color: white;
margin-bottom: 16px;
display: flex;
justify-content: space-between;
align-items: center;
}

.edit-btn {
background: rgba(255, 255, 255, 0.2);
color: white;
border: 1px solid rgba(255, 255, 255, 0.3);
padding: 8px 12px;
border-radius: 8px;
font-size: 0.8rem;
font-weight: 500;
cursor: pointer;
transition: all 0.2s ease;
text-decoration: none;
display: inline-flex;
align-items: center;
gap: 5px;
}

.edit-btn:hover {
background: rgba(255, 255, 255, 0.3);
transform: translateY(-1px);
}

.info-item {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 12px;
padding: 8px 0;
border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:last-child {
border-bottom: none;
margin-bottom: 0;
}

.info-label {
font-weight: 500;
color: rgba(255, 255, 255, 0.8);
font-size: 0.95rem;
}

.info-value {
font-weight: 600;
color: white;
font-size: 1rem;
}

/* Modal de modification */
.modal-overlay {
position: fixed;
top: 0;
left: 0;
width: 100%;
height: 100%;
background: rgba(0, 0, 0, 0.7);
backdrop-filter: blur(5px);
z-index: 1000;
display: none;
align-items: center;
justify-content: center;
}

.modal-overlay.active {
display: flex;
}

.modal {
background: rgba(255, 255, 255, 0.95);
backdrop-filter: blur(20px);
border-radius: 20px;
padding: 40px;
max-width: 500px;
width: 90%;
max-height: 80vh;
overflow-y: auto;
border: 1px solid rgba(255, 255, 255, 0.3);
}

.modal-header {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 30px;
}

.modal-title {
font-size: 1.5rem;
font-weight: 700;
color: #374151;
}

.close-btn {
background: none;
border: none;
font-size: 1.5rem;
cursor: pointer;
color: #6b7280;
padding: 5px;
border-radius: 50%;
transition: background 0.2s ease;
}

.close-btn:hover {
background: rgba(0, 0, 0, 0.1);
}

.form-group {
margin-bottom: 20px;
}

.form-label {
display: block;
font-weight: 600;
color: #374151;
margin-bottom: 8px;
font-size: 0.9rem;
}

.form-input {
width: 100%;
padding: 12px 16px;
border: 2px solid rgba(0, 0, 0, 0.1);
border-radius: 10px;
font-size: 1rem;
transition: all 0.2s ease;
background: rgba(255, 255, 255, 0.8);
}

.form-input:focus {
outline: none;
border-color: #7c3aed;
background: white;
}

.password-note {
font-size: 0.8rem;
color: #6b7280;
margin-top: 4px;
}

.form-actions {
display: flex;
gap: 12px;
justify-content: flex-end;
margin-top: 30px;
}

.btn {
padding: 12px 24px;
border: none;
border-radius: 10px;
font-weight: 600;
cursor: pointer;
transition: all 0.2s ease;
font-size: 0.95rem;
}

.btn-primary {
background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
color: white;
}

.btn-primary:hover {
transform: translateY(-1px);
box-shadow: 0 5px 15px rgba(124, 58, 237, 0.3);
}

.btn-secondary {
background: rgba(0, 0, 0, 0.1);
color: #374151;
}

.btn-secondary:hover {
background: rgba(0, 0, 0, 0.2);
}

/* Styles existants pour les cours */
.courses-section {
margin-top: 30px;
margin-bottom: 60px;
}

.section-header {
display: flex;
align-items: center;
margin-bottom: 24px;
}

.section-icon {
width: 50px;
height: 50px;
border-radius: 12px;
background: rgba(255, 255, 255, 0.2);
display: flex;
align-items: center;
justify-content: center;
margin-right: 16px;
font-size: 20px;
}

.section-title {
font-size: 1.8rem;
font-weight: 700;
color: white;
}

.courses-grid {
display: grid;
grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
gap: 20px;
}

.course-card {
background: rgba(255, 255, 255, 0.15);
backdrop-filter: blur(20px);
border-radius: 20px;
padding: 24px;
border: 1px solid rgba(255, 255, 255, 0.2);
transition: all 0.3s ease;
}

.course-card:hover {
transform: translateY(-2px);
box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
background: rgba(255, 255, 255, 0.2);
}

.course-header {
display: flex;
justify-content: space-between;
align-items: flex-start;
margin-bottom: 16px;
}

.course-title {
font-size: 1.2rem;
font-weight: 700;
color: white;
line-height: 1.3;
flex: 1;
margin-right: 12px;
}

.platform-badge {
background: linear-gradient(135deg, #10b981 0%, #059669 100%);
color: white;
padding: 6px 12px;
border-radius: 12px;
font-size: 0.8rem;
font-weight: 600;
text-transform: uppercase;
letter-spacing: 0.5px;
}

.course-details {
space-y: 8px;
}

.course-detail {
display: flex;
justify-content: space-between;
align-items: center;
margin-bottom: 8px;
font-size: 0.95rem;
}

.course-detail-label {
color: rgba(255, 255, 255, 0.8);
font-weight: 500;
}

.course-detail-value {
color: white;
font-weight: 600;
}

.no-courses {
text-align: center;
background: rgba(255, 255, 255, 0.1);
backdrop-filter: blur(20px);
border-radius: 20px;
padding: 60px 40px;
border: 1px solid rgba(255, 255, 255, 0.2);
}

.no-courses-icon {
font-size: 4rem;
margin-bottom: 20px;
opacity: 0.6;
}

.no-courses-text {
color: rgba(255, 255, 255, 0.8);
font-size: 1.1rem;
font-weight: 500;
}

@media (max-width: 768px) {
.main-content {
padding: 10px;
}

.welcome-section {
padding: 24px;
margin-bottom: 20px;
}

.welcome-title {
font-size: 2rem;
}

.profile-grid {
grid-template-columns: 1fr;
gap: 16px;
}

.profile-card {
padding: 20px;
}

.courses-grid {
grid-template-columns: 1fr;
}

.modal {
padding: 20px;
width: 95%;
}
}
</style>

<!-- Contenu principal -->
<div class="main-content">
<div class="container">
<!-- Messages d'alerte -->
<?php if (!empty($success_message)): ?>
<div class="alert alert-success">
<span>✅</span>
<?php echo htmlspecialchars($success_message); ?>
</div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
<div class="alert alert-error">
<span>❌</span>
<?php echo htmlspecialchars($error_message); ?>
</div>
<?php endif; ?>

<!-- Section de bienvenue -->
<div class="welcome-section">
<div class="profile-avatar">
<?php echo getInitials($user_data, $user_type); ?>
</div>
<h1 class="welcome-title">Bienvenue, <?php echo htmlspecialchars(getFullName($user_data, $user_type)); ?>!</h1>
<p class="welcome-subtitle">
<?php if ($user_type === 'etudiant'): ?>
Consultez vos informations personnelles et suivez vos cours
<?php else: ?>
Gérez vos cours et consultez vos informations
<?php endif; ?>
</p>
<span class="user-type-badge">
<?php echo $user_type === 'etudiant' ? 'Étudiant' : 'Professeur'; ?>
</span>
</div>

<!-- Grille des informations profil -->
<div class="profile-grid">
<!-- Carte Informations personnelles -->
<div class="profile-card">
<div class="card-icon">👤</div>
<h3 class="card-title">
Informations personnelles
<button class="edit-btn" onclick="openEditModal()">
✏️ Modifier
</button>
</h3>

<?php if ($user_type === 'etudiant'): ?>
<div class="info-item">
<span class="info-label">Nom complet</span>
<span class="info-value"><?php echo htmlspecialchars($user_data['prenom'] . ' ' . $user_data['nom']); ?></span>
</div>
<div class="info-item">
<span class="info-label">Email</span>
<span class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></span>
</div>
<div class="info-item">
<span class="info-label">Date de naissance</span>
<span class="info-value">
<?php
if (!empty($user_data['date_naissance'])) {
$date = new DateTime($user_data['date_naissance']);
echo $date->format('d/m/Y');
} else {
echo 'Non renseignée';
}
?>
</span>
</div>
<div class="info-item">
<span class="info-label">Âge</span>
<span class="info-value">
<?php
if (!empty($user_data['date_naissance'])) {
$naissance = new DateTime($user_data['date_naissance']);
$aujourd_hui = new DateTime();
$age = $aujourd_hui->diff($naissance)->y;
echo $age . ' ans';
} else {
echo 'Non calculé';
}
?>
</span>
</div>
<?php else: // Professeur ?>
<div class="info-item">
<span class="info-label">Nom</span>
<span class="info-value"><?php echo htmlspecialchars($user_data['nom']); ?></span>
</div>
<div class="info-item">
<span class="info-label">Email</span>
<span class="info-value"><?php echo htmlspecialchars($user_data['email']); ?></span>
</div>
<?php if (isset($user_data['specialite'])): ?>
<div class="info-item">
<span class="info-label">Spécialité</span>
<span class="info-value"><?php echo htmlspecialchars($user_data['specialite']); ?></span>
</div>
<?php endif; ?>
<?php endif; ?>
</div>

<!-- Carte Statistiques -->
<div class="profile-card">
<div class="card-icon">📊</div>
<h3 class="card-title">Mes statistiques</h3>
<div class="info-item">
<span class="info-label">Nombre de cours</span>
<span class="info-value"><?php echo count($cours); ?></span>
</div>
<div class="info-item">
<span class="info-label">ID <?php echo $user_type === 'etudiant' ? 'Étudiant' : 'Professeur'; ?></span>
<span class="info-value">#<?php echo htmlspecialchars($user_id); ?></span>
</div>
<div class="info-item">
<span class="info-label">Statut</span>
<span class="info-value">Actif</span>
</div>
<div class="info-item">
<span class="info-label">Dernière connexion</span>
<span class="info-value">Aujourd'hui</span>
</div>
</div>
</div>

<!-- Section des cours -->
<div class="courses-section">
<div class="section-header">
<div class="section-icon">📚</div>
<h2 class="section-title">
<?php echo $user_type === 'etudiant' ? 'Mes Cours' : 'Mes Cours Enseignés'; ?>
(<?php echo count($cours); ?>)
</h2>
</div>

<?php if (empty($cours)): ?>
<div class="no-courses">
<div class="no-courses-icon">📭</div>
<p class="no-courses-text">
<?php echo $user_type === 'etudiant' ? 'Aucun cours inscrit pour le moment.' : 'Aucun cours assigné pour le moment.'; ?>
</p>
</div>
<?php else: ?>
<div class="courses-grid">
<?php foreach ($cours as $c): ?>
<div class="course-card">
<div class="course-header">
<h4 class="course-title"><?php echo htmlspecialchars($c['intitule']); ?></h4>
<span class="platform-badge"><?php echo htmlspecialchars($c['plateforme']); ?></span>
</div>
<div class="course-details">
<div class="course-detail">
<span class="course-detail-label">Matière</span>
<span class="course-detail-value"><?php echo htmlspecialchars($c['matiere_nom']); ?></span>
</div>

<?php if ($user_type === 'etudiant'): ?>
<div class="course-detail">
<span class="course-detail-label">Professeur</span>
<span class="course-detail-value"><?php echo htmlspecialchars($c['prof_nom']); ?></span>
</div>
<?php else: ?>
<div class="course-detail">
<span class="course-detail-label">Étudiants inscrits</span>
<span class="course-detail-value"><?php echo $c['nombre_etudiants']; ?></span>
</div>
<?php endif; ?>

<div class="course-detail">
<span class="course-detail-label">Date</span>
<span class="course-detail-value">
<?php
$date = new DateTime($c['date']);
echo $date->format('d/m/Y');
?>
</span>
</div>
</div>
</div>
<?php endforeach; ?>
</div>
<?php endif; ?>
</div>
</div>
</div>

<!-- Modal de modification -->
<div class="modal-overlay" id="editModal">
<div class="modal">
<div class="modal-header">
<h2 class="modal-title">Modifier mes informations</h2>
<button class="close-btn" onclick="closeEditModal()">&times;</button>
</div>

<form method="POST" action="">
<input type="hidden" name="update_profile" value="1">

<?php if ($user_type === 'etudiant'): ?>
<div class="form-group">
<label class="form-label" for="prenom">Prénom *</label>
<input type="text" id="prenom" name="prenom" class="form-input"
value="<?php echo htmlspecialchars($user_data['prenom']); ?>" required>
</div>

<div class="form-group">
<label class="form-label" for="nom">Nom *</label>
<input type="text" id="nom" name="nom" class="form-input"
value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
</div>

<div class="form-group">
<label class="form-label" for="email">Email *</label>
<input type="email" id="email" name="email" class="form-input"
value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
</div>

<div class="form-group">
<label class="form-label" for="date_naissance">Date de naissance</label>
<input type="date" id="date_naissance" name="date_naissance" class="form-input"
value="<?php echo htmlspecialchars($user_data['date_naissance'] ?? ''); ?>">
</div>

<?php else: // Professeur ?>
<div class="form-group">
<label class="form-label" for="nom">Nom *</label>
<input type="text" id="nom" name="nom" class="form-input"
value="<?php echo htmlspecialchars($user_data['nom']); ?>" required>
</div>

<div class="form-group">
<label class="form-label" for="email">Email *</label>
<input type="email" id="email" name="email" class="form-input"
value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
</div>

<div class="form-group">
<label class="form-label" for="specialite">Spécialité</label>
<input type="text" id="specialite" name="specialite" class="form-input"
value="<?php echo htmlspecialchars($user_data['specialite'] ?? ''); ?>">
</div>
<?php endif; ?>

<div class="form-group">
<label class="form-label" for="nouveau_password">Nouveau mot de passe</label>
<input type="password" id="nouveau_password" name="nouveau_password" class="form-input"
placeholder="Laissez vide pour ne pas changer">
<div class="password-note">Minimum 6 caractères. Laissez vide si vous ne souhaitez pas changer votre mot de passe.</div>
</div>

<div class="form-actions">
<button type="button" class="btn btn-secondary" onclick="closeEditModal()">Annuler</button>
<button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
</div>
</form>
</div>
</div>

<script>
function openEditModal() {
document.getElementById('editModal').classList.add('active');
document.body.style.overflow = 'hidden';
}

function closeEditModal() {
document.getElementById('editModal').classList.remove('active');
document.body.style.overflow = 'auto';
}

// Fermer le modal en cliquant sur l'overlay
document.getElementById('editModal').addEventListener('click', function(e) {
if (e.target === this) {
closeEditModal();
}
});

// Fermer le modal avec la touche Escape
document.addEventListener('keydown', function(e) {
if (e.key === 'Escape' && document.getElementById('editModal').classList.contains('active')) {
closeEditModal();
}
});

// Auto-masquer les messages d'alerte après 5 secondes
document.addEventListener('DOMContentLoaded', function() {
const alerts = document.querySelectorAll('.alert');
alerts.forEach(function(alert) {
setTimeout(function() {
alert.style.opacity = '0';
alert.style.transform = 'translateY(-10px)';
setTimeout(function() {
alert.remove();
}, 300);
}, 5000);
});
});
</script>

</body>
</html>

<?php
require_once 'footer.php';
?>