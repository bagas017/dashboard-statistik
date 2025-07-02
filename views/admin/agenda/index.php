<?php
require_once '../../../config/database.php';

// Ambil semua agenda, urutkan dari yang terbaru
$stmt = $pdo->query("SELECT * FROM agenda ORDER BY tanggal DESC");
$agendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

function formatAgenda($tanggal, $mulai, $selesai) {
    // Format: 23 Juni 2025, 13.00 - 14.00 WIB
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
        '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
        '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    $pecah = explode('-', $tanggal);
    $tglFormatted = ltrim($pecah[2], '0') . ' ' . $bulan[$pecah[1]] . ' ' . $pecah[0];
    return $tglFormatted . ', ' . substr($mulai, 0, 5) . ' - ' . substr($selesai, 0, 5) . ' WIB';
}
?>

<h2>Daftar Agenda</h2>
<a href="tambah.php">+ Tambah Agenda Baru</a><br><br>

<?php if (isset($_GET['success'])): ?>
  <p style="color:green;">Agenda berhasil ditambahkan!</p>
<?php endif; ?>

<table border="1" cellpadding="10" cellspacing="0">
  <tr>
    <th>Judul</th>
    <th>Lokasi</th>
    <th>Waktu</th>
    <th>Aksi</th>
  </tr>
  <?php foreach ($agendas as $agenda): ?>
    <tr>
      <td><?= htmlspecialchars($agenda['nama_agenda']) ?></td>
      <td><?= htmlspecialchars($agenda['lokasi']) ?></td>
      <td><?= formatAgenda($agenda['tanggal'], $agenda['jam_mulai'], $agenda['jam_selesai']) ?></td>
      <td>
        <!-- Edit dan hapus bisa ditambahkan nanti -->
        <a href="#">✏️ Edit</a> | <a href="#">🗑️ Hapus</a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

