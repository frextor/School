
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `acheques_supprimer`;
/*!50001 DROP VIEW IF EXISTS `acheques_supprimer`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `acheques_supprimer` AS SELECT 
 1 AS `id_cheque_paiement`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `amos_absence_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_absence_eleve` (
  `id_absence` int(11) NOT NULL AUTO_INCREMENT,
  `date_absence` date NOT NULL,
  `heure_absence` time NOT NULL,
  `id_cours` int(11) NOT NULL,
  `retards_non_justifies` tinyint(1) NOT NULL,
  `retards_excuses` tinyint(1) NOT NULL,
  `absences_non_justifies` tinyint(1) NOT NULL,
  `absences_excuses` tinyint(1) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `annotation` varchar(250) NOT NULL,
  `semestre` int(11) NOT NULL,
  `valide` int(11) NOT NULL DEFAULT 1,
  `justificatif` int(11) NOT NULL DEFAULT 0,
  `justificatif_fichers` text DEFAULT NULL,
  `date_justificatif` datetime DEFAULT NULL,
  `modification_justificatif` int(11) NOT NULL,
  PRIMARY KEY (`id_absence`),
  KEY `ids` (`id_cours`,`id_eleve`),
  KEY `id_unite_enseignement` (`id_unite_enseignement`),
  KEY `id_eleve` (`id_eleve`),
  KEY `date_absence` (`date_absence`),
  KEY `heure_absence` (`heure_absence`),
  CONSTRAINT `amos_absence_eleve_ibfk_1` FOREIGN KEY (`id_eleve`) REFERENCES `amos_eleves` (`id_eleve`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_absence_eleve_ibfk_2` FOREIGN KEY (`id_unite_enseignement`) REFERENCES `amos_unite_enseignement` (`id_unite_enseignement`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10596 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_actions_crm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_actions_crm` (
  `id_action` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `info_utile` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  UNIQUE KEY `id_action` (`id_action`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_actions_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_actions_eleve` (
  `id_action` int(11) NOT NULL AUTO_INCREMENT,
  `engagement_associatif` double NOT NULL,
  `projet_tutore` double NOT NULL,
  `participation_salon` double NOT NULL,
  `stage_sejour` double NOT NULL,
  `commentaire` text NOT NULL,
  `id_eleve` int(11) NOT NULL,
  PRIMARY KEY (`id_action`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_activite_hors_planning`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_activite_hors_planning` (
  `id_activite_hors_planning` int(11) NOT NULL AUTO_INCREMENT,
  `activite_hors_planning_nom` varchar(50) CHARACTER SET latin1 NOT NULL,
  `activite_hors_planning_couleur` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id_activite_hors_planning`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_activite_hors_planning_personne`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_activite_hors_planning_personne` (
  `id_activite_hors_planning_personne` int(11) NOT NULL AUTO_INCREMENT,
  `id_personne` int(11) NOT NULL,
  `id_activite_hors_planning` int(11) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  PRIMARY KEY (`id_activite_hors_planning_personne`),
  KEY `id_personne` (`id_personne`),
  KEY `id_activite_hors_planning` (`id_activite_hors_planning`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_activite_intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_activite_intervenant` (
  `id_activite_intervenant` int(11) NOT NULL AUTO_INCREMENT,
  `id_intervenant` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `id_classe` varchar(50) NOT NULL,
  `id_salle` varchar(50) NOT NULL,
  `id_groupe` varchar(50) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  `annotation` text NOT NULL,
  `annotation_etudiant` varchar(250) DEFAULT NULL,
  `annotation_intervenant` varchar(250) DEFAULT NULL,
  `url_files` varchar(250) DEFAULT NULL,
  `groupe` varchar(100) NOT NULL,
  `semestre` tinyint(1) NOT NULL DEFAULT 1,
  `annee` int(4) NOT NULL,
  `is_recurrence` int(11) NOT NULL DEFAULT 0,
  `all_day` int(11) DEFAULT 0,
  PRIMARY KEY (`id_activite_intervenant`),
  KEY `id_intervenant` (`id_intervenant`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_cours` (`id_cours`),
  KEY `id_classe` (`id_classe`),
  KEY `id_salle` (`id_salle`),
  KEY `semestre` (`semestre`),
  KEY `annee` (`annee`),
  KEY `date_debut` (`date_debut`),
  KEY `date_fin` (`date_fin`)
) ENGINE=InnoDB AUTO_INCREMENT=7799 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_activite_intervenant_groupe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_activite_intervenant_groupe` (
  `id_activite_intervenant_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `id_activite_intervenant` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  PRIMARY KEY (`id_activite_intervenant_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_add_rows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_add_rows` (
  `id_add_rows` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `date_add` date NOT NULL,
  `nb_add` int(11) NOT NULL,
  PRIMARY KEY (`id_add_rows`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_admin_origine_traces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_admin_origine_traces` (
  `id_admin_origine_trace` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `module` varchar(25) NOT NULL,
  `trace` text NOT NULL,
  `espace` varchar(255) NOT NULL,
  `utilisateur` varchar(255) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_admin_origine_trace` (`id_admin_origine_trace`)
) ENGINE=InnoDB AUTO_INCREMENT=505935 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_admins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_admins` (
  `id_admin` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(64) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profil` varchar(50) NOT NULL,
  `avatar` varchar(30) NOT NULL DEFAULT 'userprofile.png',
  `id_service` int(11) unsigned NOT NULL,
  `token` varchar(250) NOT NULL,
  PRIMARY KEY (`id_admin`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_admins_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_admins_etablissement` (
  `id_admin` int(11) unsigned NOT NULL,
  `id_etablissement` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `etablissement_principal` tinyint(1) NOT NULL DEFAULT 0,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=615 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_admins_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_admins_notifications` (
  `id_notification` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_admin` int(11) NOT NULL,
  `event` enum('ri','ea','salons') DEFAULT NULL,
  `titre` varchar(100) NOT NULL,
  `message` varchar(250) NOT NULL,
  `lue` bit(1) NOT NULL DEFAULT b'0',
  `lien` varchar(250) NOT NULL,
  `picto` varchar(250) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_notification` (`id_notification`),
  KEY `id_admin` (`id_admin`),
  KEY `lue` (`lue`),
  CONSTRAINT `amos_admins_notifications_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `amos_admins` (`id_admin`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=228138 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_annotations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_annotations` (
  `id_annotations` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) unsigned NOT NULL,
  `content` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `id_annotations` (`id_annotations`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_api_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_api_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_batch_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_batch_queue` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `data` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4951 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_canal`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_canal` (
  `id_canal` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `actif` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_canal`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_cheques_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_cheques_paiement` (
  `id_cheque_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `photo_cheque` text NOT NULL,
  `numero_cheque` varchar(50) NOT NULL,
  `montant_paiement` float NOT NULL,
  `date_encaissement` date DEFAULT NULL,
  `nom_banque` varchar(100) NOT NULL,
  `id_paiement_eleve` int(11) NOT NULL,
  `id_objet_paiement` int(11) NOT NULL,
  `date_echeance` date DEFAULT NULL,
  `id_etablissement` int(11) NOT NULL,
  `id_entreprises_type_contrat` int(11) DEFAULT NULL,
  `paiement_recu` tinyint(1) DEFAULT NULL,
  `cheque_caution` tinyint(1) DEFAULT NULL,
  `mode_paiement` varchar(30) DEFAULT NULL,
  `numero_echeance` int(11) DEFAULT NULL,
  `echeance` varchar(100) DEFAULT NULL,
  `cout_reel` double DEFAULT NULL,
  `montant_a_verser` double DEFAULT NULL,
  `date_maximum_communication` date DEFAULT NULL,
  PRIMARY KEY (`id_cheque_paiement`),
  KEY `id_paiement_eleve` (`id_paiement_eleve`,`id_objet_paiement`)
) ENGINE=InnoDB AUTO_INCREMENT=31522 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_cheques_paiement2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_cheques_paiement2` (
  `id_cheque_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `photo_cheque` text NOT NULL,
  `numero_cheque` varchar(50) NOT NULL,
  `montant_paiement` float NOT NULL,
  `date_encaissement` date NOT NULL,
  `nom_banque` varchar(100) NOT NULL,
  `id_paiement_eleve` int(11) NOT NULL,
  `id_objet_paiement` int(11) NOT NULL,
  `date_echeance` date NOT NULL,
  PRIMARY KEY (`id_cheque_paiement`),
  KEY `id_paiement_eleve` (`id_paiement_eleve`,`id_objet_paiement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_classe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_classe` (
  `id_classe` int(11) NOT NULL AUTO_INCREMENT,
  `code_classe` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `classe` text COLLATE utf8_unicode_ci NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `couleur` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_classe`),
  KEY `id_niveau` (`id_niveau`,`id_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_classe_annees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_classe_annees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_classe` int(11) NOT NULL,
  `annee` smallint(4) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_clients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_clients` (
  `numero_client` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  PRIMARY KEY (`numero_client`,`id_eleve`),
  KEY `numero_client` (`numero_client`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=1064 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_config_periode_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_config_periode_formation` (
  `id_config_periode_formation` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `annee_scolaire` varchar(100) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `nb_heure_annuel` int(10) unsigned NOT NULL,
  `nb_heure_trimestriel` int(10) unsigned NOT NULL,
  `diplome_rncp` varchar(255) DEFAULT NULL,
  `code_diplome` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id_config_periode_formation` (`id_config_periode_formation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_config_periodes_formation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_config_periodes_formation` (
  `id_config_periode_formation` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `annee_scolaire` varchar(100) NOT NULL,
  `periode` varchar(255) NOT NULL,
  `nb_heure_annuel` decimal(11,2) DEFAULT NULL,
  `diplome_rncp` varchar(255) DEFAULT NULL,
  `code_diplome` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id_config_periode_formation` (`id_config_periode_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_config_periodes_formation_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_config_periodes_formation_classes` (
  `id_config_periode_formation_classe` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_config_periode_formation` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  UNIQUE KEY `id_config_periode_formation_classe` (`id_config_periode_formation_classe`)
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_config_periodes_formation_niveaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_config_periodes_formation_niveaux` (
  `id_config_periode_formation_niveau` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_config_periode_formation` int(11) unsigned NOT NULL,
  `id_niveau` int(11) unsigned NOT NULL,
  UNIQUE KEY `id_config_periode_formation_niveau` (`id_config_periode_formation_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=134 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_config_periodes_formation_periodes_trimestrielles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_config_periodes_formation_periodes_trimestrielles` (
  `id_config_periode_formation_periode_trimestrielle` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_config_periode_formation` int(11) unsigned NOT NULL,
  `periode` varchar(255) NOT NULL,
  `nb_heure` decimal(11,2) DEFAULT NULL,
  UNIQUE KEY `id_config_periode_formation_periode_trimestrielle` (`id_config_periode_formation_periode_trimestrielle`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_constants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_constants` (
  `id_constant` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `value` varchar(150) NOT NULL,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`id_constant`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_contact_ecoles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_contact_ecoles` (
  `id_contact_ecole` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `etablissement` varchar(80) NOT NULL,
  `ordre` varchar(60) NOT NULL,
  PRIMARY KEY (`id_contact_ecole`),
  KEY `id_contact` (`id_contact`),
  FULLTEXT KEY `etablissement` (`etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=50977 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `maut_sync_campus` AFTER INSERT ON `amos_contact_ecoles` FOR EACH ROW BEGIN  

IF (SELECT mautic_id FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
IS NOT NULL AND NEW.id_contact = (SELECT id_contact_parent FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,
(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(
    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
        ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(    
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = NEW.id_contact
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `maut_update_campus` AFTER UPDATE ON `amos_contact_ecoles` FOR EACH ROW BEGIN  

IF (SELECT mautic_id FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
IS NOT NULL AND NEW.id_contact = (SELECT id_contact_parent FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,
(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(
    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
    ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(    
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = NEW.id_contact
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_contact_origine_traces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_contact_origine_traces` (
  `id_contact_origine_trace` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_contact_parent` int(11) NOT NULL,
  `trace` text NOT NULL,
  `espace` varchar(255) CHARACTER SET utf8 NOT NULL,
  `date` datetime NOT NULL,
  `utilisateur` varchar(200) NOT NULL,
  `id_tache` int(11) unsigned DEFAULT NULL,
  `abouti` tinyint(1) DEFAULT NULL,
  `entrant` tinyint(1) DEFAULT NULL,
  `id_action` int(11) unsigned DEFAULT NULL,
  `id_canal` int(11) unsigned DEFAULT NULL,
  `id_utilisateur` int(11) unsigned DEFAULT NULL,
  `type_tache` varchar(255) DEFAULT NULL,
  `id_motif_abandon` int(11) DEFAULT NULL,
  `type_utilisateur` enum('admin','lead') DEFAULT NULL,
  PRIMARY KEY (`id_contact_origine_trace`),
  KEY `idx_id_contact` (`id_contact`),
  KEY `idx_id_action` (`id_action`),
  KEY `idx_id_tache` (`id_tache`),
  KEY `idx_abouti` (`abouti`),
  KEY `type_utilisateur` (`type_utilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=1120233 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_contacts` (
  `id_contact` int(11) NOT NULL AUTO_INCREMENT,
  `id_contact_parent` int(11) NOT NULL,
  `civilite` enum('Mme','Melle','M') DEFAULT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `sexe` enum('f','m') NOT NULL,
  `date_naissance` varchar(10) NOT NULL,
  `lieu_naissance` varchar(25) NOT NULL,
  `pays_naissance` varchar(20) DEFAULT NULL,
  `nationalite` varchar(20) NOT NULL,
  `adresse` mediumtext NOT NULL,
  `code_postal` varchar(10) NOT NULL,
  `ville` varchar(30) NOT NULL,
  `pays` varchar(30) NOT NULL,
  `residence` enum('fr','ue','hue') DEFAULT NULL,
  `code_country` varchar(25) DEFAULT NULL,
  `tel_country` varchar(25) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(60) NOT NULL,
  `email_office` varchar(60) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `comment_connaitre_amos` mediumtext NOT NULL,
  `intitule_derniere_formation` varchar(80) NOT NULL,
  `lieu_derniere_formation` varchar(60) NOT NULL,
  `date_derniere_formation` varchar(7) NOT NULL,
  `niveau_derniere_formation` varchar(10) NOT NULL,
  `diplome_derniere_formation` varchar(80) NOT NULL,
  `newsletter` tinyint(1) NOT NULL,
  `offres_partenaires` tinyint(1) NOT NULL,
  `candidat` tinyint(1) NOT NULL,
  `date_inscription` datetime NOT NULL,
  `reunion_info` tinyint(1) NOT NULL,
  `derniere_reunion` int(11) NOT NULL,
  `compteur_reunion` tinyint(1) NOT NULL,
  `reaffectation_manuelle` tinyint(1) NOT NULL,
  `demande_brochure` tinyint(1) NOT NULL,
  `rappel_reunion` tinyint(1) NOT NULL,
  `participe_reunion` tinyint(1) NOT NULL,
  `salon` tinyint(1) NOT NULL,
  `agent_de_joueur` tinyint(1) NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `salon_nom` varchar(50) NOT NULL,
  `salon_ville` varchar(20) NOT NULL,
  `salon_date` datetime NOT NULL,
  `annotation` text NOT NULL,
  `note_globale` int(5) DEFAULT NULL,
  `step` enum('1','2','3','4','5') NOT NULL,
  `annee_rentree` int(5) NOT NULL,
  `source` int(11) NOT NULL,
  `trace_contact` varchar(250) DEFAULT NULL,
  `last_update` timestamp NULL DEFAULT NULL,
  `suivre_actu` tinyint(1) NOT NULL,
  `mautic_id` int(11) unsigned DEFAULT NULL,
  `id_admin_proprietaire` int(11) DEFAULT NULL,
  `stop_relances` tinyint(1) NOT NULL DEFAULT 0,
  `id_motif_abandon` int(5) DEFAULT NULL,
  `type` varchar(250) DEFAULT NULL,
  `brochure_envoye` tinyint(1) NOT NULL DEFAULT 0,
  `debug_annee_rentree` int(5) DEFAULT NULL,
  `id_filiere` int(11) DEFAULT NULL,
  `id_rythme` int(11) DEFAULT NULL,
  `pseudo_skype` varchar(50) DEFAULT NULL,
  `dossier_complet` tinyint(1) DEFAULT NULL,
  `bulletin_recu` tinyint(1) DEFAULT NULL,
  `ue` tinyint(1) DEFAULT NULL,
  `documents_a_apporter` tinyint(1) DEFAULT NULL,
  `nom_jeune_fille` varchar(50) DEFAULT NULL,
  `date_entree_france` varchar(10) DEFAULT NULL,
  `id_diplome` int(11) DEFAULT NULL,
  `relance_cv` tinyint(1) DEFAULT NULL,
  `cv_recu` tinyint(1) DEFAULT NULL,
  `session` int(11) DEFAULT 0,
  PRIMARY KEY (`id_contact`),
  KEY `id_formation` (`id_formation`),
  KEY `idx_mautic_id` (`mautic_id`),
  KEY `idx_visible` (`visible`),
  KEY `idx_id_contact_parent` (`id_contact_parent`),
  KEY `idx_annee_rentree` (`annee_rentree`),
  KEY `id_diplome` (`id_diplome`),
  KEY `source` (`source`),
  KEY `id_admin_proprietaire` (`id_admin_proprietaire`),
  FULLTEXT KEY `nom` (`nom`,`prenom`,`nationalite`,`ville`,`pays`,`niveau_derniere_formation`,`telephone`,`email`)
) ENGINE=InnoDB AUTO_INCREMENT=44714 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_contacts_etablissements_groupped`;
/*!50001 DROP VIEW IF EXISTS `amos_contacts_etablissements_groupped`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `amos_contacts_etablissements_groupped` AS SELECT 
 1 AS `id_contact`,
 1 AS `etablissements`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `amos_contacts_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_contacts_sources` (
  `id_source` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(250) NOT NULL,
  `id_famille_des_sources` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_source`)
) ENGINE=InnoDB AUTO_INCREMENT=604 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_conventions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_conventions` (
  `id_convention` int(11) NOT NULL AUTO_INCREMENT,
  `titre_document` varchar(100) NOT NULL,
  `nom_document` varchar(100) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_convention`),
  KEY `id_niveau` (`id_niveau`,`id_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_cours` (
  `id_cours` int(11) NOT NULL AUTO_INCREMENT,
  `code_cours` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nom_cours` text COLLATE utf8_unicode_ci NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  PRIMARY KEY (`id_cours`),
  KEY `id_unite_enseignement` (`id_unite_enseignement`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_cours_annees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_cours_annees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_unite_enseignement` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `annee` smallint(4) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1120 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_cours_competences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_cours_competences` (
  `id_competence` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_cours` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `id_annee_formation` int(11) NOT NULL,
  `competences` text NOT NULL,
  UNIQUE KEY `id_competence` (`id_competence`),
  KEY `id_cours` (`id_cours`),
  KEY `annee` (`annee`),
  KEY `id_annee_formation` (`id_annee_formation`),
  CONSTRAINT `amos_cours_competences_ibfk_1` FOREIGN KEY (`id_cours`) REFERENCES `amos_cours` (`id_cours`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_cron_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_cron_etablissement` (
  `id_cron_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `etablissement` varchar(50) NOT NULL,
  `date_cron` date NOT NULL,
  `decision` varchar(60) NOT NULL,
  `is_sent` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_cron_etablissement`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=6217 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_db_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_db_migrations` (
  `version` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_dettes_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_dettes_eleve` (
  `id_dette` int(11) NOT NULL AUTO_INCREMENT,
  `formation` varchar(100) NOT NULL,
  `montants` double NOT NULL,
  `solde_du` double NOT NULL,
  `id_eleve` int(11) NOT NULL,
  PRIMARY KEY (`id_dette`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_diplomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_diplomes` (
  `id_diplome` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom_diplome` varchar(100) NOT NULL,
  UNIQUE KEY `id_diplome` (`id_diplome`),
  UNIQUE KEY `nom_diplome` (`nom_diplome`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_disponibilite_intervenants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_disponibilite_intervenants` (
  `id_disponibilite_intervenants` int(11) NOT NULL AUTO_INCREMENT,
  `id_intervenant` int(11) NOT NULL,
  `id_etablissement` varchar(50) NOT NULL,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime NOT NULL,
  PRIMARY KEY (`id_disponibilite_intervenants`),
  KEY `id_intervenant` (`id_intervenant`),
  KEY `id_disponibilite_intervenants` (`id_disponibilite_intervenants`)
) ENGINE=InnoDB AUTO_INCREMENT=1739 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_document_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_document_eleve` (
  `id_document` int(11) NOT NULL AUTO_INCREMENT,
  `document_eleve` text NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `visible` tinyint(1) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  PRIMARY KEY (`id_document`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_niveau` (`id_niveau`),
  KEY `id_classe` (`id_classe`),
  KEY `id_intervenant` (`id_intervenant`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_ects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_ects` (
  `id_ects` int(11) NOT NULL AUTO_INCREMENT,
  `credit_ects` text NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  PRIMARY KEY (`id_ects`),
  UNIQUE KEY `id_unite_enseignement` (`id_unite_enseignement`,`id_niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_absence_historique_courriel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_absence_historique_courriel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `modele` tinyint(1) NOT NULL,
  `date` datetime NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `fait` tinyint(1) NOT NULL,
  `link` varchar(300) NOT NULL,
  `semestre` int(11) NOT NULL,
  `id_directeur` int(11) NOT NULL,
  `date_reunion` timestamp NULL DEFAULT NULL,
  `time_reunion` varchar(10) NOT NULL,
  `adresse` text NOT NULL,
  `entretien` varchar(5000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_avoirs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_avoirs` (
  `id_avoir` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve_parent` int(11) NOT NULL,
  `reference` char(15) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `id_contrat` int(11) DEFAULT 0,
  `info_supplementaire` text DEFAULT NULL,
  `periode_formation` varchar(100) DEFAULT NULL,
  `nb_heure_periode_formation` decimal(11,2) DEFAULT NULL,
  `nb_heures_absences_injustifiees` decimal(11,2) DEFAULT NULL,
  `id_payeur` int(11) DEFAULT NULL,
  `annee_rentree` smallint(4) DEFAULT NULL,
  `contrat_pro_option` tinyint(1) DEFAULT NULL,
  `montant_formation_du` float DEFAULT NULL,
  PRIMARY KEY (`id_avoir`),
  KEY `reference` (`reference`,`id_eleve`),
  KEY `id_eleve_parent` (`id_eleve_parent`),
  KEY `reference_2` (`reference`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_contrat` (`id_contrat`),
  KEY `id_payeur` (`id_payeur`),
  KEY `annee_rentree` (`annee_rentree`),
  KEY `contrat_pro_option` (`contrat_pro_option`)
) ENGINE=InnoDB AUTO_INCREMENT=365 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_avoirs_echeances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_avoirs_echeances` (
  `id_item` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_avoir` int(11) unsigned NOT NULL,
  `id_cheque_paiement` int(11) unsigned NOT NULL,
  `numero_echeance` varchar(100) NOT NULL,
  `echeance` double NOT NULL,
  `date_echeance` date NOT NULL,
  `montant_a_verser` double NOT NULL,
  `id_eleve` int(11) unsigned NOT NULL,
  `id_niveau` int(11) unsigned NOT NULL,
  UNIQUE KEY `id_item` (`id_item`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_avoirs_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_avoirs_items` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_avoir` int(11) NOT NULL,
  `id_objet_paiement` int(11) NOT NULL,
  `titre_objet_paiement` varchar(250) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `montant` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  KEY `id_avoir` (`id_avoir`,`id_objet_paiement`,`id_eleve`,`id_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=377 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_bulletin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_bulletin` (
  `id_bls` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `id_referentiel` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `session` int(11) NOT NULL,
  `commentaire` text NOT NULL,
  `decision_jury` text NOT NULL,
  `bulletin_json` longtext NOT NULL,
  `est_publie` text NOT NULL,
  `date_create` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_bls` (`id_bls`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_referentiel` (`id_referentiel`),
  KEY `annee` (`annee`),
  KEY `semestre` (`semestre`),
  KEY `session` (`session`)
) ENGINE=InnoDB AUTO_INCREMENT=2496 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_deplacements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_deplacements` (
  `id_deplacement` int(11) NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `date_aller` timestamp NULL DEFAULT NULL,
  `date_retour` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `statut` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_deplacement`),
  KEY `statut` (`statut`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_classe` (`id_classe`),
  KEY `id_eleve` (`id_eleve`),
  KEY `date_aller` (`date_aller`,`date_retour`),
  KEY `semestre` (`semestre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_experiances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_experiances` (
  `id_eleve_experiance` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `entreprise` varchar(30) NOT NULL,
  `date_debut` varchar(10) NOT NULL,
  `date_fin` varchar(10) NOT NULL,
  `post_occupe` varchar(250) NOT NULL,
  `principales_missions` text NOT NULL,
  `principales_responsabilites` text NOT NULL,
  PRIMARY KEY (`id_eleve_experiance`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=13370 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_factures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_factures` (
  `id_facture` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve_parent` int(11) NOT NULL,
  `reference` char(15) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `id_contrat` int(11) DEFAULT NULL,
  `info_supplementaire` text DEFAULT NULL,
  `periode_formation` varchar(100) DEFAULT NULL,
  `nb_heure_periode_formation` decimal(11,2) DEFAULT NULL,
  `nb_heures_absences_injustifiees` decimal(11,2) DEFAULT NULL,
  `id_payeur` int(11) DEFAULT NULL,
  `contrat_pro_option` tinyint(4) DEFAULT NULL,
  `new_montant_formation` float DEFAULT 0,
  `montant_formation_du` float DEFAULT NULL,
  PRIMARY KEY (`id_facture`),
  KEY `reference` (`reference`,`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=3209 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_factures_echeances`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_factures_echeances` (
  `id_facture_echeance` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_facture` int(11) unsigned NOT NULL,
  `id_cheque_paiement` int(11) unsigned NOT NULL,
  UNIQUE KEY `id_facture_echeance` (`id_facture_echeance`)
) ENGINE=InnoDB AUTO_INCREMENT=1929 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_factures_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_factures_items` (
  `id_item` int(11) NOT NULL AUTO_INCREMENT,
  `id_facture` int(11) NOT NULL,
  `id_objet_paiement` int(11) NOT NULL,
  `titre_objet_paiement` varchar(300) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `montant` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  KEY `id_facture` (`id_facture`,`id_objet_paiement`,`id_eleve`,`id_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=1873 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_factures_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_factures_old` (
  `id_facture` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve_parent` int(11) NOT NULL,
  `reference` char(15) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id_facture`),
  KEY `reference` (`reference`,`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_formations` (
  `id_eleve_formation` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `intitule_formation` varchar(80) NOT NULL,
  `lieu_formation` varchar(60) NOT NULL,
  `date_formation` varchar(7) NOT NULL,
  `niveau_formation` varchar(10) NOT NULL,
  `diplome_formation` varchar(80) NOT NULL,
  PRIMARY KEY (`id_eleve_formation`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_infos_parents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_infos_parents` (
  `id_infos_parents` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom_parent` varchar(100) DEFAULT NULL,
  `prenom_parent` varchar(100) DEFAULT NULL,
  `email_parent` varchar(100) DEFAULT NULL,
  `telephone_personnel_parent` varchar(100) DEFAULT NULL,
  `telephone_pro_parent` varchar(100) DEFAULT NULL,
  `profession_parent` varchar(100) DEFAULT NULL,
  `nom_entreprise_parent` varchar(100) DEFAULT NULL,
  `code_postal_parent` varchar(100) DEFAULT NULL,
  `ville_parent` varchar(100) DEFAULT NULL,
  `pays_parent` varchar(100) DEFAULT NULL,
  `adresse_parent` varchar(100) DEFAULT NULL,
  `lien_parente` varchar(100) DEFAULT NULL,
  `position_formulaire` tinyint(1) NOT NULL,
  `id_famille_des_sources` int(11) unsigned DEFAULT NULL,
  `id_source` int(11) unsigned DEFAULT NULL,
  `autres` varchar(60) DEFAULT NULL,
  `id_eleve` int(11) unsigned NOT NULL,
  UNIQUE KEY `id_infos_parents` (`id_infos_parents`),
  KEY `id_famille_des_sources` (`id_famille_des_sources`),
  KEY `id_source` (`id_source`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=5502 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_langues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_langues` (
  `id_eleve_langue` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `langue` varchar(20) NOT NULL,
  `nb_annee_etudes_langue` varchar(10) NOT NULL,
  `parle_langue` tinyint(1) NOT NULL,
  `lue_langue` tinyint(1) NOT NULL,
  `ecrite_langue` tinyint(1) NOT NULL,
  `diplome_langue` varchar(160) NOT NULL,
  PRIMARY KEY (`id_eleve_langue`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=9615 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_livret`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_livret` (
  `id_livret` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `semestre` tinyint(1) NOT NULL,
  `session` tinyint(1) NOT NULL,
  `status` bit(1) NOT NULL,
  `creation` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_livret` (`id_livret`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `annee` (`annee`),
  KEY `id_classe` (`id_classe`),
  KEY `semestre` (`semestre`),
  KEY `session` (`session`),
  KEY `status` (`status`),
  CONSTRAINT `amos_eleve_livret_ibfk_1` FOREIGN KEY (`id_eleve`) REFERENCES `amos_eleves` (`id_eleve`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_eleve_livret_ibfk_2` FOREIGN KEY (`id_etablissement`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_eleve_livret_ibfk_3` FOREIGN KEY (`id_classe`) REFERENCES `amos_classe` (`id_classe`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_niveaux_langues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_niveaux_langues` (
  `id_eleve_niveaux_langues` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_niveau_langue` int(11) NOT NULL,
  PRIMARY KEY (`id_eleve_niveaux_langues`),
  KEY `id_eleve` (`id_eleve`,`id_niveau_langue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_niveaux_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_niveaux_options` (
  `id_eleve_niveaux_options` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_niveau_option` int(11) NOT NULL,
  PRIMARY KEY (`id_eleve_niveaux_options`),
  KEY `id_eleve` (`id_eleve`,`id_niveau_option`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_paiements_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_paiements_options` (
  `id_eleve_paiements_options` int(11) NOT NULL AUTO_INCREMENT,
  `id_paiement_eleve` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_niveau_option` int(11) NOT NULL,
  `montant` float DEFAULT NULL,
  PRIMARY KEY (`id_eleve_paiements_options`),
  KEY `id_paiement_eleve` (`id_paiement_eleve`,`id_eleve`,`id_niveau_option`),
  KEY `id_paiement_eleve_2` (`id_paiement_eleve`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_niveau_option` (`id_niveau_option`)
) ENGINE=InnoDB AUTO_INCREMENT=16278 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_reglements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_reglements` (
  `id_eleve_reglement` int(11) NOT NULL AUTO_INCREMENT,
  `id_saisie` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `transaction_id` double NOT NULL,
  `titre` varchar(80) NOT NULL,
  `mode` varchar(30) NOT NULL,
  `date` datetime NOT NULL,
  `montant` float NOT NULL,
  `nom_banque` varchar(100) NOT NULL,
  `numero_cheque` varchar(50) NOT NULL,
  `commentaires` varchar(250) NOT NULL,
  `photo_cheque` varchar(255) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `archive` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_eleve_reglement`),
  KEY `id_saisie` (`id_saisie`),
  KEY `id_eleve` (`id_eleve`) USING BTREE,
  KEY `transaction_id` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_sejours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_sejours` (
  `id_eleve_sejour` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `pays_sejour` varchar(30) NOT NULL,
  `type_sejour` varchar(30) NOT NULL,
  `titule_etudes` varchar(180) NOT NULL,
  PRIMARY KEY (`id_eleve_sejour`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleve_sports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleve_sports` (
  `id_eleve_sport` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `sport` varchar(30) NOT NULL,
  `niveau` int(1) NOT NULL,
  `palmares` varchar(200) NOT NULL,
  PRIMARY KEY (`id_eleve_sport`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=5136 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleves` (
  `id_eleve` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve_parent` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `lang_maternelle` varchar(20) NOT NULL,
  `situation_famille` varchar(20) NOT NULL,
  `avoir_enfants` enum('oui','non') NOT NULL DEFAULT 'non',
  `nbr_enfants` varchar(2) NOT NULL,
  `situation_actuelle` varchar(20) NOT NULL,
  `bac_obtenu` varchar(50) DEFAULT NULL,
  `bac_obtenu_encours` varchar(40) NOT NULL,
  `class_actuelle` varchar(50) DEFAULT NULL,
  `situation_actuelle_autre` varchar(60) NOT NULL,
  `photo` varchar(250) NOT NULL,
  `formations_complementaires` text NOT NULL,
  `duree_experience_pro` varchar(20) NOT NULL,
  `unite_experience_pro` enum('mois','années') NOT NULL,
  `motivations` text NOT NULL,
  `competences` text NOT NULL,
  `autres_competences` text NOT NULL,
  `qualites` text NOT NULL,
  `defauts` text NOT NULL,
  `commentaire` text NOT NULL,
  `portfolio_url` varchar(255) DEFAULT NULL,
  `portfolio` varchar(255) DEFAULT NULL,
  `carte_identite` text NOT NULL,
  `diplomes` text NOT NULL,
  `releve_bac` text DEFAULT NULL,
  `releves_notes` text NOT NULL,
  `cv` text NOT NULL,
  `lettre_motivation` text NOT NULL,
  `carte_vitale` text DEFAULT NULL,
  `attestation_recensement` text DEFAULT NULL,
  `autres` text DEFAULT NULL,
  `date_depot` datetime NOT NULL,
  `profil` enum('candidat','eleve','alumni','reinscrit','abandon') NOT NULL DEFAULT 'candidat',
  `id_niveau` int(11) NOT NULL,
  `id_niveau_future` int(11) NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `rappel_paiement` tinyint(4) NOT NULL,
  `dernier_epreuve` int(11) NOT NULL,
  `compteur_epreuve` tinyint(2) NOT NULL,
  `reaffectation_manuelle_epreuve` tinyint(1) NOT NULL,
  `rappel_epreuve` tinyint(1) NOT NULL,
  `echelonnement` tinyint(1) NOT NULL,
  `presence_eleve` tinyint(1) NOT NULL,
  `paiement_formation` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'Non Payé',
  `visible` tinyint(1) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `paiement_valide` tinyint(1) NOT NULL,
  `attente_epreuve` tinyint(1) NOT NULL,
  `visible_attente_epreuve` tinyint(1) NOT NULL,
  `importe` tinyint(1) NOT NULL,
  `numero_social` varchar(20) NOT NULL,
  `cacher_encaissement` tinyint(1) NOT NULL,
  `num_facture` varchar(20) NOT NULL,
  `groupes` varchar(250) NOT NULL,
  `specialisations` varchar(250) NOT NULL,
  `montant_formation` varchar(20) NOT NULL,
  `deplacement` int(1) NOT NULL DEFAULT 0,
  `date_inscription` datetime NOT NULL,
  `id_source_informations` int(11) DEFAULT NULL,
  `id_famille_sources_informations` int(11) DEFAULT NULL,
  `date_debut_exclusion` date DEFAULT NULL,
  `date_fin_exclusion` date DEFAULT NULL,
  `espace_user` tinyint(4) DEFAULT 1,
  `rythme_alternance` varchar(25) DEFAULT NULL,
  `entreprise_alternance` varchar(25) DEFAULT NULL,
  `alternance_en_attente` int(5) DEFAULT NULL,
  `ioa` int(1) DEFAULT NULL,
  `alternance_en_cours` int(1) DEFAULT NULL,
  `jpo` tinyint(1) DEFAULT NULL,
  `nom_responsable` varchar(255) DEFAULT NULL,
  `prenom_responsable` varchar(255) DEFAULT NULL,
  `email_responsable` varchar(255) DEFAULT NULL,
  `telephone_responsable` varchar(30) DEFAULT NULL,
  `permis_conduire` bit(1) DEFAULT NULL,
  `vehicule` bit(1) DEFAULT b'0',
  `handicap` int(1) DEFAULT 0,
  `id_motif_abandon_scolarite` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_eleve`),
  KEY `id_contact` (`id_contact`,`id_niveau`,`id_classe`),
  KEY `id_niveau_future` (`id_niveau_future`),
  KEY `num_facture` (`num_facture`),
  KEY `deplacement` (`deplacement`),
  KEY `idx_visible` (`visible`),
  KEY `id_eleve_parent` (`id_eleve_parent`),
  KEY `id_contact_2` (`id_contact`),
  KEY `id_classe` (`id_classe`),
  KEY `id_niveau` (`id_niveau`),
  KEY `id_niveau_future_2` (`id_niveau_future`),
  KEY `presence_eleve` (`presence_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=44708 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `maut_sync_eleve` AFTER UPDATE ON `amos_eleves` FOR EACH ROW BEGIN  

IF (SELECT con.mautic_id FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
IS NOT NULL AND NEW.id_eleve = NEW.id_eleve_parent
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
    ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
    (
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = (SELECT mel.id_contact FROM miracle.amos_eleves mel WHERE mel.id_eleve = NEW.id_eleve)
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_eleves_groupes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleves_groupes` (
  `id_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `nom_groupe` varchar(150) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_groupe`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleves_groupes_refs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_eleves_groupes_refs` (
  `id_groupes_refs` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  PRIMARY KEY (`id_groupes_refs`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_groupe` (`id_groupe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_eleves_paiement_somme`;
/*!50001 DROP VIEW IF EXISTS `amos_eleves_paiement_somme`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `amos_eleves_paiement_somme` AS SELECT 
 1 AS `id_eleve`,
 1 AS `montant_formation`,
 1 AS `somme`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `amos_emails_one_shot`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_emails_one_shot` (
  `id_email` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `id_template_mautic` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `id_email` (`id_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises` (
  `id_entreprise` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) DEFAULT NULL,
  `type_entreprise` enum('Entreprise mère','Entreprise','OPCO') DEFAULT 'Entreprise mère',
  `tel_country` varchar(100) DEFAULT NULL,
  `nom_entreprise` varchar(250) NOT NULL,
  `adresse` text DEFAULT NULL,
  `code_postal` varchar(20) DEFAULT NULL,
  `ville` varchar(20) DEFAULT NULL,
  `pays` varchar(50) DEFAULT NULL,
  `telephone` varchar(20) NOT NULL,
  `email` varchar(250) NOT NULL,
  `id_etablissement` int(11) DEFAULT NULL,
  `site_web` varchar(500) DEFAULT NULL,
  `contact` varchar(150) DEFAULT NULL,
  `information_complementaire` text NOT NULL,
  `code_country` varchar(3) DEFAULT NULL,
  `place_id` varchar(1000) DEFAULT NULL,
  `id_secteur` int(11) DEFAULT NULL,
  `siret` bigint(21) DEFAULT NULL,
  `numero_convention_collective` int(11) DEFAULT NULL,
  `numero_adherent_opco` int(11) DEFAULT NULL,
  `numero_tva` varchar(20) DEFAULT NULL,
  `id_entreprises_source` int(11) DEFAULT NULL,
  `id_entreprises_recherche` int(11) DEFAULT NULL,
  `group_principal` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp(),
  `id_admin_proprietaire` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_entreprise`),
  KEY `id_etablissement` (`id_etablissement`),
  CONSTRAINT `amos_entreprises_ibfk_1` FOREIGN KEY (`id_etablissement`) REFERENCES `amos_etablissement` (`id_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=1070 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_contacts` (
  `id_entreprises_contacts` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_entreprise` int(11) NOT NULL,
  `civilite` varchar(10) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `telephone` varchar(100) NOT NULL,
  `tel_country_contact` varchar(100) NOT NULL,
  `code_country_contact` varchar(25) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `id_poste` int(11) NOT NULL,
  `id_type_contact` int(11) NOT NULL,
  `commentaire` text DEFAULT NULL,
  `mautic_id` int(11) DEFAULT NULL,
  `date_de_creation` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_entreprises_contacts` (`id_entreprises_contacts`)
) ENGINE=InnoDB AUTO_INCREMENT=1036 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_departements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_departements` (
  `id_entreprises_departements` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `departement` varchar(100) NOT NULL,
  UNIQUE KEY `id_entreprises_departements` (`id_entreprises_departements`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_eleves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_eleves` (
  `id_entreprises_eleves` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_entreprise` int(11) unsigned NOT NULL,
  `id_eleve` int(11) unsigned NOT NULL,
  `id_eleve_parent` int(25) DEFAULT NULL,
  `annee_scolaire` varchar(50) NOT NULL,
  `id_entreprises_type_contrat` int(11) unsigned NOT NULL,
  `taux_horaire_formation` float NOT NULL,
  `montant_formation` float NOT NULL,
  `id_tuteur` int(11) NOT NULL,
  `id_departement` int(11) NOT NULL,
  `id_poste` int(11) NOT NULL,
  `id_opco` int(11) DEFAULT NULL,
  `num_dossier` varchar(30) DEFAULT NULL,
  `montant_formation_opco` double DEFAULT NULL,
  `taux_horaire_opco` double DEFAULT NULL,
  `cout_branche` double DEFAULT NULL,
  `id_contact_opco` int(11) DEFAULT NULL,
  `accord_opco` tinyint(1) DEFAULT NULL,
  `contrat_signe` tinyint(1) NOT NULL,
  `info_supplementaire` varchar(255) DEFAULT NULL,
  `periode_contrat` varchar(255) NOT NULL,
  `periode_formation` varchar(255) NOT NULL,
  `periode_formation_annuelle` varchar(255) DEFAULT NULL,
  `nb_heure_periode_formation_annuelle` decimal(11,2) DEFAULT NULL,
  UNIQUE KEY `id_entreprises_eleves` (`id_entreprises_eleves`)
) ENGINE=InnoDB AUTO_INCREMENT=660 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_eleves_periodes_trimestrielles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_eleves_periodes_trimestrielles` (
  `id_periode` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `periode` varchar(255) NOT NULL,
  `nb_heure` decimal(11,2) DEFAULT NULL,
  `id_entreprises_eleves` int(11) NOT NULL,
  UNIQUE KEY `id_periode` (`id_periode`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_eleves_pieces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_eleves_pieces` (
  `id_piece` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_entreprises_eleves` int(11) unsigned NOT NULL,
  `id_type_piece` int(11) unsigned NOT NULL,
  `path` varchar(255) NOT NULL,
  `file` varchar(255) NOT NULL,
  `periode` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id_piece` (`id_piece`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_eleves_types_piece`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_eleves_types_piece` (
  `id_type_piece` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `periode_associee` tinyint(1) NOT NULL,
  UNIQUE KEY `id_type_piece` (`id_type_piece`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_familles_des_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_familles_des_sources` (
  `id_entreprises_famille` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_entreprises_famille` (`id_entreprises_famille`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_gestions_tva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_gestions_tva` (
  `id_entreprises_gestion_tva` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) unsigned NOT NULL,
  `gestion` tinyint(1) unsigned NOT NULL,
  `taux` double unsigned NOT NULL,
  UNIQUE KEY `id_entreprises_gestion_tva` (`id_entreprises_gestion_tva`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_missions_professionnelles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_missions_professionnelles` (
  `id_entreprises_missions_professionnelles` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_entreprise` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `id_type_mission` int(11) NOT NULL,
  `id_departement` int(11) NOT NULL,
  `id_poste` int(11) NOT NULL,
  `duree_reelle` int(11) NOT NULL,
  `duree_academique` int(11) NOT NULL,
  `credits` int(11) NOT NULL,
  `ects_finaux` int(11) NOT NULL,
  `information_complementaire` text DEFAULT NULL,
  `files_mission` varchar(255) NOT NULL,
  `date_debut` datetime NOT NULL DEFAULT current_timestamp(),
  `date_fin` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_entreprises_missions_professionnelles` (`id_entreprises_missions_professionnelles`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_portail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_portail` (
  `id_entreprises_portail` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_entreprise` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `connexion` datetime NOT NULL,
  `token` varchar(100) NOT NULL,
  `valide` int(1) NOT NULL,
  UNIQUE KEY `id_entreprises_portail` (`id_entreprises_portail`)
) ENGINE=InnoDB AUTO_INCREMENT=1064 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_postes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_postes` (
  `id_entreprises_postes` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_poste` varchar(100) NOT NULL,
  UNIQUE KEY `id_entreprises_postes` (`id_entreprises_postes`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_recherches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_recherches` (
  `id_entreprises_recherche` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_recherche` varchar(100) NOT NULL,
  UNIQUE KEY `id_entreprises_recherche` (`id_entreprises_recherche`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_secteurs_activites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_secteurs_activites` (
  `id_entreprises_secteurs_activites` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_secteur` varchar(100) NOT NULL,
  UNIQUE KEY `id_entreprises_secteurs_activites` (`id_entreprises_secteurs_activites`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_sources` (
  `id_entreprises_source` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(100) NOT NULL,
  `id_entreprises_famille` int(11) NOT NULL,
  UNIQUE KEY `id_entreprises_source` (`id_entreprises_source`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_statuts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_statuts` (
  `id_entreprises_statuts` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(200) NOT NULL,
  `libelle_front` varchar(200) NOT NULL,
  `code_status` int(11) NOT NULL,
  `score` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_entreprises_statuts` (`id_entreprises_statuts`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_taches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_taches` (
  `id_entreprises_taches` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) NOT NULL,
  `id_admin_assigne` int(11) NOT NULL,
  `id_service_assigne` int(11) NOT NULL,
  `id_entreprise_assigne` int(11) NOT NULL,
  `valid` int(11) NOT NULL,
  `type_tache` varchar(10) NOT NULL,
  `objet` varchar(100) NOT NULL,
  `commentaire` varchar(255) NOT NULL,
  `telephone` varchar(100) NOT NULL,
  `tel_country_contact` varchar(100) NOT NULL,
  `score` int(11) NOT NULL,
  `id_type_tache` int(11) NOT NULL,
  `id_statut_contact` int(11) NOT NULL,
  `id_statut_tache` int(11) NOT NULL,
  `archive` int(11) NOT NULL,
  `lieu` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `deadline` datetime NOT NULL DEFAULT current_timestamp(),
  `deadline2` datetime NOT NULL DEFAULT current_timestamp(),
  `date_realisation` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_entreprises_taches` (`id_entreprises_taches`)
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_traces`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_traces` (
  `id_entreprises_traces` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_entreprise` int(11) NOT NULL,
  `trace` text NOT NULL,
  `espace` varchar(100) NOT NULL,
  `utilisateur` varchar(200) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_entreprises_traces` (`id_entreprises_traces`)
) ENGINE=InnoDB AUTO_INCREMENT=2632 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_types_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_types_contacts` (
  `id_type_contact` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  UNIQUE KEY `id_type_contact` (`id_type_contact`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_types_contrat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_types_contrat` (
  `id_entreprises_type_contrat` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type_contrat` varchar(255) NOT NULL,
  UNIQUE KEY `id_entreprises_type_contrat` (`id_entreprises_type_contrat`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_entreprises_types_missions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_entreprises_types_missions` (
  `id_entreprises_types_missions` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `type_mission` varchar(100) NOT NULL,
  UNIQUE KEY `id_entreprises_types_missions` (`id_entreprises_types_missions`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_envois_rappels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_envois_rappels` (
  `id_eleve` int(11) unsigned NOT NULL,
  `rappel_admission_1` tinyint(1) NOT NULL DEFAULT 0,
  `rappel_admission_2` tinyint(1) NOT NULL DEFAULT 0,
  `rappel_admission_3` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_epreuves_admission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_epreuves_admission` (
  `id_epreuve_admission` int(11) NOT NULL AUTO_INCREMENT,
  `date_epreuve` datetime NOT NULL,
  `lieu` varchar(16) NOT NULL,
  `effectif` int(2) NOT NULL,
  `distanciel` int(1) NOT NULL DEFAULT 0,
  `url_distanciel` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id_epreuve_admission`),
  KEY `idx_date_epreuve` (`date_epreuve`)
) ENGINE=InnoDB AUTO_INCREMENT=2952 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_epreuves_admission_eleves`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_epreuves_admission_eleves` (
  `id_epreuve_admission_eleve` int(11) NOT NULL AUTO_INCREMENT,
  `id_epreuve_admission` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `presence` tinyint(1) NOT NULL,
  `date_operation` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_epreuve_admission_eleve`),
  KEY `id_epreuve_admission` (`id_epreuve_admission`),
  KEY `id_eleve` (`id_eleve`),
  KEY `presence` (`presence`)
) ENGINE=InnoDB AUTO_INCREMENT=36375 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `maut_sync_lead_epreuves` AFTER INSERT ON `amos_epreuves_admission_eleves` FOR EACH ROW BEGIN  

IF (SELECT con.mautic_id FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
IS NOT NULL AND (SELECT con.id_contact FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve) = (SELECT con.id_contact_parent FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
    ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
    (
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = (SELECT mel.id_contact FROM miracle.amos_eleves mel WHERE mel.id_eleve = NEW.id_eleve)
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_epreuves_admission_formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_epreuves_admission_formations` (
  `id__epreuves_admission_formation` int(11) NOT NULL AUTO_INCREMENT,
  `id_epreuve_admission` int(11) NOT NULL,
  `id_formation` int(11) NOT NULL,
  PRIMARY KEY (`id__epreuves_admission_formation`),
  UNIQUE KEY `id_epreuve_admission` (`id_epreuve_admission`,`id_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=16886 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_epreuves_admission_limit3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_epreuves_admission_limit3` (
  `id_epreuve_admission` int(11) NOT NULL DEFAULT 0,
  `date_epreuve` datetime NOT NULL,
  `lieu` varchar(16) CHARACTER SET latin1 NOT NULL,
  `effectif` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_etablissement` (
  `id_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `nom_etablissement` varchar(100) CHARACTER SET latin1 NOT NULL,
  `code_ville` char(2) COLLATE utf8_unicode_ci NOT NULL,
  `adresse` varchar(600) COLLATE utf8_unicode_ci NOT NULL,
  `visible` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_etablissement_regle_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_etablissement_regle_session` (
  `id_rules` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `moyenne_UE_inf_declenchement_annee_1` float NOT NULL DEFAULT 10,
  `moyenne_UE_inf_declenchement_annee_2` float NOT NULL DEFAULT 10,
  `moyenne_UE_inf_declenchement_annee_3` float NOT NULL DEFAULT 10,
  `moyenne_UE_inf_declenchement_annee_4` float NOT NULL DEFAULT 10,
  `moyenne_UE_inf_declenchement_annee_5` float NOT NULL DEFAULT 10,
  UNIQUE KEY `id_rules` (`id_rules`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_etablissement_regles_assiduite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_etablissement_regles_assiduite` (
  `id_regle_assiduite` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `nom_regle_assiduite` text NOT NULL,
  UNIQUE KEY `id_regle_assiduite` (`id_regle_assiduite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_etablissements_niveaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_etablissements_niveaux` (
  `id_etablissement` int(11) unsigned NOT NULL,
  `id_niveau` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_evaluation_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_evaluation_eleve` (
  `id_evaluation` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `note` double NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `coefficient` int(11) NOT NULL,
  `type_evaluation` varchar(5) NOT NULL,
  `semestre` int(11) NOT NULL,
  `commentaire_evaluation` text NOT NULL,
  PRIMARY KEY (`id_evaluation`),
  KEY `id_eleve` (`id_eleve`,`id_etablissement`,`id_niveau`,`id_classe`,`id_matiere`,`id_cours`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) COLLATE utf8_spanish_ci DEFAULT NULL,
  `class` varchar(45) COLLATE utf8_spanish_ci NOT NULL DEFAULT 'info',
  `start` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `end` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  `color` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_intervenant` (`id_intervenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_familles_des_sources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_familles_des_sources` (
  `id_famille` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `id_famille` (`id_famille`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_files_activite_intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_files_activite_intervenant` (
  `id_file_activite` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_activite_intervenant` int(11) NOT NULL,
  `fichier` varchar(200) NOT NULL,
  UNIQUE KEY `id_file_activite` (`id_file_activite`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_filieres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_filieres` (
  `id_filiere` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(50) NOT NULL,
  UNIQUE KEY `id_filiere` (`id_filiere`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_formations` (
  `id_formation` int(11) NOT NULL AUTO_INCREMENT,
  `niveau` varchar(100) NOT NULL,
  `description` varchar(100) NOT NULL,
  `priorite` int(1) NOT NULL,
  PRIMARY KEY (`id_formation`),
  FULLTEXT KEY `niveau` (`niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant` (
  `id_intervenant` int(11) NOT NULL AUTO_INCREMENT,
  `civilite` varchar(10) CHARACTER SET latin1 NOT NULL,
  `nom` varchar(100) CHARACTER SET latin1 NOT NULL,
  `prenom` varchar(100) CHARACTER SET latin1 NOT NULL,
  `email` varchar(100) CHARACTER SET latin1 NOT NULL,
  `email_office` varchar(60) CHARACTER SET utf8 NOT NULL,
  `date_naissance` date NOT NULL,
  `id_nationalite` int(11) NOT NULL,
  `id_langue` char(3) CHARACTER SET latin1 NOT NULL,
  `telephone` varchar(50) CHARACTER SET latin1 NOT NULL,
  `mobile` varchar(50) CHARACTER SET latin1 NOT NULL,
  `adresse` varchar(250) CHARACTER SET latin1 NOT NULL,
  `code_postal` varchar(50) CHARACTER SET latin1 NOT NULL,
  `ville` varchar(50) CHARACTER SET latin1 NOT NULL,
  `id_pays` int(11) NOT NULL,
  `formation_suivie_intitule` varchar(100) CHARACTER SET latin1 NOT NULL,
  `niveau_formation_suivie` varchar(100) CHARACTER SET latin1 NOT NULL,
  `lieu_formation_suivie` varchar(100) CHARACTER SET latin1 NOT NULL,
  `profession` varchar(100) CHARACTER SET latin1 NOT NULL,
  `cv` varchar(50) CHARACTER SET latin1 NOT NULL,
  `photo` varchar(50) CHARACTER SET latin1 NOT NULL,
  `id_societe` int(11) NOT NULL,
  `poste_actuel` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `signature` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_intervenant`),
  KEY `id_nationalite` (`id_nationalite`,`id_pays`,`id_societe`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_competences`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_competences` (
  `id_competence` int(11) NOT NULL AUTO_INCREMENT,
  `nom_competence` varchar(200) NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  PRIMARY KEY (`id_competence`),
  KEY `id_intervenant` (`id_intervenant`)
) ENGINE=InnoDB AUTO_INCREMENT=220 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_cours` (
  `id_cours_intervenant` int(11) NOT NULL AUTO_INCREMENT,
  `id_cours` int(11) NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  PRIMARY KEY (`id_cours_intervenant`)
) ENGINE=InnoDB AUTO_INCREMENT=1821 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_cours_autres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_cours_autres` (
  `id_intervenant_cours_autres` int(11) NOT NULL AUTO_INCREMENT,
  `id_intervenant` int(11) NOT NULL,
  `nom_cours` varchar(100) NOT NULL,
  PRIMARY KEY (`id_intervenant_cours_autres`),
  KEY `id_intervenant` (`id_intervenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_diplomes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_diplomes` (
  `id_diplome` int(11) NOT NULL AUTO_INCREMENT,
  `titre_diplome` varchar(100) NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  PRIMARY KEY (`id_diplome`),
  KEY `id_intervenant` (`id_intervenant`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_documents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_intervenant` int(11) NOT NULL,
  `document` varchar(300) NOT NULL,
  `size` int(11) NOT NULL,
  `extension` varchar(10) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_intervenant` (`id_intervenant`),
  KEY `document` (`document`),
  CONSTRAINT `amos_intervenant_documents_ibfk_1` FOREIGN KEY (`id_intervenant`) REFERENCES `amos_intervenant` (`id_intervenant`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_etablissement` (
  `id_intervenant_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `id_intervenant` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  PRIMARY KEY (`id_intervenant_etablissement`),
  KEY `id_intervenant` (`id_intervenant`,`id_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=607 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_secteur_activite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_secteur_activite` (
  `id_secteur_activite` int(11) NOT NULL AUTO_INCREMENT,
  `nom_secteur_activite` varchar(200) NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  PRIMARY KEY (`id_secteur_activite`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_intervenant_societe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_intervenant_societe` (
  `id_societe` int(11) NOT NULL AUTO_INCREMENT,
  `raison_sociale` varchar(500) NOT NULL,
  `adresse_societe` varchar(200) NOT NULL,
  `tel_societe` varchar(100) NOT NULL,
  `fax_societe` varchar(100) NOT NULL,
  `email_societe` varchar(100) NOT NULL,
  `id_secteur_activite` int(11) NOT NULL,
  PRIMARY KEY (`id_societe`),
  KEY `id_secteur_activite` (`id_secteur_activite`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_keys`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_langues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_langues` (
  `id_langue` char(3) NOT NULL COMMENT 'ISO 639-2 Code',
  `en` varchar(60) DEFAULT NULL,
  `fr` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id_langue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_locks` (
  `id_lock` int(11) NOT NULL AUTO_INCREMENT,
  `module` varchar(50) NOT NULL,
  `page` varchar(100) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `SESSIONID` varchar(100) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id_lock`)
) ENGINE=InnoDB AUTO_INCREMENT=5748 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_locks_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_locks_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` text NOT NULL,
  `SESSIONID` varchar(50) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1571 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_matiere`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_matiere` (
  `id_matiere` int(11) NOT NULL AUTO_INCREMENT,
  `code_matiere` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `nom_matiere` text COLLATE utf8_unicode_ci NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  PRIMARY KEY (`id_matiere`),
  KEY `id_unite_enseignement` (`id_unite_enseignement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_matiere_intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_matiere_intervenant` (
  `id_matiere` int(11) NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  KEY `id_matiere` (`id_matiere`,`id_intervenant`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_modes_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_modes_paiement` (
  `id_mode_paiement` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `system` bit(1) NOT NULL DEFAULT b'0',
  UNIQUE KEY `id_mode_paiement` (`id_mode_paiement`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_module_type_cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_module_type_cours` (
  `id_unite_enseignement` int(11) NOT NULL,
  `id_type_cours` int(11) NOT NULL,
  `value_horaire` float NOT NULL,
  KEY `id_unite_enseignement` (`id_unite_enseignement`,`id_type_cours`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_motifs_abandon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_motifs_abandon` (
  `id_motif` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `id_motif` (`id_motif`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_motifs_abandon_scolarite`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_motifs_abandon_scolarite` (
  `id_motif_abandon_scolarite` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_motif_abandon_scolarite` (`id_motif_abandon_scolarite`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_motifs_refus_candidat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_motifs_refus_candidat` (
  `id_motif_refus` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `id_motif_refus` (`id_motif_refus`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_nationalites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_nationalites` (
  `id_nationalite` int(11) NOT NULL AUTO_INCREMENT,
  `code_pays` varchar(3) NOT NULL,
  `fr` varchar(250) NOT NULL,
  PRIMARY KEY (`id_nationalite`)
) ENGINE=InnoDB AUTO_INCREMENT=199 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_niveaux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_niveaux` (
  `id_niveau` int(11) NOT NULL AUTO_INCREMENT,
  `code_niveau` varchar(50) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `id_diplome` int(11) DEFAULT NULL,
  `nom_niveau` varchar(64) NOT NULL,
  `deuxieme_langue` tinyint(1) NOT NULL,
  `id_niveau_future` int(11) NOT NULL,
  PRIMARY KEY (`id_niveau`),
  KEY `id_formation` (`id_formation`),
  KEY `id_niveau_future` (`id_niveau_future`),
  KEY `idx_nom_niveau` (`nom_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_niveaux_echelonnements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_niveaux_echelonnements` (
  `id_niveau_echelonnement` int(11) NOT NULL AUTO_INCREMENT,
  `id_niveau` int(11) NOT NULL,
  `date` varchar(10) NOT NULL,
  PRIMARY KEY (`id_niveau_echelonnement`),
  KEY `id_niveau` (`id_niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_niveaux_echelonnements_lines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_niveaux_echelonnements_lines` (
  `id_niveau_echelonnement_line` int(11) NOT NULL AUTO_INCREMENT,
  `id_niveau_echelonnement` int(11) NOT NULL,
  `titre` varchar(64) NOT NULL,
  `montant` int(2) NOT NULL,
  `ordre_de` varchar(64) NOT NULL,
  `options` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_niveau_echelonnement_line`),
  KEY `id_niveau_echelonnement` (`id_niveau_echelonnement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_niveaux_frais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_niveaux_frais` (
  `id_niveau_option` int(11) NOT NULL AUTO_INCREMENT,
  `id_niveau` int(11) NOT NULL,
  `titre` varchar(64) NOT NULL,
  `montant` int(2) NOT NULL,
  `ordre` int(1) NOT NULL,
  PRIMARY KEY (`id_niveau_option`),
  KEY `id_niveau` (`id_niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_niveaux_langues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_niveaux_langues` (
  `id_niveau_langue` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(64) NOT NULL,
  `montant` int(2) NOT NULL,
  PRIMARY KEY (`id_niveau_langue`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_niveaux_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_niveaux_options` (
  `id_niveau_option` int(11) NOT NULL AUTO_INCREMENT,
  `id_niveau` int(11) NOT NULL,
  `id_objet_paiement` int(11) NOT NULL,
  `titre` varchar(64) NOT NULL,
  `montant` decimal(11,2) DEFAULT NULL,
  `ordre` int(1) NOT NULL,
  `annee` int(11) NOT NULL,
  PRIMARY KEY (`id_niveau_option`),
  KEY `id_niveau` (`id_niveau`),
  KEY `id_objet_paiement` (`id_objet_paiement`),
  KEY `annee` (`annee`)
) ENGINE=InnoDB AUTO_INCREMENT=525 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_objet_paiement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_objet_paiement` (
  `id_objet_paiement` int(11) NOT NULL AUTO_INCREMENT,
  `objet_paiement` varchar(255) NOT NULL,
  `reference` varchar(20) CHARACTER SET utf8 DEFAULT NULL,
  `type` varchar(50) NOT NULL,
  PRIMARY KEY (`id_objet_paiement`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_options`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option` varchar(30) NOT NULL,
  `valeur` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_paiement_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_paiement_eleve` (
  `id_paiement_eleve` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_eleve_parent` int(11) NOT NULL,
  `titre` varchar(250) NOT NULL,
  `date` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  `commentaire` text NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `id_contrat` int(11) DEFAULT NULL,
  `id_niveau` int(11) DEFAULT NULL,
  `annee_rentree` int(11) DEFAULT NULL,
  `accord_opco` tinyint(1) DEFAULT NULL,
  `mode_paiement` enum('CB','CHEQUE','VIREMENT') DEFAULT NULL,
  `paiement_recu` tinyint(1) DEFAULT NULL,
  `type_saisie_apprentissage` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id_paiement_eleve`),
  UNIQUE KEY `id_paiement_eleve` (`id_paiement_eleve`,`id_eleve`,`id_eleve_parent`),
  KEY `id_eleve_parent` (`id_eleve_parent`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=2216 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_panneaux_classes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_panneaux_classes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_panneau` int(11) unsigned NOT NULL,
  `id_classe` int(11) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_panneaux_formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_panneaux_formations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_panneau` int(11) unsigned NOT NULL,
  `id_formation` int(11) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_panneaux_groupes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_panneaux_groupes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_panneau` int(11) unsigned NOT NULL,
  `id_groupe` int(11) unsigned NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_panneaux_lumineux`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_panneaux_lumineux` (
  `id_panneau` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `annee` varchar(250) DEFAULT NULL,
  `identifiant_panneaux` varchar(255) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `plage_horaire` int(11) unsigned NOT NULL,
  `delai_horaire` int(11) unsigned NOT NULL,
  UNIQUE KEY `id_panneau` (`id_panneau`),
  UNIQUE KEY `identifiant_panneaux` (`identifiant_panneaux`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_param_sources_connaitre_ecole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_param_sources_connaitre_ecole` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom_source` varchar(255) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_pays`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_pays` (
  `id_pays` int(11) NOT NULL AUTO_INCREMENT,
  `code_pays` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `fr` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `en` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `es` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_pays`)
) ENGINE=InnoDB AUTO_INCREMENT=240 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_permissions` (
  `id_permission` int(11) NOT NULL AUTO_INCREMENT,
  `nom_permission` varchar(100) NOT NULL,
  `route` text NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  `ParentID` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_permission`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_recapitulatif`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_recapitulatif` (
  `id_recapitulatif` int(11) NOT NULL AUTO_INCREMENT,
  `date_recapitulatif` date NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_type_cours` int(11) NOT NULL,
  `hdebut` time NOT NULL,
  `hfin` time NOT NULL,
  `id_cours` int(11) NOT NULL,
  `volume_horaire` time NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  PRIMARY KEY (`id_recapitulatif`),
  KEY `id_classe` (`id_classe`,`id_type_cours`,`id_cours`,`id_etablissement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_recurrence_deleted`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_recurrence_deleted` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_recurrence` int(11) NOT NULL,
  `date_excepted` date NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_recurrence` (`id_recurrence`),
  KEY `date_excepted` (`date_excepted`),
  CONSTRAINT `amos_recurrence_deleted_ibfk_1` FOREIGN KEY (`id_recurrence`) REFERENCES `amos_referentiel_recurrence` (`id_recurrence`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_recurrence_updated`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_recurrence_updated` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_recurrence` int(11) NOT NULL,
  `date_recurrence` date NOT NULL,
  `data_recurrence` text NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_recurrence` (`id_recurrence`),
  KEY `date_recurrence` (`date_recurrence`),
  CONSTRAINT `amos_recurrence_updated_ibfk_1` FOREIGN KEY (`id_recurrence`) REFERENCES `amos_referentiel_recurrence` (`id_recurrence`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_classe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_classe` (
  `id_referentiel_classe` int(11) NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `anne` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `semestre` varchar(45) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `cc` float NOT NULL,
  `cr` float NOT NULL,
  `td` float NOT NULL,
  `ei` float NOT NULL,
  `cc_val` float NOT NULL,
  `cr_val` float NOT NULL,
  `td_val` float NOT NULL,
  `ei_val` float NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  `volume` float NOT NULL,
  `ects` float NOT NULL,
  `id_classe` int(11) NOT NULL,
  `modifie` tinyint(1) NOT NULL,
  `ignore` tinyint(1) NOT NULL DEFAULT 0,
  `id_referentiel_niveau` int(11) NOT NULL,
  PRIMARY KEY (`id_referentiel_classe`),
  KEY `id_etablissement` (`id_etablissement`,`id_unite_enseignement`,`anne`,`id_niveau`,`id_cours`,`id_intervenant`,`id_classe`,`id_referentiel_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=176220 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_config_pdf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_config_pdf` (
  `id_referentiel_config_pdf` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) NOT NULL,
  `texte_attestation` text NOT NULL,
  `texte_facture` text NOT NULL,
  `texte_facture_contrat_apprentissage` text DEFAULT NULL,
  `texte_facture_contrat_pro` text DEFAULT NULL,
  `footer_initial` text NOT NULL,
  `footer_apprentissage` text DEFAULT NULL,
  `footer_professionnalisation` text DEFAULT NULL,
  `type` varchar(11) NOT NULL DEFAULT 'facture',
  UNIQUE KEY `id_referentiel_config_pdf` (`id_referentiel_config_pdf`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_config_pdf_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_config_pdf_etablissement` (
  `id_referentiel_config_pdf_etablissement` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_referentiel_config_pdf` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  UNIQUE KEY `id_referentiel_config_pdf_etablissement` (`id_referentiel_config_pdf_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_groupe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_groupe` (
  `id_referentiel_groupe` int(11) NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `anne` int(11) NOT NULL,
  `id_groupe` int(11) NOT NULL,
  `semestre` varchar(45) CHARACTER SET latin1 NOT NULL,
  `id_cours` int(11) NOT NULL,
  `cc` float DEFAULT NULL,
  `cr` float DEFAULT NULL,
  `td` float DEFAULT NULL,
  `ei` float DEFAULT NULL,
  `cc_val` float DEFAULT NULL,
  `cr_val` float DEFAULT NULL,
  `td_val` float DEFAULT NULL,
  `ei_val` float DEFAULT NULL,
  `id_intervenant` int(11) NOT NULL,
  `volume` float NOT NULL,
  `ects` float NOT NULL,
  `id_classe` varchar(250) NOT NULL,
  `modifie` tinyint(1) NOT NULL,
  `ignore` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_referentiel_groupe`),
  KEY `id_etablissement` (`id_etablissement`,`id_unite_enseignement`,`anne`,`id_groupe`,`id_cours`,`id_intervenant`,`id_classe`(191))
) ENGINE=InnoDB AUTO_INCREMENT=8475 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_niveau`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_niveau` (
  `id_referentiel_niveau` int(11) NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `anne` varchar(12) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `semestre` varchar(3) NOT NULL,
  `id_cours` int(11) NOT NULL,
  `cc` float NOT NULL,
  `cr` float NOT NULL,
  `td` float NOT NULL,
  `ei` float NOT NULL,
  `cc_val` float NOT NULL,
  `cr_val` float NOT NULL,
  `td_val` float NOT NULL,
  `ei_val` float NOT NULL,
  `id_intervenant` int(11) NOT NULL,
  `volume` float NOT NULL,
  `ects` float NOT NULL,
  PRIMARY KEY (`id_referentiel_niveau`),
  KEY `id_etablissement` (`id_etablissement`,`id_unite_enseignement`,`anne`,`id_niveau`,`id_cours`,`id_intervenant`)
) ENGINE=InnoDB AUTO_INCREMENT=94531 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_recurrence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_recurrence` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_recurrence` int(11) NOT NULL,
  `type_recurrence` varchar(20) NOT NULL,
  `fin_recurrence` datetime DEFAULT NULL,
  `count_recurrence` int(20) DEFAULT NULL,
  `days_recurrence` varchar(50) DEFAULT NULL,
  `months_recurrence` varchar(50) DEFAULT NULL,
  `days_of_month` varchar(10) DEFAULT NULL,
  `occurences` int(11) DEFAULT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'vacance',
  UNIQUE KEY `id` (`id`),
  KEY `id_recurrence` (`id_recurrence`),
  KEY `id_recurrence_2` (`id_recurrence`),
  CONSTRAINT `amos_referentiel_recurrence_ibfk_2` FOREIGN KEY (`id_recurrence`) REFERENCES `amos_activite_intervenant` (`id_activite_intervenant`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_regles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_regles` (
  `id_regle` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `niveau` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  `id_ue` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `code_regle` text DEFAULT NULL,
  `is_actif` int(11) NOT NULL DEFAULT 0,
  UNIQUE KEY `id_regle` (`id_regle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_signatures`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_signatures` (
  `id_signature` int(11) NOT NULL AUTO_INCREMENT,
  `civilite` char(5) NOT NULL,
  `nom_directeur` varchar(70) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `signature` varchar(100) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  `fonction` varchar(150) NOT NULL,
  PRIMARY KEY (`id_signature`),
  KEY `id_etablissement` (`id_etablissement`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_referentiel_vacance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_referentiel_vacance` (
  `id_referentiel_vacance` int(5) NOT NULL AUTO_INCREMENT,
  `titre` char(255) CHARACTER SET latin1 DEFAULT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `type` enum('partiel','ferie','vacance','stage','fermeture','event','sejour') CHARACTER SET latin1 DEFAULT NULL,
  `id_etablissement` varchar(255) DEFAULT NULL,
  `id_classe` varchar(255) DEFAULT NULL,
  `date_creation` datetime DEFAULT NULL,
  `ip` char(15) CHARACTER SET latin1 DEFAULT NULL,
  `id_cours` int(11) DEFAULT NULL,
  `is_recurrence` int(11) NOT NULL DEFAULT 0,
  `all_day` int(11) DEFAULT 0,
  PRIMARY KEY (`id_referentiel_vacance`),
  KEY `type` (`type`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_classe` (`id_classe`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_reglement_dette`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_reglement_dette` (
  `id_reglement_dette` int(11) NOT NULL AUTO_INCREMENT,
  `date_reglement` date NOT NULL,
  `montant_reglement` double NOT NULL,
  `numero_cheque` varchar(255) NOT NULL,
  `id_dette` int(11) NOT NULL,
  PRIMARY KEY (`id_reglement_dette`),
  KEY `id_dette` (`id_dette`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_regles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_regles` (
  `id_regle` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `code_regle` text DEFAULT NULL,
  `libelle` text DEFAULT NULL,
  UNIQUE KEY `id_regle` (`id_regle`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_regles_notation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_regles_notation` (
  `id_regle` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `nom_regle` text NOT NULL,
  UNIQUE KEY `id_regle` (`id_regle`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_regles_notation_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_regles_notation_etablissement` (
  `id_regle` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_regles_notation_type_impacte`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_regles_notation_type_impacte` (
  `id_regle` int(11) NOT NULL,
  `id_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_resultats_epreuve_eleve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_resultats_epreuve_eleve` (
  `id_resultat_epreuve_eleve` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `id_epreuve_admission` int(11) NOT NULL,
  `anglais` int(1) NOT NULL,
  `culture_generale` int(1) NOT NULL,
  `epreuve_redaction` int(1) NOT NULL,
  `entretien` int(1) NOT NULL,
  `decision` enum('accepte','accepter_niveau_inferieur','refuse','en_attente','accepter_avec_entreprise') NOT NULL,
  `id_motif_refus` int(11) DEFAULT NULL,
  `archive` int(11) NOT NULL DEFAULT 0,
  `date_operation` datetime DEFAULT NULL,
  PRIMARY KEY (`id_resultat_epreuve_eleve`),
  KEY `id_eleve` (`id_eleve`,`id_epreuve_admission`),
  KEY `id_epreuve_admission` (`id_epreuve_admission`),
  KEY `id_eleve_2` (`id_eleve`),
  KEY `id_motif_refus` (`id_motif_refus`),
  KEY `archive` (`archive`),
  KEY `decision` (`decision`),
  KEY `entretien` (`entretien`),
  KEY `epreuve_redaction` (`epreuve_redaction`),
  KEY `culture_generale` (`culture_generale`),
  KEY `anglais` (`anglais`)
) ENGINE=InnoDB AUTO_INCREMENT=9610 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `sync_decision` AFTER INSERT ON `amos_resultats_epreuve_eleve` FOR EACH ROW BEGIN  

IF (SELECT con.mautic_id FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
IS NOT NULL AND (SELECT con.id_contact FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve) = (SELECT con.id_contact_parent FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
        ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
    (
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = (SELECT mel.id_contact FROM miracle.amos_eleves mel WHERE mel.id_eleve = NEW.id_eleve)
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_reunions_information`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_reunions_information` (
  `id_reunion_information` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `lieu` varchar(30) NOT NULL,
  `effectif` int(2) NOT NULL DEFAULT 60,
  `distanciel` int(1) NOT NULL DEFAULT 0,
  `url_distanciel` varchar(225) DEFAULT NULL,
  PRIMARY KEY (`id_reunion_information`),
  KEY `date` (`date`),
  KEY `idx_date` (`date`),
  FULLTEXT KEY `lieu` (`lieu`)
) ENGINE=InnoDB AUTO_INCREMENT=663 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_reunions_information_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_reunions_information_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_reunion_information` int(11) NOT NULL,
  `id_contact` int(11) NOT NULL,
  `presence` tinyint(1) NOT NULL,
  `rappel` tinyint(1) NOT NULL,
  `date_operation` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_reunion_information` (`id_reunion_information`),
  KEY `id_contact` (`id_contact`),
  KEY `presence` (`presence`)
) ENGINE=InnoDB AUTO_INCREMENT=9340 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `sync_ri` AFTER INSERT ON `amos_reunions_information_contacts` FOR EACH ROW BEGIN  

IF (SELECT mautic_id FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
IS NOT NULL AND NEW.id_contact = (SELECT id_contact_parent FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,
(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(
    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
    ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(    
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = NEW.id_contact
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `sync_ri_presence` AFTER UPDATE ON `amos_reunions_information_contacts` FOR EACH ROW BEGIN  

IF (SELECT mautic_id FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
IS NOT NULL AND NEW.id_contact = (SELECT id_contact_parent FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,
(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(
    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
        ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(    
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = NEW.id_contact
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_reunions_information_formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_reunions_information_formations` (
  `id_reunions_information_formation` int(11) NOT NULL AUTO_INCREMENT,
  `id_reunion_information` int(11) NOT NULL,
  `id_formation` int(11) NOT NULL,
  PRIMARY KEY (`id_reunions_information_formation`),
  KEY `id_reunion_information` (`id_reunion_information`),
  KEY `id_formation` (`id_formation`)
) ENGINE=InnoDB AUTO_INCREMENT=2986 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_reunions_information_limit3`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_reunions_information_limit3` (
  `id_reunion_information` int(11) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  `lieu` varchar(30) NOT NULL,
  `effectif` int(2) NOT NULL DEFAULT 60
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_roles` (
  `id_role` int(11) NOT NULL AUTO_INCREMENT,
  `nom_role` varchar(100) NOT NULL,
  `nom_machine` varchar(200) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime DEFAULT NULL,
  `locked` int(11) NOT NULL,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_roles_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_roles_permissions` (
  `id_role` int(11) NOT NULL,
  `id_permission` int(11) NOT NULL,
  KEY `index_permission` (`id_role`,`id_permission`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_rythme`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_rythme` (
  `id_rythme` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nom_rythme` varchar(255) NOT NULL,
  UNIQUE KEY `id_rythme` (`id_rythme`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_salles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_salles` (
  `id_salle` int(11) NOT NULL AUTO_INCREMENT,
  `code_salle` varchar(5) NOT NULL,
  `nom_salle` varchar(50) NOT NULL,
  `nombre_place` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  `date_creation` datetime NOT NULL,
  `ip` char(15) NOT NULL,
  PRIMARY KEY (`id_salle`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_semestre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_semestre` (
  `id_semestre` int(11) NOT NULL AUTO_INCREMENT,
  `libelle_semestre` varchar(15) NOT NULL,
  PRIMARY KEY (`id_semestre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_services` (
  `id_service` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `id_parent` int(11) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_service`),
  KEY `id_service` (`id_service`),
  KEY `id_parent` (`id_parent`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_sn_base_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_sn_base_notes` (
  `id_note` int(11) NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `id_referentiel` int(11) NOT NULL,
  `id_ue` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_type` int(11) NOT NULL,
  `id_evaluation` int(11) NOT NULL,
  `eval_session` int(11) NOT NULL,
  `id_eleve` int(11) NOT NULL,
  `validation_sans_note` int(11) NOT NULL,
  `note` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `publier_admin` tinyint(11) NOT NULL DEFAULT 0,
  `publier_eleve` tinyint(1) NOT NULL DEFAULT 0,
  `date_saisie` datetime NOT NULL,
  `session` tinyint(4) NOT NULL,
  `referentiel` enum('classe','groupe') NOT NULL,
  PRIMARY KEY (`id_note`),
  KEY `id_campus` (`id_campus`),
  KEY `id_classe` (`id_referentiel`),
  KEY `id_ue` (`id_ue`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_type` (`id_type`),
  KEY `id_evaluation` (`id_evaluation`),
  KEY `id_eleve` (`id_eleve`),
  KEY `publier_eleve` (`publier_eleve`),
  CONSTRAINT `amos_sn_base_notes_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_base_notes_ibfk_3` FOREIGN KEY (`id_ue`) REFERENCES `amos_unite_enseignement` (`id_unite_enseignement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_base_notes_ibfk_4` FOREIGN KEY (`id_matiere`) REFERENCES `amos_cours` (`id_cours`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_base_notes_ibfk_5` FOREIGN KEY (`id_evaluation`) REFERENCES `amos_sn_evaluations_existantes` (`id_evaluation`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_base_notes_ibfk_6` FOREIGN KEY (`id_eleve`) REFERENCES `amos_eleves` (`id_eleve`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=35657 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_sn_base_notes_historique`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_sn_base_notes_historique` (
  `id_sn_base_notes_historique` int(1) unsigned NOT NULL AUTO_INCREMENT,
  `id_note` int(11) NOT NULL,
  `ancienne_note` varchar(8) NOT NULL,
  `nouvelle_note` varchar(8) NOT NULL,
  `raison` text NOT NULL,
  `session` int(1) DEFAULT 1,
  `id_admin` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id_sn_base_notes_historique` (`id_sn_base_notes_historique`)
) ENGINE=InnoDB AUTO_INCREMENT=2496 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_sn_bulletins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_sn_bulletins` (
  `id_bulletin` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `pdf` longblob NOT NULL,
  `annee` smallint(6) NOT NULL,
  `semestre` tinyint(4) NOT NULL,
  `id_etablissement` tinyint(11) unsigned NOT NULL,
  `id_eleve` int(11) unsigned NOT NULL,
  `id_niveau` tinyint(11) unsigned NOT NULL,
  `session` tinyint(4) NOT NULL,
  `date_insert` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `date_update` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `active` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_bulletin`),
  KEY `annee` (`annee`),
  KEY `semestre` (`semestre`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_eleve` (`id_eleve`),
  KEY `id_niveau` (`id_niveau`),
  KEY `session` (`session`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_sn_evaluations_existantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_sn_evaluations_existantes` (
  `id_evaluation` int(11) NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `id_referentiel` int(11) NOT NULL,
  `id_ue` int(11) NOT NULL,
  `id_matiere` int(11) NOT NULL,
  `id_type_evaluation` int(11) NOT NULL,
  `nom_evaluation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_evaluation` date NOT NULL,
  `heure_debut` text DEFAULT NULL,
  `heure_fin` text DEFAULT NULL,
  `type_notation` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `boolean_facultatif` tinyint(1) NOT NULL,
  `referentiel` enum('classe','groupe') NOT NULL,
  `id_evaluation_parent` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_evaluation`),
  KEY `id_campus` (`id_campus`),
  KEY `id_classe` (`id_referentiel`),
  KEY `id_ue` (`id_ue`),
  KEY `id_matiere` (`id_matiere`),
  KEY `id_type_evaluation` (`id_type_evaluation`),
  CONSTRAINT `amos_sn_evaluations_existantes_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_evaluations_existantes_ibfk_3` FOREIGN KEY (`id_ue`) REFERENCES `amos_unite_enseignement` (`id_unite_enseignement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_evaluations_existantes_ibfk_4` FOREIGN KEY (`id_matiere`) REFERENCES `amos_cours` (`id_cours`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_evaluations_existantes_ibfk_5` FOREIGN KEY (`id_type_evaluation`) REFERENCES `amos_sn_type_evaluation` (`id_type_evaluation`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3118 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_sn_type_evaluation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_sn_type_evaluation` (
  `id_type_evaluation` int(11) NOT NULL AUTO_INCREMENT,
  `id_etablissement` int(11) NOT NULL,
  `coef` float NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `id_cours` int(11) DEFAULT 0,
  `id_niveau` int(11) NOT NULL,
  `id_referentiel` int(11) NOT NULL,
  `id_type` int(11) NOT NULL,
  `annee` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `referentiel` enum('classe','groupe') NOT NULL,
  PRIMARY KEY (`id_type_evaluation`),
  KEY `id_etablissement` (`id_etablissement`),
  KEY `id_type` (`id_type`),
  KEY `id_unite_enseignement` (`id_unite_enseignement`),
  KEY `id_niveau` (`id_niveau`),
  CONSTRAINT `amos_sn_type_evaluation_ibfk_1` FOREIGN KEY (`id_etablissement`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_sn_type_evaluation_ibfk_3` FOREIGN KEY (`id_unite_enseignement`) REFERENCES `amos_unite_enseignement` (`id_unite_enseignement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `id_type` FOREIGN KEY (`id_type`) REFERENCES `amos_type_evaluation` (`id_type`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2165 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_specialisation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_specialisation` (
  `id_specialisation` int(11) NOT NULL AUTO_INCREMENT,
  `nom_specialisation` varchar(150) NOT NULL,
  `date_creation` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  PRIMARY KEY (`id_specialisation`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_stats_appels_sortant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_stats_appels_sortant` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `date` date NOT NULL,
  `annee` int(4) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `id_statut` int(11) unsigned NOT NULL,
  `abouti` int(11) NOT NULL,
  `non_abouti` int(11) NOT NULL,
  `rappel` int(11) NOT NULL,
  `inscription_jpo` int(11) NOT NULL,
  `inscription_ea` int(11) NOT NULL,
  `envoi_email_one_shot` int(11) NOT NULL,
  `stop_relance` int(11) NOT NULL,
  `envoi_courrier` int(11) NOT NULL,
  `rappel_immediat` int(11) NOT NULL,
  `demande_brochure` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_campus` (`id_campus`),
  KEY `date` (`date`),
  KEY `annee` (`annee`),
  KEY `id_formation` (`id_formation`),
  KEY `id_statut` (`id_statut`),
  CONSTRAINT `amos_stats_appels_sortant_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_stats_appels_sortant_ibfk_2` FOREIGN KEY (`id_statut`) REFERENCES `amos_statuts` (`id_statut`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44964 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_stats_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_stats_history` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `date` date NOT NULL,
  `annee` int(4) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `id_contact` int(11) unsigned NOT NULL,
  `segment` varchar(50) NOT NULL,
  `columns` varchar(255) NOT NULL,
  `op` varchar(20) NOT NULL,
  `number` int(11) NOT NULL,
  `id_famille` int(11) unsigned NOT NULL,
  `id_statut` int(11) unsigned NOT NULL,
  `id_motif` int(11) unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  UNIQUE KEY `id` (`id`),
  KEY `id_campus` (`id_campus`),
  KEY `date` (`date`),
  KEY `annee` (`annee`),
  KEY `id_formation` (`id_formation`),
  KEY `id_contact` (`id_contact`),
  KEY `op` (`op`),
  KEY `id_famille` (`id_famille`),
  KEY `id_statut` (`id_statut`),
  KEY `id_motif` (`id_motif`)
) ENGINE=InnoDB AUTO_INCREMENT=910737 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_stats_objectifs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_stats_objectifs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `date` date NOT NULL,
  `annee` int(4) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `objectif` int(11) NOT NULL,
  `realise` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_campus` (`id_campus`),
  KEY `date` (`date`),
  KEY `annee` (`annee`),
  KEY `id_formation` (`id_formation`),
  KEY `objectif` (`objectif`),
  KEY `realise` (`realise`),
  CONSTRAINT `amos_stats_objectifs_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27507 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_stats_transformations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_stats_transformations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `date` date NOT NULL,
  `annee` int(4) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `id_famille` int(11) unsigned NOT NULL,
  `prospects` int(11) NOT NULL,
  `demandes_brochure` int(11) NOT NULL,
  `inscrits_jpo` int(11) NOT NULL,
  `participants_jpo` int(11) NOT NULL,
  `non_participants_jpo` int(11) NOT NULL,
  `inscrits_ea` int(11) NOT NULL,
  `paiement_ea` int(11) NOT NULL,
  `non_paiement_ea` int(11) NOT NULL,
  `participants_ea` int(11) NOT NULL,
  `non_participants_ea` int(11) NOT NULL,
  `admis` int(11) NOT NULL,
  `inscrits_partiel` int(11) NOT NULL,
  `inscrits` int(11) NOT NULL,
  `reinscrits_partiel` int(11) NOT NULL,
  `reinscrits` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_campus` (`id_campus`),
  KEY `date` (`date`),
  KEY `annee` (`annee`),
  KEY `id_formation` (`id_formation`),
  KEY `id_famille` (`id_famille`),
  KEY `prospects` (`prospects`),
  KEY `demandes_brochure` (`demandes_brochure`),
  KEY `inscrits_jpo` (`inscrits_jpo`),
  KEY `participants_jpo` (`participants_jpo`),
  KEY `non_participants_jpo` (`non_participants_jpo`),
  KEY `inscrits_ea` (`inscrits_ea`),
  KEY `paiement_ea` (`paiement_ea`),
  KEY `non_paiement_ea` (`non_paiement_ea`),
  KEY `participants_ea` (`participants_ea`),
  KEY `non_participants_ea` (`non_participants_ea`),
  KEY `admis` (`admis`),
  KEY `inscrits_partiel` (`inscrits_partiel`),
  KEY `inscrits` (`inscrits`),
  KEY `reinscrits_partiel` (`reinscrits_partiel`),
  KEY `reinscrits` (`reinscrits`),
  CONSTRAINT `amos_stats_transformations_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_stats_transformations_ibfk_2` FOREIGN KEY (`id_famille`) REFERENCES `amos_familles_des_sources` (`id_famille`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27123 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_stats_transformations_abandonnistes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_stats_transformations_abandonnistes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_campus` int(11) NOT NULL,
  `date` date NOT NULL,
  `annee` int(4) NOT NULL,
  `id_formation` int(11) NOT NULL,
  `id_famille` int(11) unsigned NOT NULL,
  `id_motif` int(11) unsigned NOT NULL,
  `prospects` int(11) NOT NULL,
  `demandes_brochure` int(11) NOT NULL,
  `inscrits_jpo` int(11) NOT NULL,
  `participants_jpo` int(11) NOT NULL,
  `inscrits_ea` int(11) NOT NULL,
  `participants_ea` int(11) NOT NULL,
  `admis` int(11) NOT NULL,
  `inscrits` int(11) NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `id_campus` (`id_campus`),
  KEY `date` (`date`),
  KEY `annee` (`annee`),
  KEY `id_formation` (`id_formation`),
  KEY `id_famille` (`id_famille`),
  KEY `prospects` (`prospects`),
  KEY `demandes_brochure` (`demandes_brochure`),
  KEY `inscrits_jpo` (`inscrits_jpo`),
  KEY `participants_jpo` (`participants_jpo`),
  KEY `inscrits_ea` (`inscrits_ea`),
  KEY `participants_ea` (`participants_ea`),
  KEY `admis` (`admis`),
  KEY `inscrits` (`inscrits`),
  KEY `id_motif` (`id_motif`),
  CONSTRAINT `amos_stats_transformations_abandonnistes_ibfk_1` FOREIGN KEY (`id_campus`) REFERENCES `amos_etablissement` (`id_etablissement`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_stats_transformations_abandonnistes_ibfk_2` FOREIGN KEY (`id_famille`) REFERENCES `amos_familles_des_sources` (`id_famille`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `amos_stats_transformations_abandonnistes_ibfk_3` FOREIGN KEY (`id_motif`) REFERENCES `amos_motifs_abandon` (`id_motif`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=305801 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_statuts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_statuts` (
  `id_statut` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) DEFAULT NULL,
  `code_status` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `score` int(5) NOT NULL DEFAULT 0,
  `libelle_front` varchar(255) DEFAULT NULL,
  UNIQUE KEY `id_statut` (`id_statut`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_statuts_taches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_statuts_taches` (
  `id_statut_tache` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_statut_tache`),
  UNIQUE KEY `id_statut_tache` (`id_statut_tache`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_sync_mautic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_sync_mautic` (
  `id_contact` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `sync_mautic` AFTER INSERT ON `amos_sync_mautic` FOR EACH ROW BEGIN  

IF (SELECT mautic_id FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
IS NOT NULL AND NEW.id_contact = (SELECT id_contact_parent FROM miracle.amos_contacts WHERE id_contact = NEW.id_contact)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`

    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,
(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(
    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
        ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(    
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = NEW.id_contact
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
        l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_taches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_taches` (
  `id_tache` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_contact` int(11) unsigned NOT NULL,
  `id_admin_assigne` int(11) unsigned DEFAULT NULL,
  `id_service_assigne` int(11) unsigned DEFAULT NULL,
  `id_ecole_assigne` int(11) unsigned DEFAULT NULL,
  `type_tache` varchar(255) DEFAULT NULL,
  `objet` varchar(255) DEFAULT NULL,
  `commentaire` text DEFAULT NULL,
  `date_debut` datetime DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `date_realisation` date DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `id_type_tache` int(11) NOT NULL DEFAULT 0,
  `id_statut_contact` int(11) DEFAULT NULL,
  `id_statut_tache` int(11) unsigned DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `archive` tinyint(1) DEFAULT 0,
  UNIQUE KEY `id_tache` (`id_tache`),
  KEY `idx_id_contact` (`id_contact`),
  KEY `idx_id_statut_contact` (`id_statut_contact`)
) ENGINE=InnoDB AUTO_INCREMENT=177988 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_textes_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_textes_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(3) NOT NULL,
  `categorie` varchar(30) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `statut` int(11) NOT NULL DEFAULT 1,
  `titre` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_type_absence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_type_absence` (
  `id_type_absence` int(11) NOT NULL AUTO_INCREMENT,
  `nom_type_absence` varchar(255) NOT NULL,
  PRIMARY KEY (`id_type_absence`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_type_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_type_access` (
  `id_access` int(11) NOT NULL AUTO_INCREMENT,
  `id_parent` int(11) NOT NULL,
  `class_method` varchar(50) NOT NULL,
  `method` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `libelle` varchar(500) NOT NULL,
  PRIMARY KEY (`id_access`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_type_cours`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_type_cours` (
  `id_type_cours` int(11) NOT NULL AUTO_INCREMENT,
  `type_cours` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type_cours`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_type_evaluation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_type_evaluation` (
  `id_type` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_type`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_types_taches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_types_taches` (
  `id_type_tache` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `libelle` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_type_tache`),
  UNIQUE KEY `id_type_tache` (`id_type_tache`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_unite_enseignement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_unite_enseignement` (
  `id_unite_enseignement` int(11) NOT NULL AUTO_INCREMENT,
  `code_unite` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `nom_unite_enseignement` text COLLATE utf8_unicode_ci NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `couleur` char(10) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id_unite_enseignement`),
  KEY `id_niveau` (`id_niveau`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_unite_enseignement_annees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_unite_enseignement_annees` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_unite_enseignement` int(11) NOT NULL,
  `annee` smallint(4) NOT NULL,
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=564 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `id_eleve` int(11) NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(250) NOT NULL,
  `connexion` datetime NOT NULL,
  `token` varchar(200) NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `email_etudiant` varchar(500) NOT NULL,
  PRIMARY KEY (`id_user`),
  KEY `id_eleve` (`id_eleve`)
) ENGINE=InnoDB AUTO_INCREMENT=44705 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=CURRENT_USER*/ /*!50003 TRIGGER `maut_sync_user` AFTER INSERT ON `amos_users` FOR EACH ROW BEGIN  

IF (SELECT con.mautic_id FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
IS NOT NULL AND (SELECT con.id_contact FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve) = (SELECT con.id_contact_parent FROM miracle.amos_eleves el LEFT JOIN amos_contacts con ON con.id_contact = el.id_contact WHERE el.id_eleve = NEW.id_eleve)
THEN
UPDATE mautic.maut_leads AS l, (
SELECT DISTINCT
    `c`.`id_contact` AS `id_contact`,
    `c`.`id_contact_parent` AS `id_contact_parent`,
    `e`.`id_eleve` AS `id_eleve`,
    CASE WHEN `c`.`civilite` = 'Mme' THEN 'Madame' WHEN `c`.`civilite` = 'Melle' THEN 'Madame' WHEN `c`.`civilite` = 'M' THEN 'Monsieur' ELSE NULL
END AS `contact_civilite`,
`c`.`nom` AS `contact_nom`,
`c`.`prenom` AS `contact_prenom`,
`c`.`sexe` AS `contact_sexe`,
`c`.`residence` AS `residence`,
`c`.`date_naissance` AS `contact_date_naissance`,
`c`.`nationalite` AS `contact_nationalite`,
`c`.`adresse` AS `contact_adresse`,
`c`.`code_postal` AS `contact_code_postal`,
`c`.`ville` AS `contact_ville`,
`e`.`profil` AS `profil_eleve`,
`c`.`pays` AS `contact_pays`,
`c`.`telephone` AS `contact_telephone`,
`c`.`dossier_complet` AS `dossier_complet`,
`c`.`relance_cv` AS `relance_cv`,
`c`.`cv_recu` AS `cv_recu`,
  (
    CASE WHEN(
      (`c`.`telephone` LIKE '06%') OR(`c`.`telephone` LIKE '07%') OR(`c`.`telephone` LIKE '+336%') OR(`c`.`telephone` LIKE '+337%')
    ) THEN
  REPLACE
    (`c`.`telephone`, ' ', '') WHEN(
    (`c`.`telephone` LIKE '7%' OR `c`.`telephone` LIKE '6%') AND c.code_country = 33
  ) THEN
REPLACE
  (
  CONCAT('0',`c`.`telephone`),
    ' ',
    ''
) ELSE ''
  END
) AS `contact_mobile`,
`c`.`email` AS `contact_email`,
`c`.`newsletter` AS `contact_newsletter`,
`c`.`reunion_info` AS `contact_reunion_info`,
`c`.`derniere_reunion` AS `contact_derniere_reunion`,
`c`.`candidat` AS `contact_candidat`,
CONVERT_TZ(
    `c`.`date_inscription`,
    'Europe/Paris',
    'UTC'
) AS `contact_date_inscription`,
`c`.`annee_rentree` AS `contact_annee_rentree`,
`f`.`description` AS `formation`,
`f`.`niveau` AS `formation_niveau`,
`e`.`paiement_formation` AS `formation_paiement`,
`e`.`cv` AS `cv`,
`cs`.`titre` AS `source`,
`cs`.`id_source` AS `source_id`,
`cs`.`id_famille_des_sources` AS `famille_id`,
`u`.`username` AS `username`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_contact_ecoles`.`etablissement`,
                    2
                )
            )
        )
    FROM
        `miracle`.`amos_contact_ecoles`
    WHERE
        `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_contact_ecoles`.`ordre`,
        `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
    LIMIT 0,
    1
) AS `etablissement1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_id1`,
(   SELECT
        `miracle`.`amos_reunions_information`.`distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `ri_distanciel`,(
    SELECT
        `miracle`.`amos_reunions_information`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date`
    DESC
        ,
        `miracle`.`amos_reunions_information`.`id_reunion_information`
    DESC
LIMIT 0,
1
) AS `url_ri_distanciel`,

(   SELECT
        `miracle`.`amos_epreuves_admission`.`distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `ea_distanciel`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`url_distanciel`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    DESC
        ,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    DESC
LIMIT 0,
1
) AS `url_ea_distanciel`,
(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_reunions_information`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_lieu1`,(
    SELECT
        `miracle`.`amos_reunions_information`.`date`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_date1`,(
    SELECT
        `miracle`.`amos_reunions_information_contacts`.`presence`
    FROM
        (
            `miracle`.`amos_reunions_information`
        JOIN `miracle`.`amos_reunions_information_contacts`
        )
    WHERE
        `miracle`.`amos_reunions_information`.`id_reunion_information` = `miracle`.`amos_reunions_information_contacts`.`id_reunion_information` AND `miracle`.`amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`
    ORDER BY
        `miracle`.`amos_reunions_information`.`date` DESC,
        `miracle`.`amos_reunions_information`.`id_reunion_information` DESC
    LIMIT 0,
    1
) AS `reunion_info_presence1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_id1`,(
    SELECT
        CONCAT(
            UCASE(
                LEFT(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    1
                )
            ),
            LCASE(
                SUBSTR(
                    `miracle`.`amos_epreuves_admission`.`lieu`,
                    2
                )
            )
        )
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_lieu1`,(
    SELECT
        `miracle`.`amos_epreuves_admission`.`date_epreuve`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_date1`,(    SELECT
        `mautic`.`maut_dynamic_content`.`content`
    FROM
        `mautic`.`maut_dynamic_content`
    WHERE
    REPLACE
        (
            `mautic`.`maut_dynamic_content`.`name`,
            'signature_',
            ''
        ) = CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
     ''
)
) AS `signature_sync`,
(
    SELECT
        `mautic`.`maut_users`.`id`
    FROM
        `mautic`.`maut_users`
    WHERE
        `mautic`.`maut_users`.`position` LIKE CONCAT(
            (
            SELECT
                CONVERT(
                    LCASE(
                        `miracle`.`amos_contact_ecoles`.`etablissement`
                    ) USING utf8mb4
                )
            FROM
                `miracle`.`amos_contact_ecoles`
            WHERE
                `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
            ORDER BY
                `miracle`.`amos_contact_ecoles`.`ordre`,
                `miracle`.`amos_contact_ecoles`.`id_contact_ecole`
            LIMIT 0,
            1
        ),
    ''
)
) AS `mautic_owner_id`,(
    SELECT
        `miracle`.`amos_epreuves_admission_eleves`.`presence`
    FROM
        (
            `miracle`.`amos_epreuves_admission`
        JOIN `miracle`.`amos_epreuves_admission_eleves`
        )
    WHERE
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` = `miracle`.`amos_epreuves_admission_eleves`.`id_epreuve_admission` AND `miracle`.`amos_epreuves_admission_eleves`.`id_eleve` = `e`.`id_eleve`
    ORDER BY
        `miracle`.`amos_epreuves_admission`.`date_epreuve` DESC,
        `miracle`.`amos_epreuves_admission`.`id_epreuve_admission` DESC
    LIMIT 0,
    1
) AS `concours_presence1`,(
    SELECT
        1
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`nom_niveau` LIKE '%international%' AND `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau`
    LIMIT 0,
    1
) AS `master_international`,(
    SELECT
        `miracle`.`amos_cron_etablissement`.`etablissement`
    FROM
        `miracle`.`amos_cron_etablissement`
    WHERE
        `miracle`.`amos_cron_etablissement`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `lieu_affectation`,(
    SELECT
        `miracle`.`amos_eleves`.`id_niveau`
    FROM
        `miracle`.`amos_eleves`
    WHERE
        `miracle`.`amos_eleves`.`id_eleve` = `e`.`id_eleve`
    LIMIT 0,
    1
) AS `id_niveau`,(
    SELECT
        `miracle`.`amos_niveaux`.`nom_niveau`
    FROM
        `miracle`.`amos_niveaux`
    WHERE
        `miracle`.`amos_niveaux`.`id_niveau` = `e`.`id_niveau` 
    LIMIT 0,
    1
) AS `nom_niveau`,(
    SELECT
        `miracle`.`amos_contacts`.`stop_relances`
    FROM
        `miracle`.`amos_contacts`
    WHERE
        `miracle`.`amos_contacts`.`id_contact` = `c`.`id_contact`
    LIMIT 0,
    1
) AS `stop_relances`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 18 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_appel_snp_non_aboutis`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 6 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ri_non_abouti`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 31 
        AND `miracle`.`amos_contact_origine_traces`.`utilisateur` NOT LIKE '%marketingautomation%'
        AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_inscription_ea_non_abo`,(
    SELECT
        COUNT(0)
    FROM
        (
            `miracle`.`amos_contact_origine_traces`
        JOIN `miracle`.`amos_taches`
        )
    WHERE
        `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache` AND `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact` AND `miracle`.`amos_taches`.`id_statut_contact` = 12 AND(
            `miracle`.`amos_contact_origine_traces`.`abouti` IS NULL OR `miracle`.`amos_contact_origine_traces`.`abouti` = 0
        )
    LIMIT 0,
    1
) AS `nb_non_pres_ea_non_abouti`,(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li style="text-align: left; font-family: Open Sans, Verdana, Arial, sans-serif; font-size:16px; line-height:30px; font-weight:400; color:#212121; padding: 0; margin: 0;">',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ri`.`date`, 'Le %W %e %M %Y à %kh%i') USING utf8
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ri`.`lieu`,
                    `ri`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `reunions`
    FROM
        `mautic`.`amos_reunions_information_limit3` `ri`
    WHERE
        UCASE(TRIM(`ri`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ri`.`effectif` >(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_reunions_information_contacts` `ric`
    WHERE
        `ric`.`id_reunion_information` = `ri`.`id_reunion_information`
) AND `ri`.`date` >= CURRENT_TIMESTAMP()
ORDER BY
    `ri`.`date`) AS `reunions_prochaines_dates`,
(
    SELECT
        CONCAT(
            '<ul>',
            GROUP_CONCAT(
                CONCAT(
                    '<li>',
                    CONVERT(
                        CONVERT(
                            DATE_FORMAT(`ea`.`date_epreuve`, '%d/%m/%Y %Hh%i') USING latin1
                        ) USING utf8mb4
                    ),
                    ' - ',
                    `ea`.`lieu`,
                    `ea`.`distanciel`,
                    '</li>'
                ) SEPARATOR ''
            ),
            '</ul>'
        ) AS `epreuves`
    FROM
        `mautic`.`amos_epreuves_admission_limit3` `ea`
    WHERE
        UCASE(TRIM(`ea`.`lieu`)) IN(
        SELECT
            UCASE(
                TRIM(
                    `miracle`.`amos_contact_ecoles`.`etablissement`
                )
            )
        FROM
            `miracle`.`amos_contact_ecoles`
        WHERE
            `miracle`.`amos_contact_ecoles`.`id_contact` = `c`.`id_contact`
    ) AND `ea`.`effectif` >=(
    SELECT
        COUNT(0)
    FROM
        `miracle`.`amos_epreuves_admission_eleves` `eae`
    WHERE
        `eae`.`id_epreuve_admission` = `ea`.`id_epreuve_admission`
) AND `ea`.`date_epreuve` >= CURRENT_TIMESTAMP()) AS `concours_prochaines_dates`,
(
    SELECT
        `t`.`status` AS `status`
    FROM
        (
        SELECT
            `amos_contacts_status`.`id_contact` AS `id_contact`,
            GROUP_CONCAT(
                `amos_contacts_status`.`status` SEPARATOR ' , '
            ) AS `status`
        FROM
            `mautic`.`amos_contacts_status`
        GROUP BY
            `amos_contacts_status`.`id_contact`
    ) `t`
WHERE
    `t`.`id_contact` = `c`.`id_contact`
) AS `status`,
    (
SELECT
  COUNT(
    DISTINCT `miracle`.`amos_contact_origine_traces`.`id_tache`
  )
FROM
  (
    `miracle`.`amos_contact_origine_traces`
  JOIN
    `miracle`.`amos_taches`
  )
WHERE
  (
    (
      `miracle`.`amos_contact_origine_traces`.`id_tache` = `miracle`.`amos_taches`.`id_tache`
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`id_contact` = `c`.`id_contact`
    ) AND(
      `miracle`.`amos_taches`.`id_statut_contact` = 2
    ) AND(
      `miracle`.`amos_contact_origine_traces`.`abouti` = 0
    )
  )
) AS `nb_brochure_non_aboutis`,
0 AS `relance`
FROM
    (
        (
                (
                    (
                        `miracle`.`amos_contacts` `c`
                    LEFT JOIN `miracle`.`amos_formations` `f`
                    ON
                        (
                            `f`.`id_formation` = `c`.`id_formation`
                        )
                    )
                LEFT JOIN `miracle`.`amos_contacts_sources` `cs`
                ON
                    (`cs`.`id_source` = `c`.`source`)
                )
            LEFT JOIN `miracle`.`amos_eleves` `e`
            ON
                (`e`.`id_contact` = `c`.`id_contact`)
            )
        LEFT JOIN `miracle`.`amos_users` `u`
        ON
            (`u`.`id_eleve` = `e`.`id_eleve`)
        )
WHERE
    (`e`.`visible` = 0 OR `c`.`visible` = 0)
    AND c.id_contact = (SELECT mel.id_contact FROM miracle.amos_eleves mel WHERE mel.id_eleve = NEW.id_eleve)
) AS c
SET 
		l.owner_id = c.mautic_owner_id,
        l.is_published=1, 
		l.id_niveau= c.id_niveau,
		l.email = c.contact_email,
		l.firstname = c.contact_prenom,
		l.lastname= c.contact_nom,
		l.civilite = c.contact_civilite,
		l.phone = c.contact_telephone,
        l.mobile =  c.contact_mobile,
		l.address1 = c.contact_adresse,
		l.zipcode = c.contact_code_postal,
		l.city = c.contact_ville,
		l.profil_eleve = c.profil_eleve,
		l.country = c.contact_pays, 
		l.date_modified = CONVERT_TZ(NOW(), 'Europe/Paris','UTC'), 
		l.modified_by = 11, 
		l.modified_by_user = 'admin', 
		l.contact_sexe = c.contact_sexe, 
		l.contact_date_naissance = c.contact_date_naissance, 
		l.contact_nationalite = c.contact_nationalite, 
		l.contact_newsletter = c.contact_newsletter, 
		l.contact_reunion_info = c.contact_reunion_info, 
		l.contact_derniere_reunion = c.contact_derniere_reunion, 
		l.contact_candidat = c.contact_candidat, 
		l.contact_annee_rentree = c.contact_annee_rentree, 
		l.formation = c.nom_niveau, 
		l.formation_niveau = c.formation_niveau, 
		l.formation_paiement = c.formation_paiement, 
		l.source_amos = c.source, 
		l.source_id_amos = c.source_id, 
		l.id_famille = c.famille_id, 
		l.etablissement1 = c.etablissement1, 
		l.reunion_info_id1 = c.reunion_info_id1, 
		l.reunion_info_lieu1 = c.reunion_info_lieu1, 
		l.reunion_info_date1 = c.reunion_info_date1,  
        l.reunion_info_date2 = DATE_FORMAT(c.reunion_info_date1, "%Y-%m-%e %H:%i:%s"),
		l.reunion_info_date1_full_t = DATE_FORMAT(c.reunion_info_date1, "%W %e %M %Y à %kh%i"),
		l.prochaines_ri = c.reunions_prochaines_dates,
		l.prochaines_ea = c.concours_prochaines_dates,
		l.reunion_info1_heure = DATE_FORMAT(c.reunion_info_date1, "%kh%i"),
		l.reunion_info_presence1 = c.reunion_info_presence1, 
		l.master_international = c.master_international, 
        l.concours_id1 = c.concours_id1,
		l.concours_lieu1 = c.concours_lieu1,
		l.concours_date1 = c.concours_date1,
        l.concours_date2 = DATE_FORMAT(c.concours_date1, "%Y-%m-%e %H:%i:%s"),
		l.concours_sup_7jours = c.concours_date1 >= DATE_ADD(now(), INTERVAL 7 DAY),
		l.concours_date1_full_text = DATE_FORMAT(c.concours_date1, "%W %e %M %Y à %kh%i"),
		l.concours1_heure =  DATE_FORMAT(c.concours_date1, "%kh%i"),
		l.concours_presence1 = c.concours_presence1,
		l.lieu_affectation = c.lieu_affectation,
		l.stop_relances = c.stop_relances,
		l.nb_appel_snp_non_aboutis = c.nb_appel_snp_non_aboutis,
		l.date_identified = c.contact_date_inscription,
		l.amos_status = c.status,
		l.amos_relance = c.relance,
		l.amos_username = c.username,
		l.dossier_complet = c.dossier_complet,
        l.signature_sync = c.signature_sync,
        l.relance_cv = c.relance_cv,
        l.cv = c.cv,
        l.cv_recu = c.cv_recu,
        l.nb_brochure_non_aboutis = c.nb_brochure_non_aboutis,
        l.ri_distanciel = c.ri_distanciel,
        l.ea_distanciel = c.ea_distanciel,
        l.url_ri_distanciel = c.url_ri_distanciel,
        l.url_ea_distanciel = c.url_ea_distanciel,
        l.nb_inscription_ea_non_abo = c.nb_inscription_ea_non_abo,
        l.nb_non_pres_ea_non_abouti = c.nb_non_pres_ea_non_abouti,
        l.nb_non_pres_ri_non_abouti = c.nb_non_pres_ri_non_abouti,
		l.residence = c.residence
WHERE l.amos_contact_id = c.id_contact_parent;
END IF;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
DROP TABLE IF EXISTS `amos_users_intervenant`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_users_intervenant` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `id_intervenant` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(250) NOT NULL,
  `connexion` datetime NOT NULL,
  `token` varchar(200) NOT NULL,
  `valide` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_user`),
  KEY `id_intervenant` (`id_intervenant`)
) ENGINE=InnoDB AUTO_INCREMENT=88 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_vacance_classe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_vacance_classe` (
  `id_vacance_classe` int(11) NOT NULL AUTO_INCREMENT,
  `id_referentiel_vacance` int(11) NOT NULL,
  `id_classe` int(11) NOT NULL,
  PRIMARY KEY (`id_vacance_classe`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_vacance_etablissement`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_vacance_etablissement` (
  `id_vacance_etablissement` int(11) NOT NULL AUTO_INCREMENT,
  `id_referentiel_vacance` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  PRIMARY KEY (`id_vacance_etablissement`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_variables` (
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_view_commerciaux_des_fiches`;
/*!50001 DROP VIEW IF EXISTS `amos_view_commerciaux_des_fiches`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `amos_view_commerciaux_des_fiches` AS SELECT 
 1 AS `id_contact_origine_trace`,
 1 AS `id_contact`,
 1 AS `utilisateur`,
 1 AS `id_utilisateur`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `amos_volume_horaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_volume_horaire` (
  `id_volume_horaire` int(11) NOT NULL AUTO_INCREMENT,
  `volume` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  `id_unite_enseignement` int(11) NOT NULL,
  `id_niveau` int(11) NOT NULL,
  PRIMARY KEY (`id_volume_horaire`),
  KEY `id_unite_enseignement` (`id_unite_enseignement`,`id_niveau`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_volumes_formations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_volumes_formations` (
  `id_volume_formation` int(11) NOT NULL AUTO_INCREMENT,
  `effectif` int(11) NOT NULL,
  `nb_classe` int(11) NOT NULL,
  `volume_cours` double NOT NULL,
  `id_niveau` int(11) NOT NULL,
  `id_etablissement` int(11) NOT NULL,
  PRIMARY KEY (`id_volume_formation`),
  UNIQUE KEY `effectif` (`effectif`,`nb_classe`,`id_niveau`,`id_etablissement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `amos_watching`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amos_watching` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `env` varchar(200) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `id_contact` int(11) DEFAULT NULL,
  `id_intervenant` int(11) DEFAULT NULL,
  `id_entreprise` int(11) DEFAULT NULL,
  `type` enum('crud','mail','api') NOT NULL,
  `value` varchar(50) NOT NULL,
  `data_before` text DEFAULT NULL,
  `element` varchar(100) NOT NULL,
  `module` varchar(100) DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `message` text NOT NULL,
  `message_technique` text DEFAULT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(15) NOT NULL,
  `badge` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `env` (`env`),
  KEY `id_admin` (`id_admin`),
  KEY `id_contact` (`id_contact`),
  KEY `type` (`type`),
  KEY `value` (`value`),
  KEY `element` (`element`),
  KEY `date` (`date`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=1204745 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `view_abandonniste_statuts`;
/*!50001 DROP VIEW IF EXISTS `view_abandonniste_statuts`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_abandonniste_statuts` AS SELECT 
 1 AS `id_contact`,
 1 AS `motif_abandon`,
 1 AS `famille`,
 1 AS `id_famille`,
 1 AS `source`,
 1 AS `id_source`,
 1 AS `annee_rentree`,
 1 AS `id_formation`,
 1 AS `date_inscription`,
 1 AS `etablissement`,
 1 AS `statut_pipe`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_epreuves_admission`;
/*!50001 DROP VIEW IF EXISTS `view_epreuves_admission`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_epreuves_admission` AS SELECT 
 1 AS `id_epreuve_admission`,
 1 AS `date_epreuve`,
 1 AS `lieu_ea`,
 1 AS `effectif_ea`,
 1 AS `nb_inscrit_ea`,
 1 AS `nb_participant_ea`,
 1 AS `nb_paiement_ea`,
 1 AS `nb_non_paiement_ea`,
 1 AS `nb_admis_ea`,
 1 AS `date_str`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_inscriptions`;
/*!50001 DROP VIEW IF EXISTS `view_inscriptions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_inscriptions` AS SELECT 
 1 AS `id_eleve`,
 1 AS `id_classe`,
 1 AS `id_niveau`,
 1 AS `classe`,
 1 AS `lieu`,
 1 AS `inscription`,
 1 AS `reinscription`,
 1 AS `date_inscription`,
 1 AS `date_str`,
 1 AS `nom_classe`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_inscriptions_annee_scolaire`;
/*!50001 DROP VIEW IF EXISTS `view_inscriptions_annee_scolaire`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_inscriptions_annee_scolaire` AS SELECT 
 1 AS `id_eleve`,
 1 AS `id_classe`,
 1 AS `id_niveau`,
 1 AS `classe`,
 1 AS `lieu`,
 1 AS `inscription`,
 1 AS `reinscription`,
 1 AS `date_inscription`,
 1 AS `date_str`,
 1 AS `nom_classe`,
 1 AS `annee_rentree`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_options`;
/*!50001 DROP VIEW IF EXISTS `view_options`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_options` AS SELECT 
 1 AS `id_eleve`,
 1 AS `id_niveau`,
 1 AS `nom_niveau`,
 1 AS `lieu`,
 1 AS `Anglais`,
 1 AS `Espagnol`,
 1 AS `TOEFL`,
 1 AS `Allemand`,
 1 AS `Portugais`,
 1 AS `Mandarin`,
 1 AS `Arabe`,
 1 AS `date_inscription`,
 1 AS `date_str`,
 1 AS `annee_rentree`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_reunions_informations`;
/*!50001 DROP VIEW IF EXISTS `view_reunions_informations`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_reunions_informations` AS SELECT 
 1 AS `id_reunion_information`,
 1 AS `date`,
 1 AS `lieu`,
 1 AS `effectif`,
 1 AS `nb_inscrit`,
 1 AS `nb_participant`,
 1 AS `date_str`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_suivi_transformation`;
/*!50001 DROP VIEW IF EXISTS `view_suivi_transformation`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_suivi_transformation` AS SELECT 
 1 AS `id_contact`,
 1 AS `id_source`,
 1 AS `source`,
 1 AS `id_famille`,
 1 AS `famille`,
 1 AS `annee_rentree`,
 1 AS `id_formation`,
 1 AS `description`,
 1 AS `niveau`,
 1 AS `date_inscription`,
 1 AS `etablissement`,
 1 AS `motif_abandon`,
 1 AS `demande_brochure`,
 1 AS `inscrit_ri`,
 1 AS `participant_ri`,
 1 AS `non_participant_ri`,
 1 AS `inscrit_concours`,
 1 AS `participant_concours`,
 1 AS `non_participant_concours`,
 1 AS `paiement_concours`,
 1 AS `admis`,
 1 AS `inscrit`,
 1 AS `abandon`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_suivi_transformation_abandonniste`;
/*!50001 DROP VIEW IF EXISTS `view_suivi_transformation_abandonniste`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_suivi_transformation_abandonniste` AS SELECT 
 1 AS `id_contact`,
 1 AS `motif_abandon`,
 1 AS `id_famille`,
 1 AS `famille`,
 1 AS `source`,
 1 AS `id_source`,
 1 AS `annee_rentree`,
 1 AS `id_formation`,
 1 AS `etablissement`,
 1 AS `date_inscription`,
 1 AS `prospect`,
 1 AS `demande_brochure`,
 1 AS `inscrit_ri`,
 1 AS `participant_ri`,
 1 AS `inscrit_concours`,
 1 AS `participant_concours`,
 1 AS `admis`,
 1 AS `inscrit`*/;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `view_synthese_cial`;
