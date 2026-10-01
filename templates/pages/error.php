<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error de eliminacion</title>
</head>
<body style="font-family: Arial, sans-serif; background: #111827; color: #f9fafb; padding: 40px;">
    <h1>Error de eliminacion</h1>
    <p><?= e($error ?? 'No se pudo completar la eliminacion.') ?></p>
    <p><a style="color: #00e1ab;" href="index.php">Volver a la galeria</a></p>
</body>
</html>
