<?php
session_start();
require_once "../config/database.php";

$active_tab = 'laporan';

include "layout/header.php";

?>
<div class="header-print" style="display: none; margin-bottom: 20px;">
    <div style="display: flex; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 10px;">
        <div style="width: 80px; text-align:center;">
            <img src="../assets/img/smkislam.png" style="width: 70px;"> 
        </div>
        <div style="flex: 1; text-align: center;">
            <h2 style="margin: 0; font-size: 24px;">SMK ISLAM SALAKBROJO</h2>
            <p style="margin: 0; font-size: 12px; font-weight: bold;">Laporan Rekapitulasi Absensi Siswa</p>
        </div>
    </div>
</div>
<?php

// --- KONFIGURASI FILTER ---
$bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$kelas_id    = isset($_GET['kelas_id']) ? $_GET['kelas_id'] : '';
$mapel_id    = isset($_GET['mapel_id']) ? $_GET['mapel_id'] : ''; // Default kosong

// Array Nama Bulan
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<div class="card">
    <div class="card-header-actions">
        <h2 class="card-title card-title-clean">
            <i class="fas fa-file-alt"></i> Laporan Absensi Bulanan
        </h2>
    </div>

    <form method="GET" action="" class="filter-form">
        
        <div class="filter-group">
            <label class="filter-label">Kelas:</label>
            <select name="kelas_id" onchange="this.form.submit()" class="filter-select">
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

        <div class="filter-group">
            <label class="filter-label">Jenis Laporan / Mapel:</label>
            <select name="mapel_id" class="filter-select">
                <option value="">-- Pilih Laporan --</option>
                
                <option value="harian" style="font-weight:bold;" <?= ($mapel_id == 'harian') ? 'selected' : ''; ?>>
                    Rekap Harian
                </option>

                <?php
                // Tampilkan Mapel Sesuai Kelas
                if ($kelas_id) {
                    $q_m = mysqli_query($conn, "SELECT * FROM mapel WHERE id_kelas='$kelas_id' ORDER BY nama_mapel ASC");
                    while ($m = mysqli_fetch_assoc($q_m)) {
                        $sel = ($m['id_mapel'] == $mapel_id) ? 'selected' : '';
                        echo "<option value='{$m['id_mapel']}' $sel>{$m['nama_mapel']}</option>";
                    }
                } else {
                    echo "<option value='' disabled>Pilih Kelas Dulu</option>";
                }
                ?>
            </select>
        </div>

        <div class="filter-group-sm">
            <label class="filter-label">Bulan:</label>
            <select name="bulan" class="filter-select">
                <?php foreach ($nama_bulan as $key => $val): ?>
                    <option value="<?= $key; ?>" <?= ($bulan_pilih == $key) ? 'selected' : ''; ?>>
                        <?= $val; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group-xs">
            <label class="filter-label">Tahun:</label>
            <select name="tahun" class="filter-select">
                <?php
                $tahun_skrg = date('Y');
                for ($t = $tahun_skrg; $t >= $tahun_skrg - 2; $t--) {
                    $sel = ($tahun_pilih == $t) ? 'selected' : '';
                    echo "<option value='$t' $sel>$t</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary btn-filter">
            <i class="fas fa-filter"></i> Tampilkan
        </button>
    </form>
    
    <hr class="report-divider">

    <?php if ($kelas_id == ''): ?>
        
        <div class="empty-state-report">
            <i class="fas fa-search empty-icon"></i>
            <p class="empty-text">Silakan pilih <b>Kelas</b> terlebih dahulu untuk melihat laporan.</p>
        </div>
    <?php else: ?>
        
        <?php
            // Judul Laporan Dinamis
            $q_info = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id_kelas='$kelas_id'");
            $d_info = mysqli_fetch_assoc($q_info);
            $nama_kelas_label = $d_info['nama_kelas'] ?? '-';
            
            $judul_sub = "Rekap Absensi Harian";
            if($mapel_id != 'harian' && !empty($mapel_id)) {
                $q_mpl = mysqli_query($conn, "SELECT nama_mapel FROM mapel WHERE id_mapel='$mapel_id'");
                $d_mpl = mysqli_fetch_assoc($q_mpl);
                $judul_sub = "Rekap Mapel: " . ($d_mpl['nama_mapel'] ?? '-');
            }
        ?>

        <div class="report-header">
            <h3 class="report-title"><?= $judul_sub; ?> - Kelas <?= $nama_kelas_label; ?></h3>
            <p class="report-period">Periode: <?= $nama_bulan[$bulan_pilih]; ?> <?= $tahun_pilih; ?></p>
        </div>
        <div style="text-align: right; margin-bottom: 10px;">
            <button onclick="window.print()" class="btn-cetak">
                <i class="fas fa-print"></i> Cetak Laporan
            </button>
        </div>
        <div class="table-responsive">
            <table class="table-report" border="1" style="width:100%; border-collapse: collapse; font-size: 12px;">
                <thead>
                    <tr style="background: #f4f4f4;">
                        <th rowspan="2" width="30">No</th>
                        <th rowspan="2" style="min-width:150px;">Nama Siswa</th>
                        <?php
                        $jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan_pilih, $tahun_pilih);
                        for ($d = 1; $d <= $jumlah_hari; $d++) {
                            echo "<th rowspan='2' style='width: 25px; text-align:center; font-size:11px;'>$d</th>";
                        }
                        ?>
                        <th colspan="4" style="text-align:center;">Total</th>
                        <th rowspan="2" style="text-align:center;">%</th>
                    </tr>
                    <tr style="background: #f4f4f4;">
                        <th class="text-hadir" title="Hadir">H</th>
                        <th class="text-sakit" title="Sakit">S</th>
                        <th class="text-izin" title="Izin">I</th>
                        <th class="text-alpha" title="Alpha">A</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil Info Kelas (Tingkat & Jurusan)
                    $cek_k = mysqli_query($conn, "SELECT tingkat, jurusan FROM kelas WHERE id_kelas='$kelas_id'");
                    $dat_k = mysqli_fetch_assoc($cek_k);

                    // --- PERBAIKAN 1: Pastikan data kelas ditemukan ---
                    if ($dat_k) {
                        $tingkat = $dat_k['tingkat'];
                        $jurusan = $dat_k['jurusan'];
                        $tabel_siswa = "siswa_kelas" . $tingkat;

                        // Query Siswa
                        $q_siswa = mysqli_query($conn, "SELECT * FROM $tabel_siswa WHERE jurusan='$jurusan' ORDER BY nama ASC");

                        if (mysqli_num_rows($q_siswa) > 0) {
                            $no = 1;
                            while ($s = mysqli_fetch_assoc($q_siswa)) {
                                $nis = $s['nis'];
                                
                                // --- LOGIKA QUERY PINTAR ---
                                $mapel_filter = "";
                                
                                // Jika User memilih 'harian' atau Kosong -> JANGAN difilter ID Mapel (Ambil semua)
                                if ($mapel_id == 'harian' || empty($mapel_id)) {
                                    $mapel_filter = ""; 
                                } else {
                                    // Jika User memilih Mapel Tertentu -> Filter ID Mapel
                                    $mapel_filter = "AND id_mapel='$mapel_id'";
                                }

                                // Query Ambil Data Absen
                                $q_cek = mysqli_query($conn, "SELECT DAY(tanggal) as tgl, status FROM absensi_siswa 
                                                              WHERE nis='$nis' 
                                                              AND MONTH(tanggal)='$bulan_pilih' 
                                                              AND YEAR(tanggal)='$tahun_pilih'
                                                              $mapel_filter
                                                              ORDER BY tanggal ASC");
                                
                                // Array Penampung
                                $data_absen = [];

                                // --- LOGIKA PENYIMPULAN (Hukum Rimba) ---
                                while ($row = mysqli_fetch_assoc($q_cek)) {
                                    $tgl = $row['tgl'];
                                    $status_baru = $row['status'];

                                    if (!isset($data_absen[$tgl])) {
                                        $data_absen[$tgl] = $status_baru;
                                    } else {
                                        // Jika sudah ada data, ambil yang paling kuat (H > S > I > A)
                                        $status_lama = $data_absen[$tgl];
                                        
                                        if ($status_baru == 'H') { $data_absen[$tgl] = 'H'; }
                                        elseif ($status_baru == 'S' && $status_lama != 'H') { $data_absen[$tgl] = 'S'; }
                                        elseif ($status_baru == 'I' && $status_lama == 'A') { $data_absen[$tgl] = 'I'; }
                                    }
                                }

                                // Hitung Total dari Array Final
                                $h = 0; $sa = 0; $iz = 0; $al = 0;
                                foreach ($data_absen as $sts) {
                                    if ($sts == 'H') $h++;
                                    elseif ($sts == 'S') $sa++;
                                    elseif ($sts == 'I') $iz++;
                                    elseif ($sts == 'A') $al++;
                                }

                                // Hitung Persentase
                                $total_absen = $h + $sa + $iz + $al;
                                $persen = ($total_absen > 0) ? round(($h / $total_absen) * 100) : 0;
                    ?>
                        <tr>
                            <td class="th-center"><?= $no++; ?></td>
                            <td><?= $s['nama']; ?></td>
                            
                            <?php 
                            for ($d = 1; $d <= $jumlah_hari; $d++) {
                                $status = isset($data_absen[$d]) ? $data_absen[$d] : '-';
                                
                                // Warna Warni
                                $bg = ''; $simbol = '';
                                if ($status == 'H') { $simbol = 'H'; $bg = ''; } 
                                elseif ($status == 'S') { $simbol = 'S'; $bg = '#fff3cd'; } 
                                elseif ($status == 'I') { $simbol = 'I'; $bg = '#d1ecf1'; } 
                                elseif ($status == 'A') { $simbol = 'A'; $bg = '#f8d7da'; } 

                                echo "<td style='text-align:center; background:$bg; font-size:11px;'>$simbol</td>";
                            }
                            ?>
                            
                            <td class="th-center text-hadir"><b><?= $h; ?></b></td>
                            <td class="th-center text-sakit"><b><?= $sa; ?></b></td>
                            <td class="th-center text-izin"><b><?= $iz; ?></b></td>
                            <td class="th-center text-alpha"><b><?= $al; ?></b></td>
                            <td class="th-center"><?= $persen; ?>%</td>
                        </tr>
                    <?php
                            }
                        } else {
                            echo "<tr><td colspan='40' style='text-align:center; padding:20px;'>Belum ada siswa di kelas ini.</td></tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</div>

<?php include "layout/footer.php"; ?>