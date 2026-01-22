<?php 
session_start();
if($_SESSION['role'] != 'guru'){ header("location:../index.php"); exit; }
include '../config/koneksi.php';

// --- LOGIKA SIMPAN SOAL ---
if(isset($_POST['simpan'])){
    $mapel = $_POST['mapel_id']; 
    $tipe = $_POST['tipe_soal']; // Tangkap tipe soal
    $tanya = mysqli_real_escape_string($koneksi, $_POST['pertanyaan']);
    
    // Default kosong untuk essay
    $a = "-"; $b = "-"; $c = "-"; $d = "-"; $kunci = "-";

    // Jika Pilihan Ganda, baru ambil data opsi
    if($tipe == 'pg'){
        $a = mysqli_real_escape_string($koneksi, $_POST['opsi_a']);
        $b = mysqli_real_escape_string($koneksi, $_POST['opsi_b']);
        $c = mysqli_real_escape_string($koneksi, $_POST['opsi_c']);
        $d = mysqli_real_escape_string($koneksi, $_POST['opsi_d']);
        $kunci = $_POST['jawaban_benar'];
    }
    
    $query = "INSERT INTO soal_quiz (mapel_id, tipe_soal, pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, jawaban_benar) 
              VALUES ('$mapel', '$tipe', '$tanya', '$a', '$b', '$c', '$d', '$kunci')";
    
    if(mysqli_query($koneksi, $query)){
        echo "<script>alert('Soal berhasil disimpan!'); window.location='soal.php';</script>";
    }
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus']; 
    mysqli_query($koneksi, "DELETE FROM soal_quiz WHERE id='$id'"); 
    echo "<script>window.location='soal.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Soal & Ujian</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* CSS INDIGO THEME (Sama dengan halaman lain) */
        :root { --primary: #4f46e5; --sidebar-bg: #1e1b4b; --bg-body: #f3f4f6; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-body); margin: 0; display: flex; }
        
        /* SIDEBAR */
        .sidebar { width: 260px; height: 100vh; background: var(--sidebar-bg); color: white; position: fixed; top: 0; left: 0; z-index: 100; transition:0.3s; }
        .sidebar-brand { padding: 30px; font-size: 1.2rem; font-weight: 800; background: rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px; }
        .sidebar-menu { list-style: none; padding: 20px 15px; margin: 0; }
        .sidebar-menu a { display: flex; align-items: center; padding: 14px 20px; color: #a5b4fc; text-decoration: none; border-radius: 12px; transition: 0.3s; font-weight: 500; margin-bottom: 5px; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: var(--primary); color: white; transform: translateX(5px); box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); }
        .sidebar-menu i { width: 25px; font-size: 1.1rem; }

        /* CONTENT */
        .main-content { margin-left: 260px; padding: 40px; width: 100%; min-height: 100vh; transition:0.3s; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); } .main-content { margin-left: 0; padding: 20px; } }

        /* CARD & FORM */
        .card { background: white; border-radius: 20px; padding: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 30px; border: 1px solid rgba(0,0,0,0.05); }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #4b5563; font-size: 0.9rem; }
        .form-input { width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; box-sizing: border-box; transition: 0.3s; font-family: inherit; }
        .form-input:focus { border-color: var(--primary); outline: none; }
        .btn-primary { width: 100%; padding: 12px; background: var(--primary); color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3); }
        
        /* ITEM SOAL */
        .question-item { background: white; border-radius: 12px; padding: 20px; margin-bottom: 15px; border: 1px solid #e5e7eb; transition:0.3s; position: relative; }
        .question-item:hover { border-color: var(--primary); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        /* SWITCH TIPE SOAL */
        .type-switch { display: flex; gap: 15px; margin-bottom: 20px; }
        .type-label { 
            flex: 1; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; 
            text-align: center; cursor: pointer; font-weight: 600; color: #6b7280; transition: 0.2s;
        }
        .type-radio { display: none; }
        .type-radio:checked + .type-label { 
            border-color: var(--primary); background: #eef2ff; color: var(--primary); 
        }

        .badge-essay { background: #fce7f3; color: #be185d; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .badge-pg { background: #dbeafe; color: #1e40af; padding: 4px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; }
        .info-paket { font-size:0.8rem; font-weight:700; color:#4f46e5; background:#e0e7ff; padding:2px 8px; border-radius:4px; margin-left:5px; }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-rocket"></i> ALC TEACHER</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li><a href="materi.php"><i class="fas fa-play-circle"></i> Video Materi</a></li>
            <li><a href="tugas.php"><i class="fas fa-clipboard-check"></i> Tugas & Ujian</a></li>
            <li><a href="soal.php" class="active"><i class="fas fa-file-alt"></i> Bank Soal</a></li>
            <li><a href="presensi.php"><i class="fas fa-clock"></i> Presensi</a></li>
            <li style="margin-top: 30px;"><a href="../logout.php" style="color:#f87171;"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <h1 style="font-size: 1.8rem; font-weight: 800; color: #1f2937; margin-bottom: 30px;">Bank Soal Ujian</h1>
        
        <div class="card">
            <h3 style="margin-top:0; margin-bottom:20px;">Input Soal Baru</h3>
            <form method="POST">
                
                <div class="form-label">Pilih Tipe Soal:</div>
                <div class="type-switch">
                    <label style="flex:1;">
                        <input type="radio" name="tipe_soal" value="pg" class="type-radio" checked onclick="toggleType('pg')">
                        <div class="type-label"><i class="fas fa-list-ul"></i> Pilihan Ganda</div>
                    </label>
                    <label style="flex:1;">
                        <input type="radio" name="tipe_soal" value="essay" class="type-radio" onclick="toggleType('essay')">
                        <div class="type-label"><i class="fas fa-align-left"></i> Essay / Uraian</div>
                    </label>
                </div>

                <div style="margin-bottom:15px;">
                    <label class="form-label">Mata Pelajaran & Paket</label>
                    <select name="mapel_id" class="form-input" required>
                        <?php 
                        // UPDATE LOGIC: Tampilkan SEMUA Mapel (Single Teacher Mode)
                        // Diurutkan berdasarkan Paket (Tingkatan) lalu Nama Mapel
                        $mapel = mysqli_query($koneksi, "SELECT * FROM mapel ORDER BY tingkatan ASC, nama_mapel ASC");
                        while($m = mysqli_fetch_array($mapel)){ 
                            echo "<option value='".$m['id']."'>".$m['nama_mapel']." [".$m['tingkatan']."]</option>"; 
                        }
                        ?>
                    </select>
                </div>

                <div style="margin-bottom:15px;">
                    <label class="form-label">Pertanyaan</label>
                    <textarea name="pertanyaan" class="form-input" rows="3" placeholder="Tulis pertanyaan soal disini..." required></textarea>
                </div>

                <div id="area-pg">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                        <div><label class="form-label">Opsi A</label><input type="text" name="opsi_a" class="form-input" placeholder="Jawaban A"></div>
                        <div><label class="form-label">Opsi B</label><input type="text" name="opsi_b" class="form-input" placeholder="Jawaban B"></div>
                        <div><label class="form-label">Opsi C</label><input type="text" name="opsi_c" class="form-input" placeholder="Jawaban C"></div>
                        <div><label class="form-label">Opsi D</label><input type="text" name="opsi_d" class="form-input" placeholder="Jawaban D"></div>
                    </div>
                    <div style="margin-top:15px;">
                        <label class="form-label">Kunci Jawaban</label>
                        <select name="jawaban_benar" class="form-input">
                            <option value="A">A</option><option value="B">B</option><option value="C">C</option><option value="D">D</option>
                        </select>
                    </div>
                </div>

                <button type="submit" name="simpan" class="btn-primary">Simpan Soal</button>
            </form>
        </div>

        <h3 style="color:#4b5563;">Daftar Soal Tersimpan</h3>
        <?php 
        // Cek tabel soal ada atau tidak
        $cek_tabel = mysqli_query($koneksi, "SHOW TABLES LIKE 'soal_quiz'");
        if(mysqli_num_rows($cek_tabel) > 0){
            
            // Query Tampilkan Soal + Info Paket (JOIN Mapel)
            $query = "SELECT soal_quiz.*, mapel.nama_mapel, mapel.tingkatan 
                      FROM soal_quiz 
                      JOIN mapel ON soal_quiz.mapel_id = mapel.id 
                      ORDER BY soal_quiz.id DESC";
            
            $data = mysqli_query($koneksi, $query);
            while($d = mysqli_fetch_array($data)){
                // Cek tipe soal
                $tipe = isset($d['tipe_soal']) ? $d['tipe_soal'] : 'pg'; 
        ?>
        <div class="question-item">
            <div style="display:flex; justify-content:space-between; align-items:start;">
                <div>
                    <span style="font-weight:700; color:#1f2937;">
                        <?php echo $d['nama_mapel']; ?>
                        <span class="info-paket"><?php echo $d['tingkatan']; ?></span>
                    </span>
                    
                    <div style="margin-top:5px;">
                        <?php if($tipe == 'essay'){ ?>
                            <span class="badge-essay">ESSAY</span>
                        <?php } else { ?>
                            <span class="badge-pg">PILIHAN GANDA</span>
                        <?php } ?>
                    </div>
                </div>
                <a href="soal.php?hapus=<?php echo $d['id']; ?>" onclick="return confirm('Hapus Soal ini?')" style="color:#ef4444;"><i class="fas fa-trash-alt"></i></a>
            </div>
            
            <p style="margin:15px 0; color:#374151; font-size:1.05rem; white-space: pre-line;"><?php echo $d['pertanyaan']; ?></p>
            
            <?php if($tipe == 'pg') { ?>
                <div style="font-size:0.9rem; color:#6b7280; display:grid; grid-template-columns: 1fr 1fr; gap:8px; background:#f9fafb; padding:15px; border-radius:10px;">
                    <div><b>A.</b> <?php echo $d['opsi_a']; ?></div>
                    <div><b>B.</b> <?php echo $d['opsi_b']; ?></div>
                    <div><b>C.</b> <?php echo $d['opsi_c']; ?></div>
                    <div><b>D.</b> <?php echo $d['opsi_d']; ?></div>
                </div>
                <div style="margin-top:10px; font-weight:700; color:#059669; font-size:0.9rem;">
                    <i class="fas fa-key"></i> Kunci Jawaban: <?php echo $d['jawaban_benar']; ?>
                </div>
            <?php } ?>
        </div>
        <?php 
            } // end while
        } else {
            echo "<div style='padding:20px; background:#fee2e2; color:#991b1b; border-radius:10px;'>Error: Tabel soal_quiz belum dibuat di database.</div>";
        }
        ?>
    </div>

    <script>
        function toggleType(type) {
            const areaPG = document.getElementById('area-pg');
            const inputsPG = areaPG.querySelectorAll('input, select');

            if (type === 'essay') {
                areaPG.style.display = 'none';
                // Matikan required agar form bisa disubmit tanpa isi opsi
                inputsPG.forEach(input => input.removeAttribute('required'));
            } else {
                areaPG.style.display = 'block';
                // Hidupkan required kembali
                inputsPG.forEach(input => input.setAttribute('required', ''));
            }
        }
    </script>

</body>
</html>