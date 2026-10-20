
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

$id = $_GET['id'] ?? '';

if ($id == '' || !is_numeric($id)) {
    echo "ID siswa tidak valid";
    exit;
}

$id = (int) $id;

$query = mysqli_query($koneksi, "SELECT * FROM t_siswa WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data siswa tidak ditemukan";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">
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
                    <a href="data_siswa.php" class="nav-link active">
                        Data Siswa
                    </a>
                </li>

                <li class="nav-item">
                    <a href="data_guru.php" class="nav-link text-white">
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


        <!-- KONTEN UTAMA -->
        <main class="col-md-9 col-lg-10 offset-md-3 offset-lg-2 p-4">

            <!-- HEADER -->
            <div class="bg-white rounded shadow-sm p-4 mb-4">

                <div class="text-secondary small mb-2">
                    Dashboard / Data Siswa / Edit Data Siswa
                </div>

                <h2 class="fw-bold mb-2">Edit Data Siswa</h2>

                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($name); ?></b>.
                    Silakan perbarui informasi siswa di bawah ini.
                </p>

            </div>

            <!-- FORM EDIT -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-white p-3">
                    <h5 class="mb-1 fw-bold">Formulir Edit Siswa</h5>

                    <small class="text-secondary">
                        Kolom bertanda <span class="text-danger">*</span>
                        wajib diisi.
                    </small>
                </div>

                <div class="card-body p-4">

                    <form action="proses_edit_siswa.php" method="POST">

                        <input type="hidden" name="id"
                               value="<?= htmlspecialchars($data['id']); ?>">

                        <!-- BAGIAN 1: IDENTITAS -->
                        <h6 class="fw-bold text-primary mb-3">
                            1. Identitas Siswa
                        </h6>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <label for="nis" class="form-label">
                                    NIS <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="nis"
                                       name="nis"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nis'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label for="nisn" class="form-label">
                                    NISN <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="nisn"
                                       name="nisn"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nisn'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="col-12">
                                <label for="nama" class="form-label">
                                    Nama Lengkap Siswa
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="text"
                                       id="nama"
                                       name="nama"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['nama'] ?? ''); ?>"
                                       required>
                            </div>

                        </div>

                        <hr class="mb-4">

                        <!-- BAGIAN 2: INFORMASI PRIBADI -->
                        <h6 class="fw-bold text-primary mb-3">
                            2. Informasi Pribadi
                        </h6>

                        <div class="row g-3 mb-4">

                            <div class="col-md-6">
                                <label for="jenis_kelamin" class="form-label">
                                    Jenis Kelamin
                                    <span class="text-danger">*</span>
                                </label>

                                <select id="jenis_kelamin"
                                        name="jenis_kelamin"
                                        class="form-select"
                                        required>

                                    <option value="">
                                        Pilih Jenis Kelamin
                                    </option>

                                    <option value="L"
                                        <?= ($data['jenis_kelamin'] ?? '') == 'L' ? 'selected' : ''; ?>>
                                        Laki-laki
                                    </option>

                                    <option value="P"
                                        <?= ($data['jenis_kelamin'] ?? '') == 'P' ? 'selected' : ''; ?>>
                                        Perempuan
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="tanggal_lahir" class="form-label">
                                    Tanggal Lahir
                                    <span class="text-danger">*</span>
                                </label>

                                <input type="date"
                                       id="tanggal_lahir"
                                       name="tanggal_lahir"
                                       class="form-control"
                                       value="<?= htmlspecialchars($data['tanggal_lahir'] ?? ''); ?>"
                                       required>
                            </div>

                            <div class="col-12">
                                <label for="alamat" class="form-label">
                                    Alamat
                                </label>

                                <textarea id="alamat"
                                          name="alamat"
                                          class="form-control"
                                          rows="3"
                                          placeholder="Masukkan alamat lengkap siswa"><?= htmlspecialchars($data['alamat'] ?? ''); ?></textarea>
                            </div>

                        </div>

                        <hr class="mb-4">

                        <!-- BAGIAN 3: STATUS -->
                        <h6 class="fw-bold text-primary mb-3">
                            3. Status Siswa
                        </h6>

                        <div class="row">
                            <div class="col-md-6">

                                <label for="status_aktif" class="form-label">
                                    Status Aktif
                                    <span class="text-danger">*</span>
                                </label>

                                <select id="status_aktif"
                                        name="status_aktif"
                                        class="form-select"
                                        required>

                                    <option value="1"
                                        <?= ($data['status_aktif'] ?? 0) == 1 ? 'selected' : ''; ?>>
                                        Aktif
                                    </option>

                                    <option value="0"
                                        <?= ($data['status_aktif'] ?? 0) == 0 ? 'selected' : ''; ?>>
                                        Tidak Aktif
                                    </option>

                                </select>

                            </div>
                        </div>

                        <!-- TOMBOL -->
                        <div class="d-flex justify-content-between align-items-center border-top mt-4 pt-3">

                            <a href="data_siswa.php"
                               class="btn btn-outline-secondary">
                                Kembali
                            </a>

                        <button type="submit"
                            name="update"
                            value="1"
                            class="btn btn-primary">
                            Simpan Perubahan
                        </button>

                        </div>

                    </form>

                </div>
            </div>

        </main>

    </div>
</div>

<!-- JAVASCRIPT BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>

</body>
</html>
