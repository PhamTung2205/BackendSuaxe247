/*
 Navicat Premium Dump SQL

 Source Server         : localhost_3306
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : suaxe247

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 05/11/2025 11:24:02
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for appointment
-- ----------------------------
DROP TABLE IF EXISTS `appointment`;
CREATE TABLE `appointment`  (
  `PK_idAppointment` int NOT NULL AUTO_INCREMENT,
  `FK_idStore` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_idCustomer` int NULL DEFAULT NULL,
  `FK_idVehicle` int NULL DEFAULT NULL,
  `appointmentTime` time NULL DEFAULT NULL,
  `appointmentDate` date NULL DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idAppointment`) USING BTREE,
  INDEX `FK_idStore`(`FK_idStore` ASC) USING BTREE,
  INDEX `FK_idCustomer`(`FK_idCustomer` ASC) USING BTREE,
  INDEX `FK_idVehicle`(`FK_idVehicle` ASC) USING BTREE,
  CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`FK_idStore`) REFERENCES `store` (`PK_idStore`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`FK_idCustomer`) REFERENCES `user` (`PK_idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`FK_idVehicle`) REFERENCES `vehicle` (`PK_idVehicle`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of appointment
-- ----------------------------
INSERT INTO `appointment` VALUES (1, 'S001', 1, 1, '09:00:00', '2025-09-20', 'Chờ xác nhận', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `appointment` VALUES (2, 'S002', 2, 2, '09:00:00', '2025-09-20', 'Đã xác nhận', '2025-09-20 17:29:30', '2025-10-29 11:17:01', 0);
INSERT INTO `appointment` VALUES (3, 'S002', 3, 3, '09:00:00', '2025-09-20', 'Đã xác nhận', '2025-09-20 17:29:30', '2025-10-29 11:17:03', 0);
INSERT INTO `appointment` VALUES (4, 'S002', 4, 4, '09:00:00', '2025-09-20', 'Chờ xác nhận', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `appointment` VALUES (5, 'S001', 5, 5, '09:00:00', '2025-09-20', 'Chờ xác nhận', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `appointment` VALUES (6, 'S002', 16, 1, '11:40:00', '2025-10-15', 'Đã hủy', '2025-10-15 22:40:42', '2025-10-30 13:54:11', 0);
INSERT INTO `appointment` VALUES (7, 'S002', 16, 1, '00:46:00', '2025-10-15', 'Hoàn thành', '2025-10-15 22:47:46', '2025-10-30 13:54:09', 0);
INSERT INTO `appointment` VALUES (8, 'S002', 16, 43, '11:30:00', '2025-10-30', 'Chờ xác nhận', '2025-10-20 11:15:11', '2025-11-04 20:01:48', 1);
INSERT INTO `appointment` VALUES (9, 'S002', 16, 40, '18:00:00', '2025-10-21', 'Hoàn thành', '2025-10-20 14:33:00', '2025-10-30 13:54:34', 0);
INSERT INTO `appointment` VALUES (10, 'S002', 15, 44, '07:30:00', '2025-10-28', 'Chờ xác nhận', '2025-10-27 22:56:47', NULL, 0);
INSERT INTO `appointment` VALUES (11, 'S002', 16, 40, '16:00:00', '2025-10-29', 'Hoàn thành', '2025-10-29 15:50:21', '2025-11-04 00:03:10', 0);
INSERT INTO `appointment` VALUES (12, 'S002', 13, 46, '07:30:00', '2025-10-30', 'Hoàn thành', '2025-10-29 23:20:37', '2025-10-29 23:39:30', 0);
INSERT INTO `appointment` VALUES (13, 'S002', 16, 6, '14:00:00', '2025-10-30', 'Hoàn thành', '2025-10-30 13:46:49', '2025-11-03 14:13:07', 0);
INSERT INTO `appointment` VALUES (14, 'S002', 16, 1, '15:00:00', '2025-10-30', 'Hoàn thành', '2025-10-30 14:40:23', '2025-10-30 14:44:05', 0);
INSERT INTO `appointment` VALUES (15, 'S002', 16, 1, '08:00:00', '2025-11-05', 'Chờ xác nhận', '2025-11-04 20:01:59', '2025-11-05 01:58:45', 0);

-- ----------------------------
-- Table structure for appointment_service
-- ----------------------------
DROP TABLE IF EXISTS `appointment_service`;
CREATE TABLE `appointment_service`  (
  `PK_id` int NOT NULL AUTO_INCREMENT,
  `FK_idAppointment` int NOT NULL,
  `FK_idService` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`PK_id`) USING BTREE,
  INDEX `FK_idAppointment`(`FK_idAppointment` ASC) USING BTREE,
  INDEX `FK_idService`(`FK_idService` ASC) USING BTREE,
  CONSTRAINT `appointment_service_ibfk_1` FOREIGN KEY (`FK_idAppointment`) REFERENCES `appointment` (`PK_idAppointment`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `appointment_service_ibfk_2` FOREIGN KEY (`FK_idService`) REFERENCES `service` (`PK_idService`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of appointment_service
-- ----------------------------
INSERT INTO `appointment_service` VALUES (1, 10, 'SVC01');
INSERT INTO `appointment_service` VALUES (2, 10, 'SVC02');
INSERT INTO `appointment_service` VALUES (3, 11, 'SVC01');
INSERT INTO `appointment_service` VALUES (4, 11, 'SVC03');
INSERT INTO `appointment_service` VALUES (7, 8, 'SVC01');
INSERT INTO `appointment_service` VALUES (11, 12, 'SVC02');
INSERT INTO `appointment_service` VALUES (13, 13, 'SVC01');
INSERT INTO `appointment_service` VALUES (14, 14, 'SVC01');
INSERT INTO `appointment_service` VALUES (15, 14, 'SVC02');
INSERT INTO `appointment_service` VALUES (17, 15, 'SVC07');

-- ----------------------------
-- Table structure for importreceipt
-- ----------------------------
DROP TABLE IF EXISTS `importreceipt`;
CREATE TABLE `importreceipt`  (
  `PK_idImport` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FK_idStore` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_idSupplier` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_idCreatedBy` int NULL DEFAULT NULL,
  `deliveryReceipt` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idImport`) USING BTREE,
  INDEX `FK_idStore`(`FK_idStore` ASC) USING BTREE,
  INDEX `FK_idSupplier`(`FK_idSupplier` ASC) USING BTREE,
  INDEX `FK_idCreatedBy`(`FK_idCreatedBy` ASC) USING BTREE,
  CONSTRAINT `importreceipt_ibfk_1` FOREIGN KEY (`FK_idStore`) REFERENCES `store` (`PK_idStore`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `importreceipt_ibfk_2` FOREIGN KEY (`FK_idSupplier`) REFERENCES `supplier` (`PK_idSupplier`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `importreceipt_ibfk_4` FOREIGN KEY (`FK_idCreatedBy`) REFERENCES `user` (`PK_idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of importreceipt
-- ----------------------------
INSERT INTO `importreceipt` VALUES ('IMPS0011', 'S001', 'SUP01', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0012', 'S001', 'SUP02', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0013', 'S001', 'SUP03', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0014', 'S001', 'SUP04', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0015', 'S001', 'SUP05', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0021', 'S002', 'SUP01', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0022', 'S002', 'SUP02', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0023', 'S002', 'SUP03', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0024', 'S002', 'SUP04', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('IMPS0025', 'S002', 'SUP05', 1, NULL, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `importreceipt` VALUES ('PN20251016001932', 'S002', 'SUP01', 14, NULL, '2025-10-16 00:19:32', '2025-10-16 00:19:32', 0);
INSERT INTO `importreceipt` VALUES ('PN20251016153603', 'S002', 'SUP02', 14, NULL, '2025-10-16 15:36:03', '2025-10-16 15:36:03', 0);
INSERT INTO `importreceipt` VALUES ('PN20251019141029', 'S002', 'SUP02', 14, '122', '2025-10-19 14:10:29', '2025-10-19 14:10:29', 0);
INSERT INTO `importreceipt` VALUES ('PN20251103141147', 'S002', 'SUP02', 14, '2111', '2025-11-03 14:11:47', '2025-11-03 14:11:47', 0);

-- ----------------------------
-- Table structure for importreceiptdetail
-- ----------------------------
DROP TABLE IF EXISTS `importreceiptdetail`;
CREATE TABLE `importreceiptdetail`  (
  `PK_idImportDetail` int NOT NULL AUTO_INCREMENT,
  `FK_idImport` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_idSparePart` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `requestedQty` int NULL DEFAULT NULL,
  `importedQty` int NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idImportDetail`) USING BTREE,
  INDEX `FK_idImport`(`FK_idImport` ASC) USING BTREE,
  INDEX `FK_idSparePart`(`FK_idSparePart` ASC) USING BTREE,
  CONSTRAINT `importreceiptdetail_ibfk_1` FOREIGN KEY (`FK_idImport`) REFERENCES `importreceipt` (`PK_idImport`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `importreceiptdetail_ibfk_2` FOREIGN KEY (`FK_idSparePart`) REFERENCES `sparepart` (`PK_idSparePart`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 102 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of importreceiptdetail
-- ----------------------------
INSERT INTO `importreceiptdetail` VALUES (1, 'IMPS0011', 'P002', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (2, 'IMPS0011', 'P003', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (3, 'IMPS0011', 'P004', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (4, 'IMPS0011', 'P005', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (5, 'IMPS0011', 'P006', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (6, 'IMPS0011', 'P007', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (7, 'IMPS0011', 'P008', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (11, 'IMPS0012', 'P003', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (12, 'IMPS0012', 'P004', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (13, 'IMPS0012', 'P005', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (14, 'IMPS0012', 'P006', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (15, 'IMPS0012', 'P007', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (16, 'IMPS0012', 'P008', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (20, 'IMPS0013', 'P004', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (21, 'IMPS0013', 'P005', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (22, 'IMPS0013', 'P006', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (28, 'IMPS0014', 'P005', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (29, 'IMPS0014', 'P006', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (30, 'IMPS0014', 'P007', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (31, 'IMPS0014', 'P008', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (32, 'IMPS0014', 'P001', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (33, 'IMPS0014', 'P002', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (34, 'IMPS0014', 'P003', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (39, 'IMPS0015', 'P006', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (40, 'IMPS0015', 'P007', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (41, 'IMPS0015', 'P008', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (42, 'IMPS0015', 'P001', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (43, 'IMPS0015', 'P002', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (44, 'IMPS0015', 'P003', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (45, 'IMPS0015', 'P004', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (50, 'IMPS0021', 'P002', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (51, 'IMPS0021', 'P003', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (55, 'IMPS0022', 'P003', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (56, 'IMPS0022', 'P004', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (57, 'IMPS0022', 'P005', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (58, 'IMPS0022', 'P006', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (63, 'IMPS0023', 'P004', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (64, 'IMPS0023', 'P005', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (65, 'IMPS0023', 'P006', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (66, 'IMPS0023', 'P007', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (67, 'IMPS0023', 'P008', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (68, 'IMPS0023', 'P001', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (74, 'IMPS0024', 'P005', NULL, 3, 0);
INSERT INTO `importreceiptdetail` VALUES (75, 'IMPS0024', 'P006', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (76, 'IMPS0024', 'P007', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (77, 'IMPS0024', 'P008', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (78, 'IMPS0024', 'P001', NULL, 2, 0);
INSERT INTO `importreceiptdetail` VALUES (83, 'IMPS0025', 'P006', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (84, 'IMPS0025', 'P007', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (85, 'IMPS0025', 'P008', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (86, 'IMPS0025', 'P001', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (87, 'IMPS0025', 'P002', NULL, 1, 0);
INSERT INTO `importreceiptdetail` VALUES (88, 'IMPS0025', 'P003', NULL, 5, 0);
INSERT INTO `importreceiptdetail` VALUES (89, 'IMPS0025', 'P004', NULL, 4, 0);
INSERT INTO `importreceiptdetail` VALUES (90, 'PN20251016001932', 'P012', NULL, 100, 0);
INSERT INTO `importreceiptdetail` VALUES (91, 'PN20251016001932', 'P007', NULL, 100, 0);
INSERT INTO `importreceiptdetail` VALUES (92, 'PN20251016153603', 'P005', NULL, 50, 0);
INSERT INTO `importreceiptdetail` VALUES (93, 'PN20251016153603', 'P002', NULL, 50, 0);
INSERT INTO `importreceiptdetail` VALUES (97, 'PN20251019141029', '111111', 10, 10, 0);
INSERT INTO `importreceiptdetail` VALUES (98, 'PN20251019141029', 'P001', 20, 20, 0);
INSERT INTO `importreceiptdetail` VALUES (99, 'PN20251103141147', 'P001', 50, 50, 0);
INSERT INTO `importreceiptdetail` VALUES (100, 'PN20251103141147', 'P003', 100, 100, 0);
INSERT INTO `importreceiptdetail` VALUES (101, 'PN20251103141147', 'P006', 20, 20, 0);

-- ----------------------------
-- Table structure for invoice
-- ----------------------------
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE `invoice`  (
  `PK_idInvoice` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FK_idAppointment` int NULL DEFAULT NULL,
  `FK_idVehicle` int NULL DEFAULT NULL,
  `FK_idCustomer` int NULL DEFAULT NULL,
  `FK_idStore` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_idCashier` int NULL DEFAULT NULL,
  `FK_idTechnician` int NULL DEFAULT NULL,
  `checkInTime` datetime NULL DEFAULT NULL,
  `checkOutTime` datetime NULL DEFAULT NULL,
  `customerName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `kmNumber` int NULL DEFAULT NULL,
  `customerRequest` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `postRepairStatus` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `paymentMethod` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idInvoice`) USING BTREE,
  INDEX `FK_idAppointment`(`FK_idAppointment` ASC) USING BTREE,
  INDEX `FK_idVehicle`(`FK_idVehicle` ASC) USING BTREE,
  INDEX `FK_idCustomer`(`FK_idCustomer` ASC) USING BTREE,
  INDEX `FK_idStore`(`FK_idStore` ASC) USING BTREE,
  INDEX `FK_idCashier`(`FK_idCashier` ASC) USING BTREE,
  INDEX `FK_idTechnician`(`FK_idTechnician` ASC) USING BTREE,
  CONSTRAINT `invoice_ibfk_1` FOREIGN KEY (`FK_idAppointment`) REFERENCES `appointment` (`PK_idAppointment`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `invoice_ibfk_3` FOREIGN KEY (`FK_idVehicle`) REFERENCES `vehicle` (`PK_idVehicle`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `invoice_ibfk_4` FOREIGN KEY (`FK_idCustomer`) REFERENCES `user` (`PK_idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `invoice_ibfk_5` FOREIGN KEY (`FK_idStore`) REFERENCES `store` (`PK_idStore`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `invoice_ibfk_6` FOREIGN KEY (`FK_idCashier`) REFERENCES `user` (`PK_idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `invoice_ibfk_7` FOREIGN KEY (`FK_idTechnician`) REFERENCES `user` (`PK_idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of invoice
-- ----------------------------
INSERT INTO `invoice` VALUES ('INV001', 1, 1, 16, 'S001', 1, 15, '2025-09-18 17:11:30', '2025-09-18 17:29:30', 'Khách hàng', 450000, 'Yêu cầu 1', '1', 'Chuyển khoản', 'Đã thanh toán', '2025-09-18 17:29:30', 0);
INSERT INTO `invoice` VALUES ('INV002', 2, 2, 16, 'S001', 1, 3, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 'Khách hàng', 2000, 'Yêu cầu 2', NULL, 'Tiền mặt', 'Đã thanh toán', '2025-09-20 17:29:30', 0);
INSERT INTO `invoice` VALUES ('INV003', 3, 3, 12, 'S002', 1, 15, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 'Thương', 10000, 'Yêu cầu 3', 'tồn tại sau sửa chữa', 'Chuyển khoản', 'Đã thanh toán', '2025-09-20 17:29:30', 0);
INSERT INTO `invoice` VALUES ('INV004', 4, 4, 12, 'S002', 5, 15, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 'Admin', 5000, 'Yêu cầu 4', NULL, 'Chuyển khoản', 'Đã thanh toán', '2025-09-20 17:29:30', 0);
INSERT INTO `invoice` VALUES ('INV005', 5, 5, 16, 'S002', 5, 8, '2025-09-20 17:29:30', '2025-09-20 17:29:30', 'Khách hàng', 70000, 'Yêu cầu 5', NULL, 'Tiền mặt', 'Đã thanh toán', '2025-09-20 17:29:30', 0);
INSERT INTO `invoice` VALUES ('S002-000001', NULL, 35, 47, 'S002', 14, 15, '2025-10-25 13:15:00', '2025-10-25 13:24:00', 'thương', 15511, 'Yêu cầu của Thương', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-25 13:25:38', 0);
INSERT INTO `invoice` VALUES ('S002-000002', NULL, 35, 47, 'S002', 14, 15, '2025-10-25 13:41:00', '2025-10-25 13:48:00', 'Dương Thương', 152221, 'Yêu cầu của Dương Thương', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-25 13:48:47', 0);
INSERT INTO `invoice` VALUES ('S002-000003', NULL, 1, 16, 'S002', 14, 15, '2025-10-24 13:50:00', '2025-10-24 13:53:00', 'Khách hàng', 45511, 'Yêu cầu của khách hàng', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-25 13:50:58', 0);
INSERT INTO `invoice` VALUES ('S002-000004', NULL, 40, 16, 'S002', 14, 6, '2025-10-25 13:52:00', '2025-10-25 13:52:00', 'Khách hàng', 15451, 'yêu cầu', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-25 13:53:31', 0);
INSERT INTO `invoice` VALUES ('S002-000005', NULL, 42, 53, 'S002', 14, 15, '2025-10-25 13:56:00', '2025-10-25 13:56:00', 'Dương Văn Nam', 5054, 'Yêu cầu cảu Nguyễn Văn Nam', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-25 13:58:00', 0);
INSERT INTO `invoice` VALUES ('S002-000006', NULL, 38, 50, 'S002', 14, 6, '2025-10-25 13:59:00', '2025-10-25 13:59:00', 'Thương', 6548, 'yeu cau', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-25 13:59:52', 0);
INSERT INTO `invoice` VALUES ('S002-000007', NULL, 35, 2, 'S002', 14, 6, '2025-10-27 11:30:00', '2025-10-27 11:30:00', 'th ư', 6563, 'hjwh jwwhd ', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-27 11:31:01', 0);
INSERT INTO `invoice` VALUES ('S002-000008', NULL, 2, 16, 'S002', 14, 7, '2025-10-27 23:00:00', '2025-10-27 23:00:00', 'Trần Thị BB', 5465, 'rgt', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-27 23:01:19', 0);
INSERT INTO `invoice` VALUES ('S002-000009', 9, 40, 16, 'S002', 14, 15, '2025-10-29 16:25:00', '2025-10-29 16:25:00', 'Khách hàng', 11111, '1', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-29 16:25:28', 0);
INSERT INTO `invoice` VALUES ('S002-000010', 12, 46, 13, 'S002', 14, 7, '2025-10-29 23:38:00', '2025-10-29 23:38:00', 'Quản lý hệ thống', 555, '5', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-29 23:38:28', 0);
INSERT INTO `invoice` VALUES ('S002-000011', 14, 1, 16, 'S002', 14, 6, '2025-10-30 14:41:00', '2025-10-30 14:41:00', 'Khách hàng', 12345, '1', '', 'Chuyển khoản', 'Đã thanh toán', '2025-10-30 14:44:05', 0);
INSERT INTO `invoice` VALUES ('S002-000012', NULL, 9, 14, 'S002', 14, 6, '2025-11-03 00:02:00', '2025-11-03 00:02:00', 'Quản lý cửa hàng', 122222, '1222', '', 'Chuyển khoản', 'Đã thanh toán', '2025-11-03 00:03:26', 0);
INSERT INTO `invoice` VALUES ('S002-000013', 13, 6, 16, 'S002', 14, 7, '2025-11-03 14:11:00', '2025-11-03 14:12:00', 'Khách hàng', 11111, '1', '', 'Chuyển khoản', 'Đã thanh toán', '2025-11-03 14:13:07', 0);
INSERT INTO `invoice` VALUES ('S002-000014', NULL, 47, 60, 'S002', 14, 6, '2025-11-03 23:56:00', '2025-11-03 23:56:00', 'KH mới', 12345, 'yêu cầu', '', 'Chuyển khoản', 'Đã thanh toán', '2025-11-03 23:57:28', 0);
INSERT INTO `invoice` VALUES ('S002-000015', 11, 40, 16, 'S002', 14, 7, '2025-11-04 00:02:00', '2025-11-04 00:02:00', 'Khách hàng', 11111111, '1', '', 'Chuyển khoản', 'Đã thanh toán', '2025-11-04 00:03:10', 0);

-- ----------------------------
-- Table structure for invoice_service
-- ----------------------------
DROP TABLE IF EXISTS `invoice_service`;
CREATE TABLE `invoice_service`  (
  `PK_id` int NOT NULL AUTO_INCREMENT,
  `FK_idInvoice` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FK_idService` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `laborCost` decimal(10, 0) NULL DEFAULT NULL,
  PRIMARY KEY (`PK_id`) USING BTREE,
  INDEX `FK_idInvoice`(`FK_idInvoice` ASC) USING BTREE,
  INDEX `FK_idService`(`FK_idService` ASC) USING BTREE,
  CONSTRAINT `invoice_service_ibfk_1` FOREIGN KEY (`FK_idInvoice`) REFERENCES `invoice` (`PK_idInvoice`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `invoice_service_ibfk_2` FOREIGN KEY (`FK_idService`) REFERENCES `service` (`PK_idService`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 55 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of invoice_service
-- ----------------------------
INSERT INTO `invoice_service` VALUES (1, 'INV001', 'SVC01', 5000);
INSERT INTO `invoice_service` VALUES (2, 'INV002', 'SVC02', 50000);
INSERT INTO `invoice_service` VALUES (3, 'INV002', 'SVC03', 30000);
INSERT INTO `invoice_service` VALUES (4, 'INV003', 'SVC05', 20000);
INSERT INTO `invoice_service` VALUES (5, 'INV004', 'SVC06', 30000);
INSERT INTO `invoice_service` VALUES (6, 'INV004', 'SVC02', 50000);
INSERT INTO `invoice_service` VALUES (7, 'INV005', 'SVC04', 25000);
INSERT INTO `invoice_service` VALUES (27, 'S002-000001', 'SVC02', 20000);
INSERT INTO `invoice_service` VALUES (28, 'S002-000001', 'SVC03', 50000);
INSERT INTO `invoice_service` VALUES (29, 'S002-000002', 'SVC01', 50000);
INSERT INTO `invoice_service` VALUES (30, 'S002-000003', 'SVC01', 0);
INSERT INTO `invoice_service` VALUES (31, 'S002-000004', 'SVC02', 10000);
INSERT INTO `invoice_service` VALUES (32, 'S002-000004', 'SVC03', 50000);
INSERT INTO `invoice_service` VALUES (33, 'S002-000005', 'SVC07', 50000);
INSERT INTO `invoice_service` VALUES (34, 'S002-000006', 'SVC04', 5000);
INSERT INTO `invoice_service` VALUES (44, 'S002-000007', 'SVC02', 50000);
INSERT INTO `invoice_service` VALUES (45, 'S002-000008', 'SVC01', 100000);
INSERT INTO `invoice_service` VALUES (46, 'S002-000009', 'SVC01', 50000);
INSERT INTO `invoice_service` VALUES (47, 'S002-000010', 'SVC01', 50000);
INSERT INTO `invoice_service` VALUES (48, 'S002-000011', 'SVC01', 50000);
INSERT INTO `invoice_service` VALUES (49, 'S002-000011', 'SVC02', 50000);
INSERT INTO `invoice_service` VALUES (50, 'S002-000012', 'SV06', 100000);
INSERT INTO `invoice_service` VALUES (51, 'S002-000012', 'SVC05', 500000);
INSERT INTO `invoice_service` VALUES (52, 'S002-000013', 'SVC01', 100000);
INSERT INTO `invoice_service` VALUES (53, 'S002-000014', 'SVC10', 180000);
INSERT INTO `invoice_service` VALUES (54, 'S002-000015', 'SVC01', 100000);

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role`  (
  `PK_idRole` int NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_idRole`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of role
-- ----------------------------
INSERT INTO `role` VALUES (1, 'Quản lý hệ thống');
INSERT INTO `role` VALUES (2, 'Quản lý cửa hàng');
INSERT INTO `role` VALUES (3, 'Kỹ thuật viên');
INSERT INTO `role` VALUES (4, 'Khách hàng');
INSERT INTO `role` VALUES (5, 'Admin');

-- ----------------------------
-- Table structure for service
-- ----------------------------
DROP TABLE IF EXISTS `service`;
CREATE TABLE `service`  (
  `PK_idService` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `serviceName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `estimatedPrice` decimal(10, 0) NULL DEFAULT NULL,
  `estimatedTime` int NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `imageURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idService`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of service
-- ----------------------------
INSERT INTO `service` VALUES ('SV06', 'Dịch vụ 6', 100000, 60, '', NULL, '2025-11-02 01:20:05', '2025-11-02 01:20:05', 0);
INSERT INTO `service` VALUES ('SVC01', 'Dịch vụ 1 ', 100000, 30, 'Mô tả Dịch vụ 1', '/uploads/services/SVC01.jpg', '2025-09-20 17:29:30', '2025-10-09 14:28:43', 0);
INSERT INTO `service` VALUES ('SVC02', 'Dịch vụ 2', 150000, 20, 'Mô tả dịch vụ 2', '/uploads/services/SVC02.jpg', '2025-09-20 17:29:30', '2025-10-09 14:28:49', 0);
INSERT INTO `service` VALUES ('SVC03', 'Dịch vụ 3', 200000, 60, 'Mô tả dịch vụ 3', '/uploads/services/SVC03.jpg', '2025-09-20 17:29:30', '2025-10-09 14:28:54', 0);
INSERT INTO `service` VALUES ('SVC04', 'Dịch vụ 4', 300000, 40, 'Mô tả dịch vụ 4', '/uploads/services/SVC04.jpg', '2025-09-20 17:29:30', '2025-10-09 14:29:02', 0);
INSERT INTO `service` VALUES ('SVC05', 'Dịch vụ 5', 500000, 120, 'Mô tả dịch vụ 5', '/uploads/services/SVC05.jpg', '2025-09-20 17:29:30', '2025-10-09 14:29:25', 0);
INSERT INTO `service` VALUES ('SVC06', 'Dịch cụ 6', 80000, 25, 'Mô tả dịch vụ 6', '/uploads/services/SVC06.jpg', '2025-09-20 17:29:30', '2025-10-09 14:29:31', 0);
INSERT INTO `service` VALUES ('SVC07', 'Dịch vụ 7', 400000, 45, 'Mô tả dịch vụ 7', 'img/SVC07.jpg', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `service` VALUES ('SVC08', 'Dịch vụ 8', 60000, 20, 'Mô tả dịch vụ 8', 'img/SVC08.jpg', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `service` VALUES ('SVC09', 'Dịch vụ 9', 150000, 35, 'Mô tả dịch vụ 9', 'img/SVC09.jpg', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `service` VALUES ('SVC10', 'Dịch vụ 10', 180000, 40, 'Mô tả dịch vụ 10', 'img/SVC10.jpg', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);

-- ----------------------------
-- Table structure for service_sparepart
-- ----------------------------
DROP TABLE IF EXISTS `service_sparepart`;
CREATE TABLE `service_sparepart`  (
  `PK_id` int NOT NULL AUTO_INCREMENT,
  `FK_idService` int NOT NULL,
  `FK_idSparePart` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `quantity` int NOT NULL,
  PRIMARY KEY (`PK_id`) USING BTREE,
  INDEX `FK_idSparePart`(`FK_idSparePart` ASC) USING BTREE,
  INDEX `service_sparepart_ibfk_1`(`FK_idService` ASC) USING BTREE,
  CONSTRAINT `service_sparepart_ibfk_1` FOREIGN KEY (`FK_idService`) REFERENCES `invoice_service` (`PK_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `service_sparepart_ibfk_2` FOREIGN KEY (`FK_idSparePart`) REFERENCES `sparepart` (`PK_idSparePart`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 76 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of service_sparepart
-- ----------------------------
INSERT INTO `service_sparepart` VALUES (1, 1, 'P001', 1);
INSERT INTO `service_sparepart` VALUES (2, 1, 'P004', 1);
INSERT INTO `service_sparepart` VALUES (3, 2, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (4, 2, 'P004', 1);
INSERT INTO `service_sparepart` VALUES (5, 3, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (6, 3, 'P006', 1);
INSERT INTO `service_sparepart` VALUES (7, 4, 'P001', 1);
INSERT INTO `service_sparepart` VALUES (8, 4, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (9, 5, 'P004', 1);
INSERT INTO `service_sparepart` VALUES (10, 5, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (11, 6, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (12, 7, 'P001', 2);
INSERT INTO `service_sparepart` VALUES (38, 27, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (39, 28, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (40, 28, 'P007', 2);
INSERT INTO `service_sparepart` VALUES (41, 29, 'P007', 1);
INSERT INTO `service_sparepart` VALUES (42, 29, 'P008', 1);
INSERT INTO `service_sparepart` VALUES (43, 30, 'P007', 1);
INSERT INTO `service_sparepart` VALUES (44, 30, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (45, 31, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (46, 32, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (47, 33, 'P006', 1);
INSERT INTO `service_sparepart` VALUES (48, 34, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (59, 44, 'P004', 1);
INSERT INTO `service_sparepart` VALUES (60, 45, 'P007', 1);
INSERT INTO `service_sparepart` VALUES (61, 45, 'P006', 1);
INSERT INTO `service_sparepart` VALUES (62, 46, 'P001', 1);
INSERT INTO `service_sparepart` VALUES (63, 47, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (64, 48, 'P012', 1);
INSERT INTO `service_sparepart` VALUES (65, 48, 'P006', 1);
INSERT INTO `service_sparepart` VALUES (66, 49, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (67, 50, 'P002', 1);
INSERT INTO `service_sparepart` VALUES (68, 50, 'P006', 1);
INSERT INTO `service_sparepart` VALUES (69, 51, 'P008', 1);
INSERT INTO `service_sparepart` VALUES (70, 52, 'P001', 1);
INSERT INTO `service_sparepart` VALUES (71, 52, 'P003', 1);
INSERT INTO `service_sparepart` VALUES (72, 52, 'P008', 1);
INSERT INTO `service_sparepart` VALUES (73, 53, 'P001', 1);
INSERT INTO `service_sparepart` VALUES (74, 53, 'P007', 1);
INSERT INTO `service_sparepart` VALUES (75, 54, 'P002', 1);

-- ----------------------------
-- Table structure for sparepart
-- ----------------------------
DROP TABLE IF EXISTS `sparepart`;
CREATE TABLE `sparepart`  (
  `PK_idSparePart` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FK_idCategory` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sparePartName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `unit` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `purchasePrice` decimal(10, 0) NULL DEFAULT NULL,
  `salePrice` decimal(10, 0) NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idSparePart`) USING BTREE,
  INDEX `FK_idCategory`(`FK_idCategory` ASC) USING BTREE,
  CONSTRAINT `sparepart_ibfk_1` FOREIGN KEY (`FK_idCategory`) REFERENCES `sparepartcategory` (`PK_idCategory`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sparepart
-- ----------------------------
INSERT INTO `sparepart` VALUES ('111111', 'C001', '11112', '1111', 100000, 100000, '11111', '2025-10-16 15:37:39', '2025-11-05 01:56:39', 0);
INSERT INTO `sparepart` VALUES ('heee', 'C0011', '33', 'cái', 3333, 33333, 'ff', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `sparepart` VALUES ('P001', 'C001', 'Bugii ', 'cái', 20000, 30000, 'Mô tả Bugi', '2025-09-20 17:29:30', '2025-10-09 12:31:40', 0);
INSERT INTO `sparepart` VALUES ('P002', 'C001', 'Piston', 'cái', 50000, 70000, 'Mô tả Piston', '2025-09-20 17:29:30', '2025-10-09 12:13:21', 0);
INSERT INTO `sparepart` VALUES ('P003', 'C002', 'Má phanh', 'bộ', 60000, 90000, 'Mô tả Má phanh', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `sparepart` VALUES ('P004', 'C003', 'Lốp trước', 'cái', 200000, 250000, 'Mô tả Lốp trước', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `sparepart` VALUES ('P005', 'C0011', 'Ắc quy', 'cái', 400000, 500000, 'Mô tả Ắc quy', '2025-09-20 17:29:30', '2025-10-09 14:14:20', 0);
INSERT INTO `sparepart` VALUES ('P006', 'C005', 'Khung xe', 'cái', 1000000, 1200000, 'Mô tả Khung xe', '2025-09-20 17:29:30', '2025-10-08 11:28:00', 0);
INSERT INTO `sparepart` VALUES ('P007', 'C002', 'Dầu phanh', 'chai', 80000, 120000, 'Mô tả Dầu phanh', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `sparepart` VALUES ('P008', 'C003', 'Lốp sau', 'cái', 220000, 270000, 'Mô tả Lốp sau', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `sparepart` VALUES ('P009', 'C001', 'Bugi Iridium', 'Cái', 35000, 55000, 'Bugi cao cấp cho xe tay ga', '2025-10-08 16:59:31', '2025-10-08 16:59:31', 0);
INSERT INTO `sparepart` VALUES ('P010', 'C001', 'Bugi Iridium', 'Cái', 35000, 55000, 'Bugi cao cấp cho xe tay ga', '2025-10-08 17:59:34', '2025-10-08 17:59:34', 0);
INSERT INTO `sparepart` VALUES ('P011', 'C001', 'Bugi Irjidium', 'Cái', 35000, 55000, 'Bugi cao cấp cho xe tay ga', '2025-10-08 22:46:34', '2025-10-08 22:46:34', 0);
INSERT INTO `sparepart` VALUES ('P012', 'C0011', 'Dầu phanh DOT 33', 'Chai', 45000, 65000, 'Dầu phanh cao cấp, dung tích 250ml, dùng cho các loại xe phổ thông.', '2025-10-08 23:09:31', '2025-10-28 00:43:08', 0);
INSERT INTO `sparepart` VALUES ('testne', 'C004', 'Piston xe máy 3', 'cái', 10000, 100000, 'mo ta', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);

-- ----------------------------
-- Table structure for sparepartcategory
-- ----------------------------
DROP TABLE IF EXISTS `sparepartcategory`;
CREATE TABLE `sparepartcategory`  (
  `PK_idCategory` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `categoryName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idCategory`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sparepartcategory
-- ----------------------------
INSERT INTO `sparepartcategory` VALUES ('434', '45', '', '2025-11-05 01:57:38', '2025-11-05 01:57:41', 1);
INSERT INTO `sparepartcategory` VALUES ('C001', 'Động cơ', 'Mô tả Động cơ', '2025-09-20 17:29:30', '2025-11-05 01:57:34', 0);
INSERT INTO `sparepartcategory` VALUES ('C0011', 'Vành xe', '113', '2025-10-06 00:21:26', '2025-10-08 11:10:12', 0);
INSERT INTO `sparepartcategory` VALUES ('C002', 'Phanh', 'Mô tả Phanh', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `sparepartcategory` VALUES ('C003', 'Lốp', 'Mô tả Lốp', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `sparepartcategory` VALUES ('C004', 'Điện', 'Mô tả Điện', '2025-09-20 17:29:30', '2025-10-06 00:18:24', 0);
INSERT INTO `sparepartcategory` VALUES ('C005', 'Khung', 'Mô tả Khung', '2025-09-20 17:29:30', '2025-09-26 09:51:07', 1);
INSERT INTO `sparepartcategory` VALUES ('C00tt', 'test 2s', '', NULL, '2025-10-08 11:08:18', 1);
INSERT INTO `sparepartcategory` VALUES ('s', 's', 's', NULL, '2025-10-08 11:08:16', 1);
INSERT INTO `sparepartcategory` VALUES ('ss', 'a', 'a', NULL, '2025-10-06 00:21:59', 1);
INSERT INTO `sparepartcategory` VALUES ('uiew7', 'test 21', '3333', '2025-10-08 19:04:59', '2025-10-08 19:05:18', 1);

-- ----------------------------
-- Table structure for store
-- ----------------------------
DROP TABLE IF EXISTS `store`;
CREATE TABLE `store`  (
  `PK_idStore` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `imageURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idStore`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of store
-- ----------------------------
INSERT INTO `store` VALUES ('1', '1', '0312456789', NULL, '2025-10-29 10:39:02', '2025-10-29 10:52:32', 1);
INSERT INTO `store` VALUES ('S001', ' 57 Chùa Quỳnh, Quỳnh Mai, Hai Bà Trưng, Hà Nội', '0901111111', '/uploads/stores/S001.jpg', '2025-09-20 17:29:30', '2025-10-27 23:54:56', 1);
INSERT INTO `store` VALUES ('S002', '134 P. Lê Thanh Nghị, Bách Khoa, Hai Bà Trưng, Hà Nội', '0902222222', '/uploads/stores/S002.jpg', '2025-09-20 17:29:30', '2025-10-09 14:32:35', 0);
INSERT INTO `store` VALUES ('S003', '155 Lê Thanh Nghị, Hà Nội', '0123456789', '/uploads/stores/S003.jpg', '2025-09-20 17:29:30', '2025-10-09 14:25:10', 0);
INSERT INTO `store` VALUES ('S004', '599 Cầu Giấy, Hà Nội', '0123654987', '/uploads/stores/S004.jpg', '2025-09-20 17:29:30', '2025-10-09 14:25:21', 0);
INSERT INTO `store` VALUES ('S009', 'Hải Phòng', '0983404587', '/uploads/stores/S009.jpg', '2025-10-08 10:56:20', '2025-10-09 14:25:29', 0);
INSERT INTO `store` VALUES ('S011', 'Tô Hiệu', '0983404598', '/uploads/stores/S011.jpg', '2025-10-08 20:03:35', '2025-10-09 14:25:38', 0);
INSERT INTO `store` VALUES ('sss', 'Cửa hanf1', '0987456123', NULL, '2025-10-29 10:40:01', '2025-10-29 10:40:01', 0);

-- ----------------------------
-- Table structure for store_sparepart
-- ----------------------------
DROP TABLE IF EXISTS `store_sparepart`;
CREATE TABLE `store_sparepart`  (
  `PK_idSSP` int NOT NULL AUTO_INCREMENT,
  `FK_idStore` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_idSparePart` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `stockQty` int NULL DEFAULT NULL,
  `warningQty` int NULL DEFAULT 10,
  `location` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idSSP`) USING BTREE,
  INDEX `FK_idStore`(`FK_idStore` ASC) USING BTREE,
  INDEX `FK_idSparePart`(`FK_idSparePart` ASC) USING BTREE,
  CONSTRAINT `store_sparepart_ibfk_1` FOREIGN KEY (`FK_idStore`) REFERENCES `store` (`PK_idStore`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `store_sparepart_ibfk_2` FOREIGN KEY (`FK_idSparePart`) REFERENCES `sparepart` (`PK_idSparePart`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11122 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of store_sparepart
-- ----------------------------
INSERT INTO `store_sparepart` VALUES (1, 'S001', 'P001', 100, 10, 'Kệ A1', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (2, 'S001', 'P002', 80, 10, 'Kệ A2', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (3, 'S001', 'P003', 70, 10, 'Kệ A3', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (4, 'S001', 'P004', 60, 10, 'Kệ B1', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (5, 'S001', 'P005', 50, 10, 'Kệ B2', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (6, 'S001', 'P006', 40, 10, 'Kệ B3', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (7, 'S001', 'P007', 90, 10, 'Kệ C1', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (8, 'S001', 'P008', 55, 10, 'Kệ C2', '2025-10-04 21:56:45', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (9, 'S002', 'P001', 153, 10, 'Kệ A22222', '2025-10-04 21:56:45', '2025-11-03 23:57:28', 0);
INSERT INTO `store_sparepart` VALUES (10, 'S002', 'P002', 110, 10, 'Kệ A21', '2025-10-04 21:56:45', '2025-11-04 00:03:10', 0);
INSERT INTO `store_sparepart` VALUES (11, 'S002', 'P003', 159, 10, 'Kệ A3', '2025-10-04 21:56:45', '2025-11-03 14:13:07', 0);
INSERT INTO `store_sparepart` VALUES (12, 'S002', 'P004', 50, 10, 'Kệ B1', '2025-10-04 21:56:45', '2025-10-27 11:31:01', 0);
INSERT INTO `store_sparepart` VALUES (13, 'S002', 'P005', 94, 10, 'Kệ B2', '2025-10-04 21:56:45', '2025-10-25 14:12:38', 0);
INSERT INTO `store_sparepart` VALUES (14, 'S002', 'P006', 51, 10, 'Kệ B3', '2025-10-04 21:56:45', '2025-11-03 00:03:26', 0);
INSERT INTO `store_sparepart` VALUES (15, 'S002', 'P007', 176, 10, 'Kệ C1', '2025-10-04 21:56:45', '2025-11-03 23:57:28', 0);
INSERT INTO `store_sparepart` VALUES (16, 'S002', 'P008', 47, 10, 'Kệ C2', '2025-10-04 21:56:45', '2025-11-03 14:13:07', 0);
INSERT INTO `store_sparepart` VALUES (17, 'S003', 'P001', 80, 10, 'Kệ A1', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (18, 'S003', 'P002', 60, 10, 'Kệ A2', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (19, 'S003', 'P003', 55, 10, 'Kệ A3', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (20, 'S003', 'P004', 45, 10, 'Kệ B1', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (21, 'S003', 'P005', 35, 10, 'Kệ B2', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (22, 'S003', 'P006', 30, 10, 'Kệ B3', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (23, 'S003', 'P007', 75, 10, 'Kệ C1', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (24, 'S003', 'P008', 45, 10, 'Kệ C2', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (25, 'S004', 'P001', 70, 10, 'Kệ A1', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (26, 'S004', 'P002', 55, 10, 'Kệ A2', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (27, 'S004', 'P003', 50, 10, 'Kệ A3', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (28, 'S004', 'P004', 40, 10, 'Kệ B1', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (29, 'S004', 'P005', 30, 10, 'Kệ B2', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (30, 'S004', 'P006', 25, 10, 'Kệ B3', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (31, 'S004', 'P007', 65, 10, 'Kệ C1', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (32, 'S004', 'P008', 40, 10, 'Kệ C2', '2025-10-04 21:56:45', '2025-10-04 21:56:45', 0);
INSERT INTO `store_sparepart` VALUES (35, 'S001', 'P009', 0, 10, 'Chưa xác định', '2025-10-08 16:59:31', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (36, 'S002', 'P009', 10, 10, 'Chưa xác định', '2025-10-08 16:59:31', '2025-10-08 16:59:31', 0);
INSERT INTO `store_sparepart` VALUES (37, 'S003', 'P009', 0, 10, 'Chưa xác định', '2025-10-08 16:59:31', '2025-10-08 16:59:31', 0);
INSERT INTO `store_sparepart` VALUES (38, 'S004', 'P009', 0, 10, 'Chưa xác định', '2025-10-08 16:59:31', '2025-10-08 16:59:31', 0);
INSERT INTO `store_sparepart` VALUES (39, 'S009', 'P009', 0, 10, 'Chưa xác định', '2025-10-08 16:59:31', '2025-10-08 16:59:31', 0);
INSERT INTO `store_sparepart` VALUES (43, 'S001', 'P010', 0, 10, 'Chưa xác định', '2025-10-08 17:59:34', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (44, 'S002', 'P010', 10, 10, 'Chưa xác định', '2025-10-08 17:59:34', '2025-10-08 17:59:34', 0);
INSERT INTO `store_sparepart` VALUES (45, 'S003', 'P010', 0, 10, 'Chưa xác định', '2025-10-08 17:59:34', '2025-10-08 17:59:34', 0);
INSERT INTO `store_sparepart` VALUES (46, 'S004', 'P010', 0, 10, 'Chưa xác định', '2025-10-08 17:59:34', '2025-10-08 17:59:34', 0);
INSERT INTO `store_sparepart` VALUES (51, 'S001', 'P011', 0, 10, 'Chưa xác định', '2025-10-08 22:46:34', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (52, 'S002', 'P011', 0, 10, 'Chưa xác định', '2025-10-08 22:46:34', '2025-10-08 22:46:34', 0);
INSERT INTO `store_sparepart` VALUES (53, 'S003', 'P011', 0, 10, 'Chưa xác định', '2025-10-08 22:46:34', '2025-10-08 22:46:34', 0);
INSERT INTO `store_sparepart` VALUES (54, 'S004', 'P011', 0, 10, 'Chưa xác định', '2025-10-08 22:46:34', '2025-10-08 22:46:34', 0);
INSERT INTO `store_sparepart` VALUES (55, 'S009', 'P011', 0, 10, 'Chưa xác định', '2025-10-08 22:46:34', '2025-10-08 22:46:34', 0);
INSERT INTO `store_sparepart` VALUES (56, 'S011', 'P011', 0, 10, 'Chưa xác định', '2025-10-08 22:46:34', '2025-10-08 22:46:34', 0);
INSERT INTO `store_sparepart` VALUES (59, 'S001', 'P012', 0, 10, 'Chưa xác định', '2025-10-08 23:09:31', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (60, 'S002', 'P012', 100, 10, 'Chưa xác định', '2025-10-08 23:09:31', '2025-10-08 23:09:31', 0);
INSERT INTO `store_sparepart` VALUES (61, 'S003', 'P012', 0, 10, 'Chưa xác định', '2025-10-08 23:09:31', '2025-10-08 23:09:31', 0);
INSERT INTO `store_sparepart` VALUES (62, 'S004', 'P012', 0, 10, 'Chưa xác định', '2025-10-08 23:09:31', '2025-10-08 23:09:31', 0);
INSERT INTO `store_sparepart` VALUES (63, 'S009', 'P012', 0, 10, 'Chưa xác định', '2025-10-08 23:09:31', '2025-10-08 23:09:31', 0);
INSERT INTO `store_sparepart` VALUES (64, 'S011', 'P012', 0, 10, 'Chưa xác định', '2025-10-08 23:09:31', '2025-10-08 23:09:31', 0);
INSERT INTO `store_sparepart` VALUES (67, 'S001', 'heee', 0, 10, 'Chưa xác định', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `store_sparepart` VALUES (68, 'S002', 'heee', 0, 10, 'Chưa xác định', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `store_sparepart` VALUES (69, 'S003', 'heee', 0, 10, 'Chưa xác định', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `store_sparepart` VALUES (70, 'S004', 'heee', 0, 10, 'Chưa xác định', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `store_sparepart` VALUES (71, 'S009', 'heee', 0, 10, 'Chưa xác định', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `store_sparepart` VALUES (72, 'S011', 'heee', 0, 10, 'Chưa xác định', '2025-10-08 23:14:09', '2025-10-09 09:00:59', 1);
INSERT INTO `store_sparepart` VALUES (75, 'S001', 'testne', 0, 10, 'Chưa xác định', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);
INSERT INTO `store_sparepart` VALUES (76, 'S002', 'testne', 0, 10, 'Chưa xác định', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);
INSERT INTO `store_sparepart` VALUES (77, 'S003', 'testne', 0, 10, 'Chưa xác định', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);
INSERT INTO `store_sparepart` VALUES (78, 'S004', 'testne', 0, 10, 'Chưa xác định', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);
INSERT INTO `store_sparepart` VALUES (79, 'S009', 'testne', 0, 10, 'Chưa xác định', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);
INSERT INTO `store_sparepart` VALUES (80, 'S011', 'testne', 0, 10, 'Chưa xác định', '2025-10-08 23:25:11', '2025-10-09 08:55:56', 1);
INSERT INTO `store_sparepart` VALUES (81, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0);
INSERT INTO `store_sparepart` VALUES (82, 'S001', '111111', 0, 10, 'Chưa xác định', '2025-10-16 15:37:39', '2025-10-27 23:54:55', 1);
INSERT INTO `store_sparepart` VALUES (83, 'S002', '111111', 10, 10, 'Chưa xác địnhh', '2025-10-16 15:37:39', '2025-10-16 15:37:39', 0);
INSERT INTO `store_sparepart` VALUES (84, 'S003', '111111', 0, 10, 'Chưa xác định', '2025-10-16 15:37:39', '2025-10-16 15:37:39', 0);
INSERT INTO `store_sparepart` VALUES (85, 'S004', '111111', 0, 10, 'Chưa xác định', '2025-10-16 15:37:39', '2025-10-16 15:37:39', 0);
INSERT INTO `store_sparepart` VALUES (86, 'S009', '111111', 0, 10, 'Chưa xác định', '2025-10-16 15:37:39', '2025-10-16 15:37:39', 0);
INSERT INTO `store_sparepart` VALUES (87, 'S011', '111111', 0, 10, 'Chưa xác định', '2025-10-16 15:37:39', '2025-10-16 15:37:39', 0);
INSERT INTO `store_sparepart` VALUES (11112, 'sss', '111111', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11113, 'sss', 'P012', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11114, 'sss', 'P011', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11115, 'sss', 'P010', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11116, 'sss', 'P009', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11117, 'sss', 'P001', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11118, 'sss', 'P002', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11119, 'sss', 'P003', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11120, 'sss', 'P004', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);
INSERT INTO `store_sparepart` VALUES (11121, 'sss', 'P005', 0, 5, 'Kho sss', '2025-10-29 10:40:02', '2025-10-29 10:40:02', 0);

-- ----------------------------
-- Table structure for supplier
-- ----------------------------
DROP TABLE IF EXISTS `supplier`;
CREATE TABLE `supplier`  (
  `PK_idSupplier` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `supplierName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idSupplier`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of supplier
-- ----------------------------
INSERT INTO `supplier` VALUES ('3', '3', '3', '3@gmail.com', '0901000001', '2025-10-08 23:51:43', '2025-10-08 23:51:47', 1);
INSERT INTO `supplier` VALUES ('SUP01', 'Công ty A', 'Hà Nội', 'supA@gmail.com', '0911111122', '2025-09-20 17:29:30', '2025-10-08 23:51:19', 0);
INSERT INTO `supplier` VALUES ('SUP02', 'Công ty B', 'HCM', 'supB@gmail.com', '0922222222', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `supplier` VALUES ('SUP03', 'Công ty C', 'Đà Nẵng', 'supC@gmail.com', '0933333333', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `supplier` VALUES ('SUP04', 'Công ty D', 'Cần Thơ', 'supD@gmail.com', '0944444445', '2025-09-20 17:29:30', '2025-10-05 22:32:07', 0);
INSERT INTO `supplier` VALUES ('SUP05', 'Công ty E', 'Huế', 'supE@gmail.com', '0955555555', '2025-09-20 17:29:30', '2025-10-05 15:42:04', 1);
INSERT INTO `supplier` VALUES ('SUP07', 'Công ty TNHH ABC', 'ngách 32 ngõ 197 hoàng mai', 'khangsnd163@gmail.com', '0983404591', NULL, NULL, 0);
INSERT INTO `supplier` VALUES ('SUP08', 'Công ty TNHH ABC', 'ngách 32 ngõ 197 hoàng mai', 'khangsnd163@gmail.com', '0901000001', '2025-10-06 00:18:06', '2025-10-06 00:18:06', 0);

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `PK_idUser` int NOT NULL AUTO_INCREMENT,
  `fullName` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `gender` tinyint(1) NULL DEFAULT NULL,
  `birthDate` date NULL DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `FK_idRole` int NULL DEFAULT NULL,
  `FK_idStore` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idUser`) USING BTREE,
  INDEX `FK_idRole`(`FK_idRole` ASC) USING BTREE,
  INDEX `FK_idStore`(`FK_idStore` ASC) USING BTREE,
  CONSTRAINT `user_ibfk_1` FOREIGN KEY (`FK_idRole`) REFERENCES `role` (`PK_idRole`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `user_ibfk_2` FOREIGN KEY (`FK_idStore`) REFERENCES `store` (`PK_idStore`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 61 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, 'Trần Thị a', 'a@company.com', '0901000001', '$2y$10$DEC80ALdgjZBHsdsxuy.7.rVtufsxnBGYddPPTFgEJtaGfJcIJrZS', 1, '1990-01-01', 'Địa chỉ 1', 2, 'S001', '2025-09-20 17:29:30', '2025-11-05 01:57:52', 0);
INSERT INTO `user` VALUES (2, 'Trần Thị BB', 'b@company.com', '0901000002', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 1, '1990-01-01', 'Địa chỉ 2', 3, 'S001', '2025-09-20 17:29:30', '2025-10-13 00:26:18', 0);
INSERT INTO `user` VALUES (3, 'Lê Văn C', 'c@company.com', '0901000003', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 0, '1990-01-01', 'Địa chỉ 3', 3, 'S001', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `user` VALUES (4, 'Phạm Thị D', 'd@company.com', '0901000004', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 1, '1990-01-01', 'Địa chỉ 4', 3, 'S001', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `user` VALUES (5, 'Vũ Văn E', 'e@company.com', '0901000005', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 0, '1990-01-01', 'Địa chỉ 5', 2, 'S002', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `user` VALUES (6, 'Hoàng Thị F', 'f@company.com', '0901000006', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 1, '1990-01-01', 'Địa chỉ 6', 3, 'S002', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `user` VALUES (7, 'Đỗ Văn G', 'g@company.com', '0901000007', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 0, '1990-01-01', 'Địa chỉ 7', 3, 'S002', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `user` VALUES (8, 'Phan Thị H', 'h@company.com', '0901000008', '$2y$10$heZ97R62XnfRugpFfix6OeRwpatgFgUsbnau311ohnmSfJGQc1.zm', 1, '1990-01-01', 'Địa chỉ 8', 3, 'S003', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `user` VALUES (9, 'DT Thương', 'duongthithuong10a9@gmail.com', '0386866714', '$2y$10$I6fCasqD9KiKyUWEYTGw8u.tFJTFrF77Ezsy0rOeRmi7i3bBVpfPu', 1, '2025-09-02', 'Quảng Yên', 3, 'S003', '2025-09-29 09:30:35', '2025-09-29 09:30:35', 0);
INSERT INTO `user` VALUES (10, 'Phạm Tuấn Khang 1', 'rykrax.ksnd.1122@gmail.com', '0983404598', '$2y$10$l/xZZcLuueshmYVCfdCdHe/og3LXuWZ/VH0bRBn3qM/OTZ1iTSPAe', NULL, NULL, NULL, 4, 'S002', '2025-10-05 14:08:29', '2025-10-05 14:08:29', 0);
INSERT INTO `user` VALUES (11, 'Phạm Tuấn Khang 2', 'rykrax.ksnd.1122@gmail.co', '0983404595', '$2y$10$n0.A73eRAQTqPHOcL9rVRuPpxihJyXrs0YnE452vmoS2NYqowKMo6', NULL, NULL, NULL, 3, 'S003', '2025-10-05 17:28:12', '2025-10-05 17:28:12', 0);
INSERT INTO `user` VALUES (12, 'Admin', 'admin@gmail.com', '0555555555', '$2y$10$rpqOvMS8zQ/OwkeSTTFgIeSqbwfznjSRkZtCxG8Ke.NcQPSv3HSzi', NULL, NULL, NULL, 5, NULL, '2025-10-10 12:57:51', '2025-10-10 12:57:51', 0);
INSERT INTO `user` VALUES (13, 'QL hệ thống', 'qlhethong@gmail.com', '0311111111', '$2y$10$LuyP0XjU86zcAq574du4Mu90SkksC.2y0GF8Tc4nr4o1vXUjzrZP6', 0, '2000-06-15', 'Hà Nội', 1, 'S009', '2025-10-10 12:59:13', '2025-10-10 12:59:13', 0);
INSERT INTO `user` VALUES (14, 'QL cửa hàngg', 'qlcuahang@gmail.com', '0322222222', '$2y$10$/wiek0MNO/VHjL21GA0CRujRwzKA8odrVWhxdIiiKItJYI5BQb0ju', 0, '2000-06-15', 'Hà Nội', 2, 'S002', '2025-10-10 12:59:33', '2025-10-10 12:59:33', 0);
INSERT INTO `user` VALUES (15, 'KTV', 'kythuatvien@gmail.com', '0333333333', '$2y$10$RyeVOX8WttYNnR/9W7KZxOI1yW0op/oevsa3bvnTMp.hXi5ykqem2', 0, '2000-06-15', 'Hà Nội', 3, 'S002', '2025-10-10 13:00:19', '2025-10-27 14:53:15', 0);
INSERT INTO `user` VALUES (16, 'Khách', 'khachhang@gmail.com', '0386866777', '$2y$10$4ogvRJAb.7Lxs4NSOSBO6eC74Cs3ScLwsPTHixKLlTTWvAM8lXsZm', 1, '2000-11-04', 'Hà Nội', 4, NULL, '2025-10-10 13:00:35', '2025-11-04 20:33:06', 0);
INSERT INTO `user` VALUES (17, 'Dương Thị Thương', 'thuongryu@gmail.com', '0386866713', '$2y$10$3hw3baD5a1t14aHIGeuca.8CNuyNfLSnT60gumZW5wUmIf0B148Cu', 0, '1999-11-11', 'Quảng Yên', 1, NULL, '2025-10-13 10:56:11', '2025-10-13 10:56:11', 0);
INSERT INTO `user` VALUES (18, 'ưesf', 'bb@company.com', '0386866722', '$2y$10$BwNfJbeqNH34iwG1tcyFLueN1F2CQkDWV6LYYLmFy3SiDhKBUwuqu', 0, '2000-01-01', 'Quảng Yên', 2, 'S004', '2025-10-13 10:56:36', '2025-10-13 10:56:36', 0);
INSERT INTO `user` VALUES (47, 'test', 'test@gmail.com', '0386866715', '$2y$10$TMhESQVvMmG.bAcfM35cQOT3d0kUJzNatvcythi.CfRRirFt36.oi', NULL, NULL, NULL, 4, NULL, '2025-10-22 16:38:19', '2025-10-27 22:23:00', 0);
INSERT INTO `user` VALUES (50, 'Thương', 'thg@gmail.com', '0386866711', '$2y$10$N082uWvPukjkwC31E8RXve/EiEjn9FuLEO1PbBoVB179MArHzB9NS', NULL, NULL, NULL, 4, NULL, '2025-10-22 23:06:29', '2025-10-22 23:22:17', 0);
INSERT INTO `user` VALUES (51, 'Anh Bình', NULL, '0386866710', NULL, NULL, NULL, NULL, 4, NULL, '2025-10-22 23:09:30', '2025-10-22 23:09:30', 0);
INSERT INTO `user` VALUES (52, 'Mai Lan', NULL, '0386866123', NULL, NULL, NULL, NULL, 4, NULL, '2025-10-22 23:59:39', '2025-10-22 23:59:39', 0);
INSERT INTO `user` VALUES (53, 'Dương Văn Nam', NULL, '0386866712', NULL, NULL, NULL, NULL, 4, NULL, '2025-10-25 13:58:00', '2025-10-25 13:58:00', 0);
INSERT INTO `user` VALUES (58, 'Kim Lan', 'lan@gmail.com', '0386866999', '$2y$10$qRrTqJxnVItGlcq0LD1yr.d0bmiGpnPv2cPinxcA3WkpQRgQ8chq2', NULL, NULL, NULL, 4, NULL, '2025-10-27 22:22:27', '2025-10-27 22:22:27', 0);
INSERT INTO `user` VALUES (59, 'Dương Thị Thương', 'ttt@gmail.com', '0386866733', '$2y$10$95xLKbAzOh5157E1EMSISuFcvbW5qZPdBU3aYeO4/DUxFTxf4pf.a', NULL, NULL, NULL, 4, NULL, '2025-10-28 00:00:24', '2025-10-28 00:00:24', 0);
INSERT INTO `user` VALUES (60, 'KH mới', NULL, '0321654987', NULL, NULL, NULL, NULL, 4, NULL, '2025-11-03 23:57:28', '2025-11-03 23:57:28', 0);

-- ----------------------------
-- Table structure for vehicle
-- ----------------------------
DROP TABLE IF EXISTS `vehicle`;
CREATE TABLE `vehicle`  (
  `PK_idVehicle` int NOT NULL AUTO_INCREMENT,
  `FK_idUser` int NULL DEFAULT NULL,
  `licensePlate` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `created` datetime NULL DEFAULT NULL,
  `updated` datetime NULL DEFAULT NULL,
  `deleted` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`PK_idVehicle`) USING BTREE,
  INDEX `FK_idUser`(`FK_idUser` ASC) USING BTREE,
  CONSTRAINT `vehicle_ibfk_1` FOREIGN KEY (`FK_idUser`) REFERENCES `user` (`PK_idUser`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of vehicle
-- ----------------------------
INSERT INTO `vehicle` VALUES (1, 16, '29A1-10011', 'Vision', '2025-09-20 17:29:30', '2025-10-29 23:15:26', 0);
INSERT INTO `vehicle` VALUES (2, 2, '29A-1002', 'Vision', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `vehicle` VALUES (3, 3, '29A-1003', 'SH', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `vehicle` VALUES (4, 4, '29A-1004', 'Wave', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `vehicle` VALUES (5, 12, '29A-1005', 'Vision', '2025-09-20 17:29:30', '2025-09-20 17:29:30', 0);
INSERT INTO `vehicle` VALUES (6, 16, '14H1-12345', 'Wave', '2025-10-14 23:53:21', '2025-10-27 14:08:47', 0);
INSERT INTO `vehicle` VALUES (7, 16, '1', '1', '2025-10-14 23:56:33', '2025-10-27 11:33:38', 1);
INSERT INTO `vehicle` VALUES (8, 16, '2', '2', '2025-10-15 00:01:49', '2025-10-16 18:13:45', 1);
INSERT INTO `vehicle` VALUES (9, 14, 'd', 'd', '2025-10-15 00:03:53', '2025-10-15 00:03:53', 0);
INSERT INTO `vehicle` VALUES (10, 16, '22', '22', '2025-10-16 18:13:49', '2025-10-16 18:13:49', 0);
INSERT INTO `vehicle` VALUES (11, 16, 'test', 'test', '2025-10-21 19:15:31', '2025-10-21 19:16:19', 1);
INSERT INTO `vehicle` VALUES (35, 47, '11H1-12345', 'Wave', NULL, NULL, 0);
INSERT INTO `vehicle` VALUES (38, 50, '29xxx', 'Vision', NULL, NULL, 0);
INSERT INTO `vehicle` VALUES (39, 51, '29H-12345', 'Wave', NULL, NULL, 0);
INSERT INTO `vehicle` VALUES (40, 16, '14xxxx', 'Wave', '2025-10-22 23:16:59', '2025-11-04 19:59:22', 0);
INSERT INTO `vehicle` VALUES (41, 52, '14xxxx', 'SH Mode', '2025-10-22 23:59:39', '2025-10-22 23:59:39', 0);
INSERT INTO `vehicle` VALUES (42, 53, '29D12345', 'SH', '2025-10-25 13:58:00', '2025-10-25 13:58:00', 0);
INSERT INTO `vehicle` VALUES (43, 16, '15B2-25554', 'SH mode', '2025-10-27 14:10:01', '2025-10-27 14:10:01', 0);
INSERT INTO `vehicle` VALUES (44, 15, '29D1-22200', 'Lead', '2025-10-27 22:56:39', '2025-10-27 22:56:39', 0);
INSERT INTO `vehicle` VALUES (45, 13, '12D1-12345', 'Wave alpha 10', '2025-10-29 23:20:29', '2025-10-29 23:20:29', 0);
INSERT INTO `vehicle` VALUES (46, 13, 'test2', 'test2', '2025-10-29 23:21:02', '2025-10-29 23:21:02', 0);
INSERT INTO `vehicle` VALUES (47, 60, '11X1-12345', 'Wave', '2025-11-03 23:57:28', '2025-11-03 23:57:28', 0);
INSERT INTO `vehicle` VALUES (48, 16, '1111', '1111', '2025-11-04 19:59:36', '2025-11-04 20:01:40', 1);
INSERT INTO `vehicle` VALUES (49, 16, '11', '11', '2025-11-04 20:23:43', '2025-11-04 20:23:43', 0);
INSERT INTO `vehicle` VALUES (50, 16, '1111', '1111', '2025-11-04 20:23:48', '2025-11-04 20:23:48', 0);

-- ----------------------------
-- Procedure structure for AddNewSparePart
-- ----------------------------
DROP PROCEDURE IF EXISTS `AddNewSparePart`;
delimiter ;;
CREATE PROCEDURE `AddNewSparePart`(IN p_PK_idSparePart VARCHAR(20),
    IN p_FK_idCategory VARCHAR(20),
    IN p_sparePartName VARCHAR(255),
    IN p_unit VARCHAR(50),
    IN p_purchasePrice DECIMAL(10, 0),
    IN p_salePrice DECIMAL(10, 0),
    IN p_description TEXT,
    IN p_initialStockQty INT,
		IN p_location VARCHAR(255))
BEGIN
    -- Bắt đầu một giao dịch để đảm bảo tất cả các lệnh INSERT
    -- hoặc thành công, hoặc thất bại cùng nhau.
    START TRANSACTION;

    INSERT INTO sparepart (
        PK_idSparePart, FK_idCategory, sparePartName, unit,
        purchasePrice, salePrice, description, created, updated, deleted
    ) VALUES (
        p_PK_idSparePart, p_FK_idCategory, p_sparePartName, p_unit,
        p_purchasePrice, p_salePrice, p_description, NOW(), NOW(), 0
    );

    INSERT INTO store_sparepart (
        FK_idStore, FK_idSparePart, stockQty, warningQty,
        location, created, updated, deleted
    ) VALUES (
        'KHO', 
        p_PK_idSparePart,
        p_initialStockQty,
        10,
        IF(p_location IS NULL OR p_location = '', 'Chưa xác định', p_location),
        NOW(), NOW(), 0
    );

    INSERT INTO store_sparepart (
        FK_idStore, FK_idSparePart, stockQty, warningQty,
        location, created, updated, deleted
    )
    SELECT
        s.PK_idStore,
        p_PK_idSparePart,
        0,
        10,
        'Chưa xác định',
        NOW(), NOW(), 0
    FROM
        store s
    WHERE
        s.PK_idStore <> 'KHO' AND s.deleted = 0;

    COMMIT;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for AddNewSparePart_DetailedErrors
-- ----------------------------
DROP PROCEDURE IF EXISTS `AddNewSparePart_DetailedErrors`;
delimiter ;;
CREATE PROCEDURE `AddNewSparePart_DetailedErrors`(IN p_PK_idSparePart VARCHAR(20),
    IN p_FK_idCategory VARCHAR(20),
    IN p_sparePartName VARCHAR(255),
    IN p_unit VARCHAR(50),
    IN p_purchasePrice DECIMAL(10, 0),
    IN p_salePrice DECIMAL(10, 0),
    IN p_description TEXT,
    IN p_initialStockQty INT)
BEGIN
    -- BƯỚC 1: KHAI BÁO BIẾN CHO THÔNG BÁO LỖI
    DECLARE errorMessage VARCHAR(255);

    -- Handler chung vẫn giữ lại để bắt các lỗi không lường trước
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Đã có lỗi không xác định xảy ra. Dữ liệu đã được hoàn tác.';
    END;

    -- BƯỚC 2: KIỂM TRA DỮ LIỆU ĐẦU VÀO VÀ GÁN LỖI VÀO BIẾN
    -- Kiểm tra xem mã phụ tùng đã tồn tại chưa
    IF EXISTS (SELECT 1 FROM sparepart WHERE PK_idSparePart = p_PK_idSparePart) THEN
        -- Gán thông báo lỗi vào biến
        SET errorMessage = CONCAT('Lỗi: Mã phụ tùng "', p_PK_idSparePart, '" đã tồn tại trong hệ thống.');
        -- Ném lỗi bằng biến
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = errorMessage;
    END IF;

    -- Kiểm tra xem mã danh mục có hợp lệ không (giả sử bạn có bảng `category`)
    IF NOT EXISTS (SELECT 1 FROM category WHERE PK_idCategory = p_FK_idCategory) THEN
        -- Gán thông báo lỗi vào biến
        SET errorMessage = CONCAT('Lỗi: Mã danh mục "', p_FK_idCategory, '" không tồn tại.');
        -- Ném lỗi bằng biến
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = errorMessage;
    END IF;

    -- Bắt đầu giao dịch chỉ khi dữ liệu đầu vào hợp lệ
    START TRANSACTION;

    -- Thêm phụ tùng mới vào bảng `sparepart`
    INSERT INTO sparepart (
        PK_idSparePart, FK_idCategory, sparePartName, unit, 
        purchasePrice, salePrice, description, created, updated, deleted
    ) VALUES (
        p_PK_idSparePart, p_FK_idCategory, p_sparePartName, p_unit, 
        p_purchasePrice, p_salePrice, p_description, NOW(), NOW(), 0
    );

    -- Thêm phụ tùng vào KHO TỔNG với số lượng ban đầu
    INSERT INTO store_sparepart (
        FK_idStore, FK_idSparePart, stockQty, warningQty, 
        location, created, updated, deleted
    ) VALUES (
        'KHO', -- Mã kho tổng
        p_PK_idSparePart,
        p_initialStockQty,
        10,
        'Chưa xác định',
        NOW(), NOW(), 0
    );

    -- Thêm phụ tùng vào các cửa hàng khác
    INSERT INTO store_sparepart (
        FK_idStore, FK_idSparePart, stockQty, warningQty, 
        location, created, updated, deleted
    )
    SELECT
        s.PK_idStore,
        p_PK_idSparePart,
        0, 
        10, 
        'Chưa xác định',
        NOW(), NOW(), 0
    FROM
        store s
    WHERE
        s.PK_idStore <> 'KHO' AND s.deleted = 0;

    COMMIT;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for DeleteSparePart
-- ----------------------------
DROP PROCEDURE IF EXISTS `DeleteSparePart`;
delimiter ;;
CREATE PROCEDURE `DeleteSparePart`(IN p_PK_idSparePart VARCHAR(20))
BEGIN
    -- Khai báo một handler để bắt lỗi. Nếu có bất kỳ lỗi nào xảy ra,
    -- toàn bộ giao dịch sẽ được ROLLBACK (hoàn tác).
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Xóa phụ tùng thất bại. Dữ liệu đã được hoàn tác.';
    END;

    -- Kiểm tra xem phụ tùng có tồn tại và chưa bị xóa hay không.
    IF NOT EXISTS (SELECT 1 FROM sparepart WHERE PK_idSparePart = p_PK_idSparePart AND deleted = 0) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Không tìm thấy phụ tùng để xóa hoặc phụ tùng đã bị xóa trước đó.';
    END IF;

    -- Bắt đầu một giao dịch để đảm bảo tính toàn vẹn dữ liệu.
    START TRANSACTION;

    -- BƯỚC 1: Đánh dấu "đã xóa" cho phụ tùng trong bảng thông tin chung.
    -- Thao tác này sẽ ẩn phụ tùng khỏi các chức năng chung của hệ thống.
    UPDATE sparepart
    SET
        deleted = 1,
        updated = NOW()
    WHERE
        PK_idSparePart = p_PK_idSparePart;

    -- BƯỚC 2: Đánh dấu "đã xóa" cho phụ tùng đó ở TẤT CẢ các kho và cửa hàng.
    -- Thao tác này đảm bảo phụ tùng không còn xuất hiện trong kho của bất kỳ cửa hàng nào.
    UPDATE store_sparepart
    SET
        deleted = 1,
        updated = NOW()
    WHERE
        FK_idSparePart = p_PK_idSparePart;

    -- Nếu tất cả các lệnh trên thành công, COMMIT để lưu lại thay đổi.
    COMMIT;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for GetImportRequestsByAccessLevel
-- ----------------------------
DROP PROCEDURE IF EXISTS `GetImportRequestsByAccessLevel`;
delimiter ;;
CREATE PROCEDURE `GetImportRequestsByAccessLevel`(IN p_roleName VARCHAR(50),
    IN p_storeId VARCHAR(20),
    IN p_searchTerm VARCHAR(100),
    IN p_filterStoreId VARCHAR(20),
    IN p_status VARCHAR(20),
    IN p_limit INT,
    IN p_offset INT)
BEGIN
    -- Định nghĩa ID của kho tổng
    DECLARE v_warehouseId VARCHAR(20) DEFAULT 'KHO';

    SELECT
        ir.PK_idRequest,
        ir.FK_idStore,
        ir.FK_idCreatedBy,
        ir.reason,
        ir.status,
        ir.created,
        s.address AS storeAddress,
        u.fullName AS createdByFullName
    FROM
        importrequest ir
    LEFT JOIN store s ON s.PK_idStore = ir.FK_idStore
    LEFT JOIN user u ON u.PK_idUser = ir.FK_idCreatedBy
    WHERE
        ir.deleted = 0 -- Yêu cầu phải còn hoạt động
        
        -- ==========================================================
        -- LOGIC PHÂN QUYỀN TRUY CẬP (ACCESS CONTROL)
        -- ==========================================================
        AND
        (
            -- 1. Bộ phận kho và Admin: Xem toàn bộ
            p_roleName IN ('Bộ phận kho', 'Admin') 
            
            -- 2. Quản lý cửa hàng: Chỉ xem yêu cầu của cửa hàng mình
            OR (p_roleName = 'Quản lý cửa hàng' AND ir.FK_idStore = p_storeId)
            
            -- 3. Kế toán: Chỉ xem yêu cầu của kho tổng ('KHO')
            OR (p_roleName = 'Kế toán' AND ir.FK_idStore = v_warehouseId)
        )
        
        -- ==========================================================
        -- LOGIC BỘ LỌC TÙY CHỌN TỪ FRONTEND (UI Filters)
        -- ==========================================================
        
        -- Lọc theo Store ID
        AND (
            p_filterStoreId IS NULL OR p_filterStoreId = '' OR p_filterStoreId = 'all'
            OR ir.FK_idStore = p_filterStoreId
        )

        -- Lọc theo Status
        AND (
            p_status IS NULL OR p_status = '' OR p_status = 'all'
            OR ir.status = p_status
        )
        
        -- Tìm kiếm theo Mã yêu cầu hoặc Tên đầy đủ người tạo
        AND (
            p_searchTerm IS NULL OR p_searchTerm = '' 
            OR ir.PK_idRequest LIKE CONCAT('%', p_searchTerm, '%')
            OR u.fullName LIKE CONCAT('%', p_searchTerm, '%')
        )
    ORDER BY
        ir.created DESC
    LIMIT p_limit OFFSET p_offset;
END
;;
delimiter ;

-- ----------------------------
-- Procedure structure for GetSparePartsByAccessLevel
-- ----------------------------
DROP PROCEDURE IF EXISTS `GetSparePartsByAccessLevel`;
delimiter ;;
CREATE PROCEDURE `GetSparePartsByAccessLevel`(IN `p_idUser` INT)
BEGIN
    DECLARE v_user_role_id INT;
    DECLARE v_user_store_id VARCHAR(20);

    SELECT FK_idRole, FK_idStore INTO v_user_role_id, v_user_store_id
    FROM `user`
    WHERE PK_idUser = p_idUser;

    SELECT
        -- Các cột từ bảng store_sparepart (ssp)
        ssp.PK_idSSP,
        ssp.FK_idStore,
        ssp.FK_idSparePart,
        ssp.stockQty,
        ssp.warningQty,
        ssp.location,
        ssp.created,
        ssp.updated,
        ssp.deleted,
        
        -- Các cột từ bảng store (s)
        s.address AS storeAddress,
        
        -- Các cột từ bảng sparepart (sp)
				sp.PK_idSparePart, 
        sp.FK_idCategory,
        sp.sparePartName,
        sp.unit,
        sp.purchasePrice,
        sp.salePrice,
        sp.description
    FROM
        store_sparepart AS ssp
    JOIN
        sparepart AS sp ON ssp.FK_idSparePart = sp.PK_idSparePart
    JOIN
        store AS s ON ssp.FK_idStore = s.PK_idStore
    WHERE
        sp.deleted = 0
        AND ssp.deleted = 0
        AND s.deleted = 0
				AND (
            v_user_role_id IN (1, 2, 5, 10) OR ssp.FK_idStore = v_user_store_id
        );
END
;;
delimiter ;

SET FOREIGN_KEY_CHECKS = 1;
