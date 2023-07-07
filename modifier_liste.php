<?php
session_start();

// Si la session n'est pas présente, rediriger vers la page de connexion
if (!isset($_SESSION["id"])) {
  header("Location: connexion.php");
  exit();
}

// Informations de connexion à la base de données
$server = "localhost";
$user = "root";
$passwordDB = "";
$base = "cadeaux";

try {
  // Connexion à la base de données
  $db = new PDO("mysql:host=$server;dbname=$base", $user, $passwordDB);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Erreur lors de la connexion : " . $e->getMessage());
}

// Vérification si le paramètre d'ID de la liste est présent dans l'URL
if (isset($_GET["id"])) {
  $listeId = $_GET["id"];
} else {
  echo "ID de liste non spécifié.";
  exit();
}

// Requête pour récupérer les détails de la liste à modifier
$query = "SELECT * FROM listes_de_souhaits WHERE idListe = :idListe";
$statement = $db->prepare($query);
$statement->bindParam(':idListe', $listeId);
$statement->execute();
$liste = $statement->fetch(PDO::FETCH_ASSOC);

// Vérification si la liste existe
if (!$liste) {
  echo "Liste non trouvée.";
  exit();
}

// Si la méthode de requête est POST pour la modification de liste
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["list_name"]) && isset($_POST["list_description"])) {
  // Récupération des nouvelles valeurs du formulaire de modification de liste
  $newListName = $_POST["list_name"];
  $newListDescription = $_POST["list_description"];

  // Mise à jour des valeurs dans la base de données
  $updateQuery = "UPDATE listes_de_souhaits SET titre = :newListName, description = :newListDescription WHERE idListe = :idListe";
  $updateStatement = $db->prepare($updateQuery);
  $updateStatement->bindParam(':newListName', $newListName);
  $updateStatement->bindParam(':newListDescription', $newListDescription);
  $updateStatement->bindParam(':idListe', $listeId);
  $updateStatement->execute();

  // Redirection vers la page du profil avec un message de succès
  header("Location: profil.php?success=1");
  exit();

  // Affichage d'un message de confirmation dans une zone de message
  echo '<div class="message">La liste a été modifiée avec succès.</div>';

}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Modifier la liste</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 500px;
      margin: 0 auto;
      padding: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .form-group input[type="text"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    .form-group input[type="submit"] {
      background-color: #4caf50;
      color: #fff;
      border: none;
      border-radius: 3px;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Modifier la liste</h2>
    <form action="modifier_liste.php?id=<?php echo $liste['idListe']; ?>" method="POST">
      <div class="form-group">
        <label>Nouveau nom de la liste:</label>
        <input type="text" name="list_name" value="<?php echo $liste['titre']; ?>" required>
      </div>
      <div class="form-group">
        <label>Nouvelle description de la liste:</label>
        <textarea name="list_description" required><?php echo $liste['description']; ?></textarea>
      </div>
      <div class="form-group">
        <input type="submit" value="Modifier">
      </div>
    </form>
  </div>
</body>
</html>
