<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Rekap Absensi Siswa";
$active_menu = 'absensi';

// Inisialisasi Filter
$bulan_ini = date('m');
$tahun_ini = date('Y');

$f_kelas = isset($_GET['kelas']) ? $_GET['kelas'] : '';
$f_mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$f_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : $bulan_ini;
$f_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $tahun_ini;

$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Siswa</title>
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
            
            <div class="rekap-header">
                <div>
                    <h3><i class="fas fa-clipboard-list"></i> Rekap Absensi Siswa</h3>
                    <p class="rekap-desc">Laporan Bulanan Siswa</p>
                </div>
            </div>

            <form action="" method="GET" class="filter-box" id="formFilter">
                
                <div class="filter-item">
                    <label class="filter-label">Kelas</label>
                    <select name="kelas" class="form-control" onchange="this.form.submit()" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php 
                        $q_kelas = mysqli_query($conn, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
                        while($k = mysqli_fetch_assoc($q_kelas)): ?>
                            <option value="<?= $k['id_kelas'] ?>" <?= ($k['id_kelas'] == $f_kelas) ? 'selected' : '' ?>>
                                <?= $k['nama_kelas'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="filter-item">
                    <label class="filter-label">Jenis Laporan / Mapel</label>
                    <select name="mapel" class="form-control">
                        <option value="">-- Pilih --</option>
                        
                        <option value="harian" style="font-weight:bold;" <?= ($f_mapel == 'harian') ? 'selected' : '' ?>>
                            Rekap Harian 
                        </option>

                        <?php 
                        if ($f_kelas) {
                            $query_mapel = "SELECT * FROM mapel WHERE id_kelas = '$f_kelas' ORDER BY nama_mapel ASC";
                            $q_mapel = mysqli_query($conn, $query_mapel);
                            
                            if(mysqli_num_rows($q_mapel) > 0){
                                while($m = mysqli_fetch_assoc($q_mapel)): ?>
                                    <option value="<?= $m['id_mapel'] ?>" <?= ($m['id_mapel'] == $f_mapel) ? 'selected' : '' ?>>
                                        <?= $m['nama_mapel'] ?>
                                    </option>
                                <?php endwhile;
                            } else {
                                echo "<option value='' disabled>Tidak ada mapel</option>";
                            }
                        } else {
                            echo "<option value='' disabled>Pilih Kelas Dulu</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="filter-item-small">
                    <label class="filter-label">Bulan</label>
                    <select name="bulan" class="form-control">
                        <?php foreach($nama_bulan as $k => $v): ?>
                            <option value="<?= $k ?>" <?= ($k == $f_bulan) ? 'selected' : '' ?>><?= $v ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-item-small">
                    <label class="filter-label">Tahun</label>
                    <select name="tahun" class="form-control">
                        <?php for($y=2024; $y<=date('Y')+2; $y++): ?>
                            <option value="<?= $y ?>" <?= ($y == $f_tahun) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="filter-btn-container">
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search"></i> Tampilkan
                    </button>
                </div>
            </form>

            <?php if ($f_kelas && $f_mapel): ?>
                
                <div class="table-responsive">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="5%" class="th-center">No</th>
                                <th width="15%">NIS</th>
                                <th>Nama Siswa</th>
                                <th width="8%" class="th-center"><span class="dot dot-h"></span>H</th>
                                <th width="8%" class="th-center"><span class="dot dot-s"></span>S</th>
                                <th width="8%" class="th-center"><span class="dot dot-i"></span>I</th>
                                <th width="8%" class="th-center"><span class="dot dot-a"></span>A</th>
                                <th width="20%" class="th-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // TABEL SISWA 
                            $q_cek = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id_kelas='$f_kelas'");
                            $d_cek = mysqli_fetch_assoc($q_cek);
                            $nama_kelas_str = strtoupper($d_cek['nama_kelas']);

                            if (strpos($nama_kelas_str, '10') !== false || strpos($nama_kelas_str, 'X ') !== false || strpos($nama_kelas_str, 'X-') !== false) {
                                $tabel_siswa = 'siswa_kelas10';
                            } elseif (strpos($nama_kelas_str, '11') !== false || strpos($nama_kelas_str, 'XI') !== false) {
                                $tabel_siswa = 'siswa_kelas11';
                            } elseif (strpos($nama_kelas_str, '12') !== false || strpos($nama_kelas_str, 'XII') !== false) {
                                $tabel_siswa = 'siswa_kelas12';
                            } else {
                                $tabel_siswa = 'siswa_kelas10'; 
                            }

                            // LOGIKA QUERY UTAMA
                            
                            $query = "";

                            if ($f_mapel == 'harian') {
                                
                                $query = "SELECT * FROM $tabel_siswa ORDER BY nama ASC";
                            } else {

                                $query = "
                                    SELECT 
                                        s.nis, s.nama, 
                                        SUM(CASE WHEN a.status = 'H' THEN 1 ELSE 0 END) as jum_h,
                                        SUM(CASE WHEN a.status = 'S' THEN 1 ELSE 0 END) as jum_s,
                                        SUM(CASE WHEN a.status = 'I' THEN 1 ELSE 0 END) as jum_i,
                                        SUM(CASE WHEN a.status = 'A' THEN 1 ELSE 0 END) as jum_a,
                                        COUNT(a.id_absensi) as total_input
                                    FROM $tabel_siswa s
                                    LEFT JOIN absensi_siswa a 
                                        ON s.nis = a.nis 
                                        AND a.id_mapel = '$f_mapel'
                                        AND MONTH(a.tanggal) = '$f_bulan' 
                                        AND YEAR(a.tanggal) = '$f_tahun'
                                    GROUP BY s.nis
                                    ORDER BY s.nama ASC
                                ";
                            }

                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                echo "<tr><td colspan='8' class='msg-error'>Error: " . mysqli_error($conn) . "</td></tr>";
                            } elseif (mysqli_num_rows($result) > 0) {
                                $no = 1;
                                
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $nis = $row['nis'];
                                    
                                    // VARIABEL PENAMPUNG HASIL
                                    $h = 0; $s_cnt = 0; $i = 0; $a = 0; $persen = 0;

                                    // PENGHITUNGAN DATA 
                                    if ($f_mapel == 'harian') {
                                        // LOGIKA KHUSUS HARIAN
                                        $q_absen = mysqli_query($conn, "SELECT DAY(tanggal) as tgl, status FROM absensi_siswa 
                                                                        WHERE nis='$nis' 
                                                                        AND MONTH(tanggal)='$f_bulan' 
                                                                        AND YEAR(tanggal)='$f_tahun'
                                                                        ORDER BY tanggal ASC");
                                        $data_temp = [];
                                        
                                        while ($d = mysqli_fetch_assoc($q_absen)) {
                                            $tgl = $d['tgl'];
                                            $stat = $d['status'];
                                            
                                            if (!isset($data_temp[$tgl])) {
                                                $data_temp[$tgl] = $stat;
                                            } else {
                                                // cek prioritas
                                                $old = $data_temp[$tgl];
                                                if ($stat == 'H') $data_temp[$tgl] = 'H';
                                                elseif ($stat == 'S' && $old != 'H') $data_temp[$tgl] = 'S';
                                                elseif ($stat == 'I' && $old == 'A') $data_temp[$tgl] = 'I';
                                            }
                                        }

                                        // Hitung Total Final
                                        foreach ($data_temp as $final_stat) {
                                            if ($final_stat == 'H') $h++;
                                            elseif ($final_stat == 'S') $s_cnt++;
                                            elseif ($final_stat == 'I') $i++;
                                            elseif ($final_stat == 'A') $a++;
                                        }
                                        
                                        $total_hari = $h + $s_cnt + $i + $a;
                                        $persen = ($total_hari > 0) ? round(($h / $total_hari) * 100) : 0;

                                    } else {
                                        // AMBIL DARI QUERY SQL
                                        $h = $row['jum_h'];
                                        $s_cnt = $row['jum_s'];
                                        $i = $row['jum_i'];
                                        $a = $row['jum_a'];
                                        
                                        $total = $row['total_input'];
                                        $persen = ($total > 0) ? round(($h / $total) * 100) : 0;
                                    }

                                    // TAMPILAN BAR PROGRESS
                                    if ($persen >= 80) $class_bar = 'bg-high';
                                    elseif ($persen >= 50) $class_bar = 'bg-med';
                                    else $class_bar = 'bg-low';

                                    $class_alpha = ($a > 0) ? 'text-red' : 'text-mute';
                                    $nama_tampil = isset($row['nama']) ? $row['nama'] : (isset($row['nama_siswa']) ? $row['nama_siswa'] : '-');
                            ?>
                                <tr>
                                    <td class="td-num"><?= $no++; ?></td>
                                    <td class="font-mono"><?= $nis; ?></td>
                                    <td class="td-bold"><?= $nama_tampil; ?></td>
                                    <td class="td-num"><?= $h; ?></td>
                                    <td class="td-num"><?= $s_cnt; ?></td>
                                    <td class="td-num"><?= $i; ?></td>
                                    <td class="td-num <?= $class_alpha; ?>"><?= $a; ?></td>
                                    <td>
                                        <div class="progress-wrapper">
                                            <div class="progress-track">
                                                <div class="progress-fill <?= $class_bar; ?>" style="width: <?= $persen; ?>%;"></div>
                                            </div>
                                            <span class="progress-text"><?= $persen; ?>%</span>
                                        </div>
                                    </td>
                                </tr>
                            <?php 
                                }
                            } else {
                                echo "<tr><td colspan='8' class='msg-info'>Belum ada data siswa di kelas ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                
                <div class="empty-state">
                    <i class="fas fa-filter empty-icon"></i>
                    <p class="empty-text">
                        Silakan pilih <b>Kelas</b> dan <b>Jenis Laporan</b> di atas<br>
                        untuk menampilkan data.
                    </p>
                </div>

            <?php endif; ?>

            <div>
                <a href="../data/menu_absensi.php" class="link-back">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

        </div>

        <script src="../js/admin.js"></script> 
    <script src="../js/script.js"></script>
    </div>
</body>
</html>