<?php 
session_start(); // Mulai sesi
session_destroy(); // Hapus semua data sesi (login)
header("location:index.php?pesan=logout"); // Kembalikan ke halaman login utama
?>