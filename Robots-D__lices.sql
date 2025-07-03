-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : jeu. 03 juil. 2025 à 14:21
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

-- --------------------------------------------------------

--
-- Structure de la table `category`
--

CREATE TABLE `category` (
  `id` int NOT NULL,
  `category_name` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `category`
--

INSERT INTO `category` (`id`, `category_name`, `created_at`) VALUES
(1, 'Entrées', '2025-07-03 09:17:35'),
(2, 'Plats', '2025-07-03 13:41:48'),
(3, 'Desserts', '2025-07-03 13:41:48'),
(4, 'Boissons', '2025-07-03 13:41:48'),
(5, 'Végétarien', '2025-07-03 13:41:48'),
(6, 'Rapide', '2025-07-03 13:41:48');

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
  `category_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `image_caption` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `recipes`
--

INSERT INTO `recipes` (`id`, `slug`, `user_id`, `title`, `description`, `ingredients`, `instructions`, `cooking_time`, `number_persons`, `difficulty`, `category_name`, `photo`, `image_caption`, `created_at`) VALUES
(1, 'tarte_aux_pommes_traditionnelle_1', 0, 'Tarte aux Pommes Traditionnelle', 'Une délicieuse tarte aux pommes comme grand-mère la faisait, avec une pâte croustillante et des pommes fondantes parfumées à la cannelle', '1 pâte brisée, 6 pommes Golden, 80g de sucre en poudre, 50g de beurre, 2 œufs,\r\n20cl de crème fraîche, \r\n1 sachet de sucre vanillé, 1 pincée de cannelle', 'Préchauffez le four à 180°C (thermostat 6). Beurrez et farinez un moule à tarte de 28 cm de diamètre.\r\n\r\nÉtalez la pâte brisée dans le moule en la faisant bien adhérer aux bords. Piquez le fond avec une fourchette. \r\n\r\nÉpluchez les pommes et coupez-les en quartiers fins et réguliers. Retirez le cœur et les pépins.\r\n\r\nDisposez les quartiers de pommes sur la pâte en rosace, en les faisant se chevaucher légèrement. \r\n\r\nSaupoudrez les pommes de sucre en poudre et de cannelle selon votre goût. \r\n\r\nDans un bol, battez les œufs avec la crème fraîche et le sucre vanillé jusqu\'à obtenir un mélange homogène. \r\n\r\nVersez délicatement ce mélange sur les pommes, en veillant à ce qu\'il se répartisse bien. \r\n\r\nEnfournez pour 35 à 40 minutes jusqu\'à ce que le dessus soit bien doré et que la crème soit prise. \r\n\r\nLaissez refroidir 10 minutes avant de démouler. Servez tiède ou froid selon vos préférences. ', '45 min', '6', 'Facile', 'Desserts', '/img/tarte_aux_pommes.jpg', 'Une tarte aux pommes parfaitement dorée avec sa garniture fondante', '2025-07-01 11:37:02'),
(2, 'salade_cesar_2', 0, 'Salade César', NULL, NULL, NULL, '30 min', '4', 'Facile', 'Entrées', '/img/salade_cesar.jpg', NULL, '2025-07-03 13:51:31');

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
(0, 'Admin', 'Admin', 'admin@robots-delices.fr', 'admin', '2025-07-03 14:06:29');

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
-- Index pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `category_name_2` (`category_name`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_name` (`category_name`),
  ADD KEY `category_name_3` (`category_name`);

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
-- AUTO_INCREMENT pour la table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`);

--
-- Contraintes pour la table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
