document.addEventListener("DOMContentLoaded", function () {
    const errorBox = document.getElementById("error-message");

    if (errorBox) {
        setTimeout(() => {
            errorBox.style.opacity = "0";
            errorBox.style.transition = "opacity 0.5s ease";
        }, 3000); // 3 detik

        setTimeout(() => {
            errorBox.style.display = "none";
        }, 3500);
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const currentPage = window.location.pathname.split("/").pop();

    document.querySelectorAll(".menu a").forEach(link => {
        const page = link.getAttribute("href").split("/").pop();

        if (page === currentPage) {
            link.classList.add("active");
        }
    });
    const toggleIcons = document.querySelectorAll(".toggle-password");
    toggleIcons.forEach(function (icon) {
        icon.addEventListener("click", function () {
            const passwordField = icon.closest(".password-wrapper").querySelector("input");
            const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
            passwordField.setAttribute("type", type);
            this.classList.toggle("fa-eye");
            this.classList.toggle("fa-eye-slash");
        });
    });
});

// Logout Modal
document.addEventListener("DOMContentLoaded", function () {
    const logoutBtn = document.getElementById("logout-btn");
    const modal = document.getElementById("logout-modal");
    const batalBtn = document.getElementById("logout-modal-close-btn");

    if (modal) {
        document.body.appendChild(modal);
    }

    if (!logoutBtn || !modal) return;

    logoutBtn.addEventListener("click", function (e) {
        e.preventDefault();
        modal.style.display = "flex"; 
    });

    if (batalBtn) {
        batalBtn.addEventListener("click", function() {
            modal.style.display = "none";
        });
    }

    modal.addEventListener("click", function (e) {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });
});

// Hapus mapel Modal
function openDeleteMapelModal(id_mapel) {
    const modal = document.getElementById("delete-mapel-modal");
    const confirmBtn = document.getElementById("confirm-delete-mapel");

    confirmBtn.href = `atur_mapel.php?id=${getIdKelas()}&hapus_mapel=${id_mapel}`;
    modal.style.display = "flex";
}

function closeDeleteMapelModal() {
    document.getElementById("delete-mapel-modal").style.display = "none";
}

function getIdKelas() {
    const params = new URLSearchParams(window.location.search);
    return params.get("id");
}

function openDeleteKelasModal(id) {
    const modal = document.getElementById("delete-kelas-modal");
    const confirmBtn = document.getElementById("confirm-delete-kelas");

    confirmBtn.href = "data_kelas.php?hapus=" + id;
    modal.style.display = "flex";
}

function closeDeleteKelasModal() {
    document.getElementById("delete-kelas-modal").style.display = "none";
}

function bukaModalEdit(id, nama) {
    document.getElementById('input_id_mapel').value = id;
    document.getElementById('input_nama_mapel').value = nama;

    document.getElementById('modalEdit').style.display = 'flex';

    document.getElementById('input_nama_mapel').focus();
}

function tutupModal() {
    document.getElementById('modalEdit').style.display = 'none';
}

window.onclick = function (event) {
    let modal = document.getElementById('modalEdit');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}