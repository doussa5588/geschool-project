-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 14 juil. 2025 à 12:14
-- Version du serveur : 10.4.24-MariaDB
-- Version de PHP : 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `geschool`
--

-- --------------------------------------------------------

--
-- Structure de la table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') COLLATE utf8mb4_unicode_ci NOT NULL,
  `justification` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recorded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `level_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int(11) NOT NULL,
  `room` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `name`, `level_id`, `academic_year`, `capacity`, `room`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'L1A', 1, '2025-2026', 30, 'Salle L1A', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(2, 'L1B', 1, '2025-2026', 30, 'Salle L1B', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(3, 'L1C', 1, '2025-2026', 30, 'Salle L1C', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(4, 'L2A', 2, '2025-2026', 30, 'Salle L2A', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(5, 'L2B', 2, '2025-2026', 30, 'Salle L2B', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(6, 'L2C', 2, '2025-2026', 30, 'Salle L2C', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(7, 'L3A', 3, '2025-2026', 30, 'Salle L3A', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(8, 'L3B', 3, '2025-2026', 30, 'Salle L3B', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(9, 'L3C', 3, '2025-2026', 30, 'Salle L3C', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(10, 'M1A', 4, '2025-2026', 30, 'Salle M1A', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(11, 'M1B', 4, '2025-2026', 30, 'Salle M1B', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(12, 'M1C', 4, '2025-2026', 30, 'Salle M1C', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(13, 'M2A', 5, '2025-2026', 30, 'Salle M2A', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(14, 'M2B', 5, '2025-2026', 30, 'Salle M2B', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(15, 'M2C', 5, '2025-2026', 30, 'Salle M2C', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(16, 'L3-INFO', 3, '2025-2026', 25, 'Labo Informatique 1', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(17, 'M1-IA', 4, '2025-2026', 20, 'Labo IA', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(18, 'M2-CYBER', 5, '2025-2026', 15, 'Labo Sécurité', 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03');

-- --------------------------------------------------------

--
-- Structure de la table `class_subjects`
--

CREATE TABLE `class_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `class_subjects`
--

