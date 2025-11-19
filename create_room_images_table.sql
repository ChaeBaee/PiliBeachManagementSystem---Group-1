-- Create room_images table for storing multiple images per room
CREATE TABLE `room_images` (
  `image_id` int(10) NOT NULL AUTO_INCREMENT,
  `room_id` int(10) NOT NULL,
  `image_filename` varchar(255) NOT NULL,
  `image_title` varchar(255) DEFAULT NULL,
  `image_description` text DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `display_order` int(10) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`image_id`),
  KEY `room_id` (`room_id`),
  CONSTRAINT `fk_room_images_room` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`room_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample images for existing rooms (using existing room images)
INSERT INTO `room_images` (`room_id`, `image_filename`, `image_title`, `is_primary`, `display_order`) VALUES
(1, 'r1.jpg', 'Deluxe Room - Main View', 1, 1),
(1, 'r2.jpg', 'Deluxe Room - Bathroom', 0, 2),
(1, 'r3.jpg', 'Deluxe Room - Bed Area', 0, 3),
(2, 'r2.jpg', 'Double Room - Main View', 1, 1),
(2, 'r4.jpg', 'Double Room - Seating Area', 0, 2),
(2, 'r5.jpg', 'Double Room - Balcony', 0, 3),
(3, 'r3.jpg', 'Deluxe Room - Main View', 1, 1),
(3, 'r6.jpg', 'Deluxe Room - Bathroom', 0, 2),
(4, 'r4.jpg', 'Single Room - Main View', 1, 1),
(4, 'r7.jpg', 'Single Room - Work Area', 0, 2),
(5, 'r5.jpg', 'Double Room - Main View', 1, 1),
(5, 'r8.jpg', 'Double Room - Bathroom', 0, 2),
(6, 'r6.jpg', 'Standard Room - Main View', 1, 1),
(6, 'r9.jpg', 'Standard Room - Seating', 0, 2),
(7, 'r7.jpg', 'Standard Room - Main View', 1, 1),
(7, 'r1.jpg', 'Standard Room - Bathroom', 0, 2),
(8, 'r8.jpg', 'Deluxe Room - Main View', 1, 1),
(8, 'r2.jpg', 'Deluxe Room - Balcony', 0, 2),
(9, 'r9.jpg', 'Double Room - Main View', 1, 1),
(9, 'r3.jpg', 'Double Room - Seating', 0, 2);

-- Add some additional sample images for variety
INSERT INTO `room_images` (`room_id`, `image_filename`, `image_title`, `is_primary`, `display_order`) VALUES
(1, 'home.jpg', 'Deluxe Room - Beach View', 0, 4),
(1, 'pili-beach-agmanic.jpg', 'Deluxe Room - Ocean View', 0, 5),
(2, 'home.jpg', 'Double Room - Beach Access', 0, 4),
(3, 'pili-beach-agmanic.jpg', 'Deluxe Room - Sunset View', 0, 3),
(5, 'home.jpg', 'Double Room - Garden View', 0, 3),
(6, 'pili-beach-agmanic.jpg', 'Standard Room - Pool View', 0, 3);

-- Add caption.jpg as an example image for room 1001 (room_id=1)
INSERT INTO `room_images` (`room_id`, `image_filename`, `image_title`, `is_primary`, `display_order`) VALUES
(1, 'caption.jpg', 'Sample Caption Image', 0, 99); 