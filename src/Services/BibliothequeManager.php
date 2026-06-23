<?php

declare(strict_types=1);

namespace BIBLIO_POO\Services;

use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Models\Utilisateur;

class BibliothequeManager
{
    private array $documents = [];
    private array $utilisateurs = [];

    public function ajouterDocument(
        Document $document
    ): void {

        $this->documents[] = $document;
    }

    public function ajouterUtilisateur(
        Utilisateur $utilisateur
    ): void {

        $this->utilisateurs[] = $utilisateur;
    }

    public function getDocuments(): array
    {
        return $this->documents;
    }

    public function getUtilisateurs(): array
    {
        return $this->utilisateurs;
    }

    public function rechercherDocumentParTitre(
        string $titre
    ): ?Document {

        foreach ($this->documents as $document) {

            if (
                stripos(
                    $document->getTitre(),
                    $titre
                ) !== false
            ) {

                return $document;
            }
        }

        return null;
    }

    public function supprimerDocument(
        Document $documentASupprimer
    ): bool {

        foreach (
            $this->documents as $index => $document
        ) {

            if ($document === $documentASupprimer) {

                unset($this->documents[$index]);

                $this->documents =
                    array_values($this->documents);

                return true;
            }
        }

        return false;
    }

    public function compterDocuments(): int
    {
        return count($this->documents);
    }

    public function compterUtilisateurs(): int
    {
        return count($this->utilisateurs);
    }

    public function afficherDocumentsDisponibles(): array
    {
        $documentsDisponibles = [];

        foreach ($this->documents as $document) {

            if (
                method_exists(
                    $document,
                    'estDisponible'
                )
                &&
                $document->estDisponible()
            ) {

                $documentsDisponibles[] = $document;
            }
        }

        return $documentsDisponibles;
    }
}
