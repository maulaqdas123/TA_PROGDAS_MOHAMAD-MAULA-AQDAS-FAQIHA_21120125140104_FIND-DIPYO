<?php

// TIPE DATA: Variabel untuk pesan error (String)
$errorMessage = '';

// PENGKONDISIAN: Memeriksa apakah form login disubmit (Function)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // PENGKONDISIAN & FUNCTION: Cek validasi sederhana
    // Aturan: Username diisi kata, Password (NIM) diisi angka.
    $isUsernameValid = !empty($username) && !is_numeric($username);
    $isPasswordValid = is_numeric($password);

    if ($isUsernameValid && $isPasswordValid) {
        $nim = $password; 
        // FUNCTION: Redirect ke dashboard
        header("Location: BusTracker.php?user_id={$nim}");
        exit();
    } else {
        $errorMessage = 'Login Gagal. Pastikan Username diisi kata dan Password (NIM) diisi angka.';
    }
}
?>
<!DOCTYPE html>
<html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Login - Dipyo Undip Tracker</title>
<!-- GUI: CSS Internal yang dirampingkan -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
:root{--primary-color:#003366;--secondary-color:#ffd700;--bg-dark:#121212;--card-dark:#1f1f1f;--text-light:#f3f4f6;--danger:#ef4444;}
*{box-sizing:border-box;font-family:'Inter',sans-serif;}
body{background-color:var(--bg-dark);color:var(--text-light);margin:0;padding:0;display:flex;justify-content:center;align-items:center;min-height:100vh;}
.login-container{background-color:var(--card-dark);padding:3.5rem;border-radius:20px;box-shadow:0 15px 50px rgba(0,0,0,.9);width:90%;max-width:450px;text-align:center;border:1px solid #333;z-index:1;}
.login-container h1{color:var(--secondary-color);margin-bottom:.2rem;font-weight:700;font-size:2.8rem;}
.login-container p{margin-bottom:2.5rem;color:#aaa;font-size:1rem;}
.form-group{margin-bottom:1.8rem;text-align:left;}
.form-group label{display:block;margin-bottom:.6rem;font-weight:600;color:var(--text-light);}
.form-group input{width:100%;padding:1.2rem;border:1px solid #444;border-radius:12px;background-color:#2c2c2c;color:var(--text-light);font-size:1rem;transition:border-color .3s;}
.form-group input:focus{border-color:var(--secondary-color);outline:none;}
.btn-login{width:100%;padding:1.3rem;border:none;border-radius:12px;background-color:var(--primary-color);color:var(--text-light);font-size:1.3rem;font-weight:700;cursor:pointer;transition:background-color .3s,transform .2s;margin-top:1.5rem;}
.btn-login:hover{background-color:#004d99;transform:translateY(-2px);}
.error-message{margin-top:1.5rem;padding:1rem;background-color:var(--danger);color:var(--text-light);border-radius:8px;font-weight:600;}
.info-text{margin-top:2.5rem;font-size:.85rem;color:#888;padding-top:1rem;border-top:1px solid #333;}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
<div class="login-container">
    <!-- GUI: Struktur utama -->
    <h1>Dipyo Tracker</h1>
    <p>Sistem Pemantauan Bus Kampus Undip</p>

    <?php
    // GUI: Menampilkan pesan error (PENGKONDISIAN)
    if (!empty($errorMessage)) {
        echo "<div class='error-message'>{$errorMessage}</div>";
    }
    ?>

    <form method="POST" action="index.php">
        <div class="form-group">
            <label for="username"><i class="fas fa-user-circle" style="margin-right: 8px;"></i> Username</label>
            <input type="text" id="username" name="username" required placeholder="Contoh: MahasiswaUndip">
        </div>
        <div class="form-group">
            <label for="password"><i class="fas fa-lock" style="margin-right: 8px;"></i> Password (NIM)</label>
            <input type="password" id="password" name="password" required placeholder="Contoh: 120221001">
        </div>
        <button type="submit" class="btn-login">MASUK</button>
    </form>

    <p class="info-text">
        *Untuk uji coba: Username harus kata (teks), Password (NIM) harus angka.
    </p>
</div>
</body>
</html>