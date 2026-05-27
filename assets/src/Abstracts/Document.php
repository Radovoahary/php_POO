<?php
declare(strict_types=1);
namespace assets\src\abstracts;

abstract class Document 
{
    protected string $titre;
    protected string $auteur;
    protected string $anneePulication;

    protected static int $nombreDocuments = 0;
    
    public function _construct(
        string $titre,
        string $auteur,
        int $anneePulication
    ) {
        if (trim($titre) === '')
            {
                throw new \InvalidArgumentException("Le titre ne peut pas être vide.");
            }

            if (trim($auteur) === '')
                {
                    throw new \InvalidArgumentException("L'auteur ne peut pas être vide");
                }
                if ($anneePulication < 0)
                    {
                        throw new \InvalidArgumentException("Année de publication invalide");
                    }

        $this->$titre = $titre;
        $this->$auteur = $auteur;
        $this->$anneePulication = $anneePulication;
        //Incrémentation de la propriété statique de notre code
        self::$nombreDocuments++;

    }

    public function getTitre(): string {
        return $this->$titre;
    }

    public function getAuteur(): string {
        return $this->$auteur;
    }

    public function getAnneePublication(): int {
        return $this->$anneePulication;
    }

    public function setTitre(string $titre): void
    {
        if (trim($titre) === '')
            {
                throw new \InvalidArgumentException("Titre invalilde.");
            }
            $this->titre = $titre;
    }

    //Methode statique
    public static function getNombreDocuments(): int
    {
        return self::$nombreDocuments;
    }

    //Methode abstraite pour les classes enfants
    abstract public function getTypeDocument(): string;

    public function _toString(): string 
    {
        return "Titre : ";
    }
}
?>