<?php

declare(strict_types=1);

$pageTitle = 'Detalle | ' . $photo['titulo'];
$activePage = 'gallery';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/nav.php';
?>
<main class="mx-auto min-h-screen max-w-[1600px] px-4 pb-28 pt-24 md:px-8 md:pb-12">
    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
        <section class="lg:col-span-8 space-y-6">
            <div class="relative overflow-hidden rounded-3xl bg-surface-container-low shadow-2xl">
                <img class="aspect-[16/10] w-full object-cover" src="<?= e($photo['image_url']) ?>" alt="<?= e($photo['text']) ?>">
                <div class="absolute bottom-6 left-6 right-6 flex flex-wrap items-end justify-between gap-4">
                    <div class="rounded-2xl border border-outline-variant/10 bg-surface/70 p-4 backdrop-blur-xl">
                        <span class="mb-1 block text-[10px] font-bold uppercase tracking-[0.3em] text-tertiary">Visualizacion detallada</span>
                        <h1 class="text-2xl font-extrabold tracking-tight text-primary"><?= e($photo['titulo']) ?></h1>
                    </div>
                    <div class="flex gap-2">
                        <a class="rounded-2xl bg-surface/70 p-3 text-on-surface transition hover:bg-surface-container-high" href="<?= e($photo['image_url']) ?>" target="_blank" rel="noreferrer">
                            <span class="material-symbols-outlined">download</span>
                        </a>
                        <a class="rounded-2xl bg-surface/70 p-3 text-on-surface transition hover:bg-surface-container-high" href="index.php">
                            <span class="material-symbols-outlined">grid_view</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl bg-surface-container-low p-8">
                <p class="max-w-3xl text-lg leading-relaxed text-on-surface-variant"><?= e($photo['text']) ?></p>
            </div>
        </section>

        <aside class="space-y-8 lg:col-span-4">
            <div class="grid grid-cols-2 gap-px overflow-hidden rounded-3xl bg-outline-variant/10">
                <div class="bg-surface-container p-5">
                    <span class="material-symbols-outlined mb-2 text-tertiary">image</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Formato</span>
                    <span class="text-sm font-semibold"><?= e($photo['format_label']) ?></span>
                </div>
                <div class="bg-surface-container p-5">
                    <span class="material-symbols-outlined mb-2 text-tertiary">photo_size_select_large</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Dimensiones</span>
                    <span class="text-sm font-semibold"><?= e($photo['dimensions_label']) ?></span>
                </div>
                <div class="bg-surface-container p-5">
                    <span class="material-symbols-outlined mb-2 text-tertiary">database</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Archivo</span>
                    <span class="text-sm font-semibold"><?= e($photo['file_name']) ?></span>
                </div>
                <div class="bg-surface-container p-5">
                    <span class="material-symbols-outlined mb-2 text-tertiary">deployed_code</span>
                    <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Peso</span>
                    <span class="text-sm font-semibold"><?= e($photo['size_label']) ?></span>
                </div>
            </div>

            <div class="rounded-3xl border border-outline-variant/20 bg-surface-container-low p-6">
                <div class="mb-5">
                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-tertiary">Editar metadatos</span>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight">Gestion editorial</h2>
                </div>

                <?php if ($saved): ?>
                    <div class="mb-4 rounded-2xl border border-tertiary/20 bg-tertiary/10 px-4 py-3 text-sm text-tertiary">
                        Los datos de la imagen se actualizaron correctamente.
                    </div>
                <?php endif; ?>

                <?php if ($errors !== []): ?>
                    <div class="mb-4 rounded-2xl border border-error/20 bg-error-container/30 px-4 py-3 text-sm text-error">
                        <ul class="space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="space-y-4" method="post" action="<?= e(buildUrl('detalle.php', ['id' => $photo['id']])) ?>">
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-tertiary" for="title">Titulo</label>
                        <input class="w-full rounded-2xl border-none bg-surface-container px-4 py-3 text-on-surface focus:ring-1 focus:ring-tertiary/40" id="title" name="title" value="<?= e($photo['titulo']) ?>" required>
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-tertiary" for="description">Descripcion</label>
                        <textarea class="min-h-[150px] w-full rounded-2xl border-none bg-surface-container px-4 py-3 text-on-surface focus:ring-1 focus:ring-tertiary/40" id="description" name="description" required><?= e($photo['text']) ?></textarea>
                    </div>
                    <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-tertiary px-5 py-3 text-sm font-bold uppercase tracking-[0.18em] text-slate-900 transition hover:brightness-110" type="submit">
                        <span class="material-symbols-outlined">save</span>
                        Guardar cambios
                    </button>
                </form>
            </div>
        </aside>
    </div>

    <?php if ($relatedPhotos !== []): ?>
        <section class="mt-20">
            <div class="mb-8 flex items-end justify-between border-b border-outline-variant/20 pb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-tertiary">Relacionadas</span>
                    <h2 class="mt-2 text-3xl font-extrabold tracking-tight">Perspectivas cercanas</h2>
                </div>
                <a class="text-sm font-bold text-primary transition hover:text-tertiary" href="index.php">Volver a la galeria</a>
            </div>

            <div class="hide-scrollbar flex gap-6 overflow-x-auto pb-4">
                <?php foreach ($relatedPhotos as $relatedPhoto): ?>
                    <article class="min-w-[280px] overflow-hidden rounded-3xl bg-surface-container">
                        <a href="<?= e(buildUrl('detalle.php', ['id' => $relatedPhoto['id']])) ?>">
                            <img class="aspect-[4/5] w-full object-cover" src="<?= e($relatedPhoto['image_url']) ?>" alt="<?= e($relatedPhoto['text']) ?>">
                            <div class="p-5">
                                <h3 class="text-lg font-bold"><?= e($relatedPhoto['titulo']) ?></h3>
                                <p class="mt-2 line-clamp-2 text-sm text-on-surface-variant"><?= e($relatedPhoto['text']) ?></p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
