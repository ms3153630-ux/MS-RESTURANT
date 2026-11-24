-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 06, 2026 at 08:39 PM
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
-- Database: `restaurant_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `status`, `created_at`) VALUES
(1, 'Starters', 'Appetizing soups, crispy bites, and mouth-watering starters to kick off your feast.', 'cat-starters.jpg', 'active', '2026-10-06 10:42:32'),
(2, 'BBQ', 'Tender, chargrilled meats infused with authentic aromatic spices and smoky herbs.', 'cat-bbq.jpg', 'active', '2026-10-06 10:42:32'),
(3, 'Burgers', 'Gourmet artisanal patties grilled to perfection with melting cheese and secret sauces.', 'cat-burgers.jpg', 'active', '2026-10-06 10:42:32'),
(4, 'Pizza', 'Wood-fired crispy dough crowned with rich marinara, melted mozzarella, and fresh toppings.', 'cat-pizza.jpg', 'active', '2026-10-06 10:42:32'),
(5, 'Chinese', 'Wok-tossed delicacies featuring sizzling noodles, savory gravies, and fragrant fried rice.', 'cat-chinese.jpg', 'active', '2026-10-06 10:42:32'),
(6, 'Pakistani', 'Classic royal heritage curries, fragrant biryanis, and succulent karahi specials.', 'cat-pakistani.jpg', 'active', '2026-10-06 10:42:32'),
(7, 'Fast Food', 'Crispy loaded platters, golden fries, crunchy wings, and juicy wraps.', 'cat-fastfood.jpg', 'active', '2026-10-06 10:42:32'),
(8, 'Drinks', 'Freshly brewed mocktails, chilled shakes, mint lemonades, and traditional coolers.', 'cat-drinks.jpg', 'active', '2026-10-06 10:42:32'),
(9, 'Desserts', 'Decadent sweet endings: warm molten lava cakes, royal kheer, and artisan cheesecakes.', 'cat-desserts.jpg', 'active', '2026-10-06 10:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contacts`
--

INSERT INTO `contacts` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Zubair Tariq', 'zubair@example.com', '03331122334', 'Catering Inquiry for Corporate Dinner', 'Hello, we would like to inquire about your catering packages for an upcoming corporate event of 50 people.', 'unread', '2026-10-06 07:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(3,1) NOT NULL DEFAULT 4.5,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0,
  `is_special` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`id`, `category_id`, `name`, `description`, `ingredients`, `price`, `image`, `rating`, `is_featured`, `is_popular`, `is_special`, `status`, `created_at`) VALUES
