<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$page = max(1, (int) ($_GET['page'] ?? 1));
$search = trim((string) ($_GET['q'] ?? ''));
$format = (string) ($_GET['format'] ?? 'all');
$deleted = isset($_GET['deleted']) && $_GET['deleted'] === '1';
$cleanupFailed = isset($_GET['cleanup']) && $_GET['cleanup'] === 'failed';

if (!in_array($format, $config['gallery']['valid_formats'], true)) {
    $format = 'all';
}

$pagination = $galleryRepository->paginate($page, $config['gallery']['per_page'], $search, $format);
$photos = $imageInspector->enrichMany($pagination['items']);

View::render(__DIR__ . '/templates/pages/gallery.php', [
    'config' => $config,
    'photos' => $photos,
    'pagination' => $pagination,
    'search' => $search,
    'format' => $format,
    'deleted' => $deleted,
    'cleanupFailed' => $cleanupFailed,
]);
