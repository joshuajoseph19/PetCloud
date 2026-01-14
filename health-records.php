<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Pet Owner';

// Handle Add Record Submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_record'])) {
    $pet_id = $_POST['pet_id'];
    $type = $_POST['record_type'];
    $date = $_POST['record_date'];
    $desc = $_POST['description'];

    $stmt = $pdo->prepare("INSERT INTO health_records (user_id, pet_id, record_type, record_date, description) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$user_id, $pet_id, $type, $date, $desc])) {
        echo "<script>alert('Health record saved successfully! ✨');</script>";
    }
}

// Handle Task Toggle (AJAX-like simple POST)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_task'])) {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'];
    $pdo->prepare("UPDATE daily_tasks SET is_done = ? WHERE id = ? AND user_id = ?")->execute([$status, $task_id, $user_id]);
    exit(); // Stop here for AJAX
}

// Handle Add New Task
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_new_task'])) {
    $task_name = $_POST['task_name'];
    $pdo->prepare("INSERT INTO daily_tasks (user_id, task_name, task_time, task_date) VALUES (?, ?, 'Just now', CURDATE())")->execute([$user_id, $task_name]);
    exit();
}

// Fetch User's Pets
$petsStmt = $pdo->prepare("SELECT * FROM user_pets WHERE user_id = ?");
$petsStmt->execute([$user_id]);
$allPets = $petsStmt->fetchAll();

// Fetch Real Daily Tasks
$tasksStmt = $pdo->prepare("SELECT * FROM daily_tasks WHERE user_id = ? AND task_date = CURDATE()");
$tasksStmt->execute([$user_id]);
$dailyTasks = $tasksStmt->fetchAll();

