
<?php
session_start();

include "config/koneksi.php";

// Cek login dan hak akses
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    echo "Anda tidak memiliki akses";
    exit;
}

// Pastikan data dikirim melalui form
if (isset($_POST['simpan'])) {

    $id = $_POST['id'];
    $siswa_id = $_POST['siswa_id'];
    $tahun_ajaran_id = $_POST['tahun_ajaran_id'];
    $kelas_id = $_POST['kelas_id'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = !empty($_POST['tanggal_selesai'])
        ? $_POST['tanggal_selesai']
        : null;
    $status_aktif = $_POST['status_aktif'];

    // Validasi ID dan status
    if (
        !ctype_digit((string) $id) || (int) $id < 1 ||
        !ctype_digit((string) $siswa_id) || (int) $siswa_id < 1 ||
        !ctype_digit((string) $tahun_ajaran_id) || (int) $tahun_ajaran_id < 1 ||
        !ctype_digit((string) $kelas_id) || (int) $kelas_id < 1 ||
        !in_array((string) $status_aktif, ['0', '1'])
    ) {
        $_SESSION['pesan_error'] = "Data yang dimasukkan tidak valid.";
        header("Location: data_penempatan_siswa.php");
        exit;
    }

    // Validasi tanggal
    $tanggal_mulai_valid = DateTime::createFromFormat('!Y-m-d', $tanggal_mulai);

    if (
        !$tanggal_mulai_valid ||
        $tanggal_mulai_valid->format('Y-m-d') !== $tanggal_mulai
    ) {
        $_SESSION['pesan_error'] = "Tanggal mulai tidak valid.";
        header("Location: edit_penempatan_siswa.php?id=" . (int) $id);
        exit;
    }

    if ($tanggal_selesai !== null) {
        $tanggal_selesai_valid = DateTime::createFromFormat(
            '!Y-m-d',
            $tanggal_selesai
        );

        if (
            !$tanggal_selesai_valid ||
            $tanggal_selesai_valid->format('Y-m-d') !== $tanggal_selesai ||
            $tanggal_selesai < $tanggal_mulai
        ) {
            $_SESSION['pesan_error'] = "Tanggal selesai tidak valid.";
            header("Location: edit_penempatan_siswa.php?id=" . (int) $id);
            exit;
        }
    }

    // Pastikan data penempatan tersedia
    $cek = mysqli_prepare(
        $koneksi,
        "SELECT id FROM t_kelas_siswa WHERE id = ?"
    );
    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);
    mysqli_stmt_store_result($cek);
    $data_ada = mysqli_stmt_num_rows($cek) > 0;
    mysqli_stmt_close($cek);

    if (!$data_ada) {
        $_SESSION['pesan_error'] = "Data penempatan siswa tidak ditemukan.";
        header("Location: data_penempatan_siswa.php");
        exit;
    }

    // Pastikan siswa, tahun ajaran, dan kelas tersedia
    $referensi = [
        ["t_siswa", $siswa_id],
        ["t_tahun_ajaran", $tahun_ajaran_id],
        ["t_kelas", $kelas_id]
    ];

    foreach ($referensi as $item) {
        $tabel = $item[0];
        $nilai_id = (int) $item[1];

        $cek_ref = mysqli_prepare(
            $koneksi,
            "SELECT id FROM $tabel WHERE id = ?"
        );

        mysqli_stmt_bind_param($cek_ref, "i", $nilai_id);
        mysqli_stmt_execute($cek_ref);
        mysqli_stmt_store_result($cek_ref);

        $referensi_ada = mysqli_stmt_num_rows($cek_ref) > 0;
        mysqli_stmt_close($cek_ref);

        if (!$referensi_ada) {
            $_SESSION['pesan_error'] =
                "Data siswa, tahun ajaran, atau kelas tidak ditemukan.";
            header("Location: edit_penempatan_siswa.php?id=" . (int) $id);
            exit;
        }
    }

    // Update data penempatan siswa
    $query = mysqli_prepare(
        $koneksi,
        "UPDATE t_kelas_siswa
         SET siswa_id = ?,
             tahun_ajaran_id = ?,
             kelas_id = ?,
             tanggal_mulai = ?,
             tanggal_selesai = ?,
             status_aktif = ?
         WHERE id = ?"
    );

    mysqli_stmt_bind_param(
        $query,
        "iiissii",
        $siswa_id,
        $tahun_ajaran_id,
        $kelas_id,
        $tanggal_mulai,
        $tanggal_selesai,
        $status_aktif,
        $id
    );

    if (mysqli_stmt_execute($query)) {
        $_SESSION['pesan_sukses'] = "Data penempatan siswa berhasil diperbarui.";
        mysqli_stmt_close($query);
        header("Location: data_penempatan_siswa.php");
        exit;
    } else {
        $_SESSION['pesan_error'] = "Data gagal diperbarui: " . mysqli_error($koneksi);
        mysqli_stmt_close($query);
        header("Location: edit_penempatan_siswa.php?id=" . (int) $id);
        exit;
    }

} else {
    header("Location: data_penempatan_siswa.php");
    exit;
}
?>
