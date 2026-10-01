<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    fwrite(STDERR, "Este script solo puede ejecutarse desde CLI.\n");
    exit(1);
}

require dirname(__DIR__) . '/bootstrap.php';

$token = getenv('DYNAMIC_GALERY_READ_WRITE_TOKEN') ?: getenv('BLOB_READ_WRITE_TOKEN') ?: '';
if ($token === '') {
    throw new RuntimeException('Falta DYNAMIC_GALERY_READ_WRITE_TOKEN.');
}

$directory = $config['paths']['images_dir'];
$files = glob($directory . '/*') ?: [];
$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
$uploaded = 0;
$skipped = 0;

foreach ($files as $file) {
    if (!is_file($file)) {
        continue;
    }

    $filename = basename($file);
    $photo = $pdo->prepare('SELECT id, imagen FROM fotos WHERE imagen = :imagen LIMIT 1');
    $photo->execute([':imagen' => $filename]);
    $row = $photo->fetch();

    if (!$row || filter_var($row['imagen'], FILTER_VALIDATE_URL)) {
        $skipped++;
        continue;
    }

    $info = @getimagesize($file);
    $mime = $info['mime'] ?? '';
    if (!isset($allowed[$mime])) {
        fwrite(STDERR, "Omitida (formato no permitido): {$filename}\n");
        $skipped++;
        continue;
    }

    $pathname = 'gallery/' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $handle = curl_init('https://blob.vercel-storage.com/' . $pathname);
    curl_setopt_array($handle, [
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS => file_get_contents($file),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $token,
            'Content-Type: ' . $mime,
            'x-api-version: 7',
        ],
    ]);
    $response = curl_exec($handle);
    $status = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);
    curl_close($handle);
    $blob = json_decode((string) $response, true);

    if ($status < 200 || $status >= 300 || empty($blob['url'])) {
        throw new RuntimeException("Vercel Blob rechazo {$filename} (HTTP {$status}).");
    }

    $update = $pdo->prepare('UPDATE fotos SET imagen = :imagen, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
    $update->execute([':imagen' => $blob['url'], ':id' => $row['id']]);
    $uploaded++;
    fwrite(STDOUT, "Subida: {$filename}\n");
}

fwrite(STDOUT, "Completado. Subidas: {$uploaded}; omitidas: {$skipped}.\n");
