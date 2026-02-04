<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$judul_halaman = "Rekap Harian Guru";
$active_menu = 'absensi';

// Filter Waktu
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Hitung jumlah hari dalam bulan tersebut
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Harian Guru</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include "../layout/sidebar.php"; ?>

    <div class="main-content">
        <?php include "../layout/topbar.php"; ?>

        <div class="card-table">
            
            <div class="rekap-header">
                <div>
                    <h3><i class="fas fa-calendar-alt"></i> Absensi Harian Guru</h3>
                    <p class="rekap-desc">Detail per tanggal: <b><?= $nama_bulan[$bulan]; ?> <?= $tahun; ?></b></p>
                </div>

                <div class="flex-row-gap">
                    <form action="" method="GET" class="filter-form">
                        <select name="bulan" class="form-control input-auto">
                            <?php foreach($nama_bulan as $k => $v): ?>
                                <option value="<?= $k ?>" <?= ($k == $bulan) ? 'selected' : '' ?>><?= $v ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select name="tahun" class="form-control input-auto">
                            <?php for($y=2024; $y<=date('Y')+2; $y++): ?>
                                <option value="<?= $y ?>" <?= ($y == $tahun) ? 'selected' : '' ?>><?= $y ?></option>
                            <?php endfor; ?>
                        </select>
                        <button type="submit" class="btn-filter"><i class="fas fa-search"></i></button>
                    </form>

                    <a href="rekap_guru.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>" class="btn-secondary">
                        <i class="fas fa-list"></i> Ringkasan
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th class="sticky-col-header">Nama Guru</th>
                            
                            <?php for($d=1; $d<=$jumlah_hari; $d++): ?>
                                <th class="th-date"><?= $d; ?></th>
                            <?php endfor; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // 1. Ambil Data Absen (Disimpan di Array)
                        $data_absen = [];
                        $q_absen = mysqli_query($conn, "SELECT id_guru, DAY(tanggal) as tgl, status FROM absensi_guru WHERE MONTH(tanggal)='$bulan' AND YEAR(tanggal)='$tahun'");
                        while($ab = mysqli_fetch_assoc($q_absen)) {
                            $data_absen[$ab['id_guru']][$ab['tgl']] = $ab['status'];
                        }

                        // 2. Ambil Data Guru
                        $q_guru = mysqli_query($conn, "SELECT id_guru, nama_guru FROM guru ORDER BY nama_guru ASC");
                        
                        while($g = mysqli_fetch_assoc($q_guru)):
                            $id = $g['id_guru'];
                        ?>
                            <tr>
                                <td class="sticky-col-body">
                                    <?= $g['nama_guru']; ?>
                                </td>

                                <?php for($d=1; $d<=$jumlah_hari; $d++): 
                                    $status = isset($data_absen[$id][$d]) ? $data_absen[$id][$d] : '-';
                                    
                                    // Logika Class Warna (Pengganti if hex color)
                                    $class_warna = 'st-none';
                                    $tanda = '.'; // Default tanda titik

                                    if ($status == 'H') { $class_warna = 'st-h'; $tanda = 'H'; }
                                    elseif ($status == 'I') { $class_warna = 'st-i'; $tanda = 'I'; }
                                    elseif ($status == 'S') { $class_warna = 'st-s'; $tanda = 'S'; }
                                    elseif ($status == 'A') { $class_warna = 'st-a'; $tanda = 'A'; }
                                ?>
                                    <td class="td-date <?= $class_warna; ?>">
                                        <?= $tanda; ?>
                                    </td>
                                <?php endfor; ?>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

        </div>
        <script src="../js/admin.js"></script>
        <script src="../js/script.js"></script>
    </div>
</body>
</html>