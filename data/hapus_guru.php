<?php
session_start();
require_once "../config/database.php";

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil id_guru dari URL
$id_guru = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_guru <= 0) {
    header("Location: data_guru.php");
    exit;
}

// 1. Ambil id_user dari tabel guru
$q = mysqli_query(
    $conn,
    "SELECT id_user FROM guru WHERE id_guru = $id_guru"
);

if (!$q || mysqli_num_rows($q) === 0) {
    header("Location: data_guru.php");
    exit;
}

$data = mysqli_fetch_assoc($q);
$id_user = $data['id_user'];

// 2. Hapus USERS (guru akan ikut terhapus karena CASCADE)
mysqli_query(
    $conn,
    "DELETE FROM users WHERE id_user = $id_user"
);

header("Location: data_guru.php");
exit;