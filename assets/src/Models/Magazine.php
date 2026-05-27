<?php
declare(strict_type=1);

namespace BIBLIO_POO\Models;
use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Interfaces\EmpruntableInterface;

class Magazine extends Document implements EmpruntableInterface {
    private int $numeroEdition;
    private bool $disponible = true;

    public function _construct(
        string $titre,
        string $auteur,
        int $anneePublication,
        int $numeroEdition
    )
    {
        parent::_construct(
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

    public function getTypeDocument()
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

    public function _toString(): string 
    {
        return parent::_toString() . "Edition n° {$this->numeroEdition}" . " | Statut : {$this->afficherStatut()}";
    }
}

?>