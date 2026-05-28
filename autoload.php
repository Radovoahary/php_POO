<?php

declare(strict_types=1);

spl_autoload_register(function (string $classe): void {

    $prefixe = 'BIBLIO_POO\\';
    $baseDir = __DIR__ . '/src/';

    if (!str_starts_with($classe, $prefixe)) {
        return;
    }

    $relatif = substr($classe, strlen($prefixe));

    $fichier = $baseDir .
        str_replace('\\', '/', $relatif) .
        '.php';

    if (file_exists($fichier)) {
        require $fichier;
    }
});