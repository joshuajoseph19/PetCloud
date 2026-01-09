<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Pet Lover';

$success = "";
$error = "";

// Handle Booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {
    $pet_name = $_POST['pet_name'];
    $breed = $_POST['breed'];
    $service_type = $_POST['service_type'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $cost = 45.00; // Mock cost from image

    try {
        $stmt = $pdo->prepare("INSERT INTO appointments (user_id, pet_name, breed, service_type, title, appointment_date, appointment_time, description, cost) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $title = $service_type . " for " . $pet_name;
        if ($stmt->execute([$user_id, $pet_name, $breed, $service_type, $title, $date, $time, "Scheduled via premium booking", $cost])) {
            $success = "Booking confirmed for " . $pet_name . "! ✨";
        }
    } catch (PDOException $e) {
        $error = "Booking failed: " . $e->getMessage();
    }
}

// Fetch user pets
$petsStmt = $pdo->prepare("SELECT * FROM user_pets WHERE user_id = ?");
$petsStmt->execute([$user_id]);
$allPets = $petsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Appointment - PetCloud</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0f172a;
            --accent: #3b82f6;
            --bg: #f8fafc;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: #1e293b;
        }

        .navbar {
            background: white;
            padding: 1.25rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--accent);
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Outfit';
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
        }

        .nav-links a {
            text-decoration: none;
            color: #64748b;
            font-weight: 500;
            font-size: 0.95rem;
            transition: 0.2s;
        }

        .nav-links a:hover {
            color: var(--accent);
        }

        .nav-auth {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .btn-account {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .main-container {
            max-width: 1300px;
            margin: 3rem auto;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 4rem;
            padding: 0 5%;
        }

        /* Left Side */
        .hero-section {
            padding-top: 2rem;
        }

        .accepting-badge {
            background: #eff6ff;
            color: #3b82f6;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .accepting-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            background: #3b82f6;
            border-radius: 50%;
        }

        .hero-title {
            font-family: 'Outfit';
            font-size: 3.5rem;
            color: var(--primary);
            line-height: 1.1;
            margin-bottom: 1.5rem;
        }

        .hero-title span {
            color: var(--accent);
        }

        .hero-subtitle {
            color: #64748b;
            font-size: 1.15rem;
            line-height: 1.6;
            margin-bottom: 3rem;
            max-width: 90%;
        }

        .hero-img-card {
            background: white;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
            position: relative;
            margin-bottom: 2.5rem;
        }

        .hero-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
        }

        .social-proof {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            padding: 1rem 1.5rem;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avatars {
            display: flex;
            margin-right: 0.5rem;
        }

        .avatars img {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid white;
            margin-left: -10px;
        }

        .avatars img:first-child {
            margin-left: 0;
        }

        .avatars-plus {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            border: 2px solid white;
            margin-left: -10px;
        }

        .stars {
            color: #fbbf24;
            font-size: 0.9rem;
        }

        .proof-text {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }

        .trust-badges {
            display: flex;
            gap: 1.5rem;
        }

        .trust-badge {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 600;
            font-size: 0.9rem;
            color: #334155;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            flex: 1;
        }

        .trust-badge i {
            color: #10b981;
            font-size: 1.25rem;
        }

        /* Right Side: Form Card */
        .form-card {
            background: white;
            border-radius: 2.5rem;
            padding: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid #f1f5f9;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-family: 'Outfit';
            font-size: 1.75rem;
            color: var(--primary);
        }

        .step-badge {
            background: #f1f5f9;
            color: #64748b;
            padding: 0.4rem 0.8rem;
            border-radius: 2rem;
            font-size: 0.75rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .step-dot {
            width: 6px;
            height: 6px;
            background: #10b981;
            border-radius: 50%;
        }

        .section-label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.25rem;
            margin-top: 2rem;
        }

        .input-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.85rem 1.25rem;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            font-size: 0.95rem;
            outline: none;
            transition: 0.2s;
        }

        .form-control:focus {
            border-color: var(--accent);
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        }

        /* Service Cards */
        .service-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .service-option {
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            padding: 1.5rem 1rem;
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
            background: white;
        }

        .service-option:hover {
            border-color: var(--accent);
            background: #f8fafc;
        }

        .service-option.active {
            border: 2.5px solid var(--accent);
            background: #eff6ff;
        }

        .service-option i {
            display: block;
            font-size: 1.5rem;
            color: #64748b;
            margin-bottom: 0.75rem;
        }

        .service-option.active i {
            color: var(--accent);
        }

        .service-name {
            font-size: 0.85rem;
            font-weight: 700;
            color: #475569;
        }

        /* Custom Calendar (Simplified for UI) */
        .calendar-wrap {
            background: #f8fafc;
            border-radius: 1.5rem;
            padding: 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .calendar-title {
            font-weight: 700;
            font-size: 1rem;
        }

        .calendar-nav {
            display: flex;
            gap: 1rem;
            color: #64748b;
            cursor: pointer;
        }

        .calendar-days {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            text-align: center;
            font-size: 0.7rem;
            font-weight: 700;
            color: #94a3b8;
            margin-bottom: 1rem;
        }

        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.5rem;
            text-align: center;
        }

        .day {
            padding: 0.6rem;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            border-radius: 0.75rem;
        }

        .day:hover {
            background: #e2e8f0;
        }

        .day.selected {
            background: var(--accent);
            color: white;
            font-weight: 700;
        }

        .day.muted {
            color: transparent;
            cursor: default;
        }

        /* Time Slots */
        .time-panel {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .time-slot {
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            border-radius: 0.75rem;
            text-align: center;
            font-size: 0.9rem;
            font-weight: 600;
            background: white;
            cursor: pointer;
            transition: 0.2s;
        }

        .time-slot:hover {
            border-color: var(--accent);
        }

        .time-slot.active {
            border: 2px solid var(--accent);
            background: #eff6ff;
            color: var(--accent);
        }

        .time-slot.disabled {
            opacity: 0.3;
            background: #f1f5f9;
            cursor: not-allowed;
        }

        .info-box {
            background: #eff6ff;
            padding: 1rem;
            border-radius: 1rem;
            margin-top: 1.5rem;
            display: flex;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: #1e3a8a;
            line-height: 1.5;
        }

        .info-box i {
            color: var(--accent);
            margin-top: 2px;
        }

        .form-footer {
            margin-top: 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-box span {
            display: block;
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
        }

        .total-price {
            font-size: 1.5rem;
            font-weight: 700;
            font-family: 'Outfit';
            color: var(--primary);
        }

        .btn-confirm {
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: 1rem;
            border: none;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: 0.3s;
        }

        .btn-confirm:hover {
            background: #000;
            transform: translateY(-2px);
        }

        .page-footer {
            margin-top: 5rem;
            padding: 2.5rem 0;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: center;
            gap: 3rem;
            color: #94a3b8;
            font-size: 0.85rem;
            font-weight: 500;
        }

        .footer-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Success Message Overlay */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(5px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
    </style>
</head>

<body>

    <?php if ($success): ?>
        <div class="overlay" onclick="this.remove()">
            <div>
                <div
                    style="width: 80px; height: 80px; background: #dcfce7; color: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 2rem; font-size: 2rem;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h1 style="font-family:'Outfit'; margin-bottom: 1rem;">Appointment Confirmed!</h1>
                <p style="color: #64748b; margin-bottom: 2rem;"><?php echo $success; ?></p>
                <a href="dashboard.php" class="btn-account">Return to Dashboard</a>
            </div>
        </div>
    <?php endif; ?>

    <nav class="navbar">
        <a href="dashboard.php" class="logo">
            <i class="fa-solid fa-cloud"></i> PetCloud
        </a>
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="marketplace.php">Services</a>
            <a href="adoption.php">Adoption</a>
            <a href="health-records.php">Health</a>
        </div>
        <div class="nav-auth">
            <a href="logout.php"
                style="text-decoration:none; color:#ef4444; font-weight:600; font-size:0.9rem;">Logout</a>
            <a href="dashboard.php" class="btn-account">My Dashboard</a>
        </div>
    </nav>

    <main class="main-container">
        <!-- Left Column -->
        <section class="hero-section">
            <div class="accepting-badge">Accepting New Patients</div>
            <h1 class="hero-title">Expert care for your <span>furry family.</span></h1>
            <p class="hero-subtitle">Book a verified professional for grooming, checkups, or daycare in less than 2
                minutes. We treat them like our own.</p>

            <div class="hero-img-card">
                <img src="https://images.unsplash.com/photo-1552053831-71594a27632d?w=1200" class="hero-img"
                    alt="Dogs running">
                <div class="social-proof">
                    <div class="avatars">
                        <img src="https://i.pravatar.cc/100?u=1" alt="user">
                        <img src="https://i.pravatar.cc/100?u=2" alt="user">
                        <img src="https://i.pravatar.cc/100?u=3" alt="user">
                        <div class="avatars-plus">+2k</div>
                    </div>
                    <div style="display:flex; flex-direction:column;">
                        <div class="stars">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                        </div>
                        <span class="proof-text">Trusted by pet parents worldwide</span>
                    </div>
                </div>
            </div>

            <div class="trust-badges">
                <div class="trust-badge"><i class="fa-solid fa-circle-check"></i> Certified Vets</div>
                <div class="trust-badge"><i class="fa-solid fa-clock"></i> 24/7 Support</div>
            </div>
        </section>

        <!-- Right Column -->
        <section class="appointment-form">
            <div class="form-card">
                <div class="form-header">
                    <h2>Schedule Appointment</h2>
                    <div class="step-badge">
                        <div class="step-dot"></div> Step 1 of 3
                    </div>
                </div>

                <form method="POST" id="bookingForm">
                    <div class="section-label"><i class="fa-solid fa-paw"></i> Pet Details</div>
                    <div class="input-row">
                        <div class="form-group">
                            <label>Pet Name</label>
                            <input type="text" name="pet_name" id="petNameInput" class="form-control"
                                placeholder="e.g. Bella" required>
                        </div>
                        <div class="form-group">
                            <label>Breed</label>
                            <select name="breed" class="form-control" required>
                                <option value="Dog">Dog</option>
                                <option value="Cat">Cat</option>
                                <option value="Bird">Bird</option>
                                <option value="Rabbit">Rabbit</option>
                            </select>
                        </div>
                    </div>

                    <div class="section-label"><i class="fa-solid fa-briefcase-medical"></i> Service Type</div>
                    <div class="service-grid">
                        <div class="service-option active" data-service="Checkup">
                            <i class="fa-solid fa-stethoscope"></i>
                            <span class="service-name">Checkup</span>
                        </div>
                        <div class="service-option" data-service="Grooming">
                            <i class="fa-solid fa-scissors"></i>
                            <span class="service-name">Grooming</span>
                        </div>
                        <div class="service-option" data-service="Vaccine">
                            <i class="fa-solid fa-syringe"></i>
                            <span class="service-name">Vaccine</span>
                        </div>
                    </div>
                    <input type="hidden" name="service_type" id="serviceTypeInput" value="Checkup">

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1rem;">
                        <div>
                            <div class="section-label"><i class="fa-solid fa-calendar"></i> Select Date</div>
                            <div class="calendar-wrap">
                                <div class="calendar-header">
                                    <div class="calendar-title">October 2026</div>
                                    <div class="calendar-nav">
                                        <i class="fa-solid fa-chevron-left"></i>
                                        <i class="fa-solid fa-chevron-right"></i>
                                    </div>
                                </div>
                                <div class="calendar-days">
                                    <span>SU</span><span>MO</span><span>TU</span><span>WE</span><span>TH</span><span>FR</span><span>SA</span>
                                </div>
                                <div class="calendar-grid">
                                    <span class="day muted">27</span><span class="day muted">28</span><span
                                        class="day muted">29</span><span class="day muted">30</span><span
                                        class="day">1</span><span class="day">2</span><span class="day">3</span>
                                    <span class="day">4</span><span class="day selected">5</span><span
                                        class="day">6</span><span class="day">7</span><span class="day">8</span><span
                                        class="day">9</span><span class="day">10</span>
                                    <span class="day">11</span><span class="day">12</span><span
                                        class="day">13</span><span class="day">14</span><span class="day">15</span><span
                                        class="day">16</span><span class="day">17</span>
                                </div>
                            </div>
                            <input type="hidden" name="appointment_date" id="dateInput" value="2026-10-05">
                        </div>
                        <div>
                            <div class="section-label"><i class="fa-solid fa-clock"></i> Available Time</div>
                            <div class="time-panel">
                                <div class="time-slot" data-time="09:00:00">09:00 AM</div>
                                <div class="time-slot active" data-time="10:30:00">10:30 AM</div>
                                <div class="time-slot" data-time="13:00:00">01:00 PM</div>
                                <div class="time-slot" data-time="14:30:00">02:30 PM</div>
                                <div class="time-slot" data-time="16:00:00">04:00 PM</div>
                                <div class="time-slot disabled">05:30 PM</div>
                            </div>
                            <input type="hidden" name="appointment_time" id="timeInput" value="10:30:00">
                        </div>
                    </div>

                    <div class="info-box">
                        <i class="fa-solid fa-circle-info"></i>
                        <div>The <span style="font-weight:700;">10:30 AM</span> slot is popular. Book soon to secure it
                            for <span id="petNameLabel">Bella</span>.</div>
                    </div>

                    <div class="form-footer">
                        <div class="total-box">
                            <span>Total estimation</span>
                            <div class="total-price">$45.00</div>
                        </div>
                        <button type="submit" name="confirm_booking" class="btn-confirm">
                            Confirm Booking <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <footer class="page-footer">
        <div class="footer-item"><i class="fa-solid fa-shield-halved"></i> Secure Payment</div>
        <div class="footer-item"><i class="fa-solid fa-user-shield"></i> Verified Pros</div>
        <div class="footer-item"><i class="fa-solid fa-rotate-left"></i> Instant Refund</div>
    </footer>

    <script>
        // Service Selection
        const serviceOptions = document.querySelectorAll('.service-option');
        const serviceInput = document.getElementById('serviceTypeInput');
        serviceOptions.forEach(opt => {
            opt.addEventListener('click', () => {
                serviceOptions.forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                serviceInput.value = opt.dataset.service;
            });
        });

        // Time Selection
        const timeSlots = document.querySelectorAll('.time-slot:not(.disabled)');
        const timeInput = document.getElementById('timeInput');
        timeSlots.forEach(slot => {
            slot.addEventListener('click', () => {
                timeSlots.forEach(s => s.classList.remove('active'));
                slot.classList.add('active');
                timeInput.value = slot.dataset.time;
            });
        });

        // Pet Name Sync
        const petNameInput = document.getElementById('petNameInput');
        const petNameLabel = document.getElementById('petNameLabel');
        petNameInput.addEventListener('input', (e) => {
            petNameLabel.textContent = e.target.value || 'Bella';
        });

        // Date selection (Simplified)
        const days = document.querySelectorAll('.day:not(.muted)');
        const dateInput = document.getElementById('dateInput');
        days.forEach(day => {
            day.addEventListener('click', () => {
                days.forEach(d => d.classList.remove('selected'));
                day.classList.add('selected');
                // Mock date formatting
                const dayVal = day.textContent.padStart(2, '0');
                dateInput.value = `2026-10-${dayVal}`;
            });
        });
    </script>
</body>

</html>