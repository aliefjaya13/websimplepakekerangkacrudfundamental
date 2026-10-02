<?php
include 'config.php';

$students = [];
$columnResult = mysqli_query($conn, 'SHOW COLUMNS FROM tbsiswa');
$columnNames = $columnResult ? array_column(mysqli_fetch_all($columnResult, MYSQLI_ASSOC), 'Field') : [];
$selectFields = ['id', 'nama', 'kelas'];
foreach (['no_absen', 'status_kehadiran', 'terakhir_piket'] as $optionalField) {
  if (in_array($optionalField, $columnNames, true)) {
    $selectFields[] = $optionalField;
  }
}
$result = mysqli_query($conn, 'SELECT ' . implode(', ', $selectFields) . ' FROM tbsiswa');
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }
}

usort($students, static function ($first, $second) {
  $firstValue = trim((string) ($first['no_absen'] ?? $first['kelas']));
  $secondValue = trim((string) ($second['no_absen'] ?? $second['kelas']));
  $firstIsNumber = ctype_digit($firstValue);
  $secondIsNumber = ctype_digit($secondValue);

  if ($firstIsNumber && $secondIsNumber) {
    return ((int) $firstValue <=> (int) $secondValue)
      ?: ((int) $first['id'] <=> (int) $second['id']);
  }

  return ($secondIsNumber <=> $firstIsNumber)
    ?: ((int) $first['id'] <=> (int) $second['id']);
});

$todayIndex = count($students) ? (int) date('z') % count($students) : 0;
$schedule = array_map(static function ($student) {
  $storedValue = trim((string) ($student['no_absen'] ?? $student['kelas']));
    return [
        'id' => (int) $student['id'],
        'nama' => $student['nama'],
    'value' => $storedValue,
    'is_absen' => ctype_digit($storedValue),
    'status_kehadiran' => $student['status_kehadiran'] ?? '',
    'terakhir_piket' => $student['terakhir_piket'] ?? '',
    ];
}, $students);

