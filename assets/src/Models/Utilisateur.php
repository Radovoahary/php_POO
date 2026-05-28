<?php
declare(strict_type=1);
namespace BIBLIO_POO\Models;

use BIBLIO_POO\Interfaces\EmpruntableInterface;
use BIBLIO_POO\Traits\HorotadageTrait;

class Utilisateur 
{
    use HorotadageTrait;
    private string $nom;
    private string $email;

    //Tableau pour documents empruntés
    private array $documentEmpruntes = [];
    public function _construct(string $nom,string $email)
    {
        if (trim($nom) === '')
            {
                throw new \InvalidArgumentException("Nom invalide.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                {
                    throw new \InvalidArgumentException("Adresse email invalide.");
                }
                $this->nom = $nom;
                $this->email = $email;
                //Initialisation du trait
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
    //Tenter l'emprunt    
    if ($document->emprunter())
            {
                return false;
            }
    $this->documentEmpruntes[] = $document;

    //Mise à jour la date de modification
    $this->mettreAJourModification();

    return true;
    }


    public function retournerDocument(EmpruntableInterface $document): void
    {
        $document->retourner();

        foreach ($this->documentsEmpruntes as $index => $doc)
            {
                if ($doc === $document)
                    {
                //Supprimer un élément du tableau        
                unset($this->documentEmpruntes[$index]);
                    }
            }

            $this->documentEmpruntes = array_values($this->documentsEmpruntes);
            $this->mettreAJourModification();
    }

    public function getDocumentEmpruntes(): array
    {
        return $this->documentsEmpruntes;
    }

    public function compterDocumentEmpruntes(): int
    {
        return count($this->documentsEmpruntes);
    }


    public function _toString(): string
    {
        return "Nom: {$this->nom} | " . "Email : {$this->email} | " . "Documents empruntés : " . $this->compterDocumentEmpruntes();
    }
}

?>