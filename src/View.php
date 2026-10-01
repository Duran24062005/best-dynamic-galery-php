<?php

declare(strict_types=1);

final class View
{
    public static function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require $template;
    }
}
