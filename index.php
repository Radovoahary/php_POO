<?php

declare(strict_types=1);

require_once 'autoload.php';
//Importation des classes
use BIBLIO_POO\Models\Livre;
use BIBLIO_POO\Models\Magazine;
use BIBLIO_POO\Models\Utilisateur;
use BIBLIO_POO\Services\BibliothequeManager;
//Initialisation du manager
$manager = new BibliothequeManager();
//Donnée d'illustration
$livre1 = new Livre(
    "Clean Code",
    "Robert Martin",
    2008,
    "9780132350884"
);

$livre2 = new Livre(
    "Design Patterns",
    "GoF",
    1994,
    "9780201633610"
);

$magazine1 = new Magazine(
    "Science & Vie",
    "Rédaction",
    2025,
    150
);

$manager->ajouterDocument($livre1);
$manager->ajouterDocument($livre2);
$manager->ajouterDocument($magazine1);
$action = $_GET['action'] ?? 'accueil';

// Variable des messages
$message = '';
//Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //Ajouter un livre
    if ($action === 'ajouter-livre') {

        try {

            $livre = new Livre(
                $_POST['titre'],
                $_POST['auteur'],
                (int) $_POST['annee'],
                $_POST['isbn']
            );

            $manager->ajouterDocument($livre);

            $message = "Livre ajouté avec succès.";

        } catch (Exception $e) {

            $message = $e->getMessage();
        }
    }
    //Ajouter un utilisateur
    if ($action === 'ajouter-utilisateur') {

        try {

            $utilisateur = new Utilisateur(
                $_POST['nom'],
                $_POST['email']
            );

            $manager->ajouterUtilisateur($utilisateur);

            $message =
                "Utilisateur ajouté avec succès.";

        } catch (Exception $e) {

            $message = $e->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bibliothèque Municipale</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <h1>Bibliothèque Municipale</h1>
    <!-- Navigation -->
    <nav>

        <a href="?action=accueil">Accueil</a>

        <a href="?action=liste">
            Liste des documents
        </a>

        <a href="?action=ajouter-livre">
            Ajouter un livre
        </a>

        <a href="?action=ajouter-utilisateur">
            Ajouter un utilisateur
        </a>

    </nav>

    <hr>

    <!-- Message -->

    <?php if ($message !== '') : ?>
        <p>
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
    <?php
    switch ($action) {
        //Page d'acceuil
        case 'accueil':
            ?>
            <h2>Accueil</h2>
            <p>
                Bienvenue dans le système
                de gestion de bibliothèque.
            </p>
            <p>
                Nombre total de documents :
                <?= $manager->compterDocuments(); ?>
            </p>
            <?php

            break;
            //Liste des documents
        case 'liste':
            ?>
            <h2>Liste des documents</h2>
            <?php
            $documents = $manager->getDocuments();

            //Verfication si vide
            if (count($documents) === 0) {

                ?>

                <p>Aucun document.</p>

                <?php

            } else {

                ?>
                <ul>
                    <?php
                    foreach ($documents as $document) :
                        ?>
                        <li>
                            <?= htmlspecialchars(
                                (string) $document
                            ) ?>
                        </li>
                        <?php
                    endforeach;
                    ?>
                </ul>
                <?php
            }
            break;
        case 'ajouter-livre':
            ?>
            <h2>Ajouter un livre</h2>
            <form method="POST">
                <label>
                    Titre :
                </label>
                <input
                    type="text"
                    name="titre"
                    required
                >
                <br><br>
                <label>
                    Auteur :
                </label>
                <input
                    type="text"
                    name="auteur"
                    required
                >
                <br><br>
                <label>
                    Année :
                </label>
                <input
                    type="number"
                    name="annee"
                    required
                >
                <br><br>
                <label>
                    ISBN :
                </label>
                <input
                    type="text"
                    name="isbn"
                    required
                >
                <br><br>
                <button type="submit">
                    Ajouter
                </button>
            </form>
            <?php
            break;
        case 'ajouter-utilisateur':
            ?>
            <h2>
                Ajouter un utilisateur
            </h2>
            <form method="POST">
                <label>
                    Nom :
                </label>
                <input
                    type="text"
                    name="nom"
                    required
                >
                <br><br>
                <label>
                    Email :
                </label>
                <input
                    type="email"
                    name="email"
                    required
                >
                <br><br>
                <button type="submit">
                    Ajouter
                </button>
            </form>
            <?php
            break;
        default:
            ?>
            <h2>Erreur</h2>
            <p>
                Action inconnue.
            </p>
            <?php
    }
    ?>
</body>
</html>