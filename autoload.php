<?php

declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    // Le namespace de base de votre projet
    $prefixe = 'BIBLIO_POO\\';
    
    // Chemin absolu vers le dossier src
    $baseDir = __DIR__ . '/src/';

    // On vérifie normalement si la classe commence bien par BIBLIO_POO\
    if (!str_starts_with($classe, $prefixe)) {
        return;
    }

    // On récupère le reste (ex: Services\BibliothequeManager)
    $relatif = substr($classe, strlen($prefixe));

    // On remplace les \ par des / pour Windows/Linux et on ajoute .php
    $fichier = $baseDir . str_replace('\\', '/', $relatif) . '.php';

    // On charge le fichier s'il existe
    if (file_exists($fichier)) {
        require_once $fichier;
    }
});