<li><a href="/absensi/auth/logout.php">Logout</a></li><?php
                                                        $role = $_SESSION['role'];
                                                        ?>

<div class="sidebar">
    <img src="/absensi/assets/img/smkislam.png" width="140" alt="SMK Islam Salakbrojo Logo">

    <ul class="menu">
        <li>
            <a href="/absensi/data/dashboard.php"
                class="<?= ($active_menu == 'dashboard') ? 'active' : '' ?>">
                Dashboard
            </a>
        </li>

        <?php if ($role === 'admin'): ?>
            <li>
                <a href="/absensi/data/data_guru.php"
                    class="<?= ($active_menu == 'data_guru') ? 'active' : '' ?>">
                    Data Guru
                </a>
            </li>

            <li>
                <a href="/absensi/data/data_siswa.php"
                    class="<?= ($active_menu == 'data_siswa') ? 'active' : '' ?>">
                    Data Siswa
                </a>
            </li>
            <li>
                <a href="/absensi/data/data_kelas.php"
                    class="<?= ($active_menu == 'data_kelas') ? 'active' : '' ?>">
                    Data Kelas
                </a>
            </li>
            <li>
                <a href="/absensi/data/menu_absensi.php"
                    class="<?= ($active_menu == 'absensi') ? 'active' : '' ?>">
                    Menu Absensi
                </a>
            </li>
            <li>
                <a href="#" id="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        <?php endif; ?>
    </ul>
    <!-- MODAL LOGOUT -->

    <div id="logout-modal" class="modal-logout-overlay" style="display: none;">
        <div class="modal-logout-box">
            <div class="modal-header">
                <h3 style="margin:0;"><i class="fas fa-sign-out-alt"></i> Konfirmasi Logout</h3>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin keluar?</p>
                <div class="logout-actions">
                    <button id="logout-modal-close-btn" class="btn-cancel">Batal</button>
                    <a href="/absensi/auth/logout.php" class="btn-keluar">Ya, Keluar</a>
                </div>
            </div>
        </div>
    </div>
    <!-- END MODAL LOGOUT -->
</div>