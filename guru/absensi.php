<?php
session_start();
require_once "../config/database.php";

$active_tab = 'absensi';
include "layout/header.php";

// --- LOGIC 1: PROSES SIMPAN DATA (Jika Tombol Simpan Ditekan) ---
if (isset($_POST['simpan_absen'])) {
    $tgl_absen = $_POST['tanggal'];
    $id_kelas  = $_POST['kelas_id_hidden'];
    $id_mapel  = $_POST['id_mapel'];
    // --------------------------------

    if (isset($_POST['status']) && count($_POST['status']) > 0) {
        $jumlah_sukses = 0;

        foreach ($_POST['status'] as $nis_siswa => $nilai_status) {
            $nama_siswa = $_POST['nama_siswa'][$nis_siswa];
            $jurusan    = $_POST['jurusan'][$nis_siswa];
            $tingkat    = $_POST['tingkat'][$nis_siswa];

            // CEK DATA: Sekarang ceknya harus spesifik (NIS + Tanggal + MAPEL)
            $cek = mysqli_query($conn, "SELECT id_absensi FROM absensi_siswa 
                                        WHERE nis='$nis_siswa' 
                                        AND tanggal='$tgl_absen' 
                                        AND id_mapel='$id_mapel'");

            if (mysqli_num_rows($cek) > 0) {
                // UPDATE (Sertakan id_mapel di WHERE)
                $query_simpan = "UPDATE absensi_siswa SET status='$nilai_status' 
                                 WHERE nis='$nis_siswa' AND tanggal='$tgl_absen' AND id_mapel='$id_mapel'";
            } else {
                // INSERT (Masukkan $id_mapel ke dalam kolom database)
                $query_simpan = "INSERT INTO absensi_siswa (nis, nama, id_kelas, id_mapel, jurusan, tingkat, tanggal, status) 
                                 VALUES ('$nis_siswa', '$nama_siswa', '$id_kelas', '$id_mapel', '$jurusan', '$tingkat', '$tgl_absen', '$nilai_status')";
            }

            if (mysqli_query($conn, $query_simpan)) {
                $jumlah_sukses++;
            }
        }

        // Refresh halaman dengan pesan sukses
        echo "<script>
            alert('Berhasil menyimpan data absensi untuk $jumlah_sukses siswa!');
            window.location.href='absensi.php?kelas_id=$id_kelas';
        </script>";
    }
}

// --- LOGIC 2: PERSIAPAN TAMPILAN ---
$kelas_id = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
$tgl_hari_ini = date('Y-m-d');
?>

<div class="card">
    <h2 class="card-title"><i class="fas fa-calendar-day"></i> Absensi Harian</h2>

    <form method="GET" action="" class="filter-box">
        <label style="font-weight:bold; display:block; margin-bottom:5px;">Pilih Kelas:</label>
        <select name="kelas_id" onchange="this.form.submit()" class="form-control">
            <option value="">-- Pilih Kelas --</option>
            <?php
            $q_kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat ASC, jurusan ASC");
            while ($k = mysqli_fetch_assoc($q_kelas)) {
                $selected = ($kelas_id == $k['id_kelas']) ? 'selected' : '';
                echo "<option value='{$k['id_kelas']}' $selected>{$k['nama_kelas']}</option>";
            }
            ?>
        </select>
    </form>

    <?php if ($kelas_id != ''): ?>
        <form method="POST" action="">
            <input type="hidden" name="kelas_id_hidden" value="<?= $kelas_id; ?>">

            <div class="filter-box">
                <div style="margin-bottom: 15px;">
                    <label style="font-weight: bold; display:block;">Tanggal Absen:</label>
                    <input type="date" name="tanggal" value="<?= $tgl_hari_ini; ?>" required class="form-control">
                </div>

                <div>
                    <label style="font-weight: bold; display:block;">Mata Pelajaran:</label>
                    <select name="id_mapel" required class="form-control">
                        <option value="">-- Pilih Mapel --</option>
                        <?php
                        if (!empty($kelas_id)) {
                            // --- LOGIKA FILTER: Hanya ambil mapel milik kelas ini ---
                            $query_mapel = "SELECT * FROM mapel WHERE id_kelas = '$kelas_id' ORDER BY nama_mapel ASC";
                            $q_mapel = mysqli_query($conn, $query_mapel);

                            if (mysqli_num_rows($q_mapel) > 0) {
                                while ($m = mysqli_fetch_assoc($q_mapel)) {
                                    echo "<option value='{$m['id_mapel']}'>{$m['nama_mapel']}</option>";
                                }
                            } else {
                                echo "<option value='' disabled>Belum ada mapel di kelas ini</option>";
                            }
                        } else {
                            echo "<option value='' disabled>Pilih Kelas Terlebih Dahulu</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table-absensiswa">
                    <thead>
                        <tr>
                            <th style="text-align:center;">No</th>
                            
                            <!-- <th class="nis-col">NIS</th> -->
                            
                            <th>Nama Siswa</th>
                            
                            <th>Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Ambil Detail Kelas untuk tahu Jurusan & Tingkat
                        $cek_k = mysqli_query($conn, "SELECT tingkat, jurusan FROM kelas WHERE id_kelas = '$kelas_id'");
                        $data_k = mysqli_fetch_assoc($cek_k);

                        if ($data_k) {
                            $tingkat = $data_k['tingkat'];
                            $jurusan = $data_k['jurusan'];
                            $tabel_target = "siswa_kelas" . $tingkat;

                            // Query Siswa
                            $q_siswa = mysqli_query($conn, "SELECT * FROM $tabel_target WHERE jurusan = '$jurusan' ORDER BY nama ASC");

                            if (mysqli_num_rows($q_siswa) > 0) {
                                $no = 1;
                                while ($s = mysqli_fetch_assoc($q_siswa)) {
                                    $nis = $s['nis'];
                        ?>
                            <tr>
                                <td style="text-align:center;"><?= $no++; ?></td>
                                
                                <!-- <td class="nis-col"><?= $nis; ?></td> -->

                                <td>
                                    <span class="siswa-nama"><?= $s['nama']; ?></span>
                                    
                                    <div class="mobile-nis">NIS: <?= $nis; ?></div>

                                    <input type="hidden" name="nama_siswa[<?= $nis; ?>]" value="<?= $s['nama']; ?>">
                                    <input type="hidden" name="jurusan[<?= $nis; ?>]" value="<?= $jurusan; ?>">
                                    <input type="hidden" name="tingkat[<?= $nis; ?>]" value="<?= $tingkat; ?>">
                                </td>

                                <td style="text-align:center;">
                                    <select name="status[<?= $nis ?>]" class="select-status status-H" onchange="ubahWarna(this)">
                                        <option value="H">Hadir</option>
                                        <option value="S">Sakit</option>
                                        <option value="I">Izin</option>
                                        <option value="A">Alpha</option>
                                    </select>
                                </td>
                            </tr>
                        <?php
                                }
                            } else {
                                echo "<tr><td colspan='4' style='text-align:center; padding:20px;'>Tidak ada siswa ditemukan.</td></tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top: 20px; text-align: right;">
                <button type="submit" name="simpan_absen" class="btn btn-primary">
                    <i class="fas fa-save"></i> Simpan Absensi
                </button>
            </div>
        </form>

    <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-chalkboard-teacher" style="font-size: 3rem; color: #ddd; margin-bottom: 10px;"></i>
            <p>Silakan pilih <b>Kelas</b> terlebih dahulu untuk mulai mengabsen.</p>
        </div>
    <?php endif; ?>
</div>

<script src="../js/script.js"></script>

<?php include "layout/footer.php"; ?>