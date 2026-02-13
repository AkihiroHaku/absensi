<?php
session_start();
require_once "../config/database.php";

if (isset($_POST['update_mapel'])) {
    $id_edit   = $_POST['id_mapel_edit'];
    $nama_baru = mysqli_real_escape_string($conn, $_POST['nama_mapel_edit']);

    // Update ke database
    $q_update = mysqli_query($conn, "UPDATE mapel SET nama_mapel='$nama_baru' WHERE id_mapel='$id_edit'");

    if ($q_update) {
        // Redirect biar refresh
        echo "<script>alert('Nama mapel berhasil diubah!'); window.location='atur_mapel.php?id=$id_kelas';</script>";
    } else {
        echo "<script>alert('Gagal mengubah data!');</script>";
    }
}

$id_kelas = $_GET['id'];

$judul_halaman = "Data Mapel";
$deskripsi_halaman = "Kelola mapel";
$active_menu = 'data_kelas';

// Ambil info kelas
$q_kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas'");
$d_kelas = mysqli_fetch_assoc($q_kelas);

// Logic Tambah Mapel
if (isset($_POST['tambah_mapel'])) {
    $nama_mapel = htmlspecialchars($_POST['nama_mapel']);
    mysqli_query($conn, "INSERT INTO mapel (id_kelas, nama_mapel) VALUES ('$id_kelas', '$nama_mapel')");
    header("Location: atur_mapel.php?id=$id_kelas");
    exit;
}

// Logic Hapus Mapel
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id_mapel'])) {
    $id_mapel_hapus = mysqli_real_escape_string($conn, $_GET['id_mapel']);

    // Hapus dari database
    $q_hapus = mysqli_query($conn, "DELETE FROM mapel WHERE id_mapel = '$id_mapel_hapus'");

    if ($q_hapus) {
        // Redirect kembali ke halaman ini (refresh bersih)
        header("Location: atur_mapel.php?id=$id_kelas&msg=sukses_hapus");
        exit;
    } else {
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
}
// ------------------------------------------
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Atur Mapel</title>
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
            <div class="form-header">
                <h4 class="form-title">
                    Mapel Kelas <?= $d_kelas['nama_kelas']; ?>
                </h4>
                <a href="data_kelas.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
            <form action="" method="POST" class="kelas-form">
                <input type="text" name="nama_mapel" class="input-text" placeholder="Nama Mata Pelajaran..." required>
                <button type="submit" name="tambah_mapel" class="btn-submit">
                    <i class="fas fa-plus"></i> Tambah Mapel
                </button>
            </form>


        <div class="mapel-grid">
            <?php
            $q_mapel = mysqli_query($conn, "SELECT * FROM mapel WHERE id_kelas = '$id_kelas'");
            if (mysqli_num_rows($q_mapel) > 0) {
                while ($m = mysqli_fetch_assoc($q_mapel)) {
            ?>
                    <div class="mapel-card">
                        <div class="mapel-info">
                            <div class="mapel-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="mapel-name" id="nama_<?= $m['id_mapel']; ?>">
                                <?= $m['nama_mapel']; ?>
                            </div>
                        </div>

                        <div class="action-buttons">

                            <button type="button" class="btn-edit-icon"
                                onclick="bukaModalEdit('<?= $m['id_mapel']; ?>', '<?= $m['nama_mapel']; ?>')"
                                title="Edit Nama">
                                <i class="fas fa-pen"></i>
                            </button>

                            <a href="atur_mapel.php?id=<?= $id_kelas ?>&aksi=hapus&id_mapel=<?= $m['id_mapel'] ?>"
                                class="btn-delete-icon"
                                onclick="return confirm('Hapus mapel ini?')"
                                title="Hapus Mapel">
                                <i class="fas fa-trash-alt"></i>
                            </a>

                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p style='color:#ee5253;'>Belum ada mapel di kelas ini.</p>";
            }
            ?>
        </div>
    </div>
    </div>
    <div id="modalEdit" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-title">Edit Mata Pelajaran</div>

            <form action="" method="POST">
                <input type="hidden" name="id_mapel_edit" id="input_id_mapel">

                <label style="font-size:12px; font-weight:bold; color:#666;">Nama Mapel:</label>
                <input type="text" name="nama_mapel_edit" id="input_nama_mapel" class="form-control" required autocomplete="off">

                <div class="modal-buttons">
                    <button type="button" onclick="tutupModal()" class="btn-reset">
                        Batal
                    </button>
                    <button type="submit" name="update_mapel" class="btn-submit">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="../js/admin.js"></script>
    <script src="../js/script.js"></script>
</body>

</html>