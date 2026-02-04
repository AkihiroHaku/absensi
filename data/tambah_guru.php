<?php
session_start();
require_once "../config/database.php";

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Tambah Guru";
$deskripsi_halaman = "Input data pengajar baru dan buatkan akun login.";
$active_menu = 'data_guru';

// --- PROSES SIMPAN ---
if (isset($_POST['simpan_guru'])) {
    $nip      = htmlspecialchars($_POST['nip']);
    $nama     = htmlspecialchars($_POST['nama_guru']);
    $jk       = $_POST['jenis_kelamin'];
    $hp       = $_POST['no_hp'];
    $username = htmlspecialchars($_POST['username']);
    $password = $_POST['password']; 

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $id_role_guru = 2; 
    $query_user = "INSERT INTO users (username, password, id_role) VALUES ('$username', '$password_hash', '$id_role_guru')";
    
    if (mysqli_query($conn, $query_user)) {
        $id_user_baru = mysqli_insert_id($conn);
        $query_guru = "INSERT INTO guru (id_user, nip, nama_guru, jenis_kelamin, no_hp) 
                       VALUES ('$id_user_baru', '$nip', '$nama', '$jk', '$hp')";

        if (mysqli_query($conn, $query_guru)) {
            echo "<script>alert('Berhasil! Akun login dan Data Guru telah dibuat.'); window.location='data_guru.php';</script>";
        } else {
            mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id_user_baru'");
            echo "<script>alert('Gagal menyimpan biodata guru!');</script>";
        }

    } else {
        echo "<script>alert('Gagal membuat akun login! Username mungkin sudah ada.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Guru</title>

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

    <?php include "../layout/sidebar.php"; ?>

    <div class="main-content">

        <?php include "../layout/topbar.php"; ?>

        <div class="card-table">
            <div class="card-header-title">
                <h3>Form Data Guru</h3>
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
                                <input type="number" name="nip" class="form-control" placeholder="Contoh: 1980xxxx" required>
                            </div>

                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control" placeholder="Nama lengkap beserta gelar" required>
                            </div>

                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="jk" class="form-control">
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>No. Handphone</label>
                                <input type="text" name="hp" class="form-control" placeholder="08xxxxxxx">
                            </div>
                        </div>

                        <div class="right-side">
                            <h4 class="form-section-title account-title">
                                <i class="fas fa-user-lock"></i> Akun Login
                            </h4>

                            <div class="account-section">
                                <div class="form-group">
                                    <label>Username <span class="required">*</span></label>
                                    <input type="text" name="username" class="form-control" placeholder="Username login" required>
                                </div>
                                <div class="form-group">
                                    <label>Password <span class="required">*</span></label>
                                    <div class="password-wrapper">
                                        <input type="password" name="password" class="form-control" placeholder="Password akun" required>
                                        <i class="fas fa-eye toggle-password"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="reset" class="btn-reset">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Simpan Guru
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