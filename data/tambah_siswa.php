<?php
session_start();
require_once "../config/database.php";

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Tambah Siswa";
$deskripsi_halaman = "Menambahkan data siswa baru ke dalam sistem.";
$active_menu = 'data_siswa';

// --- PROSES SIMPAN DATA ---
// --- PROSES SIMPAN DATA (PERBAIKAN) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kelas = mysqli_real_escape_string($conn, $_POST['kelas']);
    $nis_array  = $_POST['nis'];
    $nama_array = $_POST['nama'];

    if (empty($nama_kelas)) {
        $error = "Kelas harus dipilih!";
    } elseif (empty($nis_array) || empty($nama_array)) {
        $error = "Minimal satu siswa harus diisi!";
    } else {
        // 1. Ambil info LENGKAP Kelas (termasuk id_kelas)
        // PERBAIKAN DI SINI: Kita tambahkan 'id_kelas' dalam SELECT
        $query_kelas = "SELECT id_kelas, tingkat, jurusan FROM kelas WHERE nama_kelas='$nama_kelas' LIMIT 1";
        $result_kelas = mysqli_query($conn, $query_kelas);

        if (mysqli_num_rows($result_kelas) == 0) {
            $error = "Kelas '$nama_kelas' tidak ditemukan.";
        } else {
            $data_kelas = mysqli_fetch_assoc($result_kelas);
            $id_kelas_target = $data_kelas['id_kelas']; // Simpan ID Kelas
            $tingkat = $data_kelas['tingkat'];
            $jurusan = $data_kelas['jurusan'];

            // 2. Tentukan Tabel Tujuan
            $table = "";
            if ($tingkat == '10') $table = "siswa_kelas10";
            elseif ($tingkat == '11') $table = "siswa_kelas11";
            elseif ($tingkat == '12') $table = "siswa_kelas12";
            else {
                $error = "Tingkat kelas ($tingkat) tidak valid!";
            }

            // 3. Loop Insert Data
            if (!empty($table)) {
                $success_count = 0;
                $errors = [];

                for ($i = 0; $i < count($nis_array); $i++) {
                    $nis  = mysqli_real_escape_string($conn, trim($nis_array[$i]));
                    $nama = mysqli_real_escape_string($conn, trim($nama_array[$i]));

                    if (!empty($nis) && !empty($nama)) {
                        // Cek NIS Duplikat (Opsional, sebaiknya tetap ada)
                        $cek_nis = mysqli_query($conn, "
                            SELECT nis FROM siswa_kelas10 WHERE nis='$nis'
                            UNION ALL
                            SELECT nis FROM siswa_kelas11 WHERE nis='$nis'
                            UNION ALL
                            SELECT nis FROM siswa_kelas12 WHERE nis='$nis'
                        ");

                        if (mysqli_num_rows($cek_nis) > 0) {
                            $errors[] = "NIS $nis sudah terdaftar.";
                        } else {
                            $query = "INSERT INTO $table (nis, nama, jurusan, id_kelas) 
                                      VALUES ('$nis', '$nama', '$jurusan', '$id_kelas_target')";

                            if (mysqli_query($conn, $query)) {
                                $success_count++;
                            } else {
                                $errors[] = "Gagal simpan $nama: " . mysqli_error($conn);
                            }
                        }
                    }
                }

                if ($success_count > 0) {
                    header("Location: data_siswa.php?id_kelas=$id_kelas_target&success=1");
                    exit;
                } else {
                    $error = "Gagal menyimpan data. " . implode(", ", $errors);
                }
            }
        }
    }
}
?>

<?php
$id_kelas = isset($_GET['id_kelas']) ? intval($_GET['id_kelas']) : 0;

if ($id_kelas <= 0) {
    header("Location:data_siswa.php?id_kelas=$id_kelas");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Siswa</title>
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
                <h3>Form Tambah Siswa</h3>
                <a href="data_siswa.php?id_kelas=<?= $id_kelas ?>" class="btn-add" style="background: #6c757d;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <div class="form-container">
                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form action="" method="POST" id="form-tambah-siswa">

                    <div class="form-group">
                        <label for="kelas">Pilih Kelas</label>
                        <?php $auto_kelas = isset($_GET['auto_kelas']) ? urldecode($_GET['auto_kelas']) : ''; ?>

                        <select id="kelas" name="kelas">
                            <option value="">-- Pilih Kelas --</option>
                            <?php
                            $q_kelas = mysqli_query($conn, "SELECT id_kelas, nama_kelas FROM kelas ORDER BY tingkat ASC, jurusan ASC");

                            while ($k = mysqli_fetch_assoc($q_kelas)) {
                                $selected = '';

                                if (isset($_POST['kelas']) && $_POST['kelas'] == $k['nama_kelas']) {
                                    $selected = 'selected';
                                }
                                elseif ($id_kelas == $k['id_kelas']) {
                                    $selected = 'selected';
                                }

                                echo "<option value='{$k['nama_kelas']}' $selected>{$k['nama_kelas']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="table-input-container">
                        <div class="table-header-actions">
                            <button type="button" id="add-row" class="btn-add-row">
                                <i class="fas fa-plus"></i> Tambah Baris
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="modern-table" id="siswa-table">
                                <thead>
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="15%">NIS</th>
                                        <th>Nama Siswa</th>
                                        <th width="15%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="siswa-tbody">
                                    <tr class="siswa-row">
                                        <td>1</td>
                                        <td><input type="text" name="nis[]" placeholder="Masukkan NIS"></td>
                                        <td><input type="text" name="nama[]" placeholder="Masukkan Nama"></td>
                                        <td><button type="button" class="btn-delete-row"><i class="fas fa-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-save"></i> Simpan Semua
                        </button>
                        <button type="reset" class="btn-reset">
                            <i class="fas fa-undo"></i> Reset
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