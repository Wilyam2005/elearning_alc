<?php 
session_start();
if($_SESSION['role'] != 'admin'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

// TAMBAH KELAS
if(isset($_POST['tambah_kelas'])){
    $nama = $_POST['nama_kelas'];
    $tingkatan = $_POST['tingkatan']; // Input Paket
    mysqli_query($koneksi, "INSERT INTO kelas (nama_kelas, tingkatan) VALUES ('$nama', '$tingkatan')");
    echo "<script>alert('Kelas Berhasil Ditambahkan'); window.location='kelas.php';</script>";
}

// HAPUS KELAS
if(isset($_GET['hapus'])){
    mysqli_query($koneksi, "DELETE FROM kelas WHERE id='$_GET[hapus]'");
    echo "<script>window.location='kelas.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas & Paket</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #3b82f6; --dark: #0f172a; --bg: #f1f5f9; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        /* SIDEBAR TETAP UTUH */
        .sidebar { width: 260px; height: 100vh; background: var(--dark); color: white; position: fixed; overflow-y: auto; transition: 0.3s; z-index: 100; }
        .sidebar-brand { padding: 25px; font-weight: 800; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-header { padding: 20px 25px 10px; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 12px 25px; color: #cbd5e1; text-decoration: none; transition: 0.3s; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.1); color: white; border-left: 4px solid var(--primary); }
        .sidebar-menu i { width: 25px; text-align: center; margin-right: 10px; }

        .main-content { margin-left: 260px; padding: 30px; width: 100%; transition: 0.3s; }
        
        /* RESPONSIVE HP */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-content { margin-left: 0; padding: 20px; }
        }

        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); margin-bottom: 20px; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 8px; margin-bottom: 15px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; }
        
        /* TABEL RESPONSIF */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 500px; }
        th, td { padding: 12px; border-bottom: 1px solid #e2e8f0; text-align: left; font-size: 0.9rem; }
        
        /* Label Paket */
        .badge { padding: 5px 10px; border-radius: 50px; font-size: 0.7rem; font-weight: 700; }
        .pkt-a { background: #dcfce7; color: #166534; }
        .pkt-b { background: #dbeafe; color: #1e40af; }
        .pkt-c { background: #fce7f3; color: #be185d; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-shield-alt"></i> ADMIN PANEL</div>
        
        <div class="sidebar-header">Data Master</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Kelola User</a></li>
            <li><a href="kelas.php" class="active"><i class="fas fa-chalkboard"></i> Kelola Kelas</a></li>
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
        <h2>Manajemen Kelas Kesetaraan</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
            <div class="card">
                <h3>Tambah Kelas Baru</h3>
                <form method="POST">
                    <label style="font-size:0.9rem; font-weight:600;">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Contoh: Kelas 10" required>
                    
                    <label style="font-size:0.9rem; font-weight:600;">Jenjang / Paket</label>
                    <select name="tingkatan" class="form-control" required>
                        <option value="Paket A">Paket A (Setara SD)</option>
                        <option value="Paket B">Paket B (Setara SMP)</option>
                        <option value="Paket C">Paket C (Setara SMA)</option>
                    </select>
                    
                    <button type="submit" name="tambah_kelas" class="btn">Simpan Kelas</button>
                </form>
            </div>
            
            <div class="card table-responsive">
                <h3>Daftar Kelas Tersedia</h3>
                <table>
                    <thead><tr><th>Kelas</th><th>Paket</th><th>Aksi</th></tr></thead>
                    <tbody>
                        <?php 
                        $data = mysqli_query($koneksi, "SELECT * FROM kelas ORDER BY tingkatan ASC, nama_kelas ASC");
                        while($d = mysqli_fetch_array($data)){
                            $badge = ($d['tingkatan']=='Paket A')?'pkt-a':(($d['tingkatan']=='Paket B')?'pkt-b':'pkt-c');
                        ?>
                        <tr>
                            <td><b><?php echo $d['nama_kelas']; ?></b></td>
                            <td><span class="badge <?php echo $badge; ?>"><?php echo $d['tingkatan']; ?></span></td>
                            <td>
                                <a href="kelas.php?hapus=<?php echo $d['id']; ?>" style="color:red; font-size:0.85rem;" onclick="return confirm('Hapus Kelas ini?')"><i class="fas fa-trash"></i> Hapus</a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>