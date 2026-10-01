<?php

declare(strict_types=1);
?>
<nav class="fixed top-0 z-50 flex w-full items-center justify-between bg-surface/70 px-6 py-4 shadow-glow backdrop-blur-xl md:px-8">
    <div class="flex items-center gap-8">
        <a class="text-xl font-extrabold tracking-tight text-primary" href="index.php"><?= e($config['app']['name']) ?></a>
        <div class="hidden items-center gap-6 md:flex">
            <a class="<?= $activePage === 'gallery' ? 'border-b-2 border-tertiary text-primary font-bold' : 'text-on-surface-variant hover:text-primary' ?> pb-1 transition-colors" href="index.php">Galeria</a>
            <a class="<?= $activePage === 'upload' ? 'border-b-2 border-tertiary text-primary font-bold' : 'text-on-surface-variant hover:text-primary' ?> pb-1 transition-colors" href="subir.php">Agregar</a>
        </div>
    </div>
    <div class="hidden text-sm text-on-surface-variant md:block"><?= e($config['app']['tagline']) ?></div>
</nav>
