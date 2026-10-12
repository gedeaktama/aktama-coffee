<?php
/**
 * Skrip Darurat Pembuatan Symlink Storage Publik via Web Browser
 * Digunakan jika shared hosting tidak menyediakan Terminal SSH.
 *
 * PENTING: Demi alasan keamanan sistem, SEGERA HAPUS FILE INI
 * setelah dijalankan satu kali di browser hosting Anda!
 */

// 1. Tentukan target folder storage/app/public
$target = __DIR__ . '/../storage/app/public';

// Jika menggunakan arsitektur folder terpisah (kopi-source sejajar dengan public_html)
if (!file_exists($target) && file_exists(__DIR__ . '/../kopi-source/storage/app/public')) {
    $target = __DIR__ . '/../kopi-source/storage/app/public';
}

$shortcut = __DIR__ . '/storage';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penyambung Storage Publik - Aktama Coffee</title>
    <style>
        body { font-family: system-ui, sans-serif; padding: 2rem; background: #f8fafc; color: #1e293b; max-width: 600px; margin: auto; }
        .card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 2rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .success { color: #15803d; background: #dcfce7; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .error { color: #b91c1c; background: #fee2e2; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .warning { color: #b45309; background: #fef3c7; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        code { background: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Penyambung Storage Publik &mdash; Aktama Coffee</h2>
        <p>Target folder: <code><?= htmlspecialchars($target) ?></code></p>
        <p>Shortcut: <code><?= htmlspecialchars($shortcut) ?></code></p>

        <?php if (file_exists($shortcut)): ?>
            <div class="success">
                <strong>Status: SUDAH TERHUBUNG!</strong><br>
                Folder symlink storage sudah aktif di direktori publik ini.
            </div>
        <?php elseif (@symlink($target, $shortcut)): ?>
            <div class="success">
                <strong>Status: BERHASIL DIBUAT!</strong><br>
                Symlink berhasil menghubungkan storage publik ke folder aplikasi.
            </div>
        <?php else: ?>
            <div class="error">
                <strong>Status: GAGAL MEMBUAT SYMLINK</strong><br>
                Fungsi <code>symlink()</code> mungkin dinonaktifkan oleh hosting atau izin folder tidak memadai.
            </div>
        <?php endif; ?>

        <div class="warning">
            <strong>PERINGATAN KEAMANAN:</strong><br>
            Segera <strong>HAPUS</strong> file <code>storage-link-runner.php</code> ini dari File Manager cPanel hosting Anda sekarang!
        </div>
    </div>
</body>
</html>
