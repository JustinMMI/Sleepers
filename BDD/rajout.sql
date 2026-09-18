-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 18 sep. 2026 à 14:26
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tinder22`
--

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `idUserC1` int NOT NULL,
  `idUserC2` int NOT NULL,
  `libComment` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`idUserC1`,`idUserC2`),
  KEY `COMMENTS_FK` (`idUserC1`,`idUserC2`),
  KEY `FK_COMMENTS2` (`idUserC2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`idUserC1`, `idUserC2`, `libComment`) VALUES
(2, 8, 'pas mon délire de dormir que 2 minutes finalement'),
(2, 12, 'Trop d\'échardes, je recommande pas du tout'),
(3, 1, 'Très bonne sieste. Mouvementée'),
(4, 12, 'Pas trop kiffé les échardes mais c\'était cool.'),
(5, 1, 'ui'),
(5, 7, 'bouge trop, dommage pour un lit 0.5 place'),
(5, 9, 'Top'),
(6, 11, 'OUI'),
(9, 11, 'très bon toit'),
(9, 12, 'ah'),
(10, 11, 'bonne charpente pour dormir mais la carapace c\'est mieux'),
(10, 13, 'on respire mal'),
(14, 8, 'bloup bloup'),
(14, 11, 'BLOUP BLOUPPPP');

-- --------------------------------------------------------

--
-- Structure de la table `genre`
--

