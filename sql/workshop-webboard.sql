/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50738
 Source Host           : 127.0.0.1:3306
 Source Schema         : webboard

 Target Server Type    : MySQL
 Target Server Version : 50738
 File Encoding         : 65001

 Date: 29/12/2023 17:30:51
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for comment
-- ----------------------------
DROP TABLE IF EXISTS `comment`;
CREATE TABLE `comment`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `topic_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `modified` timestamp(0) NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `topic_id`(`topic_id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `created`(`created`) USING BTREE,
  INDEX `name`(`name`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of comment
-- ----------------------------

-- ----------------------------
-- Table structure for topic
-- ----------------------------
DROP TABLE IF EXISTS `topic`;
CREATE TABLE `topic`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `created` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP(0),
  `modified` timestamp(0) NULL DEFAULT NULL,
  `last_commented` timestamp(0) NOT NULL DEFAULT '0000-00-00 00:00:00',
  `tags` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ip` varchar(45) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `num_comments` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_commented_user_id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `last_commented_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `num_views` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`user_id`) USING BTREE,
  INDEX `created`(`created`) USING BTREE,
  INDEX `last_replied`(`last_commented`) USING BTREE,
  INDEX `name`(`name`) USING BTREE,
  INDEX `num_comments`(`num_comments`) USING BTREE,
  INDEX `num_views`(`num_views`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of topic
-- ----------------------------

-- ----------------------------
-- Table structure for users
-- ----------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`  (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of users
-- ----------------------------
INSERT INTO `users` VALUES (1, 'admin', 'admin@gmail.com', '123456');

SET FOREIGN_KEY_CHECKS = 1;
