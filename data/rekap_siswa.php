<?php
session_start();
require_once "../config/database.php";

// 1. Cek Login
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$judul_halaman = "Rekap Absensi Siswa";
$active_menu = 'absensi';

// 2. Inisialisasi Filter
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
                    <p class="rekap-desc">Laporan Bulanan per Mata Pelajaran</p>
                </div>
                <?php if($f_kelas && $f_mapel): ?>
                <?php endif; ?>
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
                    <label class="filter-label">Mata Pelajaran</label>
                    <select name="mapel" class="form-control">
                        <option value="">-- Pilih Mapel --</option>
                        <?php 
                        // LOGIKA BARU (Sesuai Struktur Tabel 'mapel' kamu)
                        if ($f_kelas) {
                            // Ambil mapel yang id_kelas-nya SAMA dengan kelas yang sedang dipilih
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
                                <th width="8%" class="th-center"><span class="dot dot-i"></span>I</th>
                                <th width="8%" class="th-center"><span class="dot dot-s"></span>S</th>
                                <th width="8%" class="th-center"><span class="dot dot-a"></span>A</th>
                                <th width="20%" class="th-center">Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // --- LOGIKA MEMILIH TABEL SISWA (Detektif) ---

                            $q_cek = mysqli_query($conn, "SELECT nama_kelas FROM kelas WHERE id_kelas='$f_kelas'");
                            $d_cek = mysqli_fetch_assoc($q_cek);
                            $nama_kelas_str = strtoupper($d_cek['nama_kelas']); // Ubah ke Huruf Besar biar mudah dicek

                            if (strpos($nama_kelas_str, '10') !== false || strpos($nama_kelas_str, 'X ') !== false || strpos($nama_kelas_str, 'X-') !== false) {
                                $tabel_siswa = 'siswa_kelas10';
                            }
                            elseif (strpos($nama_kelas_str, '11') !== false || strpos($nama_kelas_str, 'XI') !== false) {
                                $tabel_siswa = 'siswa_kelas11';
                            }
                            elseif (strpos($nama_kelas_str, '12') !== false || strpos($nama_kelas_str, 'XII') !== false) {
                                $tabel_siswa = 'siswa_kelas12';
                            } 
                            else {
                                $tabel_siswa = 'siswa_kelas10'; 
                            }

                            // --- QUERY UTAMA ---
                            $query = "
                                SELECT 
                                    s.nis, s.nama, /* Pastikan di tabelmu kolomnya 'nama' atau 'nama_siswa'? Sesuaikan disini */
                                    SUM(CASE WHEN a.status = 'H' THEN 1 ELSE 0 END) as jum_h,
                                    SUM(CASE WHEN a.status = 'I' THEN 1 ELSE 0 END) as jum_i,
                                    SUM(CASE WHEN a.status = 'S' THEN 1 ELSE 0 END) as jum_s,
                                    SUM(CASE WHEN a.status = 'A' THEN 1 ELSE 0 END) as jum_a,
                                    COUNT(a.id_absensi) as total_input
                                FROM $tabel_siswa s
                                LEFT JOIN absensi_siswa a 
                                    ON s.nis = a.nis 
                                    AND a.id_mapel = '$f_mapel' 
                                    AND MONTH(a.tanggal) = '$f_bulan' 
                                    AND YEAR(a.tanggal) = '$f_tahun'
                                /* WHERE s.id_kelas = '$f_kelas'  <-- HAPUS baris ini jika tabel siswa pisah TIDAK punya kolom id_kelas */
                                GROUP BY s.nis
                                ORDER BY s.nama ASC
                            ";

                            $result = mysqli_query($conn, $query);

                            if (!$result) {
                                // Info Error jika tabel tidak ditemukan
                                echo "<tr><td colspan='8' class='msg-error'>
                                    Gagal mengambil data dari tabel <b>$tabel_siswa</b>.<br>
                                    <small>" . mysqli_error($conn) . "</small>
                                </td></tr>";
                            } elseif (mysqli_num_rows($result) > 0) {
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $total = $row['total_input'];
                                    $persen = ($total > 0) ? round(($row['jum_h'] / $total) * 100) : 0;
                                    
                                    if ($persen >= 80) $class_bar = 'bg-high';
                                    elseif ($persen >= 50) $class_bar = 'bg-med';
                                    else $class_bar = 'bg-low';

                                    $class_alpha = ($row['jum_a'] > 0) ? 'text-red' : 'text-mute';
                                    
                                    // Cek nama kolom nama (nama / nama_siswa)
                                    $nama_tampil = isset($row['nama']) ? $row['nama'] : $row['nama_siswa'];
                            ?>
                                <tr>
                                    <td class="td-num"><?= $no++; ?></td>
                                    <td class="font-mono"><?= $row['nis']; ?></td>
                                    <td class="td-bold"><?= $nama_tampil; ?></td>
                                    <td class="td-num"><?= $row['jum_h']; ?></td>
                                    <td class="td-num"><?= $row['jum_i']; ?></td>
                                    <td class="td-num"><?= $row['jum_s']; ?></td>
                                    <td class="td-num <?= $class_alpha; ?>"><?= $row['jum_a']; ?></td>
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
                                echo "<tr><td colspan='8' class='msg-info'>Belum ada data siswa di kelas ($nama_kelas_str) ini.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            <?php else: ?>
                
                <div class="empty-state">
                    <i class="fas fa-filter empty-icon"></i>
                    <p class="empty-text">
                        Silakan pilih <b>Kelas</b> dan <b>Mata Pelajaran</b> di atas<br>
                        untuk menampilkan laporan absensi siswa.
                    </p>
                </div>

            <?php endif; ?>

            <div>
                <a href="../data/menu_absensi.php" class="link-back">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

        </div>
        <script src="../js/admin.js"></script>
        <script src="../js/script.js"></script>
    </div>
</body>
</html>