<?php
/**
 * Aktama Coffee - Diagnosa Sistem Hosting
 * Skrip diagnosa mandiri untuk memeriksa konfigurasi server, database, dan izin folder.
 * Akses: https://aktama.com/coffee/diagnosa.php
 */

header('Content-Type: text/html; charset=utf-8');

$sourceDir = file_exists(__DIR__ . '/../kopi-source') ? __DIR__ . '/../kopi-source' : __DIR__ . '/..';
if (file_exists(__DIR__ . '/artisan')) {
    $sourceDir = __DIR__;
}

$envFile = $sourceDir . '/.env';
$envExists = file_exists($envFile);
$env = [];
if ($envExists) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) continue;
        [$k, $v] = explode('=', $line, 2);
        $env[trim($k)] = trim(trim($v), '"\'');
    }
}

$dbStatus = 'Belum dicek';
$dbError = '';
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? '';
$dbUser = $env['DB_USERNAME'] ?? '';
$dbPass = $env['DB_PASSWORD'] ?? '';

if ($dbName && $dbUser) {
    try {
        $pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 5,
        ]);
        $tableCount = $pdo->query("SELECT count(*) FROM information_schema.tables WHERE table_schema = '{$dbName}'")->fetchColumn();
        $dbStatus = "Berhasil Terhubung (Ditemukan {$tableCount} tabel di database)";
    } catch (Throwable $e) {
        $dbStatus = "Gagal Terhubung";
        $dbError = $e->getMessage();
    }
}

$storageDir = $sourceDir . '/storage';
$storageWritable = is_writable($storageDir);
$viewsDir = $storageDir . '/framework/views';
$viewsWritable = file_exists($viewsDir) && is_writable($viewsDir);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosa Sistem - Aktama Coffee</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0f172a; color: #f8fafc; padding: 2rem; margin: 0; }
        .card { max-width: 800px; margin: 0 auto; background: #1e293b; border-radius: 12px; padding: 2rem; box-shadow: 0 10px 25px rgba(0,0,0,0.3); border: 1px solid #334155; }
        h1 { color: #f59e0b; margin-top: 0; font-size: 1.5rem; }
        .item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid #334155; }
        .item:last-child { border-bottom: none; }
        .label { font-weight: 500; color: #94a3b8; }
        .badge { padding: 0.35rem 0.75rem; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; }
        .badge-success { background: #065f46; color: #6ee7b7; }
        .badge-danger { background: #7f1d1d; color: #fca5a5; }
        .badge-warning { background: #78350f; color: #fde68a; }
        .error-box { background: #450a0a; border: 1px solid #991b1b; color: #fecaca; padding: 1rem; border-radius: 8px; margin-top: 1rem; font-family: monospace; font-size: 0.85rem; }
        .btn { display: inline-block; background: #f59e0b; color: #000; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 1.5rem; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Diagnosa Sistem Aktama Coffee</h1>
        <p style="color: #94a3b8; font-size: 0.9rem;">Halaman ini memeriksa status konfigurasi server secara real-time.</p>

        <div class="item">
            <span class="label">Versi PHP Server</span>
            <span class="badge <?= version_compare(PHP_VERSION, '8.2.0', '>=') ? 'badge-success' : 'badge-danger' ?>">
                PHP <?= PHP_VERSION ?> <?= version_compare(PHP_VERSION, '8.2.0', '>=') ? '(Sesuai)' : '(Wajib PHP 8.2+)' ?>
            </span>
        </div>

        <div class="item">
            <span class="label">File .env</span>
            <span class="badge <?= $envExists ? 'badge-success' : 'badge-danger' ?>">
                <?= $envExists ? 'Ditemukan' : 'Tidak Ditemukan (Wajib dibuat)' ?>
            </span>
        </div>

        <div class="item">
            <span class="label">APP_KEY (Enkripsi)</span>
            <span class="badge <?= !empty($env['APP_KEY']) ? 'badge-success' : 'badge-danger' ?>">
                <?= !empty($env['APP_KEY']) ? 'Terisi (' . substr($env['APP_KEY'], 0, 15) . '...)' : 'KOSONG (Penyebab 500 Error)' ?>
            </span>
        </div>

        <div class="item">
            <span class="label">APP_DEBUG</span>
            <span class="badge <?= ($env['APP_DEBUG'] ?? '') === 'true' ? 'badge-warning' : 'badge-success' ?>">
                <?= ($env['APP_DEBUG'] ?? 'false') ?>
            </span>
        </div>

        <div class="item">
            <span class="label">Koneksi Database MySQL</span>
            <span class="badge <?= str_contains($dbStatus, 'Berhasil') ? 'badge-success' : 'badge-danger' ?>">
                <?= $dbStatus ?>
            </span>
        </div>

        <?php if ($dbError): ?>
        <div class="error-box">
            <strong>Detail Error Database:</strong><br>
            <?= htmlspecialchars($dbError) ?>
        </div>
        <?php endif; ?>

        <div class="item">
            <span class="label">Folder storage (Izin Tulis)</span>
            <span class="badge <?= $storageWritable ? 'badge-success' : 'badge-danger' ?>">
                <?= $storageWritable ? 'Dapat Ditulis (Writable)' : 'Terkunci (Jalankan chmod 775)' ?>
            </span>
        </div>

        <div class="item">
            <span class="label">Folder framework/views</span>
            <span class="badge <?= $viewsWritable ? 'badge-success' : 'badge-danger' ?>">
                <?= $viewsWritable ? 'Siap' : 'Belum Ada / Izin Kurang' ?>
            </span>
        </div>

        <div style="margin-top: 1.5rem; text-align: center;">
            <a href="index.php" class="btn">Buka Halaman Utama Website &rarr;</a>
        </div>
    </div>
</body>
</html>
