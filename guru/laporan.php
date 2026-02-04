<?php
session_start();
require_once "../config/database.php";

$active_tab = 'laporan';
include "layout_header.php";

// --- KONFIGURASI FILTER ---
$bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$kelas_id    = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';

// Array Nama Bulan
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e9ecef; padding-bottom: 15px; margin-bottom: 20px;">
        <h2 class="card-title" style="border:none; margin:0; padding:0;">
            <i class="fas fa-file-alt"></i> Laporan Absensi Bulanan
        </h2>
        <button onclick="window.print()" class="btn btn-primary" style="background: #6c757d;">
            <i class="fas fa-print"></i> Cetak / PDF
        </button>
    </div>

    <form method="GET" action="" style="background: #f8f9fa; padding: 15px; border-radius: 10px; display: flex; gap: 10px; flex-wrap: wrap; align-items: end;">
        
        <div style="flex: 1; min-width: 200px;">
            <label style="font-size: 0.9rem; font-weight: bold;">Kelas:</label>
            <select name="kelas_id" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                <option value="">-- Pilih Kelas --</option>
                <?php
                $q_kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY tingkat ASC, jurusan ASC");
                while ($k = mysqli_fetch_assoc($q_kelas)) {
                    $sel = ($kelas_id == $k['id_kelas']) ? 'selected' : '';
                    echo "<option value='{$k['id_kelas']}' $sel>{$k['nama_kelas']}</option>";
                }
                ?>
            </select>
        </div>

        <div style="width: 150px;">
            <label style="font-size: 0.9rem; font-weight: bold;">Bulan:</label>
            <select name="bulan" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                <?php foreach ($nama_bulan as $key => $val): ?>
                    <option value="<?= $key; ?>" <?= ($bulan_pilih == $key) ? 'selected' : ''; ?>>
                        <?= $val; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="width: 100px;">
            <label style="font-size: 0.9rem; font-weight: bold;">Tahun:</label>
            <select name="tahun" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px;">
                <?php
                $tahun_skrg = date('Y');
                for ($t = $tahun_skrg; $t >= $tahun_skrg - 2; $t--) {
                    $sel = ($tahun_pilih == $t) ? 'selected' : '';
                    echo "<option value='$t' $sel>$t</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="padding: 9px 20px;">
            <i class="fas fa-filter"></i> Tampilkan
        </button>
    </form>
    
    <hr style="margin: 20px 0; border: 0; border-top: 1px solid #eee;">

    <?php if ($kelas_id == ''): ?>
        
        <div class="empty-state">
            <i class="fas fa-search" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
            <p style="color: #666;">Silakan pilih <b>Kelas</b> terlebih dahulu untuk melihat laporan.</p>
        </div>

    <?php else: ?>
        
        <?php
            // Ambil nama kelas untuk judul
            $q_info = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id_kelas='$kelas_id'");
            $d_info = mysqli_fetch_assoc($q_info);
            $nama_kelas_label = $d_info['nama_kelas'] ?? '-';
        ?>

        <div style="text-align: center; margin-bottom: 20px;">
            <h3 style="margin:0;">Rekap Absensi Kelas <?= $nama_kelas_label; ?></h3>
            <p style="color:#666;">Periode: <?= $nama_bulan[$bulan_pilih]; ?> <?= $tahun_pilih; ?></p>
        </div>

        <div class="table-responsive">
            <table style="width: 100%; border: 1px solid #ddd;">
                <thead>
                    <tr style="background: #f1f3f5;">
                        <th style="width: 5%; text-align: center;">No</th>
                        <th style="width: 15%;">NIS</th>
                        <th>Nama Siswa</th>
                        <th style="width: 8%; text-align: center; color: #28a745;">Hadir</th>
                        <th style="width: 8%; text-align: center; color: #ffc107;">Sakit</th>
                        <th style="width: 8%; text-align: center; color: #007bff;">Izin</th>
                        <th style="width: 8%; text-align: center; color: #dc3545;">Alpha</th>
                        <th style="width: 10%; text-align: center;">% Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // --- LOGIC MENGAMBIL DATA SISWA (SAMA SEPERTI DI FILE SISWA.PHP) ---
                    // 1. Cek detail kelas untuk tahu harus ambil dari tabel mana
                    $cek_k = mysqli_query($conn, "SELECT tingkat, jurusan FROM kelas WHERE id_kelas='$kelas_id'");
                    $dat_k = mysqli_fetch_assoc($cek_k);
                    
                    if ($dat_k) {
                        $tingkat = $dat_k['tingkat'];
                        $jurusan = $dat_k['jurusan'];
                        $tabel_siswa = "siswa_kelas" . $tingkat;

                        // 2. Query Siswa
                        $q_siswa = mysqli_query($conn, "SELECT * FROM $tabel_siswa WHERE jurusan='$jurusan' ORDER BY nama ASC");

                        if (mysqli_num_rows($q_siswa) > 0) {
                            $no = 1;
                            while ($s = mysqli_fetch_assoc($q_siswa)) {
                                // --- DISINI NANTI KITA HITUNG JUMLAH ABSENSI DARI DATABASE ---
                                // Karena tabel absensi belum ada, kita set 0 dulu (DUMMY)
                                $h = 0; $s_cnt = 0; $i = 0; $a = 0; 
                                $total_hari = 20; // Anggap sebulan sekolah 20 hari
                                $persen = ($h / $total_hari) * 100;
                    ?>
                        <tr>
                            <td style="text-align: center;"><?= $no++; ?></td>
                            <td><?= $s['nis']; ?></td>
                            <td><?= $s['nama']; ?></td> <td style="text-align: center; font-weight:bold;"><?= $h; ?></td>
                            <td style="text-align: center; font-weight:bold;"><?= $s_cnt; ?></td>
                            <td style="text-align: center; font-weight:bold;"><?= $i; ?></td>
                            <td style="text-align: center; font-weight:bold;"><?= $a; ?></td>
                            <td style="text-align: center;">
                                <span class="badge" style="background: #e9ecef; color: #333;">
                                    <?= round($persen); ?>%
                                </span>
                            </td>
                        </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center; padding:20px;'>Tidak ada siswa di kelas ini.</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px; font-size: 0.8rem; color: #666;">
            * Data di atas adalah rekapitulasi otomatis berdasarkan input absensi harian.
        </div>

    <?php endif; ?>
</div>

<?php include "layout_footer.php"; ?>