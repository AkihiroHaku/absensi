<?php
session_start();
require_once "../config/database.php";

// 1. Cek Login Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// 2. Cek Parameter NIS di URL
if (isset($_GET['nis'])) {
    $nis = mysqli_real_escape_string($conn, $_GET['nis']);

    // --- LOGIKA HAPUS DARI 3 TABEL ---
    // Karena kita tidak tahu siswa ini ada di tabel kelas 10, 11, atau 12,
    // kita coba hapus dari ketiganya secara berurutan.
    
    $berhasil = false;

    // Coba hapus dari Kelas 10
    $query1 = mysqli_query($conn, "DELETE FROM siswa_kelas10 WHERE nis = '$nis'");
    // mysqli_affected_rows mengecek apakah ada baris yang terhapus?
    if (mysqli_affected_rows($conn) > 0) {
        $berhasil = true;
    }

    // Jika belum ketemu, coba hapus dari Kelas 11
    if (!$berhasil) {
        $query2 = mysqli_query($conn, "DELETE FROM siswa_kelas11 WHERE nis = '$nis'");
        if (mysqli_affected_rows($conn) > 0) {
            $berhasil = true;
        }
    }

    // Jika masih belum ketemu, coba hapus dari Kelas 12
    if (!$berhasil) {
        $query3 = mysqli_query($conn, "DELETE FROM siswa_kelas12 WHERE nis = '$nis'");
        if (mysqli_affected_rows($conn) > 0) {
            $berhasil = true;
        }
    }

    // 3. Feedback ke User
    if ($berhasil) {
        echo "<script>
                alert('Data siswa berhasil dihapus!');
                document.location.href = 'data_siswa.php';
              </script>";
    } else {
        echo "<script>
                alert('Data siswa tidak ditemukan atau sudah terhapus!');
                document.location.href = 'data_siswa.php';
              </script>";
    }

} else {
    // Jika tidak ada NIS di URL, kembalikan ke halaman data
    header("Location: data_siswa.php");
    exit;
}
?>