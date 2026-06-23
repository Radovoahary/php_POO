<?php
declare(strict_types=1);
namespace BIBLIO_POO\Traits;

trait HorotadageTrait
{
    protected string $dateCreation;
    protected ?string $dateModification = null;
    
    //Initialisation des dates
    public function initialiserHorotadage(): void
    {
        $this->dateCreation = date('Y-m-d H:i:s');
    }

    public function mettreAJourModification(): void
    {
        $this->dateModification = date('Y-m-d H:i:s');
    }

    public function getDateCreation(): string
    {
        return $this->dateCreation;
    }

    public function getDateModification(): ?string
    {
        return $this->dateModification;
    }
}

?>