<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; 

    // Mencocokkan akun petugas admin Resto
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_nama'] = $username; 
        header("Location: index.php");
        exit;
    } else {
        $error = "Akun tidak terdaftar atau password keliru meow~";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - NyamMeow</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .login-box { max-width: 400px; margin: 80px auto; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); text-align: center; }
        .form-input { width: 100%; padding: 11px; margin: 10px 0; border: 1px solid #ccc; border-radius: 8px; font-size: 14px; }
        .btn-login { width: 100%; padding: 11px; background: #FF8C42; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🐱 Admin Sign In 🐱</h2>
        <p style="font-size: 13px; color: #666; margin-bottom: 20px;">Silakan masukkan akun kasir yang bertugas shift hari ini</p>
        
        <?php if(isset($error)): ?>
            <p style="color: red; font-size: 13px; margin-bottom: 10px;">⚠️ <?= $error ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Masukkan Username" class="form-input" required>
            <input type="password" name="password" placeholder="Masukkan Password" class="form-input" required>
            <button type="submit" name="login" class="btn-login">Masuk ke Dashboard</button>
        </form>
    </div>
</body>
</html>