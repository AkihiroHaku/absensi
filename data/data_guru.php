<?php
session_start();
require_once "../config/database.php";

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Data Guru";
$deskripsi_halaman = "Kelola data guru.";
$active_menu = 'data_guru';
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

        <?php include "../layout/topbar.php"; ?>

        <?php if (isset($_SESSION['berhasil'])): ?>
            <div class="alert alert-success" style="margin-top: 20px;">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['berhasil']; ?>
            </div>
            <?php unset($_SESSION['berhasil']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['gagal'])): ?>
            <div class="alert alert-error" style="margin-top: 20px;">
                <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['gagal']; ?>
            </div>
            <?php unset($_SESSION['gagal']); ?>
        <?php endif; ?>

        <div class="card-table">
            <div class="card-header-title">
                <h3>Daftar Guru Aktif</h3>
                <a href="tambah_guru.php" class="btn-add">
                    <i class="fas fa-plus"></i> Tambah Guru
                </a>
            </div>

            <div class="table-responsive">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>NIP</th>
                            <th>No. HP</th>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = mysqli_query($conn, "SELECT * FROM guru ORDER BY nama_guru ASC");

                        if (mysqli_num_rows($query) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= $row['nip'] ? $row['nip'] : '-'; ?></td>
                                    <td><?= $row['no_hp']; ?></td>
                                    <td><?= $row['nama_guru']; ?></td>
                                    <td><?= $row['username']; ?></td>
                                    <td>
                                        <a href="edit_guru.php?id=<?= $row['id_guru']; ?>" class="btn-small btn-edit"><i class="fas fa-edit"></i></a>
                                        <button
                                            class="btn-small btn-delete"
                                            onclick="openDeleteGuruModal(<?= $row['id_guru'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='5' style='text-align:center; padding: 30px; color: #999;'>Belum ada data guru.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- MODAL HAPUS GURU -->
            <div id="delete-guru-modal" class="modal-overlay">
                <div class="modal-content modal-sm">
                    <div class="modal-header">
                        <h3><i class="fas fa-trash"></i> Hapus Guru</h3>
                    </div>

                    <div class="modal-body-centered">
                        <p>Apakah Anda yakin ingin menghapus data guru ini?</p>

                        <div class="logout-actions">
                            <a id="confirm-delete-guru" href="#" class="btn-keluar">
                                Ya, Hapus
                            </a>
                            <button onclick="closeDeleteGuruModal()" class="btn-secondary">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END MODAL HAPUS GURU -->
        </div>
        <script src="../js/admin.js"></script>
        <script src="../js/script.js"></script>
    </div>
</body>

</html>