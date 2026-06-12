<?php
// Week 4: Login System with PHP Sessions & Secure Password Storage
// BIT3208 - Advanced Web Design and Development

session_start();
require_once './includes/db.php';

$error = '';
$success = '';

// ==============================
// HANDLE LOGIN FORM SUBMISSION
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Fetch user by email using prepared statement (prevents SQL injection)
        $stmt = mysqli_prepare($conn, "SELECT id, username, email, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = mysqli_fetch_assoc($result);

        // Verify password using password_verify() — works with password_hash()
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}

// ==============================
// HANDLE REGISTRATION
// ==============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['reg_username']);
    $email    = trim($_POST['reg_email']);
    $password = $_POST['reg_password'];
    $confirm  = $_POST['reg_confirm'];

    if (empty($username) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password securely
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'customer')");
        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);

        if (mysqli_stmt_execute($stmt)) {
            $success = "Account created! You can now login.";
        } else {
            $error = "Email already exists or registration failed.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login System</title>
    <style>
        :root {
            --primary: #1a3c5e;
            --accent: #e74c3c;
            --success: #27ae60;
            --light: #f0f4f8;
            --border: #bdc3c7;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a3c5e 0%, #2980b9 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .week-label {
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .logo {
            color: white;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 30px;
            text-align: center;
        }

        .auth-box {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            overflow: hidden;
        }

        /* TABS */
        .tabs { display: flex; }
        .tab {
            flex: 1;
            padding: 16px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            background: var(--light);
            color: #7f8c8d;
            border: none;
            transition: all 0.2s;
        }
        .tab.active {
            background: white;
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }

        .tab-content { padding: 30px; display: none; }
        .tab-content.active { display: block; }

        /* FORM */
        h3 { color: var(--primary); margin-bottom: 20px; font-size: 1.1rem; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; color: #555; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid var(--border);
            border-radius: 7px;
            font-size: 14px;
            transition: border-color 0.2s;
            outline: none;
        }
        .form-group input:focus { border-color: var(--primary); }

        .btn {
            width: 100%;
            padding: 13px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 5px;
            transition: background 0.2s, transform 0.15s;
        }
        .btn:hover { background: #16344f; transform: translateY(-1px); }

        .alert {
            padding: 12px 15px;
            border-radius: 7px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .alert-error { background: #fdecea; color: #c0392b; border: 1px solid #e74c3c; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #27ae60; }

        /* SECURITY NOTES */
        .security-note {
            background: var(--light);
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 12px;
            color: #555;
            margin-top: 15px;
            border-left: 3px solid var(--primary);
        }
        .security-note strong { color: var(--primary); }

        footer {
            color: rgba(255,255,255,0.6);
            font-size: 12px;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="week-label"></div>
<div class="logo">🛒 ShopSmart</div>

<div class="auth-box">
    <div class="tabs">
        <button class="tab active" onclick="switchTab('login')">Login</button>
        <button class="tab" onclick="switchTab('register')">Register</button>
    </div>

    <!-- LOGIN TAB -->
    <div class="tab-content active" id="loginTab">
        <h3>Welcome back!</h3>

        <?php if ($error && !isset($_POST['action']) || (isset($_POST['action']) && $_POST['action'] === 'login' && $error)): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Your password" required>
            </div>
            <button type="submit" class="btn">Login to Account</button>
        </form>
    </div>

    <!-- REGISTER TAB -->
    <div class="tab-content" id="registerTab">
        <h3>Create an Account</h3>

        <?php if ($error && isset($_POST['action']) && $_POST['action'] === 'register'): ?>
            <div class="alert alert-error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="action" value="register">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="reg_username" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="reg_email" placeholder="you@example.com" required>
            </div>
            <div class="form-group">
                <label>Password (min. 8 characters)</label>
                <input type="password" name="reg_password" placeholder="Create a strong password" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="reg_confirm" placeholder="Repeat your password" required>
            </div>
            <button type="submit" class="btn">Create Account</button>
        </form>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById(tab + 'Tab').classList.add('active');
        event.target.classList.add('active');
    }
</script>

</body>
</html>
