-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: codeigniter
-- ------------------------------------------------------
-- Server version	8.0.37

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `calendars`
--

DROP TABLE IF EXISTS `calendars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendars` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendars`
--

LOCK TABLES `calendars` WRITE;
/*!40000 ALTER TABLE `calendars` DISABLE KEYS */;
INSERT INTO `calendars` VALUES (1,'Đi làm','DNC','2024-09-07 07:00:00','2024-09-07 17:00:00'),(2,'Test Calendar','Test','2024-10-12 14:22:00','2024-10-12 19:22:00'),(3,'Go home','Home','2024-10-12 16:25:00','2024-10-12 17:25:00'),(4,'HEHE','HEHE','2024-10-12 16:26:00','2024-10-12 19:26:00'),(5,'Test Calendar','Test','2024-10-13 06:48:00','2024-10-15 06:49:00');
/*!40000 ALTER TABLE `calendars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todo`
--

DROP TABLE IF EXISTS `todo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `todo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `image` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `priority` int DEFAULT NULL,
  `status` int DEFAULT NULL COMMENT '1. Chưa bắt đầu 2. Đang làm 3. Hoàn thành',
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_id_idx` (`user_id`),
  CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todo`
--

LOCK TABLES `todo` WRITE;
/*!40000 ALTER TABLE `todo` DISABLE KEYS */;
INSERT INTO `todo` VALUES (1,1,'uploads/e4377bd46c756d09ca8de8f0b83e48334cc5d5a496677193967d62cba647b34f.enc','5tan','NodeJS',2,2,'2024-10-31'),(2,1,'uploads/e364d5c89fbb217319556bc93b2e8840d39fb8547428569f703ea2374abb1810.enc','Tankkk','Build body',1,3,'2024-10-27'),(3,1,'uploads/8a4db18dec7d523e89fd1fc5ad9932c6057ea52ce3d0aa90953efd86b9881a37.enc','Code','Success',2,3,'2024-10-28'),(4,1,'uploads/6692c7e3cd77ada943e53bd1a2ebee880d4348281390683958668bba407bb9b1.enc','Tam','Second',3,2,'2024-10-13'),(5,1,'uploads/6377dbca712fbf346913782363042351c19a7fc3711d0f78aec65aba27fff7e3.enc','Task 1','Description of task 1',1,2,'2024-11-12'),(6,1,NULL,'Task 2','Description of task 2',3,NULL,NULL),(7,1,NULL,'Task 3','Description of task 3',2,NULL,NULL);
/*!40000 ALTER TABLE `todo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todo_meta`
--

DROP TABLE IF EXISTS `todo_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `todo_meta` (
  `todo_meta_id` int NOT NULL AUTO_INCREMENT,
  `todo_id` int DEFAULT NULL,
  `code` varchar(45) DEFAULT NULL,
  `fieldname` varchar(45) DEFAULT NULL,
  `fieldvalue` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`todo_meta_id`),
  KEY `fk_todo_id_idx` (`todo_id`),
  CONSTRAINT `fk_todo_id` FOREIGN KEY (`todo_id`) REFERENCES `todo` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='		';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todo_meta`
--

LOCK TABLES `todo_meta` WRITE;
/*!40000 ALTER TABLE `todo_meta` DISABLE KEYS */;
INSERT INTO `todo_meta` VALUES (1,1,'status','Trạng thái','Ổn'),(2,1,'deleted','Xóa','Đã xóa'),(3,2,'deleted','Xóa','Đã xóa'),(4,3,'percent','Phần trăm','100'),(5,3,'time','Thời gian','7:00'),(6,1,'note','Ghi chú','Thử nghiệm');
/*!40000 ALTER TABLE `todo_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT NULL,
  `firstname` varchar(100) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_image` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `user_gender` tinyint DEFAULT NULL,
  `user_birthday` date DEFAULT NULL,
  `user_phone` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'5tan@gmail.com','Tấn','Nguyễn','./uploads/profile_pictures/yae2.jpg','uploads/21bdce733cfb86a9f27185f2d206d47b8d639080b525d6e7020799a213b60989.enc',1,'2001-10-01','0328069555');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-12-19  9:04:34