(1, 1, 'Dynamite Prawns', 'Crispy fried prawns tossed in our signature spicy and creamy dynamite dressing.', 'King Prawns, Paprika, Sriracha, Mayo, Garlic, Spring Onions', 1150.00, 'dynamite-prawns.jpg', 4.9, 1, 1, 0, 'active', '2026-10-06 10:42:32'),
(2, 1, 'Stuffed Chicken Strips', 'Golden fried chicken tenders stuffed with melted cheddar and jalape├▒o bits.', 'Chicken Breast, Cheddar Cheese, Jalape├▒os, Breadcrumbs, Herbs', 750.00, 'stuffed-strips.jpg', 4.7, 0, 1, 0, 'active', '2026-10-06 10:42:32'),
(3, 1, 'Hot & Sour Soup', 'Rich and hearty soup loaded with shredded chicken, black mushrooms, egg ribbons, and bamboo shoots.', 'Chicken Broth, Shredded Chicken, Wood Ear Mushroom, Egg, Vinegar, Chili Oil', 550.00, 'hot-sour-soup.jpg', 4.6, 0, 0, 0, 'active', '2026-10-06 10:42:32'),
(4, 2, 'Chicken Malai Boti', 'Tender boneless chicken cubes marinated in heavy cream, green chilies, and ground cardamom.', 'Boneless Chicken, Fresh Cream, Yogurt, Cardamom, White Pepper, Green Chili', 890.00, 'malai-boti.jpg', 4.9, 1, 1, 1, 'active', '2026-10-06 10:42:32'),
(5, 2, 'Beef Bihari Kabab', 'Mouth-melting slivers of beef marinated with raw papaya and authentic mustard oil.', 'Prime Beef, Papaya Paste, Fried Onions, Mustard Oil, Garam Masala', 950.00, 'bihari-kabab.jpg', 4.8, 1, 1, 0, 'active', '2026-10-06 10:42:32'),
(6, 2, 'Mutton Chops Grill', 'Succulent lamb/mutton ribs marinated overnight in ginger garlic and roasted cumin.', 'Mutton Ribs, Cumin, Coriander, Lemon, Ginger-Garlic paste, Kashmiri Chili', 1650.00, 'mutton-chops.jpg', 4.9, 0, 0, 1, 'active', '2026-10-06 10:42:32'),
(7, 3, 'Signature Gourmet Smash Burger', 'Double smashed beef patties, aged cheddar cheese, caramelized onions, pickles, and truffle mayo on a brioche bun.', 'Prime Beef Patty, Cheddar Cheese, Brioche Bun, Truffle Mayo, Pickles, Grilled Onions', 890.00, 'smash-burger.jpg', 4.9, 1, 1, 1, 'active', '2026-10-06 10:42:32'),
(8, 3, 'Crispy Zinger Crunch', 'Golden batter-fried chicken thigh glazed with spicy peri peri sauce and chilled slaw.', 'Crispy Chicken Thigh, Peri Peri Glaze, Coleslaw, Sesame Brioche Bun, Cheese Slice', 650.00, 'zinger-burger.jpg', 4.8, 0, 1, 0, 'active', '2026-10-06 10:42:32'),
(9, 3, 'Smokey BBQ Beef Burger', 'Chargrilled beef patty with crispy turkey bacon, smoked gouda, and hickory BBQ sauce.', 'Beef Patty, Turkey Bacon, Smoked Gouda, Hickory BBQ Sauce, Crispy Onions', 980.00, 'bbq-burger.jpg', 4.7, 0, 0, 0, 'active', '2026-10-06 10:42:32'),
(10, 4, 'Royal Chicken Tikka Pizza', 'Tender chunks of spicy chicken tikka, red onions, bell peppers, and bubbling mozzarella.', 'Pizza Dough, Pizza Sauce, Mozzarella, Chicken Tikka, Bell Peppers, Red Onion, Oregano', 1450.00, 'pizza-tikka.jpg', 4.8, 1, 1, 0, 'active', '2026-10-06 10:42:32'),
(11, 4, 'Smoked Pepperoni Feast', 'Artisanal crust topped with double beef pepperoni, zesty marinara sauce, and fresh basil.', 'Crispy Thin Crust, Beef Pepperoni, Mozzarella, Marinara, Parmesan, Fresh Basil', 1590.00, 'pizza-pepperoni.jpg', 4.9, 0, 1, 1, 'active', '2026-10-06 10:42:32'),
(12, 4, 'Creamy Ranch Supreme', 'Grilled chicken, sliced mushrooms, jalape├▒os, and drizzle of fresh herbed ranch dressing.', 'Hand Tossed Crust, White Ranch Base, Roasted Chicken, Mushrooms, Jalape├▒os, Mozzarella', 1500.00, 'pizza-ranch.jpg', 4.6, 0, 0, 0, 'active', '2026-10-06 10:42:32'),
(13, 5, 'Kung Pao Chicken', 'Classic Sichuan wok tossed chicken with dried red chilies, roasted peanuts, and spring onions.', 'Chicken Cubes, Roasted Peanuts, Szechuan Chili, Bell Peppers, Soy-Vinegar Reduction', 880.00, 'kung-pao.jpg', 4.8, 0, 1, 0, 'active', '2026-10-06 10:42:32'),
(14, 5, 'Egg Fried Rice Platter', 'Fragrant jasmine rice wok-fried with fluffy scrambled eggs, spring scallions, and sesame oil.', 'Jasmine Rice, Eggs, Carrots, Peas, Spring Onion, Sesame Oil, Light Soy', 520.00, 'fried-rice.jpg', 4.7, 0, 0, 0, 'active', '2026-10-06 10:42:32'),
(15, 5, 'Beef Chilli Dry', 'Crispy strips of prime beef wok-fried with spicy green chilies, ginger, and savory garlic sauce.', 'Prime Beef Strips, Thai Green Chilies, Fresh Ginger, Garlic, Dark Soy Sauce', 990.00, 'beef-chilli.jpg', 4.9, 1, 0, 1, 'active', '2026-10-06 10:42:32'),
(16, 6, 'Royal Mutton Dum Biryani', 'Aromatic basmati rice cooked on slow flame (dum) with tender mutton chunks and saffron spices.', 'Aged Basmati Rice, Mutton, Saffron Milk, Mint, Brown Onions, Royal Spices', 1190.00, 'mutton-biryani.jpg', 5.0, 1, 1, 1, 'active', '2026-10-06 10:42:32'),
(17, 6, 'Desi Chicken Shinwari Karahi', 'Cooked in pure animal fat and butter with fresh tomatoes, green chilies, and crushed black pepper.', 'Fresh Chicken, Vine Tomatoes, Green Chilies, Ginger Juliennes, Pure Desi Butter', 1350.00, 'chicken-karahi.jpg', 4.9, 1, 1, 0, 'active', '2026-10-06 10:42:32'),
(18, 6, 'Special Garlic Roghani Naan', 'Freshly baked tandoori flatbread brushed with garlic butter, sesame seeds, and fresh coriander.', 'Refined Flour, Milk, Butter, Garlic, Sesame Seeds, Coriander', 120.00, 'roghani-naan.jpg', 4.8, 0, 0, 0, 'active', '2026-10-06 10:42:32'),
(19, 7, 'Loaded Volcano Fries', 'Crinkle cut golden fries smothered in cheese sauce, diced grilled chicken, jalape├▒os, and bacon bits.', 'Potato Fries, Cheddar Cheese Sauce, Grilled Chicken, Jalape├▒os, Crispy Bits', 580.00, 'loaded-fries.jpg', 4.8, 0, 1, 0, 'active', '2026-10-06 10:42:32'),
(20, 7, 'Crispy Buffalo Wings (8 Pcs)', 'Deep fried chicken wings tossed in tangy cayenne pepper buffalo sauce with blue cheese dip.', 'Chicken Wings, Cayenne Hot Sauce, Butter, Vinegar, Blue Cheese Dip', 690.00, 'buffalo-wings.jpg', 4.7, 0, 1, 0, 'active', '2026-10-06 10:42:32'),
(21, 8, 'Fresh Mint Lemonade Cooler', 'Chilled crushed ice beverage with fresh garden mint leaves, lime juice, and soda water.', 'Garden Mint, Fresh Lime, Rock Salt, Soda Water, Crushed Ice, Sugar Cane Syrup', 280.00, 'mint-lemonade.jpg', 4.9, 0, 1, 0, 'active', '2026-10-06 10:42:32'),
(22, 8, 'Nutella Brownie Thick Shake', 'Rich creamy blend of whole milk, Nutella spread, fudge brownies, and chocolate drizzle.', 'Nutella, Vanilla Ice Cream, Fudge Brownie, Full Cream Milk, Whipped Cream', 490.00, 'nutella-shake.jpg', 4.9, 1, 1, 0, 'active', '2026-10-06 10:42:32'),
(23, 9, 'Molten Chocolate Lava Cake', 'Warm chocolate sponge filled with rich molten chocolate center, served with vanilla bean ice cream.', 'Belgian Dark Chocolate, Butter, Farm Eggs, Flour, Vanilla Bean Gelato', 620.00, 'lava-cake.jpg', 5.0, 1, 1, 1, 'active', '2026-10-06 10:42:32'),
(24, 9, 'Royal Shahi Kheer', 'Traditional slow-cooked fragrant rice pudding garnished with roasted pistachios, almonds, and silver vark.', 'Full Fat Milk, Broken Rice, Cardamom, Pistachios, Almonds, Saffron', 380.00, 'shahi-kheer.jpg', 4.8, 0, 0, 0, 'active', '2026-10-06 10:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(150) NOT NULL,
  `customer_phone` varchar(30) NOT NULL,
  `delivery_address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(50) NOT NULL DEFAULT 'Cash on Delivery',
  `payment_status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `order_status` enum('Pending','Confirmed','Preparing','Out for Delivery','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `delivery_address`, `city`, `notes`, `total_amount`, `delivery_fee`, `payment_method`, `payment_status`, `order_status`, `created_at`) VALUES
(1, 'ORD-20261001-101', 2, 'Ahmed Khan', 'ahmed@example.com', '03001234567', 'House 14-B, Sector 11-A, North Karachi', 'Karachi', 'Please provide extra mint sauce.', 1600.00, 150.00, 'Cash on Delivery', 'pending', 'Delivered', '2026-10-04 10:42:32'),
(2, 'ORD-20261003-102', 3, 'Fatima Noor', 'fatima@example.com', '03129876543', 'Apartment 402, Gulshan-e-Iqbal Block 13, Karachi', 'Karachi', 'Ring doorbell twice.', 2340.00, 150.00, 'Cash on Delivery', 'pending', 'Preparing', '2026-10-06 09:42:32'),
(3, 'ORD-20261006-816AF', 4, 'daniyalbaig', 'daniyalbaig@gmail.com', '03578754587', 'House 12-A, Street 7, Block 5, Clifton, Karachi 75600, Pakistan', 'Karachi', '', 1800.00, 150.00, 'Cash on Delivery', 'pending', 'Confirmed', '2026-10-06 11:22:59'),
(4, 'ORD-20261006-AAE2C', 1, 'ayeshanoor', 'ayeshanoor@gmail.com', '021343678637', 'chemomarket', 'Karachi', '', 900.00, 150.00, 'Cash on Delivery', 'pending', 'Confirmed', '2026-10-06 18:10:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `food_id` int(11) DEFAULT NULL,
  `food_name` varchar(150) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `food_id`, `food_name`, `quantity`, `price`, `subtotal`) VALUES
(1, 1, 7, 'Signature Gourmet Smash Burger', 1, 890.00, 890.00),
(2, 1, 19, 'Loaded Volcano Fries', 1, 580.00, 580.00),
(3, 2, 16, 'Royal Mutton Dum Biryani', 1, 1190.00, 1190.00),
(4, 2, 4, 'Chicken Malai Boti', 1, 890.00, 890.00),
(5, 2, 21, 'Fresh Mint Lemonade Cooler', 1, 280.00, 280.00),
(6, 3, 6, 'Mutton Chops Grill', 1, 1650.00, 1650.00),
(7, 4, 2, 'Stuffed Chicken Strips', 1, 750.00, 750.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `guests` int(11) NOT NULL DEFAULT 2,
  `special_request` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `customer_name`, `email`, `phone`, `reservation_date`, `reservation_time`, `guests`, `special_request`, `status`, `created_at`) VALUES
(1, 2, 'Ahmed Khan', 'ahmed@example.com', '03001234567', '2026-10-07', '20:00:00', 4, 'Family gathering, quiet window table requested.', 'Confirmed', '2026-10-06 10:42:32'),
(2, 3, 'Fatima Noor', 'fatima@example.com', '03129876543', '2026-10-08', '19:30:00', 2, 'Anniversary surprise setup if possible.', 'Pending', '2026-10-06 10:42:32'),
(3, 4, 'daniyalbaig', 'daniyalbaig@gmail.com', '03578754587', '2026-11-06', '20:00:00', 6, 'birthday', 'Pending', '2026-10-06 11:12:33'),
(4, NULL, 'daniyalbaig', 'daniyalbaig@gmail.com', '03578754587', '2026-11-06', '20:00:00', 6, 'birthday', 'Confirmed', '2026-10-06 11:14:34'),
(5, 1, 'ayesha noor', 'ayeshanoor@gmail.com', '01654858687', '2026-10-08', '21:00:00', 3, 'special dinner', 'Confirmed', '2026-10-06 18:13:02');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `food_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 5,
  `review` text NOT NULL,
  `status` enum('pending','approved','hidden') NOT NULL DEFAULT 'approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `food_id`, `rating`, `review`, `status`, `created_at`) VALUES
(1, 2, 7, 5, 'The Signature Gourmet Smash Burger is simply out of this world! Juicy, perfectly seasoned, and the truffle mayo adds a luxurious touch. Highly recommended!', 'approved', '2026-10-04 10:42:32'),
(2, 3, 16, 5, 'Royal Mutton Dum Biryani reminded me of pure traditional royal feasts. Fragrant basmati and tender meat. Fast delivery as well!', 'approved', '2026-10-05 10:42:32'),
(3, 2, 4, 5, 'Chicken Malai Boti was creamy and melted right away. Excellent quality and presentation.', 'approved', '2026-10-05 22:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `restaurant_name` varchar(150) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `whatsapp` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `opening_hours` varchar(255) DEFAULT NULL,
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 150.00,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `currency_symbol` varchar(10) NOT NULL DEFAULT 'Rs. '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `restaurant_name`, `logo`, `phone`, `email`, `whatsapp`, `address`, `opening_hours`, `delivery_fee`, `facebook`, `instagram`, `website`, `currency_symbol`) VALUES
(1, 'Saffron & Spice Gourmet Restaurant', 'logo.png', '+92 300 1234567', 'info@saffronspice.com', '923213098649', 'Plot 42, Food Street, Block 7, Clifton, Karachi, Pakistan', 'Mon - Sun: 12:00 PM - 01:00 AM', 150.00, 'https://facebook.com/saffronspice', 'https://instagram.com/saffronspice', 'http://localhost/MS-RESTURANT', 'Rs. ');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `address`, `role`, `status`, `created_at`) VALUES
(1, 'Restaurant Admin', 'admin@restaurant.com', '+92 300 0000000', '$2y$10$u3FsVr7FbgepOsKxqkhmOu/E4lkTzXpKQv/5XqvIzPx4/M0IARMTe', 'Restaurant Management Office, Karachi', 'admin', 'active', '2026-10-06 10:42:32'),
(2, 'Ahmed Khan', 'ahmed@example.com', '03001234567', '$2y$10$VEh.2608HAqbYCiBSAIq..Qi4rmhoLL5C.oIzeuQaajb0Cnu79CTa', 'House 14-B, Sector 11-A, North Karachi', 'customer', 'active', '2026-10-06 10:42:32'),
(3, 'Fatima Noor', 'fatima@example.com', '03129876543', '$2y$10$VEh.2608HAqbYCiBSAIq..Qi4rmhoLL5C.oIzeuQaajb0Cnu79CTa', 'Apartment 402, Gulshan-e-Iqbal Block 13, Karachi', 'customer', 'active', '2026-10-06 10:42:32'),
(4, 'daniyalbaig', 'daniyalbaig@gmail.com', '03578754587', '$2y$10$6NArPEdkZEE0Ju8E2QDtUexrw7/4zw49uANiNT0pEvuRK67pgXAdy', 'House 12-A, Street 7, Block 5, Clifton, Karachi 75600, Pakistan', 'customer', 'active', '2026-10-06 11:10:55'),
(5, 'ayeshanoor', 'ayeshanoor@gmail.com', '03264987646', '$2y$10$K60BvpdR2iylrMqfPBAZUutriIQ5yBvO.MJg1wH6ovjGgpBWszmQq', 'chemomarket', 'customer', 'active', '2026-10-06 18:08:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_foods_category` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_items_order` (`order_id`),
  ADD KEY `fk_order_items_food` (`food_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reservations_user` (`user_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_user` (`user_id`),
  ADD KEY `fk_reviews_food` (`food_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `foods`
--
ALTER TABLE `foods`
  ADD CONSTRAINT `fk_foods_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `fk_reservations_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_food` FOREIGN KEY (`food_id`) REFERENCES `foods` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
