-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Хост: 192.168.1.64
-- Время создания: Дек 28 2016 г., 18:22
-- Версия сервера: 5.5.50-MariaDB
-- Версия PHP: 5.6.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `sale`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cards`
--

CREATE TABLE `cards` (
  `card_id` int(11) NOT NULL,
  `card_number` char(30) NOT NULL,
  `card_owner_name` text NOT NULL,
  `card_owner_address` text NOT NULL,
  `card_type` int(1) NOT NULL,
  `card_owner_birth_date` date NOT NULL,
  `card_owner_birth_year` int(2) NOT NULL,
  `card_owner_social_status` int(1) NOT NULL,
  `card_owner_phone` text NOT NULL,
  `card_blocked` int(11) NOT NULL,
  `card_discount` int(11) NOT NULL,
  `card_change_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

CREATE TABLE `files` (
  `file_id` int(11) NOT NULL,
  `file_name` text NOT NULL,
  `file_size` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `log`
--

CREATE TABLE `log` (
  `log_id` int(11) NOT NULL,
  `log_ip` varchar(45) NOT NULL,
  `log_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `log_action` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `sales`
--

CREATE TABLE `sales` (
  `sale_id` int(11) NOT NULL,
  `sale_name` varchar(30) NOT NULL,
  `sale_price` decimal(10,2) NOT NULL,
  `sale_quantity` decimal(10,3) NOT NULL,
  `sale_money_count` decimal(10,2) NOT NULL,
  `sale_discount` decimal(2,0) DEFAULT NULL,
  `sale_operation_type` varchar(3) NOT NULL,
  `sale_date_unix` int(11) NOT NULL,
  `sale_cashbox` int(3) NOT NULL,
  `sale_checknum` int(7) NOT NULL,
  `sale_cashless` tinyint(1) NOT NULL,
  `sale_code` int(11) NOT NULL,
  `sale_client_card_id` varchar(13) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `shops`
--

CREATE TABLE `shops` (
  `shop_id` int(11) NOT NULL,
  `shop_name` varchar(3) NOT NULL,
  `shop_real_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `units`
--

CREATE TABLE `units` (
  `unit_id` int(11) NOT NULL,
  `unit_code` int(11) NOT NULL,
  `unit_name` text NOT NULL,
  `unit_type` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

-- --------------------------------------------------------

--
-- Структура таблицы `units_types`
--

CREATE TABLE `units_types` (
  `utype_id` int(11) NOT NULL,
  `utype_code` int(11) NOT NULL,
  `utype_name` text NOT NULL,
  `utype_parent` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=cp1251;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cards`
--
ALTER TABLE `cards`
  ADD UNIQUE KEY `cards` (`card_id`),
  ADD KEY `card_owner_birth_date` (`card_owner_birth_date`),
  ADD KEY `card_owner_birth_year` (`card_owner_birth_year`);

--
-- Индексы таблицы `files`
--
ALTER TABLE `files`
  ADD UNIQUE KEY `files` (`file_id`);

--
-- Индексы таблицы `log`
--
ALTER TABLE `log`
  ADD UNIQUE KEY `log_id` (`log_id`);

--
-- Индексы таблицы `sales`
--
ALTER TABLE `sales`
  ADD UNIQUE KEY `sales` (`sale_id`),
  ADD KEY `sale_date` (`sale_date`),
  ADD KEY `sale_client_card_id` (`sale_client_card_id`),
  ADD KEY `sale_code` (`sale_code`),
  ADD KEY `sale_cashbox` (`sale_cashbox`),
  ADD KEY `sale_discount` (`sale_discount`),
  ADD KEY `sale_money_count` (`sale_money_count`),
  ADD KEY `t1` (`sale_date`,`sale_cashbox`) USING BTREE,
  ADD KEY `t2` (`sale_discount`,`sale_date`),
  ADD KEY `t3` (`sale_date`,`sale_code`) USING BTREE;

--
-- Индексы таблицы `shops`
--
ALTER TABLE `shops`
  ADD UNIQUE KEY `shops` (`shop_id`);

--
-- Индексы таблицы `units`
--
ALTER TABLE `units`
  ADD UNIQUE KEY `units` (`unit_id`),
  ADD KEY `unit_code` (`unit_code`),
  ADD KEY `unit_type` (`unit_type`);

--
-- Индексы таблицы `units_types`
--
ALTER TABLE `units_types`
  ADD UNIQUE KEY `utype_id` (`utype_id`),
  ADD KEY `utype_code` (`utype_code`),
  ADD KEY `utype_parent` (`utype_parent`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cards`
--
ALTER TABLE `cards`
  MODIFY `card_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20999;
--
-- AUTO_INCREMENT для таблицы `files`
--
ALTER TABLE `files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1734;
--
-- AUTO_INCREMENT для таблицы `log`
--
ALTER TABLE `log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
--
-- AUTO_INCREMENT для таблицы `sales`
--
ALTER TABLE `sales`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2689372;
--
-- AUTO_INCREMENT для таблицы `shops`
--
ALTER TABLE `shops`
  MODIFY `shop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2058;
--
-- AUTO_INCREMENT для таблицы `units`
--
ALTER TABLE `units`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31632;
--
-- AUTO_INCREMENT для таблицы `units_types`
--
ALTER TABLE `units_types`
  MODIFY `utype_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1124;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
