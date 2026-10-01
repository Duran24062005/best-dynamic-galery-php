<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => 'Nueva Galeria Dinamica',
        'tagline' => 'Una curaduria visual construida en PHP con una base mas limpia y escalable.',
    ],
    'db' => [
        'host' => '127.0.0.1',
        'name' => 'nueva_galeria_dinamica',
        'user' => 'alexidg',
        'pass' => '12345',
        'charset' => 'utf8mb4',
    ],
    'gallery' => [
        'per_page' => 9,
        'valid_formats' => ['all', 'png', 'jpeg'],
    ],
    'paths' => [
        'images_dir' => __DIR__ . '/../public/img',
        'images_url' => 'public/img',
    ],
];
