<?php
session_start();
require_once "../config/database.php";

// Cek Login
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$active_tab = 'profil';
include "layout/header.php";

$id_guru = $_SESSION['id_guru'];

// Ambil data guru
$query = mysqli_query($conn, "SELECT * FROM guru WHERE id_guru = '$id_guru'");
$guru  = mysqli_fetch_assoc($query);

// Data default jika null (Mencegah Error Deprecated)
$nama_guru = $guru['nama_guru'] ?? 'Tanpa Nama';
$username  = $guru['username'] ?? '-';
$nip       = $guru['nip'] ?? '-'; // Jika ada kolom NIP
?>

<style>
</style>

<div class="main-content">
    
    <div class="profile-card">
        <div class="profile-header-bg">
            <div class="profile-avatar-container">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($nama_guru); ?>&background=random&size=128" 
                     alt="Foto Guru" class="profile-avatar">
            </div>
        </div>

        <div class="profile-body">
            <h2 class="profile-name"><?= htmlspecialchars($nama_guru); ?></h2>
            <div class="profile-role">Guru Pengajar</div>

            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="profile-details">
                            
                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-id-card margin-right-5"></i> NIP / ID</span>
                                <span class="detail-value"><?= $nip; ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-user-circle margin-right-5"></i> Username</span>
                                <span class="detail-value"><?= htmlspecialchars($username); ?></span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-lock margin-right-5"></i> Password</span>
                                <span class="detail-value">********</span>
                            </div>

                            <div class="detail-item">
                                <span class="detail-label"><i class="fas fa-check-circle margin-right-5"></i> Status Akun</span>
                                <span class="detail-value text-success">Aktif</span>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

                <a href="#" class="btn-keluar" onclick="bukaModal()">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>

        </div>
    </div>
    <div id="modalKeluar" class="modal-overlay">
    <div class="modal-box">
        
        <div class="modal-icon">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h3>Konfirmasi</h3>
        <p>Apakah Anda yakin ingin keluar?</p>
        
        <div class="modal-buttons">
            <button class="btn-batal" onclick="tutupModal()">Batal</button>
            
            <a href="../auth/logout.php" class="btn-ya">Ya, Keluar</a>
        </div>
        <script src="../js/guru.js"></script>
    </div>
</div>
</div>

<?php include "layout/footer.php"; ?>