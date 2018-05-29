
DROP TABLE IF EXISTS `cms_wx_access_token`;
CREATE TABLE `cms_wx_access_token`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `component_access_token` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '第三方盾牌',
  `token_time` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '获取盾牌的时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `cms_wx_auth_code`;
CREATE TABLE `cms_wx_auth_code`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `pre_auth_code` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '预授权码',
  `expires_in` int(255) UNSIGNED NOT NULL COMMENT '授权失效时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `cms_wx_authorizer_access_token`;
CREATE TABLE `cms_wx_authorizer_access_token`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `auth_code` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '授权码',
  `authorizer_access_token` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '授权盾牌',
  `expires_in` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '盾牌到期时间',
  `text` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '盾牌内容',
  `authorizer_appid` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '小程序或公众号id',
  `authorizer_refresh_token` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '刷新盾牌',
  `authorizer_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '小程序或公众号名称',
  `authorizer_qrcode_url` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '二维码的图片',
  `authorizer_signature` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '账号信息',
  `text2` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '程序信息内容',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `cms_wx_submitcode`;
CREATE TABLE `cms_wx_submitcode`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `auditid` int(10) UNSIGNED NULL DEFAULT NULL COMMENT '审核编号',
  `status` int(2) UNSIGNED NULL DEFAULT NULL COMMENT '审核状态',
  `addtime` int(11) UNSIGNED NOT NULL COMMENT '添加时间',
  `type` int(1) NULL DEFAULT NULL COMMENT '信息的类型',
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '信息内容',
  `template_id` int(11) NULL DEFAULT NULL COMMENT '上传的模板id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `cms_wx_trilateraluser`;
CREATE TABLE `cms_wx_trilateraluser`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `trilateralAppID` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信第三方appid',
  `trilateralAppSecret` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信第三方AppSecret',
  `trilateralToken` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信第三方消息验证Token',
  `trilateralKey` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信第三方消息加解密Key',
  `trilateralName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信第三方名称',
  `trilateralUrl` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信验证URL',
  `trilateralVerify_ticket` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信第三方的Verify_ticket（每隔十分钟推送一条)',
  `trilateralAccess_token` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信第三方盾牌',
  `trilateralAccess_token_time` int(255) NULL DEFAULT NULL COMMENT '微信第三方盾牌授权结束时间',
  `trilateralAuth_code` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '微信第三方预授权码',
  `trilateralAuth_code_time` int(11) NULL DEFAULT NULL COMMENT '微信第三方式授权码结束时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;


DROP TABLE IF EXISTS `cms_wx_verify_ticket`;
CREATE TABLE `cms_wx_verify_ticket`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '内容',
  `verify_ticket` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '结果',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB  CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;


