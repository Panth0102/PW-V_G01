-- =====================================================
-- SkillSwap Complete Database Reset Script for MAMP
-- This script will DROP everything and recreate with sample data
-- Optimized for MAMP MySQL configuration
-- =====================================================

-- Set SQL mode for MAMP compatibility
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- Drop existing database if it exists
DROP DATABASE IF EXISTS `skillswap`;

-- Create new database with MAMP-optimized settings
CREATE DATABASE `skillswap` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `skillswap`;

-- =====================================================
-- CREATE TABLES
-- =====================================================

-- Create Users table
CREATE TABLE `Users` (
  `User_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL UNIQUE,
  `Contact_Number` varchar(20) DEFAULT NULL,
  `Location` varchar(100) DEFAULT NULL,
  `Password` varchar(255) NOT NULL,
  `Registration_Date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`User_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Categories table
CREATE TABLE `Categories` (
  `Category_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Category_Name` varchar(50) NOT NULL UNIQUE,
  PRIMARY KEY (`Category_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Skills table (with payment system fields)
CREATE TABLE `Skills` (
  `Skill_ID` int(11) NOT NULL AUTO_INCREMENT,
  `User_ID` int(11) NOT NULL,
  `Skill_Name` varchar(100) NOT NULL,
  `Skill_Type` enum('Offering','Seeking') NOT NULL,
  `Description` text DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT 0.00 COMMENT 'Price in Indian Rupees (INR)',
  `Duration_Hours` int(11) DEFAULT 1 COMMENT 'Course duration in hours',
  `Max_Students` int(11) DEFAULT 10 COMMENT 'Maximum students for this skill',
  `Date_Posted` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Skill_ID`),
  FOREIGN KEY (`User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create SkillCategories table (Junction Table)
CREATE TABLE `SkillCategories` (
  `Skill_ID` int(11) NOT NULL,
  `Category_ID` int(11) NOT NULL,
  PRIMARY KEY (`Skill_ID`, `Category_ID`),
  FOREIGN KEY (`Skill_ID`) REFERENCES `Skills`(`Skill_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Category_ID`) REFERENCES `Categories`(`Category_ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Messages table
CREATE TABLE `Messages` (
  `Message_ID` int(11) NOT NULL AUTO_INCREMENT,
  `From_User_ID` int(11) NOT NULL,
  `To_User_ID` int(11) NOT NULL,
  `Skill_ID` int(11) DEFAULT NULL,
  `Message_Text` text NOT NULL,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Message_ID`),
  FOREIGN KEY (`From_User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`To_User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`Skill_ID`) REFERENCES `Skills`(`Skill_ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Payments table
CREATE TABLE `Payments` (
  `Payment_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Student_User_ID` int(11) NOT NULL COMMENT 'User who is paying',
  `Instructor_User_ID` int(11) NOT NULL COMMENT 'User who will receive payment',
  `Skill_ID` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL COMMENT 'Amount in INR',
  `Payment_Status` enum('Pending','Completed','Failed','Refunded') DEFAULT 'Pending',
  `Payment_Method` varchar(50) DEFAULT 'UPI' COMMENT 'UPI, Card, NetBanking, etc.',
  `Transaction_ID` varchar(100) DEFAULT NULL COMMENT 'Payment gateway transaction ID',
  `Payment_Date` datetime DEFAULT CURRENT_TIMESTAMP,
  `Completion_Date` datetime DEFAULT NULL,
  PRIMARY KEY (`Payment_ID`),
  FOREIGN KEY (`Student_User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`Instructor_User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`Skill_ID`) REFERENCES `Skills`(`Skill_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Enrollments table
CREATE TABLE `Enrollments` (
  `Enrollment_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Student_User_ID` int(11) NOT NULL,
  `Skill_ID` int(11) NOT NULL,
  `Payment_ID` int(11) DEFAULT NULL,
  `Enrollment_Date` datetime DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('Active','Completed','Cancelled') DEFAULT 'Active',
  `Progress` int(11) DEFAULT 0 COMMENT 'Progress percentage 0-100',
  PRIMARY KEY (`Enrollment_ID`),
  UNIQUE KEY `unique_enrollment` (`Student_User_ID`, `Skill_ID`),
  FOREIGN KEY (`Student_User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`Skill_ID`) REFERENCES `Skills`(`Skill_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`Payment_ID`) REFERENCES `Payments`(`Payment_ID`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create Reviews table
CREATE TABLE `Reviews` (
  `Review_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Student_User_ID` int(11) NOT NULL,
  `Skill_ID` int(11) NOT NULL,
  `Rating` int(1) NOT NULL CHECK (Rating >= 1 AND Rating <= 5),
  `Review_Text` text DEFAULT NULL,
  `Review_Date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`Review_ID`),
  UNIQUE KEY `unique_review` (`Student_User_ID`, `Skill_ID`),
  FOREIGN KEY (`Student_User_ID`) REFERENCES `Users`(`User_ID`) ON DELETE CASCADE,
  FOREIGN KEY (`Skill_ID`) REFERENCES `Skills`(`Skill_ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- INSERT SAMPLE DATA
-- =====================================================

-- Insert Users (with plain text passwords for easy testing)
INSERT INTO `Users` (`Name`, `Email`, `Contact_Number`, `Location`, `Password`, `Registration_Date`) VALUES
('Admin User', 'admin@skillswap.com', '+919876543210', 'Mumbai, India', 'admin123', '2024-01-15 10:30:00'),
('Priya Sharma', 'priya.sharma@email.com', '+919123456789', 'Delhi, India', 'password', '2024-01-20 14:45:00'),
('Rahul Kumar', 'rahul.kumar@email.com', '+918765432109', 'Bangalore, India', 'password', '2024-02-01 09:15:00'),
('Anita Patel', 'anita.patel@email.com', '+917654321098', 'Pune, India', 'password', '2024-02-10 16:20:00'),
('Vikram Singh', 'vikram.singh@email.com', '+916543210987', 'Chennai, India', 'password', '2024-02-15 11:00:00'),
('Neha Gupta', 'neha.gupta@email.com', '+915432109876', 'Hyderabad, India', 'password', '2024-02-20 12:30:00');

-- Insert Categories
INSERT INTO `Categories` (`Category_Name`) VALUES
('Programming'),
('Design'),
('Writing'),
('Music'),
('Cooking'),
('Language'),
('Fitness'),
('Art'),
('Photography'),
('Business');

-- Insert Skills with Indian pricing
INSERT INTO `Skills` (`User_ID`, `Skill_Name`, `Skill_Type`, `Description`, `Price`, `Duration_Hours`, `Max_Students`, `Date_Posted`) VALUES
(1, 'Complete Web Development Course', 'Offering', 'Learn HTML, CSS, JavaScript, PHP, and MySQL from scratch. Build real-world projects and become a full-stack developer. Perfect for beginners and intermediate learners.', 2999.00, 40, 15, '2024-01-16 08:00:00'),
(1, 'Digital Marketing Mastery', 'Offering', 'Master SEO, Social Media Marketing, Google Ads, and Content Marketing. Grow your business online with proven strategies and hands-on practice.', 1999.00, 25, 20, '2024-01-17 10:00:00'),
(2, 'Professional Graphic Design', 'Offering', 'Learn Adobe Photoshop, Illustrator, and InDesign. Create stunning logos, branding materials, and marketing designs. Industry-standard techniques included.', 1799.00, 30, 12, '2024-01-21 13:30:00'),
(3, 'Content Writing Expertise', 'Seeking', 'Looking for an experienced content writer to help with blog writing and SEO content creation for my tech startup. Need engaging, conversion-focused content.', 0.00, 0, 0, '2024-02-02 10:45:00'),
(4, 'Guitar Mastery Course', 'Offering', 'Complete guitar course from beginner to advanced. Learn acoustic and electric guitar, music theory, chord progressions, and popular songs. Individual attention guaranteed.', 1499.00, 20, 8, '2024-02-11 15:20:00'),
(5, 'Indian Cooking Masterclass', 'Offering', 'Learn authentic Indian recipes, spices, and cooking techniques. From basic curries to advanced dishes. Includes North Indian, South Indian, and street food recipes.', 999.00, 12, 6, '2024-02-16 12:00:00'),
(6, 'Yoga and Meditation', 'Offering', 'Complete yoga course covering asanas, pranayama, and meditation techniques for mind and body wellness. Suitable for all fitness levels.', 1299.00, 15, 25, '2024-02-20 14:30:00'),
(2, 'Photography Fundamentals', 'Offering', 'Learn DSLR photography, composition, lighting, and photo editing. Perfect for beginners wanting to master photography skills.', 1599.00, 18, 10, '2024-02-22 16:00:00'),
(3, 'Business Strategy Course', 'Offering', 'Learn startup fundamentals, business planning, market analysis, and growth strategies. Real-world case studies included.', 2499.00, 35, 15, '2024-02-25 11:00:00'),
(4, 'English Speaking Course', 'Offering', 'Improve your English speaking skills, pronunciation, and confidence. Perfect for professionals and students.', 1199.00, 20, 20, '2024-02-28 09:30:00');

-- Insert SkillCategories (linking skills to categories)
INSERT INTO `SkillCategories` (`Skill_ID`, `Category_ID`) VALUES
(1, 1), -- Web Development -> Programming
(2, 10), -- Digital Marketing -> Business
(3, 2), -- Graphic Design -> Design
(4, 3), -- Content Writing -> Writing
(5, 4), -- Guitar Lessons -> Music
(6, 5), -- Indian Cooking -> Cooking
(7, 7), -- Yoga -> Fitness
(8, 9), -- Photography -> Photography
(9, 10), -- Business Strategy -> Business
(10, 6); -- English Speaking -> Language

-- Insert Messages
INSERT INTO `Messages` (`From_User_ID`, `To_User_ID`, `Skill_ID`, `Message_Text`, `Timestamp`) VALUES
(2, 1, 1, 'Hi! I saw your web development course. I need help with a website project. Are you available for consultation?', '2024-01-17 09:30:00'),
(3, 1, 2, 'Hello! Your digital marketing course looks comprehensive. I have a startup and need marketing guidance. What are your rates?', '2024-01-22 14:15:00'),
(4, 2, 3, 'Hi Priya! I can help you with content writing. I have experience in tech writing and SEO. Let\'s discuss your needs.', '2024-02-03 11:20:00'),
(5, 4, 5, 'Anita, I\'m interested in guitar lessons! I\'m a complete beginner. What\'s your teaching schedule like?', '2024-02-12 16:45:00'),
(6, 5, 6, 'Vikram! Your Indian cooking masterclass sounds amazing. I\'d love to learn authentic recipes. When can we start?', '2024-02-17 13:30:00'),
(3, 6, 7, 'Neha, I\'m interested in your yoga classes. I\'m looking to improve my flexibility and reduce stress. Are there beginner sessions?', '2024-02-21 10:15:00');

-- Insert Sample Payments
INSERT INTO `Payments` (`Student_User_ID`, `Instructor_User_ID`, `Skill_ID`, `Amount`, `Payment_Status`, `Payment_Method`, `Transaction_ID`, `Payment_Date`, `Completion_Date`) VALUES
(2, 1, 1, 2999.00, 'Completed', 'UPI', 'TXN202401170001', '2024-01-17 10:30:00', '2024-01-17 10:30:15'),
(3, 1, 2, 1999.00, 'Completed', 'Card', 'TXN202401220002', '2024-01-22 15:45:00', '2024-01-22 15:45:22'),
(5, 4, 5, 1499.00, 'Completed', 'NetBanking', 'TXN202402120003', '2024-02-12 17:20:00', '2024-02-12 17:20:18'),
(6, 5, 6, 999.00, 'Completed', 'UPI', 'TXN202402170004', '2024-02-17 14:15:00', '2024-02-17 14:15:12'),
(3, 6, 7, 1299.00, 'Completed', 'Card', 'TXN202402210005', '2024-02-21 11:00:00', '2024-02-21 11:00:25');

-- Insert Sample Enrollments
INSERT INTO `Enrollments` (`Student_User_ID`, `Skill_ID`, `Payment_ID`, `Enrollment_Date`, `Status`, `Progress`) VALUES
(2, 1, 1, '2024-01-17 10:30:30', 'Active', 25),
(3, 2, 2, '2024-01-22 15:45:45', 'Active', 40),
(5, 5, 3, '2024-02-12 17:20:30', 'Active', 60),
(6, 6, 4, '2024-02-17 14:15:30', 'Active', 80),
(3, 7, 5, '2024-02-21 11:00:45', 'Active', 15);

-- Insert Sample Reviews
INSERT INTO `Reviews` (`Student_User_ID`, `Skill_ID`, `Rating`, `Review_Text`, `Review_Date`) VALUES
(2, 1, 5, 'Excellent web development course! The instructor explains concepts clearly and provides hands-on projects. Highly recommended for beginners.', '2024-01-25 16:30:00'),
(3, 2, 4, 'Great digital marketing course with practical insights. Learned a lot about SEO and social media marketing. Could use more case studies.', '2024-01-30 14:20:00'),
(5, 5, 5, 'Amazing guitar lessons! Patient instructor and well-structured curriculum. I can already play several songs after just a few weeks.', '2024-02-20 18:45:00'),
(6, 6, 5, 'Fantastic cooking class! Learned authentic Indian recipes and techniques. The spice combinations are incredible. Worth every rupee!', '2024-02-25 12:30:00');

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Show all tables
SHOW TABLES;

-- Count records in each table
SELECT 'Users' as Table_Name, COUNT(*) as Record_Count FROM Users
UNION ALL
SELECT 'Categories', COUNT(*) FROM Categories
UNION ALL
SELECT 'Skills', COUNT(*) FROM Skills
UNION ALL
SELECT 'SkillCategories', COUNT(*) FROM SkillCategories
UNION ALL
SELECT 'Messages', COUNT(*) FROM Messages
UNION ALL
SELECT 'Payments', COUNT(*) FROM Payments
UNION ALL
SELECT 'Enrollments', COUNT(*) FROM Enrollments
UNION ALL
SELECT 'Reviews', COUNT(*) FROM Reviews;

-- Show sample data from each table
SELECT '=== USERS ===' as Info;
SELECT User_ID, Name, Email, Location FROM Users;

SELECT '=== CATEGORIES ===' as Info;
SELECT Category_ID, Category_Name FROM Categories;

SELECT '=== SKILLS ===' as Info;
SELECT s.Skill_ID, s.Skill_Name, s.Skill_Type, s.Price, s.Duration_Hours, u.Name as User_Name 
FROM Skills s 
JOIN Users u ON s.User_ID = u.User_ID;

SELECT '=== SKILL CATEGORIES ===' as Info;
SELECT sc.Skill_ID, s.Skill_Name, c.Category_Name 
FROM SkillCategories sc
JOIN Skills s ON sc.Skill_ID = s.Skill_ID
JOIN Categories c ON sc.Category_ID = c.Category_ID;

SELECT '=== MESSAGES ===' as Info;
SELECT m.Message_ID, 
       u1.Name as From_User, 
       u2.Name as To_User, 
       LEFT(m.Message_Text, 50) as Message_Preview
FROM Messages m
JOIN Users u1 ON m.From_User_ID = u1.User_ID
JOIN Users u2 ON m.To_User_ID = u2.User_ID;

SELECT '=== PAYMENTS ===' as Info;
SELECT p.Payment_ID, 
       u1.Name as Student, 
       u2.Name as Instructor,
       s.Skill_Name,
       CONCAT('₹', p.Amount) as Amount,
       p.Payment_Status,
       p.Payment_Method
FROM Payments p
JOIN Users u1 ON p.Student_User_ID = u1.User_ID
JOIN Users u2 ON p.Instructor_User_ID = u2.User_ID
JOIN Skills s ON p.Skill_ID = s.Skill_ID;

SELECT '=== ENROLLMENTS ===' as Info;
SELECT e.Enrollment_ID,
       u.Name as Student,
       s.Skill_Name,
       e.Status,
       CONCAT(e.Progress, '%') as Progress
FROM Enrollments e
JOIN Users u ON e.Student_User_ID = u.User_ID
JOIN Skills s ON e.Skill_ID = s.Skill_ID;

SELECT '=== REVIEWS ===' as Info;
SELECT r.Review_ID,
       u.Name as Student,
       s.Skill_Name,
       r.Rating,
       LEFT(r.Review_Text, 50) as Review_Preview
FROM Reviews r
JOIN Users u ON r.Student_User_ID = u.User_ID
JOIN Skills s ON r.Skill_ID = s.Skill_ID;

-- Commit the transaction
COMMIT;

-- =====================================================
-- SKILLSWAP COMPLETE DATABASE WITH PAYMENT SYSTEM
-- =====================================================
-- This script includes:
-- ✅ Core SkillSwap tables (Users, Skills, Categories, Messages)
-- ✅ Payment System tables (Payments, Enrollments, Reviews)
-- ✅ Indian pricing in INR (₹)
-- ✅ Sample data with realistic courses and pricing
-- ✅ Payment transactions and enrollments
-- ✅ Student reviews and ratings
--
-- MAMP OPTIMIZATION:
-- - Uses utf8mb4_unicode_ci collation for better Unicode support
-- - Sets proper SQL mode for MAMP compatibility
-- - Uses transactions for data integrity
-- - All passwords are stored as plain text for easy development/testing
-- 
-- DEFAULT LOGIN CREDENTIALS:
-- Email: admin@skillswap.com
-- Password: admin123 (or "password" for other users)
--
-- SAMPLE COURSES WITH PRICING:
-- - Web Development: ₹2,999 (40 hours)
-- - Digital Marketing: ₹1,999 (25 hours)
-- - Guitar Lessons: ₹1,499 (20 hours)
-- - Indian Cooking: ₹999 (12 hours)
-- - Yoga & Meditation: ₹1,299 (15 hours)
-- =====================================================
