<?php

declare(strict_types=1);

$config = require __DIR__ . '/config/app.php';

require __DIR__ . '/src/Database.php';
require __DIR__ . '/src/GalleryRepository.php';
require __DIR__ . '/src/BlobStorage.php';
require __DIR__ . '/src/ImageInspector.php';
require __DIR__ . '/src/View.php';

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function buildUrl(string $script, array $params = []): string
{
    $filtered = array_filter(
        $params,
        static fn ($value) => $value !== null && $value !== ''
    );

    if ($filtered === []) {
        return $script;
    }

    return $script . '?' . http_build_query($filtered);
}

try {
    $pdo = Database::connect($config['db']);
} catch (PDOException $exception) {
    http_response_code(500);
    echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Error de base de datos</title></head><body style="font-family: Arial, sans-serif; background:#111827; color:#f9fafb; padding:40px;"><h1>Error de conexion a la base de datos</h1><p>No se pudo establecer conexion con la base configurada para la galeria.</p><pre style="white-space:pre-wrap; background:#1f2937; padding:16px; border-radius:8px;">' . e($exception->getMessage()) . '</pre></body></html>';
    exit();
}

$galleryRepository = new GalleryRepository($pdo);
$imageInspector = new ImageInspector(
    $config['paths']['images_dir'],
    $config['paths']['images_url']
);
