-- Create contact_submissions table for storing contact form submissions
CREATE TABLE IF NOT EXISTS `contact_submissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('new','read','replied','archived') NOT NULL DEFAULT 'new',
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `created_at` (`created_at`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample data (optional)
-- INSERT INTO `contact_submissions` (`name`, `email`, `phone`, `subject`, `message`) VALUES
-- ('John Doe', 'john@example.com', '+1234567890', 'General Inquiry', 'I would like to know more about your resort.'),
-- ('Jane Smith', 'jane@example.com', '+0987654321', 'Booking Question', 'What are your check-in and check-out times?'); 