<?php

declare(strict_types=1);

$pageTitle = 'Agregar Imagen | ' . $config['app']['name'];
$activePage = 'upload';

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/nav.php';
?>
<main class="min-h-screen px-6 pb-28 pt-28 md:px-12 lg:px-24">
    <div class="mx-auto max-w-7xl">
        <header class="mb-12">
            <h1 class="mb-3 text-4xl font-extrabold tracking-tighter md:text-5xl">Curar y publicar nuevas imagenes</h1>
            <p class="max-w-2xl text-lg font-light leading-relaxed text-on-surface-variant">
                Agrega nuevas capturas a la misma biblioteca de `galeria_practica` usando un flujo mas claro y preparado para crecer.
            </p>
        </header>

        <div class="grid grid-cols-1 items-start gap-12 lg:grid-cols-12">
            <section class="space-y-8 lg:col-span-7">
                <?php if ($errors !== []): ?>
                    <div class="rounded-2xl border border-error/20 bg-error-container/30 px-4 py-4 text-sm text-error">
                        <ul class="space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?= e($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="space-y-8" action="subir.php" method="post" enctype="multipart/form-data">
                    <div class="rounded-3xl border-2 border-dashed border-outline-variant/30 bg-surface-container-low p-10 text-center transition hover:border-tertiary/50">
                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-surface-container-highest text-tertiary">
                            <span class="material-symbols-outlined text-4xl">add_a_photo</span>
                        </div>
                        <p class="text-lg font-semibold">Selecciona una nueva imagen para la galeria</p>
                        <p class="mt-2 text-sm text-on-surface-variant">Formatos soportados: JPG, JPEG y PNG.</p>
                        <div class="mt-6">
                            <input class="mx-auto block w-full max-w-md rounded-2xl border-none bg-surface-container px-4 py-3 text-sm text-on-surface file:mr-4 file:rounded-xl file:border-0 file:bg-primary file:px-4 file:py-2 file:font-semibold file:text-on-primary hover:file:brightness-110" type="file" name="photo" accept="image/*" required>
                        </div>
                    </div>

                    <div class="rounded-3xl bg-surface-container-low p-8">
                        <div class="mb-6">
                            <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-tertiary">Metadatos</span>
                            <h2 class="mt-2 text-2xl font-bold tracking-tight">Ficha editorial</h2>
                        </div>
                        <div class="space-y-5">
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-tertiary" for="title">Titulo de la obra</label>
                                <input class="w-full rounded-2xl border-none bg-surface-container px-4 py-4 text-lg text-on-surface focus:ring-1 focus:ring-tertiary/40" id="title" name="title" placeholder="Ingresa un titulo cinematografico..." value="<?= e((string) ($_POST['title'] ?? '')) ?>" required>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.2em] text-tertiary" for="description">Descripcion</label>
                                <textarea class="min-h-[180px] w-full rounded-2xl border-none bg-surface-container px-4 py-4 text-on-surface focus:ring-1 focus:ring-tertiary/40" id="description" name="description" placeholder="Describe el encuadre, el contexto o la intencion visual..." required><?= e((string) ($_POST['description'] ?? '')) ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-4 md:flex-row">
                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-tertiary px-5 py-4 text-sm font-extrabold uppercase tracking-[0.18em] text-slate-900 transition hover:brightness-110" type="submit">
                            <span class="material-symbols-outlined">publish</span>
                            Publicar en la galeria
                        </button>
                        <a class="inline-flex w-full items-center justify-center rounded-2xl bg-surface-container-high px-5 py-4 text-sm font-bold uppercase tracking-[0.18em] text-on-surface transition hover:bg-surface-container-highest" href="index.php">
                            Cancelar
                        </a>
                    </div>
                </form>
            </section>

            <aside class="space-y-8 lg:col-span-5 lg:sticky lg:top-28">
                <div class="overflow-hidden rounded-3xl bg-surface-container shadow-2xl">
                    <?php if ($previewPhoto !== null): ?>
                        <div class="relative">
                            <img class="aspect-[4/5] w-full object-cover opacity-85" src="<?= e($previewPhoto['image_url']) ?>" alt="<?= e($previewPhoto['text']) ?>">
                            <div class="absolute inset-0 bg-gradient-to-t from-surface via-transparent to-transparent opacity-70"></div>
                            <div class="absolute bottom-6 left-6 right-6">
                                <span class="rounded-full bg-surface/80 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.3em] text-tertiary backdrop-blur-sm">Vista previa editorial</span>
                                <h3 class="mt-3 text-2xl font-bold tracking-tight"><?= e($previewPhoto['titulo']) ?></h3>
                                <p class="mt-2 line-clamp-3 text-sm text-on-surface-variant"><?= e($previewPhoto['text']) ?></p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-px bg-outline-variant/10">
                            <div class="bg-surface-container p-5">
                                <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Formato</span>
                                <span class="text-sm font-semibold"><?= e($previewPhoto['format_label']) ?></span>
                            </div>
                            <div class="bg-surface-container p-5">
                                <span class="block text-[10px] font-bold uppercase tracking-[0.2em] text-on-surface-variant">Peso</span>
                                <span class="text-sm font-semibold"><?= e($previewPhoto['size_label']) ?></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-8 text-on-surface-variant">
                            Aun no hay imagenes disponibles para mostrar una vista previa.
                        </div>
                    <?php endif; ?>
                </div>
            </aside>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../partials/footer.php'; ?>
