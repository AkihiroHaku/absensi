<?php
session_start();
require_once "../config/database.php";

$active_tab = 'siswa';
include "layout/header.php";

// --- LOGIC BARU (SESUAI GAMBAR DATABASE KAMU) ---

$kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
$search   = isset($_GET['search']) ? $_GET['search'] : '';
$final_query = "";

// SKENARIO 1: Jika Guru MEMILIH KELAS di Dropdown
if ($kelas_id != '') {
    // 1. Kita harus tahu dulu, ID Kelas ini tingkat berapa & jurusannya apa?
    $cek_kelas = mysqli_query($conn, "SELECT tingkat, jurusan FROM kelas WHERE id_kelas = '$kelas_id'");
    $data_k    = mysqli_fetch_assoc($cek_kelas);
    
    if ($data_k) {
        $tingkat_target = $data_k['tingkat']; // misal: 10
        $jurusan_target = $data_k['jurusan']; // misal: RPL
        
        // 2. Pilih tabel yang sesuai (siswa_kelas10, 11, atau 12)
        $nama_tabel = "siswa_kelas" . $tingkat_target;
        
        // 3. Query datanya (Filter berdasarkan jurusan)
        // Perhatikan: kita pakai 'nama' bukan 'nama_siswa'
        $final_query = "SELECT nis, nama AS nama_siswa, jurusan, 'Kelas $tingkat_target' as tingkat 
                        FROM $nama_tabel 
                        WHERE jurusan = '$jurusan_target'";
    }
} 
// SKENARIO 2: Jika TIDAK MEMILIH KELAS (Tampilkan Semua)
else {
    $final_query = "
        SELECT nis, nama AS nama_siswa, jurusan, 'Kelas 10' as tingkat FROM siswa_kelas10
        UNION ALL
        SELECT nis, nama AS nama_siswa, jurusan, 'Kelas 11' as tingkat FROM siswa_kelas11
        UNION ALL
        SELECT nis, nama AS nama_siswa, jurusan, 'Kelas 12' as tingkat FROM siswa_kelas12
    ";
}

// Tambahkan Logic Pencarian (Search)
if ($search != '') {
    // Karena query sudah dibentuk di atas, kita bungkus lagi biar search jalan di hasil filter/union
    $final_query = "SELECT * FROM ($final_query) AS gabungan 
                    WHERE (nama_siswa LIKE '%$search%' OR nis LIKE '%$search%')";
}

// Jalankan Query
$result = mysqli_query($conn, $final_query);
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 class="card-title" style="border:none; margin:0; padding:0;">
            <i class="fas fa-users"></i> Data Siswa
        </h2>
    </div>

    <form method="GET" action="" style="display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <select name="kelas_id" onchange="this.form.submit()" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                <option value="">-- Semua Kelas --</option>
                <?php
                $q_kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat ASC, jurusan ASC");
                while ($k = mysqli_fetch_assoc($q_kelas)) {
                    $sel = ($kelas_id == $k['id_kelas']) ? 'selected' : '';
                    echo "<option value='{$k['id_kelas']}' $sel>{$k['nama_kelas']}</option>";
                }
                ?>
            </select>
        </div>

        <div style="flex: 2; display: flex; gap: 10px; min-width: 300px;">
            <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Cari Nama atau NIS..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
            <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                <i class="fas fa-search"></i> Cari
            </button>
        </div>
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>NIS</th>
                    <th>Nama Lengkap</th>
                    <th>Kelas</th>
                    <th>Jurusan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0) {
                    $no = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><?= $row['nis']; ?></td>
                        <td><strong><?= $row['nama_siswa']; ?></strong></td>
                        <td><span class="badge bg-blue"><?= $row['tingkat']; ?></span></td>
                        <td><?= $row['jurusan']; ?></td>
                    </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 40px; color: #999;'>
                        <i class='fas fa-user-slash' style='font-size: 3rem; margin-bottom: 10px; display:block;'></i>
                        Data siswa tidak ditemukan.
                    </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include "layout/footer.php"; ?>