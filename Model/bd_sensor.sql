/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50724
 Source Host           : localhost:3306
 Source Schema         : w_temperatura

 Target Server Type    : MySQL
 Target Server Version : 50724
 File Encoding         : 65001

 Date: 23/08/2021 17:05:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- todo está organizado en orden de primero la clase padre y despues las hijas, las clases que tienen referencia a otra clase se encuentran despues de esa clase
-- ----------------------------
-- Table structure for w_temperaturasensor
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturasensor`;
CREATE TABLE `w_temperaturasensor`  (
  `idtemperaturasensor` int(255) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tscodigo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tsubicacion` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tselementosresguardan` smallint(6) NOT NULL,
  `tsmontoresguardado` float NOT NULL,
  PRIMARY KEY (`idtemperaturasensor`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 56 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_temperaturasensorheladera
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturasensorheladera`;
CREATE TABLE `w_temperaturasensorheladera`  (
  `idtemperaturasensor` int(255) UNSIGNED NOT NULL,
  `marca` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `modelo` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`idtemperaturasensor`) USING BTREE,
  CONSTRAINT `fk_w_temperaturasensorheladera_w_temperaturasensor_1` FOREIGN KEY (`idtemperaturasensor`) REFERENCES `w_temperaturasensor` (`idtemperaturasensor`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_temperaturasensorservidor
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturasensorservidor`;
CREATE TABLE `w_temperaturasensorservidor`  (
  `idtemperaturasensor` int(10) UNSIGNED NOT NULL,
  `tssporcentajeperdida` float NOT NULL,
  PRIMARY KEY (`idtemperaturasensor`) USING BTREE,
  CONSTRAINT `fk_w_temperaturasensorservidor_w_temperaturasensor_1` FOREIGN KEY (`idtemperaturasensor`) REFERENCES `w_temperaturasensor` (`idtemperaturasensor`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_temperaturaregistro
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturaregistro`;
CREATE TABLE `w_temperaturaregistro`  (
  `idtemperaturaregistro` int(11) NOT NULL AUTO_INCREMENT,
  `idtemperaturasensor` int(10) UNSIGNED NOT NULL,
  `tltemperatura` float NOT NULL,
  `tlfecharegistro` timestamp(0) NOT NULL,
  PRIMARY KEY (`idtemperaturaregistro`) USING BTREE,
  INDEX `fk_w_temperaturalog_w_temperaturasensor_1`(`idtemperaturasensor`) USING BTREE,
  CONSTRAINT `fk_w_temperaturalog_w_temperaturasensor_1` FOREIGN KEY (`idtemperaturasensor`) REFERENCES `w_temperaturasensor` (`idtemperaturasensor`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_temperaturaalarmas
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturaalarmas`;
CREATE TABLE `w_temperaturaalarmas`  (
  `idtemperaturaalarma` int(11) NOT NULL AUTO_INCREMENT,
  `idtemperaturasensor` int(11) UNSIGNED NOT NULL,
  `tasuperior` float NOT NULL,
  `tainferior` float NOT NULL,
  `tafechainicio` datetime(0) NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `tafechafin` datetime(0) NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`idtemperaturaalarma`) USING BTREE,
  INDEX `fk_w_temperaturaalarmas_w_temperaturasensor_1`(`idtemperaturasensor`) USING BTREE,
  CONSTRAINT `fk_w_temperaturaalarmas_w_temperaturasensor_1` FOREIGN KEY (`idtemperaturasensor`) REFERENCES `w_temperaturasensor` (`idtemperaturasensor`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_temperaturaaviso
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturaaviso`;
CREATE TABLE `w_temperaturaaviso`  (
  `idtemperaturaaviso` int(11) NOT NULL AUTO_INCREMENT,
  `taactivo` timestamp(1) NULL DEFAULT NULL,
  `tanombre` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `taemail` longtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`idtemperaturaaviso`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for w_temperaturasensortemperaturaaviso
-- ----------------------------
DROP TABLE IF EXISTS `w_temperaturasensortemperaturaaviso`;
CREATE TABLE `w_temperaturasensortemperaturaaviso`  (
  `idavisoalarma` int(11) NOT NULL AUTO_INCREMENT,---id propio de la relacion, me parece que es más manejable tener un id individual que estar tratando con una clave compuesta
  `idtemperaturaaviso` int(11) NOT NULL,
  `idtemperaturaalarma` int(11) NOT NULL,
  PRIMARY KEY (`idavisoalarma`) USING BTREE,
 FOREIGN KEY (`idtemperaturaaviso`) REFERENCES `w_temperaturaaviso` (`idtemperaturaaviso`) ON DELETE RESTRICT ON UPDATE RESTRICT,
 FOREIGN KEY (`idtemperaturaalarma`) REFERENCES `w_temperaturaalarmas` (`idtemperaturaalarma`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

SET FOREIGN_KEY_CHECKS = 1;