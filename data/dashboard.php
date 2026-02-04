<?php
session_start();
require_once "../config/database.php";

// Cek Login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Dashboard Admin";
$deskripsi_halaman = "Ringkasan statistik";
$active_menu = 'dashboard';

// --- QUERY DATA ---
// 1. Total Siswa (Gabungan 3 Tabel)
$q_siswa = mysqli_query($conn, "SELECT COUNT(*) FROM siswa_kelas10 UNION ALL SELECT COUNT(*) FROM siswa_kelas11 UNION ALL SELECT COUNT(*) FROM siswa_kelas12");
$jml_siswa = 0;
while ($row = mysqli_fetch_array($q_siswa)) {
    $jml_siswa += $row[0];
}

// 2. Total Guru
$q_guru = mysqli_query($conn, "SELECT COUNT(*) as jum FROM guru");
$d_guru = mysqli_fetch_assoc($q_guru);
$jml_guru = $d_guru['jum'];

// 3. Total Kelas
$q_kelas = mysqli_query($conn, "SELECT COUNT(*) as jum FROM kelas");
$d_kelas = mysqli_fetch_assoc($q_kelas);
$jml_kelas = $d_kelas['jum'];

// 4. Total Mapel
$q_mapel = mysqli_query($conn, "SELECT COUNT(*) as jum FROM mapel");
$d_mapel = mysqli_fetch_assoc($q_mapel);
$jml_mapel = $d_mapel['jum'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/data.css">
</head>

<body>

    <?php include "../layout/sidebar.php"; ?>

    <div class="main-content">

        <?php include "../layout/topbar.php"; ?>

        <div class="cards-grid">

            <div class="stat-card">
                <div class="icon-box blue">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="info-box">
                    <h3>Total Siswa</h3>
                    <h2><?= $jml_siswa; ?></h2>
                </div>
            </div>

            <div class="stat-card green">
                <div class="icon-box green">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="info-box">
                    <h3>Total Guru</h3>
                    <h2><?= $jml_guru; ?></h2>
                </div>
            </div>

            <div class="stat-card orange">
                <div class="icon-box orange">
                    <i class="fas fa-school"></i>
                </div>
                <div class="info-box">
                    <h3>Total Kelas</h3>
                    <h2><?= $jml_kelas; ?></h2>
                </div>
            </div>

            <div class="stat-card red">
                <div class="icon-box red">
                    <i class="fas fa-book"></i>
                </div>
                <div class="info-box">
                    <h3>Total Mapel</h3>
                    <h2><?= $jml_mapel; ?></h2>
                </div>
            </div>
        </div>
        <div class="info-banner">
            <i class="fas fa-info-circle"></i>
            <p>Sistem ini terintegrasi dengan data Absensi Guru & Siswa. Pastikan data master selalu update.</p>
        </div>
        <script src="../js/script.js"></script>
    </div>
</body>

</html>