mysqli_close($conn);
$escape = static fn($value) => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
$initialStudent = $schedule[$todayIndex] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#f5f6ee">
  <title>Ruang Piket | Adiwiyata</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root{--ink:#20332b;--muted:#718078;--green:#276b4c;--green-dark:#194b37;--lime:#d6e77b;--paper:#f5f6ee;--line:#e2e7dc;--white:#fff;--red:#a34535}
    *{box-sizing:border-box}body{margin:0;background:var(--paper);color:var(--ink);font-family:ui-sans-serif,system-ui,-apple-system,"Segoe UI",sans-serif}
    .shell{max-width:1160px;margin:auto;padding:28px 34px 60px}.topbar{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;padding:13px 0 22px;border-top:3px solid #d71920;border-bottom:1px solid var(--line)}
    .brand{display:flex;align-items:center;gap:10px;min-width:0;font-weight:750;letter-spacing:.01em}.school-crest,.rpl-mark{width:42px;height:42px;flex:none;object-fit:contain;border-radius:8px}.school-crest{border:1px solid #d71920;background:#080808}.rpl-mark{border:1px solid #35e600;background:#080808}.brand-copy{min-width:0}.brand-copy small{display:block;margin-top:2px;color:var(--muted);font-size:11px;font-weight:650;letter-spacing:.08em;text-transform:uppercase}.nav{display:flex;gap:8px}.nav a{padding:10px 14px;color:var(--muted);text-decoration:none;font-size:14px;font-weight:650;border-radius:8px}.nav a.active{background:#e6ecdd;color:var(--green-dark)}
    .intro{display:flex;align-items:end;justify-content:space-between;gap:20px;padding:35px 0 24px}.eyebrow{margin:0 0 8px;color:var(--green);font-size:12px;font-weight:750;letter-spacing:.13em;text-transform:uppercase}.intro h1{margin:0;font-size:clamp(27px,4vw,39px);line-height:1.12;letter-spacing:-.035em}.intro p{margin:9px 0 0;color:var(--muted);font-size:14px}.date-chip{padding:10px 13px;border:1px solid var(--line);border-radius:8px;background:#fff9;font-size:13px;font-weight:650;white-space:nowrap}
    .layout{display:grid;grid-template-columns:minmax(0,1.15fr) minmax(290px,.85fr);gap:18px;align-items:start}.panel{background:var(--white);border:1px solid var(--line);border-radius:10px}.panel-head{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:20px 22px 16px;border-bottom:1px solid #edf0e9}.panel-head h2{margin:0;font-size:16px;letter-spacing:-.015em}.panel-head p{margin:5px 0 0;color:var(--muted);font-size:12px}.step{color:#87938a;font-size:11px;font-weight:750;letter-spacing:.1em;text-transform:uppercase}
    .form-body{padding:21px 22px 23px}.input-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}.field{margin-bottom:17px}.field label,.field-title{display:block;margin-bottom:7px;font-size:13px;font-weight:700}.field input,.field select{width:100%;height:44px;padding:0 12px;border:1px solid #d9e0d7;border-radius:7px;background:white;color:var(--ink);font:inherit;font-size:14px;outline:none}.field input:focus,.field select:focus{border-color:var(--green);box-shadow:0 0 0 3px #276b4c1a}.field input::placeholder{color:#a0aaa2}.help{margin:6px 0 0;color:var(--muted);font-size:11px}.submit{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;height:45px;border:0;border-radius:7px;background:var(--green);color:#fff;font:inherit;font-size:14px;font-weight:700;cursor:pointer}.submit:hover{background:var(--green-dark)}
    .roster{margin-top:18px;padding:18px 20px}.roster-title{display:flex;align-items:center;justify-content:space-between;margin-bottom:13px}.roster-title h2{margin:0;font-size:14px}.count{color:var(--muted);font-size:12px}.roster-list{display:flex;flex-wrap:wrap;gap:7px}.roster-item{display:flex;align-items:center;gap:7px;padding:7px 9px;border:1px solid var(--line);border-radius:7px;font-size:12px}.roster-item b{display:grid;place-items:center;width:21px;height:21px;border-radius:6px;background:#edf2e8;color:var(--green-dark);font-size:11px}.empty{color:var(--muted);font-size:13px}
    .today{position:relative;overflow:hidden;padding:22px;background:var(--green-dark);color:#fff}.today:after{position:absolute;right:-33px;top:-55px;width:165px;height:165px;border:1px solid #ffffff20;border-radius:50%;content:"";box-shadow:0 0 0 22px #ffffff09,0 0 0 48px #ffffff06}.today .eyebrow{color:var(--lime)}.today-label{position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#d2dfd5}.live-dot{width:7px;height:7px;margin-right:7px;display:inline-block;border-radius:50%;background:var(--lime)}.person{position:relative;z-index:1;margin:26px 0 21px}.person .number{display:block;color:var(--lime);font-size:12px;font-weight:700;letter-spacing:.09em;text-transform:uppercase}.person strong{display:block;margin-top:7px;font-size:27px;line-height:1.15;letter-spacing:-.025em}.today-meta{position:relative;z-index:1;display:flex;justify-content:space-between;padding-top:14px;border-top:1px solid #ffffff2a;color:#d2dfd5;font-size:12px}.today-empty{position:relative;z-index:1;margin:25px 0;color:#e4ebe5;font-size:14px}
    .controls{margin-top:18px;padding:20px}.controls h2{margin:0;font-size:15px}.controls .sub{margin:5px 0 17px;color:var(--muted);font-size:12px}.controls .field{margin-bottom:14px}.status-row{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:7px}.status-option input{position:absolute;opacity:0;pointer-events:none}.status-option span{display:block;padding:10px 8px;border:1px solid #ffffff3a;border-radius:7px;background:#ffffff12;text-align:center;color:#e7eee8;font-size:12px;font-weight:650;cursor:pointer}.status-option input:checked+span{border-color:var(--lime);background:var(--lime);color:var(--green-dark)}.attendance-submit{width:100%;height:42px;margin-top:13px;border:0;border-radius:7px;background:var(--lime);color:var(--green-dark);font:inherit;font-size:13px;font-weight:750;cursor:pointer}.attendance-submit:hover{background:#e4f19f}.attendance-empty{margin-top:13px;color:#f1e6af;font-size:12px}
    @media(max-width:760px){.shell{padding:18px 16px 40px}.intro{align-items:start;flex-direction:column;padding-top:27px}.layout{grid-template-columns:1fr}.right-column{display:grid;grid-template-columns:1fr 1fr;gap:12px}.controls{margin-top:0}.today{min-height:210px}.roster{margin-top:14px}}
    @media(max-width:520px){.topbar{align-items:flex-start}.nav a{padding:9px 10px;font-size:12px}.brand{gap:7px;font-size:14px}.school-crest,.rpl-mark{width:34px;height:34px}.brand-copy small{font-size:9px;letter-spacing:.04em}.right-column{grid-template-columns:1fr}.today{min-height:0}.date-chip{font-size:12px}.form-body,.panel-head{padding-left:17px;padding-right:17px}.input-grid{grid-template-columns:1fr}}
    .today-status{position:relative;z-index:1;margin-top:13px}.today-status span{display:inline-flex;padding:6px 9px;border-radius:99px;background:#ffffff18;color:#e2e9e2;font-size:11px;font-weight:700}.today-status[data-state="Hadir"] span{background:#d9f1dd;color:#1f653e}.today-status[data-state="Tidak Hadir"] span{background:#fbecea;color:#a34535}.today-status[data-state="Izin"] span{background:#f8efc9;color:#796522}
  </style>
</head>
<body>
  <main class="shell">
    <header class="topbar">
      <div class="brand"><img class="school-crest" src="img/logo.jpg" alt="Logo SMKN 1 Probolinggo"><span class="brand-copy">Ruang Piket<small>SMKN 1 Probolinggo · XI RPL 1</small></span><img class="rpl-mark" src="img/rpl.jpg" alt="Logo Rekayasa Perangkat Lunak"></div>
      <nav class="nav" aria-label="Navigasi utama"><a class="active" href="index.php">Dashboard</a><a href="pageview.php">Data siswa</a></nav>
    </header>
    <section class="intro">
      <div><p class="eyebrow">Pengelolaan kelas</p><h1>Jadwal piket, lebih teratur.</h1><p>Tambah siswa dan lihat giliran piket hari ini.</p></div>
      <div class="date-chip"><?= $escape(date('l, d F Y')) ?></div>
    </section>
    <section class="layout">
      <div>
        <section class="panel">
          <header class="panel-head"><div><h2>Tambah siswa</h2><p>Masukkan nama dan nomor absen siswa.</p></div><span class="step">01 / Data</span></header>
          <form class="form-body" action="prosesinput.php" method="post">
            <div class="input-grid grid grid-cols-1 gap-3 sm:grid-cols-2">
              <div class="field"><label for="nama">Nama siswa</label><input id="nama" type="text" name="nama" placeholder="Contoh: Alya Putri" autocomplete="name" required></div>
              <div class="field"><label for="kelas">Nomor absen</label><input id="kelas" type="text" inputmode="numeric" name="kelas" placeholder="Contoh: 12" required><p class="help">Disimpan melalui field kelas CRUD.</p></div>
            </div>
            <button class="submit" type="submit"><span aria-hidden="true">＋</span> Simpan data siswa</button>
          </form>
        </section>
        <section class="panel roster">
          <div class="roster-title"><h2>Urutan petugas</h2><span class="count"><?= count($students) ?> siswa</span></div>
          <?php if ($students): ?>
            <div class="roster-list">
              <?php foreach ($schedule as $student): ?>
                <span class="roster-item"><b><?= $student['is_absen'] ? $escape($student['value']) : '•' ?></b><?= $escape($student['nama']) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="empty">Belum ada siswa. Tambahkan data untuk memulai jadwal.</p>
          <?php endif; ?>
        </section>
      </div>
      <div class="right-column">
        <section class="panel today" aria-live="polite">
          <div class="today-label"><span><i class="live-dot"></i>JADWAL HARI INI</span><span id="rotation-label">Urutan absen</span></div>
          <?php if ($initialStudent): ?>
            <div class="person"><span class="number" id="today-number"><?= $initialStudent['is_absen'] ? 'Absen ' . $escape($initialStudent['value']) : 'Data kelas tersimpan' ?></span><strong id="today-name"><?= $escape($initialStudent['nama']) ?></strong></div>
            <div class="today-meta"><span>Petugas piket</span><span id="today-position">Giliran <?= $todayIndex + 1 ?> dari <?= count($students) ?></span></div>
            <div class="today-status" id="today-status"><span><?= $escape($initialStudent['status_kehadiran'] ?: 'Belum dicatat') ?></span></div>
            <form id="attendance-form" action="konfirmasipiket.php" method="post">
              <input type="hidden" id="attendance-student-id" name="id" value="<?= (int) $initialStudent['id'] ?>">
              <div class="status-row" role="group" aria-label="Status kehadiran petugas">
                <label class="status-option"><input type="radio" name="status_kehadiran" value="Hadir" required><span>Hadir</span></label>
                <label class="status-option"><input type="radio" name="status_kehadiran" value="Tidak Hadir" required><span>Tidak hadir</span></label>
                <label class="status-option"><input type="radio" name="status_kehadiran" value="Izin" required><span>Izin</span></label>
                <label class="status-option"><input type="radio" name="status_kehadiran" value="Sakit" required><span>Sakit</span></label>
              </div>
              <button class="attendance-submit" type="submit">Simpan konfirmasi</button>
            </form>
          <?php else: ?>
            <p class="today-empty">Tambahkan data siswa untuk melihat petugas piket.</p>
          <?php endif; ?>
        </section>
        <section class="panel controls">
          <h2>Penyesuaian hari ini</h2><p class="sub">Atur tampilan petugas dan status kehadiran.</p>
          <?php if ($schedule): ?>
            <div class="field"><label for="halangan">Petugas berhalangan</label><select id="halangan"><option value="">Tidak ada</option><?php foreach ($schedule as $index => $student): ?><option value="<?= $index ?>"><?= $escape($student['nama']) ?> · <?= $student['is_absen'] ? 'Absen ' : 'Data ' ?><?= $escape($student['value']) ?></option><?php endforeach; ?></select></div>
            <p class="help">Memilih siswa akan menampilkan petugas berikutnya berdasarkan nomor absen.</p>
          <?php else: ?><p class="empty">Kontrol tersedia setelah ada data siswa.</p><?php endif; ?>
        </section>
      </div>
    </section>
  </main>
  <script>
    const schedule = <?= json_encode($schedule, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const todayIndex = <?= $todayIndex ?>;
    const unavailable = document.getElementById('halangan');
    const attendanceForm = document.getElementById('attendance-form');

    if (unavailable) {
      unavailable.addEventListener('change', () => {
        const unavailableIndex = unavailable.value === '' ? null : Number(unavailable.value);
        const startIndex = unavailableIndex === null ? todayIndex : (unavailableIndex + 1) % schedule.length;
        const nextIndex = Array.from({ length: schedule.length }, (_, offset) => (startIndex + offset) % schedule.length)
          .find(index => index !== unavailableIndex);
        const name = document.getElementById('today-name');
        const number = document.getElementById('today-number');
        const position = document.getElementById('today-position');
        const attendanceId = document.getElementById('attendance-student-id');
        const status = document.querySelector('#today-status span');

        if (nextIndex === undefined) {
          name.textContent = 'Tidak ada petugas';
          number.textContent = 'Jadwal penuh';
          position.textContent = 'Tambahkan siswa lain';
          status.textContent = 'Belum dicatat';
          attendanceForm.hidden = true;
          return;
        }

        attendanceForm.hidden = false;
        attendanceId.value = schedule[nextIndex].id;
        attendanceForm.querySelectorAll('input[name="status_kehadiran"]').forEach(input => { input.checked = false; });
        name.textContent = schedule[nextIndex].nama;
        number.textContent = schedule[nextIndex].is_absen ? `Absen ${schedule[nextIndex].value}` : 'Data kelas tersimpan';
        position.textContent = `Giliran ${nextIndex + 1} dari ${schedule.length}`;
        status.textContent = schedule[nextIndex].status_kehadiran || 'Belum dicatat';
      });
    }
  </script>
</body>
</html>