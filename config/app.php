<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Nueva Galeria Dinamica',
        'tagline' => 'Una curaduria visual construida en PHP con una base mas limpia y escalable.',
    ],
    'db' => [
        'host' => 'localhost',
        'name' => 'nueva_galeria_dinamica',
        'user' => '',
        'pass' => '',
        'charset' => 'utf8',
    ],
    'gallery' => [
        'per_page' => 9,
        'valid_formats' => ['all', 'png', 'jpeg'],
        'max_upload_bytes' => 5000000,
    ],
    'paths' => [
        'images_dir' => __DIR__ . '/../public/img',
        'images_url' => 'public/img',
    ],
];
