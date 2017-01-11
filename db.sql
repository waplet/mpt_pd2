-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.6.21 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             9.1.0.4867
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table mpt_pd2.komanda
CREATE TABLE IF NOT EXISTS `komanda` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nosaukums` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nosaukums` (`nosaukums`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.maina
CREATE TABLE IF NOT EXISTS `maina` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `laiks` time NOT NULL DEFAULT '00:00:00',
  `spele_key` int(10) unsigned NOT NULL,
  `komanda_key` int(10) unsigned NOT NULL,
  `speletajs_nost_key` int(10) unsigned NOT NULL,
  `speletajs_uz_key` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `spele_key_komanda_key_speletajs_nost_key_speletajs_uz_key` (`spele_key`,`komanda_key`,`speletajs_nost_key`,`speletajs_uz_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.piespele
CREATE TABLE IF NOT EXISTS `piespele` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `varti_key` int(10) unsigned NOT NULL DEFAULT '0',
  `speletajs_key` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.sods
CREATE TABLE IF NOT EXISTS `sods` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `laiks` time NOT NULL DEFAULT '00:00:00',
  `spele_key` int(10) unsigned NOT NULL,
  `komanda_key` int(10) unsigned NOT NULL,
  `speletajs_key` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `laiks_spele_key_speletajs_key` (`laiks`,`spele_key`,`speletajs_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.spele
CREATE TABLE IF NOT EXISTS `spele` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `laiks` date NOT NULL,
  `vieta` varchar(50) NOT NULL,
  `komanda1_key` int(10) unsigned NOT NULL,
  `komanda2_key` int(10) unsigned NOT NULL,
  `vecakais_tiesnesis_key` int(10) unsigned NOT NULL,
  `lt1_key` int(10) unsigned NOT NULL,
  `lt2_key` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `laiks_vieta` (`laiks`,`vieta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.speletajs
CREATE TABLE IF NOT EXISTS `speletajs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uzvards` varchar(50) NOT NULL,
  `vards` varchar(50) NOT NULL,
  `loma` char(1) NOT NULL,
  `nr` int(10) unsigned NOT NULL,
  `komanda_key` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nr_komanda_key` (`nr`,`komanda_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.tiesnesis
CREATE TABLE IF NOT EXISTS `tiesnesis` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uzvards` varchar(50) NOT NULL,
  `vards` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uzvards_vards` (`uzvards`,`vards`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.


-- Dumping structure for table mpt_pd2.varti
CREATE TABLE IF NOT EXISTS `varti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `laiks` time NOT NULL DEFAULT '00:00:00',
  `sitiens` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `spele_key` int(10) unsigned NOT NULL,
  `komanda_key` int(10) unsigned NOT NULL,
  `speletajs_key` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

--

ALTER TABLE `spele`
	ADD COLUMN `skatitaji` INT(10) UNSIGNED NOT NULL;

ALTER TABLE `varti`
  ADD UNIQUE INDEX `laiks_sitiens_spele_key_komanda_key_speletajs_key` (`laiks`, `sitiens`, `spele_key`, `komanda_key`, `speletajs_key`);

ALTER TABLE `piespele`
  ADD COLUMN `laiks` TIME NOT NULL DEFAULT '0',
  ADD UNIQUE INDEX `laiks_varti_key_speletajs_key` (`laiks`, `varti_key`, `speletajs_key`);

CREATE TABLE `pamatsastavs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spele_key` INT(10) UNSIGNED NOT NULL,
  `speletajs_key` INT(10) UNSIGNED NOT NULL,
  `komanda_key` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `spele_key_speletajs_key` (`spele_key`, `speletajs_key`)
)
  ENGINE=InnoDB
;

ALTER TABLE `spele`
  DROP INDEX `laiks_vieta`,
  ADD UNIQUE INDEX `laiks_vieta` (`laiks`, `vieta`, `vecakais_tiesnesis_key`);

