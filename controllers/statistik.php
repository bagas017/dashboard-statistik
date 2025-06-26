<?php
require_once '../config/database.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function generateFilename($ext) {
    return 'stat_' . time() . '_' . rand(100, 999) . '.' . $ext;
}

// Tambah statistik
if (isset($_POST['tambah'])) {
    $kategori_id = $_POST['kategori_id'];
    $judul = $_POST['judul'];
    $tipe_grafik = $_POST['tipe_grafik'];
    $sumber_data = $_POST['sumber_data'];
    $file_csv = null;

    $pdo->beginTransaction();

    try {
        if ($sumber_data === 'csv' && isset($_FILES['file_csv'])) {
            $file = $_FILES['file_csv'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = generateFilename($ext);
            move_uploaded_file($file['tmp_name'], '../uploads/csv/' . $filename);
            $file_csv = $filename;
        }

        $stmt = $pdo->prepare("INSERT INTO statistik (kategori_id, judul, tipe_grafik, sumber_data, file_csv) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$kategori_id, $judul, $tipe_grafik, $sumber_data, $file_csv]);

        $statistik_id = $pdo->lastInsertId();

        if ($sumber_data === 'manual' && isset($_POST['label'], $_POST['value'])) {
            $labels = $_POST['label'];
            $values = $_POST['value'];

            if (count($labels) === count($values)) {
                $stmt = $pdo->prepare("INSERT INTO statistik_data_manual (statistik_id, label, value) VALUES (?, ?, ?)");
                foreach ($labels as $i => $label) {
                    if (trim($label) !== '' && is_numeric($values[$i])) {
                        $stmt->execute([$statistik_id, $label, $values[$i]]);
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
?>