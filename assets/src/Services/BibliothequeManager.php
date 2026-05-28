<?php
declare(strict_type=1);
namespace BIBLIO_POO\Services;

use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Models\Utilisateur;

class BibliothequeManager
{
    private array $document = [];
    private array $utilisateur = [];

    public function ajouterDocument(Document $document): void
    {
        $this->document[] = $document;
    }

    public function ajouterUtilisateur(Utilisateur $utilisateur): void
    {
        $this->utilisateurs[] = $utilisateur;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function getUtilisateur(): array
    {
        return $this->utilisateurs;
    }

    public function rechercherDocumentParTitre(string $titre): ?Document 
    {
        foreach($this->documents as $document)
            {
        //stripos : recherche de texte sans tenir en compte les majuscules       
        if (stripos($document->getTitre(), $titre !== false))
                    {
                        return $document;
                    }
            }
            return null;
    }

    public function suppimerDocument(Document $documentASupprimer): bool
    {
        foreach ($this->documents as $index => $document)
            {
                if ($document === $documentASupprimer)
                    {
                        unset($this->documents[$index]);
                        $this->documents = array_values($this->documents);
                        return true;
                    }
            }
            return false;
    }

    public function compterDocuments(): int
    {
        return count($this->documents);
    }

    public function compterUtilisateur(): int 
    {
        return count($this->utilisateurs);
    }

    public function afficherDocumentsDisponible(): array
    {
        $documentsDisponible = [];

        foreach ($this->documents as $document)
            {
        //method_exists vérifie si un objet possède une methode spécifique        
        if (method_exists($document, 'estDisponible') && $document->estDisponible)
                    {
                        $documentsDisponibles[] = $document;
                    }
            }

            return $documentsDisponibles;
    }
}

?>