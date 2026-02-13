<?php
session_start();
require_once "../config/database.php";

$active_tab = 'dashboard'; 
include "layout/header.php";

// --- LOGIC PHP: MENGHITUNG DATA REAL HARI INI ---

// 1. Ambil Tanggal Hari Ini
$tgl_hari_ini = date('Y-m-d');

// 2. Query Menghitung Jumlah Status (H, S, I, A)
// Kita gunakan fungsi COUNT untuk menghitung baris data di tabel 'absensi'

// Hitung HADIR
$q_h = mysqli_query($conn, "SELECT COUNT(*) as jum FROM absensi_siswa WHERE status='H' AND tanggal='$tgl_hari_ini'");
$d_h = mysqli_fetch_assoc($q_h);
$hadir = $d_h['jum'];

// Hitung SAKIT
$q_s = mysqli_query($conn, "SELECT COUNT(*) as jum FROM absensi_siswa WHERE status='S' AND tanggal='$tgl_hari_ini'");
$d_s = mysqli_fetch_assoc($q_s);
$sakit = $d_s['jum'];

// Hitung IZIN
$q_i = mysqli_query($conn, "SELECT COUNT(*) as jum FROM absensi_siswa WHERE status='I' AND tanggal='$tgl_hari_ini'");
$d_i = mysqli_fetch_assoc($q_i);
$izin = $d_i['jum'];

// Hitung ALPHA
$q_a = mysqli_query($conn, "SELECT COUNT(*) as jum FROM absensi_siswa WHERE status='A' AND tanggal='$tgl_hari_ini'");
$d_a = mysqli_fetch_assoc($q_a);
$alpha = $d_a['jum'];

// 3. Logic Menghitung Persentase (Biar tampilan % tidak error division by zero)
$total_masuk_data = $hadir + $sakit + $izin + $alpha;

if ($total_masuk_data > 0) {
    $persen_h = round(($hadir / $total_masuk_data) * 100);
    $persen_s = round(($sakit / $total_masuk_data) * 100);
    $persen_i = round(($izin / $total_masuk_data) * 100);
    $persen_a = round(($alpha / $total_masuk_data) * 100);
} else {
    // Jika belum ada data masuk hari ini, set 0 semua
    $persen_h = 0; $persen_s = 0; $persen_i = 0; $persen_a = 0;
}
?>

<div class="card">
    <h2 class="card-title">
        <i class="fas fa-chart-line"></i> Ringkasan Absensi Hari Ini
    </h2>
    
    <div class="stats">
        <div class="stat-card hadir">
            <div class="stat-value"><?= $hadir; ?></div>
            <div class="stat-label">Hadir</div>
            <span class="stat-sub"><?= $persen_h; ?>%</span>
        </div>

        <div class="stat-card sakit">
            <div class="stat-value"><?= $sakit; ?></div>
            <div class="stat-label">Sakit</div>
            <span class="stat-sub"><?= $persen_s; ?>%</span>
        </div>

        <div class="stat-card izin">
            <div class="stat-value"><?= $izin; ?></div>
            <div class="stat-label">Izin</div>
            <span class="stat-sub"><?= $persen_i; ?>%</span>
        </div>

        <div class="stat-card alpha">
            <div class="stat-value"><?= $alpha; ?></div>
            <div class="stat-label">Alpha</div>
            <span class="stat-sub"><?= $persen_a; ?>%</span>
        </div>
    </div>
</div>

<div class="card">
    <h2 class="card-title">
        <i class="fas fa-info-circle"></i> Info Sistem
    </h2>
    <p>Data di atas adalah rekapitulasi absensi yang diinput pada tanggal <b><?= date('d-m-Y'); ?></b>.</p>
    <p>Jika angka masih 0, silakan lakukan absensi di menu <b>Absensi Harian</b>.</p>
</div>

<?php include "layout/footer.php"; ?>