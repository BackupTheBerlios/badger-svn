CREATE DATABASE badgerbank;
USE badgerbank;

-- phpMyAdmin SQL Dump
-- version 2.8.0.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Aug 26, 2006 at 08:24 PM
-- Server version: 5.0.24
-- PHP Version: 5.1.6-0.dotdeb.1
-- 
-- Database: `badgerbank`
-- 
CREATE DATABASE `badgerbank` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `badgerbank`;

-- --------------------------------------------------------

-- 
-- Table structure for table `account`
-- 

DROP TABLE IF EXISTS `account`;
CREATE TABLE IF NOT EXISTS `account` (
  `account_id` int(10) unsigned NOT NULL auto_increment,
  `currency_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `lower_limit` decimal(20,2) default NULL,
  `upper_limit` decimal(20,2) default NULL,
  PRIMARY KEY  (`account_id`),
  KEY `account_FKIndex1` (`currency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `account`
-- 

INSERT INTO `account` (`account_id`, `currency_id`, `title`, `description`, `lower_limit`, `upper_limit`) VALUES (1, 1, 'Girokonto', 'Deutsche Bank Kto-Nr.: 12345678', '-1000.00', '2000.00'),
(2, 1, 'Visa-Karte', 'Visa Kredit-Karte', '-3000.00', NULL),
(3, 1, 'Tagesgeldkonto', 'Konto mit täglicher Verfügbarkeit, höhere Zinsen', '0.00', '3000.00'),
(4, 2, 'Paypal', 'Pay Pal Account geführt in Dollar', '0.00', '1000.00');

-- --------------------------------------------------------

-- 
-- Table structure for table `account_ids_seq`
-- 

DROP TABLE IF EXISTS `account_ids_seq`;
CREATE TABLE IF NOT EXISTS `account_ids_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `account_ids_seq`
-- 

INSERT INTO `account_ids_seq` (`id`) VALUES (5);

-- --------------------------------------------------------

-- 
-- Table structure for table `account_property`
-- 

DROP TABLE IF EXISTS `account_property`;
CREATE TABLE IF NOT EXISTS `account_property` (
  `prop_key` varchar(100) NOT NULL,
  `account_id` int(10) unsigned NOT NULL,
  `prop_value` varchar(255) NOT NULL,
  PRIMARY KEY  (`prop_key`,`account_id`),
  KEY `account_properties_FKIndex1` (`account_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `account_property`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `category`
-- 

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int(10) unsigned NOT NULL auto_increment,
  `parent_id` int(10) unsigned default NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `outside_capital` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`category_id`),
  KEY `category_FKIndex1` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `category`
-- 

