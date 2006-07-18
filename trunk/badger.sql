CREATE DATABASE badgerbank;
USE badgerbank;

-- phpMyAdmin SQL Dump
-- version 2.7.0-pl1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jul 11, 2006 at 12:15 PM
-- Server version: 5.0.18
-- PHP Version: 5.1.1
-- 
-- Database: `badgerbank`
-- 

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- 
-- Dumping data for table `account`
-- 

INSERT INTO `account` VALUES (1, 1, 'Girokonto', 'Deutsche Bank Kto-Nr.: 12345678', -1000.00, 2000.00);
INSERT INTO `account` VALUES (2, 1, 'Visa-Karte', 'Visa Kredit-Karte', -3000.00, NULL);
INSERT INTO `account` VALUES (3, 1, 'Tagesgeldkonto', 'Konto mit täglicher Verfügbarkeit, höhere Zinsen', 0.00, 3000.00);
INSERT INTO `account` VALUES (4, 2, 'Paypal', 'Pay Pal Account geführt in Dollar', 0.00, 1000.00);

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
-- Table structure for table `accountIds_seq`
-- 

DROP TABLE IF EXISTS `accountIds_seq`;
CREATE TABLE IF NOT EXISTS `accountIds_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `accountIds_seq`
-- 

INSERT INTO `accountIds_seq` VALUES (5);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `category`
-- 

INSERT INTO `category` VALUES (1, NULL, 'Miete', 'Transaktionen die mit Miete zu tun haben.', 0);
INSERT INTO `category` VALUES (2, NULL, 'Auto', 'Auto-Transaktionen', 0);
INSERT INTO `category` VALUES (3, 11, 'Haushalt', NULL, 1);
INSERT INTO `category` VALUES (4, NULL, 'Gehalt', NULL, 0);
INSERT INTO `category` VALUES (5, NULL, 'Kommunikation', 'Kommunikationsausgaben', 0);
INSERT INTO `category` VALUES (6, 11, 'Kleidung', NULL, 0);
INSERT INTO `category` VALUES (7, NULL, 'Studium', NULL, 0);
INSERT INTO `category` VALUES (8, NULL, 'Sparen', NULL, 0);
INSERT INTO `category` VALUES (9, 7, 'Buecher', NULL, 0);
INSERT INTO `category` VALUES (10, 7, 'Buerokratie', NULL, 0);
INSERT INTO `category` VALUES (11, NULL, 'Lebensführung', NULL, 0);
INSERT INTO `category` VALUES (12, 11, 'Lebensmittel', NULL, 0);
INSERT INTO `category` VALUES (13, 11, 'Luxus und Genuss', NULL, 0);
INSERT INTO `category` VALUES (14, 2, 'Instandhaltung', NULL, 0);
INSERT INTO `category` VALUES (15, 2, 'Benzin', NULL, 0);
INSERT INTO `category` VALUES (16, NULL, 'Sonstiges', NULL, 0);
INSERT INTO `category` VALUES (17, NULL, 'Hobbies und Freizeit', NULL, 0);
INSERT INTO `category` VALUES (18, NULL, 'Bargeld', NULL, 0);
INSERT INTO `category` VALUES (19, NULL, 'Gesundheit', NULL, 0);

-- --------------------------------------------------------

-- 
-- Table structure for table `categoryIds_seq`
-- 

DROP TABLE IF EXISTS `categoryIds_seq`;
CREATE TABLE IF NOT EXISTS `categoryIds_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

-- 
-- Dumping data for table `categoryIds_seq`
-- 

INSERT INTO `categoryIds_seq` VALUES (20);

-- --------------------------------------------------------

-- 
-- Table structure for table `csv_parser`
-- 

