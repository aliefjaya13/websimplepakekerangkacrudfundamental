<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#f5f6ee">
    <title>Edit Siswa | Ruang Piket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-[#f5f6ee] font-sans text-[#20332b]">
<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id     = $_GET['id'];
    $sql    = "SELECT * FROM tbsiswa WHERE id='$id'";
    $result = mysqli_query($conn, $sql);
    $row    = mysqli_fetch_assoc($result);
}
?>

<main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-6 sm:px-8 sm:py-8">
    <header class="flex flex-wrap items-center justify-between gap-2 border-y border-t-[3px] border-[#e2e7dc] border-t-[#d71920] py-3">
        <a href="index.php" class="flex min-w-0 items-center gap-2 no-underline">
            <img src="img/logo.jpg" alt="Logo SMKN 1 Probolinggo" class="h-8 w-8 shrink-0 rounded-lg border border-[#d71920] bg-[#080808] object-contain sm:h-10 sm:w-10">
            <span class="min-w-0 text-sm font-bold tracking-wide sm:text-base">Ruang Piket<span class="mt-0.5 block text-[9px] font-semibold uppercase tracking-wider text-[#718078] sm:text-[11px] sm:tracking-widest">SMKN 1 Probolinggo · XI RPL 1</span></span>
            <img src="img/rpl.jpg" alt="Logo Rekayasa Perangkat Lunak" class="h-8 w-8 shrink-0 rounded-lg border border-[#35e600] bg-[#080808] object-contain sm:h-10 sm:w-10">
        </a>
        <nav class="flex items-center gap-1" aria-label="Navigasi utama">
            <a href="index.php" class="rounded-lg px-3 py-2 text-sm font-semibold text-[#718078] hover:bg-[#e6ecdd] hover:text-[#194b37]">Dashboard</a>
            <a href="pageview.php" class="rounded-lg bg-[#e6ecdd] px-3 py-2 text-sm font-semibold text-[#194b37]">Data siswa</a>
        </nav>
    </header>

    <section class="flex flex-1 items-center justify-center py-10">
        <div class="w-full max-w-xl overflow-hidden rounded-xl border border-[#e2e7dc] bg-white shadow-sm">
            <div class="border-b border-[#e2e7dc] px-6 py-5 sm:px-8">
                <p class="mb-2 text-xs font-bold uppercase tracking-[0.14em] text-[#276b4c]">Direktori kelas</p>
                <h1 class="text-2xl font-bold tracking-tight">Edit data siswa</h1>
                <p class="mt-2 text-sm text-[#718078]">Perbarui nama atau nomor absen siswa.</p>
            </div>
            <form action="prosesupdate.php" method="post" class="space-y-5 px-6 py-6 sm:px-8">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                <div>
                    <label for="nama" class="mb-2 block text-sm font-semibold">Nama siswa</label>
                    <input id="nama" type="text" name="nama" value="<?php echo $row['nama']; ?>" required class="h-11 w-full rounded-lg border border-[#e2e7dc] bg-white px-3 text-sm outline-none transition focus:border-[#276b4c] focus:ring-4 focus:ring-[#276b4c]/10">
                </div>
                <div>
                    <label for="kelas" class="mb-2 block text-sm font-semibold">Nomor absen</label>
                    <input id="kelas" type="text" name="kelas" value="<?php echo $row['kelas']; ?>" required class="h-11 w-full rounded-lg border border-[#e2e7dc] bg-white px-3 text-sm outline-none transition focus:border-[#276b4c] focus:ring-4 focus:ring-[#276b4c]/10">
                </div>
                <div class="flex flex-col-reverse gap-3 border-t border-[#e2e7dc] pt-5 sm:flex-row sm:justify-end">
                    <a href="pageview.php" class="inline-flex h-11 items-center justify-center rounded-lg border border-[#e2e7dc] px-5 text-sm font-semibold text-[#718078] transition hover:bg-[#f5f6ee]">Batal</a>
                    <button type="submit" value="Update Data" class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-[#276b4c] px-5 text-sm font-bold text-white transition hover:bg-[#194b37] focus:outline-none focus:ring-4 focus:ring-[#276b4c]/20"><span aria-hidden="true">✓</span> Update Data</button>
                </div>
            </form>
        </div>
    </section>
</main>
</body>
</html>