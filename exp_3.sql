-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 26, 2024 at 11:35 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tle-exp2`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer_content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `answers`
--

INSERT INTO `answers` (`id`, `question_id`, `answer_content`, `created_at`, `user_id`) VALUES
(7, 18, 'sadfasdf', '2024-09-25 19:41:19', 8),
(8, 15, 'xzvsdfsdfdsf', '2024-09-25 19:51:19', 8);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `expert_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `is_handled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','accepted') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tag_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `user_id`, `tag_id`, `content`, `created_at`) VALUES
(5, 1, 2, 'asdfdsafsdafsda', '2024-09-25 11:17:59'),
(6, 1, 2, 'sadfsadfsdafasfdsa', '2024-09-25 11:18:01'),
(7, 1, 2, 'asdfsadfsdafasfdsda', '2024-09-25 11:18:04'),
(8, 1, 1, 'sadfsafsdafas', '2024-09-25 11:24:03'),
(9, 1, 1, 'asdfasdfsadfasdf', '2024-09-25 11:24:06'),
(10, 1, 1, 'fasdfdsafsadfasdfds', '2024-09-25 11:24:09'),
(11, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:24:45'),
(12, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:24:47'),
(13, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:24:49'),
(14, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:25:10'),
(15, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:25:12'),
(16, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:25:14'),
(17, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:25:16'),
(18, 1, 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent mi tellus, condimentum eget ante non, cursus ultricies felis. Suspendisse luctus dolor dui, ut suscipit eros hendrerit at. Curabitur eu vulputate est. Mauris libero urna, pellentesque vitae porttitor vel, blandit id justo. Pellentesque hendrerit fringilla dolor, a imperdiet tortor consequat eu. Nunc maximus condimentum sapien, at tempus purus dapibus eu. Suspendisse nec elit volutpat, varius nisl at, euismod nulla. Donec et placerat nisl. Duis hendrerit eget metus ac mollis.', '2024-09-25 11:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE `rating` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_votes` int(11) DEFAULT 0,
  `points` decimal(5,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `tag_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `tag_name`) VALUES
(1, 'Maths'),
(2, 'Science'),
(3, 'physics'),
(4, 'Computer Science'),
(5, 'Biology'),
(6, 'Geography'),
(7, 'History\r\n'),
(8, 'English '),
(9, 'Dutch'),
(10, 'Spanish'),
(11, 'Japanese'),
(12, 'Portugese'),
(13, 'Chinese');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','expert','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`) VALUES
(1, 'Hans Anders', 'HansAnders@live.nl', '94@4769#hFhD83hD9', 'expert'),
(2, 'me', 'apple@gmail.com', 'me', 'expert'),
(3, 'test', 'test', 'test\r\n', 'user'),
(4, 'banaan', 'banaan@gmail.com', '$2y$10$I/PRCP6hg5YKLvuI.DJT/OjAsfiZQSHmCyzheDehMd5Ss7coOMCju', 'user'),
(5, 'kiwi', 'kiwi@gmail.com', '$2y$10$5OECMCI/4yQORJKgai5n0eXB9BmiIpvVGMZzmSBzKJ7/6AFzOq7D6', 'expert'),
(6, 'obada', 'ubadafatta@gmail.com', '$2y$10$LmPRfjikOltY6Dkf7qFWWefxq20wrBDNWc1i0jgaho3e7jZjix/NW', 'user'),
(7, 'ubadafatta@gmail.com', 'obadafatta@gmail.com', '$2y$10$VaefU25BrtJ0U9kujDThbOUCsGGtd8iCnoqZO1Ido6HBpZrJC4WRy', 'expert'),
(8, 'admin', 'admin@admin.com', '$2y$10$FWbJ0C03V2HVwqazWsGq3OkKOd6.dYASHsT/2d2C48ZXK1q8dIhNG', 'expert');

-- --------------------------------------------------------

--
-- Table structure for table `usertags`
--

CREATE TABLE `usertags` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `usertags`
--

INSERT INTO `usertags` (`id`, `user_id`, `tag_id`) VALUES
(14, 1, 12),
(17, 5, 1),
(18, 5, 3),
(19, 5, 4),
(20, 7, 1),
(21, 8, 5),
(22, 8, 13),
(23, 8, 4),
(24, 8, 9),
(25, 8, 8),
(26, 8, 6),
(27, 8, 7),
(28, 8, 11),
(29, 8, 1),
(30, 8, 3),
(31, 8, 12),
(32, 8, 2),
(33, 8, 10);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `answers_ibfk_2` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expert_id` (`expert_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tag_id` (`tag_id`);

--
-- Indexes for table `rating`
--
ALTER TABLE `rating`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usertags`
--
ALTER TABLE `usertags`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rating`
--
ALTER TABLE `rating`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `usertags`
--
ALTER TABLE `usertags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`expert_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
