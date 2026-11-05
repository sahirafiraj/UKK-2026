<?php
include 'includes/cek_session.php';
include 'config/koneksi.php';

// Memeriksa login
if (!isset($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

// Memeriksa hak akses admin
if ($_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Memeriksa ID tahun ajaran
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: data_tahun_ajaran.php");
    exit;
}

$id = (int) $_GET['id'];

// Memastikan data tahun ajaran tersedia
$cek_data = mysqli_query(
    $koneksi,
    "SELECT id FROM t_tahun_ajaran WHERE id = $id"
);

if (!$cek_data || mysqli_num_rows($cek_data) == 0) {
    echo "Data tahun ajaran tidak ditemukan.";
    exit;
}

// Memproses tombol Simpan
if (isset($_POST['simpan'])) {

    // Mengambil data dari form
    $nama = trim($_POST['nama']);
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $status_aktif = (int) $_POST['status_aktif'];

    // Validasi data
    if ($nama == "" || $tanggal_mulai == "" || $tanggal_selesai == "") {

        echo "Semua data harus diisi!";
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
        exit;
    }

    // Memeriksa tanggal
    if ($tanggal_mulai > $tanggal_selesai) {

        echo "Tanggal mulai tidak boleh melebihi tanggal selesai.";
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
        exit;
    }

    // Mengubah nama menjadi angka saja untuk pengecekan
    $nama_cek = preg_replace('/[^0-9]/', '', $nama);

    if ($nama_cek == "") {

        echo "Nama tahun ajaran tidak valid!";
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
        exit;
    }

    // Memeriksa duplikasi tahun ajaran
    $query_cek = mysqli_query(
        $koneksi,
        "SELECT id, nama FROM t_tahun_ajaran WHERE id != $id"
    );

    if (!$query_cek) {

        echo "Gagal memeriksa tahun ajaran: "
             . mysqli_error($koneksi);
        exit;
    }

    $duplikat = false;

    while ($data_cek = mysqli_fetch_assoc($query_cek)) {

        $nama_lama = preg_replace(
            '/[^0-9]/',
            '',
            trim($data_cek['nama'])
        );

        if ($nama_cek == $nama_lama) {

            $duplikat = true;
            break;
        }
    }

    if ($duplikat) {

        echo "Tahun ajaran tersebut sudah terdaftar!";
        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
        exit;
    }

    // Mengamankan data
    $nama_db = mysqli_real_escape_string(
        $koneksi,
        $nama
    );

    $tanggal_mulai_db = mysqli_real_escape_string(
        $koneksi,
        $tanggal_mulai
    );

    $tanggal_selesai_db = mysqli_real_escape_string(
        $koneksi,
        $tanggal_selesai
    );

    // Memulai transaksi database
    mysqli_begin_transaction($koneksi);

    // Jika tahun ajaran diedit menjadi Aktif
    if ($status_aktif == 1) {

        // Menonaktifkan semua tahun ajaran lainnya
        $nonaktifkan = mysqli_query(
            $koneksi,
            "UPDATE t_tahun_ajaran
             SET status_aktif = 0
             WHERE id != $id"
        );

    } else {

        // Jika Tidak Aktif, tidak mengubah tahun ajaran lain
        $nonaktifkan = true;
    }

    // Mengubah data tahun ajaran
    if ($nonaktifkan) {

        $update = mysqli_query(
            $koneksi,
            "UPDATE t_tahun_ajaran SET
                nama = '$nama_db',
                tanggal_mulai = '$tanggal_mulai_db',
                tanggal_selesai = '$tanggal_selesai_db',
                status_aktif = '$status_aktif'
             WHERE id = $id"
        );

    } else {

        $update = false;
    }

    // Memeriksa hasil perubahan
    if ($update) {

        // Menyimpan perubahan database
        mysqli_commit($koneksi);

        header("Location: data_tahun_ajaran.php");
        exit;

    } else {

        // Membatalkan perubahan jika gagal
        mysqli_rollback($koneksi);

        echo "Gagal mengubah data tahun ajaran: "
             . mysqli_error($koneksi);

        echo "<br><a href='edit_tahun_ajaran.php?id=$id'>Kembali</a>";
    }

} else {

    header("Location: data_tahun_ajaran.php");
    exit;
}
?>