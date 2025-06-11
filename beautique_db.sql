-- Create the database
CREATE DATABASE IF NOT EXISTS `beautique_db`;
USE `beautique_db`;

-- Set SQL mode and timezone
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Charset setup
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Table: categories
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert: categories
INSERT INTO `categories` (`id`, `category_name`) VALUES
(4, 'Haircare'),
(2, 'Makeup'),
(1, 'Perfume'),
(3, 'Skincare');

-- Table: products
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert: products
INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `category_id`, `image`) VALUES
(1, 'Luxury Eau de Parfum', 'A premium long-lasting fragrance.', 150.00, 10, 1, 'perfume1.jpg'),
(2, 'Rose Mist Perfume', 'A delicate floral fragrance for daily wear.', 95.00, 10, 1, 'perfume2.jpg'),
(3, 'Matte Red Lipstick', 'Highly pigmented, long-wearing lipstick.', 25.00, 10, 2, 'lipstick1.jpg'),
(4, 'Nude Lip Gloss', 'Glossy finish with hydrating properties.', 18.00, 10, 2, 'lipgloss1.jpg'),
(5, 'Vitamin C Serum', 'Brightens skin and reduces dark spots.', 40.00, 10, 3, 'serum1.jpg'),
(6, 'Hydrating Face Cream', 'Moisturizer with SPF 30.', 35.00, 10, 3, 'cream1.jpg'),
(7, 'Argan Oil Hair Serum', 'Nourishes and smooths frizzy hair.', 30.00, 10, 4, 'hairserum1.jpg'),
(8, 'Keratin Shampoo', 'Strengthens and repairs damaged hair.', 20.00, 10, 4, 'shampoo1.jpg');

-- Table: users
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fname` varchar(100) NOT NULL,
  `Lname` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert: users
INSERT INTO `users` (`id`, `fname`, `Lname`, `email`, `password`) VALUES
(1, 'yara', 'Alwadei', 'yara@example.com', '12345678'),
(3, 'fatimah', 'Al Mousa', 'fatimahM@example.com', 'hashedpassword3'),
(5, 'shahad', 'ALamri', 'shahaad@example.com', 'paswoord');

COMMIT;

-- Reset charset
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
