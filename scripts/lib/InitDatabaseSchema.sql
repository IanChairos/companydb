-- phpMyAdmin SQL Dump
-- version 4.0.0-beta3
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Úte 02. dub 2013, 18:26
-- Verze serveru: 5.1.67-0ubuntu0.11.10.1
-- Verze PHP: 5.3.6-13ubuntu3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databáze: `companies`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ic` varchar(32) NOT NULL,
  `name` varchar(32) NOT NULL,
  `email` varchar(64) NOT NULL,
  `address` varchar(128) NOT NULL,
  `employee_count` int(11) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ic` (`ic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Struktura tabulky `contact_person`
--

CREATE TABLE IF NOT EXISTS `contact_person` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(32) NOT NULL,
  `lastname` char(32) NOT NULL,
  `company_id` int(11) NOT NULL,
  `email` varchar(64) NOT NULL,
  `phone` varchar(16) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `job_name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `lastname` (`lastname`),
  KEY `company_id` (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=83 ;

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `contact_person`
--
ALTER TABLE `contact_person`
  ADD CONSTRAINT `contact_vs_company` FOREIGN KEY (`company_id`) REFERENCES `company` (`id`) ON DELETE CASCADE;
