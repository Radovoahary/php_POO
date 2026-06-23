<?php
declare(strict_types=1);

namespace BIBLIO_POO\Services;

/**
 * Encapsule l'accès à la base SQLite.
 *
 * ATTENTION : NE MODIFIE PAS CE FICHIER.
 *
 * PDO (PHP Data Objects) est un pont standard pour parler aux bases de
 * données. Tu n'as pas besoin de comprendre PDO pour ce devoir : il te
 * suffit d'appeler les 5 méthodes publiques (lireTous, lireParId,
 * inserer, mettreAJour, supprimer).
 *
 * Cette classe est un Singleton : une seule instance dans toute
 * l'application, accessible via Database::get().
 *
 * Au premier lancement, la base est créée et seedée automatiquement
 * (fichier data.sqlite à la racine du projet).
 */
final class Database
{
    private static ?Database $instance = null;
    private \PDO $pdo;

    private function __construct()
    {
        $cheminFichier = __DIR__ . '/../../data.sqlite';
        $premiereCreation = !file_exists($cheminFichier);

        $this->pdo = new \PDO('sqlite:' . $cheminFichier);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $this->pdo->prepare('PRAGMA foreign_keys = ON')->execute();

        $this->creerTables();

        if ($premiereCreation) {
            $this->seed();
        }
    }

    public static function get(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /** @return array<int, array<string, mixed>> */
    public function lireTous(string $table): array
    {
        $this->verifierTable($table);
        $stmt = $this->pdo->query("SELECT * FROM {$table}");
        return $stmt->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function lireParId(string $table, int $id): ?array
    {
        $this->verifierTable($table);
        $stmt = $this->pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $ligne = $stmt->fetch();
        return $ligne === false ? null : $ligne;
    }

    /** @param array<string, mixed> $donnees */
    public function inserer(string $table, array $donnees): int
    {
        $this->verifierTable($table);
        $colonnes = array_keys($donnees);
        $placeholders = array_map(fn($c) => ':' . $c, $colonnes);
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $colonnes),
            implode(', ', $placeholders)
        );
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($donnees);
        return (int) $this->pdo->lastInsertId();
    }

    /** @param array<string, mixed> $donnees */
    public function mettreAJour(string $table, int $id, array $donnees): void
    {
        $this->verifierTable($table);
        if ($donnees === []) return;
        $sets = array_map(fn($c) => "{$c} = :{$c}", array_keys($donnees));
        $sql = sprintf('UPDATE %s SET %s WHERE id = :id', $table, implode(', ', $sets));
        $donnees['id'] = $id;
        $this->pdo->prepare($sql)->execute($donnees);
    }

    public function supprimer(string $table, int $id): void
    {
        $this->verifierTable($table);
        $this->pdo->prepare("DELETE FROM {$table} WHERE id = :id")->execute(['id' => $id]);
    }

    private function verifierTable(string $table): void
    {
        $autorisees = ['documents', 'membres', 'emprunts'];
        if (!in_array($table, $autorisees, true)) {
            throw new \InvalidArgumentException("Table inconnue : {$table}");
        }
    }

    private function creerTables(): void
    {
        $this->pdo->prepare(<<<SQL
            CREATE TABLE IF NOT EXISTS documents (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                type          TEXT NOT NULL,
                nom           TEXT NOT NULL,
                auteur        TEXT NOT NULL,
                date_ajout    TEXT NOT NULL,
                pages         INTEGER,
                genre         TEXT,
                dessinateur   TEXT,
                tome          INTEGER,
                numero        INTEGER,
                periodicite   TEXT
            )
            SQL)->execute();

        $this->pdo->prepare(<<<SQL
            CREATE TABLE IF NOT EXISTS membres (
                id                INTEGER PRIMARY KEY AUTOINCREMENT,
                nom               TEXT NOT NULL,
                prenom            TEXT NOT NULL,
                email             TEXT NOT NULL,
                date_inscription  TEXT NOT NULL
            )
            SQL)->execute();

        $this->pdo->prepare(<<<SQL
            CREATE TABLE IF NOT EXISTS emprunts (
                id            INTEGER PRIMARY KEY AUTOINCREMENT,
                document_id   INTEGER NOT NULL,
                membre_id     INTEGER NOT NULL,
                date_emprunt  TEXT NOT NULL,
                date_retour   TEXT,
                reserve       INTEGER NOT NULL DEFAULT 0,
                FOREIGN KEY (document_id) REFERENCES documents(id),
                FOREIGN KEY (membre_id)   REFERENCES membres(id)
            )
            SQL)->execute();
    }

    private function seed(): void
    {
        $this->inserer('documents', [
            'type' => 'livre', 'nom' => 'Germinal', 'auteur' => 'Émile Zola',
            'date_ajout' => '2025-09-01', 'pages' => 591, 'genre' => 'Roman social',
        ]);
        $this->inserer('documents', [
            'type' => 'livre', 'nom' => 'L\'Étranger', 'auteur' => 'Albert Camus',
            'date_ajout' => '2025-09-15', 'pages' => 184, 'genre' => 'Roman philosophique',
        ]);
        $this->inserer('documents', [
            'type' => 'bd', 'nom' => 'Tintin au Tibet', 'auteur' => 'Hergé',
            'date_ajout' => '2025-10-02', 'dessinateur' => 'Hergé', 'tome' => 20,
        ]);
        $this->inserer('documents', [
            'type' => 'bd', 'nom' => 'Astérix le Gaulois', 'auteur' => 'René Goscinny',
            'date_ajout' => '2025-10-12', 'dessinateur' => 'Albert Uderzo', 'tome' => 1,
        ]);
        $this->inserer('documents', [
            'type' => 'magazine', 'nom' => 'Géo', 'auteur' => 'Collectif',
            'date_ajout' => '2026-04-01', 'numero' => 512, 'periodicite' => 'mensuel',
        ]);
        $this->inserer('documents', [
            'type' => 'magazine', 'nom' => 'Science & Vie', 'auteur' => 'Collectif',
            'date_ajout' => '2026-04-10', 'numero' => 1278, 'periodicite' => 'mensuel',
        ]);

        $this->inserer('membres', [
            'nom' => 'Martin', 'prenom' => 'Alice',
            'email' => 'alice.martin@biblio.fr', 'date_inscription' => '2025-01-15',
        ]);
        $this->inserer('membres', [
            'nom' => 'Bernard', 'prenom' => 'Bruno',
            'email' => 'b.bernard@biblio.fr', 'date_inscription' => '2025-03-22',
        ]);
        $this->inserer('membres', [
            'nom' => 'Clément', 'prenom' => 'Camille',
            'email' => 'camille.c@biblio.fr', 'date_inscription' => '2025-09-05',
        ]);

        $this->inserer('emprunts', [
            'document_id' => 1, 'membre_id' => 2,
            'date_emprunt' => '2026-04-20', 'date_retour' => null, 'reserve' => 0,
        ]);
    }

    private function __clone() {}
    public function __wakeup() { throw new \RuntimeException('Singleton'); }
}