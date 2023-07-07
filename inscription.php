<?php
$server = "localhost";
$user = "root";
$passwordDB = "";
$base = "cadeaux";

try {
  // Connexion à la base de données avec PDO
  $db = new PDO("mysql:host=$server;dbname=$base", $user, $passwordDB);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération et validation des données du formulaire
    $nom = isset($_POST['nom']) ? filter_var($_POST['nom'], FILTER_SANITIZE_STRING) : '';
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $passwordForm = isset($_POST['password']) ? $_POST['password'] : '';
    $passwordConfirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';

    if ($passwordForm !== $passwordConfirm) {
      die("Les mots de passe ne correspondent pas.");
    }

    // Vérification si l'e-mail est déjà utilisé
    $query = "SELECT COUNT(*) AS count FROM utilisateurs WHERE email = ?";
    $statement = $db->prepare($query);
    $statement->execute([$email]);
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result['count'] > 0) {
      echo "<script>alert('Cette adresse e-mail est déjà utilisée. Veuillez en choisir une autre.');</script>";
    } else {
      // Hachage du mot de passe
      $hashedPassword = password_hash($passwordForm, PASSWORD_DEFAULT);

      // Requête d'insertion des données dans la table utilisateurs
      $query = "INSERT INTO utilisateurs (nom, email, mot_de_passe, avatar) VALUES (?, ?, ?, ?)";
      $statement = $db->prepare($query);

      // Vérification et téléchargement de l'image de l'avatar
      $avatar = '';
      if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarName = $_FILES['avatar']['name'];
        $avatarTmpName = $_FILES['avatar']['tmp_name'];
        $avatarPath = "avatars/" . $avatarName;
        move_uploaded_file($avatarTmpName, $avatarPath);
        $avatar = $avatarPath;
      }

      $statement->execute([$nom, $email, $hashedPassword, $avatar]);

      echo "Utilisateur enregistré avec succès.";
    }
  }
} catch(PDOException $e) {
  die("Erreur lors de l'enregistrement de l'utilisateur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Formulaire d'Inscription</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
    }
    
    .container {
      max-width: 400px;
      margin: 0 auto;
      padding: 20px;
      background-color: #fff;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .container h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    
    .container input[type="text"],
    .container input[type="email"],
    .container input[type="password"] {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
    }
    
    .container input[type="file"] {
      margin-bottom: 10px;
    }
    
    .container input[type="submit"] {
      width: 100%;
      padding: 10px;
      background-color: #4caf50;
      color: #fff;
      border: none;
      border-radius: 3px;
      cursor: pointer;
    }
    
    .container input[type="submit"]:hover {
      background-color: #45a049;
    }
    
    .container .login-link {
      text-align: center;
      margin-top: 20px;
    }
    
    .container .login-link a {
      color: #999;
      text-decoration: none;
    }
    
    .container .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Inscription</h2>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="text" name="nom" placeholder="Nom" required>
      <input type="email" name="email" placeholder="Adresse email" required>
      <input type="password" name="password" placeholder="Mot de passe" required>
      <input type="password" name="password_confirm" placeholder="Vérification du mot de passe" required>
      <input type="file" name="avatar" accept="image/*">
      <input type="submit" value="S'inscrire">
    </form>
    <div class="login-link">
      <a href="connexion.php">Se connecter</a>
    </div>
  </div>
</body>
</html>
