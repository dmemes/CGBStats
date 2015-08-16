-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

-- --------------------------------------------------------

--
-- Table structure for table `cgbstats_stats`
--

CREATE TABLE IF NOT EXISTS `cgbstats_stats` (
  `userid` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `ctrophy` int(11) NOT NULL,
  `cgold` int(11) NOT NULL,
  `celix` int(11) NOT NULL,
  `cdelix` int(11) NOT NULL,
  `thlevel` int(11) NOT NULL,
  `search` int(11) NOT NULL,
  `gold` int(11) NOT NULL,
  `elix` int(11) NOT NULL,
  `delix` int(11) NOT NULL,
  `trophy` int(11) NOT NULL,
  `bgold` int(11) NOT NULL,
  `belix` int(11) NOT NULL,
  `bdelix` int(11) NOT NULL,
  `stars` int(11) NOT NULL,
  `log` text NOT NULL,
  PRIMARY KEY (`userid`,`date`),
  FULLTEXT KEY `log` (`log`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `cgbstats_user`
--

CREATE TABLE IF NOT EXISTS `cgbstats_user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `apikey` varchar(32) NOT NULL,
  `username` varchar(32) NOT NULL DEFAULT 'Anonymous',
  `lastlogin` datetime NOT NULL DEFAULT '1970-01-01 12:12:12',
  `ip` text NOT NULL,
  `ua` text NOT NULL,
  `cfray` text NOT NULL,
  `cfcountry` text NOT NULL,
  UNIQUE KEY `u_userid` (`userid`),
  UNIQUE KEY `u_apikey` (`apikey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
