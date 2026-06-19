<?php
session_start();

$password_correct = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['password'] === $password_correct) {
        $_SESSION['admin_login'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Password salah!";
    }
}

if (isset($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login Kasir - NyamMeow</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #FF8C42, #f7c56e);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            width: 350px;
        }
        .login-box h2 { color: #FF8C42; margin-bottom: 20px; }
        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
        }
        .login-box button {
            background: #FF8C42;
            color: white;
            padding: 12px;
            width: 100%;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .login-box button:hover { background: #E6781A; }
        .error { color: red; margin-bottom: 10px; }
        .back-link { display: block; margin-top: 15px; color: #666; text-decoration: none; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🐱 Login Kasir NyamMeow</h2>
        <?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Masukkan Password" required>
            <button type="submit">🔐 Login</button>
        </form>
        <a href="../index.php" class="back-link">← Kembali ke Halaman Pelanggan</a>
    </div>
</body>
</html>
