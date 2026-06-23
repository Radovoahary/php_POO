<?php
declare(strict_types=1);
namespace BIBLIO_POO\Interfaces;

interface EmpruntableInterface 
{
    public function emprunter(): bool;
    public function retourner(): void;
    public function estDisponible(): bool;

}
?>