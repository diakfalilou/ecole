-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : dim. 24 mai 2026 à 22:37
-- Version du serveur : 11.4.9-MariaDB
-- Version de PHP : 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_school`
--

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(25) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tblanneesclaire`
--

DROP TABLE IF EXISTS `tblanneesclaire`;
CREATE TABLE IF NOT EXISTS `tblanneesclaire` (
  `i_idanneesclaire` int(11) NOT NULL AUTO_INCREMENT,
  `v_annesclaire` varchar(11) NOT NULL,
  `v_debutanneesclaire` varchar(4) NOT NULL,
  `v_finanneesclaire` varchar(4) NOT NULL,
  `d_datecreationanneesclaire` datetime NOT NULL DEFAULT current_timestamp(),
  `i_userID` int(11) NOT NULL,
  PRIMARY KEY (`i_idanneesclaire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tbletablissement`
--

DROP TABLE IF EXISTS `tbletablissement`;
CREATE TABLE IF NOT EXISTS `tbletablissement` (
  `i_idetablissement` int(11) NOT NULL AUTO_INCREMENT,
  `v_nometablissement` varchar(255) NOT NULL,
  `t_adresseetablissement` text DEFAULT NULL,
  `v_telephone1etablissement` varchar(22) DEFAULT NULL,
  `v_telephone2etablissement` varchar(22) DEFAULT NULL,
  `v_adressemailv_telephone1etablissement` varchar(222) DEFAULT NULL,
  `v_nomfondateurv_telephone1etablissement` varchar(255) DEFAULT NULL,
  `i_userID` int(11) NOT NULL,
  `d_datecreationv_telephone1etablissement` datetime NOT NULL DEFAULT current_timestamp(),
  `bt_etatv_telephone1etablissement` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`i_idetablissement`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `roles` int(11) DEFAULT 0,
  `mode_user` int(11) DEFAULT 0,
  `service_id` bigint(20) DEFAULT 0,
  `del_user` tinyint(1) DEFAULT 0,
  `dateCreation` timestamp NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`) USING HASH
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `telephone`, `password`, `roles`, `mode_user`, `service_id`, `del_user`, `dateCreation`, `created_at`, `updated_at`) VALUES
(2, 'Super Admin', 'super@admin.com', '621952061', '$2y$12$R4xxHvzsDveOcTZprldyQe1f/39B1e1zoBayBBU3R7x4eRLvXrXUe', 1, 0, 0, 0, '2026-05-24 20:18:08', '2026-05-24 20:18:08', '2026-05-24 20:18:08');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
