<?php

declare(strict_types=1);
?>
<nav class="fixed bottom-0 left-0 z-50 flex w-full justify-around bg-surface/70 p-4 shadow-2xl backdrop-blur-xl md:hidden">
    <a class="<?= $activePage === 'gallery' ? 'bg-primary-container text-primary' : 'text-on-surface-variant' ?> rounded-lg px-4 py-1 text-center" href="index.php">
        <span class="material-symbols-outlined block">grid_view</span>
        <span class="text-[10px] uppercase tracking-[0.2em]">Galeria</span>
    </a>
    <a class="<?= $activePage === 'upload' ? 'bg-primary-container text-primary' : 'text-on-surface-variant' ?> rounded-lg px-4 py-1 text-center" href="subir.php">
        <span class="material-symbols-outlined block">add_a_photo</span>
        <span class="text-[10px] uppercase tracking-[0.2em]">Subir</span>
    </a>
</nav>
</body>
</html>
