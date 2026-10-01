<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

$errors = [];
$saved = isset($_GET['saved']) && $_GET['saved'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));

    if ($title === '') {
        $errors[] = 'El titulo es obligatorio.';
    }

    if ($description === '') {
        $errors[] = 'La descripcion es obligatoria.';
    }

    if ($errors === []) {
        $galleryRepository->updateMetadata($id, $title, $description);
        header('Location: ' . buildUrl('detalle.php', ['id' => $id, 'saved' => 1]));
        exit();
    }
}

$photo = $galleryRepository->find($id);
if ($photo === null) {
    header('Location: index.php');
    exit();
}

$photo = $imageInspector->enrich($photo);
$relatedPhotos = $imageInspector->enrichMany($galleryRepository->findRelated($id));

View::render(__DIR__ . '/templates/pages/detail.php', [
    'config' => $config,
    'photo' => $photo,
    'relatedPhotos' => $relatedPhotos,
    'errors' => $errors,
    'saved' => $saved,
]);
