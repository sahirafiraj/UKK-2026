
<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

$nama_login = $_SESSION['name'] ?? 'Guru';
$role = strtolower(trim($_SESSION['role'] ?? ''));
$halaman = basename($_SERVER['PHP_SELF']);

if ($role != 'guru') {
    echo "Anda tidak memiliki akses";
    exit;
}

$id_user = $_SESSION['id'] ?? $_SESSION['user_id'] ?? $_SESSION['id_user'] ?? 0;

$pesan = "";
$jenis_pesan = "";
$id_guru = 0;
$nama_guru = $nama_login;

$query_guru = mysqli_query($koneksi, "
    SELECT id, nama
    FROM t_guru
    WHERE user_id = '$id_user'
    LIMIT 1
");

if ($query_guru && mysqli_num_rows($query_guru) > 0) {
    $data_guru = mysqli_fetch_assoc($query_guru);
    $id_guru = $data_guru['id'];
    $nama_guru = $data_guru['nama'];
}

if (isset($_POST['simpan'])) {
    $penempatan_id = (int) $_POST['penempatan_id'];
    $pelanggaran_id = (int) $_POST['pelanggaran_id'];
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    $keterangan = mysqli_real_escape_string($koneksi, trim($_POST['keterangan']));

    $query_penempatan_pilih = mysqli_query($koneksi, "
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
        LIMIT 1
    ");

    $query_jenis_pilih = mysqli_query($koneksi, "
        SELECT id, pelanggaran_kategori_id, nama, poin
        FROM t_pelanggaran
        WHERE id = '$pelanggaran_id'
        AND status_aktif = 1
        LIMIT 1
    ");

    if ($id_guru == 0) {
        $pesan = "Data akun guru belum terhubung dengan tabel t_guru.";
        $jenis_pesan = "danger";
    } elseif (!$query_penempatan_pilih || mysqli_num_rows($query_penempatan_pilih) == 0) {
        $pesan = "Data penempatan siswa tidak ditemukan.";
        $jenis_pesan = "danger";
    } elseif (!$query_jenis_pilih || mysqli_num_rows($query_jenis_pilih) == 0) {
        $pesan = "Jenis pelanggaran tidak ditemukan atau tidak aktif.";
        $jenis_pesan = "danger";
    } elseif ($tanggal == "" || $keterangan == "") {
        $pesan = "Tanggal dan keterangan wajib diisi.";
        $jenis_pesan = "danger";
    } else {
        $data_penempatan = mysqli_fetch_assoc($query_penempatan_pilih);
        $data_pelanggaran = mysqli_fetch_assoc($query_jenis_pilih);

        $siswa_id = $data_penempatan['siswa_id'];
        $tahun_ajaran_id = $data_penempatan['tahun_ajaran_id'];
        $kelas_id = $data_penempatan['kelas_id'];

        $nama_siswa = mysqli_real_escape_string($koneksi, $data_penempatan['nama_siswa']);
        $nama_kelas = mysqli_real_escape_string($koneksi, $data_penempatan['nama_kelas']);
        $nama_pelanggaran = mysqli_real_escape_string($koneksi, $data_pelanggaran['nama']);
        $kategori_id = $data_pelanggaran['pelanggaran_kategori_id'];
        $poin = $data_pelanggaran['poin'];
        $nama_guru_simpan = mysqli_real_escape_string($koneksi, $nama_guru);

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
                '$id_guru',
                '$nama_guru_simpan',
                '$tanggal',
                '$keterangan',
                '$poin',
                '-',
                'Belum'
            )
        ");

        if ($simpan) {
            header("Location: catat_pelanggaran.php?berhasil=1");
            exit;
        } else {
            $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);
            $jenis_pesan = "danger";
        }
    }
}

if (isset($_GET['berhasil'])) {
    $pesan = "Catatan pelanggaran berhasil disimpan.";
    $jenis_pesan = "success";
}

$query_siswa = mysqli_query($koneksi, "
    SELECT
        ks.id AS penempatan_id,
        s.nis,
        s.nama AS nama_siswa,
        k.nama AS nama_kelas,
        ta.nama AS nama_tahun
    FROM t_kelas_siswa ks
    JOIN t_siswa s ON ks.siswa_id = s.id
    JOIN t_kelas k ON ks.kelas_id = k.id
    JOIN t_tahun_ajaran ta ON ks.tahun_ajaran_id = ta.id
    ORDER BY s.nama ASC
");

$query_pelanggaran = mysqli_query($koneksi, "
    SELECT id, kode, nama, poin
    FROM t_pelanggaran
    WHERE status_aktif = 1
    ORDER BY nama ASC
");

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
</head>

<body class="bg-light">

<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 position-fixed top-0 start-0 vh-100 p-3 overflow-auto"
             style="background-color: #172554;">

            <h4 class="text-white mb-4">Sistem Pelanggaran</h4>

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
                    <a href="tindakan.php" class="nav-link text-white">
                        Tindakan
                    </a>
                </li>

                <li class="nav-item">
                    <a href="riwayat.php" class="nav-link text-white">
                        Riwayat
                    </a>
                </li>

                <li class="nav-item">
                    <a href="rekap_poin.php" class="nav-link text-white">
                        Rekap Poin
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
                <h2 class="fw-bold">Catat Pelanggaran Siswa</h2>
                <p class="text-secondary mb-0">
                    Selamat datang,
                    <b><?= htmlspecialchars($nama_login); ?></b>
                </p>
            </div>

            <?php if ($pesan != "") { ?>
                <div class="alert alert-<?= htmlspecialchars($jenis_pesan); ?>">
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php } ?>

            <div class="card mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-0">Form Catat Pelanggaran</h5>
                </div>

                <div class="card-body p-4">
                    <form method="POST" action="">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Siswa</label>

                                <select name="penempatan_id" class="form-select" required>
                                    <option value="">-- Pilih Siswa --</option>

                                    <?php
                                    if ($query_siswa) {
                                        while ($siswa = mysqli_fetch_assoc($query_siswa)) {
                                    ?>
                                        <option value="<?= $siswa['penempatan_id']; ?>">
                                            <?= htmlspecialchars(
                                                $siswa['nis'] . ' - ' .
                                                $siswa['nama_siswa'] . ' - ' .
                                                $siswa['nama_kelas'] . ' (' .
                                                $siswa['nama_tahun'] . ')'
                                            ); ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis Pelanggaran</label>

                                <select name="pelanggaran_id" class="form-select" required>
                                    <option value="">-- Pilih Pelanggaran --</option>

                                    <?php
                                    if ($query_pelanggaran) {
                                        while ($pelanggaran = mysqli_fetch_assoc($query_pelanggaran)) {
                                    ?>
                                        <option value="<?= $pelanggaran['id']; ?>">
                                            <?= htmlspecialchars(
                                                $pelanggaran['kode'] . ' - ' .
                                                $pelanggaran['nama'] . ' (' .
                                                $pelanggaran['poin'] . ' poin)'
                                            ); ?>
                                        </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Pelanggaran</label>

                                <input type="date"
                                       name="tanggal"
                                       class="form-control"
                                       value="<?= date('Y-m-d'); ?>"
                                       required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Guru Pencatat</label>

                                <input type="text"
                                       class="form-control"
                                       value="<?= htmlspecialchars($nama_guru); ?>"
                                       readonly>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Keterangan</label>

                                <textarea name="keterangan"
                                          class="form-control"
                                          rows="3"
                                          required></textarea>
                            </div>

                        </div>

                        <div class="text-end">
                            <button type="reset" class="btn btn-light border">
                                Reset
                            </button>

                            <button type="submit"
                                    name="simpan"
                                    class="btn btn-primary">
                                Simpan Pelanggaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold mb-1">Pelanggaran Terbaru</h5>
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
                                    <th>Status</th>
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
                                        <td><?= htmlspecialchars($data['tanggal'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($data['nama_siswa'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($data['nama_kelas'] ?? '-'); ?></td>
                                        <td><?= htmlspecialchars($data['nama_pelanggaran'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-danger">
                                                <?= htmlspecialchars($data['poin'] ?? '0'); ?> poin
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($data['nama_guru'] ?? '-'); ?></td>
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
                                        <td colspan="8" class="text-center text-secondary">
                                            Belum ada catatan pelanggaran.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <p class="text-secondary text-center mt-4 small">
                © 2026 Sistem Pelanggaran Siswa
            </p>

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
        layout: {
            topStart: 'info',
            bottom: 'paging',
            bottomStart: null,
            bottomEnd: null
        },
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
        },
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                searchable: false
            }
        ]
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