INSERT INTO `class_subjects` (`id`, `class_id`, `subject_id`, `created_at`, `updated_at`) VALUES
(1, 1, 17, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(2, 1, 18, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(3, 1, 1, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(4, 1, 20, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(5, 2, 17, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(6, 2, 18, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(7, 2, 1, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(8, 2, 20, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(9, 3, 17, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(10, 3, 18, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(11, 3, 1, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(12, 3, 20, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(13, 4, 2, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(14, 4, 5, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(15, 4, 19, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(16, 4, 13, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(17, 5, 2, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(18, 5, 5, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(19, 5, 19, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(20, 5, 13, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(21, 6, 2, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(22, 6, 5, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(23, 6, 19, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(24, 6, 13, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(25, 7, 3, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(26, 7, 4, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(27, 7, 6, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(28, 7, 7, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(29, 8, 3, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(30, 8, 4, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(31, 8, 6, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(32, 8, 7, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(33, 9, 3, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(34, 9, 4, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(35, 9, 6, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(36, 9, 7, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(37, 10, 8, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(38, 10, 11, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(39, 10, 14, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(40, 10, 15, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(41, 11, 8, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(42, 11, 11, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(43, 11, 14, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(44, 11, 15, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(45, 12, 8, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(46, 12, 11, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(47, 12, 14, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(48, 12, 15, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(49, 13, 9, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(50, 13, 10, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(51, 13, 12, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(52, 13, 16, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(53, 14, 9, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(54, 14, 10, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(55, 14, 12, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(56, 14, 16, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(57, 15, 9, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(58, 15, 10, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(59, 15, 12, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(60, 15, 16, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(61, 16, 1, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(62, 16, 3, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(63, 16, 18, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(64, 16, 20, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(65, 17, 8, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(66, 17, 9, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(67, 17, 10, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(68, 18, 15, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(69, 18, 16, '2025-07-03 22:25:08', '2025-07-03 22:25:08'),
(70, 18, 4, '2025-07-03 22:25:08', '2025-07-03 22:25:08');

-- --------------------------------------------------------

--
-- Structure de la table `deliberations`
--

CREATE TABLE `deliberations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `average` decimal(5,2) NOT NULL,
  `decision` enum('pass','fail','repeat') COLLATE utf8mb4_unicode_ci NOT NULL,
  `mention` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deliberation_date` date NOT NULL,
  `validated_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `departments`
--

INSERT INTO `departments` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Informatique et Réseaux', 'INFO', 'Département spécialisé dans les technologies de l\'information et des communications', 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(2, 'Génie Logiciel', 'GL', 'Département de développement et conception de logiciels', 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(3, 'Télécommunications', 'TELECOM', 'Département des systèmes de télécommunications et réseaux', 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(4, 'Systèmes d\'Information', 'SI', 'Département de gestion et analyse des systèmes d\'information', 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(5, 'Intelligence Artificielle', 'IA', 'Département spécialisé en intelligence artificielle et machine learning', 0, '2025-06-12 00:06:56', '2025-07-05 02:53:49'),
(6, 'Cybersécurité', 'CYBER', 'Département de sécurité informatique et protection des données', 0, '2025-06-12 00:06:56', '2025-07-05 02:44:40');

-- --------------------------------------------------------

--
-- Structure de la table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `grade` decimal(10,0) NOT NULL,
  `evaluation_type` enum('homework','quiz','exam','project') COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `max_score` decimal(5,2) NOT NULL,
  `date` date NOT NULL,
  `semester` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `subject_id`, `teacher_id`, `grade`, `evaluation_type`, `score`, `max_score`, `date`, `semester`, `academic_year`, `comments`, `created_at`, `updated_at`) VALUES
(1, 1, 8, 1, '16', 'exam', '16.00', '20.00', '2024-11-15', '1', '2025-2026', 'Bon travail en Machine Learning', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(2, 1, 11, 3, '14', 'quiz', '14.00', '20.00', '2024-10-20', '1', '2025-2026', 'Résultats satisfaisants', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(3, 1, 14, 4, '15', 'project', '15.00', '20.00', '2024-12-01', '1', '2025-2026', 'Projet bien présenté', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(4, 1, 15, 5, '17', 'exam', '17.00', '20.00', '2024-11-30', '1', '2025-2026', 'Excellente maîtrise de la cryptographie', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(5, 2, 3, 6, '13', 'exam', '13.00', '20.00', '2024-11-10', '1', '2025-2026', 'Peut mieux faire en réseaux', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(6, 2, 4, 6, '15', 'quiz', '15.00', '20.00', '2024-10-25', '1', '2025-2026', 'Bonne compréhension de la sécurité', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(7, 2, 6, 2, '12', 'project', '12.00', '20.00', '2024-12-05', '1', '2025-2026', 'Gestion de projet à améliorer', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(8, 2, 7, 2, '16', 'homework', '16.00', '20.00', '2024-11-20', '1', '2025-2026', 'Très bon travail sur les tests', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(9, 3, 9, 1, '18', 'exam', '18.00', '20.00', '2024-11-12', '1', '2025-2026', 'Excellent en réseaux de neurones', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(10, 3, 10, 1, '17', 'project', '17.00', '20.00', '2024-12-03', '1', '2025-2026', 'Très bon projet NLP', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(11, 3, 12, 3, '14', 'quiz', '14.00', '20.00', '2024-10-30', '1', '2025-2026', 'Antennes et propagation correct', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(12, 3, 16, 5, '19', 'exam', '19.00', '20.00', '2024-11-25', '1', '2025-2026', 'Maîtrise parfaite de la sécurité réseau', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(13, 4, 1, 6, '15', 'exam', '15.00', '20.00', '2024-11-08', '1', '2025-2026', 'POO bien comprise', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(14, 4, 3, 6, '13', 'quiz', '13.00', '20.00', '2024-10-22', '1', '2025-2026', 'Réseaux à consolider', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(15, 4, 18, 6, '16', 'homework', '16.00', '20.00', '2024-11-18', '1', '2025-2026', 'Algorithmes bien maîtrisés', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(16, 4, 20, 2, '14', 'project', '14.00', '20.00', '2024-12-02', '1', '2025-2026', 'Projet web satisfaisant', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(17, 5, 3, 6, '12', 'exam', '12.00', '20.00', '2024-11-10', '1', '2025-2026', 'Réseaux informatiques à retravailler', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(18, 5, 4, 6, '16', 'quiz', '16.00', '20.00', '2024-10-25', '1', '2025-2026', 'Bonne sécurité informatique', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(19, 5, 6, 2, '13', 'project', '13.00', '20.00', '2024-12-05', '1', '2025-2026', 'Gestion de projet correcte', '2025-07-03 22:26:03', '2025-07-03 22:26:03'),
(20, 5, 7, 2, '15', 'homework', '15.00', '20.00', '2024-11-20', '1', '2025-2026', 'Tests logiciels bien compris', '2025-07-03 22:26:03', '2025-07-03 22:26:03');

-- --------------------------------------------------------

--
-- Structure de la table `levels`
--

CREATE TABLE `levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `levels`
--

INSERT INTO `levels` (`id`, `name`, `code`, `order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Licence 1', 'L1', 1, 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(2, 'Licence 2', 'L2', 2, 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(3, 'Licence 3', 'L3', 3, 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(4, 'Master 1', 'M1', 4, 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(5, 'Master 2', 'M2', 5, 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `body` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2025_06_11_184051_users', 1),
(3, '2025_06_11_184223_departments', 1),
(4, '2025_06_11_184309_levels', 1),
(5, '2025_06_11_184401_teachers', 1),
(6, '2025_06_11_184450_subjects', 1),
(7, '2025_06_11_184548_classes', 1),
(8, '2025_06_11_184614_students', 1),
(9, '2025_06_11_184651_grades', 1),
(10, '2025_06_11_184741_attendances', 1),
(11, '2025_06_11_184814_schedules', 1),
(12, '2025_06_11_184845_deliberations', 1),
(13, '2025_06_11_184913_messages', 1),
(14, '2025_06_11_184942_class_subjects', 1),
(15, '2025_06_11_202438_create_permission_tables', 1),
(16, '2025_06_26_135008_add_is_active_to_students_table', 2);

-- --------------------------------------------------------

--
-- Structure de la table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 3),
(2, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(2, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 23),
(2, 'App\\Models\\User', 34),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14),
(3, 'App\\Models\\User', 15),
(3, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 17),
(3, 'App\\Models\\User', 18),
(3, 'App\\Models\\User', 19),
(3, 'App\\Models\\User', 20),
(3, 'App\\Models\\User', 21),
(3, 'App\\Models\\User', 22),
(3, 'App\\Models\\User', 24),
(3, 'App\\Models\\User', 25),
(3, 'App\\Models\\User', 26),
(3, 'App\\Models\\User', 27),
(3, 'App\\Models\\User', 28),
(3, 'App\\Models\\User', 29),
(3, 'App\\Models\\User', 30),
(3, 'App\\Models\\User', 31),
(3, 'App\\Models\\User', 32),
(3, 'App\\Models\\User', 33);

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'manage-users', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(2, 'view-users', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(3, 'create-users', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(4, 'edit-users', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(5, 'delete-users', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(6, 'manage-students', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(7, 'view-students', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(8, 'create-students', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(9, 'edit-students', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(10, 'delete-students', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(11, 'manage-teachers', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(12, 'view-teachers', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(13, 'create-teachers', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(14, 'edit-teachers', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(15, 'delete-teachers', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(16, 'manage-subjects', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(17, 'view-subjects', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(18, 'create-subjects', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(19, 'edit-subjects', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(20, 'delete-subjects', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(21, 'manage-classes', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(22, 'view-classes', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(23, 'create-classes', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(24, 'edit-classes', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(25, 'delete-classes', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(26, 'manage-schedules', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(27, 'view-schedules', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(28, 'create-schedules', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(29, 'edit-schedules', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(30, 'delete-schedules', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(31, 'manage-grades', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(32, 'view-grades', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(33, 'create-grades', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(34, 'edit-grades', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(35, 'delete-grades', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(36, 'view-own-grades', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(37, 'manage-attendance', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(38, 'view-attendance', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(39, 'mark-attendance', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(40, 'view-own-attendance', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(41, 'manage-deliberations', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(42, 'view-deliberations', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(43, 'create-deliberations', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(44, 'validate-deliberations', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(45, 'view-reports', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(46, 'generate-reports', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(47, 'export-reports', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(48, 'send-messages', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(49, 'receive-messages', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(50, 'manage-messages', 'web', '2025-06-12 00:06:56', '2025-06-12 00:06:56');

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(2, 'teacher', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55'),
(3, 'student', 'web', '2025-06-12 00:06:55', '2025-06-12 00:06:55');

-- --------------------------------------------------------

--
-- Structure de la table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(7, 2),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(17, 2),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(27, 2),
(27, 3),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(31, 2),
(32, 1),
(32, 2),
(33, 1),
(33, 2),
(34, 1),
(34, 2),
(35, 1),
(35, 2),
(36, 1),
(36, 3),
(37, 1),
(37, 2),
(38, 1),
(38, 2),
(39, 1),
(39, 2),
(40, 1),
(40, 3),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(48, 2),
(48, 3),
(49, 1),
(49, 2),
(49, 3),
(50, 1);

-- --------------------------------------------------------

--
-- Structure de la table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `room` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday') COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` enum('1','2') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `students`
--

CREATE TABLE `students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `student_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `academic_year` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enrollment_date` date NOT NULL,
  `status` enum('active','inactive','graduated','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `parent_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medical_info` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blood_type` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `allergies` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `medications` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `doctor_phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `students`
--

INSERT INTO `students` (`id`, `user_id`, `student_number`, `class_id`, `academic_year`, `enrollment_date`, `status`, `parent_contact`, `emergency_contact`, `medical_info`, `blood_type`, `allergies`, `medications`, `doctor_name`, `doctor_phone`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 'STU20250001', 11, '2025-2026', '2024-09-15', 'active', '+221 77 384 8203', '+221 76 468 7796', 'Aucun problème de santé particulier. Suivi médical régulier.', 'O+', NULL, NULL, 'Dr. Amadou Diagne', '+221 77 555 0101', 0, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(2, 8, 'STU20250002', 7, '2025-2026', '2024-09-15', 'active', '+221 77 543 3693', '+221 76 859 2155', 'Asthme léger. Porte toujours un inhalateur en cas de crise.', 'A+', 'Pollen, acariens, poussière', 'Ventoline (inhalateur), Antihistaminiques en période de pollen', 'Dr. Fatou Mbaye', '+221 77 555 0102', 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(3, 9, 'STU20250003', 15, '2025-2026', '2024-09-15', 'active', '+221 77 963 3764', '+221 76 968 7506', 'En bonne santé générale. Pratique du sport régulièrement.', 'B-', 'Arachides, fruits de mer', NULL, 'Dr. Aminata Diallo', '+221 77 555 0103', 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(4, 10, 'STU20250004', 16, '2025-2026', '2024-09-15', 'active', '+221 77 698 4536', '+221 76 691 6225', 'Diabète de type 1. Contrôle glycémique régulier nécessaire.', 'AB+', NULL, 'Insuline rapide (Novorapid), Insuline lente (Lantus)', 'Dr. Ousmane Sow', '+221 77 555 0104', 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(5, 11, 'STU20250005', 9, '2025-2026', '2024-09-15', 'active', '+221 77 674 8582', '+221 76 277 5604', 'Aucun problème de santé connu.', 'O-', 'Pénicilline', NULL, 'Dr. Mariama Kane', '+221 77 555 0105', 1, '2025-06-12 00:07:04', '2025-07-04 12:20:36'),
(6, 12, 'STU20250006', 13, '2025-2026', '2024-09-15', 'active', '+221 77 408 7164', '+221 76 748 6367', 'Hypertension artérielle sous contrôle.', 'A-', NULL, 'Amlodipine 5mg (1 fois par jour)', 'Dr. Ibrahima Fall', '+221 77 555 0106', 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(7, 13, 'STU20250007', 10, '2025-2026', '2024-09-15', 'active', '+221 77 849 3471', '+221 76 737 6002', 'En bonne santé. Végétarien.', 'B+', 'Lactose', NULL, 'Dr. Aissatou Diop', '+221 77 555 0107', 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(8, 14, 'STU20250008', 10, '2025-2026', '2024-09-15', 'active', '+221 77 467 3875', '+221 76 829 7788', 'Migraine chronique. Éviter les écrans prolongés.', 'AB-', NULL, 'Sumatriptan (en cas de crise)', 'Dr. Moussa Thiam', '+221 77 555 0108', 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(9, 15, 'STU20250009', 4, '2025-2026', '2024-09-15', 'active', '+221 77 984 2113', '+221 76 800 1905', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(10, 16, 'STU20250010', 9, '2025-2026', '2024-09-15', 'active', '+221 77 356 3652', '+221 76 863 1945', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(11, 17, 'STU20250011', 10, '2025-2026', '2024-09-15', 'active', '+221 77 985 3488', '+221 76 813 7211', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(12, 18, 'STU20250012', 17, '2025-2026', '2024-09-15', 'active', '+221 77 638 3112', '+221 76 460 5953', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(13, 19, 'STU20250013', 11, '2025-2026', '2024-09-15', 'active', '+221 77 304 3183', '+221 76 915 5326', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(14, 20, 'STU20250014', 16, '2025-2026', '2024-09-15', 'active', '+221 77 822 4329', '+221 76 485 5381', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(15, 21, 'STU20250015', 7, '2025-2026', '2024-09-15', 'active', '+221 77 232 6946', '+221 76 169 7281', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(16, 22, 'STU20250016', 10, '2025-2026', '2024-09-15', 'active', '+221 77 301 5010', '+221 76 814 2028', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(17, 24, 'STU20251000', 10, '2025-2026', '2024-09-15', 'active', '+221 77 940 6903', '+221 76 404 1054', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(18, 25, 'STU20251001', 16, '2025-2026', '2024-09-15', 'active', '+221 77 641 2782', '+221 76 737 4297', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(19, 26, 'STU20251002', 8, '2025-2026', '2024-09-15', 'active', '+221 77 708 5206', '+221 76 476 1965', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:05', '2025-06-12 00:07:05'),
(20, 27, 'STU20251003', 4, '2025-2026', '2024-09-15', 'active', '+221 77 125 1872', '+221 76 196 1155', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:05', '2025-06-12 00:07:05'),
(21, 28, 'STU20251004', 3, '2025-2026', '2024-09-15', 'active', '+221 77 787 4951', '+221 76 647 2484', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:05', '2025-06-12 00:07:05'),
(22, 29, 'STU20251005', 2, '2025-2026', '2024-09-15', 'active', '+221 77 845 5186', '+221 76 345 5719', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:06', '2025-06-12 00:07:06'),
(23, 30, 'STU20251006', 12, '2025-2026', '2024-09-15', 'active', '+221 77 378 6014', '+221 76 555 5870', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:06', '2025-06-12 00:07:06'),
(24, 31, 'STU20251007', 4, '2025-2026', '2024-09-15', 'active', '+221 77 540 1971', '+221 76 285 4225', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:06', '2025-06-12 00:07:06'),
(25, 32, 'STU20251008', 2, '2025-2026', '2024-09-15', 'active', '+221 77 975 4525', '+221 76 385 8366', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:07', '2025-06-12 00:07:07'),
(26, 33, 'STU20251009', 11, '2025-2026', '2024-09-15', 'active', '+221 77 203 3292', '+221 76 117 9821', NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-06-12 00:07:07', '2025-06-12 00:07:07');

-- --------------------------------------------------------

--
-- Structure de la table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credits` int(11) NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `description`, `credits`, `teacher_id`, `department_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Programmation Orientée Objet', 'POO101', 'Introduction aux concepts de la programmation orientée objet', 4, 6, 1, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(2, 'Base de Données', 'BD201', 'Conception et gestion des bases de données relationnelles', 3, 6, 1, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(3, 'Réseaux Informatiques', 'RES301', 'Fondamentaux des réseaux et protocoles de communication', 4, 6, 1, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(4, 'Sécurité Informatique', 'SEC401', 'Principes et techniques de sécurité des systèmes informatiques', 3, 6, 1, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(5, 'Analyse et Conception', 'AC201', 'Méthodes d\'analyse et de conception de logiciels', 4, 2, 2, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(6, 'Gestion de Projet', 'GP301', 'Méthodologies de gestion de projets informatiques', 3, 2, 2, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(7, 'Tests et Qualité Logiciel', 'TQL401', 'Techniques de test et assurance qualité des logiciels', 3, 2, 2, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(8, 'Machine Learning', 'ML501', 'Algorithmes d\'apprentissage automatique', 4, 1, 5, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(9, 'Réseaux de Neurones', 'RN601', 'Deep Learning et réseaux de neurones artificiels', 4, 1, 5, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(10, 'Traitement du Langage Naturel', 'NLP701', 'Techniques de traitement automatique des langues', 3, 1, 5, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(11, 'Systèmes de Communication', 'COM201', 'Principes des systèmes de communication numériques', 4, 3, 3, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(12, 'Antennes et Propagation', 'ANT301', 'Théorie des antennes et propagation des ondes', 3, 3, 3, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(13, 'Systèmes d\'Information', 'SI201', 'Architecture et gestion des systèmes d\'information', 3, 4, 4, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(14, 'ERP et CRM', 'ERP301', 'Systèmes de gestion intégrée et relation client', 3, 4, 4, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(15, 'Cryptographie', 'CRYPT401', 'Algorithmes et protocoles cryptographiques', 4, 5, 6, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(16, 'Sécurité des Réseaux', 'SECNET501', 'Sécurisation des infrastructures réseau', 3, 5, 6, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(17, 'Mathématiques pour l\'Informatique', 'MATH101', 'Mathématiques appliquées à l\'informatique', 4, 6, 1, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(18, 'Algorithmes et Structures de Données', 'ALGO201', 'Algorithmes fondamentaux et structures de données', 4, 6, 1, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(19, 'Systèmes d\'Exploitation', 'SE301', 'Fonctionnement et administration des systèmes d\'exploitation', 3, 6, 1, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(20, 'Développement Web', 'WEB201', 'Technologies et frameworks de développement web', 3, 2, 2, 1, '2025-06-12 00:07:04', '2025-06-12 00:07:04');

-- --------------------------------------------------------

--
-- Structure de la table `teachers`
--

CREATE TABLE `teachers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `specialization` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hire_date` date NOT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `employee_number`, `specialization`, `hire_date`, `salary`, `status`, `department_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 2, 'EMP0001', 'Intelligence Artificielle', '2020-09-01', '1407385.00', 'active', 5, 1, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(2, 3, 'EMP0002', 'Génie Logiciel', '2020-09-01', '985065.00', 'active', 2, 1, '2025-06-12 00:06:57', '2025-06-12 00:06:57'),
(3, 4, 'EMP0003', 'Réseaux et Télécommunications', '2020-09-01', '839847.00', 'active', 3, 1, '2025-06-12 00:06:57', '2025-06-12 00:06:57'),
(4, 5, 'EMP0004', 'Systèmes d\'Information', '2020-09-01', '1086949.00', 'active', 4, 1, '2025-06-12 00:06:57', '2025-06-12 00:06:57'),
(5, 6, 'EMP0005', 'Cybersécurité', '2020-09-01', '1209771.00', 'active', 6, 1, '2025-06-12 00:06:58', '2025-06-12 00:06:58'),
(6, 23, 'DEMO001', 'Informatique Générale', '2020-01-01', '1000000.00', 'active', 1, 1, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(7, 34, 'EMP00006', 'IA', '2025-07-05', '5000000.00', 'active', 2, 1, '2025-07-05 19:00:21', '2025-07-05 19:00:21');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gender` enum('male','female') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `profile_photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `gender`, `email_verified_at`, `password`, `phone`, `address`, `date_of_birth`, `profile_photo`, `is_active`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Administrateur UNCHK', 'admin@unchk.edu', 'male', NULL, '$2y$12$vaPlEBVpI4zGUQZkyX8J5.LMggR2GSwncRoeUhI2cRxPzRVIoiy5y', '+221 33 123 45 67', 'Université Numérique Cheikh Hamidou Kane, Dakar', '1980-01-15', NULL, 1, NULL, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(2, 'Dr. Amadou Diallo', 'amadou.diallo@unchk.edu', 'male', NULL, '$2y$12$cpVGAwArSH6cjoCDCZjhOuZvFPwH1oK4MN.ehJuLsv0jMhZyqWCIi', '+221 77 100 1000', 'Dakar, Sénégal', '1975-01-15', NULL, 1, NULL, '2025-06-12 00:06:56', '2025-06-12 00:06:56'),
(3, 'Prof. Fatou Sall', 'fatou.sall@unchk.edu', 'female', NULL, '$2y$12$B8apTR.TZWUjR6i1VWTUFeOENF52k8LlYLCWJFIH3Q.Nsq6efC.Zq', '+221 77 101 1001', 'Dakar, Sénégal', '1975-02-15', NULL, 1, NULL, '2025-06-12 00:06:57', '2025-06-12 00:06:57'),
(4, 'Dr. Ousmane Ba', 'ousmane.ba@unchk.edu', 'male', NULL, '$2y$12$iG1WeSiZp0jRLvSPFDqZ9Ot0NuesKGfGzjrvtnCF/.9TNSmkCh4UW', '+221 77 102 1002', 'Dakar, Sénégal', '1975-03-15', NULL, 1, NULL, '2025-06-12 00:06:57', '2025-06-12 00:06:57'),
(5, 'Mme. Aïssa Ndiaye', 'aissa.ndiaye@unchk.edu', 'female', NULL, '$2y$12$.sV.vawLlCuifrx4u3O4X.0zGKK8kBuRIry3qF1XZai9ZgwKupzq6', '+221 77 103 1003', 'Dakar, Sénégal', '1975-04-15', NULL, 1, NULL, '2025-06-12 00:06:57', '2025-06-12 00:06:57'),
(6, 'M. Ibrahima Fall', 'ibrahima.fall@unchk.edu', 'male', NULL, '$2y$12$hbqmhhcmrhPpcL3IoCghDeMp1YbFhPQSNzeIcVh.hu4Xkjzvdz5oG', '+221 77 104 1004', 'Dakar, Sénégal', '1975-05-15', NULL, 1, NULL, '2025-06-12 00:06:58', '2025-06-12 00:06:58'),
(7, 'Moussa Touré', 'moussa.touré@unchk.edu', 'male', NULL, '$2y$12$MyX6E5o5mhy0FW5jaactluVmMejniy7oaFchlpQ5DuKMbzDLouQcW', '+221 76 200 2000', 'Dakar, Sénégal', '2000-01-01', NULL, 1, NULL, '2025-06-12 00:06:58', '2025-06-12 00:06:58'),
(8, 'Awa Gueye', 'awa.gueye@unchk.edu', 'female', NULL, '$2y$12$JmtO8HxRLjXhGB0GZnPHe.lXMnazZYmupaygS4EPoha1yMROoCQPm', '+221 76 201 2001', 'Dakar, Sénégal', '2000-02-02', NULL, 1, NULL, '2025-06-12 00:06:58', '2025-06-12 00:06:58'),
(9, 'Cheikh Sy', 'cheikh.sy@unchk.edu', 'male', NULL, '$2y$12$0TqxwQUgZN2ujcJqdS/S0.PE1COY1ZcGi6pPoVVV.jNxX7k4GR51y', '+221 76 202 2002', 'Dakar, Sénégal', '2000-03-03', NULL, 1, NULL, '2025-06-12 00:06:59', '2025-06-12 00:06:59'),
(10, 'Mariama Diop', 'mariama.diop@unchk.edu', 'female', NULL, '$2y$12$zMzqB0rz4M9k/ou4j.FdfOl48WFphP027s8ZmZtDRi6ky5n2odZDy', '+221 76 203 2003', 'Dakar, Sénégal', '2000-04-04', NULL, 1, NULL, '2025-06-12 00:06:59', '2025-06-12 00:06:59'),
(11, 'Abdou Kane', 'abdou.kane@unchk.edu', 'male', NULL, '$2y$12$pn31fz4VZ7YpBG1VDwVdWuoZQV0BEY7epD99NBjyEr7lAj5zB2YIK', '+221 76 204 2004', 'Dakar, Sénégal', '2000-05-05', NULL, 1, NULL, '2025-06-12 00:06:59', '2025-06-12 00:06:59'),
(12, 'Bineta Sarr', 'bineta.sarr@unchk.edu', 'female', NULL, '$2y$12$A4I.3HG95yEmqrU5R51LGuXP0tEM3JnVP7Qn.psokFIxlftN2S9GS', '+221 76 205 2005', 'Dakar, Sénégal', '2000-06-06', NULL, 1, NULL, '2025-06-12 00:07:00', '2025-06-12 00:07:00'),
(13, 'Omar Cissé', 'omar.cissé@unchk.edu', 'male', NULL, '$2y$12$zGHnNWWzLuCZ0vaKBwQz1uqMKNctzVynp18dMBIK3Ry1lwfuBihAK', '+221 76 206 2006', 'Dakar, Sénégal', '2000-07-07', NULL, 1, NULL, '2025-06-12 00:07:00', '2025-06-12 00:07:00'),
(14, 'Khady Thiam', 'khady.thiam@unchk.edu', 'female', NULL, '$2y$12$gTqNelIACOaJQifeS0xClOcLR3wHr7pFnPqvxwp0ZD7whZTY9x6KC', '+221 76 207 2007', 'Dakar, Sénégal', '2000-08-08', NULL, 1, NULL, '2025-06-12 00:07:00', '2025-06-12 00:07:00'),
(15, 'Mamadou Diouf', 'mamadou.diouf@unchk.edu', 'male', NULL, '$2y$12$B8mHAxc5/ogs9kjHr54SO.3Wwb8dipV/tRQfoNdAa0Uik8NXhGiXa', '+221 76 208 2008', 'Dakar, Sénégal', '2000-09-09', NULL, 1, NULL, '2025-06-12 00:07:01', '2025-06-12 00:07:01'),
(16, 'Ndeye Wade', 'ndeye.wade@unchk.edu', 'female', NULL, '$2y$12$KfDz3zLY5lMCapUAKXQqkOPNTbHiJTpE.igiH.u2zrZTdYDfZxgG2', '+221 76 209 2009', 'Dakar, Sénégal', '2000-10-10', NULL, 1, NULL, '2025-06-12 00:07:01', '2025-06-12 00:07:01'),
(17, 'Alioune Mbaye', 'alioune.mbaye@unchk.edu', 'male', NULL, '$2y$12$VQxSuepAUijCfyvXFzKT6eyN2k85dNcfrOCnh25RMRCHCoHFkzW4.', '+221 76 210 2010', 'Dakar, Sénégal', '2000-11-11', NULL, 1, NULL, '2025-06-12 00:07:01', '2025-06-12 00:07:01'),
(18, 'Coumba Ba', 'coumba.ba@unchk.edu', 'female', NULL, '$2y$12$ruMgfOcQ9lErWtD.CwlhoeE/ygfyfj0Wv8ubvoREPBAslpPZleYu6', '+221 76 211 2011', 'Dakar, Sénégal', '2000-12-12', NULL, 1, NULL, '2025-06-12 00:07:02', '2025-06-12 00:07:02'),
(19, 'Saliou Niang', 'saliou.niang@unchk.edu', 'male', NULL, '$2y$12$sqD5sQG.xzQBQblVp2ZnXe/p9RhVXK4LMUMnzXmGmaD6gnUqzH7fO', '+221 76 212 2012', 'Dakar, Sénégal', '2000-01-13', NULL, 1, NULL, '2025-06-12 00:07:02', '2025-06-12 00:07:02'),
(20, 'Adama Faye', 'adama.faye@unchk.edu', 'male', NULL, '$2y$12$ZIf5w3vrGKMctX5OpKt48Of/agfoJCqEGofw6tEHLnRZNawTUEn4m', '+221 76 213 2013', 'Dakar, Sénégal', '2000-02-14', NULL, 1, NULL, '2025-06-12 00:07:02', '2025-06-12 00:07:02'),
(21, 'Yacine Camara', 'yacine.camara@unchk.edu', 'female', NULL, '$2y$12$Ip1eK20kOFnP85/NLU4aNOkWIMNja8aEEjjfVEBFZRHqUA6xQf70K', '+221 76 214 2014', 'Dakar, Sénégal', '2000-03-15', NULL, 1, NULL, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(22, 'Étudiant Démo', 'student@unchk.edu', 'male', NULL, '$2y$12$93IBL2hxWJT9rPKyAXiXDuVEprp/KdvhqnWN/3s6rZbewpUdHiPkm', '+221 77 123 45 67', 'Dakar, Sénégal', '2001-05-15', NULL, 1, NULL, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(23, 'Enseignant Démo', 'teacher@unchk.edu', 'male', NULL, '$2y$12$Ns3Fwm7I2LSwWqNYukmHsOVjEs5WZ7qgbFEhn1zrQQa2HU8eQM4eC', '+221 77 987 65 43', 'Dakar, Sénégal', '1985-03-20', NULL, 1, NULL, '2025-06-12 00:07:03', '2025-06-12 00:07:03'),
(24, 'Fatima Mbengue', 'fatima.mbengue338@unchk.edu', 'female', NULL, '$2y$12$aXUbe5M1CadCBqSAGXkkqO2E0Yw5wJwdIrX9E64nis4IBfskjrKHK', '+221 78 649 4203', 'Dakar, Sénégal', '2000-02-28', NULL, 1, NULL, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(25, 'Modou Diagne', 'modou.diagne817@unchk.edu', 'male', NULL, '$2y$12$i3U6b5Gl7JSxYEf9oE70yO83Wf2FSD2JvlMAdvKsSIFMpY20atN16', '+221 78 234 2320', 'Dakar, Sénégal', '2000-10-28', NULL, 1, NULL, '2025-06-12 00:07:04', '2025-06-12 00:07:04'),
(26, 'Rama Thiaw', 'rama.thiaw815@unchk.edu', 'female', NULL, '$2y$12$SXN9Keq3k/NOLD4qQHdUoeG5bzwys1Owzkb.c9XZOfFZUsrzLo6gi', '+221 78 971 6476', 'Dakar, Sénégal', '2000-08-06', NULL, 1, NULL, '2025-06-12 00:07:05', '2025-06-12 00:07:05'),
(27, 'Babacar Seck', 'babacar.seck865@unchk.edu', 'male', NULL, '$2y$12$GhoZP8LgdLf0yHRPbAbUveYHAtQUjMhW7UeT.lxhshYDbRCZXoJNO', '+221 78 745 3375', 'Dakar, Sénégal', '2000-07-27', NULL, 1, NULL, '2025-06-12 00:07:05', '2025-06-12 00:07:05'),
(28, 'Astou Badji', 'astou.badji722@unchk.edu', 'female', NULL, '$2y$12$/7TZ37fcyfgNTO1.N86wwukSjV309k8JZq0W2zes7bPabCAAFBm5S', '+221 78 451 9001', 'Dakar, Sénégal', '2000-06-17', NULL, 1, NULL, '2025-06-12 00:07:05', '2025-06-12 00:07:05'),
(29, 'Mouhamadou Sy', 'mouhamadou.sy777@unchk.edu', 'male', NULL, '$2y$12$lOWqB/uuqml9CH65ByL.aeCrNfikez7Ty3BLtXMLSZTtXSNGsbwae', '+221 78 275 5870', 'Dakar, Sénégal', '2000-06-07', NULL, 1, NULL, '2025-06-12 00:07:06', '2025-06-12 00:07:06'),
(30, 'Khadija Fall', 'khadija.fall786@unchk.edu', 'female', NULL, '$2y$12$kCa/oGrsCJeT4up9OhzoPu4abUMsyAI1ptOzvX8Ywc/2pxoaYUXra', '+221 78 253 2584', 'Dakar, Sénégal', '2000-12-11', NULL, 1, NULL, '2025-06-12 00:07:06', '2025-06-12 00:07:06'),
(31, 'Serigne Diouf', 'serigne.diouf135@unchk.edu', 'male', NULL, '$2y$12$m8f0QP3nt66fF3TR3DbUNOEYqi7uRhBQOLpJvTKmYhoVS.N8MrNE6', '+221 78 878 6156', 'Dakar, Sénégal', '2000-12-12', NULL, 1, NULL, '2025-06-12 00:07:06', '2025-06-12 00:07:06'),
(32, 'Mame Diarra', 'mame.diarra109@unchk.edu', 'female', NULL, '$2y$12$vZr.QHslYLRP4CkwGAh0tO60geSCCzRYJskNMOwLJFwG0W/tSYRdi', '+221 78 696 2131', 'Dakar, Sénégal', '2000-03-23', NULL, 1, NULL, '2025-06-12 00:07:07', '2025-06-12 00:07:07'),
(33, 'Ibou Samb', 'ibou.samb990@unchk.edu', 'male', NULL, '$2y$12$9Fj7Yi2jSup7E1tj6WyJMOsnB7JqgrTZ9Lzr1LQKdZVWGgDhoBTz.', '+221 78 478 4242', 'Dakar, Sénégal', '2000-02-25', NULL, 1, NULL, '2025-06-12 00:07:07', '2025-06-12 00:07:07'),
(34, 'Youssouph Badji SANE', 'youssouphbadji.sane@uadb.edu.sn', NULL, NULL, '$2y$12$wuugGAn34fwjCcK5fg3KVuGN04EE0D9nTBe3ZRb5YyGnrUmNxqyOW', '+221778092105', 'Dakar', '1991-09-02', 'teachers/photos/VRbAk8tmDK6SQu67GdcZOYusfJAaVfMMz9t8L4tz.jpg', 1, NULL, '2025-07-05 19:00:21', '2025-07-05 19:00:21');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendances_student_id_foreign` (`student_id`),
  ADD KEY `attendances_subject_id_foreign` (`subject_id`),
  ADD KEY `attendances_recorded_by_foreign` (`recorded_by`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classes_level_id_foreign` (`level_id`);

--
-- Index pour la table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `class_subjects_class_id_subject_id_unique` (`class_id`,`subject_id`),
  ADD KEY `class_subjects_subject_id_foreign` (`subject_id`);

--
-- Index pour la table `deliberations`
--
ALTER TABLE `deliberations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deliberations_student_id_foreign` (`student_id`),
  ADD KEY `deliberations_validated_by_foreign` (`validated_by`);

--
-- Index pour la table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_code_unique` (`code`);

--
-- Index pour la table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_student_id_foreign` (`student_id`),
  ADD KEY `grades_subject_id_foreign` (`subject_id`),
  ADD KEY `grades_teacher_id_foreign` (`teacher_id`);

--
-- Index pour la table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `levels_code_unique` (`code`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_recipient_id_foreign` (`recipient_id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Index pour la table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Index pour la table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Index pour la table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_subject_id_foreign` (`subject_id`),
  ADD KEY `schedules_teacher_id_foreign` (`teacher_id`),
  ADD KEY `schedules_class_id_foreign` (`class_id`);

--
-- Index pour la table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `students_student_number_unique` (`student_number`),
  ADD KEY `students_user_id_foreign` (`user_id`),
  ADD KEY `students_class_id_foreign` (`class_id`);

--
-- Index pour la table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`),
  ADD KEY `subjects_teacher_id_foreign` (`teacher_id`),
  ADD KEY `subjects_department_id_foreign` (`department_id`);

--
-- Index pour la table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teachers_employee_number_unique` (`employee_number`),
  ADD KEY `teachers_user_id_foreign` (`user_id`),
  ADD KEY `teachers_department_id_foreign` (`department_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `class_subjects`
--
ALTER TABLE `class_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT pour la table `deliberations`
--
ALTER TABLE `deliberations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `students`
--
ALTER TABLE `students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_recorded_by_foreign` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`);

--
-- Contraintes pour la table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`);

--
-- Contraintes pour la table `class_subjects`
--
ALTER TABLE `class_subjects`
  ADD CONSTRAINT `class_subjects_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `deliberations`
--
ALTER TABLE `deliberations`
  ADD CONSTRAINT `deliberations_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `deliberations_validated_by_foreign` FOREIGN KEY (`validated_by`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `grades_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `grades_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `schedules_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `schedules_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Contraintes pour la table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Contraintes pour la table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `teachers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
