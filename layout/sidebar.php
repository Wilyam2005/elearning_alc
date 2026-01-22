<nav class="sidebar p-3 d-none d-md-block" style="width: 280px; min-height:100vh;">
            <div class="d-flex align-items-center mb-4 px-2">
                <i class="fas fa-graduation-cap fa-2x text-primary me-2"></i>
                <h4 class="m-0 fw-bold text-primary">ALC Learning</h4>
            </div>
            
            <div class="profile-section mb-4 text-center p-3 bg-light rounded-3">
                <img src="../assets/img/default_profile.jpg" class="rounded-circle mb-2" width="60" height="60" style="object-fit:cover;">
                <h6 class="m-0 fw-bold"><?php echo $_SESSION['nama_lengkap']; ?></h6>
                <small class="text-muted text-uppercase"><?php echo $_SESSION['role']; ?></small>
            </div>

            <ul class="nav flex-column">
                
                <?php if($_SESSION['role'] == 'admin'){ ?>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="users.php" class="nav-link"><i class="fas fa-users me-2"></i> Kelola User</a>
                    </li>
                    <li class="nav-item">
                        <a href="kelas.php" class="nav-link"><i class="fas fa-chalkboard me-2"></i> Kelola Kelas</a>
                    </li>
                    <li class="nav-item">
                        <a href="mapel.php" class="nav-link"><i class="fas fa-book me-2"></i> Kelola Mapel</a>
                    </li>
                    <li class="nav-item">
                        <a href="setting.php" class="nav-link"><i class="fas fa-cog me-2"></i> Pengaturan</a>
                    </li>
                
                <?php } elseif($_SESSION['role'] == 'guru'){ ?>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="materi.php" class="nav-link"><i class="fas fa-video me-2"></i> Materi & Video</a>
                    </li>
                    <li class="nav-item">
                        <a href="tugas.php" class="nav-link"><i class="fas fa-tasks me-2"></i> Tugas Siswa</a>
                    </li>
                    <li class="nav-item">
                        <a href="soal.php" class="nav-link"><i class="fas fa-edit me-2"></i> Bank Soal</a>
                    </li>
                    <li class="nav-item">
                        <a href="presensi.php" class="nav-link"><i class="fas fa-calendar-check me-2"></i> Presensi</a>
                    </li>
                    <li class="nav-item">
                        <a href="nilai.php" class="nav-link"><i class="fas fa-star me-2"></i> Input Nilai</a>
                    </li>

                <?php } elseif($_SESSION['role'] == 'siswa'){ ?>
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active"><i class="fas fa-home me-2"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="kelas_saya.php" class="nav-link"><i class="fas fa-chalkboard-teacher me-2"></i> Kelas Saya</a>
                    </li>
                    <li class="nav-item">
                        <a href="tugas_saya.php" class="nav-link"><i class="fas fa-clipboard-list me-2"></i> Tugas & Ujian</a>
                    </li>
                    <li class="nav-item">
                        <a href="presensi_saya.php" class="nav-link"><i class="fas fa-user-check me-2"></i> Isi Presensi</a>
                    </li>
                <?php } ?>

                <hr>
                <li class="nav-item">
                    <a href="../logout.php" class="nav-link text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
                </li>
            </ul>
        </nav>
        
        <div class="w-100 bg-light">
            <nav class="navbar navbar-expand-lg navbar-light bg-white d-md-none shadow-sm px-3">
                <span class="navbar-brand mb-0 h1 text-primary">ALC Mobile</span>
                <a href="../logout.php" class="btn btn-sm btn-danger ms-auto">Logout</a>
            </nav>
            
            <div class="main-content">