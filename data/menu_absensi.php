<?php
session_start();
// Cek Login
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$role = $_SESSION['role'];
$judul_halaman = "Menu Absensi";
$active_menu = 'absensi'; // Untuk menandai menu sidebar aktif
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Menu Absensi</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php include "../layout/sidebar.php"; ?>

    <div class="main-content">
        <?php include "../layout/topbar.php"; ?>

        <div class="card-table">

            <div class="content.wrapper">
                <h3>Pilih Menu Absensi</h3>
                <p>Silakan pilih jenis absensi yang ingin dikelola hari ini.</p>
            </div>

            <div class="menu-grid">

                <?php
                $link_guru  = ($role == 'admin') ? 'absensi_guru.php' : 'riwayat_saya.php';
                $judul_guru = ($role == 'admin') ? 'Input Absensi Guru' : 'Kehadiran Saya';
                $desc_guru  = ($role == 'admin') ? 'Kelola kehadiran harian dewan guru & staf pengajar.' : 'Lihat riwayat dan rekap kehadiran Anda sendiri.';
                ?>

                <a href="<?= $link_guru; ?>" class="menu-card">
                    <div class="card-icon icon-guru">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="card-title"><?= $judul_guru; ?></div>
                    <div class="card-desc"><?= $desc_guru; ?></div>
                </a>

                <?php if ($role == 'admin'): ?>
                    <a href="rekap_guru.php" class="menu-card">
                        <div class="card-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="card-title">Laporan Guru</div>
                        <div class="card-desc">Lihat rekapitulasi kehadiran guru bulanan & persentase.</div>
                    </a>
                <?php endif; ?>

                <?php
                // Admin ke Laporan, Guru ke Input
                $link_siswa  = ($role == 'admin') ? 'rekap_siswa.php' : 'absen_siswa.php';
                $judul_siswa = ($role == 'admin') ? 'Laporan Absensi Siswa' : 'Input Absensi Siswa';
                $desc_siswa  = ($role == 'admin') ? 'Lihat rekapitulasi kehadiran siswa per kelas & mapel.' : 'Isi jurnal kehadiran siswa di kelas yang Anda ajar.';
                ?>
                <a href="<?= $link_siswa; ?>" class="menu-card">
                    <div class="card-icon icon-siswa">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="card-title"><?= $judul_siswa; ?></div>
                    <div class="card-desc"><?= $desc_siswa; ?></div>
                </a>

            </div>
        </div>
        <script src="../js/admin.js"></script>
        <script src="../js/script.js"></script>
    </div>

</body>

</html>