INSERT INTO `category` (`category_id`, `parent_id`, `title`, `description`, `outside_capital`) VALUES (1, NULL, 'Miete', 'Transaktionen die mit Miete zu tun haben.', 0),
(2, NULL, 'Auto', 'Auto-Transaktionen', 0),
(3, 11, 'Haushalt', NULL, 1),
(4, NULL, 'Gehalt', NULL, 0),
(5, NULL, 'Kommunikation', 'Kommunikationsausgaben', 0),
(6, 11, 'Kleidung', NULL, 0),
(7, NULL, 'Studium', NULL, 0),
(8, NULL, 'Sparen', NULL, 0),
(9, 7, 'Buecher', NULL, 0),
(10, 7, 'Buerokratie', NULL, 0),
(11, NULL, 'Lebensführung', NULL, 0),
(12, 11, 'Lebensmittel', NULL, 0),
(13, 11, 'Luxus und Genuss', NULL, 0),
(14, 2, 'Instandhaltung', NULL, 0),
(15, 2, 'Benzin', NULL, 0),
(16, NULL, 'Sonstiges', NULL, 0),
(17, NULL, 'Hobbies und Freizeit', NULL, 0),
(18, NULL, 'Bargeld', NULL, 0),
(19, NULL, 'Gesundheit', NULL, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `category_ids_seq`
-- 

DROP TABLE IF EXISTS `category_ids_seq`;
CREATE TABLE IF NOT EXISTS `category_ids_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `category_ids_seq`
-- 

INSERT INTO `category_ids_seq` (`id`) VALUES (20);

-- --------------------------------------------------------

-- 
-- Table structure for table `currency`
-- 

DROP TABLE IF EXISTS `currency`;
CREATE TABLE IF NOT EXISTS `currency` (
  `currency_id` int(10) unsigned NOT NULL auto_increment,
  `long_name` varchar(100) NOT NULL,
  `symbol` char(3) NOT NULL,
  PRIMARY KEY  (`currency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `currency`
-- 

INSERT INTO `currency` (`currency_id`, `long_name`, `symbol`) VALUES (1, 'Euro', 'EUR'),
(2, 'Dollar', 'USD');

-- --------------------------------------------------------

-- 
-- Table structure for table `currency_ids_seq`
-- 

DROP TABLE IF EXISTS `currency_ids_seq`;
CREATE TABLE IF NOT EXISTS `currency_ids_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `currency_ids_seq`
-- 

INSERT INTO `currency_ids_seq` (`id`) VALUES (3);

-- --------------------------------------------------------

-- 
-- Table structure for table `datagrid_handler`
-- 

DROP TABLE IF EXISTS `datagrid_handler`;
CREATE TABLE IF NOT EXISTS `datagrid_handler` (
  `handler_name` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  PRIMARY KEY  (`handler_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `datagrid_handler`
-- 

INSERT INTO `datagrid_handler` (`handler_name`, `file_path`, `class_name`) VALUES ('AccountManager', '/modules/account/AccountManager.class.php', 'AccountManager'),
('Account', '/modules/account/Account.class.php', 'Account'),
('CategoryManager', '/modules/account/CategoryManager.class.php', 'CategoryManager'),
('CurrencyManager', '/modules/account/CurrencyManager.class.php', 'CurrencyManager');

-- --------------------------------------------------------

-- 
-- Table structure for table `finished_transaction`
-- 

DROP TABLE IF EXISTS `finished_transaction`;
CREATE TABLE IF NOT EXISTS `finished_transaction` (
  `finished_transaction_id` int(10) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned default NULL,
  `account_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `valuta_date` date default NULL,
  `amount` decimal(20,2) default NULL,
  `outside_capital` tinyint(1) NOT NULL default '0',
  `transaction_partner` varchar(100) default NULL,
  `periodical` tinyint(1) NOT NULL default '0',
  `exceptional` tinyint(1) NOT NULL default '0',
  `planned_transaction_id` int(11) default NULL,
  PRIMARY KEY  (`finished_transaction_id`),
  KEY `finished_transaction_FKIndex1` (`account_id`),
  KEY `finished_transaction_FKIndex2` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=91 DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- 
-- Dumping data for table `finished_transaction`
-- 

INSERT INTO `finished_transaction` (`finished_transaction_id`, `category_id`, `account_id`, `title`, `description`, `valuta_date`, `amount`, `outside_capital`, `transaction_partner`, `periodical`, `exceptional`, `planned_transaction_id`) VALUES (2, 4, 1, 'Gehalt', NULL, '2005-06-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(3, 4, 1, 'Gehalt', '', '2005-07-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(4, 1, 1, 'Gehalt', NULL, '2005-08-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(5, 4, 1, 'Gehalt', NULL, '2005-09-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(6, 4, 1, 'Gehalt', NULL, '2005-10-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(7, 4, 1, 'Gehalt', NULL, '2005-11-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(8, 4, 1, 'Gehalt', NULL, '2005-12-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(9, 4, 1, 'Gehalt', NULL, '2006-01-30', '1357.00', 0, 'Arbeitgeber AG', 1, 0, 5),
(10, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-07-01', '-420.00', 0, NULL, 1, 0, 6),
(11, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-08-01', '-420.00', 0, NULL, 1, 0, 6),
(12, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-09-01', '-420.00', 0, NULL, 1, 0, 6),
(13, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-10-01', '-420.00', 0, NULL, 1, 0, 6),
(14, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-11-01', '-420.00', 0, NULL, 1, 0, 6),
(15, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-12-01', '-420.00', 0, NULL, 1, 0, 6),
(16, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2006-01-01', '-420.00', 0, NULL, 1, 0, 6),
(17, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2006-02-01', '-420.00', 0, NULL, 1, 0, 6),
(18, 14, 1, 'Neue Benzinpumpe', NULL, '2005-10-12', '-200.00', 0, NULL, 0, 1, NULL),
(19, 14, 1, 'Scheibenwischer', 'Wer klaut denn jemandem einfach die Scheibenwischer? Das ist doch gemein!', '2005-11-18', '-53.00', 0, 'ATU', 0, 1, NULL),
(20, 15, 1, 'Tanken', NULL, '2005-07-01', '-62.00', 0, NULL, 0, 0, NULL),
(21, 15, 1, 'Tanken', NULL, '2005-07-20', '-53.23', 0, NULL, 0, 0, NULL),
(22, 15, 1, 'Tanken', NULL, '2005-08-07', '-53.45', 0, NULL, 0, 0, NULL),
(23, 15, 1, 'Tanken', NULL, '2005-08-25', '-53.23', 0, NULL, 0, 0, NULL),
(24, 15, 1, 'Tanken', NULL, '2005-09-18', '-44.45', 0, NULL, 0, 0, NULL),
(25, 15, 1, 'Tanken', NULL, '2005-09-30', '-52.13', 0, NULL, 0, 0, NULL),
(26, 15, 1, 'Tanken', NULL, '2005-10-12', '-53.45', 0, NULL, 0, 0, NULL),
(27, 15, 1, 'Tanken', NULL, '2005-10-29', '-47.88', 0, NULL, 0, 0, NULL),
(28, 15, 1, 'Tanken', NULL, '2005-11-07', '-61.22', 0, NULL, 0, 0, NULL),
(29, 15, 1, 'Tanken', NULL, '2005-11-18', '-33.34', 0, NULL, 0, 0, NULL),
(30, 15, 1, 'Tanken', NULL, '2005-12-20', '-58.38', 0, NULL, 0, 0, NULL),
(31, 15, 1, 'Tanken', NULL, '2005-12-30', '-50.50', 0, NULL, 0, 0, NULL),
(32, 15, 0, 'Tanken', NULL, '2006-01-18', '-48.33', 0, NULL, 0, 0, NULL),
(33, 15, 0, 'Tanken', NULL, '2006-01-31', '-50.10', 0, NULL, 0, 0, NULL),
(34, 15, 0, 'Tanken', NULL, '2006-02-12', '-20.50', 0, NULL, 0, 0, NULL),
(35, 15, 0, 'Tanken', NULL, '2006-02-12', '-12.45', 0, NULL, 0, 0, NULL),
(36, 3, 1, 'Teppichreinigung', NULL, '2005-10-30', '-120.00', 0, NULL, 0, 1, NULL),
(37, 3, 1, 'Fenster ersetzt', 'Diese Nachbarskinder', '2006-01-11', '-312.50', 0, NULL, 0, 1, NULL),
(38, 6, 1, 'Neuer Anzug', NULL, NULL, '-215.00', 0, NULL, 0, 1, NULL),
(39, 6, 1, 'Socken, Shirts', NULL, NULL, '-45.00', 0, NULL, 0, 1, NULL),
(40, 13, 2, 'Wellness-Wochenende', NULL, '2005-01-07', '-210.45', 0, NULL, 0, 1, NULL),
(41, 13, 2, 'Maniküre', NULL, '2005-09-15', '-33.00', 0, NULL, 0, 0, NULL),
(42, 9, 1, 'Buch: Solvency II im Unternehmen', NULL, '2006-01-10', '-50.00', 0, NULL, 0, 0, NULL),
(43, 9, 0, 'Wöhe', NULL, '2005-07-15', '-54.00', 0, NULL, 0, 0, NULL),
(44, 10, 1, 'Bachelor', NULL, '2006-01-03', '-172.00', 0, NULL, 0, 0, NULL),
(45, 10, 1, 'Studentenwerksbeitrag', NULL, '2005-12-01', '-182.00', 0, NULL, 0, 0, NULL),
(46, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-07-01', '-100.00', 0, NULL, 1, 0, NULL),
(47, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-08-01', '-100.00', 0, NULL, 1, 0, NULL),
(48, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-09-01', '-100.00', 0, NULL, 1, 0, NULL),
(49, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-10-01', '-100.00', 0, NULL, 1, 0, NULL),
(50, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-11-01', '-100.00', 0, NULL, 1, 0, NULL),
(51, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-12-01', '-100.00', 0, NULL, 1, 0, NULL),
(52, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2006-01-01', '-100.00', 0, NULL, 1, 0, NULL),
(53, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2006-02-01', '-100.00', 0, NULL, 1, 0, NULL),
(54, 4, 1, 'Bonus', 'Bonus für sehr gute Arbeit', '2006-01-02', '2000.00', 0, NULL, 0, 1, NULL),
(55, 17, 2, 'Lenkdrache gekauft', NULL, '2005-09-13', '-80.00', 0, NULL, 0, 1, NULL),
(56, 17, 2, 'Tauchkurs', 'PADI Open Water Diver ', '2005-11-23', '-355.00', 0, NULL, 0, 1, NULL),
(57, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-07-03', '-45.00', 0, NULL, 0, 0, NULL),
(58, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-08-03', '-45.00', 0, NULL, 0, 0, NULL),
(59, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-09-03', '-45.00', 0, NULL, 0, 0, NULL),
(60, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-10-03', '-45.00', 0, NULL, 0, 0, NULL),
(61, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-11-03', '-45.00', 0, NULL, 0, 0, NULL),
(62, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-12-03', '-45.00', 0, NULL, 0, 0, NULL),
(63, 5, 1, 'Mobiltelefonrechnung', NULL, '2006-01-03', '-45.00', 0, NULL, 0, 0, NULL),
(64, 5, 1, 'Mobiltelefonrechnung', NULL, '2006-02-03', '-45.00', 0, NULL, 0, 0, NULL),
(65, 12, 1, 'Fritten und Bier', NULL, '2005-07-01', '-15.00', 0, NULL, 0, 0, NULL),
(66, 12, 1, 'Aldi', NULL, '2005-07-02', '-45.00', 0, NULL, 0, 0, NULL),
(67, 12, 1, 'Aldi', NULL, '2005-07-11', '-65.45', 0, NULL, 0, 0, NULL),
(68, 12, 1, 'Getränke', NULL, '2005-07-17', '-34.23', 0, NULL, 0, 0, NULL),
(69, 12, 1, 'Getränke', NULL, '2005-08-12', '-32.23', 0, NULL, 0, 0, NULL),
(70, 12, 1, 'Aldi', NULL, '2005-08-20', '-30.45', 0, NULL, 0, 0, NULL),
(71, 12, 1, 'Maredo', NULL, '2005-08-30', '-80.00', 0, NULL, 0, 0, NULL),
(72, 12, 1, 'Kartoffeln', NULL, '2005-09-10', '-13.66', 0, NULL, 0, 0, NULL),
(73, 12, 1, 'Aldi', NULL, '2005-09-12', '-13.66', 0, NULL, 0, 0, NULL),
(74, 12, 1, 'Kantine', NULL, '2005-09-23', '-2.00', 0, NULL, 0, 0, NULL),
(75, 12, 1, 'Aldi', NULL, '2005-10-01', '-30.48', 0, NULL, 0, 0, NULL),
(76, 12, 1, 'Getränke', NULL, '2005-10-22', '-62.23', 0, NULL, 0, 0, NULL),
(77, 12, 1, 'Getränke', NULL, '2005-11-02', '-39.23', 0, NULL, 0, 0, NULL),
(78, 12, 1, 'Bier', NULL, '2005-11-07', '-20.00', 0, NULL, 0, 0, NULL),
(79, 12, 1, 'Aldi', NULL, '2005-11-19', '-42.00', 0, NULL, 0, 0, NULL),
(80, 12, 1, 'Fritten und Bier', NULL, '2005-11-30', '-35.00', 0, NULL, 0, 0, NULL),
(81, 12, 1, 'Süpermarket', NULL, '2005-12-03', '-33.00', 0, NULL, 0, 0, NULL),
(82, 12, 1, 'Aldi', NULL, '2005-12-08', '-42.00', 0, NULL, 0, 0, NULL),
(83, 12, 1, 'Bier', NULL, '2005-12-20', '-80.00', 0, NULL, 0, 0, NULL),
(84, 12, 1, 'Getränke', NULL, '2005-12-30', '-39.23', 0, NULL, 0, 0, NULL),
(85, 12, 1, 'Rollmöpse', NULL, '2006-01-01', '-32.00', 0, NULL, 0, 0, NULL),
(86, 12, 1, 'Aldi', NULL, '2006-01-06', '-30.48', 0, NULL, 0, 0, NULL),
(87, 12, 1, 'Kantine', NULL, '2006-01-18', '-29.00', 0, NULL, 0, 0, NULL),
(88, 12, 1, 'Aldi', NULL, '2006-01-30', '-43.66', 0, NULL, 0, 0, NULL),
(89, 12, 1, 'Kaffee', NULL, '2006-02-10', '-13.66', 0, NULL, 0, 0, NULL),
(90, 12, 1, 'Gummibärchen', NULL, '2006-01-12', '-40.00', 0, NULL, 0, 0, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `finished_transaction_ids_seq`
-- 

DROP TABLE IF EXISTS `finished_transaction_ids_seq`;
CREATE TABLE IF NOT EXISTS `finished_transaction_ids_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;

-- 
-- Dumping data for table `finished_transaction_ids_seq`
-- 

INSERT INTO `finished_transaction_ids_seq` (`id`) VALUES (91);

-- --------------------------------------------------------

-- 
-- Table structure for table `i18n`
-- 

DROP TABLE IF EXISTS `i18n`;
CREATE TABLE IF NOT EXISTS `i18n` (
  `page_id` varchar(50) NOT NULL default '',
  `id` text NOT NULL,
  `en` text,
  `de` text,
  PRIMARY KEY  (`page_id`,`id`(255))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `i18n`
-- 

INSERT INTO `i18n` (`page_id`, `id`, `en`, `de`) VALUES ('Calendar', 'gotoString', 'Go To Current Month', 'Gehe zu aktuellem Monat'),
('Calendar', 'todayString', 'Today is', 'Heute ist'),
('Calendar', 'weekString', 'Wk', 'KW'),
('Calendar', 'scrollLeftMessage', 'Click to scroll to previous month. Hold mouse button to scroll automatically.', 'Klicken, um zum vorigen Monat zu gelangen. Gedr&uuml;ckt halten, um automatisch weiter zu scrollen.'),
('Calendar', 'scrollRightMessage', 'Click to scroll to next month. Hold mouse button to scroll automatically.', 'Klicken, um zum n&auml;chsten Monat zu gelangen. Gedr&uuml;ckt halten, um automatisch weiter zu scrollen.'),
('Calendar', 'selectMonthMessage', 'Click to select a month.', 'Klicken, um Monat auszuw&auml;hlen'),
('Calendar', 'selectYearMessage', 'Click to select a year.', 'Klicken, um Jahr auszuw&auml;hlen'),
('Calendar', 'selectDateMessage', 'Select [date] as date.', 'W&auml;hle [date] als Datum.'),
('Calendar', 'closeCalendarMessage', 'Click to close the calendar.', 'Klicken, um den Kalender zu schlie&szlig;en.'),
('Calendar', 'monthName', 'new Array(\\''January\\'',\\''February\\'',\\''March\\'',\\''April\\'',\\''May\\'',\\''June\\'',\\''July\\'',\\''August\\'',\\''September\\'',\\''October\\'',\\''November\\'',\\''December\\'')', 'new Array(\\''Januar\\'',\\''Februar\\'',\\''M&auml;rz\\'',\\''April\\'',\\''Mai\\'',\\''Juni\\'',\\''Juli\\'',\\''August\\'',\\''September\\'',\\''Oktober\\'',\\''November\\'',\\''Dezember\\'')'),
('Calendar', 'monthName2', 'new Array(\\''JAN\\'',\\''FEB\\'',\\''MAR\\'',\\''APR\\'',\\''MAY\\'',\\''JUN\\'',\\''JUL\\'',\\''AUG\\'',\\''SEP\\'',\\''OCT\\'',\\''NOV\\'',\\''DEC\\'')', 'new Array(\\''JAN\\'',\\''FEB\\'',\\''MRZ\\'',\\''APR\\'',\\''MAI\\'',\\''JUN\\'',\\''JUL\\'',\\''AUG\\'',\\''SEP\\'',\\''OKT\\'',\\''NOV\\'',\\''DEZ\\'')'),
('Calendar', 'dayNameStartsWithMonday', 'new Array(\\''Mon\\'',\\''Tue\\'',\\''Wed\\'',\\''Thu\\'',\\''Fri\\'',\\''Sat\\'',\\''Sun\\'')', 'new Array(\\''Mo\\'',\\''Di\\'',\\''Mi\\'',\\''Do\\'',\\''Fr\\'',\\''Sa\\'',\\''So\\'')'),
('Calendar', 'dayNameStartsWithSunday', 'new Array(\\''Sun\\'',\\''Mon\\'',\\''Tue\\'',\\''Wed\\'',\\''Thu\\'',\\''Fri\\'',\\''Sat\\'')', 'new Array(\\''So\\'',\\''Mo\\'',\\''Di\\'',\\''Mi\\'',\\''Do\\'',\\''Fr\\'',\\''Sa\\'')'),
('badgerException', 'Errorcode', 'Error code', 'Fehlermeldung'),
('badgerException', 'Error', 'Error', 'Fehler'),
('badgerException', 'Line', 'Line', 'Zeile'),
('statistics', 'fullYear', 'Full year', 'ganzes Jahr'),
('Navigation', 'Logout', 'Logout', 'Abmelden'),
('Navigation', 'Preferences', 'Preferences', 'Einstellungen'),
('html2pdf', 'missing_url', 'No Source URL to create a PDF document from.', 'Quell-URL zum Generieren des PDFs nicht übergeben.'),
('Navigation', 'AccountManager', 'Accounts overview', 'Kontenübersicht'),
('importCsv', 'upload', 'Upload', 'Upload'),
('importCsv', 'noSeperator', 'File cannot be read by this parser. No seperator found', 'Datei kann mit diesem Parser nicht gelesen werden. Kein Trennzeichen gefunden'),
('importCsv', 'selectFile', 'Please select your CSV file', 'Bitte wählen Sie die CSV Datei aus'),
('importCsv', 'selectParser', 'Select Input Parser', 'CSV Format wählen'),
('UserSettingsAdmin', 'date_format_description', 'Sets the date format to be used.', 'Legt das zu verwendende Datumsformat fest.'),
('UserSettingsAdmin', 'change_password_heading', 'Change Password', 'Passwort ändern'),
('UserSettingsAdmin', 'maximum_login_attempts_name', 'Maximum Login Attempts:', 'Maximale Loginversuche:'),
('UserSettingsAdmin', 'new_password_confirm_description', 'Please confirm your entered password here.', 'Hier bitte das eingegebene Passwort bestätigen.'),
('UserSettingsAdmin', 'maximum_login_attempts_description', 'After how many failed login attempts should the access be temporarily denied?', 'Nach wie vielen fehlgeschlagenen Loginversuchen wird der Zugang temporär gesperrt?'),
('UserSettingsAdmin', 'linktext_after_successful_mandatory_change', 'Continue work...', 'Weiter...'),
('UserSettingsAdmin', 'lock_out_time_description', 'How many seconds should the access be denied?', 'Wie viele Sekunden wird die Sperre des Logins aufrecht erhalten?'),
('UserSettingsAdmin', 'lock_out_time_name', 'Duration of Lockout (sec):', 'Dauer der Zugangssperre (Sek.):'),
('UserSettingsAdmin', 'mandatory_change_password_heading', 'You are currently using the BADGER standard password.<br />\r\nPlease change it.<br />\r\nSie können die Sprache von BADGER unter dem Menüpunkt System / Preferences unter Language ändern.', 'Sie verwenden momentan das BADGER Standardpasswort.<br />\r\nBitte ändern Sie es.<br />\r\nYou can change the language of BADGER at menu System / Einstellungen, field Sprache.'),
('UserSettingsAdmin', 'language_name', 'Language:', 'Sprache:'),
('UserSettingsAdmin', 'linktext_after_failed_mandatory_change', 'Try again...', 'Nochmal versuchen...'),
('DataGridHandler', 'orderParamNoArray', 'The parameter to DataGridHandler::setOrder() is no array!', 'Der Parameter von DataGridHandler::setOrder() ist kein Array!'),
('DataGridHandler', 'orderArrayElementNoArray', 'The array passed to DataGridHandler::setOrder() contains a non-array element at index:', 'Das an DataGridHandler::setOrder() übergebene Array enthält an folgendem Index ein Nicht-Array-Element:'),
('DataGridHandler', 'orderKeyIndexNotDefined', 'The index ''key'' is not defined in the following element of the parameter to DataGridHandler::setOrder():', 'Der Index ''key'' ist im folgenden Element des Parameters von DataGridHandler::setOrder() nicht definiert:'),
('DataGridHandler', 'orderDirIndexNotDefined', 'The index ''dir'' is not defined in the following element of the parameter to DataGridHandler::setOrder():', 'Der Index ''dir'' ist im folgenden Element des Parameters von DataGridHandler::setOrder() nicht definiert:'),
('DataGridHandler', 'orderIllegalField', 'The following field is not known to this DataGridHandler:', 'Das folgende Feld ist diesem DataGridHandler nicht bekannt:'),
('DataGridHandler', 'orderIllegalDirection', 'The following illegal order direction was passed to DataGridHandler:', 'Die folgende ungültige Sortierrichtung wurde an DataGridHandler übergeben:'),
('DataGridHandler', 'filterParamNoArray', 'The parameter to DataGridHandler::setFilter() is no array!', 'Der Parameter von DataGridHandler::setFilter() ist kein Array!'),
('DataGridHandler', 'filterArrayElementNoArray', 'The array passed to DataGridHandler::setFilter() contains a non-array element at index:', 'Das an DataGridHandler::setFilter() übergebene Array enthält an folgendem Index ein Nicht-Array-Element:'),
('DataGridHandler', 'filterKeyIndexNotDefined', 'The index ''key'' is not defined in the following element of the parameter to DataGridHandler::setFilter():', 'Der Index ''key'' ist im folgenden Element des Parameters von DataGridHandler::setFilter() nicht definiert:'),
('DataGridHandler', 'filterOpIndexNotDefined', 'The index ''op'' is not defined in the following element of the parameter to DataGridHandler::setFilter():', 'Der Index ''op'' ist im folgenden Element des Parameters von DataGridHandler::setFilter() nicht definiert:'),
('DataGridHandler', 'filterValIndexNotDefined', 'The index ''val'' is not defined in the following element of the parameter to DataGridHandler::setFilter():', 'Der Index ''val'' ist im folgenden Element des Parameters von DataGridHandler::setFilter() nicht definiert:'),
('DataGridHandler', 'filterIllegalField', 'The following field is not known to this DataGridHandler:', 'Das folgende Feld ist diesem DataGridHandler nicht bekannt:'),
('AccountManager', 'invalidFieldName', 'The following field is not known to AccountManager:', 'Das folgende Feld ist AccountManager nicht bekannt:'),
('AccountManager', 'SQLError', 'An SQL error occured attempting to fetch the AccountManager data from the database:', 'Beim Abrufen der AccountManager-Daten aus der Datenbank trat ein SQL-Fehler auf:'),
('UserSettings', 'illegalKey', 'The following key is not defined in UserSettings:', 'Der folgende Schlüssel wurde in UserSettings nicht definiert:'),
('DataGridRepository', 'illegalHandlerName', 'The following DataGridHandler is not known to BADGER:', 'Der folgende DataGridHandler ist BADGER nicht bekannt:'),
('DataGridXML', 'undefinedColumns', 'DataGridXML::getXML() was called without setting columns!', 'DataGridXML::getXML() wurde aufgerufen, ohne vorher die Spalten zu definieren!'),
('DataGridXML', 'XmlSerializerException', 'An error occured in DataGridXML::getXML() while transforming internal data to XML.', 'Beim Umwandeln von internen Daten in XML trat in DataGridXML::getXML() ein Fehler auf.'),
('UserSettingsAdmin', 'language_description', 'Sets the language to be used.', 'Legt die zu verwendende Sprache fest.'),
('UserSettingsAdmin', 'new_password_confirm_name', 'Confirm new password:', 'Neues Passwort bestätigen:'),
('badger_login', 'submit_button', 'Submit', 'Senden'),
('UserSettingsAdmin', 'new_password_description', 'If you want to set a new password, please enter it here.', 'Falls sie ein neues Passwort festlegen wollen, geben Sie es hier ein.'),
('badger_login', 'you_are_logout', 'You have successfully logged out.', 'Sie haben sich erfolgreich ausgeloggt.'),
('badger_login', 'locked_out_refresh', 'Ban over?', 'Sperre schon vorrüber?'),
('badger_login', 'sent_password_failed', 'An error occured during sendig of the e-mail.', 'Beim Senden der E-Mail trat ein Fehler auf.'),
('badger_login', 'locked_out_part_2', ' seconds.', ' Sekunden.'),
('badger_login', 'locked_out_part_1', 'Because of too many failed login attempts you cannot login right now.<br/>The ban will be in effect for another ', 'Aufgrund zu häufiger fehlgeschlagener Loginversuche können sie sich leider derzeit nicht einloggen.<br/>Diese Sperre besteht noch für weitere '),
('badger_login', 'ask_really_send_link', 'Send the new password!', 'Neues Passwort schicken!'),
('badger_login', 'sent_password', 'A new password was sent to your e-mail adresse.', 'Ein neues Passwort wurde an die hinterlegte E-Mail Adresse gesendet.'),
('badger_login', 'ask_really_send', 'Really send a new password? Your old password will no longer work.', 'Möchten Sie sich wirklich ein neues Passwort zuschicken lassen? Ihr altes Passwort wird hiermit ungültig.'),
('badger_login', 'empty_password', 'Error: No password submitted!', 'Fehler: Kein Passwort eingegeben!'),
('badger_login', 'header', 'Login', 'Einloggen'),
('badger_login', 'wrong_password', 'Error: Wrong Password!', 'Fehler: Falsches Passwort!'),
('badger_login', 'forgot_password', 'Forgot your password?', 'Passwort vergessen?'),
('badger_login', 'enter_password', 'Please enter your password:', 'Bitte geben Sie ihr Passwort ein:'),
('importCsv', 'targetAccount', 'Please select your target account', 'Bitte wählen Sie das Zielkonto aus'),
('importCsv', 'wrongSeperatorNumber', 'File cannot be read by this parser. At least 1 line has not the right number of seperators', 'Datei kann mit diesem Parser nicht gelesen werden. Mindestens 1 Zeile enthält nicht die richtige Anzahl an Trennzeichen'),
('importCsv', 'select', 'Transfer', 'Übernehmen'),
('importCsv', 'category', 'Category', 'Kategorie'),
('importCsv', 'account', 'Account', 'Konto'),
('importCsv', 'title', 'Title', 'Verwendungszweck'),
('importCsv', 'description', 'Description', 'Beschreibung'),
('importCsv', 'valutaDate', 'Valuta Date', 'Buchungsdatum'),
('importCsv', 'amount', 'Amount', 'Betrag'),
('importCsv', 'transactionPartner', 'Transaction Partner', 'Transaktionspartner'),
('importCsv', 'save', 'Write to Database', 'In Datenbank schreiben'),
('importCsv', 'successfullyWritten', 'transactions successfully written to db', 'Transaktionen erfolgreich in die Datenbank geschrieben'),
('importCsv', 'noTransactionSelected', 'No transactions selected', 'Keine Transaktionen ausgewählt'),
('Account', 'invalidFieldName', 'The following field is not known to Account:', 'Das folgende Feld ist Account nicht bekannt:'),
('Account', 'SQLError', 'An SQL error occured attempting to fetch the Account data from the database:', 'Beim Abrufen der Account-Daten aus der Datenbank trat ein SQL-Fehler auf:'),
('CategoryManager', 'invalidFieldName', 'An unknown field was used in CategoryManager.', 'Im CategoryManager wurde ein ungültiges Feld verwendet.'),
('CategoryManager', 'SQLError', 'An SQL error occured attempting to fetch the CategoryManager data from the database.', 'Beim Abrufen der CategoryManager-Daten aus der Datenbank trat ein SQL-Fehler auf.'),
('Account', 'UnknownFinishedTransactionId', 'An unknown id was used for a single transaction.', 'Es wurde eine unbekannte ID einer einmaligen Transaktion benutzt.'),
('Account', 'insertError', 'An error occured while inserting a new single transaction into the database.', 'Beim Einfügen einer neuen einmaligen Transaktion trat ein Fehler auf.'),
('AccountManager', 'UnknownAccountId', 'An unknown id of an account was used.', 'Es wurde eine unbekannte ID eines Kontos benutzt.'),
('AccountManager', 'insertError', 'An error occured while inserting a new account in the database.', 'Beim Einfügen eines neuen Kontos trat ein Fehler auf.'),
('FinishedTransaction', 'SQLError', 'An SQL error occured attempting to edit the single transaction data in the database.', 'Beim Bearbeiten der Daten einer einmaligen Transaktion in der Datenbank trat ein SQL-Fehler auf.'),
('CategoryManager', 'UnknownCategoryId', 'An unknown id of a category was used.', 'Es wurde eine unbekannte ID einer Kategorie benutzt.'),
('CategoryManager', 'insertError', 'An error occured while inserting a new category in the database.', 'Beim Einfügen einer neuen Kategorie trat ein Fehler auf.'),
('Category', 'SQLError', 'An SQL error occured attempting to edit the Category data in the database:', 'Beim Bearbeiten der Category-Daten in der Datenbank trat ein SQL-Fehler auf:'),
('importCsv', 'periodical', 'Periodical', 'Regelmäßig'),
('importCsv', 'Exceptional', 'Exceptional', 'Außergewöhnlich'),
('importCsv', 'toolTipParserSelect', 'Choice of the csv parser. If your bank is not available or if there is a error when you upload, please visit our homepage. There perhaps you can find a proper parser or get support.', 'Auswahl des CSV Parsers. Wenn Ihre Bank nicht vorhanden ist oder es beim Upload zu Fehlern kommt, schauen Sie bitte auf unsere Website. Dort gibt es evtl. den passenden Parser oder Support.'),
('intervalUnits', 'day', 'day', 'Tag'),
('intervalUnits', 'week', 'week', 'Woche'),
('intervalUnits', 'month', 'month', 'Monat'),
('intervalUnits', 'year', 'year', 'Jahr'),
('intervalUnits', 'every', 'every', 'jede(n)/(s)'),
('importCsv', 'toolTopAccountSelect', 'Your accounts. You can administrate your accounts in the account manager.', 'Ihre Konten. Änderungen können Sie in der Kontoverwaltung vornehmen.'),
('templateEngine', 'noTemplate', 'Template not found.', 'Template nicht gefunden.'),
('widgetsEngine', 'ToolTipJSNotAdded', 'Method $widgets->addToolTipJS(); has not been evoked.', 'Die Methode $widgets->addToolTipJS(); wurde nicht vorher aufrufen.'),
('widgetsEngine', 'ToolTipLayerNotAdded', 'The method echo $widgets->addToolTipLayer(); has not been evoked.', 'Die Methode echo $widgets->addToolTipLayer(); wurde nicht vorher vorher aufrufen.'),
('widgetsEngine', 'CalendarJSNotAdded', 'The method $widgets->addCalendarJS(); has not been evoked.', 'Die Methode $widgets->addCalendarJS(); wurde nicht vorher vorher aufrufen.'),
('widgetsEngine', 'AutoCompleteJSNotAdded', 'The method $widgets->addAutoCompleteJS(); has not been evoked.', 'Die Methode $widgets->addAutoCompleteJS(); wurde nicht vorher vorher aufrufen.'),
('Account', 'FinishedTransaction', 'Single Transaction', 'Einmalige Transaktion'),
('Account', 'PlannedTransaction', 'Recurring transaction', 'Wiederkehrende Transaktion'),
('Account', 'day', 'daily', 'täglich'),
('Account', 'week', 'weekly', 'wöchentlich'),
('Account', 'month', 'monthly', 'monatlich'),
('Account', 'year', 'yearly', 'jährlich'),
('Account', 'UnknownPlannedTransactionId', 'An unknown id of a recurring transaction was used.', 'Es wurde eine unbekannte ID einer wiederkehrenden Transaktion benutzt.'),
('Account', 'IllegalRepeatUnit', 'An illigeal unit was given for a recurring transaction.', 'Für eine wiederkehrende Transaktion wurde eine ungültige Wiederholungseinheit angegeben.'),
('Account', 'illegalPropertyKey', 'An unknown property key was used for an account.', 'Für ein Konto wurde ein ungültiger Eigenschaftsschlüssel verwendet.'),
('importCsv', 'outsideCapital', 'Outside capital', 'Fremdkapital'),
('UserSettingsAdmin', 'error_standard_password', 'Please don´t use the standard password.', 'Bitte nicht das Standardpasswort verwenden.'),
('UserSettingsAdmin', 'session_time_name', 'Session time (min):', 'Sessionlänge (min):'),
('UserSettingsAdmin', 'site_name', 'User Settings', 'Einstellungen'),
('UserSettingsAdmin', 'submit_button', 'Save', 'Speichern'),
('UserSettingsAdmin', 'user_settings_heading', 'User Settings', 'Einstellungen'),
('DateFormats', 'dd.mm.yyyy', 'dd.mm.yyyy', 'tt.mm.jjjj'),
('DateFormats', 'dd/mm/yyyy', 'dd/mm/yyyy', 'tt/mm/jjjj'),
('DateFormats', 'dd-mm-yyyy', 'dd-mm-yyyy', 'tt-mm-jjjj'),
('DateFormats', 'yyyy-mm-dd', 'yyyy-mm-dd', 'jjjj-mm-tt'),
('DateFormats', 'yyyy/mm/dd', 'yyyy/mm/dd', 'jjjj/mm/tt'),
('Currency', 'SQLError', 'An SQL error occured attempting to edit the Currency data in the database:', 'Beim Bearbeiten der Währungs-Daten in der Datenbank trat ein SQL-Fehler auf:'),
('CurrencyManager', 'invalidFieldName', 'An unknown field was used in CurrencyManager.', 'Im CurrencyManager wurde ein ungültiges Feld verwendet.'),
('CurrencyManager', 'SQLError', 'An SQL error occured attempting to fetch the CurrencyManager data from the database.', 'Beim Abrufen der CurrencyManager-Daten aus der Datenbank trat ein SQL-Fehler auf.'),
('CurrencyManager', 'UnknownCurrencyId', 'An unknown id of a currency was used.', 'Es wurde eine unbekannte ID einer Währung benutzt.'),
('CurrencyManager', 'insertError', 'An error occured while inserting a new currency in the database.', 'Beim Einfügen einer neuen Währung trat ein Fehler auf.'),
('PlannedTransaction', 'SQLError', 'An SQL error occured attempting to edit the recurring transactions data in the database.', 'Beim Bearbeiten der Daten einer wiederkehrenden Transaktion in der Datenbank trat ein SQL-Fehler auf.'),
('templateEngine', 'HeaderIsAlreadyWritten', 'XHTML Head is already added to the document. This function has to be called before writing the header.', 'Der XHTML Kopf wurde bereits in das Dokument eingefügt. Die Funktion muss vor der Ausgabe aufgerufen werden.'),
('widgetsEngine', 'HeaderIsNotWritten', 'XHTML Header isn''t added to the document. Please call $tpl->getHeader() before this function.', 'Der XHTML Kopf wurde noch nicht in das Dokument eingefügt. Die Funktion $tpl->getHeader() muss vor dieser Funktion aufgerufen werden.'),
('importCsv', 'noNewTransactions', 'No new transactions found in the csv file.', 'Keine neuen Transaktionen in der CSV Datei gefunden.'),
('importCsv', 'echoFilteredTransactionNumber', 'transactions were filtered because they were already in the database.', 'Transaktionen gefiltert, da sie bereits in der Datenbank vorhanden sind.'),
('importExport', 'askTitle', 'Import / Export Data', 'Daten Import / Export'),
('importExport', 'askExportTitle', 'Export / Backup', 'Export / Datensicherung'),
('importExport', 'askExportText', 'You can save all of your BADGER finance data in a file. This file will be transmitted to your computer. Save the File at a secure place.', 'Sie können Ihre gesamten BADGER finance Daten in eine Datei sichern. Diese wird direkt auf Ihren Rechner übertragen. Speichern Sie die Datei ab.'),
('importExport', 'askExportAction', 'Export', 'Exportieren'),
('importExport', 'askImportTitle', 'Import', 'Import'),
('importExport', 'askImportInfo', 'You can upload previously saved backup data into BADGER finance.', 'Sie können einen einmal gesicherten Stand der BADGER finance Daten von einer Datei auf Ihrem Rechner zurück an BADGER finance übertragen.'),
('importExport', 'askImportWarning', 'Warning! When uploading a backup, all current data will be lost and replaced by data from the backup file.', 'Achtung: Beim Import gehen alle bereits vorhandenen Daten in BADGER finance verloren!'),
('importExport', 'askImportVersionInfo', 'If you upload a backup created with a previous BADGER finance version an update to the current database layout will occur after importing. All your data will be preserved.', 'Falls Sie eine von einer vorherigen BADGER-finance-Version erstellten Sicherheitskopie hochladen, wird im Anschluss an den Import eine Datenbank-Aktualisierung auf die neueste Version stattfinden. All Ihre Daten bleiben erhalten.'),
('importExport', 'askImportCurrentVersionInfo', 'You have the following version of BADGER finance currently installed:', 'Die aktuelle Version von BADGER finance ist:'),
('importExport', 'askImportAction', 'Import', 'Importieren'),
('importExport', 'askImportNo', 'No, I do not want to upload the backup data.', 'Nein, ich möchte die Daten nicht importieren.'),
('importExport', 'askImportYes', 'Yes I want to upload the backup file. All data will be deleted and replaced by the data from the backup file.', 'Ja, ich möchte die Daten importieren. Alle bestehenden Daten werden dabei gelöscht und durch den alten Datenbestand aus der Backup-Datei ersetzt.'),
('importExport', 'askImportFile', 'Please browse for your backup file:', 'Bitte wählen Sie die Sicherungsdatei aus:'),
('importExport', 'askImportSubmitButton', 'Import', 'Importieren'),
('importExport', 'askInsertTitle', 'Data Recovery', 'Datenwiederherstellung'),
('importExport', 'insertTitle', 'Import', 'Import'),
('importExport', 'insertNoInsert', 'You chose not to import the backup data.', 'Sie haben sich entschieden, die Daten nicht zu importieren.'),
('importExport', 'insertSuccessful', 'Data successfully saved. Please use the password from the backup file to log in.', 'Die Daten wurden erfolgreich importiert. Bitte benutzen Sie das Passwort aus der Sicherheitskopie zum einloggen.'),
('importExport', 'noSqlDumpProvided', 'Uploaded file missing.', 'Es wurde keine Datei hochgeladen.'),
('importExport', 'errorOpeningSqlDump', 'There was a problem processing the uploaded file.', 'Die hochgeladene Datei konnte nicht verarbeitet werden.'),
('importExport', 'incompatibleBadgerVersion', 'The uploaded file was not a BADGER finance file or a BADGER finance backup file from an uncompatible version.', 'Die hochgeladene Datei ist kein BADGER finance Export oder von einer inkompatiblen BADGER finance Version.'),
('importExport', 'SQLError', 'There was an Error during execution of the SQL-statement.', 'Beim Verarbeiten eines SQL-Befehls ist ein Fehler aufgetreten.'),
('importExport', 'insertNoFile', 'Error: You did not upload a file.', 'Fehler: SIe haben keine Datei hochgeladen.'),
('Navigation', 'CurrencyManager', 'Currencies', 'Währungen'),
('Navigation', 'System', 'System', 'System'),
('Navigation', 'Backup', 'Backup', 'Backup'),
('Navigation', 'CSV-Import', 'Import transactions', 'Transaktionen importieren'),
('Navigation', 'Forecast', 'Forecast', 'Prognose'),
('accountCategory', 'title', 'Category name', 'Kategoriename'),
('accountCategory', 'description', 'Description', 'Beschreibung'),
('accountAccount', 'title', 'Account name', 'Kontoname'),
('accountAccount', 'description', 'Description', 'Beschreibung'),
('accountCategory', 'outsideCapital', 'Outside capital', 'Fremdkapital'),
('accountAccount', 'lowerLimit', 'lower limit', 'Untergrenze'),
('accountAccount', 'upperLimit', 'upper limit', 'Obergrenze'),
('accountCategory', 'parent', 'Parent category', 'Elternkategorie'),
('accountAccount', 'balance', 'Balance', 'Gesamtkontostand'),
('accountAccount', 'currency', 'Currency', 'Währung'),
('accountTransaction', 'description', 'Description', 'Beschreibung'),
('accountTransaction', 'valutaDate', 'Valuta date', 'Buchungsdatum'),
('accountAccount', 'targetFutureCalcDate', 'Target future calc date', 'Stichtag'),
('accountTransaction', 'amount', 'Amount', 'Betrag'),
('accountTransaction', 'outsideCapital', 'Outside capital', 'Fremdkapital'),
('accountTransaction', 'transactionPartner', 'Transaction partner', 'Transaktionspartner'),
('accountTransaction', 'category', 'Category', 'Kategorie'),
('accountTransaction', 'periodical', 'Periodical transaction', 'Periodische Transaktionen'),
('accountTransaction', 'exceptional', 'Exceptional transaction', 'Außergewöhnliche Transaktion'),
('accountCurrency', 'symbol', 'Currency symbol', 'Währungskürzel'),
('accountCurrency', 'longname', 'Written name of the currency', 'Währungsname'),
('dataGrid', 'deleteMsg', 'Do you really want to delete the selected records? ', 'Wollen sie die selektierten Datensätze wirklich löschen?'),
('dataGrid', 'rowCounterName', 'row(s)', 'Datensätze'),
('dataGrid', 'new', 'New', 'Neu'),
('dataGrid', 'delete', 'Delete', 'Löschen'),
('UserSettingsAdmin', 'date_format_name', 'Date Format: ', 'Datumsformat: '),
('UserSettingsAdmin', 'error_confirm_failed', 'The passwords don´t match.', 'Die Passwörter stimmen nicht überein.'),
('UserSettingsAdmin', 'error_empty_password', 'Password mus have at least one letter.', 'Passwort muss mindestens ein Zeichen haben.'),
('UserSettingsAdmin', 'error_old_password_not_correct', 'Old password not correct.', 'Altes Passwort nicht korrekt.'),
('UserSettingsAdmin', 'new_password_name', 'New password:', 'Neues Passwort:'),
('UserSettingsAdmin', 'old_password_description', 'Please enter your old password.', 'Bitte geben Sie ihr altes Passwort an.'),
('UserSettingsAdmin', 'old_password_name', 'Old password:', 'Altes Passwort:'),
('UserSettingsAdmin', 'password_change_commited', 'Password was changed successfully.', 'Passwort wurde erfolgreich geändert.'),
('UserSettingsAdmin', 'seperators_description', 'Sets the number format to be used.', 'Legt das zu verwendende Zahlenformat fest.'),
('UserSettingsAdmin', 'seperators_name', 'Seperators: ', 'Trennzeichen: '),
('UserSettingsAdmin', 'session_time_description', 'Defines after how much time of inactivity a new login is neccessary.', 'Legt fest, nach wie langer Inaktivität ein erneutes Login nötig ist.'),
('UserSettingsAdmin', 'start_page_description', 'Defines the page to display at the start of BADGER.', 'Legt die Seite fest, die beim Start vom BADGER angezeigt wird.'),
('UserSettingsAdmin', 'start_page_name', 'Start page:', 'Startseite:'),
('UserSettingsAdmin', 'template_description', 'A theme determines the look of BADGER finance.', 'Ein Theme bestimmt das grundlegende Aussehen von BADGER finance.'),
('UserSettingsAdmin', 'template_name', 'Theme:', 'Theme:'),
('UserSettingsAdmin', 'user_settings_change_commited', 'User settings have been successfully commit', 'Nutzereinstellungen wurden erfolgreich gespeichert.'),
('UserSettingsAdmin', 'login_button', 'Login', 'Login'),
('badger_login', 'fs_heading', 'Login', 'Login'),
('UserSettingsAdmin', 'fs_heading', 'User Settings', 'Allgemeine Einstellungen'),
('UserSettingsAdmin', 'mandatory_fs_heading', 'Password Change', 'Passwortänderung'),
('UserSettingsAdmin', 'mandatory_commited_fs_heading', 'Password Changed', 'Passwort geändert'),
('Navigation', 'Statistics', 'Statistics', 'Statistiken'),
('dataGrid', 'save', 'Save', 'Speichern'),
('dataGrid', 'back', 'Back', 'Zurück'),
('dataGrid', 'LoadingMessage', 'Loading ...', 'Lade ...'),
('Navigation', 'CategoryManager', 'Transaction categories', 'Transaktionskategorien'),
('Navigation', 'Help', 'Help', 'Hilfe'),
('Navigation', 'About', 'About Badger', 'Über Badger'),
('Navigation', 'Analysis', 'Analysis', 'Auswertung'),
('Navigation', 'Accounts', 'Accounts', 'Konten'),
('Navigation', 'Account1', 'Checking Account', 'Girokonto'),
('Navigation', 'Account2', 'Visa-Card', 'Visa-Karte'),
('Navigation', 'Account3', 'Paypal', 'Paypal'),
('Navigation', 'Account4', 'Savings Account', 'Sparkonto'),
('Navigation', 'Documentation', 'Documentation', 'Dokumentation'),
('Navigation', 'Print', 'Print', 'Drucken'),
('Navigation', 'PrintView', 'Print view', 'Druckansicht'),
('Navigation', 'PrintPDF', 'Save as PDF', 'Als PDF speichern'),
('accountOverview', 'noAccountID', 'noAccountID', 'es wurde keine AccountID übermittelt'),
('forecast', 'toolTipAccountSelect', 'Please choose the account for the forecast', 'Bitte wählen Sie das Konto für den Forecast'),
('forecast', 'sendData', 'Create chart', 'Diagramm erstellen'),
('Account', 'One-time Transaction', 'FinishedTransaction', 'FinishedTransaction'),
('Account', 'Reoccuring transaction', 'PlannedTransaction', 'PlannedTransaction'),
('Account', 'Einmalige Transaktion', 'FinishedTransaction', 'FinishedTransaction'),
('Account', 'Wiederkehrende Transaktion', 'PlannedTransaction', 'PlannedTransaction'),
('forecast', 'lowerLimit', 'Lower Limit', 'Unteres Limit'),
('forecast', 'upperLimit', 'Upper Limit', 'Oberes Limit'),
('forecast', 'plannedTransactions', 'Trend (recurring transactions)', 'Verlauf (wiederkehrende Transaktionen)'),
('forecast', 'pocketMoney1', 'Trend (pocket money 1)', 'Verlauf (Taschengeld 1)'),
('forecast', 'pocketMoney2', 'Trend (pocket money 2)', 'Verlauf (Taschengeld 2)'),
('forecast', 'savingTarget', 'Saving target', 'Verlauf (Sparziel)'),
('accountCurrency', 'colSymbol', 'Symbol', 'Kürzel'),
('accountCurrency', 'colLongName', 'long name', 'Bezeichnung'),
('Navigation', 'BackupCreate', 'Create', 'Sichern'),
('Navigation', 'BackupUpload', 'Upload', 'Einspielen'),
('accountCategory', 'colparentTitle', 'Category', 'Kategorie'),
('accountCategory', 'colTitle', 'Sub category', 'Unterkategorie'),
('accountCategory', 'colDescription', 'Description', 'Beschreibung'),
('accountCategory', 'colOutsideCapital', 'Outside capital', 'Fremdkapital'),
('accountAccount', 'colTitle', 'Title', 'Titel'),
('accountAccount', 'colBalance', 'Balance', 'Kontostand'),
('accountAccount', 'colCurrency', 'Currency', 'Währung'),
('accountOverview', 'colTitle', 'Title', 'Titel'),
('accountOverview', 'colType', 'Type', 'Typ'),
('accountOverview', 'colDescription', 'Description', 'Beschreibung'),
('accountOverview', 'colValutaDate', 'Valuta date', 'Datum'),
('accountOverview', 'colAmount', 'Amount', 'Betrag'),
('accountOverview', 'colCategoryTitle', 'Category', 'Kategorie'),
('about', 'title', 'About BADGER finance', 'Über BADGER finance'),
('about', 'from', 'from', 'von'),
('about', 'published', 'Published under', 'Veröffentlicht unter'),
('about', 'members', 'The members of the BADGER-Developer-Team.', 'Die Mitglieder des BADGER-Entwicklungs-Teams.'),
('about', 'team', 'Developer-Team', 'Entwicklungs-Team'),
('about', 'programms', 'Used programms and components', 'Verwendete Programme und Komponenten'),
('about', 'by', 'by', 'von'),
('importCsv', 'selectToolTip', 'Checked transactions will be imported.', 'Markierte Transaktionen werden importiert.'),
('importCsv', 'categoryToolTip', 'Please choose a category .', 'Wählen sie bitte eine Kategorie.'),
('importCsv', 'valuedateToolTip', 'Please enter the posting date.', 'Bitte geben sie das Buchungsdatum ein.'),
('importCsv', 'titleToolTip', 'Please enter the reason for transfer.', 'Bitte geben sie den Verwendungszweck der Transaktion ein.'),
('importCsv', 'amountToolTip', 'Please insert the amount of the transaction.', 'Bitte geben sie den Wert der Transaktion ein.'),
('importCsv', 'transactionPartnerToolTip', 'Please enter the partner of the transaction.', 'Bitte geben sie den Transaktionspartner ein.'),
('importCsv', 'descriptionToolTip', 'Please enter a description.', 'Bitte geben sie eine Beschreibung ein.'),
('importCsv', 'periodicalToolTip', 'This setting is used for automatic pocket money calculation. When calculating your pocket money from the past (i.e. your regular money spending habits), the BADGER will ignore all transactions marked &quot;periodical&quot; because it assumes that you have those already covered in the future recurring transactions. An example would be your rent. For the future rent, you have entered a recurring transactions. Past rent payments are flagged &quot;periodical transactions&quot; and not used for pocket money calculation.', 'Diese Wert wird bei der automatischen Taschengeldberechnung benutzt. Wenn der BADGER das Taschengeld der Vergangenheit (also Ihr Ausgabeverhalten) berechnet, ignoriert er periodische Transaktionen, da angenommen wird, dass diese über wiederkehrende Transaktionen in der Zukunft bereits erfasst sind. Ein Beispiel hierfür ist die Miete: Für die Zukunft wird die Miete über eine wiederkehrende Transaktion abgebildet, muss also nicht im Taschengeld berücksichtigt werden. In der Vergangenheit sind die Mietzahlungen periodische Transaktionen.'),
('importCsv', 'ExceptionalToolTip', 'This setting is used for automatic pocket money calculation. When calculating your pocket money from the past (i.e. your regular money spending habits), the BADGER will ignore all transactions marked &quot;exceptional&quot; because they do not resemble your usual spending habits. Examples would be a surprise car repair job, a new tv (unless you buy new tvs every month) or a holiday.', 'Diese Wert wird bei der automatischen Taschengeldberechnung benutzt. Wenn der BADGER das Taschengeld der Vergangenheit (also Ihr Ausgabeverhalten) berechnet, ignoriert er außergewöhnliche Transaktionen. Beispiele hierfür sind eine große Autoreparatur, ein neuer Fernseher (wenn man nicht jeden Monat einen neuen kauft) oder ein Urlaub.'),
('importCsv', 'outsideCapitalToolTip', 'If checked the amount of the transaction will be handled as outside capital, not as revenue.  This are planned to be used for statistics and a balance sheet module in upcoming badger reaeses', 'Wenn die Checkbox markiert ist, wird der Wert der Transaktion als Fremdkapital behandelt, nicht als Einnahme. Dies soll in späteren Badgerversionen für Statistiken und eine Bilanz benutzt werden.'),
('importCsv', 'accountToolTip', 'Please choose a an account for the specific transaction.', 'Bitte wählen sie ein Konto für die einzelnen Transaktionen.'),
('forecast', 'endDateField', 'End date', 'Enddatum'),
('forecast', 'endDateToolTip', 'The forecast will be created from today to the selected date. The possible time span depends on your computer, the faster it is, the longer the time span can be. 1 year should be available on every computer.', 'Die Prognose wird vom heutigen Tag bis zu dem hier angegeben Tag erstellt. Der mögliche Zeitraum hängt von Ihrem Rechner ab, je schneller der Rechner, desto länger kann er sein. 1 Jahr sollte aber auf jedem Rechner möglich sein.'),
('forecast', 'accountField', 'Account', 'Konto'),
('forecast', 'accountToolTip', 'Please select the account for the forecast.', 'Bitte wählen Sie das Konto für die Prognose aus.'),
('accountCurrency', 'pageTitleOverview', 'Currency Manager', 'Währungsübersicht'),
('forecast', 'savingTargetField', 'Saving target', 'Sparziel'),
('forecast', 'savingTargetToolTip', 'Please insert your saving target. When the forecast is created, there will be a graph where the balance at the end date reaches the saving target. Furthermore the pocketmoney will be shown, which is available for daily use under the condition, that the saving target has to be reached.', 'Bitte geben Sie Ihr Sparziel ein. Bei der Prognose wird ein Graph ausgegeben, bei dem am Enddatum dieser Kontostand erreicht wird. Außerdem wird der Betrag ausgegeben, der Ihnen täglich zum Ausgeben zur Verfügung steht.'),
('accountCategory', 'pageTitleOverview', 'Category Manager', 'Kategorieübersicht'),
('accountAccount', 'pageTitleOverview', 'Account Manager', 'Kontenübersicht'),
('forecast', 'pocketMoney1Field', 'Pocket money 1', 'Taschengeld 1'),
('forecast', 'pocketMoney1ToolTip', 'Here you can insert an amount, which you want to dispose of every day (=pocket money). If you insert here an amount, a graph will be displayed, which shows the trend of your balances under consideration of the pocket money. Furthermore the balance at the end of the forecast period is shown.', 'Hier können Sie einen Betrag, den sie täglich zur Verfügung haben möchten (=Taschengeld). Wenn Sie hier einen Wert eingeben, wird ein Graph angezeigt, der den Verlauf des Kontostandes anzeigt, wenn Sie diesen Betrag täglich ausgeben. Außerdem wird angezeigt, wie in diesem Falle der Kontostand am Enddatum ist.'),
('accountAccount', 'pageTitle', 'Account properties', 'Kontoeigenschaften'),
('forecast', 'pocketMoney2Field', 'Pocket money 2', 'Taschengeld 2'),
('forecast', 'pocketMoney2ToolTip', 'Here you can insert a second pocket money (see tool tip for pocket money 1). This creates another graph to get an comparision. The balanced at the end of the period will also been shown.', 'Hier können Sie ein weiteres Taschengeld angeben (siehe ToolTip zu Taschengeld 1). Dies erzeugt einen weiteren Graphen zum vergleichen. Der Endkontostand wird ebenfalls angezeigt.'),
('forecast', 'lowerLimitLabel', 'Graph lower limit', 'Graph unteres Limit'),
('forecast', 'lowerLimitToolTip', 'Shows the lower limit in the graph.', 'Zeigt im Diagramm das untere Limit des Zielkontos an.'),
('Navigation', 'New', 'New', 'Neu'),
('Navigation', 'NewAccount', 'New Account', 'Neues Konto'),
('Navigation', 'NewCategory', 'New Category', 'Neue Transaktionskategorie'),
('forecast', 'upperLimitLabel', 'Graph upper limit', 'Graph oberes Limit'),
('forecast', 'upperLimitToolTip', 'Shows the upper limit in the graph.', 'Zeigt im Diagramm das obere Limit des Zielkontos.'),
('accountCurrency', 'pageTitle', 'Edit Currency', 'Währung bearbeiten'),
('accountCategory', 'pageTitle', 'Edit Catagory', 'Kategorie bearbeiten'),
('forecast', 'plannedTransactionsLabel', 'Graph planned transactions', 'Graph geplante Transaktionen'),
('forecast', 'plannedTransactionsToolTip', 'Shows the graph for planned transactions. The saving target and pocket money will not be included.', 'Zeigt den Graph für die geplanten Transaktionen. Es wird kein Sparziel und kein Taschengeld berücksichtigt.'),
('accountOverview', 'pageTitle', 'Transaction overview', 'Transaktionsübersicht'),
('forecast', 'savingTargetLabel', 'Graph saving target', 'Graph mit Sparziel'),
('forecast', 'showSavingTargetToolTip', 'Shows the trend including the saving target.', 'Zeigt den Verlauf des Kontostandes unter Berücksichtigung des Sparzieles an.'),
('accountAccount', 'pageTitleProp', 'Edit Account', 'Konto bearbeiten'),
('forecast', 'pocketMoney1Label', 'Graph pocket money 1', 'Graph Taschengeld 1'),
('forecast', 'showPocketMoney1ToolTip', 'Shows the trend of the account balance including the pocket money 1', 'Zeigt den Verlauf des Kontostandes unter Berücksichtigung des Taschengeldes 1.'),
('forecast', 'pocketMoney2Label', 'Graph pocket money 2', 'Graph Taschengeld 2'),
('forecast', 'showPocketMoney2ToolTip', 'Shows the trend of the account balance including the pocket money 2', 'Zeigt den Verlauf des Kontostandes unter Berücksichtigung des Taschengeldes 2'),
('forecast', 'noGraphchosen', 'No graph was chosen to display.', 'Kein Graph zum Anzeigen gewählt.'),
('forecast', 'noLowerLimit', 'The selected account has no lower limit.', 'Das gewählte Konto hat kein unteres Limit.'),
('forecast', 'noUpperLimit', 'The selected account has no upper limit.', 'Das gewählte Konto hat kein oberes Limit.'),
('statistics', 'accColTitle', 'Title', 'Titel'),
('statistics', 'accColBalance', 'Balance', 'Kontostand'),
('statistics', 'accColCurrency', 'Currency', 'Währung'),
('forecast', 'onlyFutureDates', 'The enddate have to be in the future. For data from the past please use the statistics.', 'Das Enddatum muss in der Zukunft liegen. Für Vergangenheitsdaten benutzen Sie bitte die Statistiken.'),
('statistics', 'pageTitle', 'Statistics', 'Statistik erstellen'),
('accountTransaction', 'title', 'Title', 'Titel'),
('statistics', 'type', 'Type', 'Typ'),
('statistics', 'category', 'Category', 'Kategorie-Art'),
('statistics', 'period', 'Period', 'Zeitraum'),
('statistics', 'catMerge', 'Category merge', 'Kategorien zusammenfassen'),
('statistics', 'accounts', 'Accounts', 'Konten'),
('statistics', 'attention', 'Attention: No currency conversion takes place during display of accounts with different currencies.', 'Achtung: Bei der gleichzeitigen Betrachtung mehrerer Konten mit unterschiedlichen Währungen findet keine Umrechnung statt!'),
('statistics', 'from', 'From', 'Vom'),
('statistics', 'to', 'to', 'bis'),
('accountTransaction', 'beginDate', 'Begin date', 'Startdatum'),
('accountTransaction', 'endDate', 'End date', 'Enddatum'),
('statistics', 'jan', 'January', 'Januar'),
('statistics', 'feb', 'February', 'Februar'),
('accountTransaction', 'repeatUnit', 'Repeat unit', 'Einheit'),
('accountTransaction', 'repeatFrequency', 'Repeat frequency', 'Intervall'),
('forecast', 'dailyPocketMoneyLabel', 'Pocket money for reaching saving Target', 'Taschengeld um Sparziel zu erreichen'),
('forecast', 'dailyPocketMoneyToolTip', 'Money, that can be spent every day, if the saving target should be reached. If negative, this amount has to be to be earned every day.', 'Geld, das maximal täglich zur Verfügung steht, wenn das Sparziel erreicht werden soll. Wenn negativ, muss im Durchschnitt jeden Tag soviel Geld eingenommen werden.'),
('statistics', 'mar', 'March', 'März'),
('statistics', 'apr', 'April', 'April'),
('statistics', 'may', 'May', 'Mai'),
('statistics', 'jun', 'June', 'Juni'),
('statistics', 'jul', 'July', 'Juli'),
('statistics', 'aug', 'August', 'August'),
('statistics', 'sep', 'September', 'September'),
('statistics', 'oct', 'October', 'Oktober'),
('statistics', 'nov', 'November', 'November'),
('statistics', 'dec', 'December', 'Dezember'),
('forecast', 'printedPocketMoney1Label', 'Balance at the end date (pocket money 1', 'Kontostand am Enddatum (Taschengeld 1'),
('forecast', 'printedPocketMoney2Label', 'Balance at the end date (pocket money 2', 'Kontostand am Enddatum (Taschengeld 2'),
('statistics', 'income', 'Income', 'Einnahmen'),
('statistics', 'expenses', 'Expenses', 'Ausgaben'),
('statistics', 'subCat', 'Merge sub-categories with main-categories', 'Unterkategorien unter der Hauptkategorie zusammenfassen'),
('statistics', 'subCat2', 'Show sub-catagory individually', 'Unterkategorien eigenständig aufführen'),
('statistics', 'errorMissingAcc', 'You did not choose an account.', 'Sie haben noch kein Konto ausgewählt.'),
('statistics', 'errorDate', 'Start date before end date.', 'Das Startdatum liegt nicht vor dem Enddatum.'),
('statistics', 'errorEndDate', 'End date in the future.', 'Das Enddatum liegt in der Zukunft.'),
('forecast', 'legendSetting', 'Parameter', 'Parameter'),
('forecast', 'legendGraphs', 'Select graphs', 'Graphen auswählen'),
('accountTransaction', 'pageTitle', 'Transaction', 'Transaktion'),
('csv', 'title', 'CSV-Import', 'CSV-Import'),
('UserSettingsAdmin', 'title', 'User Settings', 'Einstellungen'),
('forecast', 'title', 'Forecast', 'Prognose'),
('about', 'contributors', 'Contributors', 'Mitwirkende'),
('csv', 'legend', 'Properties', 'Eigenschaften'),
('askInsert', 'legend', 'Import', 'Import'),
('askExport', 'legend', 'Export', 'Export'),
('accountTransaction', 'newPlannedTrans', 'New recurring transaction', 'Neue wiederkehrende Transaktion'),
('accountTransaction', 'newFinishedTrans', 'New single transaction', 'Neue einmalige Transaktion'),
('CategoryManager', 'no_parent', 'No parent category', 'Keine Elternkategorie'),
('accountAccount', 'legend', 'Properties', 'Eigenschaften'),
('accountCategory', 'legend', 'Properties', 'Eigenschaften'),
('accountCurrency', 'legend', 'Properties', 'Eigenschaften'),
('Navigation', 'NewTransactionFinished', 'New Transaction (single)', 'Neue Transaktion (einmalig)'),
('Navigation', 'NewTransactionPlanned', 'New Transaction (recurring)', 'Neue Transaktion (wiederkehrend)'),
('accountTransaction', 'headingTransactionFinished', 'Single transaction', 'Einmalige Transaktion'),
('accountTransaction', 'headingTransactionPlanned', 'Recurring transaction', 'Wiederkehrende Transaktion'),
('accountTransaction', 'Account', 'Account', 'Konto'),
('forecast', 'calculatedPocketMoneyLabel', 'Automatically calculate pocket money 2', 'Taschengeld 2 automatisch berechnen'),
('forecast', 'calculatedPocketMoneyToolTip', 'If you press this button, a pocket money will be generated automatically and written to the pocket money 2 field. For the calculation every transaction between the selected date & today will be used, which are not marked as exceptional or periodical.', 'Wenn Sie den Button drücken, wird automatisch aus der Datenbank ein Taschengeld generiert und in das Feld Taschengeld 2 geschrieben. Beim berechnen werden alle Transaktionen berücksichtigt, die zwischen dem hier angewähltem Datum und heute liegen, und nicht als regelmäßig oder außergewöhnlich markiert sind.'),
('accountOverview', 'colSum', 'Sum', 'Summe'),
('forecast', 'calculatedPocketMoneyButton', 'Calculate', 'Berechnen'),
('statistics', 'noCategoryAssigned', '(not assigned)', '(nicht zugeordnet)'),
('badger', 'PrintMessage', 'Print', 'Drucken'),
('forecast', 'performanceWarning', 'Please pay attention to this fact before pressing the button: If the time span between today and the end date is too long, a message from the macromedia flash player appears. In this case please reduce the time span for the forecast. During the test on different computers a forecast between 1 up to 4 years were possible.', 'Bitte beachten Sie: Je weiter das Enddatum in der Zukunft liegt, desto länger dauert das Erstellen des Diagrammes. Wenn es zu weit in der Zukunft liegt, kann es zu einer Meldung des Macromedia Flash Players kommen. Verkürzen Sie in diesem Fall die Prognosedauer. Je nach Testrechner waren Prognosen zwischen 1 und 4 Jahren möglich.'),
('Navigation', 'SQLError', 'An SQL error occured attempting to fetch the navigation data from the database.', 'Beim Abrufen der Navigations-Daten aus der Datenbank trat ein SQL-Fehler auf.'),
('Navigation', 'UnknownNavigationId', 'An unknown id of an navigation entry was used.', 'Es wurde eine unbekannte ID eines Navigationseintrags benutzt.'),
('statistics', 'trend', 'Trend', 'Trend'),
('statistics', 'categories', 'Categories', 'Kategorien'),
('accountOverviewPlanned', 'noAccountID', 'noAccountID', 'es wurde keine AccountID übermittelt'),
('accountOverviewPlanned', 'pageTitle', 'Recurring transaction overview', 'Übersicht wiederkehrender Transaktionen'),
('accountOverviewPlanned', 'colBeginDate', 'Begin Date', 'Startdatum'),
('accountOverviewPlanned', 'colEndDate', 'End date', 'Enddatum'),
('accountOverviewPlanned', 'colUnit', 'Unit', 'Einheit'),
('accountOverviewPlanned', 'colFrequency', 'Interval', 'Intervall'),
('dataGrid', 'edit', 'Edit', 'Bearbeiten'),
('dataGrid', 'NoRowSelectedMsg', 'Please, select a row to edit', 'Bitte selektieren eine Zeile, die sie bearbeiten wollen.'),
('jsVal', 'err_form', 'Please enter/select values for the following fields:\\n\\n', 'Bitte geben Sie die Werte für folgende Felder ein:\\n\\n'),
('jsVal', 'err_select', 'Please select a valid "%FIELDNAME%"', 'Bitte wählen Sie einen gültigen Wert für "%FIELDNAME%"'),
('jsVal', 'err_enter', 'Please enter a valid "%FIELDNAME%"', 'Bitte geben Sie einen gültigen Wert für "%FIELDNAME%" ein'),
('accountCurrency', 'currencyIsStillUsed', 'The Currency is still used. You cannot delete it.', 'Die Währung wird noch verwendet und kann daher nicht gelöscht werden.'),
('accountCategory', 'deleteMsg', 'Do you really want to delete the selected categories? Note: All transactions using the selected categories will lose their categorization information and become uncategorized transactions.', 'Wollen sie die selektierten Kategorien wirklich löschen?\\nHinweis: Von allen Transaktionen, die diese Kategorie(n) verwenden, wird die Kategorie zurückgesetzt.'),
('accountAccount', 'deleteMsg', 'Do you really want to delete the selected accounts with all transactions?', 'Wollen sie die selektierten Konten wirklich mit allen Transaktionen löschen?'),
('badger_login', 'backend_not_login', 'Error: You do not have permission to access this page.', 'Fehler: Sie haben keine Berechtigung, auf diese Seite zuzugreifen.'),
('CategoryManager', 'outsideCapital', 'Outside Capital', 'Fremdkapital'),
('CategoryManager', 'ownCapital', 'Own Capital', 'Eigenkapital'),
('dataGrid', 'legend', 'Legend', 'Legende'),
('UserSettingsAdmin', 'autoExpandPlannedTransactionsName', 'Auto-insert recurring transactions', 'Wiederkehrende Transaktionen automatisch eintragen'),
('UserSettingsAdmin', 'autoExpandPlannedTransactionsDescription', 'If this option is checked, every occuring instance of a recurring transaction is automatically inserted as an single transaction. Uncheck this if you import your transactions from a CSV file on a regular basis.', 'Wenn diese Option ausgewählt wurde, werden eintretende Instanzen einer wiederkehrenden Transaktion automatisch als einmalige Transaktionen eingetragen. Wählen Sie die Option nicht aus, wenn Sie Ihre Transaktionen regelmäßig aus einer CSV-Datei importieren.'),
('accountOverview', 'showPlannedTrans', 'Show recurring transactions', 'Wiederkehrende Transaktionen anzeigen'),
('accountOverviewPlanned', 'showTrans', 'Show all transactions', 'Alle Transaktionen anzeigen'),
('UserSettingsAdmin', 'futureCalcSpanLabel', 'Planning horizon (months)', 'Planungszeitraum in Monaten'),
('UserSettingsAdmin', 'futureCalcSpanDescription', 'Please enter how far into the future you would like to be able to plan. With usability in mind, recurring transactions will only be displayed as far into the future as you enter here. ', 'Geben Sie hier ein, wie weit Sie in die Zukunft planen möchten. Wiedekehrende Transaktionen werden der Übersichtlichkeit wegen nur so weit in die Zukunft dargestellt, wie Sie hier eingeben.'),
('statistics', 'trendTotal', 'Total', 'Gesamt'),
('accountAccount', 'pageTitlePropNew', 'New Account', 'Konto erstellen'),
('badger_login', 'sessionTimeout', 'Your session timed out. You have been logged out for security reasons.', 'Ihre Sitzung ist abgelaufen. Sie wurden aus Sicherheitsgründen ausgeloggt.'),
('updateProcedure', 'step1PostLink', '', ''),
('updateProcedure', 'step2PreLink', 'Please click the following link to start the database update.', 'Bitte klicken Sie auf folgenden Link, um die Datenbank-Aktualisierung zu beginnen.'),
('updateProcedure', 'step1PreLink', 'Please click the following link and save the file to your computer.', 'Bitte klicken Sie auf folgenden Link und speichern Sie die Datei auf Ihrem Computer.'),
('updateProcedure', 'step1LinkText', 'Save backup', 'Sicherungskopie speichern');
INSERT INTO `i18n` (`page_id`, `id`, `en`, `de`) VALUES ('updateProcedure', 'fileVersionText', 'File version:', 'Datei-Version:'),
('updateProcedure', 'stepDescription', 'The update consists of two simple steps. First, a backup of the database is saved to your computer. This preserves your data in the rare case anything goes wrong. Second, the database is updated.', 'Die Aktualisierung besteht aus zwei einfachen Schritten. Zuerst wird eine Sicherheitskopie der Datenbank auf Ihrem Computer gespeichert. Dadurch bleiben Ihre Daten auch im unwahrscheinlichen Fall eines Fehlschlags erhalten. Anschließend wird die Datenbank aktualisiert.'),
('updateProcedure', 'dbVersionText', 'Database version:', 'Datenbank-Version:'),
('updateProcedure', 'legend', 'Steps to Update', 'Schritte zur Aktualisierung'),
('updateProcedure', 'updateInformation', 'BADGER finance detected an update of its files. This page updates the database. All your data will be preserved.', 'BADGER finance hat eine Aktualisierung seiner Dateien festgestellt. Diese Seite aktualisiert die Datenbank. Ihre Daten bleiben vollständig erhalten.'),
('updateProcedure', 'pageTitle', 'Update BADGER finance', 'BADGER finance aktualisieren'),
('updateProcedure', 'step2LinkText', 'Update database', 'Datenbank aktualisieren'),
('updateProcedure', 'step2PostLink', '', ''),
('updateUpdate', 'pageTitle', 'Updating BADGER finance', 'BADGER finance wird aktualisiert'),
('updateUpdate', 'betweenVersionsText', 'Versions in between:', 'Dazwischenliegende Versionen:'),
('updateUpdate', 'preCurrentText', 'Update from', 'Aktualisierung von'),
('updateUpdate', 'postCurrentText', 'to', 'auf'),
('updateUpdate', 'postNextText', '', ''),
('updateUpdate', 'logEntryHeader', 'Information from the update:', 'Informationen der Aktualisierung:'),
('updateUpdate', 'updateInformation', 'BADGER finance is now performing the update. It is performed step-by-step, one step for each version.', 'Die Aktualisierung wird nun durchgeführt. Dies findet Schritt für Schritt statt, einen Schritt für jede Version.'),
('updateUpdate', 'errorInformation', 'Please read the output of the process. If it encounters any severe errors they are written in red. In this case, please send the whole output to the BADGER development team (see help for contact info).', 'Bitte lesen sie die Ausgabe dieses Prozesses. Die einfachen Informationen sind auf Englisch gehalten. Falls der Prozess irgend welche schweren Fehler meldet, sind diese rot eingefärbt. Bitte schicken Sie in diesem Fall die gesamte Ausgabe an das BADGER Entwicklungsteam (siehe Hilfe für Kontaktinformationen).'),
('updateUpdate', 'updateFinished', 'The update has finished.', 'Die Aktualisierung ist beendet.'),
('updateUpdate', 'severeError', 'The update encountered a severe error. Please send the whole output to the BADGER finance development team.', 'Die Aktualisierung stieß auf einen schweren Fehler. Bitte schicken Sie die gesamte Ausgabe an das BADGER finance development team.'),
('updateUpdate', 'goToStartPagePreLink', 'Please ', 'Bitte '),
('updateUpdate', 'goToStartPageLinkText', 'go to start page', 'zur Startseite gehen'),
('updateUpdate', 'goToStartPagePostLink', ' to continue.', ' um fortzusetzen.'),
('importExport', 'goToStartPagePreLink', 'Please ', 'Bitte '),
('importExport', 'goToStartPageLinkText', 'go to start page', 'zur Startseite gehen'),
('importExport', 'goToStartPagePostLink', ' to continue.', ' um fortzusetzen.'),
('importExport', 'newerVersion', 'Your backup file was from a previous version of BADGER finance. A database update will occur.', 'Ihre Sicherheitskopie war von einer vorherigen Version von BADGER finance. Es wird eine Datenbank-Aktualisierung stattfinden.'),
('DateFormats', 'mm/dd/yy', 'mm/dd/yy', 'mm/tt/jj'),
('statistics', 'showButton', 'Show', 'Anzeigen'),
('dataGrid', 'open', 'Open', 'Öffnen'),
('Navigation', 'releaseNotes', 'Release Notes', 'Versionsgeschichte (englisch)'),
('welcome', 'pageTitle', 'Your accounts', 'Ihre Konten');

-- --------------------------------------------------------

-- 
-- Table structure for table `langs`
-- 

DROP TABLE IF EXISTS `langs`;
CREATE TABLE IF NOT EXISTS `langs` (
  `id` varchar(16) NOT NULL default '',
  `name` varchar(200) default NULL,
  `meta` text,
  `error_text` varchar(250) default NULL,
  `encoding` varchar(16) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `langs`
-- 

INSERT INTO `langs` (`id`, `name`, `meta`, `error_text`, `encoding`) VALUES ('de', 'deutsch', 'Hochdeutsch', 'not avaiable', 'iso-8859-1'),
('en', 'english', 'normal english', 'not avaiable', 'iso-8859-1');

-- --------------------------------------------------------

-- 
-- Table structure for table `navi`
-- 

DROP TABLE IF EXISTS `navi`;
CREATE TABLE IF NOT EXISTS `navi` (
  `navi_id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `menu_order` int(11) NOT NULL,
  `item_type` char(1) NOT NULL,
  `item_name` varchar(255) default NULL,
  `tooltip` varchar(255) default NULL,
  `icon_url` varchar(255) default NULL,
  `command` varchar(255) default NULL,
  PRIMARY KEY  (`navi_id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

-- 
-- Dumping data for table `navi`
-- 

INSERT INTO `navi` (`navi_id`, `parent_id`, `menu_order`, `item_type`, `item_name`, `tooltip`, `icon_url`, `command`) VALUES (23, 22, 2, 'm', 'Backup', NULL, 'server_go.gif', '{BADGER_ROOT}/modules/importExport/importExport.php'),
(22, 0, 6, 'm', 'System', NULL, 'system.gif', NULL),
(21, 31, 3, 'i', 'AccountManager', '', 'manageaccount.gif', '{BADGER_ROOT}/modules/account/AccountManagerOverview.php'),
(16, 0, 9, 'i', 'Logout', NULL, 'cancel.gif', '?logout=true'),
(17, 22, 1, 'i', 'Preferences', NULL, 'cog.gif', '{BADGER_ROOT}/core/UserSettingsAdmin/UserSettingsAdmin.php'),
(1, 22, 3, 'i', 'CurrencyManager', '', 'coins.gif', '{BADGER_ROOT}/modules/account/CurrencyManagerOverview.php'),
(24, 31, 5, 'i', 'CSV-Import', NULL, 'csvimport.gif', '{BADGER_ROOT}/modules/csvImport/csvImport.php'),
(25, 30, 5, 'i', 'Forecast', NULL, 'forecast.gif', '{BADGER_ROOT}/modules/forecast/forecast.php'),
(26, 30, 4, 'i', 'Statistics', NULL, 'statistics.gif', '{BADGER_ROOT}/modules/statistics/statistics.php'),
(27, 31, 4, 'i', 'CategoryManager', NULL, 'categories.gif', '{BADGER_ROOT}/modules/account/CategoryManagerOverview.php'),
(28, 0, 8, 'm', 'Help', NULL, 'help.gif', NULL),
(29, 28, 9, 'i', 'About', NULL, 'information.gif', '{BADGER_ROOT}/core/about.php'),
(30, 0, 4, 'm', 'Analysis', NULL, 'analysis.gif', NULL),
(31, 0, 1, 'm', 'Accounts', NULL, 'accounts.gif', NULL),
(32, 31, 7, 'i', 'Account1', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=1'),
(33, 31, 8, 'i', 'Account2', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=2'),
(34, 31, 8, 'i', 'Account3', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=4'),
(35, 31, 10, 'i', 'Account4', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=3'),
(36, 28, 2, 'i', 'Documentation', NULL, 'docu.gif', 'javascript:showBadgerHelp();'),
(55, 28, 10, 'i', 'releaseNotes', NULL, 'information.gif', 'javascript:showReleaseNotes();'),
(40, 23, 1, 'i', 'BackupCreate', NULL, 'savebackup.gif', '{BADGER_ROOT}/modules/importExport/importExport.php?mode=export'),
(41, 23, 2, 'i', 'BackupUpload', NULL, 'addbackup.gif', '{BADGER_ROOT}/modules/importExport/importExport.php?mode=import'),
(42, 31, 5, 's', NULL, NULL, NULL, NULL),
(43, 31, 2, 's', NULL, NULL, NULL, NULL),
(44, 31, 1, 'm', 'New', NULL, 'add.gif', NULL),
(47, 44, 4, 'i', 'NewAccount', NULL, 'newaccount.gif', '{BADGER_ROOT}/modules/account/AccountManager.php?action=new'),
(48, 44, 5, 'i', 'NewCategory', NULL, 'new_transactioncategory.gif', '{BADGER_ROOT}/modules/account/CategoryManager.php?action=new'),
(49, 28, 8, 's', NULL, NULL, NULL, NULL),
(53, 0, 7, 'i', 'Print', NULL, 'printer.gif', 'javascript:window.print();'),
(50, 44, 3, 's', NULL, NULL, NULL, NULL),
(51, 44, 2, 'i', 'NewTransactionPlanned', NULL, 'planned_transaction_new.gif', '{BADGER_ROOT}/modules/account/Transaction.php?action=new&type=planned'),
(52, 44, 1, 'i', 'NewTransactionFinished', NULL, 'finished_transaction_new.gif', '{BADGER_ROOT}/modules/account/Transaction.php?action=new&type=finished');

-- --------------------------------------------------------

-- 
-- Table structure for table `navi_ids_seq`
-- 

DROP TABLE IF EXISTS `navi_ids_seq`;
CREATE TABLE IF NOT EXISTS `navi_ids_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=latin1 AUTO_INCREMENT=57 ;

-- 
-- Dumping data for table `navi_ids_seq`
-- 

INSERT INTO `navi_ids_seq` (`id`) VALUES (56);

-- --------------------------------------------------------

-- 
-- Table structure for table `planned_transaction`
-- 

DROP TABLE IF EXISTS `planned_transaction`;
CREATE TABLE IF NOT EXISTS `planned_transaction` (
  `planned_transaction_id` int(10) unsigned NOT NULL auto_increment,
  `category_id` int(10) unsigned default NULL,
  `account_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text,
  `amount` decimal(20,2) default NULL,
  `outside_capital` tinyint(1) NOT NULL default '0',
  `transaction_partner` varchar(100) default NULL,
  `begin_date` date default NULL,
  `end_date` date default NULL,
  `repeat_unit` char(5) default NULL,
  `repeat_frequency` int(10) unsigned default NULL,
  PRIMARY KEY  (`planned_transaction_id`),
  KEY `planned_transaction_FKIndex1` (`account_id`),
  KEY `planned_transaction_FKIndex2` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `planned_transaction`
-- 

INSERT INTO `planned_transaction` (`planned_transaction_id`, `category_id`, `account_id`, `title`, `description`, `amount`, `outside_capital`, `transaction_partner`, `begin_date`, `end_date`, `repeat_unit`, `repeat_frequency`) VALUES (6, 1, 1, 'Miete', 'Miete für Musterstr. 16', '-420.00', 0, NULL, '2006-03-01', NULL, 'month', 1),
(5, 4, 1, 'Gehalt', 'Mein Gehalt', '1357.00', 0, 'Arbeitgeber AG', '2006-02-28', NULL, 'month', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `planned_transaction_ids_seq`
-- 

DROP TABLE IF EXISTS `planned_transaction_ids_seq`;
CREATE TABLE IF NOT EXISTS `planned_transaction_ids_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `planned_transaction_ids_seq`
-- 

INSERT INTO `planned_transaction_ids_seq` (`id`) VALUES (7);

-- --------------------------------------------------------

-- 
-- Table structure for table `session_global`
-- 

DROP TABLE IF EXISTS `session_global`;
CREATE TABLE IF NOT EXISTS `session_global` (
  `sid` varchar(100) NOT NULL,
  `variable` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`sid`,`variable`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `session_global`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `session_master`
-- 

DROP TABLE IF EXISTS `session_master`;
CREATE TABLE IF NOT EXISTS `session_master` (
  `sid` varchar(255) NOT NULL,
  `id` int(11) NOT NULL,
  `start` datetime NOT NULL,
  `last` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `logout` tinyint(4) NOT NULL,
  PRIMARY KEY  (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `session_master`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `user_settings`
-- 

DROP TABLE IF EXISTS `user_settings`;
CREATE TABLE IF NOT EXISTS `user_settings` (
  `prop_key` varchar(100) NOT NULL,
  `prop_value` text,
  PRIMARY KEY  (`prop_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `user_settings`
-- 

INSERT INTO `user_settings` (`prop_key`, `prop_value`) VALUES ('badgerTemplate', 's:8:"Standard";'),
('badgerSiteName', 's:14:"BADGER Finance";'),
('badgerLanguage', 's:2:"en";'),
('badgerDateFormat', 's:10:"yyyy-mm-dd";'),
('badgerPassword', 's:32:"fe01ce2a7fbac8fafaed7c982a04e229";'),
('badgerMaxLoginAttempts', 's:1:"5";'),
('badgerLockOutTime', 's:2:"30";'),
('badgerDecimalSeparator', 's:1:",";'),
('badgerThousandSeparator', 's:1:".";'),
('badgerSessionTime', 's:4:"9999";'),
('badgerStartPage', 's:19:"modules/welcome.php";'),
('accountNaviNextPosition', 's:2:"11";'),
('accountNaviParent', 's:2:"31";'),
('accountNaviId_1', 's:2:"32";'),
('accountNaviId_2', 's:2:"33";'),
('accountNaviId_3', 's:2:"35";'),
('accountNaviId_4', 's:2:"34";'),
('forecastStandardAccount', 's:0:"";'),
('csvImportStandardParser', 's:0:"";'),
('csvImportStandardAccount', 's:0:"";'),
('autoExpandPlannedTransactions', 'b:1;'),
('badgerDbVersion', 's:10:"1.0 beta 2";');
