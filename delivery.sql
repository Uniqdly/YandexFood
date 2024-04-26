-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 26 2024 г., 10:54
-- Версия сервера: 8.0.30
-- Версия PHP: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `delivery`
--

-- --------------------------------------------------------

--
-- Структура таблицы `Dishes`
--

CREATE TABLE `Dishes` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `ingredients_name` varchar(255) DEFAULT NULL,
  `description` text,
  `price` int DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Dishes`
--

INSERT INTO `Dishes` (`id`, `name`, `ingredients_name`, `description`, `price`, `photo`) VALUES
(1, 'Бургер', NULL, 'вкусный воппер который тает во рту', 230, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZ6fOJvmdCxCxNw_sA2eLojqyimTigPx7by_ALKrq0cA&s'),
(2, 'Картошка Фри', NULL, 'горячая сочная картошечка которую так и хочется отжарить', 79, 'https://i.pinimg.com/736x/53/e6/b4/53e6b462c1c6c5d6f97cd7380d63c008.jpg'),
(3, 'Напиток', NULL, 'напиток - это то что может охладить твой пылкий зад', 110, 'https://www.otlichnye-tseny.ru/upload/iblock/135/wk3jts424ofhquwtwguqvz84mw3zxqxw.jpg'),
(4, 'Соус', NULL, 'добавь яркого вкуса любому (у)блюду', 45, 'https://ariciapizza.ru/wp-content/uploads/2020/06/syrnyj-1-1.jpg'),
(5, 'Ролл', NULL, 'это лучше чем шаурма', 200, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS0rN2v0jlnsFka8WjZDKWWhN-TPuEzMGLWsMb_l4wWMw&s'),
(16, 'Дед', 'Старость', '', 1000, 'https://gorodrabot.ru/images/articles/927.jpg?v=1600416417');

-- --------------------------------------------------------

--
-- Структура таблицы `Ingredients`
--

CREATE TABLE `Ingredients` (
  `id` int NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `name_dishes` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Ingredients`
--

INSERT INTO `Ingredients` (`id`, `name`, `name_dishes`, `price`) VALUES
(1, 'булочка', 'Бургер', NULL),
(2, 'мясная котлета', 'Бургер', NULL),
(3, 'салат Айсберг', 'Бургер', NULL),
(4, 'помидор', 'Бургер', NULL),
(5, 'майонез', 'Бургер', NULL),
(6, 'кетчуп', 'Бургер', NULL),
(7, 'картофель', 'Картошка Фри', NULL),
(8, 'соль', 'Картошка Фри', NULL),
(9, 'спрайт', 'Напиток', NULL),
(10, 'фанта', 'Напиток', NULL),
(11, 'кола', 'Напиток', NULL),
(12, 'чай', 'Напиток', NULL),
(13, 'кофе', 'Напиток', NULL),
(14, 'уксус', 'Соус', NULL),
(15, 'томаты', 'Соус', NULL),
(16, 'базилик', 'Соус', NULL),
(17, 'усилители вкуса', 'Соус', NULL),
(18, 'лаваш', 'Ролл', NULL),
(19, 'нагетсы', 'Ролл', NULL),
(20, 'соус тар-тар', 'Ролл', NULL),
(21, 'терияки', 'Ролл', NULL),
(66, 'Старость', 'Дед', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `Orders`
--

CREATE TABLE `Orders` (
  `id` bigint UNSIGNED NOT NULL,
  `status` varchar(255) DEFAULT NULL,
  `dishes_name` text,
  `total_price` int DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `time` time DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `comment` text,
  `ingredients_name` text,
  `user_id` int DEFAULT NULL,
  `courier_login` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `Users`
--

CREATE TABLE `Users` (
  `id` bigint UNSIGNED NOT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `role` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `order_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `Users`
--

INSERT INTO `Users` (`id`, `Name`, `role`, `login`, `password`, `order_id`) VALUES
(2, NULL, 'user', 'суп', '$2y$10$Hcn8VwM26XSLklOvY/hPDuqVU7IvmhII6.QKsFfCwLxcLFy8Yd2Iq', NULL),
(3, NULL, 'manager', 'менеджер', '$2y$10$dDrIVX3E8k9ecN8vEJUixufvOZFDZHyY9WOIjPk.o9H5jRoCYH83K', NULL),
(4, NULL, 'courier', 'курьер', '$2y$10$TWIWUxC4U1zlTgsItDcyVOUhMxnXoikcG/o56eWDObartnGHBOfFy', NULL),
(5, NULL, 'cook', 'повар', '$2y$10$T0sFjy6WcOQPwAULllBlnua8mMbOlmY67y1EXMD.a.xfX3WrTsfXS', NULL),
(6, NULL, 'user', 'Настена', '$2y$10$nuLxoOPsM/D.yZzUr.3SvOTJExBVpFm0JP13fTnR5FeGHDeMmxv36', NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `Dishes`
--
ALTER TABLE `Dishes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `Ingredients`
--
ALTER TABLE `Ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name`),
  ADD KEY `name_2` (`name`);

--
-- Индексы таблицы `Orders`
--
ALTER TABLE `Orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- Индексы таблицы `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `Dishes`
--
ALTER TABLE `Dishes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `Ingredients`
--
ALTER TABLE `Ingredients`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT для таблицы `Orders`
--
ALTER TABLE `Orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `Users`
--
ALTER TABLE `Users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
