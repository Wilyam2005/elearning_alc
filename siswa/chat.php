<?php 
session_start();
include '../config/koneksi.php';
$id_siswa = $_SESSION['id_user'];

// Kirim Pesan
if(isset($_POST['kirim'])){
    $pesan = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);
    // Kita set default kelas_id = 1 untuk demo, atau ambil dari data siswa jika ada
    $kelas_id = 1; 
    mysqli_query($koneksi, "INSERT INTO pesan_kelas (kelas_id, user_id, isi_pesan) VALUES ('$kelas_id', '$id_siswa', '$pesan')");
    header("Location: chat.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Forum Kelas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #4f46e5; --bg: #f3f4f6; --sidebar: #ffffff; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; height: 100vh; overflow: hidden; }
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar); position: fixed; border-right: 1px solid #e5e7eb; }
        .brand { padding: 25px; font-size: 1.4rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .menu { list-style: none; padding: 10px 20px; }
        .menu a { display: flex; align-items: center; padding: 12px 15px; color: #6b7280; text-decoration: none; border-radius: 10px; font-weight: 500; margin-bottom: 5px; }
        .menu a:hover, .menu a.active { background: #eef2ff; color: var(--primary); }
        .menu i { width: 25px; font-size: 1.1rem; }
        
        .main { margin-left: 260px; width: calc(100% - 260px); display: flex; flex-direction: column; height: 100vh; }
        
        /* CHAT AREA */
        .chat-header { padding: 20px 30px; background: white; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .chat-box { flex: 1; padding: 30px; overflow-y: auto; background: #f9fafb; display: flex; flex-direction: column; gap: 15px; }
        
        .chat-bubble { max-width: 70%; padding: 15px; border-radius: 15px; position: relative; font-size: 0.95rem; line-height: 1.5; }
        .chat-bubble .sender { font-size: 0.75rem; font-weight: 700; margin-bottom: 5px; display: block; }
        .chat-bubble .time { font-size: 0.7rem; opacity: 0.7; float: right; margin-top: 5px; margin-left: 10px; }
        
        /* Pesan Orang Lain */
        .others { align-self: flex-start; background: white; border: 1px solid #e5e7eb; border-top-left-radius: 0; }
        .others .sender { color: var(--primary); }
        
        /* Pesan Saya */
        .me { align-self: flex-end; background: var(--primary); color: white; border-top-right-radius: 0; }
        .me .sender { color: #e0e7ff; }
        
        .input-area { padding: 20px; background: white; border-top: 1px solid #e5e7eb; }
        .input-form { display: flex; gap: 10px; }
        .input-msg { flex: 1; padding: 15px; border: 2px solid #e5e7eb; border-radius: 10px; outline: none; }
        .input-msg:focus { border-color: var(--primary); }
        .btn-send { background: var(--primary); color: white; border: none; padding: 0 25px; border-radius: 10px; cursor: pointer; font-size: 1.2rem; transition: 0.2s; }
        .btn-send:hover { background: #4338ca; transform: scale(1.05); }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><i class="fas fa-graduation-cap"></i> ALC STUDENT</div>
        <ul class="menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-book-open"></i> Materi Belajar</a></li>
            <li><a href="tugas.php"><i class="fas fa-tasks"></i> Tugas & Ujian</a></li>
            <li><a href="chat.php" class="active"><i class="fas fa-comments"></i> Forum Kelas</a></li>
            <li><a href="profil.php"><i class="fas fa-user-circle"></i> Profil Saya</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main">
        <div class="chat-header">
            <h2 style="margin:0; color:#1f2937;">Diskusi Kelas Umum</h2>
            <button onclick="location.reload()" style="border:none; background:none; color:var(--primary); cursor:pointer;"><i class="fas fa-sync"></i> Refresh Chat</button>
        </div>
        
        <div class="chat-box" id="chatContainer">
            <?php 
            // Ambil pesan, join dengan tabel user untuk dapat nama pengirim
            $query = "SELECT pesan_kelas.*, users.nama_lengkap 
                      FROM pesan_kelas 
                      JOIN users ON pesan_kelas.user_id = users.id 
                      ORDER BY waktu ASC";
            $data = mysqli_query($koneksi, $query);
            while($d = mysqli_fetch_array($data)){
                // Cek apakah pesan sendiri atau orang lain
                $class = ($d['user_id'] == $id_siswa) ? 'me' : 'others';
            ?>
            <div class="chat-bubble <?php echo $class; ?>">
                <span class="sender"><?php echo $d['nama_lengkap']; ?></span>
                <?php echo $d['isi_pesan']; ?>
                <span class="time"><?php echo date('H:i', strtotime($d['waktu'])); ?></span>
            </div>
            <?php } ?>
        </div>

        <div class="input-area">
            <form method="POST" class="input-form">
                <input type="text" name="isi_pesan" class="input-msg" placeholder="Ketik pesan untuk teman sekelas..." required autocomplete="off">
                <button type="submit" name="kirim" class="btn-send"><i class="fas fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <script>
        // Auto scroll ke bawah
        var chatBox = document.getElementById("chatContainer");
        chatBox.scrollTop = chatBox.scrollHeight;
    </script>
</body>
</html>