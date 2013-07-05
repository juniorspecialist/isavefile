-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Июл 05 2013 г., 17:48
-- Версия сервера: 5.5.31
-- Версия PHP: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `isavefile_new`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_config`
--

CREATE TABLE IF NOT EXISTS `tbl_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `param` varchar(128) NOT NULL,
  `value` text NOT NULL,
  `default` text NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `param` (`param`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Дамп данных таблицы `tbl_config`
--

INSERT INTO `tbl_config` (`id`, `param`, `value`, `default`, `label`, `type`) VALUES
(1, 'FILE_TYPE_1', 'bmp jpeg jpg gif tif png', 'bmp jpeg jpg gif tif png', 'Доступные расширения для категории - иозображения', 'string'),
(7, 'FILE_TYPE_2', 'doc txt rtf xls doc docx', 'doc txt rtf xls doc docx', 'Доступные расширения для категории - текст', 'string'),
(8, 'FILE_TYPE_3', 'zip rar jar cab', 'zip rar jar cab', 'Доступные расширения для категории - архивы', 'string');

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_file`
--

CREATE TABLE IF NOT EXISTS `tbl_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `shot_url` varchar(255) NOT NULL,
  `file_size` varchar(100) NOT NULL,
  `create_at` int(11) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `title_file` varchar(255) NOT NULL,
  `type_file` tinyint(1) NOT NULL,
  `hash_file` varchar(40) NOT NULL,
  `real_name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_tbl_file_tbl_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Загруженные файлы на хостинг' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `tbl_user`
--

CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `block` tinyint(1) NOT NULL,
  `confirm` tinyint(1) NOT NULL,
  `password` varchar(128) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT '1',
  `hash` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='пользователи системы' AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `tbl_user`
--

INSERT INTO `tbl_user` (`id`, `email`, `block`, `confirm`, `password`, `role`, `hash`) VALUES
(1, 'alexsashkan@mail.ru', 0, 1, '$2a$12$nwcVU7Sb/RaxvC96K/yx4eC044UTdEcNVYI5YEFWbRFq.s1RLRChC', 2, '5ece76741b48cbe32fa17356dd9402c4'),
(2, 'admin@mail.ru', 0, 1, '$2a$12$IZRSHUpTYIS4IYqrrFM7buyNr62NF77vWSSLNGkt33F4YHvPx8QSu', 2, 'ab8a35e7540da727c7ae4cd2312d2719');

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `tbl_file`
--
ALTER TABLE `tbl_file`
  ADD CONSTRAINT `FK_tbl_file_tbl_user` FOREIGN KEY (`user_id`) REFERENCES `tbl_user` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
