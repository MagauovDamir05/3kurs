-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 11 2025 г., 22:13
-- Версия сервера: 8.0.34-26-beget-1-1
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `p7789846_pis`
--

-- --------------------------------------------------------

--
-- Структура таблицы `accounts`
--
-- Создание: Апр 17 2025 г., 07:47
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `account_id` int UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `accounts`
--

INSERT INTO `accounts` (`account_id`, `first_name`, `last_name`, `phone_number`, `password`, `created_at`, `status`) VALUES
(2, 'Иван', 'Иванов', '87776541289', '$2y$10$ALFDegGCmazmI7mZg3iIb./P5Apwu4gwJv12nrJUOF74BrvpHjaH.', '2025-04-12 16:21:06', 'active'),
(3, 'Андрей', 'Петров', '87779876541', '$2y$10$rYT/t.fzNRpzXKCuDUrIr.i3jf.BACCMuosQJZS5jxFQkrC28XmXi', '2025-04-12 16:24:47', 'active'),
(4, 'Admin', 'Main', '87771234567', '$2y$10$tWNaPGnY8oH1tzUdHf/6uuH.XMTIB6DNbmsYFzzapJVTGf5.2BPxS', '2025-04-15 10:40:52', 'active'),
(5, 'Матвей', 'Сидоров', '87715648759', '$2y$10$1kD3.pUYrmEQycM2RMDv2uJWN2v6ibJ7wWGnEhiiIjysY.cf919Yi', '2025-04-16 11:40:36', 'active'),
(6, 'Нурсултан', 'Искаков', '87023654125', '$2y$10$KBS/pxIllqsuL5fnS3meoeOKlD/7aJEuGlsg5.oGDdjxgrfwZtKfy', '2025-04-16 11:41:45', 'active'),
(7, 'Дидар', 'Аманбаев', '87754569874', '$2y$10$cr4K42JpoZXeAN.mLj4E7.2xBg5S7b1HJgzfMshHK/VyCBE1BvdWK', '2025-04-16 11:46:12', 'active'),
(8, 'Даниял', 'Кенесов', '87756325412', '$2y$10$A7sSG0m8Ilf5bpvI6rZG0ObGjeoGAopVeRo8PRS9tvtqakURhIYsC', '2025-04-16 11:47:05', 'active'),
(9, 'Данис', 'Абильмажинов', '87754123685', '$2y$10$9OJdgJwOsuueSaNuTy8AdOkFWhrxvotP/I2LdRvu.swWlpis3NR7m', '2025-04-16 11:47:52', 'active'),
(10, 'Алексей', 'Федоренко', '87475689745', '$2y$10$jR24o4RIZTugye/LlzUCluDDiLT5AhGGLOtDTEBlPv.1KqfRElGOW', '2025-04-16 11:49:41', 'active');

-- --------------------------------------------------------

--
-- Структура таблицы `menu`
--
-- Создание: Апр 22 2025 г., 18:14
--

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `menu_id` int UNSIGNED NOT NULL,
  `category` varchar(100) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `image_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `menu`
--

INSERT INTO `menu` (`menu_id`, `category`, `item_name`, `description`, `price`, `image_path`) VALUES
(1, 'Пицца', 'Три сыра пицца 25 см', 'Моцарелла, чеддер, пармезан, белый соус\r\n', '1490.00', 'uploads/Fruit.png'),
(3, 'Бургер', 'БигПанда Бургер', 'БигПанды с двумя сочными котлетами из говядины', '2590.00', 'uploads/BigPanda1.png'),
(6, 'Пицца', 'Пепперони Фреш пицца', 'Сыр моцарелла, красный соус, пепперони, помидоры', '1390.00', 'uploads/Margo.png'),
(7, 'Пицца', 'Пепперони пицца 25 см', 'Сыр моцарелла, красный соус, пепперони.', '1590.00', 'uploads/Mario.png'),
(8, 'Бургер', 'Чикен бургер', 'Сочная котлета из куриного мяса', '1690.00', 'uploads/Hipe.png'),
(10, 'Напитки', 'Компот 1л', '', '690.00', ''),
(11, 'Напитки', 'Кола 1л', '', '690.00', 'uploads/Cola.png');

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--
-- Создание: Апр 22 2025 г., 13:28
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` int NOT NULL,
  `sender_id` int UNSIGNED DEFAULT NULL,
  `receiver_id` int UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `sent_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `is_admin_sender` tinyint(1) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_admin_sender`, `is_read`) VALUES
