<?php

declare(strict_types=1);

final class ImageInspector
{
    public function __construct(
        private string $imagesDirectory,
        private string $imagesUrl
    ) {
    }

    public function enrich(array $photo): array
    {
        $filename = $photo['imagen'];
        $isRemote = filter_var($filename, FILTER_VALIDATE_URL) !== false;
        $path = $isRemote ? '' : $this->imagesDirectory . '/' . $filename;
        $size = is_file($path) ? @filesize($path) : false;
        $dimensions = is_file($path) ? @getimagesize($path) : false;
        $extension = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));

        $photo['image_url'] = $isRemote ? $filename : $this->imagesUrl . '/' . rawurlencode($filename);
        $photo['format_label'] = in_array($extension, ['jpg', 'jpeg'], true) ? 'JPEG' : strtoupper($extension ?: 'N/A');
        $photo['size_label'] = $size ? $this->formatBytes((int) $size) : 'No disponible';
        $photo['dimensions_label'] = $dimensions ? $dimensions[0] . ' x ' . $dimensions[1] . ' px' : 'No disponible';
        $photo['file_name'] = $filename;
        $photo['image_error'] = $isRemote || is_file($path) ? null : 'Imagen no encontrada en el almacenamiento configurado.';

        return $photo;
    }

    public function enrichMany(array $photos): array
    {
        return array_map(fn (array $photo): array => $this->enrich($photo), $photos);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        if ($bytes < 1048576) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return number_format($bytes / 1048576, 2) . ' MB';
    }
}
