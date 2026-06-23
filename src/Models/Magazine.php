<?php
declare(strict_types=1);

namespace BIBLIO_POO\Models;
use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Interfaces\EmpruntableInterface;

class Magazine extends Document implements EmpruntableInterface {
    private int $numeroEdition;
    private bool $disponible = true;

    public function __construct(
        string $titre,
        string $auteur,
        int $anneePublication,
        int $numeroEdition
    )
    {
        parent::__construct(
            $titre,
            $auteur,
            $anneePublication
        );

        if ($numeroEdition <= 0)
            {
                throw new \InvalidArgumentException("Numéro d'édition invalide.");
            }

            $this->numeroEdition = $numeroEdition;
    }

    public function getNumeroEdition(): int
    {
        return $this->numeroEdition;
    }

    public function emprunter(): bool
    {
        if (!$this->disponible)
            {
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
        return "Magazine";
    }

    public function afficherStatut(): string 
    {
        if ($this->disponible)
            {
                return "Disponible";
            }
            return "Emprunté";
    }

    public function __toString(): string 
    {
        return parent::_toString() . "Edition n° {$this->numeroEdition}" . " | Statut : {$this->afficherStatut()}";
    }
}

?>