(1, 2, 0, 'dfghbvcd', '2025-04-15 14:13:15', 0, 1),
(2, 0, 2, 'патримс', '2025-04-15 14:23:36', 1, 0),
(3, 0, 2, 'маитмсамп', '2025-04-15 14:23:43', 1, 0),
(4, 0, 2, 'jhght', '2025-04-15 14:25:25', 1, 0),
(5, 2, 0, 'dfgfvd', '2025-04-15 14:26:03', 0, 1),
(6, 0, 2, 'вавппт', '2025-04-15 15:46:54', 1, 0),
(7, 2, 0, 'hgbv', '2025-04-15 15:47:29', 0, 1),
(8, 6, 0, 'У меня проблемы', '2025-04-16 11:42:49', 0, 1),
(9, 0, 10, 'Вы не выбрали количество игроков!', '2025-04-16 11:51:07', 1, 0),
(10, 10, 0, 'Ой извините, нас будет 5 игроков', '2025-04-16 11:51:57', 0, 1),
(11, 0, 10, 'Хорошо', '2025-04-17 12:36:32', 1, 0),
(12, 0, 10, '111', '2025-04-22 16:05:19', 1, 0),
(13, 0, 10, '111', '2025-04-22 16:05:47', 1, 0),
(14, 0, 6, '1111', '2025-04-22 16:05:52', 1, 0),
(15, 0, 2, 'ваи', '2025-04-22 16:29:25', 1, 0),
(16, 0, 10, 'dfgbn', '2025-04-22 16:38:29', 1, 0),
(17, 0, 10, 'wedfghj\r\n', '2025-04-22 16:38:39', 1, 0),
(18, 0, 10, 'dfghjk\r\ngnmk\r\nhg\r\n', '2025-04-22 16:38:49', 1, 0),
(19, 0, 10, 'efghj\r\n', '2025-04-22 16:38:55', 1, 0),
(20, 0, 10, 'ап', '2025-04-22 19:37:54', 1, 0),
(21, 10, 0, 'fgbgbvbb', '2025-04-22 19:40:26', 0, 1),
(22, 10, 0, 'rgffvf', '2025-04-22 19:40:29', 0, 1),
(23, 6, 0, 'trhnb', '2025-04-22 20:05:21', 0, 1),
(24, 6, 0, 'thnbv ', '2025-04-22 20:05:25', 0, 1),
(25, 5, 0, 'gghggh', '2025-04-22 20:08:20', 0, 1),
(26, 5, 0, 'tgggb', '2025-04-22 20:08:23', 0, 1),
(27, 10, 0, 'jhhjhj', '2025-04-22 20:20:23', 0, 1),
(28, 10, 0, 'ggghgh', '2025-04-22 20:25:49', 0, 1),
(29, 10, 0, 'ghgg', '2025-04-22 20:25:51', 0, 1),
(30, 0, 7, 'gfhnbv', '2025-04-22 20:26:23', 1, 0),
(31, 0, 7, 'thgb', '2025-04-22 20:26:31', 1, 0),
(32, 0, 7, 'gfg', '2025-04-25 14:57:07', 1, 0),
(33, 0, 10, 'ghhj', '2025-04-25 14:58:28', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `reservations`
--
-- Создание: Апр 24 2025 г., 08:46
--

DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `reservation_id` int UNSIGNED NOT NULL,
  `account_id` int UNSIGNED NOT NULL,
  `reservation_name` varchar(100) NOT NULL,
  `reservation_phone` varchar(20) NOT NULL,
  `tariff_id` int UNSIGNED NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `players_count` tinyint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('в ожидании','принято','отклонено') DEFAULT 'в ожидании'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `account_id`, `reservation_name`, `reservation_phone`, `tariff_id`, `reservation_date`, `reservation_time`, `players_count`, `created_at`, `status`) VALUES
