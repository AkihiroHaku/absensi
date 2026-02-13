<?php
session_start();
require_once "../config/database.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Cek Parameter NIS
if (isset($_GET['nis'])) {
    $nis = mysqli_real_escape_string($conn, $_GET['nis']);

    $berhasil = false;

    $query1 = mysqli_query($conn, "DELETE FROM siswa_kelas10 WHERE nis = '$nis'");
    if (mysqli_affected_rows($conn) > 0) {
        $berhasil = true;
    }

    if (!$berhasil) {
        $query2 = mysqli_query($conn, "DELETE FROM siswa_kelas11 WHERE nis = '$nis'");
        if (mysqli_affected_rows($conn) > 0) {
            $berhasil = true;
        }
    }

    if (!$berhasil) {
        $query3 = mysqli_query($conn, "DELETE FROM siswa_kelas12 WHERE nis = '$nis'");
        if (mysqli_affected_rows($conn) > 0) {
            $berhasil = true;
        }
    }
    if ($berhasil) {
        header("Location: data_siswa.php?id_kelas=" . $id_kelas);
        exit;
    } else {
        header("Location: data_siswa.php");
        exit;
    }   
} else {
    header("Location: data_siswa.php");
    exit;
}
?>