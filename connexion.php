<?php
session_start();
$server = "localhost";
$user = "root";
$passwordDB = "";
$base = "cadeaux";
$message = '';

try {
  // Connexion à la base de données avec PDO
  $db = new PDO("mysql:host=$server;dbname=$base", $user, $passwordDB);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Récupération des données du formulaire
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Requête pour récupérer les informations de l'utilisateur
    $query = "SELECT * FROM utilisateurs WHERE nom = ? OR email = ?";
    $statement = $db->prepare($query);
    $statement->execute([$username, $username]);
    $user = $statement->fetch();
    // $user = $statement->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      if (password_verify($password, $user['mot_de_passe'])) {
          // Connexion réussie

          // Stockage des données du user dans les sessions
          $_SESSION["id"] = $user['id'];

          // Rediriger l'utilisateur vers profil.php avec la variable $username dans l'URL
          header("Location: profil.php?username=" . urlencode($username));
          exit();
      } else {
          // Mot de passe incorrect
          $message = "Mot de passe incorrect.";
      }
  } else {
      // Utilisateur non trouvé
      $message = "Nom d'utilisateur, email ou mot de passe incorrect.";
  }
  
  }
} catch(PDOException $e) {
  die("Erreur lors de la connexion : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Connexion</title>

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
    text-align: center; /* Nouvelle règle */
  }

  .container h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
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

  .container .signup-link {
    text-align: center;
    margin-top: 20px;
  }

  .container .signup-link a {
    color: #999;
    text-decoration: none;
  }

  .container .signup-link a:hover {
    text-decoration: underline;
  }

  .error-message {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    background-color: #f44336;
    color: #fff;
    text-align: center;
    border-radius: 5px;
    font-size: 18px;
    transition: opacity 0.2s ease-in-out;
    opacity: 1;
  }

  .hide {
    opacity: 0;
    pointer-events: none;
  }
</style>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var errorMessage = document.querySelector('.error-message');
      if (errorMessage) {
        setTimeout(function() {
          errorMessage.classList.add('hide');
        }, 3000); // Temps en millisecondes (3 secondes)
      }
    });
  </script>
</head>
<body>
<div class="container">
  <h2>Connexion</h2>
  <form action="" method="POST">
    <?php if (!empty($message)) { ?>
      <div class="error-message"><?php echo $message; ?></div>
    <?php } ?>
    <input type="text" name="username" placeholder="Nom d'utilisateur ou email" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <input type="submit" value="Se connecter">
  </form>
  <div class="signup-link">
    <a href="inscription.php">S'inscrire</a>
  </div>
</div>
</body>
</html>
