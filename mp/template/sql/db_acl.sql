CREATE TABLE `acl_leaf` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL,
  `module` varchar(255) NOT NULL DEFAULT '' COMMENT '模块',
  `action` varchar(255) NOT NULL COMMENT '操作',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '名称',
  `lft` int(10) NOT NULL,
  `rght` int(10) NOT NULL,
  `is_show` tinyint(4) NOT NULL COMMENT '是否显示',
  `mark` varchar(32) NOT NULL COMMENT '临时标记',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `rght` (`rght`),
  KEY `lft` (`rght`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限表示层--叶子'

CREATE TABLE `acl_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) NOT NULL COMMENT '点节ID',
  `leaf_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '叶子ID',
  `alias` varchar(255) NOT NULL DEFAULT '' COMMENT '别名',
  `lft` int(10) NOT NULL COMMENT '左权',
  `rght` int(10) NOT NULL COMMENT '右权',
  `mark` varchar(32) NOT NULL COMMENT '临时标记',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `lft` (`lft`),
  KEY `rght` (`rght`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='权限逻辑制控层--树形结构'