<?php
session_start();
require_once "../config/database.php";

// 1. Cek Login & Role Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}

$active_menu = 'absensi';
$judul_halaman = "Absensi Guru";
$tgl_pilih = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// --- PROSES SIMPAN ABSENSI ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tgl = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $status_array = isset($_POST['status']) ? $_POST['status'] : []; 
    $ket_array    = isset($_POST['keterangan']) ? $_POST['keterangan'] : [];
    $count_berhasil = 0;

    foreach ($status_array as $id_guru => $st) {
        $id_guru = mysqli_real_escape_string($conn, $id_guru);
        $st      = mysqli_real_escape_string($conn, $st);
        $ket     = mysqli_real_escape_string($conn, $ket_array[$id_guru]);

        $cek = mysqli_query($conn, "SELECT id_absen FROM absensi_guru WHERE id_guru='$id_guru' AND tanggal='$tgl'");
        if (mysqli_num_rows($cek) > 0) {
            $query = "UPDATE absensi_guru SET status='$st', keterangan='$ket' WHERE id_guru='$id_guru' AND tanggal='$tgl'";
        } else {
            $query = "INSERT INTO absensi_guru (id_guru, tanggal, status, keterangan) VALUES ('$id_guru', '$tgl', '$st', '$ket')";
        }
        if (mysqli_query($conn, $query)) $count_berhasil++;
    }

    if ($count_berhasil > 0) {
        echo "<script>alert('Absensi tanggal $tgl berhasil disimpan!'); window.location='absensi_guru.php?tanggal=$tgl';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Input Absensi Guru</title>
    
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
            <div class="card-header-title">
                <a href="menu_absensi.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                <div>
                    <h3><i class="fas fa-user-clock"></i> Absensi Guru Harian</h3>
                </div>
                
                <form action="" method="GET">
                    <input type="date" name="tanggal" class="form-control" value="<?= $tgl_pilih; ?>">
                </form>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="tanggal" value="<?= $tgl_pilih; ?>">

                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="30%">Nama Guru</th>
                                <th width="30%">Status Kehadiran</th>
                                <th width="35%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $q = mysqli_query($conn, "SELECT g.id_guru, g.nama_guru, a.status, a.keterangan FROM guru g LEFT JOIN absensi_guru a ON g.id_guru = a.id_guru AND a.tanggal = '$tgl_pilih' ORDER BY g.nama_guru ASC");
                            
                            while ($row = mysqli_fetch_assoc($q)) {
                                $st = isset($row['status']) ? $row['status'] : 'A'; 
                                $ket = isset($row['keterangan']) ? $row['keterangan'] : '';
                            ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['nama_guru']; ?></td>
                                    <td>
                                        <div class="radio-group">
                                            <label class="radio-option">
                                                <input type="radio" name="status[<?= $row['id_guru']; ?>]" value="H" <?= ($st=='H')?'checked':''; ?>>
                                                <span class="radio-label lbl-h">Hadir</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="status[<?= $row['id_guru']; ?>]" value="I" <?= ($st=='I')?'checked':''; ?>>
                                                <span class="radio-label lbl-i">Izin</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="status[<?= $row['id_guru']; ?>]" value="S" <?= ($st=='S')?'checked':''; ?>>
                                                <span class="radio-label lbl-s">Sakit</span>
                                            </label>
                                            <label class="radio-option">
                                                <input type="radio" name="status[<?= $row['id_guru']; ?>]" value="A" <?= ($st=='A')?'checked':''; ?>>
                                                <span class="radio-label lbl-a">Alpha</span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="keterangan[<?= $row['id_guru']; ?>]" class="form-control" placeholder="..." value="<?= $ket; ?>">
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit" onclick="return confirm('Simpan data absensi?')">
                        <i class="fas fa-save"></i> Simpan Semua
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>