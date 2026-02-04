<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Data Siswa";
$deskripsi_halaman = "Kelola data siswa berdasarkan kelas.";
$active_menu = 'data_siswa';

// --- LOGIKA UTAMA: CEK APAKAH SEDANG MEMILIH KELAS? ---
$id_kelas_terpilih = $_GET['id_kelas'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Kelas</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <?php include "../layout/sidebar.php"; ?>

    <div class="main-content">

        <?php include "../layout/topbar.php";
        ?>

        <?php if ($id_kelas_terpilih == null): ?>

            <div class="card-table">
                <div class="card-header-title">
                    <h3>Silakan Pilih Kelas</h3>
                </div>

                <div class="class-grid">
                    <?php
                    // Ambil data semua kelas
                    $q_kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat ASC, jurusan ASC");

                    if (mysqli_num_rows($q_kelas) > 0) {
                        while ($k = mysqli_fetch_assoc($q_kelas)) {
                            // Tentukan warna card berdasarkan tingkat (Opsional, biar cantik)
                            $color_class = 'card-x';
                            if ($k['tingkat'] == '11') $color_class = 'card-xi';
                            if ($k['tingkat'] == '12') $color_class = 'card-xii';
                    ?>
                            <a href="?id_kelas=<?= $k['id_kelas']; ?>" class="class-card <?= $color_class; ?>">
                                <div class="icon-class">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="info-class">
                                    <h4>Kelas <?= $k['nama_kelas']; ?></h4>
                                    <span><?= $k['jurusan']; ?></span>
                                </div>
                                <i class="fas fa-chevron-right arrow-icon"></i>
                            </a>
                    <?php
                        }
                    } else {
                        echo "<p>Belum ada data kelas. Silakan tambah kelas dulu.</p>";
                    }
                    ?>
                </div>
            </div>

        <?php else: ?>

            <?php
            // 1. Ambil Info Kelas yang dipilih
            $q_info = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas_terpilih'");
            $d_kelas = mysqli_fetch_assoc($q_info);

            if (!$d_kelas) {
                echo "<script>alert('Kelas tidak ditemukan!'); window.location='data_siswa.php';</script>";
                exit;
            }

            $nama_kelas_label = $d_kelas['nama_kelas'];
            $tingkat = $d_kelas['tingkat'];
            $jurusan = $d_kelas['jurusan'];

            // 2. Tentukan mau ambil dari tabel mana (siswa_kelas10/11/12)
            $tabel_target = "";
            if ($tingkat == '10') $tabel_target = "siswa_kelas10";
            elseif ($tingkat == '11') $tabel_target = "siswa_kelas11";
            elseif ($tingkat == '12') $tabel_target = "siswa_kelas12";

            // 3. Query Ambil Siswa
            // Filter berdasarkan 'jurusan' karena struktur tabelmu begitu
            $q_siswa = mysqli_query($conn, "SELECT * FROM $tabel_target WHERE jurusan = '$jurusan' ORDER BY nama ASC");
            ?>

            <div class="card-table">
                <div class="card-header-title">
                    <div>
                        <a href="data_siswa.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <h3>Siswa Kelas <?= $nama_kelas_label; ?></h3>
                    </div>

                    <a href="tambah_siswa.php?id_kelas=<?= $id_kelas_terpilih ?>" class="btn-add">
                        <i class="fas fa-plus"></i> Tambah Siswa
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="modern-table" id="siswa-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>NIS</th>
                                <th>Nama Siswa</th>
                                <th>Jurusan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (mysqli_num_rows($q_siswa) > 0) {
                                $no = 1;
                                while ($s = mysqli_fetch_assoc($q_siswa)) {
                            ?>
                                    <tr>
                                        <td><?= $no++; ?></td>
                                        <td><?= $s['nis']; ?></td>
                                        <td style="font-weight:bold;"><?= $s['nama']; ?></td>
                                        <td><?= $s['jurusan']; ?></td>
                                        <td>
                                            <a href="edit_siswa.php?nis=<?= $s['nis']; ?>" class="btn-small btn-edit"><i class="fas fa-edit"></i></a>
                                            <a href="hapus_siswa.php?nis=<?= $s['nis']; ?>" class="btn-small btn-delete" onclick="return confirm('Hapus siswa ini?')"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='5' style='text-align:center; padding:30px;'>Belum ada siswa di kelas ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <script src="../js/admin.js"></script>
        <script src="../js/script.js"></script>
    </div>
</body>

</html>