DROP TABLE IF EXISTS `csv_parser`;
CREATE TABLE IF NOT EXISTS `csv_parser` (
  `parser_id` int(11) NOT NULL auto_increment,
  `parser_title` varchar(255) NOT NULL,
  `parser_filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`parser_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- 
-- Dumping data for table `csv_parser`
-- 

INSERT INTO `csv_parser` VALUES (1, 'Deutsche Bank', 'deutschebank.php');
INSERT INTO `csv_parser` VALUES (2, 'Postbank', 'postbank.php');
INSERT INTO `csv_parser` VALUES (3, 'Sparkasse', 'sparkasse.php');
INSERT INTO `csv_parser` VALUES (4, 'ING DiBa (Extra Konto)', 'INGDiBa.php');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `currency`
-- 

INSERT INTO `currency` VALUES (1, 'Euro', 'EUR');
INSERT INTO `currency` VALUES (2, 'Dollar', 'USD');

-- --------------------------------------------------------

-- 
-- Table structure for table `currencyIds_seq`
-- 

DROP TABLE IF EXISTS `currencyIds_seq`;
CREATE TABLE IF NOT EXISTS `currencyIds_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- 
-- Dumping data for table `currencyIds_seq`
-- 

INSERT INTO `currencyIds_seq` VALUES (3);

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

INSERT INTO `datagrid_handler` VALUES ('AccountManager', '/modules/account/AccountManager.class.php', 'AccountManager');
INSERT INTO `datagrid_handler` VALUES ('Account', '/modules/account/Account.class.php', 'Account');
INSERT INTO `datagrid_handler` VALUES ('CategoryManager', '/modules/account/CategoryManager.class.php', 'CategoryManager');
INSERT INTO `datagrid_handler` VALUES ('CurrencyManager', '/modules/account/CurrencyManager.class.php', 'CurrencyManager');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=91 ;

-- 
-- Dumping data for table `finished_transaction`
-- 

INSERT INTO `finished_transaction` VALUES (2, 4, 1, 'Gehalt', NULL, '2005-06-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (3, 4, 1, 'Gehalt', '', '2005-07-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (4, 1, 1, 'Gehalt', NULL, '2005-08-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (5, 4, 1, 'Gehalt', NULL, '2005-09-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (6, 4, 1, 'Gehalt', NULL, '2005-10-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (7, 4, 1, 'Gehalt', NULL, '2005-11-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (8, 4, 1, 'Gehalt', NULL, '2005-12-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (9, 4, 1, 'Gehalt', NULL, '2006-01-30', 1357.00, 0, 'Arbeitgeber AG', 1, 0, 5);
INSERT INTO `finished_transaction` VALUES (10, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-07-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (11, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-08-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (12, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-09-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (13, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-10-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (14, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-11-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (15, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2005-12-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (16, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2006-01-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (17, 1, 1, 'Miete', 'Miete für Musterstr. 16', '2006-02-01', -420.00, 0, NULL, 1, 0, 6);
INSERT INTO `finished_transaction` VALUES (18, 14, 1, 'Neue Benzinpumpe', NULL, '2005-10-12', -200.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (19, 14, 1, 'Scheibenwischer', 'Wer klaut denn jemandem einfach die Scheibenwischer? Das ist doch gemein!', '2005-11-18', -53.00, 0, 'ATU', 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (20, 15, 1, 'Tanken', NULL, '2005-07-01', -62.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (21, 15, 1, 'Tanken', NULL, '2005-07-20', -53.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (22, 15, 1, 'Tanken', NULL, '2005-08-07', -53.45, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (23, 15, 1, 'Tanken', NULL, '2005-08-25', -53.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (24, 15, 1, 'Tanken', NULL, '2005-09-18', -44.45, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (25, 15, 1, 'Tanken', NULL, '2005-09-30', -52.13, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (26, 15, 1, 'Tanken', NULL, '2005-10-12', -53.45, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (27, 15, 1, 'Tanken', NULL, '2005-10-29', -47.88, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (28, 15, 1, 'Tanken', NULL, '2005-11-07', -61.22, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (29, 15, 1, 'Tanken', NULL, '2005-11-18', -33.34, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (30, 15, 1, 'Tanken', NULL, '2005-12-20', -58.38, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (31, 15, 1, 'Tanken', NULL, '2005-12-30', -50.50, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (32, 15, 0, 'Tanken', NULL, '2006-01-18', -48.33, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (33, 15, 0, 'Tanken', NULL, '2006-01-31', -50.10, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (34, 15, 0, 'Tanken', NULL, '2006-02-12', -20.50, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (35, 15, 0, 'Tanken', NULL, '2006-02-12', -12.45, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (36, 3, 1, 'Teppichreinigung', NULL, '2005-10-30', -120.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (37, 3, 1, 'Fenster ersetzt', 'Diese Nachbarskinder', '2006-01-11', -312.50, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (38, 6, 1, 'Neuer Anzug', NULL, NULL, -215.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (39, 6, 1, 'Socken, Shirts', NULL, NULL, -45.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (40, 13, 2, 'Wellness-Wochenende', NULL, '2005-01-07', -210.45, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (41, 13, 2, 'Maniküre', NULL, '2005-09-15', -33.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (42, 9, 1, 'Buch: Solvency II im Unternehmen', NULL, '2006-01-10', -50.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (43, 9, 0, 'Wöhe', NULL, '2005-07-15', -54.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (44, 10, 1, 'Bachelor', NULL, '2006-01-03', -172.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (45, 10, 1, 'Studentenwerksbeitrag', NULL, '2005-12-01', -182.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (46, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-07-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (47, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-08-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (48, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-09-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (49, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-10-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (50, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-11-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (51, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2005-12-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (52, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2006-01-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (53, 8, 1, 'Fonds-Sparen', '100 Euro Überweisen an DWS Investment Fonds', '2006-02-01', -100.00, 0, NULL, 1, 0, NULL);
INSERT INTO `finished_transaction` VALUES (54, 4, 1, 'Bonus', 'Bonus für sehr gute Arbeit', '2006-01-02', 2000.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (55, 17, 2, 'Lenkdrache gekauft', NULL, '2005-09-13', -80.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (56, 17, 2, 'Tauchkurs', 'PADI Open Water Diver ', '2005-11-23', -355.00, 0, NULL, 0, 1, NULL);
INSERT INTO `finished_transaction` VALUES (57, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-07-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (58, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-08-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (59, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-09-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (60, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-10-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (61, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-11-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (62, 5, 1, 'Mobiltelefonrechnung', NULL, '2005-12-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (63, 5, 1, 'Mobiltelefonrechnung', NULL, '2006-01-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (64, 5, 1, 'Mobiltelefonrechnung', NULL, '2006-02-03', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (65, 12, 1, 'Fritten und Bier', NULL, '2005-07-01', -15.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (66, 12, 1, 'Aldi', NULL, '2005-07-02', -45.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (67, 12, 1, 'Aldi', NULL, '2005-07-11', -65.45, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (68, 12, 1, 'Getränke', NULL, '2005-07-17', -34.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (69, 12, 1, 'Getränke', NULL, '2005-08-12', -32.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (70, 12, 1, 'Aldi', NULL, '2005-08-20', -30.45, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (71, 12, 1, 'Maredo', NULL, '2005-08-30', -80.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (72, 12, 1, 'Kartoffeln', NULL, '2005-09-10', -13.66, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (73, 12, 1, 'Aldi', NULL, '2005-09-12', -13.66, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (74, 12, 1, 'Kantine', NULL, '2005-09-23', -2.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (75, 12, 1, 'Aldi', NULL, '2005-10-01', -30.48, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (76, 12, 1, 'Getränke', NULL, '2005-10-22', -62.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (77, 12, 1, 'Getränke', NULL, '2005-11-02', -39.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (78, 12, 1, 'Bier', NULL, '2005-11-07', -20.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (79, 12, 1, 'Aldi', NULL, '2005-11-19', -42.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (80, 12, 1, 'Fritten und Bier', NULL, '2005-11-30', -35.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (81, 12, 1, 'Süpermarket', NULL, '2005-12-03', -33.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (82, 12, 1, 'Aldi', NULL, '2005-12-08', -42.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (83, 12, 1, 'Bier', NULL, '2005-12-20', -80.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (84, 12, 1, 'Getränke', NULL, '2005-12-30', -39.23, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (85, 12, 1, 'Rollmöpse', NULL, '2006-01-01', -32.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (86, 12, 1, 'Aldi', NULL, '2006-01-06', -30.48, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (87, 12, 1, 'Kantine', NULL, '2006-01-18', -29.00, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (88, 12, 1, 'Aldi', NULL, '2006-01-30', -43.66, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (89, 12, 1, 'Kaffee', NULL, '2006-02-10', -13.66, 0, NULL, 0, 0, NULL);
INSERT INTO `finished_transaction` VALUES (90, 12, 1, 'Gummibärchen', NULL, '2006-01-12', -40.00, 0, NULL, 0, 0, NULL);

-- --------------------------------------------------------

-- 
-- Table structure for table `finishedTransactionIds_seq`
-- 

DROP TABLE IF EXISTS `finishedTransactionIds_seq`;
CREATE TABLE IF NOT EXISTS `finishedTransactionIds_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=92 ;

-- 
-- Dumping data for table `finishedTransactionIds_seq`
-- 

INSERT INTO `finishedTransactionIds_seq` VALUES (91);

-- --------------------------------------------------------

-- 
-- Table structure for table `i18n`
-- 

DROP TABLE IF EXISTS `i18n`;
CREATE TABLE IF NOT EXISTS `i18n` (
  `page_id` varchar(50) default NULL,
  `id` text NOT NULL,
  `en` text,
  `de` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `i18n`
-- 

INSERT INTO `i18n` VALUES ('Calendar', 'gotoString', 'Go To Current Month', 'Gehe zu aktuellem Monat');
INSERT INTO `i18n` VALUES ('Calendar', 'todayString', 'Today is', 'Heute ist');
INSERT INTO `i18n` VALUES ('Calendar', 'weekString', 'Wk', 'KW');
INSERT INTO `i18n` VALUES ('Calendar', 'scrollLeftMessage', 'Click to scroll to previous month. Hold mouse button to scroll automatically.', 'Klicken, um zum vorigen Monat zu gelangen. Gedr&uuml;ckt halten, um automatisch weiter zu scrollen.');
INSERT INTO `i18n` VALUES ('Calendar', 'scrollRightMessage', 'Click to scroll to next month. Hold mouse button to scroll automatically.', 'Klicken, um zum n&auml;chsten Monat zu gelangen. Gedr&uuml;ckt halten, um automatisch weiter zu scrollen.');
INSERT INTO `i18n` VALUES ('Calendar', 'selectMonthMessage', 'Click to select a month.', 'Klicken, um Monat auszuw&auml;hlen');
INSERT INTO `i18n` VALUES ('Calendar', 'selectYearMessage', 'Click to select a year.', 'Klicken, um Jahr auszuw&auml;hlen');
INSERT INTO `i18n` VALUES ('Calendar', 'selectDateMessage', 'Select [date] as date.', 'W&auml;hle [date] als Datum.');
INSERT INTO `i18n` VALUES ('Calendar', 'closeCalendarMessage', 'Click to close the calendar.', 'Klicken, um den Kalender zu schlie&szlig;en.');
INSERT INTO `i18n` VALUES ('Calendar', 'monthName', 'new Array(\\''January\\'',\\''February\\'',\\''March\\'',\\''April\\'',\\''May\\'',\\''June\\'',\\''July\\'',\\''August\\'',\\''September\\'',\\''October\\'',\\''November\\'',\\''December\\'')', 'new Array(\\''Januar\\'',\\''Februar\\'',\\''M&auml;rz\\'',\\''April\\'',\\''Mai\\'',\\''Juni\\'',\\''Juli\\'',\\''August\\'',\\''September\\'',\\''Oktober\\'',\\''November\\'',\\''Dezember\\'')');
INSERT INTO `i18n` VALUES ('Calendar', 'monthName2', 'new Array(\\''JAN\\'',\\''FEB\\'',\\''MAR\\'',\\''APR\\'',\\''MAY\\'',\\''JUN\\'',\\''JUL\\'',\\''AUG\\'',\\''SEP\\'',\\''OCT\\'',\\''NOV\\'',\\''DEC\\'')', 'new Array(\\''JAN\\'',\\''FEB\\'',\\''MRZ\\'',\\''APR\\'',\\''MAI\\'',\\''JUN\\'',\\''JUL\\'',\\''AUG\\'',\\''SEP\\'',\\''OKT\\'',\\''NOV\\'',\\''DEZ\\'')');
INSERT INTO `i18n` VALUES ('Calendar', 'dayNameStartsWithMonday', 'new Array(\\''Mon\\'',\\''Tue\\'',\\''Wed\\'',\\''Thu\\'',\\''Fri\\'',\\''Sat\\'',\\''Sun\\'')', 'new Array(\\''Mo\\'',\\''Di\\'',\\''Mi\\'',\\''Do\\'',\\''Fr\\'',\\''Sa\\'',\\''So\\'')');
INSERT INTO `i18n` VALUES ('Calendar', 'dayNameStartsWithSunday', 'new Array(\\''Sun\\'',\\''Mon\\'',\\''Tue\\'',\\''Wed\\'',\\''Thu\\'',\\''Fri\\'',\\''Sat\\'')', 'new Array(\\''So\\'',\\''Mo\\'',\\''Di\\'',\\''Mi\\'',\\''Do\\'',\\''Fr\\'',\\''Sa\\'')');
INSERT INTO `i18n` VALUES ('badgerException', 'Errorcode', 'Error code', 'Fehlermeldung');
INSERT INTO `i18n` VALUES ('badgerException', 'Error', 'Error', 'Fehler');
INSERT INTO `i18n` VALUES ('badgerException', 'Line', 'Line', 'Zeile');
INSERT INTO `i18n` VALUES ('statistics', 'fullYear', 'Full year', 'ganzes Jahr');
INSERT INTO `i18n` VALUES ('Navigation', 'Logout', 'Logout', 'Abmelden');
INSERT INTO `i18n` VALUES ('Navigation', 'Preferences', 'Preferences', 'Einstellungen');
INSERT INTO `i18n` VALUES ('html2pdf', 'missing_url', 'No Source URL to create a PDF document from.', 'Quell-URL zum Generieren des PDFs nicht übergeben.');
INSERT INTO `i18n` VALUES ('Navigation', 'AccountManager', 'Accounts overview', 'Kontenübersicht');
INSERT INTO `i18n` VALUES ('importCsv', 'upload', 'Upload', 'Upload');
INSERT INTO `i18n` VALUES ('importCsv', 'noSeperator', 'File cannot be read by this parser. No seperator found', 'Datei kann mit diesem Parser nicht gelesen werden. Kein Trennzeichen gefunden');
INSERT INTO `i18n` VALUES ('importCsv', 'selectFile', 'Please select your CSV file', 'Bitte wählen Sie die CSV Datei aus');
INSERT INTO `i18n` VALUES ('Navigation', 'Help', 'Help', 'Hilfe');
INSERT INTO `i18n` VALUES ('importCsv', 'selectParser', 'Select Input Parser', 'CSV Format wählen');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'date_format_description', 'Sets the date format to be used.', 'Legt das zu verwendende Datumsformat fest.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'change_password_heading', 'Change Password', 'Passwort ändern');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'maximum_login_attempts_name', 'Maximum Login Attempts:', 'Maximale Loginversuche:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'new_password_confirm_description', 'Please confirm your entered password here.', 'Hier bitte das eingegebene Passwort bestätigen.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'maximum_login_attempts_description', 'After how many failed login attempts should the access be temporarily denied?', 'Nach wie vielen fehlgeschlagenen Loginversuchen wird der Zugang temporär gesperrt?');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'linktext_after_successful_mandatory_change', 'Continue work...', 'Weiter...');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'lock_out_time_description', 'How many seconds should the access be denied?', 'Wie viele Sekunden wird die Sperre des Logins aufrecht erhalten?');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'lock_out_time_name', 'Duration of Lockout (sec):', 'Dauer der Zugangssperre (Sek.):');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'mandatory_change_password_heading', 'You are currently using the BADGER standard password.<br/>Please change it.', 'Sie verwenden momentan das BADGER Standardpasswort.<br/>Bitte ändern Sie es.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'language_name', 'Language:', 'Sprache:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'linktext_after_failed_mandatory_change', 'Try again...', 'Nochmal versuchen...');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'orderParamNoArray', 'The parameter to DataGridHandler::setOrder() is no array!', 'Der Parameter von DataGridHandler::setOrder() ist kein Array!');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'orderArrayElementNoArray', 'The array passed to DataGridHandler::setOrder() contains a non-array element at index:', 'Das an DataGridHandler::setOrder() übergebene Array enthält an folgendem Index ein Nicht-Array-Element:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'orderKeyIndexNotDefined', 'The index ''key'' is not defined in the following element of the parameter to DataGridHandler::setOrder():', 'Der Index ''key'' ist im folgenden Element des Parameters von DataGridHandler::setOrder() nicht definiert:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'orderDirIndexNotDefined', 'The index ''dir'' is not defined in the following element of the parameter to DataGridHandler::setOrder():', 'Der Index ''dir'' ist im folgenden Element des Parameters von DataGridHandler::setOrder() nicht definiert:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'orderIllegalField', 'The following field is not known to this DataGridHandler:', 'Das folgende Feld ist diesem DataGridHandler nicht bekannt:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'orderIllegalDirection', 'The following illegal order direction was passed to DataGridHandler:', 'Die folgende ungültige Sortierrichtung wurde an DataGridHandler übergeben:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'filterParamNoArray', 'The parameter to DataGridHandler::setFilter() is no array!', 'Der Parameter von DataGridHandler::setFilter() ist kein Array!');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'filterArrayElementNoArray', 'The array passed to DataGridHandler::setFilter() contains a non-array element at index:', 'Das an DataGridHandler::setFilter() übergebene Array enthält an folgendem Index ein Nicht-Array-Element:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'filterKeyIndexNotDefined', 'The index ''key'' is not defined in the following element of the parameter to DataGridHandler::setFilter():', 'Der Index ''key'' ist im folgenden Element des Parameters von DataGridHandler::setFilter() nicht definiert:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'filterOpIndexNotDefined', 'The index ''op'' is not defined in the following element of the parameter to DataGridHandler::setFilter():', 'Der Index ''op'' ist im folgenden Element des Parameters von DataGridHandler::setFilter() nicht definiert:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'filterValIndexNotDefined', 'The index ''val'' is not defined in the following element of the parameter to DataGridHandler::setFilter():', 'Der Index ''val'' ist im folgenden Element des Parameters von DataGridHandler::setFilter() nicht definiert:');
INSERT INTO `i18n` VALUES ('DataGridHandler', 'filterIllegalField', 'The following field is not known to this DataGridHandler:', 'Das folgende Feld ist diesem DataGridHandler nicht bekannt:');
INSERT INTO `i18n` VALUES ('AccountManager', 'invalidFieldName', 'The following field is not known to AccountManager:', 'Das folgende Feld ist AccountManager nicht bekannt:');
INSERT INTO `i18n` VALUES ('AccountManager', 'SQLError', 'An SQL error occured attempting to fetch the AccountManager data from the database:', 'Beim Abrufen der AccountManager-Daten aus der Datenbank trat ein SQL-Fehler auf:');
INSERT INTO `i18n` VALUES ('UserSettings', 'illegalKey', 'The following key is not defined in UserSettings:', 'Der folgende Schlüssel wurde in UserSettings nicht definiert:');
INSERT INTO `i18n` VALUES ('DataGridRepository', 'illegalHandlerName', 'The following DataGridHandler is not known to BADGER:', 'Der folgende DataGridHandler ist BADGER nicht bekannt:');
INSERT INTO `i18n` VALUES ('DataGridXML', 'undefinedColumns', 'DataGridXML::getXML() was called without setting columns!', 'DataGridXML::getXML() wurde aufgerufen, ohne vorher die Spalten zu definieren!');
INSERT INTO `i18n` VALUES ('DataGridXML', 'XmlSerializerException', 'An error occured in DataGridXML::getXML() while transforming internal data to XML.', 'Beim Umwandeln von internen Daten in XML trat in DataGridXML::getXML() ein Fehler auf.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'language_description', 'Sets the language to be used.', 'Legt die zu verwendende Sprache fest.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'new_password_confirm_name', 'Confirm new password:', 'Neues Passwort bestätigen:');
INSERT INTO `i18n` VALUES ('badger_login', 'submit_button', 'Submit', 'Senden');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'new_password_description', 'If you want to set a new password, please enter it here.', 'Falls sie ein neues Passwort festlegen wollen, geben Sie es hier ein.');
INSERT INTO `i18n` VALUES ('badger_login', 'you_are_logout', 'You have successfully logged out.', 'Sie haben sich erfolgreich ausgeloggt.');
INSERT INTO `i18n` VALUES ('badger_login', 'locked_out_refresh', 'Ban over?', 'Sperre schon vorrüber?');
INSERT INTO `i18n` VALUES ('badger_login', 'sent_password_failed', 'An error occured during sendig of the e-mail.', 'Beim Senden der E-Mail trat ein Fehler auf.');
INSERT INTO `i18n` VALUES ('badger_login', 'locked_out_part_2', ' seconds.', ' Sekunden.');
INSERT INTO `i18n` VALUES ('badger_login', 'locked_out_part_1', 'Because of too many failed login attempts you cannot login right now.<br/>The ban will be in effect for another ', 'Aufgrund zu häufiger fehlgeschlagener Loginversuche können sie sich leider derzeit nicht einloggen.<br/>Diese Sperre besteht noch für weitere ');
INSERT INTO `i18n` VALUES ('badger_login', 'ask_really_send_link', 'Send the new password!', 'Neues Passwort schicken!');
INSERT INTO `i18n` VALUES ('badger_login', 'sent_password', 'A new password was sent to your e-mail adresse.', 'Ein neues Passwort wurde an die hinterlegte E-Mail Adresse gesendet.');
INSERT INTO `i18n` VALUES ('badger_login', 'ask_really_send', 'Really send a new password? Your old password will no longer work.', 'Möchten Sie sich wirklich ein neues Passwort zuschicken lassen? Ihr altes Passwort wird hiermit ungültig.');
INSERT INTO `i18n` VALUES ('badger_login', 'empty_password', 'Error: No password submitted!', 'Fehler: Kein Passwort eingegeben!');
INSERT INTO `i18n` VALUES ('badger_login', 'header', 'Login', 'Einloggen');
INSERT INTO `i18n` VALUES ('badger_login', 'wrong_password', 'Error: Wrong Password!', 'Fehler: Falsches Passwort!');
INSERT INTO `i18n` VALUES ('badger_login', 'forgot_password', 'Forgot your password?', 'Passwort vergessen?');
INSERT INTO `i18n` VALUES ('badger_login', 'enter_password', 'Please enter your password:', 'Bitte geben Sie ihr Passwort ein:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_confirm_failed', 'The passwords don´t match.', 'Die Passwörter stimmen nicht überein.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_empty_password', 'Password mus have at least one letter.', 'Passwort muss mindestens ein Zeichen haben.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_old_password_not_correct', 'Old password not correct.', 'Altes Passwort nicht korrekt.');
INSERT INTO `i18n` VALUES ('importCsv', 'targetAccount', 'Please select your target account', 'Bitte wählen Sie das Zielkonto aus');
INSERT INTO `i18n` VALUES ('importCsv', 'wrongSeperatorNumber', 'File cannot be read by this parser. At least 1 line has not the right number of seperators', 'Datei kann mit diesem Parser nicht gelesen werden. Mindestens 1 Zeile enthält nicht die richtige Anzahl an Trennzeichen');
INSERT INTO `i18n` VALUES ('importCsv', 'select', 'Transfer', 'Übernehmen');
INSERT INTO `i18n` VALUES ('importCsv', 'category', 'Category', 'Kategorie');
INSERT INTO `i18n` VALUES ('importCsv', 'account', 'Account', 'Konto');
INSERT INTO `i18n` VALUES ('importCsv', 'title', 'Title', 'Verwendungszweck');
INSERT INTO `i18n` VALUES ('importCsv', 'description', 'Description', 'Beschreibung');
INSERT INTO `i18n` VALUES ('importCsv', 'valutaDate', 'Valuta Date', 'Buchungsdatum');
INSERT INTO `i18n` VALUES ('importCsv', 'amount', 'Amount', 'Betrag');
INSERT INTO `i18n` VALUES ('importCsv', 'transactionPartner', 'Transaction Partner', 'Transaktionspartner');
INSERT INTO `i18n` VALUES ('importCsv', 'save', 'Write to Database', 'In Datenbank schreiben');
INSERT INTO `i18n` VALUES ('importCsv', 'successfullyWritten', 'transactions successfully written to db', 'Transaktionen erfolgreich in die Datenbank geschrieben');
INSERT INTO `i18n` VALUES ('importCsv', 'noTransactionSelected', 'No transactions selected', 'Keine Transaktionen ausgewählt');
INSERT INTO `i18n` VALUES ('Account', 'invalidFieldName', 'The following field is not known to Account:', 'Das folgende Feld ist Account nicht bekannt:');
INSERT INTO `i18n` VALUES ('Account', 'SQLError', 'An SQL error occured attempting to fetch the Account data from the database:', 'Beim Abrufen der Account-Daten aus der Datenbank trat ein SQL-Fehler auf:');
INSERT INTO `i18n` VALUES ('CategoryManager', 'invalidFieldName', 'An unknown field was used in CategoryManager.', 'Im CategoryManager wurde ein ungültiges Feld verwendet.');
INSERT INTO `i18n` VALUES ('CategoryManager', 'SQLError', 'An SQL error occured attempting to fetch the CategoryManager data from the database.', 'Beim Abrufen der CategoryManager-Daten aus der Datenbank trat ein SQL-Fehler auf.');
INSERT INTO `i18n` VALUES ('Account', 'UnknownFinishedTransactionId', 'An unknown id was used for a single transaction.', 'Es wurde eine unbekannte ID einer einmaligen Transaktion benutzt.');
INSERT INTO `i18n` VALUES ('Account', 'insertError', 'An error occured while inserting a new single transaction into the database.', 'Beim Einfügen einer neuen einmaligen Transaktion trat ein Fehler auf.');
INSERT INTO `i18n` VALUES ('AccountManager', 'UnknownAccountId', 'An unknown id of an account was used.', 'Es wurde eine unbekannte ID eines Kontos benutzt.');
INSERT INTO `i18n` VALUES ('AccountManager', 'insertError', 'An error occured while inserting a new account in the database.', 'Beim Einfügen eines neuen Kontos trat ein Fehler auf.');
INSERT INTO `i18n` VALUES ('FinishedTransaction', 'SQLError', 'An SQL error occured attempting to edit the single transaction data in the database.', 'Beim Bearbeiten der Daten einer einmaligen Transaktion in der Datenbank trat ein SQL-Fehler auf.');
INSERT INTO `i18n` VALUES ('CategoryManager', 'UnknownCategoryId', 'An unknown id of a category was used.', 'Es wurde eine unbekannte ID einer Kategorie benutzt.');
INSERT INTO `i18n` VALUES ('CategoryManager', 'insertError', 'An error occured while inserting a new category in the database.', 'Beim Einfügen einer neuen Kategorie trat ein Fehler auf.');
INSERT INTO `i18n` VALUES ('Category', 'SQLError', 'An SQL error occured attempting to edit the Category data in the database:', 'Beim Bearbeiten der Category-Daten in der Datenbank trat ein SQL-Fehler auf:');
INSERT INTO `i18n` VALUES ('importCsv', 'periodical', 'Periodical', 'Regelmäßig');
INSERT INTO `i18n` VALUES ('importCsv', 'Exceptional', 'Exceptional', 'Außergewöhnlich');
INSERT INTO `i18n` VALUES ('importCsv', 'toolTipParserSelect', 'Choice of the csv parser. If your bank is not available or if there is a error when you upload, please visit our homepage. There perhaps you can find a proper parser or get support.', 'Auswahl des CSV Parsers. Wenn Ihre Bank nicht vorhanden ist oder es beim Upload zu Fehlern kommt, schauen Sie bitte auf unsere Website. Dort gibt es evtl. den passenden Parser oder Support.');
INSERT INTO `i18n` VALUES ('intervalUnits', 'day', 'day', 'Tag');
INSERT INTO `i18n` VALUES ('intervalUnits', 'week', 'week', 'Woche');
INSERT INTO `i18n` VALUES ('intervalUnits', 'month', 'month', 'Monat');
INSERT INTO `i18n` VALUES ('intervalUnits', 'year', 'year', 'Jahr');
INSERT INTO `i18n` VALUES ('intervalUnits', 'every', 'every', 'jede(n)/(s)');
INSERT INTO `i18n` VALUES ('importCsv', 'toolTopAccountSelect', 'Your accounts. You can administrate your accounts in the account manager.', 'Ihre Konten. Änderungen können Sie in der Kontoverwaltung vornehmen.');
INSERT INTO `i18n` VALUES ('templateEngine', 'noTemplate', 'Template not found.', 'Template nicht gefunden.');
INSERT INTO `i18n` VALUES ('widgetsEngine', 'ToolTipJSNotAdded', 'Method $widgets->addToolTipJS(); has not been evoked.', 'Die Methode $widgets->addToolTipJS(); wurde nicht vorher aufrufen.');
INSERT INTO `i18n` VALUES ('widgetsEngine', 'ToolTipLayerNotAdded', 'The method echo $widgets->addToolTipLayer(); has not been evoked.', 'Die Methode echo $widgets->addToolTipLayer(); wurde nicht vorher vorher aufrufen.');
INSERT INTO `i18n` VALUES ('widgetsEngine', 'CalendarJSNotAdded', 'The method $widgets->addCalendarJS(); has not been evoked.', 'Die Methode $widgets->addCalendarJS(); wurde nicht vorher vorher aufrufen.');
INSERT INTO `i18n` VALUES ('widgetsEngine', 'AutoCompleteJSNotAdded', 'The method $widgets->addAutoCompleteJS(); has not been evoked.', 'Die Methode $widgets->addAutoCompleteJS(); wurde nicht vorher vorher aufrufen.');
INSERT INTO `i18n` VALUES ('Account', 'FinishedTransaction', 'Single Transaction', 'Einmalige Transaktion');
INSERT INTO `i18n` VALUES ('Account', 'PlannedTransaction', 'Recurring transaction', 'Wiederkehrende Transaktion');
INSERT INTO `i18n` VALUES ('Account', 'day', 'daily', 'täglich');
INSERT INTO `i18n` VALUES ('Account', 'week', 'weekly', 'wöchentlich');
INSERT INTO `i18n` VALUES ('Account', 'month', 'monthly', 'monatlich');
INSERT INTO `i18n` VALUES ('Account', 'year', 'yearly', 'jährlich');
INSERT INTO `i18n` VALUES ('Account', 'UnknownPlannedTransactionId', 'An unknown id of a recurring transaction was used.', 'Es wurde eine unbekannte ID einer wiederkehrenden Transaktion benutzt.');
INSERT INTO `i18n` VALUES ('Account', 'IllegalRepeatUnit', 'An illigeal unit was given for a recurring transaction.', 'Für eine wiederkehrende Transaktion wurde eine ungültige Wiederholungseinheit angegeben.');
INSERT INTO `i18n` VALUES ('Account', 'illegalPropertyKey', 'An unknown property key was used for an account.', 'Für ein Konto wurde ein ungültiger Eigenschaftsschlüssel verwendet.');
INSERT INTO `i18n` VALUES ('importCsv', 'outsideCapital', 'Outside capital', 'Fremdkapital');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_standard_password', 'Please don´t use the standard password.', 'Bitte nicht das Standardpasswort verwenden.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'new_password_name', 'New password:', 'Neues Passwort:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'old_password_description', 'Please enter your old password.', 'Bitte geben Sie ihr altes Passwort an.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'old_password_name', 'Old password:', 'Altes Passwort:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'password_change_commited', 'Password was changed successfully.', 'Passwort wurde erfolgreich geändert.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'seperators_description', 'Sets the number format to be used.', 'Legt das zu verwendende Zahlenformat fest.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'seperators_name', 'Seperators: ', 'Trennzeichen: ');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'session_time_description', 'Defines after how much time of inactivity a new login is neccessary.', 'Legt fest, nach wie langer Inaktivität ein erneutes Login nötig ist.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'session_time_name', 'Session time:', 'Sessionlänge:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'site_name', 'User Settings', 'Einstellungen');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'start_page_description', 'Defines the page to display at the start of BADGER.', 'Legt die Seite fest, die beim Start vom BADGER angezeigt wird.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'start_page_name', 'Start page:', 'Startseite:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'submit_button', 'Save', 'Speichern');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'template_description', 'A theme determines the look of BADGER finance.', 'Ein Theme bestimmt das grundlegende Aussehen von BADGER finance.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'template_name', 'Theme:', 'Theme:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'user_settings_change_commited', 'User settings have been successfully commit', 'Nutzereinstellungen wurden erfolgreich gespeichert.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'user_settings_heading', 'User Settings', 'Einstellungen');
INSERT INTO `i18n` VALUES ('DateFormats', 'dd.mm.yyyy', 'dd.mm.yyyy', 'tt.mm.jjjj');
INSERT INTO `i18n` VALUES ('DateFormats', 'dd/mm/yyyy', 'dd/mm/yyyy', 'tt/mm/jjjj');
INSERT INTO `i18n` VALUES ('DateFormats', 'dd-mm-yyyy', 'dd-mm-yyyy', 'tt-mm-jjjj');
INSERT INTO `i18n` VALUES ('DateFormats', 'yyyy-mm-dd', 'yyyy-mm-dd', 'jjjj-mm-tt');
INSERT INTO `i18n` VALUES ('DateFormats', 'yyyy/mm/dd', 'yyyy/mm/dd', 'jjjj/mm/tt');
INSERT INTO `i18n` VALUES ('Currency', 'SQLError', 'An SQL error occured attempting to edit the Currency data in the database:', 'Beim Bearbeiten der Währungs-Daten in der Datenbank trat ein SQL-Fehler auf:');
INSERT INTO `i18n` VALUES ('CurrencyManager', 'invalidFieldName', 'An unknown field was used in CurrencyManager.', 'Im CurrencyManager wurde ein ungültiges Feld verwendet.');
INSERT INTO `i18n` VALUES ('CurrencyManager', 'SQLError', 'An SQL error occured attempting to fetch the CurrencyManager data from the database.', 'Beim Abrufen der CurrencyManager-Daten aus der Datenbank trat ein SQL-Fehler auf.');
INSERT INTO `i18n` VALUES ('CurrencyManager', 'UnknownCurrencyId', 'An unknown id of a currency was used.', 'Es wurde eine unbekannte ID einer Währung benutzt.');
INSERT INTO `i18n` VALUES ('CurrencyManager', 'insertError', 'An error occured while inserting a new currency in the database.', 'Beim Einfügen einer neuen Währung trat ein Fehler auf.');
INSERT INTO `i18n` VALUES ('PlannedTransaction', 'SQLError', 'An SQL error occured attempting to edit the recurring transactions data in the database.', 'Beim Bearbeiten der Daten einer wiederkehrenden Transaktion in der Datenbank trat ein SQL-Fehler auf.');
INSERT INTO `i18n` VALUES ('templateEngine', 'HeaderIsAlreadyWritten', 'XHTML Head is already added to the document. This function has to be called before writing the header.', 'Der XHTML Kopf wurde bereits in das Dokument eingefügt. Die Funktion muss vor der Ausgabe aufgerufen werden.');
INSERT INTO `i18n` VALUES ('widgetsEngine', 'HeaderIsNotWritten', 'XHTML Header isn''t added to the document. Please call $tpl->getHeader() before this function.', 'Der XHTML Kopf wurde noch nicht in das Dokument eingefügt. Die Funktion $tpl->getHeader() muss vor dieser Funktion aufgerufen werden.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'login_button', 'Login', 'Login');
INSERT INTO `i18n` VALUES ('importCsv', 'noNewTransactions', 'No new transactions found in the csv file.', 'Keine neuen Transaktionen in der CSV Datei gefunden.');
INSERT INTO `i18n` VALUES ('importCsv', 'echoFilteredTransactionNumber', 'transactions were filtered because they were already in the database.', 'Transaktionen gefiltert, da sie bereits in der Datenbank vorhanden sind.');
INSERT INTO `i18n` VALUES ('importExport', 'askTitle', 'Import / Export Data', 'Daten Import / Export');
INSERT INTO `i18n` VALUES ('importExport', 'askExportTitle', 'Export / Backup', 'Export / Datensicherung');
INSERT INTO `i18n` VALUES ('importExport', 'askExportText', 'You can save all of your BADGER finance data in a file. This file will be transmitted to your computer. Save the File at a secure place.', 'Sie können Ihre gesamten BADGER finance Daten in eine Datei sichern. Diese wird direkt auf Ihren Rechner übertragen. Speichern Sie die Datei ab.');
INSERT INTO `i18n` VALUES ('importExport', 'askExportAction', 'Export', 'Exportieren');
INSERT INTO `i18n` VALUES ('importExport', 'askImportTitle', 'Import', 'Import');
INSERT INTO `i18n` VALUES ('importExport', 'askImportInfo', 'You can upload previously saved backup data into BADGER finance.', 'Sie können einen einmal gesicherten Stand der BADGER finance Daten von einer Datei auf Ihrem Rechner zurück an BADGER finance übertragen.');
INSERT INTO `i18n` VALUES ('importExport', 'askImportWarning', 'Warning! When uploading a backup, all current data will be lost and replaced by data from the backup file.', 'Achtung: Beim Import gehen alle bereits vorhandenen Daten in BADGER finance verloren!');
INSERT INTO `i18n` VALUES ('importExport', 'askImportVersionInfo', 'The BADGER finance version you used to generate the backup file must be the same version you are uploading the data into.', 'Die Version von BADGER finance, mit der Sie die Daten gesichert haben, muss mit der installierten Version übereinstimmen.');
INSERT INTO `i18n` VALUES ('importExport', 'askImportCurrentVersionInfo', 'You have the following version of BADGER finance currently installed:', 'Die aktuelle Version von BADGER finance ist:');
INSERT INTO `i18n` VALUES ('importExport', 'askImportAction', 'Import', 'Importieren');
INSERT INTO `i18n` VALUES ('importExport', 'askImportNo', 'No, I do not want to upload the backup data.', 'Nein, ich möchte die Daten nicht importieren.');
INSERT INTO `i18n` VALUES ('importExport', 'askImportYes', 'Yes I want to upload the backup file. All data will be deleted and replaced by the data from the backup file.', 'Ja, ich möchte die Daten importieren. Alle bestehenden Daten werden dabei gelöscht und durch den alten Datenbestand aus der Backup-Datei ersetzt.');
INSERT INTO `i18n` VALUES ('importExport', 'askImportFile', 'Please browse for your backup file:', 'Bitte wählen Sie die Sicherungsdatei aus:');
INSERT INTO `i18n` VALUES ('importExport', 'askImportSubmitButton', 'Import', 'Importieren');
INSERT INTO `i18n` VALUES ('importExport', 'askInsertTitle', 'Data Recovery', 'Datenwiederherstellung');
INSERT INTO `i18n` VALUES ('importExport', 'insertTitle', 'Import', 'Import');
INSERT INTO `i18n` VALUES ('importExport', 'insertNoInsert', 'You chose not to import the backup data.', 'Sie haben sich entschieden, die Daten nicht zu importieren.');
INSERT INTO `i18n` VALUES ('importExport', 'insertSuccessful', 'Data successfully saved.', 'Die Daten wurden erfolgreich importiert.');
INSERT INTO `i18n` VALUES ('importExport', 'noSqlDumpProvided', 'Uploaded file missing.', 'Es wurde keine Datei hochgeladen.');
INSERT INTO `i18n` VALUES ('importExport', 'errorOpeningSqlDump', 'There was a problem processing the uploaded file.', 'Die hochgeladene Datei konnte nicht verarbeitet werden.');
INSERT INTO `i18n` VALUES ('importExport', 'incompatibleBadgerVersion', 'The uploaded file was not a BADGER finance file or a BADGER finance backup file from an uncompatible version.', 'Die hochgeladene Datei ist kein BADGER finance Export oder von einer inkompatiblen BADGER finance Version.');
INSERT INTO `i18n` VALUES ('importExport', 'SQLError', 'There was an Error during execution of the SQL-statement.', 'Beim Verarbeiten eines SQL-Befehls ist ein Fehler aufgetreten.');
INSERT INTO `i18n` VALUES ('importExport', 'insertNoFile', 'Error: You did not upload a file.', 'Fehler: SIe haben keine Datei hochgeladen.');
INSERT INTO `i18n` VALUES ('Navigation', 'CurrencyManager', 'Currencies', 'Währungen');
INSERT INTO `i18n` VALUES ('Navigation', 'System', 'System', 'System');
INSERT INTO `i18n` VALUES ('Navigation', 'Backup', 'Backup', 'Backup');
INSERT INTO `i18n` VALUES ('Navigation', 'CSV-Import', 'Import transactions', 'Transaktionen importieren');
INSERT INTO `i18n` VALUES ('Navigation', 'Forecast', 'Forecast', 'Prognose');
INSERT INTO `i18n` VALUES ('accountCategory', 'title', 'Category name', 'Kategoriename');
INSERT INTO `i18n` VALUES ('accountCategory', 'description', 'Description', 'Beschreibung');
INSERT INTO `i18n` VALUES ('accountAccount', 'title', 'Account name', 'Kontoname');
INSERT INTO `i18n` VALUES ('accountAccount', 'description', 'Description', 'Beschreibung');
INSERT INTO `i18n` VALUES ('accountCategory', 'outsideCapital', 'Outside capital', 'Fremdkapital');
INSERT INTO `i18n` VALUES ('accountAccount', 'lowerLimit', 'lower limit', 'Untergrenze');
INSERT INTO `i18n` VALUES ('accountAccount', 'upperLimit', 'upper limit', 'Obergrenze');
INSERT INTO `i18n` VALUES ('accountCategory', 'parent', 'Parent category', 'Elternkategorie');
INSERT INTO `i18n` VALUES ('accountAccount', 'balance', 'Balance', 'Gesamtkontostand');
INSERT INTO `i18n` VALUES ('accountAccount', 'currency', 'Currency', 'Währung');
INSERT INTO `i18n` VALUES ('accountTransaction', 'description', 'Description', 'Beschreibung');
INSERT INTO `i18n` VALUES ('accountTransaction', 'valutaDate', 'Valuta date', 'Buchungsdatum');
INSERT INTO `i18n` VALUES ('accountAccount', 'targetFutureCalcDate', 'Target future calc date', 'Stichtag');
INSERT INTO `i18n` VALUES ('accountTransaction', 'amount', 'Amount', 'Betrag');
INSERT INTO `i18n` VALUES ('accountTransaction', 'outsideCapital', 'Outside capital', 'Fremdkapital');
INSERT INTO `i18n` VALUES ('accountTransaction', 'transactionPartner', 'Transaction partner', 'Transaktionspartner');
INSERT INTO `i18n` VALUES ('accountTransaction', 'category', 'Category', 'Kategorie');
INSERT INTO `i18n` VALUES ('accountTransaction', 'periodical', 'Periodical transaction', 'Periodische Transaktionen');
INSERT INTO `i18n` VALUES ('accountTransaction', 'exceptional', 'Exceptional transaction', 'Außergewöhnliche Transaktion');
INSERT INTO `i18n` VALUES ('accountCurrency', 'symbol', 'Currency symbol', 'Währungskürzel');
INSERT INTO `i18n` VALUES ('accountCurrency', 'longname', 'Written name of the currency', 'Währungsname');
INSERT INTO `i18n` VALUES ('dataGrid', 'deleteMsg', 'Do you really want to delete the selected records? ', 'Wollen sie die selektierten Datensätze wirklich löschen?');
INSERT INTO `i18n` VALUES ('dataGrid', 'rowCounterName', 'row(s)', 'Datensätze');
INSERT INTO `i18n` VALUES ('dataGrid', 'new', 'New', 'Neu');
INSERT INTO `i18n` VALUES ('dataGrid', 'delete', 'Delete', 'Löschen');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'date_format_name', 'Date Format: ', 'Datumsformat: ');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_confirm_failed', 'The passwords don´t match.', 'Die Passwörter stimmen nicht überein.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_empty_password', 'Password mus have at least one letter.', 'Passwort muss mindestens ein Zeichen haben.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'error_old_password_not_correct', 'Old password not correct.', 'Altes Passwort nicht korrekt.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'new_password_name', 'New password:', 'Neues Passwort:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'old_password_description', 'Please enter your old password.', 'Bitte geben Sie ihr altes Passwort an.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'old_password_name', 'Old password:', 'Altes Passwort:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'password_change_commited', 'Password was changed successfully.', 'Passwort wurde erfolgreich geändert.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'seperators_description', 'Sets the number format to be used.', 'Legt das zu verwendende Zahlenformat fest.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'seperators_name', 'Seperators: ', 'Trennzeichen: ');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'session_time_description', 'Defines after how much time of inactivity a new login is neccessary.', 'Legt fest, nach wie langer Inaktivität ein erneutes Login nötig ist.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'session_time_name', 'Session time:', 'Sessionlänge:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'site_name', 'User Settings Administration', 'Nutzereinstellungsverwaltung');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'start_page_description', 'Defines the page to display at the start of BADGER.', 'Legt die Seite fest, die beim Start vom BADGER angezeigt wird.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'start_page_name', 'Start page:', 'Startseite:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'submit_button', 'Submit', 'Senden');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'template_description', 'A theme determines the look of BADGER finance.', 'Ein Theme bestimmt das grundlegende Aussehen von BADGER finance.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'template_name', 'Theme:', 'Theme:');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'user_settings_change_commited', 'User settings have been successfully commit', 'Nutzereinstellungen wurden erfolgreich gespeichert.');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'user_settings_heading', 'User Settings', 'Nutzereinstellungen');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'login_button', 'Login', 'Login');
INSERT INTO `i18n` VALUES ('badger_login', 'fs_heading', 'Login', 'Login');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'fs_heading', 'User Settings', 'Allgemeine Einstellungen');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'mandatory_fs_heading', 'Password Change', 'Passwortänderung');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'mandatory_commited_fs_heading', 'Password Changed', 'Passwort geändert');
INSERT INTO `i18n` VALUES ('Navigation', 'Statistics', 'Statistics', 'Statistiken');
INSERT INTO `i18n` VALUES ('dataGrid', 'save', 'Save', 'Speichern');
INSERT INTO `i18n` VALUES ('dataGrid', 'back', 'Back', 'Zurück');
INSERT INTO `i18n` VALUES ('dataGrid', 'LoadingMessage', 'Loading ...', 'Lade ...');
INSERT INTO `i18n` VALUES ('Navigation', 'CategoryManager', 'Transaction categories', 'Transaktionskategorien');
INSERT INTO `i18n` VALUES ('Navigation', 'Help', 'Help', 'Hilfe');
INSERT INTO `i18n` VALUES ('Navigation', 'About', 'About Badger', 'Über Badger');
INSERT INTO `i18n` VALUES ('Navigation', 'Analysis', 'Analysis', 'Auswertung');
INSERT INTO `i18n` VALUES ('Navigation', 'Accounts', 'Accounts', 'Konten');
INSERT INTO `i18n` VALUES ('Navigation', 'Account1', 'Checking Account', 'Girokonto');
INSERT INTO `i18n` VALUES ('Navigation', 'Account2', 'Visa-Card', 'Visa-Karte');
INSERT INTO `i18n` VALUES ('Navigation', 'Account3', 'Paypal', 'Paypal');
INSERT INTO `i18n` VALUES ('Navigation', 'Account4', 'Savings Account', 'Sparkonto');
INSERT INTO `i18n` VALUES ('Navigation', 'Documentation', 'Documentation', 'Dokumentation');
INSERT INTO `i18n` VALUES ('Navigation', 'Print', 'Print', 'Drucken');
INSERT INTO `i18n` VALUES ('Navigation', 'PrintView', 'Print view', 'Druckansicht');
INSERT INTO `i18n` VALUES ('Navigation', 'PrintPDF', 'Save as PDF', 'Als PDF speichern');
INSERT INTO `i18n` VALUES ('accountOverview', 'noAccountID', 'noAccountID', 'es wurde keine AccountID übermittelt');
INSERT INTO `i18n` VALUES ('forecast', 'toolTipAccountSelect', 'Please choose the account for the forecast', 'Bitte wählen Sie das Konto für den Forecast');
INSERT INTO `i18n` VALUES ('forecast', 'sendData', 'Create chart', 'Diagramm erstellen');
INSERT INTO `i18n` VALUES ('Account', 'One-time Transaction', 'FinishedTransaction', 'FinishedTransaction');
INSERT INTO `i18n` VALUES ('Account', 'Reoccuring transaction', 'PlannedTransaction', 'PlannedTransaction');
INSERT INTO `i18n` VALUES ('Account', 'Einmalige Transaktion', 'FinishedTransaction', 'FinishedTransaction');
INSERT INTO `i18n` VALUES ('Account', 'Wiederkehrende Transaktion', 'PlannedTransaction', 'PlannedTransaction');
INSERT INTO `i18n` VALUES ('forecast', 'lowerLimit', 'Lower Limit', 'Unteres Limit');
INSERT INTO `i18n` VALUES ('forecast', 'upperLimit', 'Upper Limit', 'Oberes Limit');
INSERT INTO `i18n` VALUES ('forecast', 'plannedTransactions', 'Trend (recurring transactions)', 'Verlauf (wiederkehrende Transaktionen)');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney1', 'Trend (pocket money 1)', 'Verlauf (Taschengeld 1)');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney2', 'Trend (pocket money 2)', 'Verlauf (Taschengeld 2)');
INSERT INTO `i18n` VALUES ('forecast', 'savingTarget', 'Saving target', 'Verlauf (Sparziel)');
INSERT INTO `i18n` VALUES ('accountCurrency', 'colSymbol', 'Symbol', 'Kürzel');
INSERT INTO `i18n` VALUES ('accountCurrency', 'colLongName', 'long name', 'Bezeichnung');
INSERT INTO `i18n` VALUES ('Navigation', 'BackupCreate', 'Create', 'Sichern');
INSERT INTO `i18n` VALUES ('Navigation', 'BackupUpload', 'Upload', 'Einspielen');
INSERT INTO `i18n` VALUES ('accountCategory', 'colparentTitle', 'Category', 'Kategorie');
INSERT INTO `i18n` VALUES ('accountCategory', 'colTitle', 'Sub category', 'Unterkategorie');
INSERT INTO `i18n` VALUES ('accountCategory', 'colDescription', 'Description', 'Beschreibung');
INSERT INTO `i18n` VALUES ('accountCategory', 'colOutsideCapital', 'Outside capital', 'Fremdkapital');
INSERT INTO `i18n` VALUES ('accountAccount', 'colTitle', 'Title', 'Titel');
INSERT INTO `i18n` VALUES ('accountAccount', 'colBalance', 'Balance', 'Kontostand');
INSERT INTO `i18n` VALUES ('accountAccount', 'colCurrency', 'Currency', 'Währung');
INSERT INTO `i18n` VALUES ('accountOverview', 'colTitle', 'Title', 'Titel');
INSERT INTO `i18n` VALUES ('accountOverview', 'colType', 'Type', 'Typ');
INSERT INTO `i18n` VALUES ('accountOverview', 'colDescription', 'Description', 'Beschreibung');
INSERT INTO `i18n` VALUES ('accountOverview', 'colValutaDate', 'Valuta date', 'Datum');
INSERT INTO `i18n` VALUES ('accountOverview', 'colAmount', 'Amount', 'Betrag');
INSERT INTO `i18n` VALUES ('accountOverview', 'colCategoryTitle', 'Category', 'Kategorie');
INSERT INTO `i18n` VALUES ('about', 'title', 'About BADGER finance', 'Über BADGER finance');
INSERT INTO `i18n` VALUES ('about', 'from', 'from', 'von');
INSERT INTO `i18n` VALUES ('about', 'published', 'Published under', 'Veröffentlicht unter');
INSERT INTO `i18n` VALUES ('about', 'members', 'The members of the BADGER-Developer-Team.', 'Die Mitglieder des BADGER-Entwicklungs-Teams.');
INSERT INTO `i18n` VALUES ('about', 'team', 'Developer-Team', 'Entwicklungs-Team');
INSERT INTO `i18n` VALUES ('about', 'programms', 'Used programms and components', 'Verwendete Programme und Komponenten');
INSERT INTO `i18n` VALUES ('about', 'by', 'by', 'von');
INSERT INTO `i18n` VALUES ('importCsv', 'selectToolTip', 'Checked transactions will be imported.', 'Markierte Transaktionen werden importiert.');
INSERT INTO `i18n` VALUES ('importCsv', 'categoryToolTip', 'Please choose a category .', 'Wählen sie bitte eine Kategorie.');
INSERT INTO `i18n` VALUES ('importCsv', 'valuedateToolTip', 'Please enter the posting date.', 'Bitte geben sie das Buchungsdatum ein.');
INSERT INTO `i18n` VALUES ('importCsv', 'titleToolTip', 'Please enter the reason for transfer.', 'Bitte geben sie den Verwendungszweck der Transaktion ein.');
INSERT INTO `i18n` VALUES ('importCsv', 'amountToolTip', 'Please insert the amount of the transaction.', 'Bitte geben sie den Wert der Transaktion ein.');
INSERT INTO `i18n` VALUES ('importCsv', 'transactionPartnerToolTip', 'Please enter the partner of the transaction.', 'Bitte geben sie den Transaktionspartner ein.');
INSERT INTO `i18n` VALUES ('importCsv', 'descriptionToolTip', 'Please enter a description.', 'Bitte geben sie eine Beschreibung ein.');
INSERT INTO `i18n` VALUES ('importCsv', 'periodicalToolTip', 'If checked the transaction will be handled as regularly recurring payments. This is an important function for the forecast option in BADGER. Thereby the transaction will not be used to calculate a pocket money from the past (See statistics)', 'Wenn die Checkbox markiert ist, wird die Transaktion als wiederkehrende Zahlung behandelt. Diese Einstellung ist wichtig für die Prognose Funktion in BADGER. Dadurch wird diese Transaktion beim Errechnen eines Taschengeldes aus der Vergangenheit nicht berücksichtigt. (Siehe Prognose)');
INSERT INTO `i18n` VALUES ('importCsv', 'ExceptionalToolTip', 'If checked the transaction will be handled as an exceptional payment. This is an important function for the forecast option in BADGER. Thereby the transaction will not be used to calculate a pocket money from the past (See statistics).', 'Wenn die Checkbox markiert ist, wird die Transaktion als außergewöhnliche Zahlung behandelt. Diese Einstellung ist wichtig für die Prognose Funktion in BADGER. Dadurch wird diese Transaktion beim Errechnen eines Taschengeldes aus der Vergangenheit nicht berücksichtigt (Siehe Prognose).');
INSERT INTO `i18n` VALUES ('importCsv', 'outsideCapitalToolTip', 'If checked the amount of the transaction will be handled as outside capital, not as revenue.  This are planned to be used for statistics and a balance sheet module in upcoming badger reaeses', 'Wenn die Checkbox markiert ist, wird der Wert der Transaktion als Fremdkapital behandelt, nicht als Einnahme. Dies soll in späteren Badgerversionen für Statistiken und eine Bilanz benutzt werden.');
INSERT INTO `i18n` VALUES ('importCsv', 'accountToolTip', 'Please choose a an account for the specific transaction.', 'Bitte wählen sie ein Konto für die einzelnen Transaktionen.');
INSERT INTO `i18n` VALUES ('forecast', 'endDateField', 'End date', 'Enddatum');
INSERT INTO `i18n` VALUES ('forecast', 'endDateToolTip', 'The forecast will be created from today to the selected date. The possible time span depends on your computer, the faster it is, the longer the time span can be. 1 year should be available on every computer.', 'Die Prognose wird vom heutigen Tag bis zu dem hier angegeben Tag erstellt. Der mögliche Zeitraum hängt von Ihrem Rechner ab, je schneller der Rechner, desto länger kann er sein. 1 Jahr sollte aber auf jedem Rechner möglich sein.');
INSERT INTO `i18n` VALUES ('forecast', 'accountField', 'Account', 'Konto');
INSERT INTO `i18n` VALUES ('forecast', 'accountToolTip', 'Please select the account for the forecast.', 'Bitte wählen Sie das Konto für die Prognose aus.');
INSERT INTO `i18n` VALUES ('accountCurrency', 'pageTitleOverview', 'Currency Manager', 'Währungsübersicht');
INSERT INTO `i18n` VALUES ('forecast', 'savingTargetField', 'Saving target', 'Sparziel');
INSERT INTO `i18n` VALUES ('forecast', 'savingTargetToolTip', 'Please insert your saving target. When the forecast is created, there will be a graph where the balance at the end date reaches the saving target. Furthermore the pocketmoney will be shown, which is available for daily use under the condition, that the saving target has to be reached.', 'Bitte geben Sie Ihr Sparziel ein. Bei der Prognose wird ein Graph ausgegeben, bei dem am Enddatum dieser Kontostand erreicht wird. Außerdem wird der Betrag ausgegeben, der Ihnen täglich zum Ausgeben zur Verfügung steht.');
INSERT INTO `i18n` VALUES ('accountCategory', 'pageTitleOverview', 'Category Manager', 'Kategorieübersicht');
INSERT INTO `i18n` VALUES ('accountAccount', 'pageTitleOverview', 'Account Manager', 'Kontenübersicht');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney1Field', 'Pocket money 1', 'Taschengeld 1');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney1ToolTip', 'Here you can insert an amount, which you want to dispose of every day (=pocket money). If you insert here an amount, a graph will be displayed, which shows the trend of your balances under consideration of the pocket money. Furthermore the balance at the end of the forecast period is shown.', 'Hier können Sie einen Betrag, den sie täglich zur Verfügung haben möchten (=Taschengeld). Wenn Sie hier einen Wert eingeben, wird ein Graph angezeigt, der den Verlauf des Kontostandes anzeigt, wenn Sie diesen Betrag täglich ausgeben. Außerdem wird angezeigt, wie in diesem Falle der Kontostand am Enddatum ist.');
INSERT INTO `i18n` VALUES ('accountAccount', 'pageTitle', 'Account properties', 'Kontoeigenschaften');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney2Field', 'Pocket money 2', 'Taschengeld 2');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney2ToolTip', 'Here you can insert a second pocket money (see tool tip for pocket money 1). This creates another graph to get an comparision. The balanced at the end of the period will also been shown.', 'Hier können Sie ein weiteres Taschengeld angeben (siehe ToolTip zu Taschengeld 1). Dies erzeugt einen weiteren Graphen zum vergleichen. Der Endkontostand wird ebenfalls angezeigt.');
INSERT INTO `i18n` VALUES ('forecast', 'lowerLimitLabel', 'Graph lower limit', 'Graph unteres Limit');
INSERT INTO `i18n` VALUES ('forecast', 'lowerLimitToolTip', 'Shows the lower limit in the graph.', 'Zeigt im Diagramm das untere Limit des Zielkontos an.');
INSERT INTO `i18n` VALUES ('Navigation', 'New', 'New', 'Neu');
INSERT INTO `i18n` VALUES ('Navigation', 'NewAccount', 'New Account', 'Neues Konto');
INSERT INTO `i18n` VALUES ('Navigation', 'NewCategory', 'New Category', 'Neue Transaktionskategorie');
INSERT INTO `i18n` VALUES ('forecast', 'upperLimitLabel', 'Graph upper limit', 'Graph oberes Limit');
INSERT INTO `i18n` VALUES ('forecast', 'upperLimitToolTip', 'Shows the upper limit in the graph.', 'Zeigt im Diagramm das obere Limit des Zielkontos.');
INSERT INTO `i18n` VALUES ('accountCurrency', 'pageTitle', 'Edit Currency', 'Währung bearbeiten');
INSERT INTO `i18n` VALUES ('accountCategory', 'pageTitle', 'Edit Catagory', 'Kategorie bearbeiten');
INSERT INTO `i18n` VALUES ('forecast', 'plannedTransactionsLabel', 'Graph planned transactions', 'Graph geplante Transaktionen');
INSERT INTO `i18n` VALUES ('forecast', 'plannedTransactionsToolTip', 'Shows the graph for planned transactions. The saving target and pocket money will not be included.', 'Zeigt den Graph für die geplanten Transaktionen. Es wird kein Sparziel und kein Taschengeld berücksichtigt.');
INSERT INTO `i18n` VALUES ('forecast', 'plannedTransactionsLabel', 'Graph planned transactions', 'Graph geplante Transaktionen');
INSERT INTO `i18n` VALUES ('forecast', 'plannedTransactionsToolTip', 'Shows the graph for planned transactions. The saving target and pocket money will not be included.', 'Zeigt den Graph für die geplanten Transaktionen. Es wird kein Sparziel und kein Taschengeld berücksichtigt.');
INSERT INTO `i18n` VALUES ('accountOverview', 'pageTitle', 'Transaction overview', 'Transaktionsübersicht');
INSERT INTO `i18n` VALUES ('forecast', 'savingTargetLabel', 'Graph saving target', 'Graph mit Sparziel');
INSERT INTO `i18n` VALUES ('forecast', 'showSavingTargetToolTip', 'Shows the trend including the saving target.', 'Zeigt den Verlauf des Kontostandes unter Berücksichtigung des Sparzieles an.');
INSERT INTO `i18n` VALUES ('accountAccount', 'pageTitleProp', 'Edit Account', 'Konto bearbeiten');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney1Label', 'Graph pocket money 1', 'Graph Taschengeld 1');
INSERT INTO `i18n` VALUES ('forecast', 'showPocketMoney1ToolTip', 'Shows the trend of the account balance including the pocket money 1', 'Zeigt den Verlauf des Kontostandes unter Berücksichtigung des Taschengeldes 1.');
INSERT INTO `i18n` VALUES ('forecast', 'pocketMoney2Label', 'Graph pocket money 2', 'Graph Taschengeld 2');
INSERT INTO `i18n` VALUES ('forecast', 'showPocketMoney2ToolTip', 'Shows the trend of the account balance including the pocket money 2', 'Zeigt den Verlauf des Kontostandes unter Berücksichtigung des Taschengeldes 2');
INSERT INTO `i18n` VALUES ('forecast', 'noGraphchosen', 'No graph was chosen to display.', 'Kein Graph zum Anzeigen gewählt.');
INSERT INTO `i18n` VALUES ('forecast', 'noLowerLimit', 'The selected account has no lower limit.', 'Das gewählte Konto hat kein unteres Limit.');
INSERT INTO `i18n` VALUES ('forecast', 'noUpperLimit', 'The selected account has no upper limit.', 'Das gewählte Konto hat kein oberes Limit.');
INSERT INTO `i18n` VALUES ('statistics', 'accColTitle', 'Title', 'Titel');
INSERT INTO `i18n` VALUES ('statistics', 'accColBalance', 'Balance', 'Kontostand');
INSERT INTO `i18n` VALUES ('statistics', 'accColCurrency', 'Currency', 'Währung');
INSERT INTO `i18n` VALUES ('forecast', 'onlyFutureDates', 'The enddate have to be in the future. For data from the past please use the statistics.', 'Das Enddatum muss in der Zukunft liegen. Für Vergangenheitsdaten benutzen Sie bitte die Statistiken.');
INSERT INTO `i18n` VALUES ('statistics', 'pageTitle', 'Statistics', 'Statistik erstellen');
INSERT INTO `i18n` VALUES ('accountTransaction', 'title', 'Title', 'Titel');
INSERT INTO `i18n` VALUES ('statistics', 'type', 'Type', 'Typ');
INSERT INTO `i18n` VALUES ('statistics', 'category', 'Category', 'Kategorie-Art');
INSERT INTO `i18n` VALUES ('statistics', 'period', 'Period', 'Zeitraum');
INSERT INTO `i18n` VALUES ('statistics', 'catMerge', 'Category merge', 'Kategorien zusammenfassen');
INSERT INTO `i18n` VALUES ('statistics', 'accounts', 'Accounts', 'Konten');
INSERT INTO `i18n` VALUES ('statistics', 'attention', 'Attention: No currency conversion takes place during display of accounts with different currencies.', 'Achtung: Bei der gleichzeitigen Betrachtung mehrerer Konten mit unterschiedlichen Währungen findet keine Umrechnung statt!');
INSERT INTO `i18n` VALUES ('statistics', 'from', 'From', 'Vom');
INSERT INTO `i18n` VALUES ('statistics', 'to', 'to', 'bis');
INSERT INTO `i18n` VALUES ('accountTransaction', 'beginDate', 'Begin date', 'Startdatum');
INSERT INTO `i18n` VALUES ('accountTransaction', 'endDate', 'End date', 'Enddatum');
INSERT INTO `i18n` VALUES ('statistics', 'jan', 'January', 'Januar');
INSERT INTO `i18n` VALUES ('statistics', 'feb', 'february', 'Februar');
INSERT INTO `i18n` VALUES ('accountTransaction', 'repeatUnit', 'Repeat unit', 'Einheit');
INSERT INTO `i18n` VALUES ('accountTransaction', 'repeatFrequency', 'Repeat frequency', 'Intervall');
INSERT INTO `i18n` VALUES ('forecast', 'dailyPocketMoneyLabel', 'Pocket money for reaching saving Target', 'Taschengeld um Sparziel zu erreichen');
INSERT INTO `i18n` VALUES ('forecast', 'dailyPocketMoneyToolTip', 'Money, that can be spent every day, if the saving target should be reached. If negative, this amount has to be to be earned every day.', 'Geld, das maximal täglich zur Verfügung steht, wenn das Sparziel erreicht werden soll. Wenn negativ, muss im Durchschnitt jeden Tag soviel Geld eingenommen werden.');
INSERT INTO `i18n` VALUES ('statistics', 'mar', 'March', 'März');
INSERT INTO `i18n` VALUES ('statistics', 'apr', 'April', 'April');
INSERT INTO `i18n` VALUES ('statistics', 'may', 'May', 'Mai');
INSERT INTO `i18n` VALUES ('statistics', 'jun', 'June', 'Juni');
INSERT INTO `i18n` VALUES ('statistics', 'jul', 'July', 'Juli');
INSERT INTO `i18n` VALUES ('statistics', 'aug', 'August', 'August');
INSERT INTO `i18n` VALUES ('statistics', 'sep', 'September', 'September');
INSERT INTO `i18n` VALUES ('statistics', 'oct', 'October', 'Oktober');
INSERT INTO `i18n` VALUES ('statistics', 'nov', 'November', 'November');
INSERT INTO `i18n` VALUES ('statistics', 'dec', 'December', 'Dezember');
INSERT INTO `i18n` VALUES ('forecast', 'printedPocketMoney1Label', 'Balance at the end date (pocket money 1', 'Kontostand am Enddatum (Taschengeld 1');
INSERT INTO `i18n` VALUES ('forecast', 'printedPocketMoney2Label', 'Balance at the end date (pocket money 2', 'Kontostand am Enddatum (Taschengeld 2');
INSERT INTO `i18n` VALUES ('statistics', 'income', 'Income', 'Einnahmen');
INSERT INTO `i18n` VALUES ('statistics', 'expenses', 'Expenses', 'Ausgaben');
INSERT INTO `i18n` VALUES ('statistics', 'subCat', 'Merge sub-categories with main-categories', 'Unterkategorien unter der Hauptkategorie zusammenfassen');
INSERT INTO `i18n` VALUES ('statistics', 'subCat2', 'Show sub-catagory individually', 'Unterkategorien eigenständig aufführen');
INSERT INTO `i18n` VALUES ('statistics', 'errorMissingAcc', 'You did not choose an account.', 'Sie haben noch kein Konto ausgewählt.');
INSERT INTO `i18n` VALUES ('statistics', 'errorDate', 'Start date before end date.', 'Das Startdatum liegt nicht vor dem Enddatum.');
INSERT INTO `i18n` VALUES ('statistics', 'errorEndDate', 'End date in the future.', 'Das Enddatum liegt in der Zukunft.');
INSERT INTO `i18n` VALUES ('forecast', 'legendSetting', 'Parameter', 'Parameter');
INSERT INTO `i18n` VALUES ('forecast', 'legendGraphs', 'Select graphs', 'Graphen auswählen');
INSERT INTO `i18n` VALUES ('accountTransaction', 'pageTitle', 'Transaction', 'Transaktion');
INSERT INTO `i18n` VALUES ('csv', 'title', 'CSV-Import', 'CSV-Import');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'title', 'User Settings', 'Einstellungen');
INSERT INTO `i18n` VALUES ('forecast', 'title', 'Forecast', 'Prognose');
INSERT INTO `i18n` VALUES ('about', 'contributors', 'Contributors', 'Mitwirkende');
INSERT INTO `i18n` VALUES ('csv', 'legend', 'Properties', 'Eigenschaften');
INSERT INTO `i18n` VALUES ('askInsert', 'legend', 'Import', 'Import');
INSERT INTO `i18n` VALUES ('askExport', 'legend', 'Export', 'Export');
INSERT INTO `i18n` VALUES ('accountTransaction', 'newPlannedTrans', 'New recurring transaction', 'Neue wiederkehrende Transaktion');
INSERT INTO `i18n` VALUES ('accountTransaction', 'newFinishedTrans', 'New single transaction', 'Neue einmalige Transaktion');
INSERT INTO `i18n` VALUES ('CategoryManager', 'no_parent', 'No parent category', 'Keine Elternkategorie');
INSERT INTO `i18n` VALUES ('accountAccount', 'legend', 'Properties', 'Eigenschaften');
INSERT INTO `i18n` VALUES ('accountCategory', 'legend', 'Properties', 'Eigenschaften');
INSERT INTO `i18n` VALUES ('accountCurrency', 'legend', 'Properties', 'Eigenschaften');
INSERT INTO `i18n` VALUES ('Navigation', 'NewTransactionFinished', 'New Transaction (single)', 'Neue Transaktion (einmalig)');
INSERT INTO `i18n` VALUES ('Navigation', 'NewTransactionPlanned', 'New Transaction (recurring)', 'Neue Transaktion (wiederkehrend)');
INSERT INTO `i18n` VALUES ('accountTransaction', 'headingTransactionFinished', 'Single transaction', 'Einmalige Transaktion');
INSERT INTO `i18n` VALUES ('accountTransaction', 'headingTransactionPlanned', 'Recurring transaction', 'Wiederkehrende Transaktion');
INSERT INTO `i18n` VALUES ('accountTransaction', 'Account', 'Account', 'Konto');
INSERT INTO `i18n` VALUES ('forecast', 'calculatedPocketMoneyLabel', 'Automatically calculate pocket money 2', 'Taschengeld 2 automatisch berechnen');
INSERT INTO `i18n` VALUES ('forecast', 'calculatedPocketMoneyToolTip', 'If you press this button, a pocket money will be generated automatically and written to the pocket money 2 field. For the calculation every transaction between the selected date & today will be used, which are not marked as exceptional or periodical.', 'Wenn Sie den Button drücken, wird automatisch aus der Datenbank ein Taschengeld generiert und in das Feld Taschengeld 2 geschrieben. Beim berechnen werden alle Transaktionen berücksichtigt, die zwischen dem hier angewähltem Datum und heute liegen, und nicht als regelmäßig oder außergewöhnlich markiert sind.');
INSERT INTO `i18n` VALUES ('accountOverview', 'colSum', 'Sum', 'Summe');
INSERT INTO `i18n` VALUES ('forecast', 'calculatedPocketMoneyButton', 'Calculate', 'Berechnen');
INSERT INTO `i18n` VALUES ('statistics', 'noCategoryAssigned', '(not assigned)', '(nicht zugeordnet)');
INSERT INTO `i18n` VALUES ('badger', 'PrintMessage', 'Print', 'Drucken');
INSERT INTO `i18n` VALUES ('forecast', 'performanceWarning', 'Please pay attention to this fact before pressing the button: If the time span between today and the end date is too long, a message from the macromedia flash player appears. In this case please reduce the time span for the forecast. During the test on different computers a forecast between 1 up to 4 years were possible.', 'Bitte beachten Sie: Je weiter das Enddatum in der Zukunft liegt, desto länger dauert das Erstellen des Diagrammes. Wenn es zu weit in der Zukunft liegt, kann es zu einer Meldung des Macromedia Flash Players kommen. Verkürzen Sie in diesem Fall die Prognosedauer. Je nach Testrechner waren Prognosen zwischen 1 und 4 Jahren möglich.');
INSERT INTO `i18n` VALUES ('Navigation', 'SQLError', 'An SQL error occured attempting to fetch the navigation data from the database.', 'Beim Abrufen der Navigations-Daten aus der Datenbank trat ein SQL-Fehler auf.');
INSERT INTO `i18n` VALUES ('Navigation', 'UnknownNavigationId', 'An unknown id of an navigation entry was used.', 'Es wurde eine unbekannte ID eines Navigationseintrags benutzt.');
INSERT INTO `i18n` VALUES ('statistics', 'trend', 'Trend', 'Trend');
INSERT INTO `i18n` VALUES ('statistics', 'categories', 'Categories', 'Kategorien');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'noAccountID', 'noAccountID', 'es wurde keine AccountID übermittelt');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'pageTitle', 'Recurring transaction overview', 'Übersicht wiederkehrender Transaktionen');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'colBeginDate', 'Begin Date', 'Startdatum');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'colEndDate', 'End date', 'Enddatum');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'colUnit', 'Unit', 'Einheit');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'colFrequency', 'Interval', 'Intervall');
INSERT INTO `i18n` VALUES ('dataGrid', 'edit', 'Edit', 'Bearbeiten');
INSERT INTO `i18n` VALUES ('dataGrid', 'NoRowSelectedMsg', 'Please, select a row to edit', 'Bitte selektieren eine Zeile, die sie bearbeiten wollen.');
INSERT INTO `i18n` VALUES ('jsVal', 'err_form', 'Please enter/select values for the following fields:\\n\\n', 'Bitte geben Sie die Werte für folgende Felder ein:\\n\\n');
INSERT INTO `i18n` VALUES ('jsVal', 'err_select', 'Please select a valid "%FIELDNAME%"', 'Bitte wählen Sie einen gültigen Wert für "%FIELDNAME%"');
INSERT INTO `i18n` VALUES ('jsVal', 'err_enter', 'Please enter a valid "%FIELDNAME%"', 'Bitte geben Sie einen gültigen Wert für "%FIELDNAME%" ein');
INSERT INTO `i18n` VALUES ('accountCurrency', 'currencyIsStillUsed', 'The Currency is still used. You cannot delete it.', 'Die Währung wird noch verwendet und kann daher nicht gelöscht werden.');
INSERT INTO `i18n` VALUES ('accountCategory', 'deleteMsg', 'Do you really want to delete the selected categories? Note: All transactions using the selected categories will lose their categorization information and become uncategorized transactions.', 'Wollen sie die selektierten Kategorien wirklich löschen?\\nHinweis: Von allen Transaktionen, die diese Kategorie(n) verwenden, wird die Kategorie zurückgesetzt.');
INSERT INTO `i18n` VALUES ('accountAccount', 'deleteMsg', 'Do you really want to delete the selected accounts with all transactions?', 'Wollen sie die selektierten Konten wirklich mit allen Transaktionen löschen?');
INSERT INTO `i18n` VALUES ('badger_login', 'backend_not_login', 'Error: You do not have permission to access this page.', 'Fehler: Sie haben keine Berechtigung, auf diese Seite zuzugreifen.');
INSERT INTO `i18n` VALUES ('CategoryManager', 'outsideCapital', 'Outside Capital', 'Fremdkapital');
INSERT INTO `i18n` VALUES ('CategoryManager', 'ownCapital', 'Own Capital', 'Eigenkapital');
INSERT INTO `i18n` VALUES ('dataGrid', 'legend', 'Legend', 'Legende');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'autoExpandPlannedTransactionsName', 'Auto-insert recurring transactions', 'Wiederkehrende Transaktionen automatisch eintragen');
INSERT INTO `i18n` VALUES ('UserSettingsAdmin', 'autoExpandPlannedTransactionsDescription', 'If this option is checked, every occuring instance of a recurring transaction is automatically inserted as an single transaction. Uncheck this if you import your transactions from a CSV file on a regular basis.', 'Wenn diese Option ausgewählt wurde, werden eintretende Instanzen einer wiederkehrenden Transaktion automatisch als einmalige Transaktionen eingetragen. Wählen Sie die Option nicht aus, wenn Sie Ihre Transaktionen regelmäßig aus einer CSV-Datei importieren.');
INSERT INTO `i18n` VALUES ('accountOverview', 'showPlannedTrans', 'Show recurring transactions', 'Wiederkehrende Transaktionen anzeigen');
INSERT INTO `i18n` VALUES ('accountOverviewPlanned', 'showTrans', 'Show all transactions', 'Alle Transaktionen anzeigen');

