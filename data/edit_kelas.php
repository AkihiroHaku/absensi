<?php
session_start();
require_once "../config/database.php";

// Pastikan ada ID yang dikirim
if (!isset($_GET['id'])) {
    header("Location: data_kelas.php");
    exit;
}

$id = $_GET['id'];
$q = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas='$id'");
$data = mysqli_fetch_assoc($q);

$judul_halaman = "Edit Kelas";
$deskripsi_halaman = "Perbarui data kelas";
$active_menu = 'data_kelas';

// Logic Update
if (isset($_POST['update'])) {
    $tingkat = $_POST['tingkat'];
    $jurusan = htmlspecialchars($_POST['jurusan']);
    $nama = htmlspecialchars($_POST['nama_kelas']);

    mysqli_query($conn, "UPDATE kelas SET tingkat='$tingkat', jurusan='$jurusan', nama_kelas='$nama' WHERE id_kelas='$id'");

    // Setelah update, balik ke halaman tabel
    header("Location: data_kelas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Kelas</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include "../layout/sidebar.php"; ?>
    <div class="main-content">
        <?php include "../layout/topbar.php"; ?>

        <div class="main">
            <div class="card-header-title">
                <h3>Edit Data kelas</h3>
                <a href="data_kelas.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="content-wrapper">
                <form action="" method="POST" class="kelas-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Tingkat:</label>
                            <select name="tingkat">
                                <option value="10" <?= ($data['tingkat'] == '10') ? 'selected' : '' ?>>Kelas 10</option>
                                <option value="11" <?= ($data['tingkat'] == '11') ? 'selected' : '' ?>>Kelas 11</option>
                                <option value="12" <?= ($data['tingkat'] == '12') ? 'selected' : '' ?>>Kelas 12</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Jurusan:</label>
                            <input type="text" name="jurusan" value="<?= $data['jurusan']; ?>">
                        </div>

                        <div class="form-group">
                            <label>Nama Kelas:</label>
                            <input type="text" name="nama_kelas" value="<?= $data['nama_kelas']; ?>">
                        </div>

                        <button type="submit" name="update" class="btn-submit">
                            <i class="fas fa-save"></i> Update Kelas
                        </button>
                    </div>
                </form>
            </div>
            <script src="../js/admin.js"></script>
            <script src="../js/script.js"></script>
        </div>
</body>

</html>