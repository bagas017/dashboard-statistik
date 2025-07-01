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
        $stmt = $pdo->prepare("INSERT INTO statistik (kategori_id, submenu_id, judul, tipe_grafik, sumber_data, file_csv) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $kategori_id ?: null,
            $submenu_id,
            $judul,
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

                $stmt = $pdo->prepare("INSERT INTO statistik_data_manual (statistik_id, label, series_label, value) VALUES (?, ?, ?, ?)");
                foreach ($labels as $i => $label) {
                    $value = $values[$i];
                    if (trim($label) !== '' && is_numeric($value)) {
                        $stmt->execute([$statistik_id, (string)$label, null, $value]);
                    }
                }

            // BAR / LINE: multi-series
            } elseif (($tipe_grafik === 'bar' || $tipe_grafik === 'line') && isset($_POST['series_name'], $_POST['series_label'], $_POST['series_value'])) {
                $series_names = $_POST['series_name']; // [series_0, series_1, ...]
                $series_labels = $_POST['series_label']; // array: [0 => [label1, label2], 1 => [...]]
                $series_values = $_POST['series_value']; // array: [0 => [val1, val2], 1 => [...]]
                
                $stmt = $pdo->prepare("INSERT INTO statistik_data_manual (statistik_id, label, series_label, value) VALUES (?, ?, ?, ?)");

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
