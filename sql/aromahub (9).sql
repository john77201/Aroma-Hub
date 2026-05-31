-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 10, 2026 at 07:01 AM
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
-- Database: `aromahub`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminusername` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminusername`, `password`) VALUES
('admin@spicesstore.com', '12345');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE `blog` (
  `blog_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'Admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog`
--

INSERT INTO `blog` (`blog_id`, `title`, `content`, `image`, `author`, `created_at`, `status`) VALUES
(8, 'Cardamom – The Queen of Spices and Its Incredible Benefits', 'Cardamom, often celebrated as the Queen of Spices, is one of the most luxurious and enchanting ingredients used in kitchens across the world. Its naturally sweet, floral fragrance brings instant sophistication to any dish, whether it’s a creamy dessert, a fragrant biryani, or a warm cup of chai. Cardamom has been treasured for centuries not only for its rich aroma but also for its remarkable healing properties. It supports digestion, freshens breath, and helps cleanse toxins from the body. Many cultures use cardamom as a natural remedy for acidity, bloating, and nausea, and its antioxidant-rich nature makes it a powerful immunity booster.\r\n\r\nIn cooking, cardamom is incredibly versatile. The whole pods are used to enhance biryanis, kheer, payasam, and curries, while the seeds and powder are favorites in baking and sweet preparations. Its ability to elevate both sweet and savory dishes is unmatched. To retain its fragrance, cardamom must be stored carefully and sourced from the right regions. At Aroma Hub, we provide premium-quality, handpicked cardamom pods known for their plumpness, bright green color, and unforgettable aroma. Every pack is sealed to preserve natural oils, ensuring maximum freshness in every dish you prepare.', 'cardamom blog.webp', 'Admin', '2025-11-15 04:46:33', 0),
(9, 'Black Pepper – The King of Spices and Its Bold Strength', 'Black pepper, widely known as the King of Spices, is one of the most ancient and powerful ingredients in global cuisine. Its sharp, fiery flavor has the ability to transform a simple dish into something exciting and aromatic. But beyond its culinary importance, black pepper is a nutritional powerhouse. It contains piperine, a natural compound that improves digestion, boosts metabolism, and enhances the absorption of essential nutrients in the body. This makes black pepper especially valuable in Ayurvedic remedies and modern wellness practices.\r\n\r\nIn everyday cooking, black pepper is found everywhere—from tadka and marination to soups, salads, and continental dishes. Whole peppercorns offer deep, lingering heat, while crushed pepper adds an instant punch of flavor. Black pepper is also a key ingredient in herbal drinks and immunity boosters. High-quality pepper is rich in essential oils and has a robust aroma that cannot be replaced by inferior varieties. At Aroma Hub, we source peppercorns from regions known for producing the boldest, oil-rich variety, ensuring that every spoonful carries intense aroma and authentic spice strength.', 'pepper blog.jpg', 'Admin', '2025-11-15 05:01:56', 0),
(10, 'Cloves – Fragrant Flower Buds with Ancient Healing Power', 'Cloves are tiny, aromatic flower buds with an extraordinary ability to enhance both flavor and health. They bring a warm, slightly sweet, and deeply comforting aroma to foods and drinks. For centuries, cloves have been used in traditional medicine due to their powerful essential oil, eugenol, which is known for its antibacterial and anti-inflammatory effects. Whether used to soothe toothache, relieve cold symptoms, or support digestion, cloves remain one of the most important wellness spices in Indian households.\r\n\r\nIn cooking, cloves are an essential element of biryani, masala chai, garam masala, curries, and desserts. Their strong aroma blends beautifully with sweet, savory, and tangy dishes alike. Because cloves are so potent, even a small quantity can significantly influence the flavor of a recipe. To preserve their natural oils, cloves must be sun-dried and stored properly. Aroma Hub offers premium-grade cloves that are hand-selected for size, color, and oil content, ensuring that every bud delivers rich aroma and long-lasting freshness.', 'cloves blog.jpg', 'Admin', '2025-11-15 05:05:40', 0),
(11, 'Cinnamon – A Sweet Spice with Comforting Warmth and Wellness', 'Cinnamon is one of the world’s most cherished spices, known for its warm sweetness and inviting fragrance. Its comforting aroma brings depth to desserts, richness to curries, and warmth to beverages like chai and cinnamon tea. Beyond its culinary charm, cinnamon carries impressive health benefits. It is widely recognized for supporting blood sugar control, boosting immunity, and helping the body fight inflammation. Its natural antimicrobial properties have made it a valued ingredient in wellness remedies for generations.\r\n\r\nCinnamon sticks, especially the premium rolled ones, have a strong essential oil presence and a unique flavor that enhances everything from biryani to baked goods. Whether added to rice dishes, infused in warm drinks, or mixed into spice blends, cinnamon brings a beautiful balance of sweetness and spice. Aroma Hub offers high-quality cinnamon sticks with a naturally rich aroma and deep color, ensuring that each piece elevates both flavor and health in your recipes.', 'cinnamon blog.jpg', 'Admin', '2025-11-15 05:07:40', 0),
(12, 'Turmeric – The Golden Spice That Nourishes Body and Mind', 'Turmeric, the golden treasure of Indian kitchens, is more than just a spice that adds color—it is a powerful healing ingredient deeply rooted in Ayurveda. Its vibrant hue comes from curcumin, a natural compound known for its anti-inflammatory and antioxidant properties. Turmeric supports immunity, improves skin health, aids digestion, and helps detoxify the body. Whether consumed as golden milk, used in herbal remedies, or added to everyday cooking, turmeric is essential for both flavor and wellness.\r\n\r\nIn the kitchen, turmeric forms the foundation of most Indian dishes. It brightens gravies, enriches rice preparations, and gives pickles their signature golden tint. The aroma and strength of turmeric depend on the quality of the raw roots and the processing method. Aroma Hub’s turmeric powder is made from carefully selected roots, finely ground to preserve color and freshness. Free from additives and chemicals, our turmeric offers authentic taste and natural health benefits with every use.', 'turmeric blog.jpg', 'Admin', '2025-11-15 05:10:41', 0),
(13, 'Cumin – The Earthy Spice That Defines Indian Flavor', 'Cumin seeds, or jeera, are one of the most essential spices in Indian cooking. Known for their warm, earthy aroma, cumin seeds bring character and depth to countless dishes. When added to hot oil, they crackle and release an unmistakable fragrance that forms the base of tadkas, curries, dals, and chutneys. Cumin has been used for centuries for its digestive benefits, and many households rely on jeera water for detoxification and metabolism support.\r\n\r\nApart from Indian cuisine, cumin is widely used in Middle Eastern, Mexican, and Mediterranean dishes. Its ability to blend with both mild and strong spices makes it a kitchen essential. Fresh, high-quality cumin seeds have a strong aroma and uniform brown color. At Aroma Hub, our cumin seeds are sourced from trusted farms and packed with care to preserve their natural oils and flavor. Each batch is cleaned thoroughly, ensuring purity and exceptional quality for your everyday cooking.', 'Cumin blog.webp', 'Admin', '2025-11-15 05:13:38', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `orderno` varchar(150) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `product_id`, `userid`, `quantity`, `rate`, `total`, `orderno`, `status`) VALUES
(23, 42, 20, 1, 499, 499, 'AROMAHUB753541', 2),
(26, 42, 22, 1, 499, 499, 'AROMAHUB631389', 2),
(27, 41, 22, 1, 1199, 1199, 'AROMAHUB557412', 2),
(28, 41, 22, 1, 1199, 1199, 'AROMAHUB896094', 2),
(29, 42, 22, 1, 499, 499, 'AROMAHUB896094', 2),
(30, 46, 22, 1, 799, 799, 'AROMAHUB856951', 1),
(31, 46, 22, 1, 799, 799, 'AROMAHUB856951', 1),
(32, 46, 22, 1, 799, 799, 'AROMAHUB284554', 2),
(33, 42, 22, 1, 499, 499, 'AROMAHUB959699', 2),
(34, 50, 22, 17, 1950, 33150, 'AROMAHUB432677', 2),
(39, 42, 22, 1, 499, 499, 'AROMAHUB267915', 2),
(40, 41, 22, 1, 1199, 1199, 'AROMAHUB367700', 2),
(41, 46, 20, 1, 799, 799, 'AROMAHUB753541', 2),
(42, 42, 20, 1, 499, 499, 'AROMAHUB833423', 2),
(43, 71, 20, 2, 550, 1100, 'AROMAHUB908213', 2),
(44, 62, 24, 2, 400, 800, 'AROMAHUB454879', 2),
(45, 62, 20, 2, 400, 800, 'AROMAHUB536675', 2);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `nm` varchar(50) NOT NULL,
  `email` text NOT NULL,
  `sub` text NOT NULL,
  `msg` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `nm`, `email`, `sub`, `msg`) VALUES
