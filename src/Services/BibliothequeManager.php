<?php

declare(strict_types=1);

namespace BIBLIO_POO\Services;

use BIBLIO_POO\Abstracts\Document;
use BIBLIO_POO\Models\Livre;
use BIBLIO_POO\Models\Magazine;
use BIBLIO_POO\Models\Utilisateur;
use PDO;

class BibliothequeManager
{
    private PDO $db;

    public function __construct()
    {
        // Connexion au fichier SQLite localisé à la racine du projet
        $this->db = new PDO('sqlite:' . __DIR__ . '/../../data.sqlite');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // On s'assure que les tables existent dès le départ
        $this->initialiserBaseDeDonnees();
    }

    /**
     * Crée automatiquement les tables dans le fichier data.sqlite si elles n'existent pas
     */
    private function initialiserBaseDeDonnees(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS documents (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                type TEXT NOT NULL, -- 'Livre' ou 'Magazine'
                titre TEXT NOT NULL,
                auteur TEXT NOT NULL,
                annee_publication INTEGER NOT NULL,
                isbn TEXT NULL,       -- Uniquement pour Livre
                numero INTEGER NULL   -- Uniquement pour Magazine
            );
            
            CREATE TABLE IF NOT EXISTS utilisateurs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nom TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                date_creation TEXT NOT NULL,
                date_modification TEXT NULL
            );
        ");
    }

    /**
     * Ajoute un Document (Livre ou Magazine) dans la base de données
     */
    public function ajouterDocument(Document $document): void 
    {
        $type = $document->getTypeDocument(); // Récupère 'Livre' ou 'Magazine'
        
        $sql = "INSERT INTO documents (type, titre, auteur, annee_publication, isbn, numero) 
                VALUES (:type, :titre, :auteur, :annee_publication, :isbn, :numero)";
                
        $stmt = $this->db->prepare($sql);
        
        // Détection dynamique des attributs spécifiques
        $isbn = ($document instanceof Livre) ? $document->getIsbn() : null;
        $numero = ($document instanceof Magazine) ? $document->getNumero() : null;

        $stmt->execute([
            ':type' => $type,
            ':titre' => $document->getTitre(),
            ':auteur' => $document->getAuteur(),
            ':annee_publication' => $document->getAnneePublication(),
            ':isbn' => $isbn,
            ':numero' => $numero
        ]);
    }

    /**
     * Ajoute un Utilisateur dans la base de données
     */
    public function ajouterUtilisateur(Utilisateur $utilisateur): void 
    {
        $sql = "INSERT INTO utilisateurs (nom, email, date_creation, date_modification) 
                VALUES (:nom, :email, :date_creation, :date_modification)";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nom' => $utilisateur->getNom(),
            ':email' => $utilisateur->getEmail(),
            ':date_creation' => $utilisateur->getDateCreation(),
            ':date_modification' => $utilisateur->getDateModification()
        ]);
    }

    /**
     * Récupère tous les documents de la BDD et les transforme en objets PHP correspondants
     */
    public function getDocuments(): array
    {
        $documents = [];
        $stmt = $this->db->query("SELECT * FROM documents");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            if ($row['type'] === 'Livre') {
                $documents[] = new Livre($row['titre'], $row['auteur'], (int)$row['annee_publication'], $row['isbn']);
            } elseif ($row['type'] === 'Magazine') {
                $documents[] = new Magazine($row['titre'], $row['auteur'], (int)$row['annee_publication'], (int)$row['numero']);
            }
        }

        return $documents;
    }

    /**
     * Récupère tous les utilisateurs depuis la base de données
     */
    public function getUtilisateurs(): array
    {
        $utilisateurs = [];
        $stmt = $this->db->query("SELECT * FROM utilisateurs");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row) {
            $utilisateurs[] = new Utilisateur($row['nom'], $row['email']);
        }

        return $utilisateurs;
    }

    /**
     * Recherche un document en base par son titre (ou une partie du titre)
     */
    public function rechercherDocumentParTitre(string $titre): ?Document 
    {
        $sql = "SELECT * FROM documents WHERE titre LIKE :titre LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':titre' => '%' . $titre . '%']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        if ($row['type'] === 'Livre') {
            return new Livre($row['titre'], $row['auteur'], (int)$row['annee_publication'], $row['isbn']);
        } else {
            return new Magazine($row['titre'], $row['auteur'], (int)$row['annee_publication'], (int)$row['numero']);
        }
    }

    /**
     * Supprime un document de la base de données
     */
    public function supprimerDocument(Document $documentASupprimer): bool 
    {
        $sql = "DELETE FROM documents WHERE titre = :titre AND auteur = :auteur";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titre' => $documentASupprimer->getTitre(),
            ':auteur' => $documentASupprimer->getAuteur()
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Compte en temps réel le nombre de documents stockés
     */
    public function compterDocuments(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM documents")->fetchColumn();
    }

    /**
     * Compte en temps réel le nombre d'utilisateurs stockés
     */
    public function compterUtilisateurs(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
    }

    /**
     * Filtre et retourne uniquement les documents disponibles
     */
    public function afficherDocumentsDisponibles(): array
    {
        $disponibles = [];
        // On récupère d'abord tous nos objets réhydratés
        foreach ($this->getDocuments() as $document) {
            if (method_exists($document, 'estDisponible') && $document->estDisponible()) {
                $disponibles[] = $document;
            }
        }
        return $disponibles;
    }
}