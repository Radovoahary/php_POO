<?php
declare(strict_type=1);

namespace BIBLIO_POO\Models;
use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Interfaces\EmpruntableInterface;

class Livre extends Document implements EmpruntableInterface {
    //Propriété spécifique du livre    
    private string $isbn;
    private bool $disponible = true;

    public function _construct(string $titre, string $auteur, int $anneePublication, string $isbn)
    {
    //Appel du constructeur parent    
    parent::_construct($titre, $auteur, $anneePublication);
    if (trim($isbn) === '')
        {
            throw new \InvalidArgumentException("ISBN invalide");
        }
        $this->isbn = $isbn;
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    //Method imposé par l'interface
    public function emprunter(): bool
    {
        
    }

}

?>