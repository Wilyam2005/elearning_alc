<?php 
session_start();
include 'config/koneksi.php';

// Cek session, jika sudah login lempar ke dashboard
if(isset($_SESSION['role'])){
    if($_SESSION['role'] == "admin"){ header("location:admin/dashboard.php"); }
    else if($_SESSION['role'] == "guru"){ header("location:guru/dashboard.php"); }
    else if($_SESSION['role'] == "siswa"){ header("location:siswa/dashboard.php"); }
}

// LOGIKA LOGIN
$pesan_error = "";
if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if(mysqli_num_rows($cek) > 0){
        $data = mysqli_fetch_assoc($cek);
        if(password_verify($password, $data['password'])){
            $_SESSION['id_user'] = $data['id'];
            $_SESSION['username'] = $username;
            $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
            $_SESSION['role'] = $data['role'];

            if($data['role']=="admin"){ header("location:admin/dashboard.php"); }
            else if($data['role']=="guru"){ header("location:guru/dashboard.php"); }
            else if($data['role']=="siswa"){ header("location:siswa/dashboard.php"); }
        } else { $pesan_error = "Password Salah!"; }
    } else { $pesan_error = "Username tidak ditemukan!"; }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ALC Learning</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            margin: 0; padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        /* --- SPLASH SCREEN --- */
        .splash-screen {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #0f172a;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease-out, visibility 0.5s;
        }
        
        .splash-logo {
            width: 120px; height: 120px;
            background: white; /* Background Putih agar Logo Jelas */
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 0 30px rgba(79, 70, 229, 0.6);
            animation: pulse 1.5s infinite;
            display: flex; align-items: center; justify-content: center;
        }
        
        .splash-logo img { width: 100%; object-fit: contain; }
        
        .splash-text {
            color: white; margin-top: 20px; font-weight: 700; font-size: 1.5rem; letter-spacing: 1px;
            animation: fadeIn 2s;
        }

        .splash-hidden { opacity: 0; visibility: hidden; }

        /* --- ANIMASI --- */
        @keyframes pulse {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7); }
            70% { transform: scale(1.05); box-shadow: 0 0 0 20px rgba(79, 70, 229, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* --- LOGIN CARD --- */
        .login-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 24px;
            width: 100%; max-width: 400px;
            text-align: center;
            opacity: 0; transform: scale(0.9);
            transition: all 0.5s ease-out;
        }
        
        .show-login { opacity: 1; transform: scale(1); }

        /* LOGO DI FORM (Background Putih agar jelas) */
        .logo-box {
            width: 90px; height: 90px;
            background: white; 
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            border: 2px solid rgba(255,255,255,0.2);
        }
        .logo-box img { width: 70%; object-fit: contain; }

        h2 { color: white; margin: 0 0 5px; font-size: 1.5rem; }
        p { color: #94a3b8; margin: 0 0 30px; font-size: 0.9rem; }

        .input-group { margin-bottom: 20px; text-align: left; position: relative; }
        .input-label { color: #cbd5e1; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; display: block; }
        
        .form-control {
            width: 100%; padding: 12px 15px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px; color: white; font-size: 1rem;
            outline: none; box-sizing: border-box; transition: 0.3s;
        }
        .form-control:focus { border-color: #4f46e5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); }

        .password-toggle { position: absolute; right: 15px; top: 38px; color: #94a3b8; cursor: pointer; }
        .password-toggle:hover { color: white; }

        .btn-login {
            width: 100%; padding: 14px;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            border: none; border-radius: 12px;
            color: white; font-weight: 700; font-size: 1rem;
            cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); }

        .forgot-link { display: block; text-align: right; margin-top: 10px; color: #64748b; font-size: 0.85rem; text-decoration: none; cursor: pointer; }
        .forgot-link:hover { color: #4f46e5; }

        .error-msg { background: rgba(220, 38, 38, 0.2); color: #fca5a5; padding: 10px; border-radius: 8px; font-size: 0.85rem; margin-bottom: 20px; border: 1px solid rgba(220, 38, 38, 0.3); }
    </style>
</head>
<body>

    <div class="splash-screen" id="splash">
        <div class="splash-logo">
            <img src="assets/img/logo%20rm%20bg.png" alt="ALC Logo">
        </div>
        <div class="splash-text">ALC Learning System</div>
        <div style="margin-top:10px; color:#64748b; font-size:0.9rem;">Memuat Aplikasi...</div>
    </div>

    <div class="login-card" id="loginCard">
        <div class="logo-box">
            <img src="assets/img/logo%20rm%20bg.png" alt="ALC Logo">
        </div>

        <h2>Assyuro ALC</h2>
        <p>Silakan login untuk melanjutkan</p>

        <?php if($pesan_error){ echo "<div class='error-msg'><i class='fas fa-exclamation-circle'></i> $pesan_error</div>"; } ?>

        <form method="POST">
            <div class="input-group">
                <label class="input-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autocomplete="off">
            </div>

            <div class="input-group">
                <label class="input-label">Password</label>
                <input type="password" name="password" id="passInput" class="form-control" placeholder="••••••••" required>
                <i class="fas fa-eye password-toggle" onclick="togglePassword()"></i>
            </div>

            <a onclick="lupaPassword()" class="forgot-link">Lupa Password?</a>

            <button type="submit" name="login" class="btn-login">Masuk Sekarang</button>
        </form>

        <div style="margin-top: 30px; font-size: 0.75rem; color: #475569;">
            &copy; 2026 ALC Learning System.
        </div>
    </div>

    <script>
        // Logika Splash Screen (3 Detik)
        window.addEventListener('load', function() {
            setTimeout(function() {
                document.getElementById('splash').classList.add('splash-hidden');
                document.getElementById('loginCard').classList.add('show-login');
            }, 2500); // Waktu splash screen (2.5 detik)
        });

        // Fitur Lihat Password
        function togglePassword() {
            var input = document.getElementById("passInput");
            var icon = document.querySelector(".password-toggle");
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }

        // Fitur Lupa Password (WA)
        function lupaPassword() {
            Swal.fire({
                title: 'Lupa Password?',
                html: "Silakan hubungi Admin untuk reset password.<br><br><b>Kontak Admin:</b>",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#25D366',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fab fa-whatsapp"></i> Chat Admin (WA)',
                cancelButtonText: 'Tutup'
            }).then((result) => {
                if (result.isConfirmed) {
                    var nomor = "6285934400903";
                    var pesan = "Halo Admin ALC, saya lupa password akun saya. Mohon bantuannya.";
                    window.open("https://wa.me/" + nomor + "?text=" + encodeURIComponent(pesan), "_blank");
                }
            })
        }
    </script>

</body>
</html>