<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$id = (int) ($_POST['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

$photo = $galleryRepository->find($id);
if ($photo === null) {
    header('Location: index.php');
    exit();
}

try {
    if (!$galleryRepository->delete($id)) {
        header('Location: index.php');
        exit();
    }
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    $error = 'PostgreSQL no pudo eliminar la imagen.';
    require __DIR__ . '/templates/pages/error.php';
    exit();
}

$cleanupFailed = false;
try {
    (new BlobStorage())->delete((string) $photo['imagen']);
} catch (Throwable $exception) {
    $cleanupFailed = true;
    error_log('No se pudo limpiar el Blob eliminado para la foto ' . $id . ': ' . $exception->getMessage());
}

$params = ['deleted' => 1];
if ($cleanupFailed) {
    $params['cleanup'] = 'failed';
}

header('Location: ' . buildUrl('index.php', $params));
exit();
