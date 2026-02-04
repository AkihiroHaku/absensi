<?php
session_start();
require_once "../config/database.php";

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Validasi ID
$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: data_guru.php");
    exit;
}

// Hapus data guru
$query = mysqli_query($conn, "DELETE FROM guru WHERE id_guru = '$id'");

header("Location: data_guru.php");
exit;
