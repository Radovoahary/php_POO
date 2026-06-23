<?php
declare(strict_types=1);

namespace Biblio\Models;

use BIBLIO_POO\Interfaces\Empruntable;
use BIBLIO_POO\Services\Database;
use InvalidArgumentException;
use RuntimeException;

/**
 * Une BD est un Document empruntable, mais NON réservable.
 *
 * Pédagogie : illustre l'ISP — la BD n'implémente que l'interface qu'elle
 * peut réellement honorer.
 */
class BD extends Document implements Empruntable
{
    public function __construct(
        int $id,
        string $nom,
        string $auteur,
        string $dateAjout,
        protected string $dessinateur,
        protected int $tome
    ) {
        parent::__construct($id, $nom, $auteur, $dateAjout);
        if (trim($dessinateur) === '') {
            throw new InvalidArgumentException('Le dessinateur est obligatoire.');
        }
        if ($tome < 1) {
            throw new InvalidArgumentException("Tome invalide : {$tome}");
        }
    }

    public function getType(): string { return 'bd'; }

    public function getDessinateur(): string { return $this->dessinateur; }
    public function getTome(): int           { return $this->tome; }

    public function getResume(): string
    {
        return "{$this->nom} — {$this->auteur} / {$this->dessinateur} — tome {$this->tome}";
    }

    public function emprunter(Membre $membre): void
    {
        if (!$this->estDisponible()) {
            throw new RuntimeException("La BD « {$this->nom} » est déjà empruntée.");
        }
        Database::get()->inserer('emprunts', [
            'document_id' => $this->id,
            'membre_id'   => $membre->getId(),
            'date_emprunt'=> date('Y-m-d'),
            'date_retour' => null,
            'reserve'     => 0,
        ]);
        $this->log("Emprunt par {$membre} (BD #{$this->id})");
    }

    public function retourner(): void
    {
        $emprunt = $this->trouverEmpruntActif();
        if ($emprunt === null) {
            throw new RuntimeException("La BD « {$this->nom} » n'est pas empruntée.");
        }
        Database::get()->mettreAJour('emprunts', (int) $emprunt['id'], [
            'date_retour' => date('Y-m-d'),
        ]);
        $this->log("Retour (BD #{$this->id})");
    }

    public function estDisponible(): bool
    {
        return $this->trouverEmpruntActif() === null;
    }

    public function getEmprunteur(): ?Membre
    {
        $emprunt = $this->trouverEmpruntActif();
        if ($emprunt === null) return null;
        $row = Database::get()->lireParId('membres', (int) $emprunt['membre_id']);
        if ($row === null) return null;
        return new Membre(
            (int) $row['id'], $row['nom'], $row['prenom'],
            $row['email'], $row['date_inscription']
        );
    }

    /** @return array<string,mixed>|null */
    private function trouverEmpruntActif(): ?array
    {
        foreach (Database::get()->lireTous('emprunts') as $emp) {
            if ((int) $emp['document_id'] === $this->id
                && $emp['date_retour'] === null
                && (int) $emp['reserve'] === 0) {
                return $emp;
            }
        }
        return null;
    }
}