(1, 'john', 'johnabraham456@gmail.com', 'fhgfhghch', 'hai'),
(2, 'john', 'johnabraham456@gmail.com', 'fhgfhghch', 'hai'),
(3, 'john', 'johnabraham456@gmail.com', 'fhgfhghch', 'hai'),
(4, 'john', 'johnabraham90856@gmail.com', 'fhgfhghch', 'hello world');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orderid` int(11) NOT NULL,
  `dt` varchar(50) NOT NULL,
  `orderno` varchar(50) NOT NULL,
  `userid` int(11) NOT NULL,
  `address` text NOT NULL,
  `town` text NOT NULL,
  `state` text NOT NULL,
  `postcode` varchar(6) NOT NULL,
  `status` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orderid`, `dt`, `orderno`, `userid`, `address`, `town`, `state`, `postcode`, `status`, `total_amount`) VALUES
(1, '29/09/2025', 'AROMAHUB856951', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 0, 1598.00),
(2, '29/09/2025', 'AROMAHUB284554', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 4, 799.00),
(3, '29/09/2025', 'AROMAHUB959699', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 100, 499.00),
(4, '30/09/2025', 'AROMAHUB432677', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 4, 33150.00),
(5, '02/10/2025', 'AROMAHUB721499', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 0, 661.50),
(6, '02/10/2025', 'AROMAHUB331665', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 5, 556.50),
(7, '02/10/2025', 'AROMAHUB489999', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 5, 444.00),
(8, '02/10/2025', 'AROMAHUB267915', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 5, 1090.00),
(9, '02/10/2025', 'AROMAHUB669914', 22, 'njljl nb,mnbm', 'nettithozhu', 'kerala', '685551', 4, 486.00),
(10, '02/10/2025', 'AROMAHUB367700', 22, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 4, 1199.00),
(11, '15/10/2025', 'AROMAHUB753541', 20, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 1, 1298.00),
(12, '15/10/2025', 'AROMAHUB833423', 20, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'kerala', '685551', 4, 499.00),
(13, '17/11/2025', 'AROMAHUB908213', 20, 'njljl nb,mnbm', 'nettithozhu', 'kerala', '685551', 4, 1100.00),
(14, '18/11/2025', 'AROMAHUB454879', 24, 'elavukaduppil house nettithozhu po nettithozhu', 'nettithozhu', 'Kerala', '685551', 4, 800.00),
(15, '23/11/2025', 'AROMAHUB536675', 20, 'njljl nb,mnbm', 'nettithozhu', 'kerala', '685551', 4, 800.00);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(70) NOT NULL,
  `product_description` text NOT NULL,
  `category` varchar(50) NOT NULL,
  `product_quantity` int(11) NOT NULL,
  `quality` varchar(50) NOT NULL,
  `product_price` decimal(50,0) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `product_image` varchar(300) NOT NULL,
  `stock_status` varchar(50) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `category`, `product_quantity`, `quality`, `product_price`, `discounted_price`, `product_image`, `stock_status`, `status`) VALUES
