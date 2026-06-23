<?php
declare(strict_types=1);
namespace BIBLIO_POO\Models;

use BIBLIO_POO\Interfaces\EmpruntableInterface;
use BIBLIO_POO\Traits\HorotadageTrait;

class Utilisateur 
{
    use HorotadageTrait;
    
    private string $nom;
    private string $email;

    // CORRIGÉ : On l'accorde au pluriel partout pour être cohérent
    private array $documentsEmpruntes = [];

    // CORRIGÉ : __construct avec deux underscores
    public function __construct(string $nom, string $email)
    {
        if (trim($nom) === '') {
            throw new \InvalidArgumentException("Nom invalide.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException("Adresse email invalide.");
        }
        
        $this->nom = $nom;
        $this->email = $email;
        
        // Initialisation du trait
        $this->initialiserHorotadage();
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function emprunterDocument(EmpruntableInterface $document): bool
    {
        // Tenter l'emprunt    
        if (!$document->emprunter()) {
            return false;
        }
        
        $this->documentsEmpruntes[] = $document;

        // Mise à jour de la date de modification
        $this->mettreAJourModification();

        return true;
    }

    public function retournerDocument(EmpruntableInterface $document): void
    {
        $document->retourner();

        foreach ($this->documentsEmpruntes as $index => $doc) {
            if ($doc === $document) {
                // Supprimer l'élément du tableau        
                unset($this->documentsEmpruntes[$index]);
            }
        }

        // Réindexer le tableau proprement
        $this->documentsEmpruntes = array_values($this->documentsEmpruntes);
        $this->mettreAJourModification();
    }

    public function getDocumentsEmpruntes(): array
    {
        return $this->documentsEmpruntes;
    }

    public function compterDocumentsEmpruntes(): int
    {
        return count($this->documentsEmpruntes);
    }

    // CORRIGÉ : __toString avec deux underscores
    public function __toString(): string
    {
        return "Nom: {$this->nom} | Email : {$this->email} | Documents empruntés : " . $this->compterDocumentsEmpruntes();
    }
}