(1, 2, 'Иван', '87776541289', 1, '2025-04-18', '11:00:00', 4, '2025-04-15 13:41:44', 'принято'),
(2, 5, 'Матвей', '87715648759', 1, '2025-04-30', '11:00:00', 9, '2025-04-16 11:41:03', 'принято'),
(3, 6, 'Нурсултан', '87023654125', 1, '2025-04-30', '12:00:00', 10, '2025-04-16 11:43:30', 'принято'),
(4, 5, 'Матвей', '87715648759', 1, '2025-05-01', '13:00:00', 4, '2025-04-16 11:45:01', 'в ожидании'),
(5, 7, 'Дидар', '87754569874', 1, '2025-05-01', '19:00:00', 9, '2025-04-16 11:46:33', 'отклонено'),
(6, 8, 'Даниял', '87756325412', 1, '2025-05-03', '15:00:00', 6, '2025-04-16 11:47:22', 'в ожидании'),
(7, 9, 'Данис', '87754123685', 1, '2025-05-05', '14:00:00', 4, '2025-04-16 11:48:11', 'в ожидании'),
(8, 5, 'Матвей', '87715648759', 1, '2025-05-05', '19:00:00', 7, '2025-04-16 11:48:51', 'в ожидании');

-- --------------------------------------------------------

--
-- Структура таблицы `reviews`
--
-- Создание: Апр 17 2025 г., 07:47
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `review_id` int UNSIGNED NOT NULL,
  `account_id` int UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `review` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Дамп данных таблицы `reviews`
--

INSERT INTO `reviews` (`review_id`, `account_id`, `rating`, `review`, `created_at`) VALUES
(1, 2, 5, 'Потрясающее впечатление! Ребёнок в восторге, мы обязательно вернёмся ещё раз!', '2025-05-09 10:25:00'),
(2, 5, 4, 'Хороший сервис и качественное оборудование. Немного не хватило времени на игру.', '2025-05-09 10:30:00'),
(3, 7, 5, 'Один из лучших VR-опытов, что я видел. Отдельное спасибо за атмосферу.', '2025-05-09 10:35:00'),
(4, 9, 3, 'Игра понравилась, но было немного жарко в помещении. Хотелось бы больше карт.', '2025-05-09 10:40:00'),
(5, 10, 5, 'Всё было супер! Персонал приветливый, игра захватывающая, рекомендую всем.', '2025-05-09 10:45:00');

-- --------------------------------------------------------

--
-- Структура таблицы `tariffs`
--
-- Создание: Апр 17 2025 г., 07:47
--

DROP TABLE IF EXISTS `tariffs`;
CREATE TABLE `tariffs` (
  `tariff_id` int UNSIGNED NOT NULL,
  `tariff_name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tariffs`
--

INSERT INTO `tariffs` (`tariff_id`, `tariff_name`, `description`, `price`, `created_at`) VALUES
(1, 'Открытая игра', 'Свободная игра с другими участниками', '5000.00', '2025-04-15 13:36:02'),
(2, 'Lite', 'Лёгкий тариф для 2–4 игроков', '7000.00', '2025-04-15 13:36:02'),
(3, 'Standart', 'Стандартная сессия', '10000.00', '2025-04-15 13:36:02'),
(4, 'Max', 'Полный безлимит + VIP-зона', '15000.00', '2025-04-15 13:36:02');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Индексы таблицы `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `fk_reservations_accounts` (`account_id`),
  ADD KEY `fk_reservations_tariffs` (`tariff_id`);

--
-- Индексы таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `fk_reviews_accounts` (`account_id`);

--
-- Индексы таблицы `tariffs`
--
ALTER TABLE `tariffs`
  ADD PRIMARY KEY (`tariff_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `accounts`
--
ALTER TABLE `accounts`
  MODIFY `account_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT для таблицы `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `tariffs`
--
ALTER TABLE `tariffs`
  MODIFY `tariff_id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_accounts` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reservations_tariffs` FOREIGN KEY (`tariff_id`) REFERENCES `tariffs` (`tariff_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_accounts` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
