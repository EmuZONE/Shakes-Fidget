/*
Navicat MySQL Data Transfer

Source Server         : Lokal
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : sfgame

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2018-09-25 21:11:01
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for copycats
-- ----------------------------
DROP TABLE IF EXISTS `copycats`;
CREATE TABLE `copycats` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `lvl` smallint(6) NOT NULL DEFAULT '150',
  `class` tinyint(4) NOT NULL,
  `str` mediumint(9) NOT NULL,
  `dex` mediumint(9) NOT NULL,
  `intel` mediumint(9) NOT NULL,
  `wit` mediumint(9) NOT NULL,
  `luck` mediumint(9) NOT NULL DEFAULT '547',
  PRIMARY KEY (`ID`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of copycats
-- ----------------------------
INSERT INTO `copycats` VALUES ('1', '1', '150', '1', '1046', '358', '531', '1065', '547');
INSERT INTO `copycats` VALUES ('2', '1', '150', '2', '358', '531', '1046', '799', '547');
INSERT INTO `copycats` VALUES ('3', '1', '150', '3', '358', '1046', '531', '799', '547');
INSERT INTO `copycats` VALUES ('4', '2', '150', '1', '1046', '358', '531', '1065', '547');
INSERT INTO `copycats` VALUES ('5', '2', '150', '2', '358', '531', '1046', '799', '547');
INSERT INTO `copycats` VALUES ('6', '2', '150', '3', '358', '1046', '531', '799', '547');
INSERT INTO `copycats` VALUES ('7', '3', '150', '1', '1046', '358', '531', '1065', '547');
INSERT INTO `copycats` VALUES ('8', '3', '150', '2', '358', '531', '1046', '799', '547');
INSERT INTO `copycats` VALUES ('9', '3', '150', '3', '358', '1046', '531', '799', '547');

-- ----------------------------
-- Table structure for fortress
-- ----------------------------
DROP TABLE IF EXISTS `fortress`;
CREATE TABLE `fortress` (
  `fortressID` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `stone` int(11) NOT NULL DEFAULT '0',
  `wood` int(11) NOT NULL DEFAULT '0',
  `u1` tinyint(2) NOT NULL DEFAULT '0',
  `u2` tinyint(4) NOT NULL DEFAULT '0',
  `u3` tinyint(4) NOT NULL DEFAULT '0',
  `ul1` tinyint(4) NOT NULL DEFAULT '1',
  `ul2` tinyint(4) DEFAULT '1',
  `ul3` tinyint(4) NOT NULL DEFAULT '1',
  `ut1` tinyint(4) NOT NULL DEFAULT '0',
  `ut2` tinyint(4) NOT NULL DEFAULT '0',
  `ut3` tinyint(4) NOT NULL DEFAULT '0',
  `uttime1` int(11) NOT NULL DEFAULT '0',
  `uttime2` int(11) NOT NULL DEFAULT '0',
  `uttime3` int(11) NOT NULL DEFAULT '0',
  `enemyid` int(11) NOT NULL DEFAULT '0',
  `enemytime` int(11) NOT NULL DEFAULT '0',
  `build_id` tinyint(4) NOT NULL DEFAULT '0',
  `build_start` int(11) NOT NULL DEFAULT '0',
  `build_end` int(11) NOT NULL DEFAULT '0',
  `dig_start` int(11) NOT NULL DEFAULT '0',
  `dig_end` int(11) NOT NULL DEFAULT '0',
  `gather1` int(11) NOT NULL DEFAULT '0',
  `gather2` int(11) NOT NULL DEFAULT '0',
  `gather3` int(11) NOT NULL DEFAULT '0',
  `hok` tinyint(4) NOT NULL DEFAULT '0',
  `b0` tinyint(4) NOT NULL DEFAULT '0',
  `b1` tinyint(4) NOT NULL DEFAULT '0',
  `b2` tinyint(4) NOT NULL DEFAULT '0',
  `b3` tinyint(4) NOT NULL DEFAULT '0',
  `b4` tinyint(4) NOT NULL DEFAULT '0',
  `b5` tinyint(4) NOT NULL DEFAULT '0',
  `b6` tinyint(4) NOT NULL DEFAULT '0',
  `b7` tinyint(4) NOT NULL DEFAULT '0',
  `b8` tinyint(4) NOT NULL DEFAULT '0',
  `b9` tinyint(4) NOT NULL DEFAULT '0',
  `b10` tinyint(4) NOT NULL DEFAULT '0',
  `b11` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fortressID`),
  KEY `owner` (`owner`),
  KEY `owner_2` (`owner`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of fortress
-- ----------------------------
INSERT INTO `fortress` VALUES ('1', '1', '0', '0', '0', '0', '0', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `fortress` VALUES ('2', '2', '11', '149', '0', '0', '0', '1', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1537444779', '1537444785', '0', '0', '1', '0', '1', '1', '0', '0', '0', '0', '0', '0', '0', '0');
INSERT INTO `fortress` VALUES ('3', '3', '459', '3952', '127', '1', '6', '1', '2', '1', '-128', '1', '0', '0', '1537444475', '0', '0', '0', '0', '0', '0', '0', '0', '1537444838', '1537444837', '1537444840', '0', '7', '6', '7', '7', '5', '5', '5', '3', '2', '6', '4', '5');

-- ----------------------------
-- Table structure for guildchat
-- ----------------------------
DROP TABLE IF EXISTS `guildchat`;
CREATE TABLE `guildchat` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `guildID` int(11) NOT NULL,
  `playerID` int(11) NOT NULL,
  `message` char(255) NOT NULL,
  `time` int(11) NOT NULL,
  `chattime` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `guildID` (`guildID`),
  KEY `chattime` (`chattime`)
) ENGINE=InnoDB AUTO_INCREMENT=125 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guildchat
-- ----------------------------
INSERT INTO `guildchat` VALUES ('1', '1', '3', '#in#SweetDana', '1537416606', '1');
INSERT INTO `guildchat` VALUES ('2', '1', '3', '#dg#05:10 SweetDana#161708500', '1537416613', '2');
INSERT INTO `guildchat` VALUES ('3', '1', '3', '#dm#05:10 SweetDana#10', '1537416617', '3');
INSERT INTO `guildchat` VALUES ('4', '1', '3', '#dm#05:10 SweetDana#10', '1537416621', '4');
INSERT INTO `guildchat` VALUES ('5', '1', '3', '$r?03060308070F0C08060301', '1537423925', '5');
INSERT INTO `guildchat` VALUES ('6', '1', '3', '#bd#07:12 SweetDana#1', '1537423931', '6');
INSERT INTO `guildchat` VALUES ('7', '1', '3', '#bd#07:12 SweetDana#1', '1537423932', '7');
INSERT INTO `guildchat` VALUES ('8', '1', '3', '#bd#07:12 SweetDana#2', '1537423935', '8');
INSERT INTO `guildchat` VALUES ('9', '1', '3', '#bd#07:12 SweetDana#2', '1537423935', '9');
INSERT INTO `guildchat` VALUES ('10', '1', '3', '#dg#07:12 SweetDana#494888200', '1537423950', '10');
INSERT INTO `guildchat` VALUES ('11', '1', '3', '#dm#07:12 SweetDana#10', '1537423956', '11');
INSERT INTO `guildchat` VALUES ('12', '1', '3', '#dm#07:12 SweetDana#10', '1537423959', '12');
INSERT INTO `guildchat` VALUES ('13', '1', '3', '#dm#07:12 SweetDana#10', '1537423962', '13');
INSERT INTO `guildchat` VALUES ('14', '1', '3', '#dm#07:12 SweetDana#10', '1537423965', '14');
INSERT INTO `guildchat` VALUES ('15', '1', '3', '#dm#07:12 SweetDana#10', '1537423968', '15');
INSERT INTO `guildchat` VALUES ('16', '1', '3', '#dm#07:12 SweetDana#10', '1537423971', '16');
INSERT INTO `guildchat` VALUES ('17', '1', '3', '#bd#07:12 SweetDana#1', '1537423975', '17');
INSERT INTO `guildchat` VALUES ('18', '1', '3', '#bd#07:12 SweetDana#1', '1537423977', '18');
INSERT INTO `guildchat` VALUES ('19', '1', '3', '#bd#07:12 SweetDana#1', '1537423977', '19');
INSERT INTO `guildchat` VALUES ('20', '1', '3', '#bd#07:12 SweetDana#1', '1537423979', '20');
INSERT INTO `guildchat` VALUES ('21', '1', '3', '#bd#07:13 SweetDana#1', '1537423980', '21');
INSERT INTO `guildchat` VALUES ('22', '1', '3', '#bd#07:13 SweetDana#1', '1537423980', '22');
INSERT INTO `guildchat` VALUES ('23', '1', '3', '#bd#07:13 SweetDana#1', '1537423982', '23');
INSERT INTO `guildchat` VALUES ('24', '1', '3', '#bd#07:13 SweetDana#1', '1537423982', '24');
INSERT INTO `guildchat` VALUES ('25', '1', '3', '#bd#07:13 SweetDana#0', '1537423994', '25');
INSERT INTO `guildchat` VALUES ('26', '1', '3', '+ 2000000 gold', '1537424313', '26');
INSERT INTO `guildchat` VALUES ('27', '1', '3', '+9000000 gold', '1537424334', '27');
INSERT INTO `guildchat` VALUES ('28', '1', '3', '#bd#08:28 SweetDana#3', '1537428514', '28');
INSERT INTO `guildchat` VALUES ('29', '1', '3', '#bd#08:28 SweetDana#3', '1537428515', '29');
INSERT INTO `guildchat` VALUES ('30', '1', '3', '#bd#08:28 SweetDana#3', '1537428516', '30');
INSERT INTO `guildchat` VALUES ('31', '1', '3', '#bd#08:28 SweetDana#3', '1537428516', '31');
INSERT INTO `guildchat` VALUES ('32', '1', '3', '#bd#08:28 SweetDana#3', '1537428516', '32');
INSERT INTO `guildchat` VALUES ('33', '1', '3', '#bd#08:28 SweetDana#3', '1537428517', '33');
INSERT INTO `guildchat` VALUES ('34', '1', '3', '#bd#08:28 SweetDana#3', '1537428518', '34');
INSERT INTO `guildchat` VALUES ('35', '1', '3', '#bd#08:28 SweetDana#3', '1537428518', '35');
INSERT INTO `guildchat` VALUES ('36', '1', '3', '#bd#08:28 SweetDana#3', '1537428518', '36');
INSERT INTO `guildchat` VALUES ('37', '1', '3', '#bd#08:28 SweetDana#3', '1537428518', '37');
INSERT INTO `guildchat` VALUES ('38', '1', '3', '#bd#08:28 SweetDana#3', '1537428518', '38');
INSERT INTO `guildchat` VALUES ('39', '1', '3', '#bd#08:28 SweetDana#3', '1537428518', '39');
INSERT INTO `guildchat` VALUES ('40', '1', '3', '#bd#08:28 SweetDana#3', '1537428519', '40');
INSERT INTO `guildchat` VALUES ('41', '1', '3', '#bd#08:28 SweetDana#3', '1537428519', '41');
INSERT INTO `guildchat` VALUES ('42', '1', '3', '#bd#08:28 SweetDana#3', '1537428519', '42');
INSERT INTO `guildchat` VALUES ('43', '1', '3', '#bd#08:28 SweetDana#3', '1537428519', '43');
INSERT INTO `guildchat` VALUES ('44', '1', '3', '#bd#08:28 SweetDana#3', '1537428519', '44');
INSERT INTO `guildchat` VALUES ('45', '1', '3', '#bd#08:28 SweetDana#3', '1537428519', '45');
INSERT INTO `guildchat` VALUES ('46', '1', '3', '#bd#08:28 SweetDana#3', '1537428520', '46');
INSERT INTO `guildchat` VALUES ('47', '1', '3', '#bd#08:28 SweetDana#3', '1537428520', '46');
INSERT INTO `guildchat` VALUES ('48', '1', '3', '#bd#08:28 SweetDana#3', '1537428520', '47');
INSERT INTO `guildchat` VALUES ('49', '1', '3', '#bd#08:28 SweetDana#3', '1537428520', '48');
INSERT INTO `guildchat` VALUES ('50', '1', '3', '#bd#08:28 SweetDana#3', '1537428521', '49');
INSERT INTO `guildchat` VALUES ('51', '1', '3', '#bd#08:28 SweetDana#3', '1537428521', '50');
INSERT INTO `guildchat` VALUES ('52', '1', '3', '#bd#08:28 SweetDana#3', '1537428521', '51');
INSERT INTO `guildchat` VALUES ('53', '1', '3', '#bd#08:28 SweetDana#3', '1537428521', '52');
INSERT INTO `guildchat` VALUES ('54', '1', '3', '#bd#08:28 SweetDana#3', '1537428521', '53');
INSERT INTO `guildchat` VALUES ('55', '1', '3', '#bd#08:28 SweetDana#3', '1537428521', '54');
INSERT INTO `guildchat` VALUES ('56', '1', '3', '#bd#08:28 SweetDana#3', '1537428522', '55');
INSERT INTO `guildchat` VALUES ('57', '1', '3', '#bd#08:28 SweetDana#3', '1537428522', '56');
INSERT INTO `guildchat` VALUES ('58', '1', '3', '#bd#08:28 SweetDana#2', '1537428525', '57');
INSERT INTO `guildchat` VALUES ('59', '1', '3', '#bd#08:28 SweetDana#2', '1537428526', '58');
INSERT INTO `guildchat` VALUES ('60', '1', '3', '#bd#08:28 SweetDana#2', '1537428526', '59');
INSERT INTO `guildchat` VALUES ('61', '1', '3', '#bd#08:28 SweetDana#2', '1537428526', '60');
INSERT INTO `guildchat` VALUES ('62', '1', '3', '#bd#08:28 SweetDana#2', '1537428526', '61');
INSERT INTO `guildchat` VALUES ('63', '1', '3', '#bd#08:28 SweetDana#2', '1537428526', '62');
INSERT INTO `guildchat` VALUES ('64', '1', '3', '#bd#08:28 SweetDana#2', '1537428527', '63');
INSERT INTO `guildchat` VALUES ('65', '1', '3', '#bd#08:28 SweetDana#2', '1537428527', '64');
INSERT INTO `guildchat` VALUES ('66', '1', '3', '#bd#08:28 SweetDana#2', '1537428527', '65');
INSERT INTO `guildchat` VALUES ('67', '1', '3', '#bd#08:28 SweetDana#2', '1537428527', '66');
INSERT INTO `guildchat` VALUES ('68', '1', '3', '#bd#08:28 SweetDana#2', '1537428527', '67');
INSERT INTO `guildchat` VALUES ('69', '1', '3', '#bd#08:28 SweetDana#2', '1537428527', '68');
INSERT INTO `guildchat` VALUES ('70', '1', '3', '#bd#08:28 SweetDana#2', '1537428528', '69');
INSERT INTO `guildchat` VALUES ('71', '1', '3', '#bd#08:28 SweetDana#2', '1537428528', '70');
INSERT INTO `guildchat` VALUES ('72', '1', '3', '#bd#08:28 SweetDana#2', '1537428528', '71');
INSERT INTO `guildchat` VALUES ('73', '1', '3', '#bd#08:28 SweetDana#2', '1537428528', '72');
INSERT INTO `guildchat` VALUES ('74', '1', '3', '#bd#08:28 SweetDana#2', '1537428528', '73');
INSERT INTO `guildchat` VALUES ('75', '1', '3', '#bd#08:28 SweetDana#2', '1537428529', '74');
INSERT INTO `guildchat` VALUES ('76', '1', '3', '#bd#08:28 SweetDana#2', '1537428529', '75');
INSERT INTO `guildchat` VALUES ('77', '1', '3', '#bd#08:28 SweetDana#2', '1537428529', '76');
INSERT INTO `guildchat` VALUES ('78', '1', '3', '#bd#08:28 SweetDana#2', '1537428529', '77');
INSERT INTO `guildchat` VALUES ('79', '1', '3', '#bd#08:28 SweetDana#1', '1537428532', '78');
INSERT INTO `guildchat` VALUES ('80', '1', '3', '#bd#08:28 SweetDana#1', '1537428532', '79');
INSERT INTO `guildchat` VALUES ('81', '1', '3', '#bd#08:28 SweetDana#1', '1537428532', '80');
INSERT INTO `guildchat` VALUES ('82', '1', '3', '#bd#08:28 SweetDana#1', '1537428533', '81');
INSERT INTO `guildchat` VALUES ('83', '1', '3', '#bd#08:28 SweetDana#1', '1537428533', '82');
INSERT INTO `guildchat` VALUES ('84', '1', '3', '#bd#08:28 SweetDana#1', '1537428533', '83');
INSERT INTO `guildchat` VALUES ('85', '1', '3', '#dm#08:28 SweetDana#10', '1537428539', '84');
INSERT INTO `guildchat` VALUES ('86', '1', '3', '#dm#08:29 SweetDana#10', '1537428542', '85');
INSERT INTO `guildchat` VALUES ('87', '1', '3', '#dm#08:29 SweetDana#10', '1537428544', '86');
INSERT INTO `guildchat` VALUES ('88', '1', '3', '#dm#08:29 SweetDana#10', '1537428547', '87');
INSERT INTO `guildchat` VALUES ('89', '1', '3', '#dm#08:29 SweetDana#10', '1537428550', '88');
INSERT INTO `guildchat` VALUES ('90', '1', '3', '#dm#08:29 SweetDana#10', '1537428552', '89');
INSERT INTO `guildchat` VALUES ('91', '1', '3', '#dm#08:29 SweetDana#10', '1537428554', '90');
INSERT INTO `guildchat` VALUES ('92', '1', '3', '#dm#08:29 SweetDana#10', '1537428556', '91');
INSERT INTO `guildchat` VALUES ('93', '1', '3', '#dm#08:29 SweetDana#10', '1537428558', '92');
INSERT INTO `guildchat` VALUES ('94', '1', '3', '#dm#08:29 SweetDana#10', '1537428560', '93');
INSERT INTO `guildchat` VALUES ('95', '1', '3', '#dm#08:29 SweetDana#10', '1537428560', '94');
INSERT INTO `guildchat` VALUES ('96', '1', '3', '#dm#08:29 SweetDana#10', '1537428562', '95');
INSERT INTO `guildchat` VALUES ('97', '1', '3', '#dm#08:29 SweetDana#10', '1537428564', '96');
INSERT INTO `guildchat` VALUES ('98', '1', '3', '#dm#08:29 SweetDana#10', '1537428565', '97');
INSERT INTO `guildchat` VALUES ('99', '1', '3', '#dm#08:29 SweetDana#10', '1537428567', '98');
INSERT INTO `guildchat` VALUES ('100', '1', '3', '$r?011F030801060C08030609', '1537428925', '99');
INSERT INTO `guildchat` VALUES ('101', '1', '3', '$r?011F030801060C08030609', '1537428926', '100');
INSERT INTO `guildchat` VALUES ('102', '1', '3', '$r?011F030801060C08030609', '1537428926', '101');
INSERT INTO `guildchat` VALUES ('103', '1', '3', '$r?011F030801060C08030609', '1537428926', '102');
INSERT INTO `guildchat` VALUES ('104', '1', '3', '#bd#08:38 SweetDana#1', '1537429137', '103');
INSERT INTO `guildchat` VALUES ('105', '1', '3', '#bd#08:38 SweetDana#1', '1537429139', '104');
INSERT INTO `guildchat` VALUES ('106', '1', '3', '#bd#08:39 SweetDana#1', '1537429140', '105');
INSERT INTO `guildchat` VALUES ('107', '1', '3', '#bd#08:39 SweetDana#1', '1537429141', '106');
INSERT INTO `guildchat` VALUES ('108', '1', '3', '#po#SweetDana#0#2434445#100', '1537443534', '107');
INSERT INTO `guildchat` VALUES ('109', '1', '3', '#bd#12:39 SweetDana#0', '1537443540', '108');
INSERT INTO `guildchat` VALUES ('110', '1', '3', '#dm#12:39 SweetDana#10', '1537443560', '109');
INSERT INTO `guildchat` VALUES ('111', '1', '3', '#dm#12:39 SweetDana#10', '1537443562', '110');
INSERT INTO `guildchat` VALUES ('112', '1', '3', '#dm#12:39 SweetDana#10', '1537443564', '111');
INSERT INTO `guildchat` VALUES ('113', '1', '3', '#dm#12:39 SweetDana#10', '1537443565', '112');
INSERT INTO `guildchat` VALUES ('114', '1', '3', '#dm#12:39 SweetDana#9', '1537443567', '113');
INSERT INTO `guildchat` VALUES ('115', '1', '3', '#dm#12:39 SweetDana#10', '1537443569', '114');
INSERT INTO `guildchat` VALUES ('116', '1', '3', '#dm#12:39 SweetDana#10', '1537443571', '115');
INSERT INTO `guildchat` VALUES ('117', '1', '3', '#dm#12:39 SweetDana#10', '1537443573', '116');
INSERT INTO `guildchat` VALUES ('118', '2', '2', '#in#Annika', '1537444396', '1');
INSERT INTO `guildchat` VALUES ('119', '2', '2', '#dm#12:53 Annika#10', '1537444402', '2');
INSERT INTO `guildchat` VALUES ('120', '2', '2', '#dm#12:53 Annika#10', '1537444404', '3');
INSERT INTO `guildchat` VALUES ('121', '2', '2', '#dg#12:53 Annika#893900', '1537444408', '4');
INSERT INTO `guildchat` VALUES ('122', '2', '2', '#bd#12:53 Annika#1', '1537444412', '5');
INSERT INTO `guildchat` VALUES ('123', '2', '2', '#bd#12:53 Annika#1', '1537444413', '6');
INSERT INTO `guildchat` VALUES ('124', '2', '2', '#bd#12:53 Annika#1', '1537444414', '7');

-- ----------------------------
-- Table structure for guildfightlogs
-- ----------------------------
DROP TABLE IF EXISTS `guildfightlogs`;
CREATE TABLE `guildfightlogs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `guildAttacker` int(11) NOT NULL,
  `guildDefender` int(11) NOT NULL,
  `log` mediumtext NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `guildAttacker` (`guildAttacker`),
  KEY `guildDefender` (`guildDefender`),
  KEY `time` (`time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guildfightlogs
-- ----------------------------

-- ----------------------------
-- Table structure for guildfights
-- ----------------------------
DROP TABLE IF EXISTS `guildfights`;
CREATE TABLE `guildfights` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `guildAttacker` int(11) NOT NULL,
  `guildDefender` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `guildAttacker` (`guildAttacker`),
  KEY `guildDefender` (`guildDefender`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guildfights
-- ----------------------------
INSERT INTO `guildfights` VALUES ('1', '1', '1000000', '1537450743');

-- ----------------------------
-- Table structure for guildinvites
-- ----------------------------
DROP TABLE IF EXISTS `guildinvites`;
CREATE TABLE `guildinvites` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `guildID` int(11) NOT NULL,
  `playerID` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `guildID` (`guildID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guildinvites
-- ----------------------------

-- ----------------------------
-- Table structure for guilds
-- ----------------------------
DROP TABLE IF EXISTS `guilds`;
CREATE TABLE `guilds` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `base` tinyint(4) NOT NULL DEFAULT '10',
  `treasure` int(11) NOT NULL DEFAULT '0',
  `instructor` int(11) NOT NULL DEFAULT '0',
  `silver` int(11) NOT NULL DEFAULT '1000',
  `mush` int(11) NOT NULL DEFAULT '0',
  `honor` int(11) NOT NULL DEFAULT '100',
  `portal` tinyint(4) NOT NULL DEFAULT '0',
  `portal_hp` bigint(20) NOT NULL DEFAULT '1003472384',
  `dungeon` tinyint(4) NOT NULL DEFAULT '0',
  `attack_init` int(11) NOT NULL DEFAULT '0',
  `event_trigger_count` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `honor` (`honor`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of guilds
-- ----------------------------
INSERT INTO `guilds` VALUES ('1', 'BloodBound', '30', '23', '30', '641078900', '159', '100', '0', '1001037939', '0', '3', '0');
INSERT INTO `guilds` VALUES ('2', 'DarkWood', '13', '0', '0', '835200', '20', '100', '0', '1003472384', '0', '0', '0');

-- ----------------------------
-- Table structure for items
-- ----------------------------
DROP TABLE IF EXISTS `items`;
CREATE TABLE `items` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `owner` int(11) NOT NULL,
  `slot` smallint(6) NOT NULL,
  `type` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `dmg_min` int(11) NOT NULL DEFAULT '0',
  `dmg_max` int(11) NOT NULL DEFAULT '0',
  `a1` int(11) NOT NULL DEFAULT '0',
  `a2` int(11) NOT NULL DEFAULT '0',
  `a3` int(11) NOT NULL DEFAULT '0',
  `a4` int(11) NOT NULL DEFAULT '0',
  `a5` int(11) NOT NULL DEFAULT '0',
  `a6` int(11) DEFAULT '0',
  `value_silver` int(11) NOT NULL,
  `value_mush` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `owner` (`owner`)
) ENGINE=InnoDB AUTO_INCREMENT=165 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of items
-- ----------------------------
INSERT INTO `items` VALUES ('1', '1', '18', '65537', '1001', '4', '8', '1', '0', '0', '2', '0', '0', '1', '0');
INSERT INTO `items` VALUES ('2', '1', '0', '13', '1', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0');
INSERT INTO `items` VALUES ('3', '1', '20', '4', '1006', '1', '0', '5', '0', '0', '1', '0', '0', '10899', '0');
INSERT INTO `items` VALUES ('4', '1', '21', '7', '1004', '1', '0', '1', '0', '0', '3', '0', '0', '28639', '0');
INSERT INTO `items` VALUES ('5', '1', '22', '6', '1010', '1', '0', '3', '0', '0', '3', '0', '0', '4067', '0');
INSERT INTO `items` VALUES ('6', '1', '23', '6', '1002', '1', '0', '1', '0', '0', '3', '0', '0', '12785', '0');
INSERT INTO `items` VALUES ('7', '1', '24', '5', '1008', '1', '0', '4', '0', '0', '1', '0', '0', '20430', '0');
INSERT INTO `items` VALUES ('8', '1', '25', '65537', '1004', '4', '8', '5', '0', '0', '3', '0', '0', '20128', '0');
INSERT INTO `items` VALUES ('9', '1', '26', '9', '15', '0', '0', '4', '0', '0', '3', '0', '0', '12585', '0');
INSERT INTO `items` VALUES ('10', '1', '27', '10', '27', '0', '0', '5', '0', '0', '2', '0', '0', '3289', '0');
INSERT INTO `items` VALUES ('11', '1', '28', '65545', '3', '0', '0', '2', '0', '0', '2', '0', '0', '14397', '0');
INSERT INTO `items` VALUES ('12', '1', '29', '65546', '36', '0', '0', '4', '0', '0', '3', '0', '0', '11545', '0');
INSERT INTO `items` VALUES ('13', '1', '30', '65546', '33', '0', '0', '5', '0', '0', '3', '0', '0', '15350', '0');
INSERT INTO `items` VALUES ('14', '1', '31', '10', '25', '0', '0', '5', '0', '0', '3', '0', '0', '18941', '0');
INSERT INTO `items` VALUES ('17', '2', '20', '7', '4', '1', '0', '2', '0', '0', '37', '0', '0', '19663', '0');
INSERT INTO `items` VALUES ('18', '2', '19', '65538', '10', '25', '0', '1', '0', '0', '1', '0', '0', '15690', '0');
INSERT INTO `items` VALUES ('19', '2', '13', '65540', '2', '1', '0', '4', '0', '0', '3', '0', '0', '18771', '0');
INSERT INTO `items` VALUES ('21', '2', '15', '65543', '2', '1', '0', '2', '0', '0', '3', '0', '0', '9683', '0');
INSERT INTO `items` VALUES ('22', '2', '12', '65541', '5', '1', '0', '5', '0', '0', '3', '0', '0', '11260', '0');
INSERT INTO `items` VALUES ('23', '2', '26', '65545', '7', '0', '0', '4', '0', '0', '36', '0', '0', '13791', '0');
INSERT INTO `items` VALUES ('24', '2', '27', '9', '2', '0', '0', '1', '0', '0', '37', '0', '0', '22913', '0');
INSERT INTO `items` VALUES ('25', '2', '28', '65544', '8', '0', '0', '3', '0', '0', '35', '0', '0', '23228', '0');
INSERT INTO `items` VALUES ('27', '2', '30', '10', '21', '0', '0', '1', '0', '0', '37', '0', '0', '24933', '0');
INSERT INTO `items` VALUES ('28', '2', '14', '65544', '21', '0', '0', '1', '0', '0', '38', '0', '0', '12577', '0');
INSERT INTO `items` VALUES ('31', '3', '20', '1', '1005', '834', '960', '3', '0', '0', '375', '0', '0', '4360', '0');
INSERT INTO `items` VALUES ('32', '3', '21', '65541', '1008', '1', '0', '5', '0', '0', '381', '0', '0', '15939', '0');
INSERT INTO `items` VALUES ('36', '3', '12', '2293765', '1007', '1', '0', '1', '0', '0', '1', '0', '0', '28832', '18808832');
INSERT INTO `items` VALUES ('38', '3', '27', '65545', '15', '0', '0', '5', '0', '0', '366', '0', '0', '7981', '0');
INSERT INTO `items` VALUES ('45', '3', '24', '3', '1010', '1', '0', '4', '0', '0', '376', '0', '0', '26038', '0');
INSERT INTO `items` VALUES ('47', '3', '23', '65540', '1001', '1', '0', '4', '0', '0', '381', '0', '0', '6473', '0');
INSERT INTO `items` VALUES ('48', '3', '22', '65537', '1010', '835', '959', '4', '0', '0', '378', '0', '0', '11315', '0');
INSERT INTO `items` VALUES ('50', '3', '14', '983048', '60', '0', '0', '6', '0', '0', '134', '0', '0', '13412', '18022400');
INSERT INTO `items` VALUES ('51', '3', '30', '10', '16', '0', '0', '4', '0', '0', '365', '0', '0', '3324', '0');
INSERT INTO `items` VALUES ('54', '3', '15', '1638407', '1051', '1', '0', '3', '4', '5', '29', '29', '29', '11644', '25034752');
INSERT INTO `items` VALUES ('60', '3', '16', '9', '51', '0', '0', '6', '0', '0', '45', '0', '0', '13941', '0');
INSERT INTO `items` VALUES ('67', '3', '28', '10', '20', '0', '0', '2', '0', '0', '366', '0', '0', '12860', '0');
INSERT INTO `items` VALUES ('69', '3', '31', '65545', '16', '0', '0', '4', '0', '0', '367', '0', '0', '20285', '0');
INSERT INTO `items` VALUES ('75', '3', '26', '65544', '16', '0', '0', '4', '0', '0', '363', '0', '0', '16632', '0');
INSERT INTO `items` VALUES ('99', '3', '13', '786436', '1055', '1', '0', '3', '4', '5', '123', '123', '123', '3778', '24641536');
INSERT INTO `items` VALUES ('106', '3', '25', '65539', '1009', '1', '0', '5', '0', '0', '378', '0', '0', '6728', '0');
INSERT INTO `items` VALUES ('123', '3', '18', '2031617', '1053', '554', '614', '3', '4', '5', '160', '160', '160', '10479', '25231360');
INSERT INTO `items` VALUES ('126', '3', '11', '2293763', '1052', '1', '0', '3', '4', '5', '169', '169', '169', '4959', '21692416');
INSERT INTO `items` VALUES ('128', '3', '17', '2293770', '54', '0', '0', '3', '4', '5', '169', '169', '169', '4604', '25559040');
INSERT INTO `items` VALUES ('133', '3', '10', '1638406', '1053', '1', '0', '6', '0', '0', '151', '0', '0', '6443', '24510464');
INSERT INTO `items` VALUES ('147', '3', '29', '65545', '4', '0', '0', '2', '0', '0', '363', '0', '0', '4385', '0');
INSERT INTO `items` VALUES ('150', '2', '11', '65539', '6', '1', '0', '3', '0', '0', '37', '0', '0', '26080', '0');
INSERT INTO `items` VALUES ('151', '2', '24', '5', '10', '1', '0', '2', '0', '0', '37', '0', '0', '21664', '0');
INSERT INTO `items` VALUES ('152', '2', '10', '6', '10', '1', '0', '4', '0', '0', '36', '0', '0', '20858', '0');
INSERT INTO `items` VALUES ('153', '2', '22', '6', '4', '1', '0', '1', '0', '0', '36', '0', '0', '2414', '0');
INSERT INTO `items` VALUES ('154', '2', '25', '65539', '2', '1', '0', '5', '0', '0', '36', '0', '0', '26705', '0');
INSERT INTO `items` VALUES ('155', '2', '21', '65539', '3', '1', '0', '5', '0', '0', '38', '0', '0', '12435', '0');
INSERT INTO `items` VALUES ('156', '2', '23', '7', '2', '1', '0', '3', '0', '0', '37', '0', '0', '2321', '0');
INSERT INTO `items` VALUES ('157', '2', '16', '65545', '11', '0', '0', '2', '0', '0', '35', '0', '0', '24114', '0');
INSERT INTO `items` VALUES ('158', '2', '29', '65545', '16', '0', '0', '3', '0', '0', '39', '0', '0', '7865', '0');
INSERT INTO `items` VALUES ('159', '2', '31', '8', '21', '0', '0', '5', '0', '0', '35', '0', '0', '18906', '0');
INSERT INTO `items` VALUES ('161', '2', '18', '1', '6', '32', '42', '5', '0', '0', '37', '0', '0', '9449', '0');
INSERT INTO `items` VALUES ('162', '2', '17', '10', '57', '0', '0', '1', '4', '5', '27', '27', '27', '24588', '0');
INSERT INTO `items` VALUES ('164', '2', '0', '5', '5', '1', '0', '2', '0', '0', '60', '0', '0', '2510', '0');

-- ----------------------------
-- Table structure for messages
-- ----------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `sender` int(11) NOT NULL,
  `reciver` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `topic` char(255) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  `hasRead` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `reciver` (`reciver`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of messages
-- ----------------------------
INSERT INTO `messages` VALUES ('1', '0', '1', '1537406898', 'Hello :)', 'Beers reset portal timer!$bTo report bugs or to contact me message Marcel$b$bHave fun!', '0');
INSERT INTO `messages` VALUES ('2', '0', '2', '1537413360', 'Hello :)', 'Beers reset portal timer!$bTo report bugs or to contact me message Marcel$b$bHave fun!', '0');
INSERT INTO `messages` VALUES ('3', '0', '3', '1537413697', 'Hello :)', 'Beers reset portal timer!$bTo report bugs or to contact me message Marcel$b$bHave fun!', '1');

-- ----------------------------
-- Table structure for online
-- ----------------------------
DROP TABLE IF EXISTS `online`;
CREATE TABLE `online` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `onlineCount` smallint(6) NOT NULL DEFAULT '0',
  `lastUpdate` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of online
-- ----------------------------

-- ----------------------------
-- Table structure for playerfightlogs
-- ----------------------------
DROP TABLE IF EXISTS `playerfightlogs`;
CREATE TABLE `playerfightlogs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `attacker` int(11) NOT NULL,
  `defender` int(11) NOT NULL,
  `log` mediumtext NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `time` (`time`),
  KEY `attacker` (`attacker`),
  KEY `defender` (`defender`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of playerfightlogs
-- ----------------------------

-- ----------------------------
-- Table structure for players
-- ----------------------------
DROP TABLE IF EXISTS `players`;
CREATE TABLE `players` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(26) NOT NULL,
  `password` char(40) CHARACTER SET latin1 NOT NULL,
  `lastLoginCount` int(3) NOT NULL DEFAULT '0',
  `face` char(32) CHARACTER SET latin1 NOT NULL,
  `ssid` char(32) CHARACTER SET latin1 NOT NULL,
  `poll` int(11) NOT NULL DEFAULT '0',
  `lvl` smallint(6) NOT NULL DEFAULT '1',
  `exp` bigint(20) NOT NULL DEFAULT '0',
  `quest_dur1` smallint(6) NOT NULL,
  `quest_dur2` smallint(6) NOT NULL,
  `quest_dur3` smallint(6) NOT NULL,
  `quest_exp1` int(11) NOT NULL,
  `quest_exp2` int(11) NOT NULL,
  `quest_exp3` int(11) NOT NULL,
  `quest_silver1` int(11) NOT NULL,
  `quest_silver2` int(11) NOT NULL,
  `quest_silver3` int(11) NOT NULL,
  `str` smallint(6) NOT NULL DEFAULT '10',
  `dex` smallint(6) NOT NULL DEFAULT '10',
  `intel` smallint(6) NOT NULL DEFAULT '10',
  `wit` smallint(6) NOT NULL DEFAULT '10',
  `luck` smallint(6) NOT NULL DEFAULT '10',
  `potion_type1` tinyint(4) NOT NULL DEFAULT '0',
  `potion_type2` tinyint(4) NOT NULL DEFAULT '0',
  `potion_type3` tinyint(4) NOT NULL DEFAULT '0',
  `potion_dur1` int(11) NOT NULL DEFAULT '0',
  `potion_dur2` int(11) NOT NULL DEFAULT '0',
  `potion_dur3` int(11) NOT NULL DEFAULT '0',
  `silver` bigint(20) NOT NULL DEFAULT '100',
  `mush` int(11) NOT NULL DEFAULT '10000000',
  `thirst` smallint(5) NOT NULL DEFAULT '6000',
  `beers` tinyint(4) NOT NULL DEFAULT '0',
  `honor` int(11) NOT NULL DEFAULT '100',
  `race` tinyint(1) NOT NULL,
  `gender` tinyint(1) NOT NULL,
  `class` tinyint(1) NOT NULL,
  `mount` tinyint(4) NOT NULL DEFAULT '0',
  `mount_time` int(11) NOT NULL DEFAULT '0',
  `album` smallint(6) NOT NULL DEFAULT '-1',
  `album_data` varchar(1024) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `status_extra` tinyint(4) NOT NULL DEFAULT '0',
  `status_time` int(11) NOT NULL DEFAULT '0',
  `dungeon_time` int(11) NOT NULL DEFAULT '0',
  `arena_time` int(11) NOT NULL DEFAULT '0',
  `arena_nme1` int(11) NOT NULL DEFAULT '0',
  `arena_nme2` int(11) NOT NULL DEFAULT '0',
  `arena_nme3` int(11) NOT NULL DEFAULT '0',
  `tower` tinyint(4) NOT NULL DEFAULT '0',
  `wcaura` smallint(6) NOT NULL DEFAULT '1',
  `wcexp` smallint(6) NOT NULL DEFAULT '0',
  `wcdate` int(11) NOT NULL DEFAULT '0',
  `portal` tinyint(4) NOT NULL DEFAULT '0',
  `portal_hp` bigint(20) NOT NULL DEFAULT '35938800',
  `portal_time` smallint(6) NOT NULL DEFAULT '0',
  `gportal_time` int(11) NOT NULL DEFAULT '0',
  `guild` int(11) NOT NULL DEFAULT '0',
  `guild_rank` tinyint(4) NOT NULL DEFAULT '3',
  `guild_fight` tinyint(4) NOT NULL DEFAULT '0',
  `event_trigger_count` int(11) NOT NULL DEFAULT '0',
  `d1` tinyint(4) NOT NULL DEFAULT '2',
  `d2` tinyint(4) DEFAULT '2',
  `d3` tinyint(4) NOT NULL DEFAULT '2',
  `d4` tinyint(4) NOT NULL DEFAULT '2',
  `d5` tinyint(4) NOT NULL DEFAULT '2',
  `d6` tinyint(4) NOT NULL DEFAULT '2',
  `d7` tinyint(4) NOT NULL DEFAULT '2',
  `d8` tinyint(4) NOT NULL DEFAULT '2',
  `d9` tinyint(4) NOT NULL DEFAULT '2',
  `d10` tinyint(4) NOT NULL DEFAULT '2',
  `d11` tinyint(4) NOT NULL DEFAULT '2',
  `d12` tinyint(4) NOT NULL DEFAULT '2',
  `d13` tinyint(4) NOT NULL DEFAULT '2',
  `d14` tinyint(4) NOT NULL DEFAULT '2',
  `dd1` tinyint(4) NOT NULL DEFAULT '2',
  `dd2` tinyint(4) NOT NULL DEFAULT '2',
  `dd3` tinyint(4) NOT NULL DEFAULT '2',
  `dd4` tinyint(4) NOT NULL DEFAULT '2',
  `dd5` tinyint(4) NOT NULL DEFAULT '2',
  `dd6` tinyint(4) NOT NULL DEFAULT '2',
  `dd7` tinyint(4) NOT NULL DEFAULT '2',
  `dd8` tinyint(4) NOT NULL DEFAULT '2',
  `dd9` tinyint(4) NOT NULL DEFAULT '2',
  `dd10` tinyint(4) NOT NULL DEFAULT '2',
  `dd11` tinyint(4) NOT NULL DEFAULT '2',
  `dd12` tinyint(4) NOT NULL DEFAULT '2',
  `dd13` tinyint(4) NOT NULL DEFAULT '2',
  `dd14` tinyint(4) NOT NULL DEFAULT '2',
  PRIMARY KEY (`ID`),
  KEY `ssid` (`ssid`),
  KEY `guild` (`guild`),
  KEY `honor` (`honor`),
  KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of players
-- ----------------------------
INSERT INTO `players` VALUES ('1', 'Wunaduk Gnalumix', 'fafcff8841e6669975053a24ba858a856fa03ab7', '117', '5,110,101,6,101,3,2,3,0', '081554ae1da3975ed3f3adf25c98fdf5', '1537406899', '4', '93', '300', '300', '900', '53', '55', '168', '25284', '23428', '74472', '10', '10', '10', '10', '10', '0', '0', '0', '0', '0', '0', '70679', '9999998', '3600', '0', '120', '2', '1', '2', '0', '0', '-1', '', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '0', '0', '0', '35938800', '0', '0', '0', '3', '0', '0', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2');
INSERT INTO `players` VALUES ('2', 'Annika', 'fafcff8841e6669975053a24ba858a856fa03ab7', '631', '6,407,401,7,0,8,2,4,0', '9e8c57876510b1314e9300d1c37a659d', '1537444796', '25', '14118', '300', '300', '1200', '11790', '12365', '54832', '144864', '124992', '515136', '210', '10', '10', '285', '310', '0', '0', '0', '0', '0', '0', '2883919', '9999913', '0', '10', '290', '2', '2', '1', '0', '0', '95', 'BAAQAowASMAAQBAkIAEQAKoACwJgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA-AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAPgAAAAAAAAAAAAAAAAAAAAAAAAAQAAAAAAAAAABAAAAAPgAAHwAAAAAA-AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHwAAAAAAAAAAAAAAAAAAHwAAAAAAAAAAAAIAAAAAHwAAAAAAAAAAAAAAAAAAAAAAAAP_AAAAAAAAAAAAAAAAAAAAAAAPgB8AAAAAAAAAAAAAAAHwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==', '1', '10', '1537480798', '1537445024', '0', '0', '0', '0', '0', '1', '0', '0', '0', '35938800', '0', '0', '2', '1', '0', '0', '9', '5', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2');
INSERT INTO `players` VALUES ('3', 'SweetDana', 'fafcff8841e6669975053a24ba858a856fa03ab7', '992', '6,302,302,5,0,8,2,5,0', '6ef1082acf30a514aaea6d94e61e6df0', '1537444842', '163', '19584863', '900', '600', '600', '7270581', '5212312', '4620260', '2822019', '1684442', '1851354', '3153', '2854', '4352', '3355', '4103', '16', '11', '14', '1538626168', '1537675772', '1537702277', '507458568', '9998983', '150', '10', '390', '2', '2', '2', '4', '1538623483', '269', 'NJGbFx5OMIhgbNfc_LF0OavfbtfkD8MAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA-AAAAAAAAAAD4PgAAAAAAAAAAAAAAAAAANAgAAAAAAAAfAAAAfAAAAAAAAAAAAAAAABAQAAAAAAYABAAAAAAAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA__4P_-D4AAAAAAAAARQAAAAAfAAAAA-AAAAAAAAAYIQAAAD4AfAAHwfAAAAAAAACQAAAAAAAAP_-AAAAAAAAAABQEAAAAAf-AHwAB8AAAAAAAAkBgAAAD4P_AD4AAAAAAAAAAEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==', '1', '10', '1537480818', '1537444260', '0', '0', '0', '0', '0', '1', '25', '1537428990', '0', '35336436', '264', '1537443534', '1', '1', '1', '0', '12', '11', '11', '11', '11', '11', '11', '11', '11', '11', '8', '4', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2', '2');
