<?php

declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    // Le namespace utilisé dans vos fichiers PHP
    $prefixe = 'BIBLIO_POO\\';
    
    // On cible le dossier src/ de manière relative et propre
    $baseDir = __DIR__ . '/src/';

    // On vérifie si la classe commence bien par le namespace BIBLIO_POO\
    if (!str_starts_with($classe, $prefixe)) {
        return;
    }

    // On isole le reste du chemin (ex: Services\BibliothequeManager)
    $relatif = substr($classe, strlen($prefixe));

    // On remplace les anti-slashes par des slashes standards
    $fichier = $baseDir . str_replace('\\', '/', $relatif) . '.php';

    // On charge le fichier s'il existe enfin !
    if (file_exists($fichier)) {
        require_once $fichier;
    }
});