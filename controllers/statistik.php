<?php
require_once '../config/database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function generateFilename($ext) {
    return 'stat_' . time() . '_' . rand(100, 999) . '.' . $ext;
}

if (isset($_POST['tambah'])) {
    $kategori_id = $_POST['kategori_id'] ?? null;
    $submenu_id = $_POST['submenu_id'] ?? null;
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'] ?? null;
    $tipe_grafik = $_POST['tipe_grafik'];
    $sumber_data = $_POST['sumber_data'];
    $file_csv = null;

    $pdo->beginTransaction();

    try {
        // Upload CSV jika dipilih
        if ($sumber_data === 'csv' && isset($_FILES['file_csv'])) {
            $file = $_FILES['file_csv'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = generateFilename($ext);
            move_uploaded_file($file['tmp_name'], '../uploads/csv/' . $filename);
            $file_csv = $filename;
        }

        // Simpan ke tabel utama
        $stmt = $pdo->prepare("
            INSERT INTO statistik (kategori_id, submenu_id, judul, deskripsi, tipe_grafik, sumber_data, file_csv) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $kategori_id ?: null,
            $submenu_id,
            $judul,
            $deskripsi,
            $tipe_grafik,
            $sumber_data,
            $file_csv
        ]);

        $statistik_id = $pdo->lastInsertId();

        // Handle input manual
        if ($sumber_data === 'manual') {

            // PIE: label[] dan value[]
            if ($tipe_grafik === 'pie' && isset($_POST['label'], $_POST['value'])) {
                $labels = $_POST['label'];
                $values = $_POST['value'];

                $stmt = $pdo->prepare("
                    INSERT INTO statistik_data_manual (statistik_id, label, series_label, value) 
                    VALUES (?, ?, ?, ?)
                ");
                foreach ($labels as $i => $label) {
                    $value = $values[$i];
                    if (trim($label) !== '' && is_numeric($value)) {
                        $stmt->execute([$statistik_id, (string)$label, null, $value]);
                    }
                }

            // BAR / LINE: multi-series input
            } elseif (
                ($tipe_grafik === 'bar' || $tipe_grafik === 'line') &&
                isset($_POST['series_name'], $_POST['series_label'], $_POST['series_value'])
            ) {
                $series_names = $_POST['series_name']; // [series_0, series_1, ...]
                $series_labels = $_POST['series_label']; // array: [0 => [label1, label2], 1 => [...]]
                $series_values = $_POST['series_value']; // array: [0 => [val1, val2], 1 => [...]]

                $stmt = $pdo->prepare("
                    INSERT INTO statistik_data_manual (statistik_id, label, series_label, value) 
                    VALUES (?, ?, ?, ?)
                ");

                foreach ($series_names as $series_index => $series_name) {
                    $labels = $series_labels[$series_index];
                    $values = $series_values[$series_index];

                    foreach ($labels as $i => $label) {
                        $value = $values[$i];
                        if (trim($label) !== '' && is_numeric($value)) {
                            $stmt->execute([$statistik_id, $label, $series_name, $value]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: ../views/admin/statistik/index.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $kategori_id = $_POST['kategori_id'] ?? null;
    $submenu_id = $_POST['submenu_id'];
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'] ?? null;
    $tipe_grafik = $_POST['tipe_grafik'];
    $sumber_data = $_POST['sumber_data'];
    $file_csv = null;

    $pdo->beginTransaction();

    try {
        // Cek apakah user upload file baru
        if ($sumber_data === 'csv' && isset($_FILES['file_csv']) && $_FILES['file_csv']['error'] === 0) {
            $file = $_FILES['file_csv'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = generateFilename($ext);
            move_uploaded_file($file['tmp_name'], '../uploads/csv/' . $filename);
            $file_csv = $filename;

            // Update statistik dengan file baru
            $stmt = $pdo->prepare("UPDATE statistik SET kategori_id = ?, submenu_id = ?, judul = ?, deskripsi = ?, tipe_grafik = ?, sumber_data = ?, file_csv = ? WHERE id = ?");
            $stmt->execute([
                $kategori_id ?: null,
                $submenu_id,
                $judul,
                $deskripsi,
                $tipe_grafik,
                $sumber_data,
                $file_csv,
                $id
            ]);
        } else {
            // Update statistik tanpa mengubah file_csv
            $stmt = $pdo->prepare("UPDATE statistik SET kategori_id = ?, submenu_id = ?, judul = ?, deskripsi = ?, tipe_grafik = ?, sumber_data = ? WHERE id = ?");
            $stmt->execute([
                $kategori_id ?: null,
                $submenu_id,
                $judul,
                $deskripsi,
                $tipe_grafik,
                $sumber_data,
                $id
            ]);
        }

        // Hapus data manual lama
        $pdo->prepare("DELETE FROM statistik_data_manual WHERE statistik_id = ?")->execute([$id]);

        // Tambahkan ulang data manual jika sumber manual
        if ($sumber_data === 'manual') {
            if ($tipe_grafik === 'pie' && isset($_POST['label'], $_POST['value'])) {
                $labels = $_POST['label'];
                $values = $_POST['value'];

                $stmt = $pdo->prepare("INSERT INTO statistik_data_manual (statistik_id, label, series_label, value) VALUES (?, ?, ?, ?)");
                foreach ($labels as $i => $label) {
                    $value = $values[$i];
                    if (trim($label) !== '' && is_numeric($value)) {
                        $stmt->execute([$id, $label, null, $value]);
                    }
                }
            } elseif (
                ($tipe_grafik === 'bar' || $tipe_grafik === 'line') &&
                isset($_POST['series_name'], $_POST['series_label'], $_POST['series_value'])
            ) {
                $series_names = $_POST['series_name'];
                $series_labels = $_POST['series_label'];
                $series_values = $_POST['series_value'];

                $stmt = $pdo->prepare("INSERT INTO statistik_data_manual (statistik_id, label, series_label, value) VALUES (?, ?, ?, ?)");

                foreach ($series_names as $index => $series_name) {
                    $labels = $series_labels[$index];
                    $values = $series_values[$index];
                    foreach ($labels as $i => $label) {
                        $value = $values[$i];
                        if (trim($label) !== '' && is_numeric($value)) {
                            $stmt->execute([$id, $label, $series_name, $value]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        header("Location: ../views/admin/statistik/index.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error saat update: " . $e->getMessage();
    }
}


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];

    try {
        // Ambil file_csv dulu untuk dihapus jika perlu
        $stmt = $pdo->prepare("SELECT file_csv FROM statistik WHERE id = ?");
        $stmt->execute([$id]);
        $stat = $stmt->fetch();

        // Hapus file_csv dari direktori jika ada
        if ($stat && $stat['file_csv']) {
            $path = '../uploads/csv/' . $stat['file_csv'];
            if (file_exists($path)) {
                unlink($path);
            }
        }

        // Hapus data manual
        $stmt = $pdo->prepare("DELETE FROM statistik_data_manual WHERE statistik_id = ?");
        $stmt->execute([$id]);

        // Hapus data utama
        $stmt = $pdo->prepare("DELETE FROM statistik WHERE id = ?");
        $stmt->execute([$id]);

        // Redirect balik ke index
        header("Location: ../views/admin/statistik/index.php");
        exit;

    } catch (Exception $e) {
        echo "Gagal menghapus data: " . $e->getMessage();
    }
}
