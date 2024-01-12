<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

$login = $_SESSION['login'];
$nom_utilisateur = $_SESSION['prenom_utilisateur'];
$prenom_utilisateur = $_SESSION['nom_utilisateur'];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Liste des Résultats - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .action-buttons button {
            background-color: #1b1b1b;
            color: #d7c378;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #d7c378;
            color: #1b1b1b;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="../admin-sports/manage-sports.php">Gestion Sports</a></li>
                <li><a href="../admin-places/manage-places.php">Gestion Lieux</a></li>
                <li><a href="../admin-events/manage-events.php">Gestion Calendrier</a></li>
                <li><a href="../admin-countries/manage-countries.php">Gestion Pays</a></li>
                <li><a href="../admin-genres/manage-genres.php">Gestion Genres</a></li>
                <li><a href="../admin-athletes/manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="../admin-results/manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Liste des Résultats</h1>
        <div class="action-buttons">
            <button onclick="openAddResultsForm()">Ajouter un Résultat</button>
            <!-- Autres boutons... -->
        </div>
        <!-- Tableau des épreuves -->
        <?php
        require_once("../../../database/database.php");

        try {
            // Requête pour récupérer la liste des épreuves depuis la base de données
            $query = "SELECT nom_athlete, prenom_athlete, nom_pays, nom_sport, nom_epreuve, resultat
            FROM ATHLETE
            INNER JOIN PAYS ON ATHLETE.id_pays = PAYS.id_pays
            INNER JOIN PARTICIPER ON ATHLETE.id_athlete = PARTICIPER.id_athlete
            INNER JOIN EPREUVE ON PARTICIPER.id_epreuve = EPREUVE.id_epreuve
            INNER JOIN SPORT ON EPREUVE.id_sport = SPORT.id_sport
            ORDER BY nom_athlete";
            $statement = $connexion->prepare($query);
            $statement->execute();

            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table>";
                echo "<tr>
                <th>Nom Athlète</th>
                <th>Prénom Athlète</th>
                <th>Pays</th>
                <th>Sport</th>
                <th>Épreuves</th>
                <th>Résultats</th>
                <th>Modifier</th>
                <th>Supprimer</th>
                </tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['prenom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_pays']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_sport']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_epreuve']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['resultat']) . "</td>";
                    echo "<td><button onclick='openModifyResultsForm(\"{$row['resultat']}\")'>Modifier</button></td>";
                    echo "<td><button onclick='deleteResultsConfirmation(\"{$row['resultat']}\")'>Supprimer</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun résultat trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
        
        <p class="paragraph-link">
            <a class="link-home" href="../admin.php">Accueil administration</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
    <script>
        function openAddResultsForm() {
            // Rediriger vers la page d'ajout de résultat
            window.location.href = 'add-results.php';
        }

        function openModifyResultsForm(resultat) {
            console.log("Ouverture de la page de modification pour le résultat : " + resultat);
            // Rediriger vers la page de modification avec le résultat
            window.location.href = 'modify-results.php?resultat=' + encodeURIComponent(resultat);
        }

        function deleteResultsConfirmation(resultat) {
            // Afficher une fenêtre de confirmation pour supprimer un résultat
            if (confirm("Êtes-vous sûr de vouloir supprimer ce résultat?")) {
                // Rediriger vers la page de suppression avec le résultat
                window.location.href = 'delete-results.php?resultat=' + encodeURIComponent(resultat);
            }
        }
    </script>
</body>

</html>
