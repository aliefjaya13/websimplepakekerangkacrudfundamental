<?php
include 'config.php';

$columnResult = mysqli_query($conn, 'SHOW COLUMNS FROM tbsiswa');
$columnNames = $columnResult ? array_column(mysqli_fetch_all($columnResult, MYSQLI_ASSOC), 'Field') : [];
$selectFields = ['id', 'nama', 'kelas'];
foreach (['no_absen', 'status_kehadiran', 'terakhir_piket'] as $optionalField) {
    if (in_array($optionalField, $columnNames, true)) {
        $selectFields[] = $optionalField;
    }
}
$orderBy = in_array('no_absen', $columnNames, true)
    ? 'no_absen, CAST(kelas AS UNSIGNED), id'
    : 'CAST(kelas AS UNSIGNED), id';
$result = mysqli_query($conn, 'SELECT ' . implode(', ', $selectFields) . ' FROM tbsiswa ORDER BY ' . $orderBy);
$students = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$badgeClasses = [
    'hadir' => 'bg-green-100 text-green-800',
    'tidak hadir' => 'bg-red-100 text-red-800',
    'izin' => 'bg-yellow-100 text-yellow-800',
    'sakit' => 'bg-yellow-100 text-yellow-800',
];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f6ee">
    <title>Data Siswa | Ruang Piket</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root{--ink:#20332b;--muted:#718078;--green:#276b4c;--green-dark:#194b37;--paper:#f5f6ee;--line:#e2e7dc;--white:#fff}
        *{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}.shell{max-width:1160px;margin:auto;padding:28px 34px 60px}
        .topbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding:13px 0 22px;border-top:3px solid #d71920;border-bottom:1px solid var(--line)}.brand{display:flex;align-items:center;gap:10px;min-width:0;font-weight:750;letter-spacing:.01em}.school-crest,.rpl-mark{width:42px;height:42px;flex:none;object-fit:contain;border-radius:8px}.school-crest{border:1px solid #d71920;background:#080808}.rpl-mark{border:1px solid #35e600;background:#080808}.brand-copy{min-width:0}.brand-copy small{display:block;margin-top:2px;color:var(--muted);font-size:11px;font-weight:650;letter-spacing:.08em;text-transform:uppercase}.nav{display:flex;gap:8px}.nav a{padding:10px 14px;color:var(--muted);text-decoration:none;font-size:14px;font-weight:650;border-radius:8px}.nav a.active{background:#e6ecdd;color:var(--green-dark)}
        .heading{display:flex;align-items:end;justify-content:space-between;gap:18px;padding:37px 0 23px}.eyebrow{margin:0 0 8px;color:var(--green);font-size:12px;font-weight:750;letter-spacing:.13em;text-transform:uppercase}.heading h1{margin:0;font-size:clamp(27px,4vw,38px);line-height:1.12;letter-spacing:-.035em}.heading p{margin:9px 0 0;color:var(--muted);font-size:14px}.primary{display:inline-flex;align-items:center;gap:8px;padding:11px 15px;border-radius:7px;background:var(--green);color:#fff;text-decoration:none;font-size:13px;font-weight:700;white-space:nowrap}.primary:hover{background:var(--green-dark)}
        .summary{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;color:var(--muted);font-size:12px}.summary strong{color:var(--ink);font-size:14px}.table-wrap{overflow:hidden;border:1px solid var(--line);border-radius:10px;background:var(--white)}table{width:100%;border-collapse:collapse;text-align:left}thead{background:#f8f9f4}th{padding:13px 17px;border-bottom:1px solid var(--line);color:#69776e;font-size:10px;font-weight:750;letter-spacing:.09em;text-transform:uppercase}td{padding:15px 17px;border-bottom:1px solid #edf0e9;font-size:13px}tbody tr:last-child td{border-bottom:0}tbody tr:hover{background:#fbfcf8}.absen{display:inline-grid;place-items:center;min-width:31px;height:29px;padding:0 7px;border-radius:7px;background:#edf2e8;color:var(--green-dark);font-size:12px;font-weight:750}.name{font-weight:700}.last-duty{color:var(--muted)}.actions{display:flex;align-items:center;gap:7px}.action{display:inline-flex;align-items:center;gap:5px;padding:7px 9px;border:1px solid var(--line);border-radius:6px;color:var(--green-dark);text-decoration:none;font-size:11px;font-weight:700}.action:hover{background:#f2f5ed}.action.delete{color:#a34535}.action.delete:hover{background:#fff2ef}.empty{text-align:center;padding:44px 20px;color:var(--muted);font-size:14px}.empty strong{display:block;margin-bottom:6px;color:var(--ink);font-size:15px}
        @media(max-width:720px){.shell{padding:18px 16px 40px}.heading{align-items:flex-start;flex-direction:column;padding-top:28px}.table-wrap{overflow:visible;border:0;background:transparent}table,tbody{display:block}thead{position:absolute;width:1px;height:1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap}tbody{display:grid;gap:10px}tbody tr{display:grid;grid-template-columns:1fr 1fr;gap:10px;padding:15px;border:1px solid var(--line);border-radius:9px;background:#fff}td{display:block;padding:0;border:0!important}td:before{display:block;margin-bottom:5px;color:var(--muted);font-size:9px;font-weight:750;letter-spacing:.08em;text-transform:uppercase;content:attr(data-label)}td:first-child{grid-column:1}td:nth-child(2){grid-column:2}td:nth-child(3),td:nth-child(4){grid-column:1/-1}td:last-child{grid-column:1/-1}.actions{padding-top:9px}.nav a{padding:9px 10px;font-size:12px}}
        @media(max-width:420px){.topbar{align-items:flex-start}.brand{gap:7px;font-size:14px}.school-crest,.rpl-mark{width:34px;height:34px}.brand-copy small{font-size:9px;letter-spacing:.04em}.heading h1{font-size:28px}.primary{width:100%;justify-content:center}.summary{align-items:flex-start;gap:8px;flex-direction:column}}
    </style>
</head>
<body>
    <main class="shell">
        <header class="topbar">
            <div class="brand"><img class="school-crest" src="img/logo.jpg" alt="Logo SMKN 1 Probolinggo"><span class="brand-copy">Ruang Piket<small>SMKN 1 Probolinggo · XI RPL 1</small></span><img class="rpl-mark" src="img/rpl.jpg" alt="Logo Rekayasa Perangkat Lunak"></div>
            <nav class="nav" aria-label="Navigasi utama"><a href="index.php">Dashboard</a><a class="active" href="pageview.php">Data siswa</a></nav>
        </header>
        <section class="heading">
            <div><p class="eyebrow">Direktori kelas</p><h1>Data siswa</h1><p>Daftar siswa dan ringkasan status piket kelas.</p></div>
            <a class="primary" href="index.php"><span aria-hidden="true">＋</span> Tambah siswa</a>
        </section>
        <div class="summary"><strong>Daftar siswa</strong><span><?= count($students) ?> siswa terdaftar</span></div>
        <div class="table-wrap">
            <?php if ($students): ?>
                <table>
                    <thead><tr><th scope="col">No Absen</th><th scope="col">Nama siswa</th><th scope="col">Status kehadiran</th><th scope="col">Terakhir piket</th><th scope="col">Aksi</th></tr></thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <?php
                            $attendanceStatus = trim((string) ($student['status_kehadiran'] ?? ''));
                            $statusKey = strtolower($attendanceStatus);
                            $statusLabel = $attendanceStatus !== '' ? $attendanceStatus : 'Belum dicatat';
                            $statusClass = $badgeClasses[$statusKey] ?? 'bg-gray-100 text-gray-600';
                            $lastPiket = trim((string) ($student['terakhir_piket'] ?? ''));
                            ?>
                            <tr>
                                <td data-label="No absen"><span class="absen"><?= $escape($student['no_absen'] ?? $student['kelas']) ?></span></td>
                                <td data-label="Nama siswa"><span class="name"><?= $escape($student['nama']) ?></span></td>
                                <td data-label="Status kehadiran"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold <?= $statusClass ?>"><?= $escape($statusLabel) ?></span></td>
                                <td data-label="Terakhir piket"><span class="last-duty"><?= $lastPiket !== '' ? $escape($lastPiket) : 'Belum ada riwayat' ?></span></td>
                                <td data-label="Aksi"><div class="actions"><a class="action" href="update.php?id=<?= (int) $student['id'] ?>"><span aria-hidden="true">✎</span> Edit</a><a class="action delete" href="hapus.php?id=<?= (int) $student['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?');"><span aria-hidden="true">×</span> Hapus</a></div></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty"><strong>Belum ada data siswa</strong>Tambahkan siswa pertama dari halaman dashboard.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>