DROP TABLE IF EXISTS `genre`;
CREATE TABLE IF NOT EXISTS `genre` (
  `idGenr` int NOT NULL AUTO_INCREMENT,
  `libGenr` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`idGenr`),
  KEY `GENRE_FK` (`idGenr`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `genre`
--

INSERT INTO `genre` (`idGenr`, `libGenr`) VALUES
(1, 'Femme'),
(2, 'Homme');

-- --------------------------------------------------------

--
-- Structure de la table `likes`
--

DROP TABLE IF EXISTS `likes`;
CREATE TABLE IF NOT EXISTS `likes` (
  `idUserL1` int NOT NULL,
  `idUserL2` int NOT NULL,
  `likeL1` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`idUserL1`,`idUserL2`),
  KEY `LIKES_FK` (`idUserL1`,`idUserL2`),
  KEY `FK_LIKES2` (`idUserL2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `likes`
--

INSERT INTO `likes` (`idUserL1`, `idUserL2`, `likeL1`) VALUES
(1, 3, 1),
(3, 1, 1),
(6, 2, 1),
(6, 9, 1),
(7, 8, 0),
(9, 6, 0);

-- --------------------------------------------------------

--
-- Structure de la table `matchs`
--

DROP TABLE IF EXISTS `matchs`;
CREATE TABLE IF NOT EXISTS `matchs` (
  `idUserM1` int NOT NULL,
  `idUserM2` int NOT NULL,
  PRIMARY KEY (`idUserM1`,`idUserM2`),
  KEY `MATCHS_FK` (`idUserM1`,`idUserM2`),
  KEY `FK_MATCHS2` (`idUserM2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `matchs`
--

INSERT INTO `matchs` (`idUserM1`, `idUserM2`) VALUES
(10, 12);

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `idUser` int NOT NULL AUTO_INCREMENT,
  `idGenr` int NOT NULL,
  `nomEUser` varchar(50) DEFAULT NULL,
  `prenomUser` varchar(50) DEFAULT NULL,
  `emailUser` varchar(255) DEFAULT NULL,
  `passwordUser` varchar(255) DEFAULT NULL,
  `photo` varchar(50) DEFAULT NULL,
  `age` int DEFAULT NULL,
  `biographie` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`idUser`),
  KEY `USER_FK` (`idUser`),
  KEY `FK_ASSOCIATION_0` (`idGenr`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb3;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`idUser`, `idGenr`, `nomEUser`, `prenomUser`, `emailUser`, `passwordUser`, `photo`, `age`, `biographie`) VALUES
(1, 2, 'Louise', 'Berger', 'Louise@louise.fr', '$2y$10$zuiVL2PeXRcijcMmBKObO.FwY.W9VAlc9FbMOZW.ZTPaaaYROgRw.', 'LIT BLEU', 68, 'J\'ai un super lit !'),
(2, 1, 'Justine', 'Regle', 'equerre@justine.fr', '$2y$10$EMGmbS.cMtGTwIDit6Jh.uzjr0PEuhHLZvkA2uFCYDoCwLJjh4QWa', 'LIT EN FORME D\'EQUERRE', 45, 'J\'ai juste un lit à angle droit.'),
(3, 2, 'Lisa', 'Bruno', 'Bruno@fr', '$2y$10$my5CJpiM4TuiwUzcVOzwGuSiJkLH5dIPIRSOHrMbvghAvyhNBMTR6', 'LIT SIMPLE A CARREAU UN PEU GRAND MAIS PAS TROP', 20, 'Oui'),
(4, 1, 'Lily', 'Oui', 'lily@fr', '$2y$10$kfZ4av6QBo02h2bZeoqIQ.rOJa9BHtBKMx.f7Mz0yGW6UY9huG6AS', 'Gigantesque lit qui peut accueillir 3 personnes', 5, 'Oui Oui'),
(5, 1, 'Lucas', 'Luc', 'Lucas@gmail.fr', '$2y$10$/nZG0QvEu872YxEtVTRgveyQazBnVF0vo/c3N2bSgr/f696BdvS1i', 'Lit', 47, 'je sais pas quoi mettre'),
(6, 1, 'non', 'prénon', 'nonnonnon@nonononononon.fr', '$2y$10$1OnO1I4GIfyixLHSHvsFiuYhtZsUMQYob6MOUMYJf5U107Ii7taxO', 'non', 99, 'oui'),
(7, 1, 'guillaume', 'genou', 'genouuuuuu@coude.fr', '$2y$10$z9X03YxlVwnBEO4V.AgfHumzra6VOxNVc0YC9FqbZR2YUzLhqB73G', 'pied de lit', 1, 'fuuyzfua\"ygfy&gefiuazbiugvliu\"yvrrz'),
(8, 2, 'dormir', 'j\'adore', 'zzzzzzzzzzzzzzzzzzzzzzzz@zzzzz.fr', '$2y$10$3iW9uVUF4WrqLWTo1I2LmuMQJqO7DBAOKgc3HHeWKgMYQPMYXvIjm', 'zzzzzzzzzzzzzzzzzzzzzzzzzzzzz', 19, 'zzzzzzzzzzzzzzzzzzzzzzzzzzzz'),
(9, 1, 'Oui', 'oui', 'oui@ooooooooooooooooooooooooooooooooo.fr', '$2y$10$vX8I/Rg0P5wamjHtp4gLcuWPe54uENNOSmEOHtzpiv4JGmG/B44P2', 'Lit oui oui', 120, 'Non'),
(10, 2, 'Franck', 'lin', 'francklin@tortue.fr', '$2y$10$z2pgPmQHzqM/9crc8/NpJ.JuoVn9C451gCTkrukzwxsqJdAnKDleS', 'Carapace très spacieuse', 34, 'salade'),
(11, 2, 'Char', 'pente', 'charpentinibananini@gmail.com', '$2y$10$fbeLuz/9KvUGXKHQIBLTe.ptoZEAWpyyiwCF0kJf3vI.g6AJtnJHy', 'Toit très confortable et étanche', 59, '3000 tuiles à mon actif'),
(12, 1, 'toungtoung', 'Sahur', 'claquette@mouche.fr', '$2y$10$aRtRPK9bZgwhl1hw3dDnqOY99yUgxOegK6pUKetws0piocbOaDqju', 'Lit en bois, présence d\'échardes', 14, 'brbrpatapin'),
(13, 1, 'Martin', 'pecheur', 'poisson@tchouptchoup.fr', '$2y$10$JTipNiNb4hc1NLPrUlS9q.MyMYPE8g1.s8DmzmTH4Aro3MrNl5bwO', 'Tout l\'océan', 20, 'Blabla'),
(14, 2, 'bloup', 'bloup', 'bloup@blouppppp.fr', '$2y$10$FGlKedZUBFf7Mi54PuKqFuAj8lvBNdF9L9p0C6KbBEACRnpyRDUkO', 'bloup bloup', 17, 'bloup bloup');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `FK_COMMENTS` FOREIGN KEY (`idUserC1`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_COMMENTS2` FOREIGN KEY (`idUserC2`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `FK_LIKES` FOREIGN KEY (`idUserL1`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_LIKES2` FOREIGN KEY (`idUserL2`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `matchs`
--
ALTER TABLE `matchs`
  ADD CONSTRAINT `FK_MATCHS` FOREIGN KEY (`idUserM1`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_MATCHS2` FOREIGN KEY (`idUserM2`) REFERENCES `user` (`idUser`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `FK_ASSOCIATION_0` FOREIGN KEY (`idGenr`) REFERENCES `genre` (`idGenr`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
