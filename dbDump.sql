-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: 127.0.0.1    Database: users
-- ------------------------------------------------------
-- Server version	8.0.44-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `bookmarked`
--

DROP TABLE IF EXISTS `bookmarked`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `bookmarked` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `book_id` int NOT NULL,
  `bookmarked_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_book_unique` (`user_id`,`book_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `bookmarked_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookmarked_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bookmarked`
--

LOCK TABLES `bookmarked` WRITE;
/*!40000 ALTER TABLE `bookmarked` DISABLE KEYS */;
INSERT INTO `bookmarked` VALUES (2,1,2,'2025-11-22 13:50:20'),(3,1,3,'2025-11-22 13:50:20'),(4,1,4,'2025-11-22 13:50:20'),(10,1,10,'2025-11-22 13:50:20'),(40,1,6,'2025-11-26 12:35:02'),(49,1,13,'2025-11-28 17:13:38');
/*!40000 ALTER TABLE `bookmarked` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isbn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_image_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `publish_date` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `books`
--

LOCK TABLES `books` WRITE;
/*!40000 ALTER TABLE `books` DISABLE KEYS */;
INSERT INTO `books` VALUES (2,'The Pragmatic Programmer','David Thomas, Andrew Hunt','9780135957059','https://covers.openlibrary.org/b/isbn/9780135957059-L.jpg','2019-09-13','2025-11-20 13:28:36'),(3,'Harry Potter and the Sorcerer\'s Stone','J.K. Rowling','9780590353427','https://covers.openlibrary.org/b/isbn/9780590353427-L.jpg','1998-09-01','2025-11-20 13:28:36'),(4,'The Hobbit','J.R.R. Tolkien','9780547928227','https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg','2012-09-18','2025-11-20 13:28:36'),(5,'Introduction to Algorithms','Thomas H. Cormen','9780262033848','https://covers.openlibrary.org/b/isbn/9780262033848-L.jpg','2009-07-31','2025-11-20 13:28:36'),(6,'Design Patterns','Erich Gamma','9780201633610','https://covers.openlibrary.org/b/isbn/9780201633610-L.jpg','1994-10-31','2025-11-20 13:28:36'),(7,'The Great Gatsby','F. Scott Fitzgerald','9780743273565','https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg','2004-09-30','2025-11-20 13:28:36'),(8,'1984','George Orwell','9780451524935','https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg','1961-01-01','2025-11-20 13:28:36'),(10,'Head First Design Patterns','Eric Freeman','9780596007126','https://covers.openlibrary.org/b/isbn/9780596007126-L.jpg','2004-10-01','2025-11-20 13:28:36'),(13,'bookwow','wowbook','1234567890123','','12-12-1212','2025-11-28 17:11:54');
/*!40000 ALTER TABLE `books` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email_unique` (`email`),
  UNIQUE KEY `username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'test','test@gmail.com','$2y$12$jvHpPhnApG7GNHtJWbYEGumPkdOqcTQ6OZ700zMPavJTwpFwD6MAa','2025-11-17 16:06:10','admin'),(2,'test2','test2@gmail.com','$2y$12$ANQij0NLqw0v/sEJTv7VrOYKWKKy2uC5kyu3.t48TOvNqQToIGzdq','2025-11-20 14:45:17','user');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-28 20:34:47
