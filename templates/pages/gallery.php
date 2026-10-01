<?php

declare(strict_types=1);

$pageTitle = 'Galeria Principal | ' . $config['app']['name'];
$activePage = 'gallery';
$cardPatterns = [
    'md:col-span-4 aspect-[4/5]',
    'md:col-span-5 md:mt-16 aspect-[3/4]',
    'md:col-span-3 aspect-[4/5]',
    'md:col-span-5 aspect-square',
    'md:col-span-4 aspect-[4/5]',
    'md:col-span-3 aspect-[3/4]',
];

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/nav.php';
?>
<main class="mx-auto max-w-[1600px] px-6 pb-24 pt-24 md:px-12 md:pb-10">
    <header class="mb-12">
        <h1 class="mb-4 text-4xl font-extrabold tracking-tighter md:text-6xl">
            Nueva <span class="text-tertiary">galeria dinamica</span>
        </h1>
        <p class="max-w-2xl text-lg font-light leading-relaxed text-on-surface-variant">
            Explora una seleccion visual curada desde la misma base de datos de la practica 05, ahora con una interfaz renovada, busqueda y filtros utiles.
        </p>
    </header>

    <?php if ($deleted): ?>
        <section class="mb-8 rounded-2xl border border-tertiary/20 bg-tertiary/10 px-5 py-4 text-sm text-tertiary" role="status">
            La imagen fue eliminada de la galeria.
            <?php if ($cleanupFailed): ?>
                El registro se retiro, pero Blob no pudo confirmar la limpieza del archivo; revisa los logs o el panel de Vercel Blob.
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="mb-10 flex flex-col gap-4 rounded-2xl border border-outline-variant/20 bg-surface-container-low/80 p-5 md:flex-row md:items-center md:justify-between">
        <form class="flex w-full flex-col gap-3 md:max-w-3xl md:flex-row" method="get" action="index.php">
            <label class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input class="w-full rounded-xl border-none bg-surface-container px-11 py-3 text-sm text-on-surface placeholder:text-on-surface-variant/50 focus:ring-1 focus:ring-tertiary/40" type="text" name="q" value="<?= e($search) ?>" placeholder="Buscar por titulo, descripcion o nombre de archivo">
            </label>
            <select class="rounded-xl border-none bg-surface-container px-4 py-3 text-sm text-on-surface focus:ring-1 focus:ring-tertiary/40" name="format">
                <option value="all" <?= $format === 'all' ? 'selected' : '' ?>>Todos los formatos</option>
                <option value="png" <?= $format === 'png' ? 'selected' : '' ?>>PNG</option>
                <option value="jpeg" <?= $format === 'jpeg' ? 'selected' : '' ?>>JPG / JPEG</option>
            </select>
            <button class="rounded-xl bg-tertiary px-5 py-3 text-sm font-bold uppercase tracking-[0.18em] text-slate-900 transition hover:brightness-110" type="submit">
                Filtrar
            </button>
        </form>

        <a class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 text-sm font-bold uppercase tracking-[0.18em] text-on-primary transition hover:brightness-110" href="subir.php">
            <span class="material-symbols-outlined">add_a_photo</span>
            Nueva imagen
        </a>
    </section>

    <section class="mb-8 flex flex-wrap gap-2">
        <?php foreach (['all' => 'Todo', 'png' => 'PNG', 'jpeg' => 'JPEG'] as $chipValue => $chipLabel): ?>
            <a
                class="<?= $format === $chipValue ? 'bg-tertiary text-slate-900' : 'bg-surface-container-low text-on-surface-variant hover:text-on-surface' ?> rounded-full px-5 py-2 text-sm font-semibold transition"
                href="<?= e(buildUrl('index.php', ['format' => $chipValue, 'q' => $search])) ?>"
            >
                <?= e($chipLabel) ?>
            </a>
        <?php endforeach; ?>
    </section>

    <?php if ($photos === []): ?>
        <section class="rounded-3xl border border-outline-variant/20 bg-surface-container-low p-10 text-center">
            <h2 class="mb-2 text-2xl font-bold">No hay resultados</h2>
            <p class="text-on-surface-variant">Ajusta los filtros o agrega una nueva imagen a la galeria.</p>
        </section>
    <?php else: ?>
        <section class="grid grid-cols-1 items-start gap-8 md:grid-cols-12">
            <?php foreach ($photos as $index => $photo): ?>
                <?php $pattern = $cardPatterns[$index % count($cardPatterns)]; ?>
                <article class="<?= $pattern ?> group relative overflow-hidden rounded-[2rem] bg-surface-container transition hover:bg-surface-container-high">
                    <a class="block h-full w-full" href="<?= e(buildUrl('detalle.php', ['id' => $photo['id']])) ?>">
                        <img class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.02]" src="<?= e($photo['image_url']) ?>" alt="<?= e($photo['text']) ?>">
                        <div class="absolute inset-0 flex flex-col justify-end bg-gradient-to-t from-surface via-surface/30 to-transparent p-6">
                            <span class="mb-2 text-[10px] font-bold uppercase tracking-[0.3em] text-tertiary"><?= e($photo['format_label']) ?></span>
                            <h2 class="text-2xl font-bold tracking-tight"><?= e($photo['titulo']) ?></h2>
                            <p class="mt-2 line-clamp-2 text-sm text-on-surface-variant"><?= e($photo['text']) ?></p>
                        </div>
                    </a>
                    <form class="absolute right-5 top-5 z-10" action="eliminar.php" method="post" onsubmit="return confirm('¿Seguro que deseas eliminar esta imagen de la galeria?');">
                        <input type="hidden" name="id" value="<?= (int) $photo['id'] ?>">
                        <button class="inline-flex items-center gap-2 rounded-xl bg-error-container/90 px-3 py-2 text-xs font-bold uppercase tracking-[0.12em] text-error transition hover:bg-error-container" type="submit">
                            <span class="material-symbols-outlined text-base">delete</span>
                            Eliminar
                        </button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>

        <section class="mt-10 flex flex-col gap-4 border-t border-outline-variant/20 pt-6 md:flex-row md:items-center md:justify-between">
            <p class="text-sm text-on-surface-variant">
                Mostrando pagina <?= e((string) $pagination['page']) ?> de <?= e((string) $pagination['pages']) ?>.
            </p>
            <div class="flex gap-3">
                <?php if ($pagination['page'] > 1): ?>
                    <a class="rounded-xl bg-surface-container px-4 py-2 text-sm font-semibold text-on-surface transition hover:bg-surface-container-high" href="<?= e(buildUrl('index.php', ['page' => $pagination['page'] - 1, 'q' => $search, 'format' => $format])) ?>">
                        Anterior
                    </a>
                <?php endif; ?>
                <?php if ($pagination['page'] < $pagination['pages']): ?>
                    <a class="rounded-xl bg-primary px-4 py-2 text-sm font-semibold text-on-primary transition hover:brightness-110" href="<?= e(buildUrl('index.php', ['page' => $pagination['page'] + 1, 'q' => $search, 'format' => $format])) ?>">
                        Siguiente
                    </a>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
