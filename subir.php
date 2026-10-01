<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$errors = [];
$latestPhoto = $galleryRepository->latest(1);
$previewPhoto = $latestPhoto !== []
    ? $imageInspector->enrich($latestPhoto[0])
    : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim((string) ($_POST['title'] ?? ''));
    $description = trim((string) ($_POST['description'] ?? ''));
    $file = $_FILES['photo'] ?? null;

    if ($title === '') {
        $errors[] = 'El titulo es obligatorio.';
    }

    if ($description === '') {
        $errors[] = 'La descripcion es obligatoria.';
    }

    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $errors[] = 'Debes seleccionar una imagen valida.';
    }

    $info = ($file && isset($file['tmp_name']))
        ? @getimagesize($file['tmp_name'])
        : false;
    $mime = $info['mime'] ?? '';
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png'];

    if ($errors === [] && !isset($allowed[$mime])) {
        $errors[] = 'Solo se permiten imagenes JPG, JPEG o PNG.';
    }

    if ($errors === [] && ($file['size'] ?? 0) > $config['max_upload_bytes']) {
        $errors[] = 'La imagen supera el limite de 5 MB.';
    }

    if ($errors === []) {
        $token = getenv('DYNAMIC_GALERY_READ_WRITE_TOKEN')
            ?: getenv('BLOB_READ_WRITE_TOKEN')
            ?: '';

        if ($token === '') {
            $errors[] = 'Falta DYNAMIC_GALERY_READ_WRITE_TOKEN. Configura el token del Blob Store en Vercel.';
        }
    }

    if ($errors === []) {
        $pathname = 'gallery/' . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
        $body = file_get_contents($file['tmp_name']);
        $ch = curl_init('https://blob.vercel-storage.com/' . $pathname);

        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => 'PUT',
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $token,
                'Content-Type: ' . $mime,
                'x-api-version: 7',
            ],
        ]);

        $response = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $blob = json_decode((string) $response, true);

        if ($status < 200 || $status >= 300 || empty($blob['url'])) {
            $errors[] = 'Vercel Blob rechazo la imagen (HTTP ' . $status . '). No se guardaron metadatos.';
        } else {
            try {
                $newId = $galleryRepository->create($title, $blob['url'], $description);
                header('Location: detalle.php?id=' . $newId);
                exit;
            } catch (Throwable $exception) {
                error_log($exception->getMessage());
                $errors[] = 'La imagen se subio a Blob pero no se pudieron guardar sus metadatos en PostgreSQL. URL: ' . $blob['url'];
            }
        }
    }
}

View::render(__DIR__ . '/templates/pages/upload.php', [
    'config' => $config,
    'errors' => $errors,
    'previewPhoto' => $previewPhoto,
]);
