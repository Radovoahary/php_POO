<?php
declare(strict_type=1);
namespace BIBLIO_POO\Interfaces;

interface EmpruntableInterface 
{
    public function emprunter(): bool;
    public function retourner(): void;
    public function estDisponible(): bool;

}
?>