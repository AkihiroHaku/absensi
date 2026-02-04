<?php
session_start();
require_once "../config/database.php";

// 1. Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Cek Parameter NIS di URL
if (!isset($_GET['nis'])) {
    header("Location: data_siswa.php");
    exit;
}

$nis_lama = mysqli_real_escape_string($conn, $_GET['nis']);
$data_siswa = null;
$table_asal = '';

// 3. Cari Data Siswa di 3 Tabel (10, 11, 12)
$list_tabel = ['siswa_kelas10', 'siswa_kelas11', 'siswa_kelas12'];

foreach ($list_tabel as $tbl) {
    $q_cari = mysqli_query($conn, "SELECT * FROM $tbl WHERE nis = '$nis_lama'");
    if (mysqli_num_rows($q_cari) > 0) {
        $data_siswa = mysqli_fetch_assoc($q_cari);
        $table_asal = $tbl;
        break; // Ketemu! Berhenti mencari.
    }
}

// Jika siswa tidak ditemukan di tabel manapun
if (!$data_siswa) {
    echo "<script>alert('Data siswa tidak ditemukan!'); window.location='data_siswa.php';</script>";
    exit;
}

$judul_halaman = "Edit Siswa";
$deskripsi_halaman = "Perbarui data siswa dan penempatan kelas.";
$active_menu = 'data_siswa';

// --- PROSES UPDATE DATA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nis_baru   = mysqli_real_escape_string($conn, $_POST['nis']);
    $nama_baru  = mysqli_real_escape_string($conn, $_POST['nama']);
    $id_kelas   = mysqli_real_escape_string($conn, $_POST['id_kelas']);

    // 1. Ambil Info Kelas Baru
    $q_kelas = mysqli_query($conn, "SELECT * FROM kelas WHERE id_kelas = '$id_kelas'");
    $d_kelas = mysqli_fetch_assoc($q_kelas);
    
    $jurusan_baru = $d_kelas['jurusan'];
    $tingkat_baru = $d_kelas['tingkat'];
    
    // Tentukan Tabel Tujuan berdasarkan Tingkat Baru
    $table_tujuan = "siswa_kelas" . $tingkat_baru; 

    // 2. Validasi NIS Duplikat (Jika NIS diganti)
    $is_duplicate = false;
    if ($nis_baru != $nis_lama) {
        // Cek di semua tabel apakah NIS baru sudah dipakai orang lain
        $cek = mysqli_query($conn, "
            SELECT nis FROM siswa_kelas10 WHERE nis='$nis_baru'
            UNION ALL
            SELECT nis FROM siswa_kelas11 WHERE nis='$nis_baru'
            UNION ALL
            SELECT nis FROM siswa_kelas12 WHERE nis='$nis_baru'
        ");
        if (mysqli_num_rows($cek) > 0) {
            $is_duplicate = true;
        }
    }

    if (empty($nis_baru) || empty($nama_baru) || empty($id_kelas)) {
        $error = "Semua data wajib diisi!";
    } elseif ($is_duplicate) {
        $error = "NIS $nis_baru sudah terdaftar atas nama siswa lain!";
    } else {
        // 3. LOGIKA PENYIMPANAN
        
        // KASUS A: Jika Tingkat SAMA (Hanya update data biasa)
        if ($table_asal == $table_tujuan) {
            $query = "UPDATE $table_asal SET 
                        nis = '$nis_baru', 
                        nama = '$nama_baru', 
                        jurusan = '$jurusan_baru',
                        id_kelas = '$id_kelas'
                      WHERE nis = '$nis_lama'";
            
            if (mysqli_query($conn, $query)) {
                $_SESSION['berhasil'] = "Data siswa berhasil diperbarui!";
                header("Location: data_siswa.php?id_kelas=$id_kelas"); // Balik ke kelasnya
                exit;
            } else {
                $error = "Gagal update: " . mysqli_error($conn);
            }
        } 
        // KASUS B: Jika Tingkat BERBEDA (Pindah Tabel, misal naik kelas)
        else {
            // Langkah 1: Masukkan ke tabel baru
            $insert = "INSERT INTO $table_tujuan (nis, nama, jurusan, id_kelas) 
                       VALUES ('$nis_baru', '$nama_baru', '$jurusan_baru', '$id_kelas')";
            
            if (mysqli_query($conn, $insert)) {
                // Langkah 2: Hapus dari tabel lama
                mysqli_query($conn, "DELETE FROM $table_asal WHERE nis = '$nis_lama'");
                
                $_SESSION['berhasil'] = "Siswa berhasil dipindahkan ke kelas tingkat $tingkat_baru!";
                header("Location: data_siswa.php?id_kelas=$id_kelas");
                exit;
            } else {
                $error = "Gagal memindahkan data: " . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Siswa</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="../css/data.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include "../layout/sidebar.php"; ?>

    <div class="main-content">
        
        <?php include "../layout/topbar.php"; ?>

        <div class="card-table">
            <div class="card-header-title">
                <h3>Edit Data Siswa</h3>
                <a href="data_siswa.php?id_kelas=<?= isset($data_siswa['id_kelas']) ? $data_siswa['id_kelas'] : ''; ?>" class="btn-back">
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
                                <i class="fas fa-user-graduate"></i> Identitas Siswa
                            </h4>

                            <div class="form-group">
                                <label>NIS <span class="required">*</span></label>
                                <input type="number" name="nis" class="form-control" value="<?= $data_siswa['nis']; ?>" required>
                            </div>

                            <div class="form-group">
                                <label>Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control" value="<?= $data_siswa['nama']; ?>" required>
                            </div>
                        </div>

                        <div class="right-side">
                            <h4 class="form-section-title account-title">
                                <i class="fas fa-chalkboard-teacher"></i> Penempatan Kelas
                            </h4>
                            
                            <div class="account-section">
                                <div class="form-group">
                                    <label>Pilih Kelas <span class="required">*</span></label>
                                    <select name="id_kelas" class="form-control" required>
                                        <option value="">-- Pilih Kelas --</option>
                                        <?php
                                        // Ambil semua data kelas
                                        $q_k = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat ASC, jurusan ASC, nama_kelas ASC");
                                        while($k = mysqli_fetch_assoc($q_k)) {
                                            // Cek Selected: Jika id_kelas siswa sama dengan id_kelas di loop
                                            $selected = ($data_siswa['id_kelas'] == $k['id_kelas']) ? 'selected' : '';
                                            echo "<option value='{$k['id_kelas']}' $selected>{$k['nama_kelas']} ({$k['jurusan']})</option>";
                                        }
                                        ?>
                                    </select>
                                    </div>
                                
                                <div class="alert alert-success" style="margin-top:15px; font-size:12px; padding:10px;">
                                    <i class="fas fa-info-circle"></i> 
                                    Saat ini siswa berada di tabel: <b><?= $table_asal; ?></b>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>
        <script src="../js/script.js"></script>
        <script src="../js/admin.js"></script>
    </div>

</body>
</html>