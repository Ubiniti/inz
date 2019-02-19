-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 19 Lut 2019, 19:24
-- Wersja serwera: 10.1.36-MariaDB
-- Wersja PHP: 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `inzynierka`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `migration_versions`
--

CREATE TABLE `migration_versions` (
  `version` varchar(14) COLLATE utf8mb4_unicode_ci NOT NULL,
  `executed_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `join_date` date NOT NULL,
  `country` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `birth_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Zrzut danych tabeli `user`
--

INSERT INTO `user` (`id`, `username`, `roles`, `password`, `email`, `join_date`, `country`, `birth_date`) VALUES
(1, 'adminJ', 'ROLE_ADMIN', '$argon2i$v=19$m=1024,t=2,p=2$TTVBckxqSEN5ajhqY2V1dw$p4e2RwJrmuJ0kb+HlyRxyXzRFaurdZTvQMpSMMll9gM', 'michal.sidor3@pollub.edu.pl', '2019-02-15', 'Poland', '1996-08-26'),
(4, 'user', '[\"ROLE_USER\"]', '$argon2i$v=19$m=1024,t=2,p=2$eUJrejViVjdVdHhNbDJsZg$zLsMyiS3xrWoPYJmRbgDn5Wd9h9TzZv0XYfFLnUGXYY', 'user@users.com', '2019-02-18', 'AI', '1999-12-10'),
(5, 'user2', '[\"ROLE_USER\"]', '$argon2i$v=19$m=1024,t=2,p=2$c3ZwRmFPTWE2UEhEd0JMQw$pwCNw5S6VxTZh6AKridJDTJJpj+fsMxo5cTzbb0Zxvw', 'user2@users.com', '2019-02-18', 'AL', '1999-11-11'),
(6, 'user3', '[\"ROLE_USER\"]', '$argon2i$v=19$m=1024,t=2,p=2$MlU2VTNaRGdrc3R1enllQg$XBDgK3Ek7pKhSr4Dv26SAaFFAp8+T2D3Pktnlpzf3Jo', 'user3@users.com', '2019-02-18', 'OM', '1987-11-11'),
(7, 'user4', '[\"ROLE_USER\"]', '$argon2i$v=19$m=1024,t=2,p=2$cUJUNXdEM0xvRXdSSmJCTQ$XLIcEAbvQ5NCc7PfGUHozs3Ja4wavYUgKiU2s82bJ6g', 'user4@gmail.com', '2019-02-18', 'AO', '1977-01-31');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `migration_versions`
--
ALTER TABLE `migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indeksy dla tabeli `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT dla tabeli `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
