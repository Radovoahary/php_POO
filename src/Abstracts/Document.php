<?php

declare(strict_types=1);

namespace BIBLIO_POO\Abstracts; // <-- Corrigé pour correspondre à l'autoloader

abstract class Document 
{
    protected string $titre;
    protected string $auteur;
    protected int $anneePublication; // <-- Corrigé (publication)

    protected static int $nombreDocuments = 0;
    
    // CORRIGÉ : __construct avec deux underscores (__)
    public function __construct(
        string $titre,
        string $auteur,
        int $anneePublication
    ) {
        if (trim($titre) === '') {
            throw new \InvalidArgumentException("Le titre ne peut pas être vide.");
        }

        if (trim($auteur) === '') {
            throw new \InvalidArgumentException("L'auteur ne peut pas être vide.");
        }
        
        if ($anneePublication < 0) {
            throw new \InvalidArgumentException("Année de publication invalide.");
        }

        // CORRIGÉ : Retrait des "$" en trop après "$this->"
        $this->titre = $titre;
        $this->auteur = $auteur;
        $this->anneePublication = $anneePublication;
        
        self::$nombreDocuments++;
    }

    // CORRIGÉ : Retrait des "$" dynamiques
    public function getTitre(): string {
        return $this->titre;
    }

    public function getAuteur(): string {
        return $this->auteur;
    }

    public function getAnneePublication(): int {
        return (int) $this->anneePublication;
    }

    public function setTitre(string $titre): void
    {
        if (trim($titre) === '') {
            throw new \InvalidArgumentException("Titre invalide.");
        }
        $this->titre = $titre;
    }

    public static function getNombreDocuments(): int
    {
        return self::$nombreDocuments;
    }

    abstract public function getTypeDocument(): string;

    // CORRIGÉ : __toString avec deux underscores (__)
    public function __toString(): string 
    {
        return "Titre : {$this->titre} | Auteur : {$this->auteur} | Année : {$this->anneePublication}";
    }
}