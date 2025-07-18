-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : jeu. 17 juil. 2025 à 09:44
-- Version du serveur : 9.3.0
-- Version de PHP : 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Robots-Délices`
--
CREATE DATABASE IF NOT EXISTS `Robots-Délices` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `Robots-Délices`;

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `category_name` varchar(50) DEFAULT NULL,
  `category_logo` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `category_name`, `category_logo`, `created_at`) VALUES
(1, 'Entrées', '🥗', '2025-07-03 09:17:35'),
(2, 'Plats', '🍖', '2025-07-03 13:41:48'),
(3, 'Desserts', '🍰', '2025-07-03 13:41:48'),
(4, 'Boissons', '🥤', '2025-07-03 13:41:48'),
(5, 'Végétarien', '🌱', '2025-07-03 13:41:48'),
(6, 'Rapide', '⚡', '2025-07-03 13:41:48');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int NOT NULL,
  `recipe_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int NOT NULL,
  `recipe_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `attempt_time` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `email`, `attempt_time`) VALUES
(1, 'valentin7@gmail.com', '2025-07-17 09:41:29'),
(2, 'valentin7@gmail.com', '2025-07-17 09:41:37'),
(3, 'valentin7@gmail.com', '2025-07-17 09:41:43'),
(4, 'valentin7@gmail.com', '2025-07-17 09:41:48'),
(5, 'valentin7@gmail.com', '2025-07-17 09:41:53');

-- --------------------------------------------------------

--
-- Structure de la table `recipes`
--

CREATE TABLE `recipes` (
  `id` int NOT NULL,
  `slug` varchar(200) NOT NULL,
  `user_id` int DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text,
  `ingredients` text,
  `instructions` text,
  `cooking_time` varchar(50) DEFAULT NULL,
  `number_persons` varchar(10) DEFAULT NULL,
  `difficulty` varchar(100) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `image_caption` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `popular` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `recipes`
--

INSERT INTO `recipes` (`id`, `slug`, `user_id`, `title`, `description`, `ingredients`, `instructions`, `cooking_time`, `number_persons`, `difficulty`, `category_id`, `photo`, `image_caption`, `created_at`, `popular`) VALUES
(1, 'tarte_aux_pommes_traditionnelle_1', 0, 'Tarte aux Pommes Traditionnelle', 'Une délicieuse tarte aux pommes comme grand-mère la faisait, avec une pâte croustillante et des pommes fondantes parfumées à la cannelle', '1 pâte brisée, 6 pommes Golden, 80g de sucre en poudre, 50g de beurre, 2 œufs,\r\n20cl de crème fraîche, \r\n1 sachet de sucre vanillé, 1 pincée de cannelle', 'Préchauffez le four à 180°C (thermostat 6). Beurrez et farinez un moule à tarte de 28 cm de diamètre.\r\n\r\nÉtalez la pâte brisée dans le moule en la faisant bien adhérer aux bords. Piquez le fond avec une fourchette. \r\n\r\nÉpluchez les pommes et coupez-les en quartiers fins et réguliers. Retirez le cœur et les pépins.\r\n\r\nDisposez les quartiers de pommes sur la pâte en rosace, en les faisant se chevaucher légèrement. \r\n\r\nSaupoudrez les pommes de sucre en poudre et de cannelle selon votre goût. \r\n\r\nDans un bol, battez les œufs avec la crème fraîche et le sucre vanillé jusqu\'à obtenir un mélange homogène. \r\n\r\nVersez délicatement ce mélange sur les pommes, en veillant à ce qu\'il se répartisse bien. \r\n\r\nEnfournez pour 35 à 40 minutes jusqu\'à ce que le dessus soit bien doré et que la crème soit prise. \r\n\r\nLaissez refroidir 10 minutes avant de démouler. Servez tiède ou froid selon vos préférences. ', '45 min', '6', 'Facile', 3, '/img/tarte_aux_pommes.jpg', 'Une tarte aux pommes parfaitement dorée avec sa garniture fondante', '2025-07-01 11:37:02', 1),
(2, 'salade_cesar_1', 0, 'Salade César', 'Une salade César complète et gourmande avec des morceaux de poulet grillé, des croûtons dorés et des oignons rouges pour une touche de couleur et de croquant. Parfaite comme plat principal équilibré', '2 blancs de poulet, 2 cœurs de laitue romaine, 1 petit oignon rouge, 100g de parmesan, 4 tranches de pain, 2 cuillères à soupe d\'huile d\'olive, 1 gousse d\'ail, 2 jaunes d\'œufs, 2 gousses d\'ail, 6 filets d\'anchois, 1 cuillère à soupe de moutarde, 3 cuillères à soupe de jus de citron, 120ml d\'huile d\'olive, 50g de parmesan râpé, Sel et poivre', 'Assaisonnez les blancs de poulet avec sel et poivre.\r\n\r\nFaites-les griller à la poêle 6-8 minutes de chaque côté.\r\n\r\nLaissez refroidir et coupez en lamelles.\r\n\r\nHachez finement l\'ail et écrasez les anchois.\r\n\r\nMélangez les jaunes d\'œufs avec la moutarde.\r\n\r\nAjoutez l\'ail, les anchois et le jus de citron.\r\n\r\nVersez l\'huile en filet en fouettant.\r\n\r\nIncorporez le parmesan râpé, salez et poivrez.\r\n\r\nCoupez le pain en cubes.\r\n\r\nFrottez avec l\'ail et arrosez d\'huile.\r\n\r\nFaites dorer au four 10 minutes à 180°C.\r\n\r\nLavez et coupez la salade en morceaux.\r\n\r\nÉmincez finement l\'oignon rouge.\r\n\r\nMélangez la salade avec une partie de la sauce.\r\n\r\nAjoutez le poulet, les croûtons et l\'oignon rouge.\r\n\r\nParsemez de copeaux de parmesan.\r\n\r\nServez avec le reste de sauce à côté.', '35 min', '4', 'Moyen', 1, '/img/salade_cesar.jpg', 'Une appétissante salade César au poulet, garnie de croûtons dorés croustillants, de lamelles d\'oignons rouges, de morceaux de poulet grillé et de feuilles de salade verte, accompagnée d\'une sauce crémeuse servie à côté', '2025-07-03 13:51:31', 1),
(3, 'spaghetti_carbonara_traditionnelle_1', 0, 'Spaghetti Carbonara Traditionnelle', 'La vraie recette de la carbonara romaine dans sa version la plus authentique. Un plat crémeux sans crème, où la magie opère avec seulement des œufs, du pecorino, du guanciale et du poivre noir. Simple et délicieux !', '400g de spaghetti, 150g de guanciale (ou pancetta/lardons), 4 œufs entiers + 2 jaunes supplémentaires, 100g de pecorino romano râpé (ou parmesan), Poivre noir fraîchement moulu, Sel pour l\'eau des pâtes, Quelques feuilles de basilic frais', 'Coupez le guanciale en petits lardons.\r\n\r\nBattez les œufs avec les jaunes dans un bol.\r\n\r\nAjoutez le pecorino râpé et beaucoup de poivre noir.\r\n\r\nMélangez bien pour obtenir une crème homogène.\r\n\r\nFaites bouillir une grande casserole d\'eau salée.\r\n\r\nPendant ce temps, faites revenir les lardons dans une poêle sans matière grasse jusqu\'à ce qu\'ils soient dorés et croustillants.\r\n\r\nCuisez les spaghetti al dente selon les instructions du paquet.\r\n\r\nRéservez un verre d\'eau de cuisson des pâtes avant d\'égoutter.\r\n\r\nAjoutez les spaghetti égouttés dans la poêle avec les lardons.\r\n\r\nRetirez du feu et laissez tiédir 1 minute.\r\n\r\nVersez le mélange œufs-fromage et mélangez rapidement.\r\n\r\nAjoutez un peu d\'eau de cuisson si nécessaire pour obtenir une consistance crémeuse.\r\n\r\nServez immédiatement avec un jaune d\'œuf au centre si désiré.\r\n\r\nDisposez dans les assiettes chaudes\r\nAjoutez un jaune d\'œuf cru au centre (optionnel).\r\n\r\nParsemez de poivre noir et de pecorino.\r\n\r\nDécorez avec quelques feuilles de basilic frais.', '20 min', '4', 'Facile', 6, '/img/spaghetti_carbonara.jpg', 'Un plat de spaghetti carbonara, garni de lardons croustillants, d\'un jaune d\'œuf coulant au centre et de feuilles de basilic frais, avec une main qui ajoute délicatement une feuille de basilic sur le dessus', '2025-07-07 12:57:55', 1),
(4, 'banane_flambée_1', NULL, 'Banane flambée', 'Ce sont des bananes et elles sont flambées', 'des bananes, du rhum', 'coupez les bananes en long\r\nnoyez les dans le rhum\r\nAllumez le feu !', '15min', '1', 'moyen', 3, 'img/bananes-flambees-1.jpg', 'Photo de la recette', '2025-07-07 14:02:02', 0),
(5, 'banane_flambée_2', NULL, 'Banane flambée', 'Chaussette !', 'des bananes, du rhum, du chocolat', 'Brulez tout !!!!!', '15min', '1', 'facile', 3, 'img/bananes-flambees-1.jpg', 'Photo de la recette', '2025-07-07 14:13:13', 0),
(6, 'banane_flambée_3', NULL, 'Banane flambée', 'bla bla bla', 'des bananes, du rhum, du chocolat', 'faite cuir et faite pas chier', '15min', '1', 'difficile', 4, 'img/bananes-flambees-1.jpg', 'Photo de la recette', '2025-07-17 08:59:10', 0),
(8, 'banane_flambée_4', NULL, 'Banane flambée', '&lt;script&gt;alert(&quot;BANANA HACK&quot;)&lt;/script&gt;', '&lt;script&gt;alert(&quot;BANANA HACK&quot;)&lt;/script&gt;', '&lt;script&gt;alert(&quot;BANANA HACK&quot;)&lt;/script&gt;', '15min', '2', 'facile', 1, 'img/bananes-flambees-1.jpg', 'Photo de la recette', '2025-07-17 09:04:54', 0);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `firstname` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `password`, `created_at`) VALUES
(0, 'Admin', 'Admin', 'admin@robots-delices.fr', 'admin', '2025-07-03 14:06:29'),
(4, 'Valentin', 'Spatafora', 'valentin@gmail.com', '$2y$12$o3M1Z0L9hmqA0G6WRZ5At.qppzCBjVLRfA7vB6ktwRx26IhiL96NC', '2025-07-10 11:50:28'),
(6, 'Valentin', 'Spatafora', 'valentin2@gmail.com', '$2y$12$o3M1Z0L9hmqA0G6WRZ5At.qppzCBjVLRfA7vB6ktwRx26IhiL96NC', '2025-07-10 11:50:28'),
(7, 'Valentin', 'Spatafora', 'valentin7@gmail.com', '$2y$12$MqxSK210hK4aPJHVdiaYfOnKFnd2jf0BWJ3dQaeOuWHwnk04JJQo6', '2025-07-10 11:50:28'),
(8, 'Valentin', 'Spatafora', 'valentin4@gmail.com', '$2y$12$o3M1Z0L9hmqA0G6WRZ5At.qppzCBjVLRfA7vB6ktwRx26IhiL96NC', '2025-07-10 11:50:28'),
(9, 'Valentin', 'Spatafora', 'valentin5@gmail.com', '$2y$12$o3M1Z0L9hmqA0G6WRZ5At.qppzCBjVLRfA7vB6ktwRx26IhiL96NC', '2025-07-10 11:50:28'),
(10, 'Valentin', 'Spatafora', 'valentin9@gmail.com', '$2y$12$RjJa4jm6ZryvtJIHvXZB6.PsafM0v7SJuVzhtQurDtps3LrIE1ZjC', '2025-07-17 09:20:54'),
(11, 'Valentin', 'Spatafora', 'valentin8@gmail.com', '$2y$12$jdEc895wVY54HFeQ1Rfn.uZPLjkOpLd4xCtI0paHQvCWjAkHVGGjC', '2025-07-17 09:33:19');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_name_2` (`category_name`),
  ADD KEY `category_name` (`category_name`),
  ADD KEY `category_name_3` (`category_name`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipe_id` (`recipe_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Index pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `category`
--
ALTER TABLE `category`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Contraintes pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recipes_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `category` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
