<?php
session_start();
require_once "../config/database.php";

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard.php");
    exit;
}
$judul_halaman = "Data Kelas";
$deskripsi_halaman = "Kelola data kelas.";
$active_menu = 'data_kelas';

// --- LOGIC TAMBAH DATA ---
if (isset($_POST['simpan'])) {
    $tingkat = $_POST['tingkat'];
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $nama_kelas = htmlspecialchars($_POST['nama_kelas']);

    mysqli_query($conn, "INSERT INTO kelas (tingkat, jurusan, nama_kelas) VALUES ('$tingkat', '$jurusan', '$nama_kelas')");
    header("Location: data_kelas.php");
    exit;
}

// --- LOGIC HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM kelas WHERE id_kelas = '$id'");
    header("Location: data_kelas.php");
    exit;
}

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

        <div class="content-wrapper">
            <h3>Tambah Kelas Baru</h3>
            <form action="" method="POST" class="kelas-form">

                <div class="form-row">
                    <div class="form-group">
                        <label>Tingkat</label>
                        <select name="tingkat" required>
                            <option value="">-- Pilih --</option>
                            <option value="10">Kelas 10</option>
                            <option value="11">Kelas 11</option>
                            <option value="12">Kelas 12</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Jurusan</label>
                        <input type="text" name="jurusan" placeholder="Contoh: RPL" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nama Kelas</label>
                    <input type="text" name="nama_kelas" placeholder="Contoh: X RPL 1" required>
                </div>
                <button type="submit" name="simpan" class="btn-submit">
                    <i class="fas fa-plus"></i> Tambah Kelas
                </button>
            </form>

            <hr>

            <h3>Daftar Kelas</h3>
            <table class="modern-table" id="kelas-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tingkat</th>
                        <th>Jurusan</th>
                        <th>Nama Kelas</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $q = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat ASC, jurusan ASC");
                    while ($d = mysqli_fetch_assoc($q)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>Kelas <?= $d['tingkat']; ?></td>
                            <td><?= $d['jurusan']; ?></td>
                            <td><?= $d['nama_kelas']; ?></td>
                            <td>
                                <a href="atur_mapel.php?id=<?= $d['id_kelas']; ?>" class="btn-warning">Mapel</a>
                                <a href="edit_kelas.php?id=<?= $d['id_kelas']; ?>" class="fas fa-edit btn-small btn-edit"></a>
                                <a href="javascript:void(0)"
                                    onclick="openDeleteKelasModal(<?= $d['id_kelas']; ?>)"
                                    class="btn-small btn-delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <div id="delete-kelas-modal" class="modal-overlay">
            <div class="modal-content modal-sm">
                <div class="modal-header">
                    <h3>Konfirmasi Hapus Kelas</h3>
                </div>
                <div class="modal-body-centered">
                    <p>
                        Menghapus kelas akan menghapus <b>seluruh siswa di kelas ini</b>.<br>
                        Apakah Anda yakin?
                    </p>

                    <div class="logout-actions">
                        <a href="#" id="confirm-delete-kelas" class="btn-keluar">
                            Ya, Hapus
                        </a>
                        <button onclick="closeDeleteKelasModal()" class="btn-secondary">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script src="../js/admin.js"></script>
        <script src="../js/script.js"></script>
    </div>