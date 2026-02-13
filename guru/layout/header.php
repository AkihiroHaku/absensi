<?php
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guru') {
    header("Location: ../auth/login.php");
    exit;
}
date_default_timezone_set('Asia/Jakarta');
$tgl_indo = date('d F Y');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Guru</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/teach.css">
</head>
<body>
    <div class="container">
        
        <header class="no-print">
            <div class="header-info">
                <i>
                    <img src="../assets/img/smkislam.png" class="remove-bg" width="140" style="border-radius: 12px;" alt="" srcset="">
                </i>
                <div class="header-text">
                    <h1>Sistem Absensi Siswa</h1>
                    <p>SMK Islam Salakbrojo - Pekalongan</p>
                </div>
            </div>
            <div class="header-right">
            <div class="date-display">
                <?= $tgl_indo; ?>
            </div>
            </div>
        </header>

        <div class="tabs no-print">
            <a href="index.php" class="tab <?= ($active_tab == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-home" style="color: forest green;"></i> Dashboard
            </a>
            <a href="absensi.php" class="tab <?= ($active_tab == 'absensi') ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-check" style="color: forest green;"></i> Absensi Harian 
            </a>
            <a href="siswa.php" class="tab <?= ($active_tab == 'siswa') ? 'active' : ''; ?>">
                <i class="fas fa-users" style="color: forest green;"></i> Data Siswa
            </a>
            <a href="laporan.php" class="tab <?= ($active_tab == 'laporan') ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar" style="color: forest green;" ></i> Laporan
            </a>
            <a href="profil_guru.php" class="tab <?= ($active_tab == 'profil') ? 'active' : ''; ?>">
                <i class="fas fa-user" style="color: forest green;" ></i> Profil Guru
            </a>
        </div>