-- --------------------------------------------------------

-- 
-- Table structure for table `langs`
-- 

DROP TABLE IF EXISTS `langs`;
CREATE TABLE IF NOT EXISTS `langs` (
  `id` varchar(16) default NULL,
  `name` varchar(200) default NULL,
  `meta` text,
  `error_text` varchar(250) default NULL,
  `encoding` varchar(16) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `langs`
-- 

INSERT INTO `langs` VALUES ('de', 'deutsch', 'Hochdeutsch', 'not avaiable', 'iso-8859-1');
INSERT INTO `langs` VALUES ('en', 'english', 'normal english', 'not avaiable', 'iso-8859-1');

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=55 ;

-- 
-- Dumping data for table `navi`
-- 

INSERT INTO `navi` VALUES (23, 22, 2, 'm', 'Backup', NULL, 'server_go.gif', '{BADGER_ROOT}/modules/importExport/importExport.php');
INSERT INTO `navi` VALUES (22, 0, 6, 'm', 'System', NULL, 'system.gif', NULL);
INSERT INTO `navi` VALUES (21, 31, 3, 'i', 'AccountManager', '', 'manageaccount.gif', '{BADGER_ROOT}/modules/account/AccountManagerOverview.php');
INSERT INTO `navi` VALUES (16, 0, 9, 'i', 'Logout', NULL, 'cancel.gif', '?logout=true');
INSERT INTO `navi` VALUES (17, 22, 1, 'i', 'Preferences', NULL, 'cog.gif', '{BADGER_ROOT}/core/UserSettingsAdmin/UserSettingsAdmin.php');
INSERT INTO `navi` VALUES (1, 22, 3, 'i', 'CurrencyManager', '', 'coins.gif', '{BADGER_ROOT}/modules/account/CurrencyManagerOverview.php');
INSERT INTO `navi` VALUES (24, 31, 5, 'i', 'CSV-Import', NULL, 'csvimport.gif', '{BADGER_ROOT}/modules/csvImport/csvImport.php');
INSERT INTO `navi` VALUES (25, 30, 5, 'i', 'Forecast', NULL, 'forecast.gif', '{BADGER_ROOT}/modules/forecast/forecast.php');
INSERT INTO `navi` VALUES (26, 30, 4, 'i', 'Statistics', NULL, 'statistics.gif', '{BADGER_ROOT}/modules/statistics/statistics.php');
INSERT INTO `navi` VALUES (27, 31, 4, 'i', 'CategoryManager', NULL, 'categories.gif', '{BADGER_ROOT}/modules/account/CategoryManagerOverview.php');
INSERT INTO `navi` VALUES (28, 0, 8, 'm', 'Help', NULL, 'help.gif', NULL);
INSERT INTO `navi` VALUES (29, 28, 9, 'i', 'About', NULL, 'information.gif', '{BADGER_ROOT}/core/about.php');
INSERT INTO `navi` VALUES (30, 0, 4, 'm', 'Analysis', NULL, 'analysis.gif', NULL);
INSERT INTO `navi` VALUES (31, 0, 1, 'm', 'Accounts', NULL, 'accounts.gif', NULL);
INSERT INTO `navi` VALUES (32, 31, 7, 'i', 'Account1', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=1');
INSERT INTO `navi` VALUES (33, 31, 8, 'i', 'Account2', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=2');
INSERT INTO `navi` VALUES (34, 31, 8, 'i', 'Account3', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=4');
INSERT INTO `navi` VALUES (35, 31, 10, 'i', 'Account4', NULL, 'account.gif', '{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=3');
INSERT INTO `navi` VALUES (36, 28, 2, 'i', 'Documentation', NULL, 'docu.gif', 'javascript:showBadgerHelp();');
INSERT INTO `navi` VALUES (40, 23, 1, 'i', 'BackupCreate', NULL, 'savebackup.gif', '{BADGER_ROOT}/modules/importExport/importExport.php?mode=export');
INSERT INTO `navi` VALUES (41, 23, 2, 'i', 'BackupUpload', NULL, 'addbackup.gif', '{BADGER_ROOT}/modules/importExport/importExport.php?mode=import');
INSERT INTO `navi` VALUES (42, 31, 5, 's', NULL, NULL, NULL, NULL);
INSERT INTO `navi` VALUES (43, 31, 2, 's', NULL, NULL, NULL, NULL);
INSERT INTO `navi` VALUES (44, 31, 1, 'm', 'New', NULL, 'add.gif', NULL);
INSERT INTO `navi` VALUES (47, 44, 4, 'i', 'NewAccount', NULL, 'newaccount.gif', '{BADGER_ROOT}/modules/account/AccountManager.php?action=new');
INSERT INTO `navi` VALUES (48, 44, 5, 'i', 'NewCategory', NULL, 'new_transactioncategory.gif', '{BADGER_ROOT}/modules/account/CategoryManager.php?action=new');
INSERT INTO `navi` VALUES (49, 28, 8, 's', NULL, NULL, NULL, NULL);
INSERT INTO `navi` VALUES (53, 0, 7, 'i', 'Print', NULL, 'printer.gif', 'javascript:window.print();');
INSERT INTO `navi` VALUES (50, 44, 3, 's', NULL, NULL, NULL, NULL);
INSERT INTO `navi` VALUES (51, 44, 2, 'i', 'NewTransactionPlanned', NULL, 'planned_transaction_new.gif', '{BADGER_ROOT}/modules/account/Transaction.php?action=new&type=planned');
INSERT INTO `navi` VALUES (52, 44, 1, 'i', 'NewTransactionFinished', NULL, 'finished_transaction_new.gif', '{BADGER_ROOT}/modules/account/Transaction.php?action=new&type=finished');

-- --------------------------------------------------------

-- 
-- Table structure for table `naviIds_seq`
-- 

DROP TABLE IF EXISTS `naviIds_seq`;
CREATE TABLE IF NOT EXISTS `naviIds_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=56 ;

-- 
-- Dumping data for table `naviIds_seq`
-- 

INSERT INTO `naviIds_seq` VALUES (55);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `planned_transaction`
-- 

INSERT INTO `planned_transaction` VALUES (6, 1, 1, 'Miete', 'Miete für Musterstr. 16', -420.00, 0, NULL, '2006-03-01', NULL, 'month', 1);
INSERT INTO `planned_transaction` VALUES (5, 4, 1, 'Gehalt', 'Mein Gehalt', 1357.00, 0, 'Arbeitgeber AG', '2006-02-28', NULL, 'month', 1);

-- --------------------------------------------------------

-- 
-- Table structure for table `plannedTransactionIds_seq`
-- 

DROP TABLE IF EXISTS `plannedTransactionIds_seq`;
CREATE TABLE IF NOT EXISTS `plannedTransactionIds_seq` (
  `id` int(10) unsigned NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- 
-- Dumping data for table `plannedTransactionIds_seq`
-- 

INSERT INTO `plannedTransactionIds_seq` VALUES (7);

-- --------------------------------------------------------

-- 
-- Table structure for table `session_global`
-- 

DROP TABLE IF EXISTS `session_global`;
CREATE TABLE IF NOT EXISTS `session_global` (
  `sid` varchar(100) NOT NULL,
  `variable` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `session_global`
-- 

INSERT INTO `session_global` VALUES ('2687fc51fd26388585b88842d7654914', 'number_of_login_attempts', '2');
INSERT INTO `session_global` VALUES ('bef37c6c69d9c31bfb5ef68939b56218', 'number_of_login_attempts', '1');
INSERT INTO `session_global` VALUES ('bef37c6c69d9c31bfb5ef68939b56218', 'password', '7e59cb5b2f52c763bc846471fe5942e4');

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
  `logout` tinyint(4) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `session_master`
-- 

INSERT INTO `session_master` VALUES ('2687fc51fd26388585b88842d7654914', 0, '2006-07-11 12:13:56', '2006-07-11 12:14:04', '127.0.0.1', 0);
INSERT INTO `session_master` VALUES ('bef37c6c69d9c31bfb5ef68939b56218', 0, '2006-07-11 12:14:13', '2006-07-11 12:14:46', '127.0.0.1', 0);

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

INSERT INTO `user_settings` VALUES ('badgerTemplate', 's:8:"Standard";');
INSERT INTO `user_settings` VALUES ('badgerSiteName', 's:14:"BADGER Finance";');
INSERT INTO `user_settings` VALUES ('badgerLanguage', 's:2:"de";');
INSERT INTO `user_settings` VALUES ('badgerDateFormat', 's:10:"dd.mm.yyyy";');
INSERT INTO `user_settings` VALUES ('badgerPassword', 's:32:"7e59cb5b2f52c763bc846471fe5942e4";');
INSERT INTO `user_settings` VALUES ('badgerMaxLoginAttempts', 's:1:"5";');
INSERT INTO `user_settings` VALUES ('badgerLockOutTime', 's:2:"30";');
INSERT INTO `user_settings` VALUES ('badgerDecimalSeparator', 's:1:",";');
INSERT INTO `user_settings` VALUES ('badgerThousandSeparator', 's:1:".";');
INSERT INTO `user_settings` VALUES ('badgerSessionTime', 's:4:"9999";');
INSERT INTO `user_settings` VALUES ('badgerStartPage', 's:19:"modules/welcome.php";');
INSERT INTO `user_settings` VALUES ('accountNaviNextPosition', 's:2:"11";');
INSERT INTO `user_settings` VALUES ('accountNaviParent', 's:2:"31";');
INSERT INTO `user_settings` VALUES ('accountNaviId_1', 's:2:"32";');
INSERT INTO `user_settings` VALUES ('accountNaviId_2', 's:2:"33";');
INSERT INTO `user_settings` VALUES ('accountNaviId_3', 's:2:"34";');
INSERT INTO `user_settings` VALUES ('accountNaviId_4', 's:2:"35";');
INSERT INTO `user_settings` VALUES ('forecastStandardAccount', 's:0:"";');
INSERT INTO `user_settings` VALUES ('csvImportStandardParser', 's:0:"";');
INSERT INTO `user_settings` VALUES ('csvImportStandardAccount', 's:0:"";');
INSERT INTO `user_settings` VALUES ('autoExpandPlannedTransactions', 'b:1;');
