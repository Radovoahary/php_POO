<?php
declare(strict_types=1);

namespace BIBLIO_POO\Models;

use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Interfaces\EmpruntableInterface;

class Livre extends Document implements EmpruntableInterface {
    // Propriété spécifique du livre    
    private string $isbn;
    private bool $disponible = true;

    // CORRIGÉ : __construct avec deux underscores
    public function __construct(string $titre, string $auteur, int $anneePublication, string $isbn)
    {
        // CORRIGÉ : parent::__construct avec deux underscores
        parent::__construct($titre, $auteur, $anneePublication);
        
        if (trim($isbn) === '') {
            throw new \InvalidArgumentException("ISBN invalide");
        }
        $this->isbn = $isbn;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    // Méthode imposée par l'interface
    public function emprunter(): bool
    {
        // Vérification si le livre est toujours disponible    
        if (!$this->disponible) {
            return false;
        }
        $this->disponible = false;
        return true;
    }

    public function retourner(): void
    {
        $this->disponible = true;
    }

    public function estDisponible(): bool
    {
        return $this->disponible;
    }

    public function getTypeDocument(): string
    {
        return "Livre";
    }

    public function afficherStatut(): string
    {
        if ($this->disponible) {
            return "Disponible";
        }

        return "Emprunté";
    }

    // CORRIGÉ : Nom de méthode magique __toString standardisé
    public function __toString(): string
    {
        return parent::__toString() . " | ISBN : {$this->isbn} | Statut : {$this->afficherStatut()}";
    }
}