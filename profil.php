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

// Requête pour récupérer l'utilisateur correspondant à l'ID de la session
$query = "SELECT * FROM utilisateurs WHERE id = :userId";
$statement = $db->prepare($query);
$statement->bindParam(':userId', $_SESSION['id']);
$statement->execute();
$user = $statement->fetch(PDO::FETCH_ASSOC);

// Vérification si l'utilisateur n'est pas trouvé
if (!$user) {
  echo "Utilisateur non trouvé.";
  exit();
}

// Si la méthode de requête est POST pour la modification du mot de passe
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["old_password"]) && isset($_POST["new_password"]) && isset($_POST["confirm_password"])) {
  // Récupération des valeurs du formulaire de modification du mot de passe
  $oldPassword = $_POST["old_password"];
  $newPassword = $_POST["new_password"];
  $confirmPassword = $_POST["confirm_password"];

  // Vérification si les mots de passe correspondent
  if ($newPassword !== $confirmPassword) {
    echo "Les mots de passe ne correspondent pas.";
    exit();
  }

  // Vérification si l'utilisateur existe
  if (!is_array($user)) {
    echo "Utilisateur non trouvé.";
    exit();
  }

  // Récupération du mot de passe stocké dans la base de données
  $storedPassword = $user['mot_de_passe'];

  // Vérification de l'ancien mot de passe
  if (!password_verify($oldPassword, $storedPassword)) {
    echo "L'ancien mot de passe est incorrect.";
    exit();
  }

  // Hachage du nouveau mot de passe
  $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

  // Mise à jour du mot de passe dans la base de données
  $updateQuery = "UPDATE utilisateurs SET mot_de_passe = :newPassword WHERE id = :userId";
  $updateStatement = $db->prepare($updateQuery);
  $updateStatement->bindParam(':newPassword', $newPasswordHash);
  $updateStatement->bindParam(':userId', $_SESSION["id"]);
  $updateStatement->execute();

  echo "Le mot de passe a été modifié avec succès.";
}

// Si la méthode de requête est POST pour la création de liste
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["list_name"]) && isset($_POST["list_description"])) {
  // Récupération des valeurs du formulaire de création de liste
  $listName = $_POST["list_name"];
  $listDescription = $_POST["list_description"];
  $userId = $_SESSION["id"];

  // Insertion de la nouvelle liste dans la table "listes_de_souhaits"
  $insertQuery = "INSERT INTO listes_de_souhaits (titre, description, utilisateur_id) VALUES (:titre, :description, :utilisateur_id)";
  $insertStatement = $db->prepare($insertQuery);
  $insertStatement->bindParam(':titre', $listName);
  $insertStatement->bindParam(':description', $listDescription);
  $insertStatement->bindParam(':utilisateur_id', $userId);
  $insertStatement->execute();

  // Récupération de l'ID de la liste créée
  $listeId = $db->lastInsertId();

  // Requête pour récupérer les détails de la liste créée
  $selectQuery = "SELECT * FROM listes_de_souhaits WHERE idListe = :listeId";
  $selectStatement = $db->prepare($selectQuery);
  $selectStatement->bindParam(':listeId', $listeId);
  $selectStatement->execute();
  $liste = $selectStatement->fetch(PDO::FETCH_ASSOC);

  // Affichage des détails de la liste
  echo "<h3>Liste créée :</h3>";
  echo "<p>Nom de la liste : " . $liste['titre'] . "</p>";
  echo "<p>Description de la liste : " . $liste['description'] . "</p>";
  // ... autres informations de la liste

  // Après l'affichage des détails de la liste
  // ...

  echo "<a href='modifier_liste.php?id=" . $liste['idListe'] . "'>Modifier la liste</a>";

  // Affichage d'un message de confirmation
  echo "La liste a été créée avec succès.";
}


?>

<!DOCTYPE html>
<html>
<head>
  <title>Espace personnel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 500px;
      margin: 0 auto;
      padding: 20px;
    }
    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .logout-btn {
      background-color: #f44336;
      color: #fff;
      border: none;
      border-radius: 3px;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
    }
    .user-info {
      display: flex;
      flex-direction: column;
      margin-bottom: 20px;
    }
    .user-info label {
      margin: 5px;
      font-weight: bold;
      margin-left: 0px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    .form-group input[type="password"] {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
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
    .avatar {
      width: 100px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 5px;
    }
    .modal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }
    .modal-content {
      background-color: #fefefe;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
    .list-container {
      margin-top: 20px;
    }
    .list-item {
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    .view-list-btn {
      background-color: #2196f3;
      color: #fff;
      border: none;
      border-radius: 3px;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
      margin-top: 10px;
    }
    .create-list-btn {
      background-color: #4caf50;
      color: #fff;
      border: none;
      border-radius: 3px;
      padding: 10px 15px;
      font-size: 14px;
      cursor: pointer;
      margin-top: 10px;
      margin-right: 10px;
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="header">
      <h2>Espace personnel</h2>
      <a href="deconnexion.php" class="logout-btn">Déconnexion</a>
    </div>

    <div class="user-info">
      <img class="avatar" src="<?php echo $user['avatar']; ?>" alt="Avatar">
      <label>Nom d'utilisateur:</label>
      <p><?php echo $user['nom']; ?></p>
      <label>Email:</label>
      <p><?php echo $user['email']; ?></p>
    </div>

    <h3>Modifier le mot de passe</h3>
    <form action="profil.php" method="POST" class="password-form">
      <div class="form-group">
        <label>Mot de passe actuel:</label>
        <input type="password" name="old_password" required>
      </div>
      <div class="form-group">
        <label>Nouveau mot de passe:</label>
        <input type="password" name="new_password" required>
      </div>
      <div class="form-group">
        <label>Confirmer le nouveau mot de passe:</label>
        <input type="password" name="confirm_password" required>
      </div>
      <div class="form-group">
        <input type="submit" value="Modifier le mot de passe">
      </div>
    </form>
    
    <h3>Créer une liste</h3>
    <button onclick="openModal()" class="create-list-btn">Créer une liste</button>
    
    <?php
    // Vérification si la liste existe pour afficher le bouton "Voir la liste"
    if ($liste) {
      echo '<a href="modifier_liste.php?id=' . $liste['idListe'] . '" class="view-list-btn">Voir la liste</a>';
    }
    ?>
    
    <div id="myModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h3>Créer une nouvelle liste</h3>
        <!-- Formulaire de création de liste -->
      </div>
    </div>

  </div>

  <script>
    // Fonction pour ouvrir la modal
    function openModal() {
      document.getElementById("myModal").style.display = "block";
    }

    // Fonction pour fermer la modal
    function closeModal() {
      document.getElementById("myModal").style.display = "none";
    }
  </script>
</body>
</html>
