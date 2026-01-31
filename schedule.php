<?php
session_start();
require_once 'db_connect.php';
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Pet Lover';

$success = "";
$error = "";

// --- AUTO-FIX: Create Tables/Columns If Missing ---
try {
    $pdo->query("SELECT payment_id FROM appointments LIMIT 1");
} catch (PDOException $e) {
    try {
        $pdo->query("SELECT 1 FROM appointments LIMIT 1");
        $pdo->exec("ALTER TABLE appointments ADD COLUMN payment_id VARCHAR(255) AFTER user_id");
    } catch (PDOException $ex) {
        // Table might not exist, but let booking logic handle it or run setup_appointment_system.php
    }
}

// Handle Booking
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_booking'])) {
    $pet_name = $_POST['pet_name'] ?? 'Pet';
    $breed = $_POST['breed'] ?? 'Unknown';
    $service_type = $_POST['service_type'] ?? 'General';
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $hospital_id = $_POST['hospital_id'];
    $payment_id = $_POST['razorpay_payment_id'] ?? '';

    if (empty($payment_id)) {
        $error = "Payment configuration error or session timeout.";
    } else {
        // Security: Re-fetch price from DB to prevent tampering
        $priceStmt = $pdo->prepare("SELECT price FROM hospital_services WHERE hospital_id = ? AND service_name = ?");
        $priceStmt->execute([$hospital_id, $service_type]);
        $priceRow = $priceStmt->fetch();

        $cost = $priceRow ? $priceRow['price'] : 0;

        try {
            // Insert with hospital_id
            $stmt = $pdo->prepare("
                INSERT INTO appointments 
                (user_id, payment_id, hospital_id, pet_name, breed, service_type, title, appointment_date, appointment_time, description, cost, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed')
            ");

            $title = $service_type . " for " . $pet_name;

            if ($stmt->execute([$user_id, $payment_id, $hospital_id, $pet_name, $breed, $service_type, $title, $date, $time, "Scheduled Appointment", $cost])) {
                $success = "Booking confirmed for " . $pet_name . "! ✨";
            }
        } catch (PDOException $e) {
            $error = "Booking failed: " . $e->getMessage();
        }
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
            <img src="images/logo.png" alt="PetCloud Logo" style="height: 60px; width: auto; object-fit: contain;">
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
                    <div class="step-badge" id="stepBadge">
                        <div class="step-dot"></div> Step 1: Details
                    </div>
                </div>

                <form method="POST" id="bookingForm">
                    <input type="hidden" name="hospital_id" id="hospitalIdInput">
                    <input type="hidden" name="service_price" id="priceInput">

                    <!-- STEP 1: Pet & Service -->
                    <div id="step1">
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

                        <div class="section-label"><i class="fa-solid fa-layer-group"></i> Select Category</div>
                        <div id="categoryGrid" class="service-grid">
                            <!-- Populated dynamically by JS -->
                            <div style="grid-column:1/-1; text-align:center; color:#94a3b8;">Loading categories...</div>
                        </div>

                        <div id="serviceSelectionSection" style="display:none; margin-top: 1.5rem;">
                            <div class="section-label"><i class="fa-solid fa-briefcase-medical"></i> Select Specific
                                Service</div>
                            <div id="serviceListGrid" class="service-grid" style="grid-template-columns: 1fr 1fr;">
                                <!-- Populated dynamically by JS -->
                            </div>
                        </div>

                        <input type="hidden" name="service_type" id="serviceTypeInput" required>
                        <div id="serviceError" style="color:red; font-size:0.8rem; margin-top:0.5rem; display:none;">
                            Please select a service.</div>

                        <!-- Hospital Selection Container -->
                        <div id="hospitalSection" style="display:none; margin-top: 2rem;">
                            <div class="section-label"><i class="fa-solid fa-hospital"></i> Select Clinic</div>
                            <div id="hospitalGrid" style="display:grid; grid-template-columns:1fr; gap:1rem;">
                                <!-- Populated by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Date & Time (Initially Hidden) -->
                    <div id="step2" style="display:none; margin-top: 2rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 1rem;">
                            <div>
                                <div class="section-label"><i class="fa-solid fa-calendar"></i> Select Date</div>
                                <input type="date" name="appointment_date" id="dateInput" class="form-control"
                                    style="font-family:inherit;" min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div>
                                <div class="section-label"><i class="fa-solid fa-clock"></i> Available Time</div>
                                <div class="time-panel" id="timeSlotContainer">
                                    <div style="grid-column: 1/-1; text-align:center; color:#94a3b8; padding:1rem;">
                                        Select a date to view slots</div>
                                </div>
                                <input type="hidden" name="appointment_time" id="timeInput" required>
                            </div>
                        </div>

                        <div class="info-box">
                            <i class="fa-solid fa-circle-info"></i>
                            <div>Selected Clinic: <span id="selectedClinicName" style="font-weight:700;">-</span></div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <div class="total-box">
                            <span>Total estimation</span>
                            <div class="total-price" id="totalPriceDisplay">₹0</div>
                        </div>
                        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
                        <input type="hidden" name="confirm_booking" value="1">
                        <button type="button" class="btn-confirm" id="btnContinue">
                            Secure Payment & Book <i class="fa-solid fa-lock" style="font-size: 0.8rem;"></i>
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
        // DOM Elements
        const categoryGrid = document.getElementById('categoryGrid');
        const serviceSelectionSection = document.getElementById('serviceSelectionSection');
        const serviceListGrid = document.getElementById('serviceListGrid');
        const serviceInput = document.getElementById('serviceTypeInput');

        const hospitalSection = document.getElementById('hospitalSection');
        const hospitalGrid = document.getElementById('hospitalGrid');
        const step2 = document.getElementById('step2');
        const dateInput = document.getElementById('dateInput');
        const timeSlotContainer = document.getElementById('timeSlotContainer');
        const timeInput = document.getElementById('timeInput');
        const hospitalIdInput = document.getElementById('hospitalIdInput');
        const priceInput = document.getElementById('priceInput');
        const totalPriceDisplay = document.getElementById('totalPriceDisplay');
        const selectedClinicName = document.getElementById('selectedClinicName');
        const stepBadge = document.getElementById('stepBadge');

        // State
        let currentCategory = null;
        let currentService = null;
        let currentHospitalId = null;

        // Initialize: Fetch Categories
        // This is a new function to fetch categories from the API
        async function fetchCategories() {
            try {
                const res = await fetch('api/get_service_categories.php');
                const result = await res.json();

                if (result.success) {
                    renderCategories(result.data);
                } else {
                    categoryGrid.innerHTML = 'Error loading categories';
                }
            } catch (e) {
                console.error(e);
                categoryGrid.innerHTML = 'Failed to load categories';
            }
        }

        // Render Categories
        function renderCategories(categories) {
            categoryGrid.innerHTML = '';
            categories.forEach(cat => {
                const div = document.createElement('div');
                div.className = 'service-option';
                div.innerHTML = `
                    <i class="fa-solid ${cat.icon || 'fa-paw'}"></i>
                    <span class="service-name">${cat.name}</span>
                `;
                div.addEventListener('click', () => {
                    // Highlight logic
                    document.querySelectorAll('#categoryGrid .service-option').forEach(el => el.classList.remove('active'));
                    div.classList.add('active');

                    selectCategory(cat.id);
                });
                categoryGrid.appendChild(div);
            });
        }

        // Handle Category Selection
        function selectCategory(categoryId) {
            currentCategory = categoryId;
            serviceSelectionSection.style.display = 'block';
            serviceListGrid.innerHTML = '<div style="grid-column:1/-1; text-align:center;">Loading services...</div>';

            // Reset downstream
            resetServiceSelection();
            resetHospitalSelection();

            fetchServices(categoryId);
        }

        // Fetch Services by Category
        async function fetchServices(categoryId) {
            try {
                const res = await fetch(`api/get_services.php?category_id=${categoryId}`);
                const result = await res.json();

                if (result.success) {
                    renderServices(result.data);
                } else {
                    serviceListGrid.innerHTML = 'Error loading services';
                }
            } catch (e) {
                console.error(e);
                serviceListGrid.innerHTML = 'Failed to load services';
            }
        }

        // Render Services
        function renderServices(services) {
            serviceListGrid.innerHTML = '';
            if (services.length === 0) {
                serviceListGrid.innerHTML = '<div style="grid-column:1/-1; text-align:center;">No services found for this category.</div>';
                return;
            }

            services.forEach(srv => {
                const div = document.createElement('div');
                div.className = 'service-option';
                // Smaller padding for list items
                div.style.padding = '1rem';
                div.style.display = 'flex';
                div.style.alignItems = 'center';
                div.style.gap = '0.75rem';

                div.innerHTML = `
                    <div style="font-weight:600; font-size:0.9rem;">${srv.name}</div>
                    <div style="margin-left:auto; font-size:0.75rem; color:#64748b;">${srv.default_duration_minutes}m</div>
                `;

                div.addEventListener('click', () => {
                    document.querySelectorAll('#serviceListGrid .service-option').forEach(el => el.classList.remove('active'));
                    div.classList.add('active');

                    selectService(srv.name);
                });
                serviceListGrid.appendChild(div);
            });
        }

        function selectService(serviceName) {
            currentService = serviceName;
            serviceInput.value = serviceName;

            resetHospitalSelection();
            fetchHospitals(serviceName);
        }

        function resetServiceSelection() {
            currentService = null;
            serviceInput.value = '';
            hospitalSection.style.display = 'none';
        }

        // Start
        fetchCategories();

        async function fetchHospitals(service) {
            hospitalGrid.innerHTML = '<div style="text-align:center; color:#64748b;">Loading clinics...</div>';
            hospitalSection.style.display = 'block';

            try {
                const res = await fetch(`api_get_hospitals.php?service=${service}`);
                const data = await res.json();

                hospitalGrid.innerHTML = '';
                if (data.length === 0) {
                    hospitalGrid.innerHTML = '<div style="color:red;">No clinics found for this service.</div>';
                    return;
                }

                data.forEach(h => {
                    const card = document.createElement('div');
                    card.className = 'service-option'; // Reusing style for simplicity
                    card.style.display = 'flex';
                    card.style.alignItems = 'center';
                    card.style.gap = '1rem';
                    card.style.marginBottom = '0.5rem';
                    card.style.textAlign = 'left';

                    card.innerHTML = `
                        <img src="${h.image_url}" style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
                        <div style="flex:1;">
                            <div style="font-weight:700; color:#1e293b;">${h.name}</div>
                            <div style="font-size:0.8rem; color:#64748b;">${h.address}</div>
                        </div>
                        <div style="font-weight:700; color:#10b981;">₹${h.price}</div>
                    `;

                    card.addEventListener('click', () => {
                        // Highlight logic
                        document.querySelectorAll('#hospitalGrid > div').forEach(d => {
                            d.style.borderColor = '#e2e8f0';
                            d.style.background = 'white';
                        });
                        card.style.borderColor = '#3b82f6';
                        card.style.background = '#eff6ff';

                        selectHospital(h);
                    });

                    hospitalGrid.appendChild(card);
                });

            } catch (e) {
                console.error(e);
                hospitalGrid.innerHTML = 'Error loading clinics.';
            }
        }

        function selectHospital(h) {
            currentHospitalId = h.id;
            hospitalIdInput.value = h.id;
            priceInput.value = h.price;

            // Update UI
            totalPriceDisplay.textContent = '₹' + h.price;
            selectedClinicName.textContent = h.name;

            // Show next step
            step2.style.display = 'block';
            stepBadge.innerHTML = '<div class="step-dot"></div> Step 2: Time';

            // Trigger slot fetch if date already present
            if (dateInput.value) fetchSlots();
        }

        function resetHospitalSelection() {
            currentHospitalId = null;
            hospitalIdInput.value = '';
            hospitalGrid.innerHTML = '';
            step2.style.display = 'none';
            totalPriceDisplay.textContent = '₹0';
        }

        // 2. Date Selection -> Fetch Slots
        dateInput.addEventListener('change', fetchSlots);

        async function fetchSlots() {
            if (!currentHospitalId || !dateInput.value) return;

            timeSlotContainer.innerHTML = 'Loading...';

            try {
                const res = await fetch(`api_get_slots.php?hospital_id=${currentHospitalId}&date=${dateInput.value}`);
                const slots = await res.json();

                timeSlotContainer.innerHTML = '';

                if (slots.length === 0) {
                    timeSlotContainer.innerHTML = 'No slots available.';
                    return;
                }

                slots.forEach(slot => {
                    const div = document.createElement('div');
                    div.className = `time-slot ${slot.available ? '' : 'disabled'}`;
                    div.textContent = slot.display;

                    if (slot.available) {
                        div.addEventListener('click', () => {
                            document.querySelectorAll('.time-slot').forEach(s => s.classList.remove('active'));
                            div.classList.add('active');
                            timeInput.value = slot.time;
                        });
                    }

                    timeSlotContainer.appendChild(div);
                });

            } catch (e) {
                console.error(e);
            }
        }

    </script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.getElementById('btnContinue').onclick = function (e) {
            const form = document.getElementById('bookingForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const amount = parseInt(priceInput.value) * 100;
            if (isNaN(amount) || amount <= 0) {
                alert('Please select a service and clinic first.');
                return;
            }

            if ("<?php echo RAZORPAY_KEY_ID; ?>".indexOf('xxxx') !== -1) {
                alert('Razorpay API Key not configured in config.php');
                return;
            }

            var options = {
                "key": "<?php echo RAZORPAY_KEY_ID; ?>",
                "amount": amount,
                "currency": "INR",
                "name": "PetCloud",
                "description": "Appointment for " + (document.getElementById('petNameInput').value || 'Pet'),
                "image": "https://img.icons8.com/deco/600/dog.png",
                "handler": function (response) {
                    document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                    form.submit();
                },
                "prefill": {
                    "name": "<?php echo htmlspecialchars($user_name); ?>",
                    "email": "<?php echo $_SESSION['user_email'] ?? ''; ?>",
                    "contact": ""
                },
                "theme": { "color": "#3b82f6" }
            };
            var rzp1 = new Razorpay(options);
            rzp1.open();
            e.preventDefault();
        }
    </script>
</body>

</html>