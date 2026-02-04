<div class="top-bar">
    <div class="welcome-text">
        <h2><?= isset($judul_halaman) ? $judul_halaman : 'Dashboard'; ?></h2>
        
    </div>
    
    <div class="profile-box">
        <i class="fas fa-user-circle"></i> <?= isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'User'; ?>
    </div>
</div>