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
                    
    }
}
?>