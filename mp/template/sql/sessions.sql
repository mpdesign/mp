CREATE TABLE `sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0' COMMENT '会话标志',
  `data` text COMMENT '会话内容',
  `expire` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '过期时间',
  PRIMARY KEY (`session_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='站点会话'