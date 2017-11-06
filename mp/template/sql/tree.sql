CREATE TABLE `tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL,
  `lft` int(10) unsigned NOT NULL COMMENT '左权',
  `rght` int(10) unsigned NOT NULL COMMENT '右权',
  `name` varchar(100) NOT NULL COMMENT '节点名称',
  `mark` varchar(32) NOT NULL COMMENT '临时标记数据',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `rght` (`rght`),
  KEY `lft` (`lft`,`rght`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8


