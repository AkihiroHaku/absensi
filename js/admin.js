document.addEventListener('DOMContentLoaded', function () {
    let rowCount = 1;

    // Fungsi untuk menambah baris
    document.getElementById('add-row').addEventListener('click', function () {
        rowCount++;
        const tbody = document.getElementById('siswa-tbody');
        const newRow = document.createElement('tr');
        newRow.className = 'siswa-row';
        newRow.innerHTML = '<td>' + rowCount + '</td>' +
            '<td><input type="text" name="nis[]" placeholder="Masukkan NIS" required></td>' +
            '<td><input type="text" name="nama[]" placeholder="Masukkan Nama" required></td>' +
            '<td><button type="button" class="btn-small btn-delete-row"><i class="fas fa-trash"></i></button></td>';
        tbody.appendChild(newRow);
        updateRowNumbers();
    });

    // Fungsi untuk menghapus baris
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('btn-delete-row') || e.target.parentElement.classList.contains('btn-delete-row')) {
            const row = e.target.closest('.siswa-row');
            if (document.querySelectorAll('.siswa-row').length > 1) {
                row.remove();
                updateRowNumbers();
            } else {
                alert('Minimal harus ada satu baris siswa!');
            }
        }
    });

    // Fungsi untuk update nomor urut
    function updateRowNumbers() {
        const rows = document.querySelectorAll('.siswa-row');
        rows.forEach((row, index) => {
            row.cells[0].textContent = index + 1;
        });
        rowCount = rows.length;
    }

    // Reset form
    document.getElementById('reset-form').addEventListener('click', function () {
        document.getElementById('form-tambah-siswa').reset();
        document.getElementById('siswa-tbody').innerHTML = '<tr class="siswa-row">' +
            '<td>1</td>' +
            '<td><input type="text" name="nis[]" placeholder="Masukkan NIS" required></td>' +
            '<td><input type="text" name="nama[]" placeholder="Masukkan Nama" required></td>' +
            '<td><button type="button" class="btn-small btn-delete-row"><i class="fas fa-trash"></i>Delete</button></td>' +
            '</tr>';
        rowCount = 1;
    });

    const toggleIcons = document.querySelectorAll(".toggle-password");
    toggleIcons.forEach(function (icon) {

        const togglePasswordIcons = document.querySelectorAll('.toggle-password');

        togglePasswordIcons.forEach(icon => {
            icon.addEventListener('click', function () {
                const passwordField = icon.closest('.password-wrapper').querySelector('input');
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const currentPage = window.location.pathname.split("/").pop();

    document.querySelectorAll(".menu a").forEach(link => {
        const linkPage = link.getAttribute("href").split("/").pop();

        if (linkPage === currentPage) {
            link.classList.add("active");
        }
    });
});

function openDeleteGuruModal(id) {
    const modal = document.getElementById("delete-guru-modal");
    const confirmBtn = document.getElementById("confirm-delete-guru");

    confirmBtn.href = "hapus_guru.php?id=" + id;
    modal.style.display = "flex";
}

function closeDeleteGuruModal() {
    document.getElementById("delete-guru-modal").style.display = "none";
}

// Klik luar modal → tutup
document.addEventListener("click", function (e) {
    const modal = document.getElementById("delete-guru-modal");
    if (e.target === modal) {
        modal.style.display = "none";
    }
});