$tasksCompleted = count(array_filter($dailyTasks, fn($t) => $t['is_done']));
$totalTasks = count($dailyTasks) ?: 1;
$progress = round(($tasksCompleted / $totalTasks) * 100);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Dashboard - PetCloud</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3b82f6;
            --primary-light: #eff6ff;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #fff;
            color: var(--gray-900);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        /* Top Nav */
        .top-nav {
            border-bottom: 1px solid var(--gray-100);
            padding: 1rem 0;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--primary);
            font-weight: 700;
            font-size: 1.25rem;
            font-family: 'Outfit';
        }

        .nav-links {
            display: flex;
            background: var(--gray-100);
            padding: 0.25rem;
            border-radius: 2rem;
            gap: 0.25rem;
        }

        .nav-link {
            text-decoration: none;
            padding: 0.5rem 1.25rem;
            border-radius: 2rem;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--gray-500);
            transition: 0.3s;
        }

        .nav-link.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .top-icons {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-500);
            cursor: pointer;
        }

        .badge-dot {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--danger);
            width: 8px;
            height: 8px;
            border-radius: 50%;
            border: 2px solid white;
        }

        /* Hero */
        .hero {
            margin-top: 1.5rem;
            border-radius: 1.5rem;
            overflow: hidden;
            position: relative;
            height: 320px;
            color: white;
        }

        .hero-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
        }

        .hero-content {
            position: absolute;
            top: 0;
            left: 0;
            padding: 3rem;
            width: 60%;
        }

        .hero-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            display: inline-block;
            margin-bottom: 1rem;
            text-transform: uppercase;
        }

        .hero h1 {
            font-family: 'Outfit';
            font-size: 2.75rem;
            line-height: 1.1;
            margin-bottom: 1rem;
        }

        .hero p {
            opacity: 0.9;
            margin-bottom: 2rem;
            font-size: 1rem;
            line-height: 1.5;
        }

        .search-wrap {
            position: relative;
            max-width: 500px;
            display: flex;
            gap: 0.5rem;
        }

        .search-input {
            flex: 1;
            padding: 0.8rem 1.25rem;
            border-radius: 0.75rem;
            border: none;
            font-size: 0.9rem;
            outline: none;
        }

        .search-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            cursor: pointer;
        }

        /* Quick Actions */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin: 2rem 0;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 1.25rem;
            border: 1px solid var(--gray-100);
            text-align: center;
            transition: 0.3s;
            cursor: pointer;
        }

        .stat-card:hover {
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.25rem;
        }

        /* Layout Grid */
        .layout-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
        }

        /* Reminder */
        .reminder-card {
            border: 1px solid var(--gray-100);
            border-radius: 1rem;
            margin-bottom: 2.5rem;
            overflow: hidden;
        }

        .reminder-header {
            padding: 1rem 1.5rem;
            background: var(--gray-50);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--gray-100);
        }

        .reminder-body {
            padding: 1.5rem;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .reminder-img {
            width: 220px;
            height: 140px;
            border-radius: 1rem;
            object-fit: cover;
        }

        .reminder-content h3 {
            font-family: 'Outfit';
            margin-bottom: 0.5rem;
        }

        .reminder-content p {
            color: var(--gray-500);
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1.25rem;
        }

        .tag {
            font-size: 0.75rem;
            color: var(--danger);
            font-weight: 700;
        }

        /* Sidebar Cards */
        .side-card {
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 1.25rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .side-card h3 {
            font-family: 'Outfit';
            margin-bottom: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .task-item {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.25rem;
            align-items: flex-start;
        }

        .task-check {
            width: 20px;
            height: 20px;
            border-radius: 6px;
            border: 2px solid var(--gray-200);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            cursor: pointer;
        }

        .task-check.done {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .task-info h4 {
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .task-info p {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .task-item.active {
            background: var(--primary-light);
            margin: -0.5rem -1rem 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #dbeafe;
        }

        /* Article Cards */
        .articles-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .article-card {
            border: 1px solid var(--gray-100);
            border-radius: 1rem;
            overflow: hidden;
        }

        .article-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .article-padding {
            padding: 1.25rem;
        }

        .item-meta {
            display: flex;
            gap: 1rem;
            font-size: 0.7rem;
            color: var(--gray-500);
            margin-bottom: 0.75rem;
        }

        .article-card h4 {
            font-family: 'Outfit';
            font-size: 1.1rem;
            margin-bottom: 0.75rem;
        }

        .article-card p {
            font-size: 0.85rem;
            color: var(--gray-500);
            line-height: 1.5;
        }

        /* Pet Profile Cards */
        .profiles-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
        }

        .scroll-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 4rem;
        }

        .pet-card {
            background: white;
            border: 1px solid var(--gray-100);
            border-radius: 1.25rem;
            padding: 1.5rem;
            position: relative;
        }

        .pet-top {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .pet-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            object-fit: cover;
        }

        .pet-line {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            font-size: 0.8rem;
            border-bottom: 1px solid var(--gray-50);
            padding-bottom: 0.5rem;
        }

        .pet-line span:first-child {
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .pet-line span:last-child {
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 0.75rem;
            font-weight: 600;
            font-size: 0.85rem;
            cursor: pointer;
            transition: 0.2s;
            border: none;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-outline {
            background: white;
            border: 1.5px solid var(--gray-200);
            color: var(--gray-700);
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.75rem;
        }

        /* Breed Card */
        .breed-card {
            text-align: center;
        }

        .breed-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 1rem;
            border: 4px solid var(--primary-light);
        }

        .breed-tags {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .pill {
            font-size: 0.65rem;
            padding: 0.2rem 0.6rem;
            border-radius: 2rem;
            background: var(--gray-100);
            color: var(--gray-500);
            font-weight: 600;
        }

        /* Progress Circle (Simplified for UI) */
        .progress-circle {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: conic-gradient(var(--primary) calc(var(--p) * 1%), var(--gray-100) 0);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .progress-circle::after {
            content: attr(data-p) '%';
            position: absolute;
            width: 38px;
            height: 38px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 1.5rem;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--gray-700);
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1.5px solid var(--gray-100);
            border-radius: 0.75rem;
            outline: none;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary);
        }
    </style>
</head>

<body>

    <nav class="top-nav">
        <div class="container nav-content">
            <div class="brand">
                <i class="fa-solid fa-paw"></i>
                <span>PetCloud</span>
            </div>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Dashboard</a>
                <a href="health-records.php" class="nav-link active">Health</a>
                <a href="schedule.php" class="nav-link">Appointments</a>
                <a href="profile.php" class="nav-link">Profile</a>
            </div>
            <div class="top-icons">
                <div class="icon-btn"><i class="fa-regular fa-bell"></i><span class="badge-dot"></span></div>
                <div class="icon-btn" style="border: none;"><img
                        src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=random"
                        style="width:32px; border-radius:50%;"></div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Hero (Professional Design) -->
        <section class="hero"
            style="background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.6)), url('https://images.unsplash.com/photo-1576201836106-ca1746f3364d?w=1200&q=80'); background-size: cover; background-position: center; height: 400px; display: flex; align-items: center; box-shadow: 0 20px 40px rgba(0,0,0,0.1); margin-top: 2rem; border-radius: 2rem; border: 1px solid rgba(0,0,0,0.05);">
            <div class="hero-content" style="width: 70%; padding-left: 4rem;">
                <span class="hero-badge"
                    style="background: #1e293b; color: white; padding: 0.6rem 1.25rem; border-radius: 2rem; font-weight: 800; border: 1px solid rgba(255,255,255,0.1); box-shadow: 0 4px 12px rgba(0,0,0,0.2);">VETERINARY
                    APPROVED</span>
                <h1
                    style="font-size: 3.8rem; font-weight: 700; letter-spacing: -2px; margin-top: 1rem; text-shadow: 0 2px 10px rgba(0,0,0,0.2);">
                    Expert Health Guidance<br>for Every <span style="color: #3b82f6;">Paw Step</span></h1>
                <p style="font-size: 1.25rem; max-width: 550px; margin-top: 1.5rem; opacity: 0.9;">Access comprehensive
                    care guides, track vital health milestones, and get personalized advice for your furry companions.
                </p>
                <div class="search-wrap"
                    style="margin-top: 2.5rem; background: white; padding: 0.5rem; border-radius: 1.25rem; box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                    <input type="text" id="mainSearch" class="search-input"
                        style="background: transparent; padding-left: 1.5rem;"
                        placeholder="Search symptoms, diet tips, or breeds..."
                        onkeyup="if(event.key==='Enter') performSearch()">
                    <button class="search-btn" onclick="performSearch()"
                        style="padding: 1rem 2rem; border-radius: 1rem;">Search Now</button>
                </div>
            </div>
        </section>

        <!-- Quick Actions -->
        <section class="stats-grid">
            <div class="stat-card" onclick="openModal()">
                <div class="stat-icon" style="background:#dbeafe; color:#3b82f6;"><i class="fa-solid fa-syringe"></i>
                </div>
                <h4 style="font-size:0.9rem; margin-bottom:0.25rem;">Log Vaccination</h4>
                <p style="font-size:0.7rem; color:var(--gray-500);">Update health records</p>
            </div>
            <a href="find-vet.php" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon" style="background:#f0fdf4; color:#22c55e;"><i
                        class="fa-solid fa-location-dot"></i></div>
                <h4 style="font-size:0.9rem; margin-bottom:0.25rem;">Find Vet</h4>
                <p style="font-size:0.7rem; color:var(--gray-500);">Clinics nearby</p>
            </a>
            <a href="mypets.php" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon" style="background:#fef9c3; color:#eab308;"><i class="fa-solid fa-plus"></i></div>
                <h4 style="font-size:0.9rem; margin-bottom:0.25rem;">Add Pet</h4>
                <p style="font-size:0.7rem; color:var(--gray-500);">Create new profile</p>
            </a>
            <a href="symptom-checker.php" class="stat-card" style="text-decoration:none; color:inherit;">
                <div class="stat-icon" style="background:#fee2e2; color:#ef4444;"><i
                        class="fa-solid fa-heart-pulse"></i></div>
                <h4 style="font-size:0.9rem; margin-bottom:0.25rem;">Symptom Checker</h4>
                <p style="font-size:0.7rem; color:var(--gray-500);">AI Health Assistant</p>
            </a>
        </section>

        <div class="layout-grid">
            <div class="main-side">
                <!-- Urgent Reminder -->
                <div class="reminder-card" id="reminderCard">
                    <div class="reminder-header">
                        <div style="display:flex; align-items:center; gap:0.75rem;">
                            <i class="fa-solid fa-circle-exclamation" style="color:var(--danger);"></i>
                            <span style="font-weight:700; font-size:0.9rem;">Urgent Reminder</span>
                        </div>
                        <span
                            style="font-size:0.75rem; color:var(--danger); font-weight:600; background:#fee2e2; padding:0.2rem 0.6rem; border-radius:1rem;">Due
                            in 12 days</span>
                    </div>
                    <div class="reminder-body">
                        <img src="https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=400"
                            class="reminder-img">
                        <div class="reminder-content">
                            <h3>Rabies Booster Shot</h3>
                            <p style="margin-bottom:0.25rem;">For: <strong>Bella</strong> (Golden Retriever)</p>
                            <p>This vaccination is critical for legal requirements and your pet's safety. Please
                                schedule an appointment with Dr. Smith soon.</p>
                            <div style="display:flex; gap:0.75rem;">
                                <a href="schedule.php" class="btn btn-primary" style="text-decoration:none;">Book
                                    Appointment</a>
                                <button class="btn btn-outline" onclick="dismissReminder()">Mark as Done</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trending Advice Removed -->
            </div>

            <div class="sidebar-side">
                <div class="side-card">
                    <h3 style="margin-bottom:0.5rem;">Daily Routine</h3>
                    <p style="font-size:0.75rem; color:var(--gray-500); margin-bottom:1.5rem;">Keep on track today</p>

                    <div style="display:flex; justify-content:flex-end; margin-top:-3rem; margin-bottom:2rem;">
                        <div class="progress-circle" id="taskProgressCircle" data-p="<?php echo $progress; ?>"
                            style="--p:<?php echo $progress; ?>;"></div>
                    </div>

                    <div class="tasks-list">
                        <?php foreach ($dailyTasks as $task): ?>
                            <div class="task-item" onclick="toggleTask(this, <?php echo $task['id']; ?>)">
                                <div class="task-check <?php echo $task['is_done'] ? 'done' : ''; ?>">
                                    <?php if ($task['is_done']): ?><i class="fa-solid fa-check"
                                            style="font-size:10px;"></i><?php endif; ?>
                                </div>
                                <div class="task-info">
                                    <h4><?php echo htmlspecialchars($task['task_name']); ?></h4>
                                    <p><?php echo htmlspecialchars($task['task_time']); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button onclick="addTask()"
                        style="width:100%; padding:0.75rem; background:transparent; border:1px dashed var(--gray-200); border-radius:0.75rem; color:var(--primary); font-size:0.8rem; font-weight:600; cursor:pointer;">+
                        Add Task</button>
                </div>

                <!-- Breed Spotlight Removed -->
            </div>
        </div>

        <div class="profiles-header">
            <div>
                <h2 style="font-family:'Outfit';">Your Pet Profiles</h2>
                <p style="font-size:0.8rem; color:var(--gray-500);">Manage individual needs and schedules</p>
            </div>
            <div style="display:flex; gap:0.5rem;">
                <button class="icon-btn" style="width:32px; height:32px;"><i
                        class="fa-solid fa-chevron-left"></i></button>
                <button class="icon-btn" style="width:32px; height:32px;"><i
                        class="fa-solid fa-chevron-right"></i></button>
            </div>
        </div>

        <div class="scroll-grid">
            <?php foreach ($allPets as $pet): ?>
                <div class="pet-card">
                    <div class="pet-top">
                        <img src="<?php echo $pet['pet_image']; ?>" class="pet-avatar">
                        <div>
                            <h4 style="font-family:'Outfit';"><?php echo $pet['pet_name']; ?></h4>
                            <p style="font-size:0.7rem; color:var(--gray-500);"><?php echo $pet['pet_breed']; ?> •
                                <?php echo $pet['pet_age']; ?>
                            </p>
                        </div>
                        <i class="fa-solid fa-ellipsis" style="margin-left:auto; color:var(--gray-200);"></i>
                    </div>
                    <div class="pet-line">
                        <span><i class="fa-solid fa-weight-scale"></i> Weight</span>
                        <span><?php echo $pet['pet_weight'] ?? '28.5 kg'; ?></span>
                    </div>
                    <div class="pet-line" style="border:none;">
                        <span><i class="fa-regular fa-calendar-check"></i> Next Vet</span>
                        <span style="color:var(--danger);">Oct 24</span>
                    </div>
                </div>
            <?php endforeach; ?>

            <a href="mypets.php" class="pet-card"
                style="border:2px dashed var(--gray-200); display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; cursor:pointer; text-decoration:none; color:inherit;">
                <div
                    style="width:44px; height:44px; border-radius:50%; border:2px solid var(--primary-light); display:flex; align-items:center; justify-content:center; color:var(--primary); margin-bottom:0.75rem;">
                    <i class="fa-solid fa-plus"></i>
                </div>
                <h4 style="font-family:'Outfit'; font-size:0.9rem;">Add New Pet</h4>
                <p style="font-size:0.7rem; color:var(--gray-500);">Create profile</p>
            </a>
        </div>
    </div>

    <!-- Add Record Modal -->
    <div class="modal" id="recordModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 style="font-family:'Outfit';">Log Health Record</h2>
                <i class="fa-solid fa-xmark" style="cursor:pointer;" onclick="closeModal()"></i>
            </div>
            <form method="POST">
                <div class="form-group">
                    <label>Select Pet</label>
                    <select class="form-control" name="pet_id" required>
                        <?php foreach ($allPets as $p): ?>
                            <option value="<?php echo $p['id']; ?>"><?php echo $p['pet_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Record Type</label>
                    <select class="form-control" name="record_type" required>
                        <option>Vaccination</option>
                        <option>Weight Check</option>
                        <option>Check-up</option>
                        <option>Surgery</option>
                        <option>Medication</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" class="form-control" name="record_date" value="<?php echo date('Y-m-d'); ?>"
                        required>
                </div>
                <div class="form-group">
                    <label>Description/Notes</label>
                    <textarea class="form-control" name="description" rows="3"
                        placeholder="e.g. Rabies booster, 3-year dose"></textarea>
                </div>
                <button type="submit" name="add_record" class="btn btn-primary"
                    style="width:100%; margin-top:1rem;">Save Record</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() { document.getElementById('recordModal').style.display = 'flex'; }
        function closeModal() { document.getElementById('recordModal').style.display = 'none'; }

        function dismissReminder() {
            const card = document.getElementById('reminderCard');
            card.style.transform = 'scale(0.95)';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 400);
        }

        async function toggleTask(el, taskId) {
            const check = el.querySelector('.task-check');
            const isDone = !check.classList.contains('done');

            // Visual feedback
            check.classList.toggle('done');
            check.innerHTML = isDone ? '<i class="fa-solid fa-check" style="font-size:10px;"></i>' : '';

            // DB Update
            const formData = new FormData();
            formData.append('toggle_task', '1');
            formData.append('task_id', taskId);
            formData.append('status', isDone ? '1' : '0');

            await fetch('health-records.php', { method: 'POST', body: formData });
            updateProgress();
        }

        function updateProgress() {
            const total = document.querySelectorAll('.task-item').length;
            const done = document.querySelectorAll('.task-check.done').length;
            const percent = total > 0 ? Math.round((done / total) * 100) : 0;
            const circle = document.getElementById('taskProgressCircle');
            circle.style.setProperty('--p', percent);
            circle.setAttribute('data-p', percent);
        }

        function performSearch() {
            const q = document.getElementById('mainSearch').value.toLowerCase();
            const articles = document.querySelectorAll('.article-card');
            let found = 0;

            articles.forEach(card => {
                const title = card.querySelector('h4').innerText.toLowerCase();
                const text = card.querySelector('p').innerText.toLowerCase();
                if (title.includes(q) || text.includes(q)) {
                    card.style.display = 'block';
                    found++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (q && found === 0) {
                alert('No health articles match your search: ' + q);
            }
        }

        async function addTask() {
            const name = prompt("Enter task name (e.g., Vitamin D Supplement):");
            if (!name) return;

            const formData = new FormData();
            formData.append('add_new_task', '1');
            formData.append('task_name', name);

            await fetch('health-records.php', { method: 'POST', body: formData });
            location.reload(); // Refresh to show new task with proper ID
        }

        const breeds = [
            { name: 'Beagle', img: 'https://images.unsplash.com/photo-1543466835-00a7907e9de1?w=200', group: 'Hound Group • Small-Medium', desc: 'Known for their keen sense of smell and tracking instinct.', tags: ['Friendly', 'High Energy', 'Curious'] },
            { name: 'Golden Retriever', img: 'https://images.unsplash.com/photo-1552053831-71594a27632d?w=200', group: 'Sporting Group • Large', desc: 'Intelligent, friendly, and devoted companions.', tags: ['Loyal', 'Gentle', 'Playful'] },
            { name: 'Corgi', img: 'https://images.unsplash.com/photo-1519098901909-b1553a1190af?w=200', group: 'Herding Group • Small', desc: 'Bold, athletic, and surprisingly active for their size.', tags: ['Smart', 'Alert', 'Affectionate'] }
        ];

        let breedIdx = 0;
        function refreshBreed() {
            breedIdx = (breedIdx + 1) % breeds.length;
            const b = breeds[breedIdx];
            document.getElementById('breedName').textContent = b.name;
            document.getElementById('breedImg').src = b.img;
            document.getElementById('breedGroup').textContent = b.group;
            document.getElementById('breedDesc').textContent = b.desc;
            const tagsWrap = document.getElementById('breedTags');
            tagsWrap.innerHTML = b.tags.map(t => `<span class="pill">${t}</span>`).join('');
        }
    </script>
</body>

</html>

</html>