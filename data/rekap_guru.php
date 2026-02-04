<?php
session_start();
require_once "../config/database.php";

// 1. Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$judul_halaman = "Rekap Absensi Guru";
$active_menu = 'absensi';

// 2. Filter Bulan & Tahun
$bulan_ini = date('m');
$tahun_ini = date('Y');

$f_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : $bulan_ini;
$f_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $tahun_ini;

// Array Nama Bulan
$nama_bulan = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Guru</title>

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
                    <h3><i class="fas fa-file-alt"></i> Rekap Absensi Guru</h3>
                    <p class="rekap-desc">
                        Laporan Periode: <b><?= $nama_bulan[$f_bulan]; ?> <?= $f_tahun; ?></b>
                    </p>
                </div>

                <form action="" method="GET" class="filter-form">
                    <select name="bulan" class="form-control input-auto">
                        <?php foreach ($nama_bulan as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($k == $f_bulan) ? 'selected' : '' ?>>
                                <?= $v ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <select name="tahun" class="form-control input-auto">
                        <?php for ($y = 2024; $y <= date('Y') + 2; $y++): ?>
                            <option value="<?= $y ?>" <?= ($y == $f_tahun) ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>

                    <button type="submit" class="btn-filter">
                        <i class="fas fa-filter"></i> Tampilkan
                    </button>
                    <a href="rekap_guru_harian.php?bulan=<?= $f_bulan ?>&tahun=<?= $f_tahun ?>" class="btn-filter" style="background:#8e44ad; text-decoration:none; margin-left:5px;">
                        <i class="fas fa-calendar-week"></i> Cek Harian
                    </a>
                </form>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="5%" class="th-center">No</th>
                            <th>Nama Guru</th>
                            <th width="10%" class="th-center"><span class="dot dot-h"></span>Hadir</th>
                            <th width="10%" class="th-center"><span class="dot dot-i"></span>Izin</th>
                            <th width="10%" class="th-center"><span class="dot dot-s"></span>Sakit</th>
                            <th width="10%" class="th-center"><span class="dot dot-a"></span>Alpha</th>
                            <th width="20%" class="th-center">Persentase Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "
                            SELECT 
                                g.nama_guru,
                                SUM(CASE WHEN a.status = 'H' THEN 1 ELSE 0 END) as jum_h,
                                SUM(CASE WHEN a.status = 'I' THEN 1 ELSE 0 END) as jum_i,
                                SUM(CASE WHEN a.status = 'S' THEN 1 ELSE 0 END) as jum_s,
                                SUM(CASE WHEN a.status = 'A' THEN 1 ELSE 0 END) as jum_a,
                                COUNT(a.id_absen) as total_input
                            FROM guru g
                            LEFT JOIN absensi_guru a 
                                ON g.id_guru = a.id_guru 
                                AND MONTH(a.tanggal) = '$f_bulan' 
                                AND YEAR(a.tanggal) = '$f_tahun'
                            GROUP BY g.id_guru
                            ORDER BY g.nama_guru ASC
                        ";

                        $result = mysqli_query($conn, $query);
                        $no = 1;

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                // Hitung Persentase
                                $total_hari = $row['total_input'];
                                $persen = ($total_hari > 0) ? round(($row['jum_h'] / $total_hari) * 100) : 0;

                                // Tentukan CLASS Warna Bar (Bukan Hex Color lagi)
                                if ($persen >= 80) {
                                    $class_bar = 'bg-high';
                                } elseif ($persen >= 50) {
                                    $class_bar = 'bg-med';
                                } else {
                                    $class_bar = 'bg-low';
                                }

                                // Class untuk Alpha (Merah jika ada alpha, abu jika 0)
                                $class_alpha = ($row['jum_a'] > 0) ? 'text-red' : 'text-mute';
                        ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td class="td-bold"><?= $row['nama_guru']; ?></td>
                                    <td class="td-num"><?= $row['jum_h']; ?></td>
                                    <td class="td-num"><?= $row['jum_i']; ?></td>
                                    <td class="td-num"><?= $row['jum_s']; ?></td>

                                    <td class="td-num <?= $class_alpha; ?>">
                                        <?= $row['jum_a']; ?>
                                    </td>

                                    <td>
                                        <div class="progress-wrapper">
                                            <div class="progress-track">
                                                <div class="progress-fill <?= $class_bar; ?>" style="width: <?= $persen; ?>%;"></div>
                                            </div>
                                            <span class="progress-text"><?= $persen; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='7' style='text-align:center; padding:20px;'>Belum ada data absensi pada bulan ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div>
                <a href="../data/menu_absensi.php" class="link-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

        </div>
    </div>
</body>

</html>