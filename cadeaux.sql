-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 07 juil. 2023 à 09:51
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `cadeaux`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `idArticle` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(45) NOT NULL,
  `description` varchar(128) NOT NULL,
  `lien_achat` int NOT NULL,
  `listes_de_souhaits_id` int DEFAULT NULL,
  PRIMARY KEY (`idArticle`),
  KEY `listes_de_souhaits_id` (`listes_de_souhaits_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
--

DROP TABLE IF EXISTS `commentaires`;
CREATE TABLE IF NOT EXISTS `commentaires` (
  `idCommentaire` int NOT NULL AUTO_INCREMENT,
  `contenu` varchar(128) NOT NULL,
  `date_com` date NOT NULL,
  `listes_de_souhaits_id` int DEFAULT NULL,
  PRIMARY KEY (`idCommentaire`),
  KEY `listes_de_souhaits_id` (`listes_de_souhaits_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `listes_de_souhaits`
--

DROP TABLE IF EXISTS `listes_de_souhaits`;
CREATE TABLE IF NOT EXISTS `listes_de_souhaits` (
  `idListe` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(45) NOT NULL,
  `description` varchar(128) NOT NULL,
  `date` date NOT NULL,
  `utilisateur_id` int DEFAULT NULL,
  PRIMARY KEY (`idListe`),
  KEY `utilisateur_id` (`utilisateur_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `listes_de_souhaits`
--

INSERT INTO `listes_de_souhaits` (`idListe`, `titre`, `description`, `date`, `utilisateur_id`) VALUES
(13, 'toto', 'mama', '0000-00-00', 5),
(14, 'pierre', 'ben', '0000-00-00', 5);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(45) NOT NULL,
  `email` varchar(45) NOT NULL,
  `mot_de_passe` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `avatar` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `avatar`) VALUES
(6, 'pierre', 'pierre@hotmail.fr', '$2y$10$GOrrKEl54WqfvApux788g.RqUEbPZUx53s6NwZNtjdoKgdKDfPyW.', 'avatars/kratos.jpg'),
(5, 'toto', 'toto@hotmail.fr', '$2y$10$fJwefnc2v0EEU5cevkIpbeWNXMmr6u7loIL3RfndOtqY.Cmo.sfka', 'avatars/69.jfif'),
(3, 'ben', 'ben@hotmail.fr', '$2y$10$ZcK6unnMPy2CuN0N/jHwk.8x/orTcadCytPICIoakQV3OvilhNG5i', 'avatars/kratos.jpg'),
(4, 'lala', 'lala@hotmail.fr', '$2y$10$zphJFPDg9lO9A2U9Q4caWO/Gs0lJznuAkqkU.vAjpjpqM5uWDvxgO', 'avatars/A boogie.jpg');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
