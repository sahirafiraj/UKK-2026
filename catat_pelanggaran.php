
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// ==========================================
// 1. CEK LOGIN DAN HAK AKSES
// ==========================================

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$role = strtolower(trim($_SESSION['role'] ?? ''));

if ($role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Guru';
$id_user = (int) ($_SESSION['id'] ?? 0);
$halaman = basename($_SERVER['PHP_SELF']);

$pesan = "";
$jenis_pesan = "";

// ==========================================
// 2. MENGAMBIL GURU SESUAI AKUN LOGIN
// ==========================================

$query_guru_login = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = '$id_user'
    AND status_aktif = 1
    LIMIT 1
");

$data_guru_login = mysqli_fetch_assoc($query_guru_login);

if (!$data_guru_login) {
    echo "Data guru tidak ditemukan atau tidak aktif.";
    exit;
}

$guru_id = (int) $data_guru_login['id'];
$nama_guru = $data_guru_login['nama'];

// ==========================================
// 3. MENGAMBIL TAHUN AJARAN AKTIF
// ==========================================

$query_tahun = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_tahun_ajaran
    WHERE status_aktif = 1
    LIMIT 1
");

$data_tahun = mysqli_fetch_assoc($query_tahun);

if (!$data_tahun) {
    echo "Tahun ajaran aktif belum tersedia.";
    exit;
}

$tahun_ajaran_id = (int) $data_tahun['id'];
$nama_tahun = $data_tahun['nama'];

// ==========================================
// 4. MENGAMBIL PILIHAN KELAS
// ==========================================

$kelas_id = (int) ($_GET['kelas_id'] ?? $_POST['kelas_id'] ?? 0);

$query_kelas = mysqli_query($koneksi, "
    SELECT DISTINCT k.id, k.nama
    FROM t_kelas_siswa ks
    JOIN t_kelas k ON ks.kelas_id = k.id
    WHERE ks.tahun_ajaran_id = '$tahun_ajaran_id'
    ORDER BY k.nama ASC
");

// ==========================================
// 5. PROSES SIMPAN PELANGGARAN
// ==========================================

if (isset($_POST['simpan'])) {

    $penempatan_id = (int) $_POST['penempatan_id'];
    $pelanggaran_id = (int) $_POST['pelanggaran_id'];
    $kelas_id = (int) $_POST['kelas_id'];

    $tanggal = mysqli_real_escape_string(
        $koneksi,
        $_POST['tanggal']
    );

    $keterangan = mysqli_real_escape_string(
        $koneksi,
        trim($_POST['keterangan'])
    );

    // Cek siswa sesuai kelas dan tahun ajaran aktif
    $query_siswa_pilih = mysqli_query($koneksi, "
        SELECT
            ks.siswa_id,
            ks.tahun_ajaran_id,
            ks.kelas_id,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas
        FROM t_kelas_siswa ks
        JOIN t_siswa s ON ks.siswa_id = s.id
        JOIN t_kelas k ON ks.kelas_id = k.id
        WHERE ks.id = '$penempatan_id'
        AND ks.kelas_id = '$kelas_id'
        AND ks.tahun_ajaran_id = '$tahun_ajaran_id'
        LIMIT 1
    ");

    // Cek jenis pelanggaran
    $query_jenis_pilih = mysqli_query($koneksi, "
        SELECT
            p.id,
            p.pelanggaran_kategori_id,
            p.poin,
            pk.nama AS nama_kategori
        FROM t_pelanggaran p
        JOIN t_pelanggaran_kategori pk
            ON p.pelanggaran_kategori_id = pk.id
        WHERE p.id = '$pelanggaran_id'
        AND p.status_aktif = 1
        AND pk.status_aktif = 1
        LIMIT 1
    ");

    if ($kelas_id <= 0) {

        $pesan = "Silakan pilih kelas terlebih dahulu.";
        $jenis_pesan = "danger";

    } elseif (!$query_siswa_pilih ||
              mysqli_num_rows($query_siswa_pilih) == 0) {

        $pesan = "Siswa tidak sesuai dengan kelas yang dipilih.";
        $jenis_pesan = "danger";

    } elseif (!$query_jenis_pilih ||
              mysqli_num_rows($query_jenis_pilih) == 0) {

        $pesan = "Silakan pilih jenis pelanggaran.";
        $jenis_pesan = "danger";

    } elseif ($tanggal == "" || $keterangan == "") {

        $pesan = "Tanggal dan keterangan wajib diisi.";
        $jenis_pesan = "danger";

    } else {

        $data_siswa = mysqli_fetch_assoc($query_siswa_pilih);

        $siswa_id = (int) $data_siswa['siswa_id'];

        $nama_siswa = mysqli_real_escape_string(
            $koneksi,
            $data_siswa['nama_siswa']
        );

        $nama_kelas = mysqli_real_escape_string(
            $koneksi,
            $data_siswa['nama_kelas']
        );

        $data_jenis = mysqli_fetch_assoc($query_jenis_pilih);

        $nama_pelanggaran = mysqli_real_escape_string(
            $koneksi,
            $data_jenis['nama_kategori']
        );

        $kategori_id = (int) $data_jenis['pelanggaran_kategori_id'];
        $poin = (int) $data_jenis['poin'];

        // Simpan pelanggaran
        $simpan = mysqli_query($koneksi, "
            INSERT INTO t_pelanggaran_siswa
            (
                tahun_ajaran_id,
                siswa_id,
                nama_siswa,
                kelas_id,
                nama_kelas,
                pelanggaran_id,
                nama_pelanggaran,
                pelanggaran_kategori_id,
                guru_id,
                nama_guru,
                tanggal,
                keterangan,
                poin,
                tindakan,
                status
            )
            VALUES
            (
                '$tahun_ajaran_id',
                '$siswa_id',
                '$nama_siswa',
                '$kelas_id',
                '$nama_kelas',
                '$pelanggaran_id',
                '$nama_pelanggaran',
                '$kategori_id',
                '$guru_id',
                '$nama_guru',
                '$tanggal',
                '$keterangan',
                '$poin',
                '-',
                'Belum'
            )
        ");

        if ($simpan) {

            header("Location: catat_pelanggaran.php?kelas_id=$kelas_id&berhasil=1");
            exit;

        } else {

            $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);
            $jenis_pesan = "danger";

        }
    }
}

// ==========================================
// 6. PESAN BERHASIL
// ==========================================

if (isset($_GET['berhasil'])) {
    $pesan = "Catatan pelanggaran berhasil disimpan.";
    $jenis_pesan = "success";
}

// ==========================================
// 7. MENGAMBIL DATA SISWA SESUAI KELAS
// ==========================================

$query_siswa = false;

if ($kelas_id > 0) {

    $query_siswa = mysqli_query($koneksi, "
        SELECT
            ks.id AS penempatan_id,
            s.nis,
            s.nama AS nama_siswa,
            k.nama AS nama_kelas
        FROM t_kelas_siswa ks
        JOIN t_siswa s ON ks.siswa_id = s.id
        JOIN t_kelas k ON ks.kelas_id = k.id
        WHERE ks.kelas_id = '$kelas_id'
        AND ks.tahun_ajaran_id = '$tahun_ajaran_id'
        ORDER BY s.nama ASC
    ");

}

// ==========================================
// 8. MENGAMBIL DATA KATEGORI PELANGGARAN
// ==========================================

$query_pelanggaran = mysqli_query($koneksi, "
    SELECT
        p.id,
        p.pelanggaran_kategori_id,
        p.poin,
        pk.nama AS nama_kategori
    FROM t_pelanggaran p
    JOIN t_pelanggaran_kategori pk
        ON p.pelanggaran_kategori_id = pk.id
    WHERE p.status_aktif = 1
    AND pk.status_aktif = 1
    ORDER BY pk.nama ASC
");

// ==========================================
// 9. MENGAMBIL SELURUH DATA PELANGGARAN
// ==========================================

$query_terbaru = mysqli_query($koneksi, "
    SELECT
        id,
        tanggal,
        nama_siswa,
        nama_kelas,
        nama_pelanggaran,
        poin,
        nama_guru,
        status
    FROM t_pelanggaran_siswa
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Catat Pelanggaran Siswa</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.datatables.net/3.1.4/css/dataTables.dataTables.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">

    <style>
        .form-pelanggaran {
            border: 0;
            border-radius: 14px;
            overflow: hidden;
        }

        .form-pelanggaran .card-header {
            border-bottom: 1px solid #eef1f5;
            padding: 20px 24px;
        }

        .form-pelanggaran .card-body {
            padding: 24px;
        }

        .form-pelanggaran .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #344054;
            margin-bottom: 8px;
        }

        .form-pelanggaran .form-control,
        .form-pelanggaran .form-select {
            min-height: 45px;
            border-radius: 8px;
            border-color: #d9dee7;
            font-size: 14px;
        }

        .form-pelanggaran .form-control:focus,
        .form-pelanggaran .form-select:focus {
            border-color: #6478b8;
            box-shadow: 0 0 0 3px rgba(23, 37, 84, .10);
        }

        .form-pelanggaran textarea.form-control {
            min-height: 110px;
            resize: vertical;
        }

        .kelas-box {
            background: #f8faff;
            border: 1px solid #e5eaf5;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 22px;
        }

        .form-divider {
            border-top: 1px solid #edf0f5;
            margin: 8px 0 22px;
        }

        .btn-form {
            min-height: 42px;
            padding: 9px 20px;
            border-radius: 8px;
            font-weight: 600;
        }

        .readonly-field {
            background: #f7f8fa !important;
            color: #667085;
        }

        @media (max-width: 576px) {
            .form-pelanggaran .card-body {
                padding: 16px;
            }

            .form-pelanggaran .card-header {
                padding: 16px;
            }

            .form-actions .btn {
                width: 100%;
            }

            .form-actions {
                display: flex;
                flex-direction: column-reverse;
                gap: 8px;
            }
        }
    </style>

</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR GURU -->
        <div class="sidebar col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto">

            <h4 class="text-white mb-4">
                Sistem Pelanggaran
            </h4>

            <ul class="nav nav-pills flex-column gap-1">

                <li class="nav-item">
                    <a href="dashboard.php"
                       class="nav-link <?= $halaman == 'dashboard.php' ? 'active' : 'text-white'; ?>">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="catat_pelanggaran.php"
                       class="nav-link <?= $halaman == 'catat_pelanggaran.php' ? 'active' : 'text-white'; ?>">
                        Catat Pelanggaran
                    </a>
                </li>

                <li class="nav-item">
                    <a href="tindakan.php"
                       class="nav-link <?= $halaman == 'tindakan.php' ? 'active' : 'text-white'; ?>">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="riwayat.php"
                       class="nav-link <?= $halaman == 'riwayat.php' ? 'active' : 'text-white'; ?>">
                        Riwayat
                    </a>
                </li>

                <li class="nav-item">
                    <a href="rekap_poin.php"
                       class="nav-link <?= $halaman == 'rekap_poin.php' ? 'active' : 'text-white'; ?>">
                        Rekap Poin
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
        <main class="content-main">
            <div class="page-content">

                <!-- HEADER -->
                <div class="bg-white rounded shadow-sm p-4 mb-4">

                    <h2 class="fw-bold">
                        Catat Pelanggaran Siswa
                    </h2>

                    <p class="text-secondary mb-0">
                        Selamat datang,
                        <b><?= htmlspecialchars($nama_login); ?></b>
                    </p>

                </div>

                <!-- PESAN -->
                <?php if ($pesan != "") { ?>

                    <div class="alert alert-<?= $jenis_pesan; ?>">
                        <?= htmlspecialchars($pesan); ?>
                    </div>

                <?php } ?>

                <!-- FORM CATAT PELANGGARAN -->
                <div class="card shadow-sm form-pelanggaran mb-4">

                    <div class="card-header bg-white">
                        <h5 class="fw-bold mb-1">Form Catat Pelanggaran</h5>
                        <small class="text-secondary">Lengkapi data pelanggaran siswa dengan benar.</small>
                    </div>

                    <div class="card-body">

                        <!-- PILIH KELAS -->
                        <form method="GET" action="catat_pelanggaran.php">
                            <div class="kelas-box">
                                <label class="form-label" for="kelas_id">Kelas Siswa</label>

                                <select name="kelas_id" id="kelas_id"
                                        class="form-select"
                                        onchange="this.form.submit()" required>
                                    <option value="">-- Pilih Kelas --</option>

                                    <?php
                                    if ($query_kelas) {
                                        while ($kelas = mysqli_fetch_assoc($query_kelas)) {
                                    ?>
                                        <option value="<?= $kelas['id']; ?>"
                                            <?= ($kelas_id == $kelas['id']) ? 'selected' : ''; ?>>
                                            <?= htmlspecialchars($kelas['nama']); ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>

                                <small class="text-secondary d-block mt-2">
                                    Pilih kelas terlebih dahulu untuk menampilkan siswa di kelas tersebut.
                                </small>
                            </div>
                        </form>

                        <!-- FORM SIMPAN -->
                        <form method="POST" action="catat_pelanggaran.php">

                            <input type="hidden" name="kelas_id" value="<?= $kelas_id; ?>">

                            <div class="row g-3">

                                <!-- NAMA SISWA -->
                                <div class="col-md-6">
                                    <label class="form-label" for="penempatan_id">Nama Siswa</label>

                                    <select name="penempatan_id" id="penempatan_id"
                                            class="form-select"
                                            required <?= $kelas_id <= 0 ? 'disabled' : ''; ?>>

                                        <option value="">-- Pilih Siswa --</option>

                                        <?php
                                        if ($query_siswa) {
                                            while ($siswa = mysqli_fetch_assoc($query_siswa)) {
                                        ?>
                                            <option value="<?= $siswa['penempatan_id']; ?>">
                                                <?= htmlspecialchars($siswa['nis'] . ' - ' . $siswa['nama_siswa']); ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>

                                    <?php if ($kelas_id <= 0) { ?>
                                        <small class="text-danger d-block mt-2">
                                            Siswa akan muncul setelah kelas dipilih.
                                        </small>
                                    <?php } ?>
                                </div>

                                <!-- JENIS PELANGGARAN -->
                                <div class="col-md-6">
                                    <label class="form-label" for="pelanggaran_id">Jenis Pelanggaran</label>

                                    <select name="pelanggaran_id" id="pelanggaran_id"
                                            class="form-select" required>

                                        <option value="">-- Pilih Jenis Pelanggaran --</option>

                                        <?php
                                        if ($query_pelanggaran) {
                                            while ($pelanggaran = mysqli_fetch_assoc($query_pelanggaran)) {
                                        ?>
                                            <option value="<?= $pelanggaran['id']; ?>">
                                                <?= htmlspecialchars(
                                                    $pelanggaran['nama_kategori'] .
                                                    ' (' .
                                                    $pelanggaran['poin'] .
                                                    ' poin)'
                                                ); ?>
                                            </option>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- TANGGAL -->
                                <div class="col-md-6">
                                    <label class="form-label" for="tanggal">Tanggal Pelanggaran</label>

                                    <input type="date" name="tanggal" id="tanggal"
                                           class="form-control"
                                           value="<?= date('Y-m-d'); ?>"
                                           required>
                                </div>

                                <!-- GURU PENCATAT -->
                                <div class="col-md-6">
                                    <label class="form-label">Guru Pencatat</label>

                                    <input type="text" class="form-control readonly-field"
                                           value="<?= htmlspecialchars($nama_guru); ?>"
                                           readonly>
                                </div>

                                <!-- TAHUN AJARAN -->
                                <div class="col-md-6">
                                    <label class="form-label">Tahun Ajaran</label>

                                    <input type="text" class="form-control readonly-field"
                                           value="<?= htmlspecialchars($nama_tahun); ?>"
                                           readonly>
                                </div>

                                <!-- KETERANGAN -->
                                <div class="col-12">
                                    <label class="form-label" for="keterangan">Keterangan</label>

                                    <textarea name="keterangan" id="keterangan"
                                              class="form-control"
                                              rows="4"
                                              placeholder="Tuliskan keterangan pelanggaran secara singkat dan jelas..."
                                              required></textarea>
                                </div>

                            </div>

                            <div class="form-divider"></div>

                            <div class="form-actions d-flex justify-content-end gap-2">

                                <button type="reset"
                                        class="btn btn-light border btn-form">
                                    Reset Form
                                </button>

                                <button type="submit" name="simpan"
                                        class="btn btn-primary btn-form"
                                        <?= $kelas_id <= 0 ? 'disabled' : ''; ?>>
                                    Simpan Pelanggaran
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

                <!-- PELANGGARAN TERBARU -->
                <div class="card shadow-sm border-0">

                    <div class="card-header bg-white py-3">

                        <h5 class="fw-bold mb-1">
                            Pelanggaran Terbaru
                        </h5>

                        <small class="text-secondary">
                            Daftar catatan pelanggaran siswa.
                        </small>

                    </div>

                    <div class="card-body">

                        <div class="table-responsive">

                            <table class="table table-bordered table-striped table-hover align-middle"
                                   id="tabelPelanggaran">

                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Tanggal</th>
                                        <th>Nama Siswa</th>
                                        <th>Kelas</th>
                                        <th>Jenis Pelanggaran</th>
                                        <th>Poin</th>
                                        <th>Guru Pencatat</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php
                                    $no = 1;

                                    if ($query_terbaru && mysqli_num_rows($query_terbaru) > 0) {

                                        while ($data = mysqli_fetch_assoc($query_terbaru)) {
                                    ?>

                                        <tr>

                                            <td><?= $no++; ?></td>

                                            <td>
                                                <?= htmlspecialchars($data['tanggal'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?>
                                            </td>

                                            <td>
                                                <span class="badge bg-danger">
                                                    <?= htmlspecialchars($data['poin'] ?? '0'); ?>
                                                    poin
                                                </span>
                                            </td>

                                            <td>
                                                <?= htmlspecialchars($data['nama_guru'] ?? '-'); ?>
                                            </td>

                                            <td>

                                                <?php
                                                if (($data['status'] ?? '') == 'Selesai') {

                                                    echo '<span class="badge bg-success">Selesai</span>';

                                                } elseif (($data['status'] ?? '') == 'Diproses') {

                                                    echo '<span class="badge bg-warning text-dark">Diproses</span>';

                                                } else {

                                                    echo '<span class="badge bg-secondary">' .
                                                        htmlspecialchars($data['status'] ?? 'Belum') .
                                                        '</span>';

                                                }
                                                ?>

                                            </td>

                                        </tr>

                                    <?php
                                        }

                                    } else {
                                    ?>

                                        <tr>
                                            <td colspan="8" class="text-center">
                                                Belum ada catatan pelanggaran.
                                            </td>
                                        </tr>

                                    <?php } ?>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

                <!-- FOOTER -->
                <div class="text-center text-secondary py-3">
                    <small>© 2026 Sistem Pelanggaran Siswa</small>
                </div>

            </div>
        </main>

    </div>
</div>

<script src="https://cdn.datatables.net/3.1.4/js/dataTables.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    new DataTable('#tabelPelanggaran', {

        pageLength: 10,

        lengthMenu: [5, 10, 25, 50, 100],

        order: [],

        language: {
            search: 'Cari:',
            searchPlaceholder: 'Cari data...',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            infoFiltered: '(difilter dari _MAX_ total data)',
            zeroRecords: 'Data tidak ditemukan',
            emptyTable: 'Belum ada catatan pelanggaran',

            paginate: {
                first: 'Pertama',
                last: 'Terakhir',
                next: 'Berikutnya',
                previous: 'Sebelumnya'
            }
        }

    });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
