-- phpMyAdmin SQL Dump
-- version 4.1.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dic 08, 2020 alle 15:50
-- Versione del server: 8.0.21
-- PHP Version: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `localhost`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `COVID19PIEMONTE_HIST`
--

CREATE TABLE IF NOT EXISTS `COVID19PIEMONTE_HIST` (
  `Ente` varchar(25) DEFAULT NULL,
  `Tipo` varchar(4) DEFAULT NULL,
  `Provincia` varchar(8) DEFAULT NULL,
  `ASL` varchar(20) DEFAULT NULL,
  `Codice ISTAT` varchar(6) DEFAULT NULL,
  `Abitanti` int DEFAULT NULL,
  `Positivi` int DEFAULT NULL,
  `Positivi 1000 abitanti` decimal(5,2) DEFAULT NULL,
  `Delta positivi` int DEFAULT NULL,
  `Delta positivi 1000 abitanti` decimal(5,2) DEFAULT NULL,
  `Data` varchar(10) DEFAULT NULL,
  KEY `Ente` (`Ente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Struttura della tabella `COVID19PIEMONTE_DAILY`
--

CREATE TABLE IF NOT EXISTS `COVID19PIEMONTE_DAILY` (
  `comune` varchar(25) DEFAULT NULL,
  `codice_istat` int DEFAULT NULL,
  `provincia` varchar(20) DEFAULT NULL,
  `positivi` int DEFAULT NULL,
  `positivi_per_1000_abitanti` decimal(4,2) DEFAULT NULL,
  KEY `provincia` (`provincia`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

