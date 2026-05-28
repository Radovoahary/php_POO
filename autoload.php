<?php
declare(strict_types=1);

/**
 * =====================================================================
 *  CHAPITRE 7 — Autoloader PSR-4 maison
 * =====================================================================
 *
 * NE MODIFIE PAS CE FICHIER.
 *
 * spl_autoload_register enregistre une fonction qui sera appelée
 * AUTOMATIQUEMENT par PHP chaque fois qu'on utilise une classe qui n'est
 * pas encore chargée.
 *
 * Règle PSR-4 :
 *   Biblio\Models\Livre   →   src/Models/Livre.php
 *   ^^^^^^                          (préfixe)
 *           \             →    /
 *            Models\Livre.php
 *
 * Avantage : plus aucun `require_once` dans ton code applicatif.
 */

spl_autoload_register(function (string $classe): void {
    $prefixe = 'Biblio\\';
    $baseDir = __DIR__ . '/src/';

    if (!str_starts_with($classe, $prefixe)) {
        return; // ce n'est pas une classe de notre projet
    }

    $relatif = substr($classe, strlen($prefixe));
    $fichier = $baseDir . str_replace('\\', '/', $relatif) . '.php';

    if (file_exists($fichier)) {
        require $fichier;
    }
});