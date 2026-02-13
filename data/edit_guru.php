<?php
session_start();
require_once "../config/database.php";

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 1. AMBIL ID DARI URL
if (!isset($_GET['id'])) {
    header("Location: data_guru.php");
    exit;
}
$id_guru = mysqli_real_escape_string($conn, $_GET['id']);

// 2. AMBIL DATA GURU LAMA
$query_old = mysqli_query($conn, "SELECT * FROM guru WHERE id_guru = '$id_guru'");
$data = mysqli_fetch_assoc($query_old);

// Jika data tidak ditemukan (misal ID salah)
if (!$data) {
    echo "<script>alert('Data guru tidak ditemukan!'); window.location='data_guru.php';</script>";
    exit;
}

$judul_halaman = "Edit Guru";
$deskripsi_halaman = "Perbarui data pengajar dan akun login.";
$active_menu = 'data_guru';

// --- PROSES UPDATE DATA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nip = mysqli_real_escape_string($conn, $_POST['nip']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $jenis_kelamin = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password_raw = $_POST['password'];

    // Validasi Dasar
    if (empty($nama) || empty($username)) {
        $error = "Nama dan Username tidak boleh kosong!";
    } else {
        // Cek Duplikat Username
        $cek = mysqli_query($conn, "SELECT id_guru FROM guru WHERE username='$username' AND id_guru != '$id_guru'");

        if (mysqli_num_rows($cek) > 0) {
            $error = "Username '$username' sudah dipakai guru lain!";
        } else {
            // LOGIKA UPDATE PASSWORD
            if (!empty($password_raw)) {
                $password_hash = md5($password_raw);
                $query_update = "UPDATE guru SET 
                                    nip = '$nip',
                                    nama_guru = '$nama',
                                    jenis_kelamin = '$jenis_kelamin', 
                                    no_hp = '$no_hp', 
                                    username = '$username', 
                                    password = '$password_hash' 
                                 WHERE id_guru = '$id_guru'";
            } else {
                $query_update = "UPDATE guru SET 
                                    nip = '$nip',
                                    nama_guru = '$nama', 
                                    username = '$username',
                                    jenis_kelamin = '$jenis_kelamin', 
                                    no_hp = '$no_hp'
                                 WHERE id_guru = '$id_guru'";
            }

            if (mysqli_query($conn, $query_update)) {
                $_SESSION['berhasil'] = "Data guru berhasil diperbarui!";
                header("Location: data_guru.php");
                exit;
            } else {
                $error = "Gagal update: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Guru</title>

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
                <h3>Edit Data Guru</h3>
                <a href="data_guru.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="form-container">

                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST">

                    <div class="form-grid">

                        <div class="left-side">
                            <h4 class="form-section-title">
                                <i class="fas fa-id-card"></i> Identitas Guru
                            </h4>

                            <div class="form-group">
                                <label>NIP <span class="required">*</span></label>
                                <input type="number" name="nip" class="form-control" value="<?= $data['nip']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control" value="<?= $data['nama_guru']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jenis_kelamin">
                                    <option value="L" <?= $data['jenis_kelamin'] == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $data['jenis_kelamin'] == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>No Handphone</label>
                                <input type="text" name="no_hp" class="form-control" placeholder="masukan no hp" value="<?= $data['no_hp']; ?>">
                            </div>

                        </div>

                        <div class="right-side">
                            <h4 class="form-section-title account-title">
                                <i class="fas fa-user-lock"></i> Akun Login
                            </h4>

                            <div class="account-section">
                                <div class="form-group">
                                    <label>Username <span class="required">*</span></label>
                                    <input type="text" name="username" class="form-control" placeholder="masukan username" value="<?= $data['username']; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Password Baru</label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password" placeholder="masukan password" class="form-control">
                                        <i class="fas fa-eye toggle-password"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        </a>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="../js/admin.js"></script>
    <script src="../js/script.js"></script>
</body>

</html>