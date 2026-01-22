<?php 
session_start();
if($_SESSION['role'] != 'admin'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

// TAMBAH USER
if(isset($_POST['tambah_user'])){
    $nama = $_POST['nama']; 
    $user = $_POST['username']; 
    $role = $_POST['role'];
    $kelas_id = ($role == 'siswa') ? $_POST['kelas_id'] : 'NULL'; 
    $pass = password_hash("123456", PASSWORD_DEFAULT); 
    
    if($kelas_id == 'NULL') {
        $query = "INSERT INTO users (username, password, nama_lengkap, role, kelas_id) VALUES ('$user','$pass','$nama','$role', NULL)";
    } else {
        $query = "INSERT INTO users (username, password, nama_lengkap, role, kelas_id) VALUES ('$user','$pass','$nama','$role', '$kelas_id')";
    }
    
    if(mysqli_query($koneksi, $query)){ echo "<script>alert('User Berhasil Ditambahkan'); window.location='users.php';</script>"; }
}

// HAPUS USER
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM users WHERE id='$_GET[hapus]'");
    echo "<script>window.location='users.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Users - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS GLOBAL ADMIN (Indigo Theme) */
        :root { --primary: #3b82f6; --dark: #0f172a; --bg: #f1f5f9; --text: #334155; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; color: var(--text); }
        
        /* SIDEBAR FIXED */
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; z-index: 100; overflow-y: auto; transition: 0.3s; }
        .sidebar-brand { padding: 25px; font-weight: 800; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); letter-spacing: 0.5px; }
        .sidebar-header { padding: 25px 25px 10px; font-size: 0.7rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 1px; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 25px; color: #cbd5e1; text-decoration: none; transition: 0.3s; font-size: 0.95rem; font-weight: 500; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid var(--primary); }
        .sidebar-menu i { width: 25px; text-align: center; margin-right: 10px; font-size: 1.1rem; }

        /* CONTENT */
        .main-content { margin-left: 260px; padding: 30px; width: 100%; transition: 0.3s; }
        h2 { margin-top: 0; font-size: 1.8rem; color: #1e293b; margin-bottom: 30px; }

        /* RESPONSIVE MOBILE */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 20px; }
        }

        /* CARDS & FORMS */
        .card { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; border: 1px solid #e2e8f0; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 15px; box-sizing: border-box; font-family: inherit; }
        .btn { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; transition: 0.2s; }
        .btn:hover { background: #2563eb; }

        /* TABLE */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; white-space: nowrap; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 700; border-bottom: 1px solid #e2e8f0; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        
        .badge { padding: 5px 10px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; }
        .bg-siswa { background: #dbeafe; color: #1e40af; }
        .bg-guru { background: #d1fae5; color: #065f46; }
        .bg-admin { background: #fee2e2; color: #991b1b; }
        
        .info-paket { font-size: 0.8rem; color: #64748b; font-weight: 600; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> ADMIN PANEL</div>
        
        <div class="sidebar-header">Data Master</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php" class="active"><i class="fas fa-users"></i> Kelola User</a></li>
            <li><a href="kelas.php"><i class="fas fa-chalkboard"></i> Kelola Kelas</a></li>
            <li><a href="mapel.php"><i class="fas fa-book"></i> Kelola Mapel</a></li>
        </ul>

        <div class="sidebar-header">Monitoring Pembelajaran</div>
        <ul class="sidebar-menu">
            <li><a href="mon_materi.php"><i class="fas fa-video"></i> Semua Materi</a></li>
            <li><a href="mon_tugas.php"><i class="fas fa-tasks"></i> Semua Tugas</a></li>
            <li><a href="mon_presensi.php"><i class="fas fa-clock"></i> Rekap Presensi</a></li>
        </ul>

        <div class="sidebar-header">Akun</div>
        <ul class="sidebar-menu">
            <li><a href="../logout.php" style="color:#ef4444;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h2>Manajemen Pengguna</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 25px;">
            <div class="card">
                <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:15px;">Registrasi User Baru</h3>
                <form method="POST">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                    
                    <label class="form-label">Username (Untuk Login)</label>
                    <input type="text" name="username" class="form-control" placeholder="Username tanpa spasi" required>
                    
                    <label class="form-label">Role (Peran)</label>
                    <select name="role" class="form-control" id="roleSelect" onchange="toggleKelas()">
                        <option value="siswa">Siswa (Warga Belajar)</option>
                        <option value="guru">Guru (Tutor)</option>
                        <option value="admin">Admin</option>
                    </select>
                    
                    <div id="kelasArea">
                        <label class="form-label" style="color:var(--primary);">Pilih Kelas & Paket (Wajib untuk Siswa)</label>
                        <select name="kelas_id" class="form-control">
                            <option value="">-- Pilih Kelas --</option>
                            <?php 
                            $kelas = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY tingkatan ASC");
                            while($k = mysqli_fetch_array($kelas)){
                                echo "<option value='".$k['id']."'>".$k['tingkatan']." - ".$k['nama_kelas']."</option>";
                            }
                            ?>
                        </select>
                    </div>
                    
                    <button type="submit" name="tambah_user" class="btn">Simpan User</button>
                </form>
            </div>
            
            <div class="card table-responsive">
                <h3 style="margin-top:0; border-bottom:1px solid #eee; padding-bottom:15px;">Daftar Pengguna</h3>
                <table>
                    <thead><tr><th>Nama Lengkap</th><th>Role</th><th>Info Kelas</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php 
                        $users = mysqli_query($koneksi, "SELECT users.*, kelas.nama_kelas, kelas.tingkatan FROM users LEFT JOIN kelas ON users.kelas_id = kelas.id ORDER BY users.id DESC");
                        while($u = mysqli_fetch_array($users)){
                        ?>
                        <tr>
                            <td>
                                <b><?php echo $u['nama_lengkap']; ?></b><br>
                                <span style="font-size:0.8rem; color:#94a3b8;">@<?php echo $u['username']; ?></span>
                            </td>
                            <td>
                                <?php if($u['role']=='siswa'){ echo "<span class='badge bg-siswa'>SISWA</span>"; }
                                      elseif($u['role']=='guru'){ echo "<span class='badge bg-guru'>GURU</span>"; }
                                      else { echo "<span class='badge bg-admin'>ADMIN</span>"; } ?>
                            </td>
                            <td>
                                <?php 
                                if($u['role'] == 'siswa' && $u['nama_kelas']){
                                    echo "<div class='info-paket'>".$u['tingkatan']."</div>";
                                    echo "<small>".$u['nama_kelas']."</small>";
                                } else { echo "-"; }
                                ?>
                            </td>
                            <td>
                                <a href="users.php?hapus=<?php echo $u['id']; ?>" style="color:#ef4444; font-size:1.1rem;" onclick="return confirm('Hapus User ini?')"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function toggleKelas() {
            var role = document.getElementById("roleSelect").value;
            var kelasArea = document.getElementById("kelasArea");
            if(role === 'siswa') { kelasArea.style.display = 'block'; } 
            else { kelasArea.style.display = 'none'; }
        }
        toggleKelas(); // Jalankan saat load
    </script>
</body>
</html>