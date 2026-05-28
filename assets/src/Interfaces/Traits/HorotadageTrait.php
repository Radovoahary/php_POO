<?php
declare(strict_type=1);
namespace BIBLIO_POO\Traits;

trait HorotadatageTrait
{
    protected string $dateCreation;
    protected ?string $dateModification = null;
    
    //Initialisation des dates
    public function initialiserHorotadage(): void
    {
        $this->dateCreation = date('Y-m-d H:i:s');
        
    }
}

?>