(41, 'Cardamom (500 g)', 'Cardamom is a fragrant spice known for its sweet, warm, and slightly citrusy flavor. Often called the “Queen of Spices,” it enhances both sweet and savory dishes and is prized for its rich aroma and digestive benefits.', 'Spices', 1, '', 1499, 1199.00, 'cardamom1.png', 'In Stock', 0),
(42, 'Black Pepper (500g)', 'Black pepper is a bold and aromatic spice with a sharp, pungent flavor. Known as the “King of Spices,” it adds warmth and depth to dishes while also aiding digestion and boosting flavor naturally.', 'Spices', 100, '', 699, 499.00, '1.png', 'In Stock', 0),
(43, 'Bay Leaf (100g)', 'Bay leaf is a fragrant leaf spice with a subtle, earthy aroma and slightly bitter taste. It enhances soups, stews, and curries by adding depth and richness to their flavor.', 'Spices', 1, '', 129, 99.00, 'bayleaf1.png', 'In Stock', 0),
(44, 'Cinnamon Stick (250g)', 'Cinnamon stick is a warm, sweet spice made from the dried bark of the cinnamon tree, often used to infuse rich flavor into teas, desserts, and curries.', 'Spices', 1, '', 199, 149.00, 'cinnamonstick1.png', 'In Stock', 0),
(45, 'Cinnamon Rolls (250g)', 'Cinnamon roll is a sweet, spiral-shaped pastry filled with cinnamon and sugar, offering a soft, buttery texture and a comforting aroma.', 'Spices', 1, '', 249, 199.00, 'cinnamonroll1.png', 'In Stock', 0),
(46, 'Cloves (500g)', 'Cloves are aromatic flower buds with a warm, sweet, and slightly bitter flavor. They are used to add depth and richness to curries, baked goods, and beverages.', 'Spices', 1, '', 1399, 799.00, 'cloves1.png', 'In Stock', 0),
(47, '7 in 1 Spices Gift Box', 'A delightful assortment of handpicked spices, elegantly packed to bring vibrant flavors and a touch of tradition — an ideal gift for any occasion.', 'Giftbox', 1, '', 1499, 999.00, 'WhatsApp Image 2025-09-16 at 11.07.35 AM.jpeg', 'In Stock', 0),
(48, '4 In 1 Spices Gift Box', 'An exquisite blend of authentic spices, neatly packaged to elevate every meal — a flavorful and memorable gift for your loved ones.', 'Giftbox', 1, '', 999, 699.00, 'WhatsApp Image 2025-09-16 at 11.11.06 AM.jpeg', 'In Stock', 0),
(49, '7 in 1 Premium Gift Box', 'A luxurious collection of 7 handpicked premium spices, beautifully packed in one elegant box — perfect for gifting rich flavors and culinary delight.', 'Giftbox', 1, '', 1799, 1199.00, '1759320760_68dd1ab8985bd.jpg', 'In Stock', 0),
(50, 'Cardamom', 'Premium quality green cardamom sourced directly from farms, available in bulk for wholesale and large orders. Fresh aroma, rich flavor – perfect for culinary, confectionery, and beverage industries.', 'Bulk ', 10, 'Premium', 2200, 1950.00, 'cardamom.jpg', 'In Stock', 0),
(52, 'giftbox', 'khbwjhe', 'Gift Box', 15, '', 1499, 999.00, 'gift box - Copy.webp', 'In Stock', 0),
(54, 'cardamom', 'custom', 'Custom', 5000, '', 3, 2.00, 'custom_68dd38102ca5b7.50012334.png', '', 0),
(55, 'Black Pepper ', 'black', 'Custom', 5000, '', 1, 0.70, 'custom_68dd3e594862f3.66068016.png', 'In Stock', 0),
(56, 'clove', 'cloves', 'Custom', 5000, '', 1, 0.75, 'custom_68de201d5d6fa0.14617405.png', '', 0),
(57, 'Black Pepper', 'black', 'Bulk', 1, 'A Grade', 900, 800.00, '1759392432_pepper.webp', 'In Stock', 0),
(58, 'cinnamon', 'custom', 'Custom', 5000, '', 1, 0.42, 'custom_68de34f747db04.36474938.png', '', 0),
(60, 'Kismis/Raisins (250gm)', 'Kismis, also known as raisins, are naturally sweet dried grapes packed with energy, fiber, and essential nutrients. They add a delicious flavor to desserts, breakfast bowls, biryanis, and snacks. Rich in iron and antioxidants, kismis supports healthy digestion, boosts energy, and promotes overall wellness, making them a perfect daily superfood.', 'Dry Fruits', 100, '', 180, 160.00, 'raisin1.jpg', '', 0),
(61, 'Badam/Almond (250gm)', 'Badam, or almonds, are nutrient-rich nuts known for their crunchy texture and natural sweetness. Packed with protein, healthy fats, vitamins, and antioxidants, they help boost memory, support heart health, and provide long-lasting energy. Perfect for snacking or adding to desserts and dishes.', 'Dry Fruits', 100, '', 600, 300.00, 'badam1.jpg', '', 0),
(62, 'Cashew Nut (250gm)', 'Cashew nuts are creamy, buttery nuts loved for their rich taste and smooth texture. Packed with healthy fats, protein, vitamins, and minerals, they support heart health, boost energy, and make a perfect snack or addition to sweets and gourmet dishes.', 'Dry Fruits', 100, '', 490, 400.00, 'cashew1.jpg', '', 0),
(63, 'Dates (250gm)', 'Dates are naturally sweet, energy-rich fruits known for their soft texture and caramel-like flavor. Packed with fiber, iron, and essential minerals, they support digestion, boost energy, and make a healthy alternative to refined sugar. Perfect for snacking or adding to desserts and smoothies.', 'Dry Fruits', 100, '', 500, 400.00, 'dates1.jpg', '', 0),
(64, 'Walnut (250gm)', 'Walnuts are nutrient-dense nuts with a rich, earthy flavor and crunchy texture. Loaded with omega-3 fatty acids, antioxidants, and essential vitamins, they support brain health, improve heart function, and provide natural energy. Ideal for snacking or adding to salads, desserts, and breakfast bowls.', 'Dry Fruits', 100, '', 600, 450.00, 'walnut1.jpg', '', 0),
(65, 'Dried Strawberry (100gm)', 'Dried strawberries are sweet, tangy, and naturally flavorful treats made from fresh strawberries. Rich in antioxidants, vitamins, and natural fiber, they offer a delicious burst of fruity taste and make a perfect snack or topping for cereals, desserts, and smoothies.', 'Dry Fruits', 100, '', 280, 180.00, 'straw1.jpg', '', 0),
(66, 'Dried Kiwi (100gm)', 'Dried kiwi offers a vibrant sweet–tangy flavor with a chewy texture. Packed with vitamin C, antioxidants, and natural fiber, it supports immunity, digestion, and overall wellness. A perfect fruity snack or colorful addition to desserts and trail mixes.', 'Dry Fruits', 100, '', 250, 180.00, 'kiwi1.jpg', '', 0),
(67, 'Red Chilli Powder (250gm)', 'Red chilli powder is a vibrant, fiery spice made from dried red chillies, known for adding heat, color, and rich flavor to dishes. It enhances curries, marinades, and everyday cooking while providing antioxidants that support metabolism and overall health.', 'Blended Spices', 100, '', 160, 120.00, 'chilli1.jpg', 'In Stock', 0),
(68, 'Garam Masala Powder (100gm)', 'Garam masala powder is a fragrant, warm spice blend made from premium spices like cardamom, cinnamon, cloves, and pepper. It adds depth, aroma, and rich flavor to curries, sabzis, and gravies, making every dish more authentic and flavorful.', 'Blended Spices', 100, '', 150, 120.00, 'garam1.jpg', '', 0),
(69, 'Coriander Powder (250gm)', 'A fragrant spice made from finely ground coriander seeds, known for its warm, citrusy flavor. Commonly used in Indian, Middle Eastern, and Asian cuisines to enhance curries, soups, and marinades.', 'Blended Spices', 100, '', 200, 110.00, 'coria1.jpg', 'In Stock', 0),
(70, 'Black Pepper Powder (100gm)', 'A pungent and aromatic spice made from finely ground black peppercorns, adding a sharp, spicy flavor to dishes. Widely used in cooking, seasoning, and marinades across global cuisines.', 'Powders', 100, '', 180, 150.00, 'pep1.jpg', '', 0),
(71, 'Cardamom Powder (100gm)', 'A fragrant spice made from ground cardamom pods, offering a warm, sweet, and slightly citrusy flavor. It is commonly used to enhance the taste of desserts, beverages, and savory dishes.', 'Powders', 100, '', 700, 550.00, 'carda1.jpg', '', 0),
(72, 'Cinnamon Powder (100gm)', 'A warm and aromatic spice made from finely ground cinnamon bark, known for its sweet and slightly spicy flavor. Commonly used in baking, desserts, beverages, and savory dishes.', 'Powders', 100, '', 150, 120.00, 'cinna1.jpg', '', 0),
(73, 'cardamomorganic', 'Queen of spices', 'Spices', 100, '', 250, 199.00, '3.png', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `image_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`image_id`, `product_id`, `image_name`) VALUES
(20, 41, '1757815551_2.png'),
(21, 41, '1757815551_3.png'),
(22, 41, '1757815551_4.png'),
(23, 41, '1757815551_5.webp'),
(24, 42, '1757815753_2.png'),
(25, 42, '1757815753_3.png'),
(26, 42, '1757815753_4.png'),
(27, 42, '1757815753_5.webp'),
(28, 43, '1757817240_2.png'),
(29, 43, '1757817240_3.png'),
(30, 43, '1757817240_4.png'),
(31, 43, '1757817240_5.webp'),
(32, 44, '1757817468_2.png'),
(33, 44, '1757817468_3.png'),
(34, 44, '1757817468_4.png'),
(35, 44, '1757817468_5.webp'),
(36, 45, '1757817653_2.png'),
(37, 45, '1757817653_3.png'),
(38, 45, '1757817653_4.png'),
(39, 45, '1757817653_5.png'),
(40, 46, '1757817839_2.png'),
(41, 46, '1757817839_3.webp'),
(42, 47, '1758001345_WhatsApp Image 2025-09-16 at 11.07.37 AM.jpeg'),
(43, 47, '1758001345_WhatsApp Image 2025-09-16 at 11.07.36 AM (1).jpeg'),
(44, 47, '1758001345_WhatsApp Image 2025-09-16 at 11.07.36 AM.jpeg'),
(45, 48, '1758001488_WhatsApp Image 2025-09-16 at 11.11.07 AM (1).jpeg'),
(46, 48, '1758001488_WhatsApp Image 2025-09-16 at 11.11.07 AM.jpeg'),
(70, 49, '1759324144_0_68dd27f02c6c6.webp'),
(71, 49, '1759324144_1_68dd27f02d772.webp'),
(72, 49, '1759324144_2_68dd27f02e2a8.webp'),
(73, 50, '68de2f1563553-2.png'),
(74, 55, '1759391839_0_68de305f25b54.png'),
(75, 55, '1759391839_2_68de305f261b0.png'),
(76, 57, '1759392432_OIP (3).webp'),
(77, 57, '1759392432_pepper.webp'),
(78, 60, '1763395095_raisin2.jpg'),
(79, 60, '1763395095_raisin3.jpg'),
(80, 60, '1763395095_raisin4.jpg'),
(81, 60, '1763395095_raisin5.webp'),
(82, 61, '1763395231_badam2.png'),
(83, 61, '1763395231_badam3.jpg'),
(84, 61, '1763395231_badam4.jpg'),
(85, 61, '1763395231_badam5.jpg'),
(86, 61, '1763395231_badam6.webp'),
(87, 62, '1763395334_cashew2.jpg'),
(88, 62, '1763395334_cashew3.jpg'),
(89, 62, '1763395334_cashew4.jpg'),
(90, 62, '1763395334_cashew5.jpg'),
(91, 63, '1763395416_dates2.jpg'),
(92, 63, '1763395416_dates3.jpg'),
(93, 63, '1763395416_dates4.jpg'),
(94, 63, '1763395416_dates5.webp'),
(95, 64, '1763395507_walnut2.jpg'),
(96, 64, '1763395507_walnut3.jpg'),
(97, 64, '1763395507_walnut4.jpg'),
(98, 64, '1763395507_walnut5.webp'),
(99, 65, '1763395662_straw2.jpg'),
(100, 65, '1763395662_straw3.jpg'),
(101, 65, '1763395662_straw4.jpg'),
(102, 65, '1763395662_straw5.webp'),
(103, 66, '1763395765_kiwi2.jpg'),
(104, 66, '1763395765_kiwi3.jpg'),
(105, 66, '1763395765_kiwi4.jpg'),
(106, 66, '1763395765_kiwi5.jpg'),
(107, 66, '1763395765_kiwi6.webp'),
(108, 67, '1763395927_chilli2.jpg'),
(109, 67, '1763395927_chilli3.jpg'),
(110, 67, '1763395927_chilli4.jpg'),
(111, 67, '1763395927_chilli5.jpg'),
(112, 68, '1763396084_garam2.jpg'),
(113, 68, '1763396084_garam3.jpg'),
(114, 68, '1763396084_garam4.jpg'),
(115, 68, '1763396084_garam5.jpg'),
(116, 69, '1763396328_coria2.jpg'),
(117, 69, '1763396328_coria3.jpg'),
(118, 69, '1763396328_coria4.jpg'),
(119, 69, '1763396328_coria5.jpg'),
(120, 70, '1763396574_pep2.jpg'),
(121, 70, '1763396574_pep3.jpg'),
(122, 70, '1763396574_pep4.jpg'),
(123, 70, '1763396574_pep5.jpg'),
(124, 71, '1763396720_carda2.jpg'),
(125, 71, '1763396720_carda3.jpg'),
(126, 71, '1763396720_carda4.jpg'),
(127, 71, '1763396720_carda5.jpg'),
(128, 72, '1763396872_cinna2.jpg'),
(129, 72, '1763396872_cinna3.jpg'),
(130, 72, '1763396872_cinna4.jpg'),
(131, 72, '1763396872_cinna5.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_no` varchar(50) NOT NULL,
  `rating` tinyint(1) NOT NULL,
  `comment` text DEFAULT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_approved` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `product_id`, `order_no`, `rating`, `comment`, `review_date`, `is_approved`) VALUES
(1, 22, 46, 'AROMAHUB284554', 5, 'very good product', '2025-10-02 10:26:55', 1),
(2, 22, 41, 'AROMAHUB367700', 5, 'nice product', '2025-10-02 18:07:49', 1),
(3, 20, 71, 'AROMAHUB908213', 5, 'Absolutely amazing cardamom powder! The aroma is strong and fresh, and the flavor is perfectly sweet and fragrant. It elevates both my desserts and tea, giving them a rich, authentic taste. High quality and finely ground—definitely worth every penny. Highly recommend!', '2025-11-17 16:33:22', 1),
(4, 24, 62, 'AROMAHUB454879', 5, 'good product', '2025-11-18 04:28:37', 1),
(5, 20, 62, 'AROMAHUB536675', 5, 'nice product', '2025-11-23 12:33:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `phonenumber` varchar(12) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userid`, `username`, `phonenumber`, `email`, `password`, `status`) VALUES
(15, 'John', '6282386042', 'johnabraham5575@gmail.com', '12', 1),
(16, 'John', '6282386042', 'johnabraham5575@gmail.com', '12', 1),
(17, 'John', '6282386042', 'johnabraham5575@mail.com', '12', 1),
(18, 'John Abraham', '6282386042', 'johnabraham4835@gmail.com', 'Jobin12@#', 1),
(19, 'johny', '123456', '12@gmail.com', '12', 1),
(20, 'Aravindh', '6282386042', 'aravindh@gmail.com', 'ara', 1),
(21, 'John', '6282386042', 'johnabraham565@gmail.com', '123', 1),
(22, 'John', '6282386042', 'johnabraham456@gmail.com', '123', 1),
(23, 'ygyt', '6282386042', '43r@g.com', '1', 1),
(24, 'john', '6282386042', 'john55@gmail.com', 'john', 1);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `added_on`) VALUES
(33, 20, 48, '2025-09-16 05:59:29'),
(37, 22, 42, '2025-10-01 08:06:58'),
(38, 22, 43, '2025-10-01 08:07:09'),
(39, 22, 44, '2025-10-01 08:07:19'),
(40, 22, 49, '2025-10-01 12:34:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`blog_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`orderid`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD UNIQUE KEY `uc_user_product_order` (`user_id`,`product_id`,`order_no`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userid`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog`
--
ALTER TABLE `blog`
  MODIFY `blog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `orderid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`userid`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
