SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `elearning_website` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `elearning_website`;

CREATE TABLE `announcements` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `forum_comments` (
  `comment_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `forum_comments` (`comment_id`, `post_id`, `content`, `created_by`, `created_at`) VALUES
(1, 4, 'sadasd', 'dsa', '2024-08-13 14:47:13'),
(2, 4, 'asdasd', 'dsa', '2024-08-13 14:47:20');

CREATE TABLE `forum_posts` (
  `post_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `forum_posts` (`post_id`, `title`, `content`, `created_by`, `created_at`) VALUES
(1, 'asd', 'asdasdadsa', 321, '2024-08-13 14:41:21'),
(2, 'asdasd', 'asdasd', 321, '2024-08-13 14:41:25'),
(3, 'asdasd', 'asdasd', 321, '2024-08-13 14:42:17'),
(4, 'asdas', 'asdasd', 321, '2024-08-13 14:42:21');

CREATE TABLE `materials` (
  `material_id` int(11) NOT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `bab` varchar(255) DEFAULT NULL,
  `content` text,
  `parent_bab_id` int(11) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `materials` (`material_id`, `subject`, `bab`, `content`, `parent_bab_id`, `video_path`) VALUES
(1, 'Matematika', 'Bab 1: Algebra', 'Introduction to Algebra', NULL, './image/menjelaskan.mp4'),
(2, 'Matematika', 'Bab 1: Algebra', 'Solving Linear Equations', NULL, './image/menjelaskan.mp4'),
(3, 'Matematika', 'Bab 2: Geometry', 'Basic Shapes and Properties', NULL, './image/menjelaskan.mp4'),
(4, 'Matematika', 'Bab 2: Geometry', 'Introduction to Angles', NULL, './image/menjelaskan.mp4'),
(5, 'Matematika', 'Bab 1', 'This is the content for Bab 1 of Matematika', NULL, './image/menjelaskan.mp4');

CREATE TABLE `profile_stickers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `sticker_path` varchar(255) NOT NULL,
  `pos_x` int(11) NOT NULL,
  `pos_y` int(11) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `z_index` int(11) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `profile_stickers` (`id`, `student_id`, `sticker_path`, `pos_x`, `pos_y`, `width`, `height`, `z_index`) VALUES
(102, 321, 'uploads/stickers/fototest.png', 104, 1755, 150, 100, 1);

CREATE TABLE `task` (
  `task_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `due_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `student_id` int(11) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `task` (`task_id`, `title`, `description`, `due_date`, `created_at`, `updated_at`, `student_id`, `link`) VALUES
(1, 'asd', 'asd', '2024-08-15', '2024-08-12 13:22:30', '2024-08-13 13:34:37', 321, 'https://www.youtube.com/@penyu1913');

CREATE TABLE `users` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `birth_place` varchar(255) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `religion` varchar(50) DEFAULT NULL,
  `address` text,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `role` varchar(9) NOT NULL DEFAULT 'Murid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`student_id`, `name`, `nickname`, `email`, `class`, `birth_place`, `birth_date`, `gender`, `religion`, `address`, `phone`, `password`, `photo_path`, `role`) VALUES
(321, 'dsa', 'dsa', 'mohamadrakha90@gmail.com', '12', 'cirebon', '2024-08-13', 'Male', 'islam', 'asdasqwd', '08158585858', '$2y$10$ktFPnsW7k.bEHDWjJoMuCuSJWdisV88.qgPct5KUHu6C2tnj8tC2i', 'uploads/profile_photos/images.jpg', 'Murid');


ALTER TABLE `announcements`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `forum_comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `post_id` (`post_id`);

ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`post_id`),
  ADD KEY `created_by` (`created_by`);

ALTER TABLE `materials`
  ADD PRIMARY KEY (`material_id`);

ALTER TABLE `profile_stickers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

ALTER TABLE `task`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `student` (`student_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`student_id`);


ALTER TABLE `announcements`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `forum_comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `forum_posts`
  MODIFY `post_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

ALTER TABLE `profile_stickers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

ALTER TABLE `task`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;


ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`student_id`);

ALTER TABLE `forum_comments`
  ADD CONSTRAINT `forum_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`post_id`) ON DELETE CASCADE;

ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`student_id`);

ALTER TABLE `profile_stickers`
  ADD CONSTRAINT `profile_stickers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE;

ALTER TABLE `task`
  ADD CONSTRAINT `student` FOREIGN KEY (`student_id`) REFERENCES `users` (`student_id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
