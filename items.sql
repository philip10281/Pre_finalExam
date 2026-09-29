-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2026 at 06:13 AM
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
-- Database: `hardware_materials`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `item_number` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(150) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `item_number`, `category`, `category_name`, `name`, `description`, `price`, `icon`, `color`, `image`, `created_at`) VALUES
(1, 1, 'hand-tools', 'Hand Tools', 'Claw Hammer', 'Hand tool used for driving and removing nails.', 350.00, 'fa-hammer', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/1.webp', '2026-09-26 06:53:08'),
(2, 2, 'hand-tools', 'Hand Tools', 'Flat Screwdriver', 'Screwdriver designed for slotted or flat-head screws.', 120.00, 'fa-screwdriver', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/2.jpg', '2026-09-26 06:53:08'),
(3, 3, 'hand-tools', 'Hand Tools', 'Phillips Screwdriver', 'Screwdriver designed for cross-head screws.', 150.00, 'fa-screwdriver', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/3.jpg', '2026-09-26 06:53:08'),
(4, 4, 'hand-tools', 'Hand Tools', 'Combination Pliers', 'Hand tool used for gripping, bending, and cutting wires.', 280.00, 'fa-wrench', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/4.jpg', '2026-09-26 06:53:08'),
(5, 5, 'hand-tools', 'Hand Tools', 'Adjustable Wrench', 'Wrench with an adjustable jaw for different nut sizes.', 320.00, 'fa-wrench', 'from-amber-500/20 to-orange-500/20 text-amber-600', 'images/5.jpg', '2026-09-26 06:53:08'),
(6, 1, 'power-tools', 'Power Tools', 'Electric Drill', 'Power tool used for drilling holes in wood, metal, and other materials.', 1800.00, 'fa-bolt-lightning', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/6.webp', '2026-09-26 06:53:08'),
(7, 2, 'power-tools', 'Power Tools', 'Angle Grinder', 'Power tool used for cutting, grinding, and polishing materials.', 2200.00, 'fa-compact-disc', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/7.avif', '2026-09-26 06:53:08'),
(8, 3, 'power-tools', 'Power Tools', 'Circular Saw', 'Power saw used for making straight cuts in wood and boards.', 2800.00, 'fa-gear', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/8.webp', '2026-09-26 06:53:08'),
(9, 4, 'power-tools', 'Power Tools', 'Jigsaw', 'Power saw used for curved and detailed cuts.', 2000.00, 'fa-saw-blade', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/9.webp', '2026-09-26 06:53:08'),
(10, 5, 'power-tools', 'Power Tools', 'Heat Gun', 'Tool that produces hot air for heating, stripping, and other applications.', 1500.00, 'fa-fire-flame-curved', 'from-red-500/20 to-rose-500/20 text-red-600', 'images/10.webp', '2026-09-26 06:53:08'),
(11, 1, 'plumbing', 'Plumbing Materials', 'PVC Pipe', 'Plastic pipe commonly used for water and drainage systems.', 180.00, 'fa-faucet', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/11.webp', '2026-09-26 06:53:08'),
(12, 2, 'plumbing', 'Plumbing Materials', 'PVC Elbow', 'Fitting used to change the direction of a PVC pipe.', 45.00, 'fa-turn-up', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/12.jpg', '2026-09-26 06:53:08'),
(13, 3, 'plumbing', 'Plumbing Materials', 'PVC Tee', 'Pipe fitting used to connect three pipe sections.', 55.00, 'fa-diagram-project', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/13.jpg', '2026-09-26 06:53:08'),
(14, 4, 'plumbing', 'Plumbing Materials', 'Pipe Wrench', 'Wrench designed for gripping and turning pipes and fittings.', 450.00, 'fa-wrench', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/14.webp', '2026-09-26 06:53:08'),
(15, 5, 'plumbing', 'Plumbing Materials', 'Teflon Tape', 'Thread-sealing tape used to help prevent leaks in pipe connections.', 35.00, 'fa-tape', 'from-blue-500/20 to-cyan-500/20 text-blue-600', 'images/15.jpg', '2026-09-26 06:53:08'),
(16, 1, 'electrical', 'Electrical Materials', 'Electrical Wire', 'Conductive wire used for electrical connections and installations.', 850.00, 'fa-plug', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/16.jpg', '2026-09-26 06:53:08'),
(17, 2, 'electrical', 'Electrical Materials', 'Circuit Breaker', 'Safety device that interrupts electrical current during overloads or faults.', 450.00, 'fa-shield-halved', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/17.png', '2026-09-26 06:53:08'),
(18, 3, 'electrical', 'Electrical Materials', 'Electrical Outlet', 'Device that provides a connection point for electrical appliances.', 120.00, 'fa-plug-circle-bolt', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/18.webp', '2026-09-26 06:53:08'),
(19, 4, 'electrical', 'Electrical Materials', 'Light Switch', 'Electrical device used to turn a light or circuit on and off.', 90.00, 'fa-toggle-on', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/19.jpg', '2026-09-26 06:53:08'),
(20, 5, 'electrical', 'Electrical Materials', 'Junction Box', 'Enclosure used to protect and organize electrical wire connections.', 75.00, 'fa-box', 'from-yellow-500/20 to-amber-500/20 text-yellow-600', 'images/20.jpg', '2026-09-26 06:53:08'),
(21, 1, 'construction', 'Construction Materials', 'Common Nail', 'Metal fastener used to join wood and other construction materials.', 90.00, 'fa-hashtag', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/21.jpg', '2026-09-26 06:53:08'),
(22, 2, 'construction', 'Construction Materials', 'Wood Screw', 'Threaded fastener commonly used for securing wood materials.', 140.00, 'fa-screwdriver', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/22.jpg', '2026-09-26 06:53:08'),
(23, 3, 'construction', 'Construction Materials', 'Cement', 'Binding material used in concrete, mortar, and other construction work.', 280.00, 'fa-cubes', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/23.jpg', '2026-09-26 06:53:08'),
(24, 4, 'construction', 'Construction Materials', 'GI Sheet', 'Galvanized metal sheet used for roofing and various construction applications.', 650.00, 'fa-sheet-plastic', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/24.jpg', '2026-09-26 06:53:08'),
(25, 5, 'construction', 'Construction Materials', 'Paint Roller', 'Tool used to apply paint evenly on walls and other large surfaces.', 180.00, 'fa-paint-roller', 'from-emerald-500/20 to-teal-500/20 text-emerald-600', 'images/25.webp', '2026-09-26 06:53:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