/*!50001 DROP VIEW IF EXISTS `view_synthese_cial`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_synthese_cial` AS SELECT 
 1 AS `id_tache`,
 1 AS `deadline`,
 1 AS `date_realisation`,
 1 AS `realise`,
 1 AS `realise_apr_dealine`,
 1 AS `realise_avt_deadline`,
 1 AS `cto_abouti`,
 1 AS `abouti`,
 1 AS `non_abouti`,
 1 AS `entrant`,
 1 AS `sortant`,
 1 AS `rappel`,
 1 AS `inscription_ri`,
 1 AS `inscription_concours`,
 1 AS `envoi_email_one_shot`,
 1 AS `stop_relance`,
 1 AS `envoi_courrier`,
 1 AS `rappel_immediat`,
 1 AS `demande_brochure`,
 1 AS `action`,
 1 AS `id_statut`,
 1 AS `statut`,
 1 AS `id_contact`,
 1 AS `etablissement`,
 1 AS `id_source`,
 1 AS `source`,
 1 AS `id_famille`,
 1 AS `famille`,
 1 AS `annee_rentree`,
 1 AS `id_formation`,
 1 AS `description`,
 1 AS `niveau`*/;
SET character_set_client = @saved_cs_client;
/*!50001 DROP VIEW IF EXISTS `acheques_supprimer`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `acheques_supprimer` AS select max(`amos_cheques_paiement`.`id_cheque_paiement`) AS `id_cheque_paiement` from `amos_cheques_paiement` group by `amos_cheques_paiement`.`numero_cheque`,`amos_cheques_paiement`.`montant_paiement`,`amos_cheques_paiement`.`date_encaissement`,`amos_cheques_paiement`.`nom_banque`,`amos_cheques_paiement`.`id_paiement_eleve`,`amos_cheques_paiement`.`date_echeance` having count(0) = 2 order by max(`amos_cheques_paiement`.`id_cheque_paiement`) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `amos_contacts_etablissements_groupped`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `amos_contacts_etablissements_groupped` AS select `amos_contact_ecoles`.`id_contact` AS `id_contact`,group_concat(`amos_contact_ecoles`.`etablissement` separator ', ') AS `etablissements` from `amos_contact_ecoles` group by `amos_contact_ecoles`.`id_contact` order by `amos_contact_ecoles`.`id_contact` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `amos_eleves_paiement_somme`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `amos_eleves_paiement_somme` AS select `amos_eleves`.`id_eleve` AS `id_eleve`,`amos_eleves`.`montant_formation` AS `montant_formation`,sum(`cp`.`montant_paiement`) AS `somme` from ((`amos_eleves` left join `amos_paiement_eleve` `pe` on(`pe`.`id_eleve` = `amos_eleves`.`id_eleve`)) left join `amos_cheques_paiement` `cp` on(`cp`.`id_paiement_eleve` = `pe`.`id_paiement_eleve`)) group by `amos_eleves`.`id_eleve` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `amos_view_commerciaux_des_fiches`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `amos_view_commerciaux_des_fiches` AS select min(`amos_contact_origine_traces`.`id_contact_origine_trace`) AS `id_contact_origine_trace`,`amos_contact_origine_traces`.`id_contact` AS `id_contact`,concat(`a`.`nom`,' ',`a`.`prenom`) AS `utilisateur`,`amos_contact_origine_traces`.`id_utilisateur` AS `id_utilisateur` from (`amos_contact_origine_traces` left join `amos_admins` `a` on(`a`.`id_admin` = `amos_contact_origine_traces`.`id_utilisateur`)) where `amos_contact_origine_traces`.`espace`  not like '%automation%' and `amos_contact_origine_traces`.`id_utilisateur` in (select `amos_admins`.`id_admin` from `amos_admins` where `amos_admins`.`profil` in ('callcenter','commercialetmarketing','raphael','superadmin','admin')) group by `amos_contact_origine_traces`.`id_contact` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_abandonniste_statuts`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_abandonniste_statuts` AS select `view_suivi_transformation`.`id_contact` AS `id_contact`,`view_suivi_transformation`.`motif_abandon` AS `motif_abandon`,`view_suivi_transformation`.`famille` AS `famille`,`view_suivi_transformation`.`id_famille` AS `id_famille`,`view_suivi_transformation`.`source` AS `source`,`view_suivi_transformation`.`id_source` AS `id_source`,`view_suivi_transformation`.`annee_rentree` AS `annee_rentree`,`view_suivi_transformation`.`id_formation` AS `id_formation`,`view_suivi_transformation`.`date_inscription` AS `date_inscription`,`view_suivi_transformation`.`etablissement` AS `etablissement`,case when `view_suivi_transformation`.`inscrit` = 1 then 'inscrit' else case when `view_suivi_transformation`.`admis` = 1 then 'admis' else case when `view_suivi_transformation`.`participant_concours` = 1 then 'participant_concours' else case when `view_suivi_transformation`.`inscrit_concours` = 1 then 'inscrit_concours' else case when `view_suivi_transformation`.`participant_ri` = 1 then 'participant_ri' else case when `view_suivi_transformation`.`inscrit_ri` = 1 then 'inscrit_ri' else case when `view_suivi_transformation`.`demande_brochure` = 1 then 'demande_brochure' else 'prospect' end end end end end end end AS `statut_pipe` from `view_suivi_transformation` where `view_suivi_transformation`.`abandon` = 1 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_epreuves_admission`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_epreuves_admission` AS select `a`.`id_epreuve_admission` AS `id_epreuve_admission`,`a`.`date_epreuve` AS `date_epreuve`,`a`.`lieu` AS `lieu_ea`,`a`.`effectif` AS `effectif_ea`,ifnull(`i`.`nb_inscrit`,0) AS `nb_inscrit_ea`,ifnull(`p`.`nb_participant`,0) AS `nb_participant_ea`,ifnull(`pa`.`nb_paiement`,0) AS `nb_paiement_ea`,ifnull(`npa`.`nb_non_paiement`,0) AS `nb_non_paiement_ea`,ifnull(`ad`.`nb_admis`,0) AS `nb_admis_ea`,date_format(`a`.`date_epreuve`,'%d-%m-%Y') AS `date_str` from (((((`miracle`.`amos_epreuves_admission` `a` left join (select `a`.`id_epreuve_admission` AS `id_epreuve_admission`,count(0) AS `nb_inscrit` from (`miracle`.`amos_epreuves_admission` `a` join `miracle`.`amos_epreuves_admission_eleves` `ae` on(`a`.`id_epreuve_admission` = `ae`.`id_epreuve_admission`)) group by `a`.`id_epreuve_admission`) `i` on(`i`.`id_epreuve_admission` = `a`.`id_epreuve_admission`)) left join (select `a`.`id_epreuve_admission` AS `id_epreuve_admission`,count(0) AS `nb_participant` from ((`miracle`.`amos_epreuves_admission` `a` join `miracle`.`amos_epreuves_admission_eleves` `ae` on(`a`.`id_epreuve_admission` = `ae`.`id_epreuve_admission`)) join `miracle`.`amos_eleves` `e` on(`e`.`id_eleve` = `ae`.`id_eleve`)) where `e`.`presence_eleve` = 1 group by `a`.`id_epreuve_admission`) `p` on(`p`.`id_epreuve_admission` = `a`.`id_epreuve_admission`)) left join (select `a`.`id_epreuve_admission` AS `id_epreuve_admission`,count(0) AS `nb_paiement` from ((`miracle`.`amos_epreuves_admission` `a` join `miracle`.`amos_epreuves_admission_eleves` `ae` on(`a`.`id_epreuve_admission` = `ae`.`id_epreuve_admission`)) join `miracle`.`amos_eleve_reglements` `r` on(`r`.`id_eleve` = `ae`.`id_eleve`)) group by `a`.`id_epreuve_admission`) `pa` on(`pa`.`id_epreuve_admission` = `a`.`id_epreuve_admission`)) left join (select `a`.`id_epreuve_admission` AS `id_epreuve_admission`,count(0) AS `nb_non_paiement` from ((`miracle`.`amos_epreuves_admission` `a` join `miracle`.`amos_epreuves_admission_eleves` `ae` on(`a`.`id_epreuve_admission` = `ae`.`id_epreuve_admission`)) left join `miracle`.`amos_eleve_reglements` `r` on(`r`.`id_eleve` = `ae`.`id_eleve`)) where `r`.`id_eleve` is null group by `a`.`id_epreuve_admission`) `npa` on(`npa`.`id_epreuve_admission` = `a`.`id_epreuve_admission`)) left join (select `a`.`id_epreuve_admission` AS `id_epreuve_admission`,count(0) AS `nb_admis` from (((`miracle`.`amos_epreuves_admission` `a` join `miracle`.`amos_resultats_epreuve_eleve` `r` on(`a`.`id_epreuve_admission` = `r`.`id_epreuve_admission`)) join `miracle`.`amos_epreuves_admission_eleves` `ae` on(`a`.`id_epreuve_admission` = `ae`.`id_epreuve_admission`)) join `miracle`.`amos_eleves` `e` on(`e`.`id_eleve` = `ae`.`id_eleve`)) where `r`.`decision` = 'accepte' and `e`.`paiement_formation` <> 'Payé' group by `a`.`id_epreuve_admission`) `ad` on(`ad`.`id_epreuve_admission` = `a`.`id_epreuve_admission`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_inscriptions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_inscriptions` AS select `e`.`id_eleve` AS `id_eleve`,`c`.`id_classe` AS `id_classe`,`c`.`id_niveau` AS `id_niveau`,`c`.`classe` AS `classe`,`et`.`nom_etablissement` AS `lieu`,case when `e`.`id_eleve_parent` = `e`.`id_eleve` then 1 else 0 end AS `inscription`,case when `e`.`id_eleve_parent` <> `e`.`id_eleve` then 1 else 0 end AS `reinscription`,`e`.`date_inscription` AS `date_inscription`,date_format(`e`.`date_inscription`,'%d-%m-%Y') AS `date_str`,case when `c`.`classe` like 'B1%' then 'B1' when `c`.`classe` like 'B2%' then 'B2' when `c`.`classe` like 'B3%PGE%' then 'B3 PGE' when `c`.`classe` like 'B3 MS%' then 'B3 MS' when `c`.`classe` like 'B3%' then 'B3' when `c`.`classe` like 'M1%' then 'M1' when `c`.`classe` like 'M2%' then 'M2' when `c`.`classe` like 'MBS 1 Inter%' then 'MBS1 Int' when `c`.`classe` like 'MBS 2 Inter%' then 'MBS2 Int' when `c`.`classe` like 'MBS 1%' then 'MBS1' when `c`.`classe` like 'MBS 2%' then 'MBS2' end AS `nom_classe` from ((`amos_eleves` `e` join `amos_classe` `c` on(`c`.`id_classe` = `e`.`id_classe`)) join `amos_etablissement` `et` on(`et`.`id_etablissement` = `c`.`id_etablissement`)) where `e`.`paiement_formation` = 'Payé' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_inscriptions_annee_scolaire`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_inscriptions_annee_scolaire` AS select `e`.`id_eleve` AS `id_eleve`,`c`.`id_classe` AS `id_classe`,`c`.`id_niveau` AS `id_niveau`,`c`.`classe` AS `classe`,`et`.`nom_etablissement` AS `lieu`,case when `e`.`id_eleve_parent` = `e`.`id_eleve` then 1 else 0 end AS `inscription`,case when `e`.`id_eleve_parent` <> `e`.`id_eleve` then 1 else 0 end AS `reinscription`,`e`.`date_inscription` AS `date_inscription`,date_format(`e`.`date_inscription`,'%d-%m-%Y') AS `date_str`,case when `c`.`classe` like 'B1%' then 'B1' when `c`.`classe` like 'B2%' then 'B2' when `c`.`classe` like 'B3%PGE%' then 'B3 PGE' when `c`.`classe` like 'B3 MS%' then 'B3 MS' when `c`.`classe` like 'B3%' then 'B3' when `c`.`classe` like 'M1%' then 'M1' when `c`.`classe` like 'M2%' then 'M2' when `c`.`classe` like 'MBS 1 Inter%' then 'MBS1 Int' when `c`.`classe` like 'MBS 2 Inter%' then 'MBS2 Int' when `c`.`classe` like 'MBS 1%' then 'MBS1' when `c`.`classe` like 'MBS 2%' then 'MBS2' end AS `nom_classe`,`co`.`annee_rentree` AS `annee_rentree` from (((`amos_eleves` `e` join `amos_classe` `c` on(`c`.`id_classe` = `e`.`id_classe`)) join `amos_contacts` `co` on(`co`.`id_contact` = `e`.`id_contact`)) join `amos_etablissement` `et` on(`et`.`id_etablissement` = `c`.`id_etablissement`)) where `e`.`paiement_formation` = 'Payé' */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_options`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_options` AS select `e`.`id_eleve` AS `id_eleve`,`n`.`id_niveau` AS `id_niveau`,`n`.`nom_niveau` AS `nom_niveau`,`ace`.`etablissement` AS `lieu`,case when `nio`.`titre` like '%Anglais%' then 1 else 0 end AS `Anglais`,case when `nio`.`titre` like '%Espagnol%' then 1 else 0 end AS `Espagnol`,case when `nio`.`titre` like '%TOEFL%' then 1 else 0 end AS `TOEFL`,case when `nio`.`titre` like '%Allemand%' then 1 else 0 end AS `Allemand`,case when `nio`.`titre` like '%Portugais%' then 1 else 0 end AS `Portugais`,case when `nio`.`titre` like '%Mandarin%' then 1 else 0 end AS `Mandarin`,case when `nio`.`titre` like '%Arabe%' then 1 else 0 end AS `Arabe`,`e`.`date_inscription` AS `date_inscription`,date_format(`e`.`date_inscription`,'%d-%m-%Y') AS `date_str`,`co`.`annee_rentree` AS `annee_rentree` from (((((`amos_eleves` `e` join `amos_contact_ecoles` `ace` on(`ace`.`id_contact` = `e`.`id_contact` and `ace`.`ordre` = 1)) left join `amos_eleve_paiements_options` `opt` on(`e`.`id_eleve` = `opt`.`id_eleve`)) left join `amos_niveaux_options` `nio` on(`opt`.`id_niveau_option` = `nio`.`id_niveau_option`)) join `amos_niveaux` `n` on(`n`.`id_niveau` = `nio`.`id_niveau`)) join `amos_contacts` `co` on(`co`.`id_contact` = `e`.`id_contact`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_reunions_informations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_reunions_informations` AS select `ri2`.`id_reunion_information` AS `id_reunion_information`,`ri2`.`date` AS `date`,`ri2`.`lieu` AS `lieu`,`ri2`.`effectif` AS `effectif`,ifnull(`i`.`nb_inscrit`,0) AS `nb_inscrit`,ifnull(`p`.`nb_participant`,0) AS `nb_participant`,date_format(`ri2`.`date`,'%d-%m-%Y') AS `date_str` from ((`miracle`.`amos_reunions_information` `ri2` left join (select `ri`.`id_reunion_information` AS `id_reunion_information`,count(0) AS `nb_inscrit` from (`miracle`.`amos_reunions_information` `ri` join `miracle`.`amos_reunions_information_contacts` `ric` on(`ri`.`id_reunion_information` = `ric`.`id_reunion_information`)) group by `ri`.`id_reunion_information`) `i` on(`i`.`id_reunion_information` = `ri2`.`id_reunion_information`)) left join (select `ri`.`id_reunion_information` AS `id_reunion_information`,count(0) AS `nb_participant` from (`miracle`.`amos_reunions_information` `ri` join `miracle`.`amos_reunions_information_contacts` `ric` on(`ri`.`id_reunion_information` = `ric`.`id_reunion_information`)) where `ric`.`presence` = 1 group by `ri`.`id_reunion_information`) `p` on(`p`.`id_reunion_information` = `ri2`.`id_reunion_information`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_suivi_transformation`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_suivi_transformation` AS select `c`.`id_contact` AS `id_contact`,`c`.`source` AS `id_source`,ifnull(`s`.`titre`,'Inconnue') AS `source`,`f`.`id_famille` AS `id_famille`,ifnull(`f`.`libelle`,'Inconnue') AS `famille`,`c`.`annee_rentree` AS `annee_rentree`,`c`.`id_formation` AS `id_formation`,`fo`.`description` AS `description`,`fo`.`niveau` AS `niveau`,`c`.`date_inscription` AS `date_inscription`,`e`.`etablissement` AS `etablissement`,(select `a`.`libelle` from (`amos_contact_origine_traces` `t` join `amos_motifs_abandon` `a` on(`a`.`id_motif` = `t`.`id_motif_abandon`)) where `t`.`id_action` = 5 and `t`.`id_contact` = `c`.`id_contact` limit 0,1) AS `motif_abandon`,case when `c`.`demande_brochure` = 1 or `c`.`source` = 1 then 1 else 0 end AS `demande_brochure`,case when `c`.`id_contact` in (select `amos_reunions_information_contacts`.`id_contact` from `amos_reunions_information_contacts` where `amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact`) then 1 else 0 end AS `inscrit_ri`,case when `c`.`id_contact` in (select `amos_reunions_information_contacts`.`id_contact` from `amos_reunions_information_contacts` where `amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact` and `amos_reunions_information_contacts`.`presence` = 1) then 1 else 0 end AS `participant_ri`,case when `c`.`id_contact` in (select `amos_reunions_information_contacts`.`id_contact` from `amos_reunions_information_contacts` where `amos_reunions_information_contacts`.`id_contact` = `c`.`id_contact` and (`amos_reunions_information_contacts`.`presence` <> 1 or `amos_reunions_information_contacts`.`id_reunion_information` in (select `amos_reunions_information`.`id_reunion_information` from `amos_reunions_information` where `amos_reunions_information`.`date` >= current_timestamp()))) then 1 else 0 end AS `non_participant_ri`,case when `c`.`id_contact` in (select `e`.`id_contact` from (`amos_epreuves_admission_eleves` `ea` join `amos_eleves` `e` on(`ea`.`id_eleve` = `e`.`id_eleve`)) where `e`.`id_contact` = `c`.`id_contact`) then 1 else 0 end AS `inscrit_concours`,case when `c`.`id_contact` in (select `e`.`id_contact` from (`amos_epreuves_admission_eleves` `ea` join `amos_eleves` `e` on(`ea`.`id_eleve` = `e`.`id_eleve`)) where `e`.`id_contact` = `c`.`id_contact` and `e`.`presence_eleve` = 1) then 1 else 0 end AS `participant_concours`,case when `c`.`id_contact` in (select `e`.`id_contact` from (`amos_epreuves_admission_eleves` `ea` join `amos_eleves` `e` on(`ea`.`id_eleve` = `e`.`id_eleve`)) where `e`.`id_contact` = `c`.`id_contact` and (`e`.`presence_eleve` <> 1 or `ea`.`id_epreuve_admission` in (select `amos_epreuves_admission`.`id_epreuve_admission` from `amos_epreuves_admission` where `amos_epreuves_admission`.`date_epreuve` >= current_timestamp()))) then 1 else 0 end AS `non_participant_concours`,case when `c`.`id_contact` in (select `e`.`id_contact` from ((`amos_epreuves_admission_eleves` `ea` join `amos_eleves` `e` on(`ea`.`id_eleve` = `e`.`id_eleve`)) join `amos_eleve_reglements` `r` on(`r`.`id_eleve` = `e`.`id_eleve`)) where `e`.`id_contact` = `c`.`id_contact` and `r`.`id_eleve` is not null) then 1 else 0 end AS `paiement_concours`,case when `c`.`id_contact` in (select `e`.`id_contact` from ((`amos_epreuves_admission_eleves` `ea` join `amos_eleves` `e` on(`ea`.`id_eleve` = `e`.`id_eleve`)) join `amos_resultats_epreuve_eleve` `r` on(`ea`.`id_epreuve_admission` = `r`.`id_epreuve_admission`)) where `e`.`id_contact` = `c`.`id_contact` and `r`.`decision` = 'accepte') then 1 else 0 end AS `admis`,case when `c`.`id_contact` in (select `e`.`id_contact` from `amos_eleves` `e` where `e`.`paiement_formation` = 'Payé' and `e`.`id_contact` = `c`.`id_contact`) then 1 else 0 end AS `inscrit`,case when `c`.`id_contact` in (select `t`.`id_contact` from `amos_contact_origine_traces` `t` where `t`.`id_action` = 5 and `t`.`id_contact` = `c`.`id_contact`) then 1 else 0 end AS `abandon` from ((((`amos_contacts` `c` left join `amos_contact_ecoles` `e` on(`c`.`id_contact` = `e`.`id_contact` and `e`.`ordre` = 1)) left join `amos_contacts_sources` `s` on(`s`.`id_source` = `c`.`source`)) left join `amos_familles_des_sources` `f` on(`f`.`id_famille` = `s`.`id_famille_des_sources`)) left join `amos_formations` `fo` on(`fo`.`id_formation` = `c`.`id_formation`)) where `c`.`visible` = 0 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_suivi_transformation_abandonniste`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_suivi_transformation_abandonniste` AS select `view_abandonniste_statuts`.`id_contact` AS `id_contact`,`view_abandonniste_statuts`.`motif_abandon` AS `motif_abandon`,`view_abandonniste_statuts`.`id_famille` AS `id_famille`,`view_abandonniste_statuts`.`famille` AS `famille`,`view_abandonniste_statuts`.`source` AS `source`,`view_abandonniste_statuts`.`id_source` AS `id_source`,`view_abandonniste_statuts`.`annee_rentree` AS `annee_rentree`,`view_abandonniste_statuts`.`id_formation` AS `id_formation`,`view_abandonniste_statuts`.`etablissement` AS `etablissement`,`view_abandonniste_statuts`.`date_inscription` AS `date_inscription`,case when `view_abandonniste_statuts`.`statut_pipe` = 'prospect' then 1 else 0 end AS `prospect`,case when `view_abandonniste_statuts`.`statut_pipe` = 'demande_brochure' then 1 else 0 end AS `demande_brochure`,case when `view_abandonniste_statuts`.`statut_pipe` = 'inscrit_ri' then 1 else 0 end AS `inscrit_ri`,case when `view_abandonniste_statuts`.`statut_pipe` = 'participant_ri' then 1 else 0 end AS `participant_ri`,case when `view_abandonniste_statuts`.`statut_pipe` = 'inscrit_concours' then 1 else 0 end AS `inscrit_concours`,case when `view_abandonniste_statuts`.`statut_pipe` = 'participant_concours' then 1 else 0 end AS `participant_concours`,case when `view_abandonniste_statuts`.`statut_pipe` = 'admis' then 1 else 0 end AS `admis`,case when `view_abandonniste_statuts`.`statut_pipe` = 'inscrit' then 1 else 0 end AS `inscrit` from `view_abandonniste_statuts` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP VIEW IF EXISTS `view_synthese_cial`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=CURRENT_USER SQL SECURITY DEFINER */
/*!50001 VIEW `view_synthese_cial` AS select `t`.`id_tache` AS `id_tache`,`t`.`deadline` AS `deadline`,`t`.`date_realisation` AS `date_realisation`,case when `t`.`date_realisation` is not null then 1 else 0 end AS `realise`,case when date_format(`t`.`deadline`,'%Y%m%d') < date_format(`t`.`date_realisation`,'%Y%m%d') then 1 else 0 end AS `realise_apr_dealine`,case when date_format(`t`.`date_realisation`,'%Y%m%d') <= date_format(`t`.`deadline`,'%Y%m%d') then 1 else 0 end AS `realise_avt_deadline`,`cto`.`abouti` AS `cto_abouti`,ifnull(`cto`.`abouti`,0) AS `abouti`,case when ifnull(`cto`.`abouti`,0) = 1 then 0 else 1 end AS `non_abouti`,ifnull(`cto`.`entrant`,0) AS `entrant`,case when ifnull(`cto`.`entrant`,0) = 1 then 0 else 1 end AS `sortant`,case when `a`.`id_action` = 1 then 1 else 0 end AS `rappel`,case when `a`.`id_action` = 2 then 1 else 0 end AS `inscription_ri`,case when `a`.`id_action` = 3 then 1 else 0 end AS `inscription_concours`,case when `a`.`id_action` = 4 then 1 else 0 end AS `envoi_email_one_shot`,case when `a`.`id_action` = 5 then 1 else 0 end AS `stop_relance`,case when `a`.`id_action` = 6 then 1 else 0 end AS `envoi_courrier`,case when `a`.`id_action` = 7 then 1 else 0 end AS `rappel_immediat`,case when `a`.`id_action` = 8 then 1 else 0 end AS `demande_brochure`,`a`.`libelle` AS `action`,`t`.`id_statut_contact` AS `id_statut`,`s`.`libelle` AS `statut`,`t`.`id_contact` AS `id_contact`,`e`.`etablissement` AS `etablissement`,`c`.`source` AS `id_source`,`cs`.`titre` AS `source`,`fs`.`id_famille` AS `id_famille`,`fs`.`libelle` AS `famille`,`c`.`annee_rentree` AS `annee_rentree`,`c`.`id_formation` AS `id_formation`,`f`.`description` AS `description`,`f`.`niveau` AS `niveau` from ((((((((`miracle`.`amos_taches` `t` left join (select `miracle`.`amos_contact_origine_traces`.`id_contact_origine_trace` AS `id_contact_origine_trace`,`miracle`.`amos_contact_origine_traces`.`id_contact` AS `id_contact`,`miracle`.`amos_contact_origine_traces`.`id_contact_parent` AS `id_contact_parent`,`miracle`.`amos_contact_origine_traces`.`trace` AS `trace`,`miracle`.`amos_contact_origine_traces`.`espace` AS `espace`,`miracle`.`amos_contact_origine_traces`.`date` AS `date`,`miracle`.`amos_contact_origine_traces`.`utilisateur` AS `utilisateur`,`miracle`.`amos_contact_origine_traces`.`id_tache` AS `id_tache`,`miracle`.`amos_contact_origine_traces`.`abouti` AS `abouti`,`miracle`.`amos_contact_origine_traces`.`entrant` AS `entrant`,`miracle`.`amos_contact_origine_traces`.`id_action` AS `id_action`,`miracle`.`amos_contact_origine_traces`.`id_canal` AS `id_canal`,`miracle`.`amos_contact_origine_traces`.`id_utilisateur` AS `id_utilisateur`,`miracle`.`amos_contact_origine_traces`.`type_tache` AS `type_tache`,`miracle`.`amos_contact_origine_traces`.`id_motif_abandon` AS `id_motif_abandon` from `miracle`.`amos_contact_origine_traces` where `miracle`.`amos_contact_origine_traces`.`id_contact_origine_trace` in (select max(`miracle`.`amos_contact_origine_traces`.`id_contact_origine_trace`) AS `id_contact_origine_trace` from `miracle`.`amos_contact_origine_traces` where `miracle`.`amos_contact_origine_traces`.`id_tache` <> 0 and `miracle`.`amos_contact_origine_traces`.`espace` <> 'Marketing Automation' and `miracle`.`amos_contact_origine_traces`.`id_action` <> 0 group by `miracle`.`amos_contact_origine_traces`.`id_tache`)) `cto` on(`t`.`id_tache` = `cto`.`id_tache`)) left join `miracle`.`amos_statuts` `s` on(`s`.`id_statut` = `t`.`id_statut_contact`)) left join `miracle`.`amos_actions_crm` `a` on(`a`.`id_action` = `cto`.`id_action`)) left join `miracle`.`amos_contact_ecoles` `e` on(`e`.`id_contact` = `t`.`id_contact` and `e`.`ordre` = 1)) join `miracle`.`amos_contacts` `c` on(`t`.`id_contact` = `c`.`id_contact`)) left join `miracle`.`amos_contacts_sources` `cs` on(`cs`.`id_source` = `c`.`source`)) left join `miracle`.`amos_familles_des_sources` `fs` on(`fs`.`id_famille` = `cs`.`id_famille_des_sources`)) left join `miracle`.`amos_formations` `f` on(`f`.`id_formation` = `c`.`id_formation`)) where `c`.`visible` = 0 */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

