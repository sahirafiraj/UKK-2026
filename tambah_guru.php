
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

$name = $_SESSION['name'];
$halaman = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Guru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR ADMIN -->
        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link text-white">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_siswa.php" class="nav-link text-white ">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link active">
                        Data Guru
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kelas.php" class="nav-link text-white">
                        Data Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_tahun_ajaran.php" class="nav-link text-white">
                        Tahun Ajaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_penempatan_siswa.php" class="nav-link text-white">
                        Penempatan Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_wali_kelas.php" class="nav-link text-white">
                        Data Wali Kelas
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_kategori_pelanggaran.php" class="nav-link text-white">
                        Kategori Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_jenis_pelanggaran.php" class="nav-link text-white">
                        Jenis Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="laporan.php" class="nav-link text-white">
                        Laporan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="about_me.php" class="nav-link text-white">
                        About Me
                    </a>
                </li>

                <hr class="border-white">

                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-white">
                        Logout
                    </a>
                </li>

            </ul>
        </div>

        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <div class="bg-white rounded shadow-sm p-4 mb-4">
                <h2 class="fw-bold mb-2">Tambah Data Guru</h2>
                <p class="text-secondary mb-0">
                    Selamat datang, <b><?= htmlspecialchars($name); ?></b>
                </p>
            </div>

            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-0 fw-bold">Form Tambah Guru</h5>
                </div>

                <div class="card-body p-4">

                    <form action="proses_tambah_guru.php" method="POST">

                        <div class="mb-3">
                            <label for="nip" class="form-label">NIP</label>
                            <input type="text"
                                   name="nip"
                                   id="nip"
                                   class="form-control"
                                   placeholder="Masukkan NIP guru"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="nama_guru" class="form-label">Nama Guru</label>
                            <input type="text"
                                   name="nama_guru"
                                   id="nama_guru"
                                   class="form-control"
                                   placeholder="Masukkan nama guru"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   id="email"
                                   class="form-control"
                                   placeholder="Masukkan email guru"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   id="password"
                                   class="form-control"
                                   placeholder="Masukkan password"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="konfirmasi_password" class="form-label">Konfirmasi Password</label>
                            <input type="password"
                                   name="konfirmasi_password"
                                   id="konfirmasi_password"
                                   class="form-control"
                                   placeholder="Masukkan ulang password"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status"
                                    id="status"
                                    class="form-select"
                                    required>
                                <option value="">-- Pilih Status --</option>
                                <option value="aktif">Aktif</option>
                                <option value="tidak aktif">Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" name="simpan" class="btn btn-primary">
                                Simpan Guru
                            </button>

                            <a href="data_guru.php" class="btn btn-secondary">
                                Kembali
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.getElementById('konfirmasi_password').addEventListener('input', function () {
    if (this.value !== document.getElementById('password').value) {
        this.setCustomValidity('Konfirmasi password tidak sama.');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password').addEventListener('input', function () {
    const konfirmasi = document.getElementById('konfirmasi_password');

    if (konfirmasi.value !== '' && konfirmasi.value !== this.value) {
        konfirmasi.setCustomValidity('Konfirmasi password tidak sama.');
    } else {
        konfirmasi.setCustomValidity('');
    }
});
</script>

</body>
</html>
