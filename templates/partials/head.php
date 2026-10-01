<?php

declare(strict_types=1);
?>
<!DOCTYPE html>
<html class="dark" lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? $config['app']['name']) ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        surface: "#131319",
                        "surface-container": "#1f1f25",
                        "surface-container-low": "#1b1b21",
                        "surface-container-high": "#2a2930",
                        "surface-container-highest": "#34343b",
                        primary: "#c0c1ff",
                        tertiary: "#00e1ab",
                        "on-surface": "#e4e1ea",
                        "on-surface-variant": "#c7c5d4",
                        "outline-variant": "#464652",
                        "primary-container": "#2e3192",
                        "on-primary": "#1e2084",
                        error: "#ffb4ab",
                        "error-container": "#93000a",
                    },
                    fontFamily: {
                        body: ["Manrope", "sans-serif"],
                    },
                    boxShadow: {
                        glow: "0 0 40px rgba(192, 193, 255, 0.08)",
                    },
                },
            },
        };
    </script>
    <style>
        body { font-family: 'Manrope', sans-serif; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-surface text-on-surface selection:bg-tertiary/30">
<div class="fixed inset-0 -z-10 overflow-hidden pointer-events-none">
    <div class="absolute -top-[10%] -left-[10%] h-[40%] w-[40%] rounded-full bg-primary/10 blur-[120px]"></div>
    <div class="absolute top-[40%] -right-[10%] h-[50%] w-[30%] rounded-full bg-tertiary/10 blur-[140px]"></div>
</div>
