/*
 Navicat MySQL Data Transfer

 Source Server         : KOHALIK
 Source Server Type    : MySQL
 Source Server Version : 80030
 Source Host           : localhost:3306
 Source Schema         : qcubed-5

 Target Server Type    : MySQL
 Target Server Version : 80030
 File Encoding         : 65001

 Date: 11/01/2024 18:17:24
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for example
-- ----------------------------
DROP TABLE IF EXISTS `example`;
CREATE TABLE `example` (
  `id` int NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8mb4_vi_0900_ai_ci,
  `picture_id` int DEFAULT NULL,
  `files_ids` varchar(255) COLLATE utf8mb4_vi_0900_ai_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_vi_0900_ai_ci;

-- ----------------------------
-- Records of example
-- ----------------------------
BEGIN;
INSERT INTO `example` VALUES (1, '<p><strong><font style=\"vertical-align: inherit;\"><font style=\"vertical-align: inherit;\">Midagi</font></font></strong><font style=\"vertical-align: inherit;\"><font style=\"vertical-align: inherit;\"> alustuseks.</font></font></p>\n', NULL, '');
INSERT INTO `example` VALUES (2, '', NULL, '');
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
