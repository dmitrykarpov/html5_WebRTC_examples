-- phpMyAdmin SQL Dump
-- version 3.3.2
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Фев 07 2013 г., 23:57
-- Версия сервера: 5.5.29
-- Версия PHP: 5.3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `html5test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `webrtc_messages`
--

CREATE TABLE IF NOT EXISTS `webrtc_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `to` varchar(2) NOT NULL,
  `read_flag` int(1) NOT NULL DEFAULT '0',
  `message` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `read_flag` (`read_flag`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=626 ;

--
-- Дамп данных таблицы `webrtc_messages`
--

