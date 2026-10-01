<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$errors = [];
$latestPhoto = $galleryRepository->latest(1);
$previewPhoto = $latestPhoto !== [] ? $imageInspector->enrich($latestPhoto[0]) : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $uploadedFile = $_FILES['photo'] ?? null;

    if ($title === '') {
        $errors[] = 'El titulo es obligatorio.';
    }

    if ($description === '') {
        $errors[] = 'La descripcion es obligatoria.';
    }

    if (!$uploadedFile || ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errors[] = 'Debes seleccionar una imagen valida.';
    }

    if ($errors === [] && @getimagesize($uploadedFile['tmp_name']) === false) {
        $errors[] = 'El archivo seleccionado no es una imagen compatible.';
    }

    if ($errors === []) {
        $safeFileName = uniqid('gallery_', true) . '.' . strtolower((string) pathinfo($uploadedFile['name'], PATHINFO_EXTENSION));
        $destination = $config['paths']['images_dir'] . '/' . $safeFileName;

        if (!move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
            $errors[] = 'No se pudo guardar la imagen en el servidor.';
        } else {
            $newId = $galleryRepository->create($title, $safeFileName, $description);
            header('Location: ' . buildUrl('detalle.php', ['id' => $newId]));
            exit();
        }
    }
}

View::render(__DIR__ . '/templates/pages/upload.php', [
    'config' => $config,
    'errors' => $errors,
    'previewPhoto' => $previewPhoto,
]);
