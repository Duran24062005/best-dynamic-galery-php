<?php

declare(strict_types=1);

require __DIR__ . '/../src/GalleryRepository.php';
require __DIR__ . '/../src/BlobStorage.php';

$assertions = 0;

function expect(bool $condition, string $message): void
{
    global $assertions;

    if (!$condition) {
        throw new RuntimeException('Fallo: ' . $message);
    }

    $assertions++;
}

final class BestGalleryTestPDO extends PDO
{
    public string $query = '';
    public int $affectedRows = 1;

    public function __construct()
    {
    }

    public function prepare($query, $options = []): PDOStatement|false
    {
        $this->query = (string) $query;
        $statement = new BestGalleryTestStatement();
        $statement->affectedRows = $this->affectedRows;

        return $statement;
    }
}

final class BestGalleryTestStatement extends PDOStatement
{
    public int $affectedRows = 1;

    public function execute(?array $params = null): bool
    {
        return true;
    }

    public function rowCount(): int
    {
        return $this->affectedRows;
    }
}

$database = new BestGalleryTestPDO();
$repository = new GalleryRepository($database);
expect($repository->delete(42), 'GalleryRepository::delete devuelve true cuando elimina un registro');
expect($database->query === 'DELETE FROM fotos WHERE id = :id', 'GalleryRepository::delete usa DELETE parametrizado');

$database->affectedRows = 0;
expect(!$repository->delete(42), 'GalleryRepository::delete devuelve false cuando no existe el registro');

putenv('DYNAMIC_GALERY_STORE_ID=store_gallery');
putenv('DYNAMIC_GALERY_READ_WRITE_TOKEN=vercel_blob_rw_fake');
$requests = [];
$storage = new BlobStorage(static function (string $url, array $headers, string $payload) use (&$requests): int {
    $requests[] = compact('url', 'headers', 'payload');

    return 204;
});

expect($storage->isManagedBlobUrl('https://gallery.public.blob.vercel-storage.com/gallery/photo.png'), 'reconoce URLs Blob administradas');
expect(!$storage->isManagedBlobUrl('https://example.com/gallery/photo.png'), 'rechaza URLs externas');
$storage->delete('https://gallery.public.blob.vercel-storage.com/gallery/photo.png');
expect(count($requests) === 1, 'envia una solicitud para eliminar un Blob remoto');
expect($requests[0]['url'] === 'https://vercel.com/api/blob/delete', 'usa el endpoint oficial de eliminacion');
expect($requests[0]['payload'] === json_encode(['urls' => ['https://gallery.public.blob.vercel-storage.com/gallery/photo.png']]), 'envia la URL en el payload esperado');
expect(in_array('x-api-version: 12', $requests[0]['headers'], true), 'envia la version de API de Blob');

$storage->delete('10.png');
expect(count($requests) === 1, 'no intenta borrar assets locales');

$failingStorage = new BlobStorage(static fn (): int => 500);
$failed = false;
try {
    $failingStorage->delete('https://gallery.public.blob.vercel-storage.com/gallery/photo.png');
} catch (RuntimeException $exception) {
    $failed = str_contains($exception->getMessage(), 'HTTP 500');
}
expect($failed, 'propaga un error cuando Blob rechaza el borrado');

$galleryView = file_get_contents(__DIR__ . '/../templates/pages/gallery.php');
$detailView = file_get_contents(__DIR__ . '/../templates/pages/detail.php');
expect(is_string($galleryView) && str_contains($galleryView, 'action="eliminar.php"'), 'el listado expone la accion de eliminar');
expect(is_string($detailView) && str_contains($detailView, 'method="post"'), 'el detalle usa POST para eliminar');

echo "OK: {$assertions} assertions\n";
