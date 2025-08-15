-- Adminer 5.3.1-dev MySQL 8.0.42-0ubuntu0.20.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `school`;
CREATE DATABASE `school` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `school`;

DROP TABLE IF EXISTS `card`;
CREATE TABLE `card` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `allowed_courses` text NOT NULL,
  `max_courses` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `cid` int NOT NULL AUTO_INCREMENT COMMENT '课程ID',
  `competition_name` varchar(255) NOT NULL COMMENT '比赛名称',
  `competition_short_name` varchar(100) DEFAULT NULL COMMENT '比赛简称',
  `competition_level` varchar(255) NOT NULL COMMENT '比赛级别',
  `submit_time` varchar(100) NOT NULL COMMENT '申报时间',
  `submit_requirements` longtext COMMENT '申报要求',
  `student_requirements` longtext COMMENT '需要学生提交的材料',
  `card_requirement` int DEFAULT '0' COMMENT '需要卡级别',
  `default_content` longtext,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='课程/比赛信息表';

INSERT INTO `course` (`cid`, `competition_name`, `competition_short_name`, `competition_level`, `submit_time`, `submit_requirements`, `student_requirements`, `card_requirement`, `default_content`) VALUES
(1,	'Demo',	'Demo',	'宇宙级',	'时间尽头',	'This is a demo.',	'This is a demo.',	999,	NULL),
(2,	'国际发明创新展览会（上交会）',	'上交会',	'国际级',	'3-4月',	'申报表、展板',	'作品说明文档',	1,	NULL),
(3,	'上海市少年儿童乐创挑战系列活动',	'乐创挑战',	'省市级',	'9-12月',	'作品方案、视频',	'作品说明文档',	1,	NULL),
(4,	'上海市青少年创新成果展',	'青少年成果展',	'省市级',	'4-5月',	'项目说明书、照片',	'作品说明文档',	1,	NULL),
(5,	'iENA德国纽伦堡国际发明展（中国站）',	'iENA中国区',	'国际级',	'9月',	'创新实物、模型、方案、展板、PPT[200字英文介绍]、视频',	'作品说明文档、项目演示文稿',	1,	NULL),
(6,	'IEYI世界青少年创新发明展（中国站）-创意设计',	'IEYI中国区',	'国际级',	'3-4月',	'作品说明书、视频',	'作品说明文档',	1,	NULL),
(7,	'杂志期刊发表',	'期刊发表',	'国家级',	'不定期',	'文章',	'征文稿件',	1,	NULL),
(8,	'科奇空间——家庭科创角',	'科奇空间',	'省市级',	'5-7月',	'设计稿',	'作品说明文档',	1,	NULL),
(9,	'小小科普讲解员大赛',	'小小科普讲解员大赛',	'省市级',	'5-6月',	'讲解稿、讲解视频',	'讲解稿件',	1,	NULL),
(10,	'青少年科学诠释者',	'青少年科学诠释者',	'省市级',	'6-9月',	'研究报告/设计方案/科普作品',	'作品说明文档',	1,	NULL),
(11,	'IEYI世界青少年创新发明展（中国站）-科幻画',	'IEYI中国区（科幻画）',	'国际级',	'3-4月',	'科幻画作、作品说明',	'科幻画作',	1,	NULL),
(12,	'上海市青少年科技创新大赛-科幻画',	'青创赛（科幻画）',	'声世界',	'1-3月',	'科幻画作、作品说明',	'科幻画作',	1,	NULL),
(13,	'“天问杯”好问题大赛',	'天问杯',	'省市级',	'9-10月',	'申报表（含问题与实践过程说明）',	'作品说明文档',	1,	NULL),
(14,	'青少年科技创新大赛',	'青创赛（科幻画）',	'国家级',	'10-12月',	'研究报告、研究日志、查新报告',	NULL,	2,	NULL),
(15,	'上海市百万青少年争创“明日科技之星”评选活动',	'明日科技之星',	'省市级',	'10-次年5月',	'研究报告、研究日志、查新报告',	NULL,	2,	NULL),
(16,	'上海市雏鹰杯红领巾科创达人挑战赛',	'雏鹰杯',	'省市级',	'9-12月',	'研究报告、研究日志、装置',	NULL,	2,	NULL),
(17,	'顶尖科学家论坛“科学T大会”',	'科学T大会',	'国际级',	'10月',	'学术简历、创新提案、英文摘要',	'创新提案初稿',	2,	NULL),
(18,	'上海市青少年创新成果展',	'青少年成果展',	'省市级',	'7-8月',	'作品视频、设计草图',	NULL,	2,	NULL),
(19,	'国际发明创新展览会（上交会）',	'上交会',	'国际级',	'3-4月',	'申报表、展板',	NULL,	2,	NULL),
(20,	'宋庆龄少年儿童发明奖',	'宋庆龄',	'国家级',	'3-5月',	'发明说明书、原型照片、研究报告',	NULL,	2,	NULL),
(21,	'水科技发明赛',	'水科技',	'国际级',	'4-6月',	'发明设计图、水环境相关数据、研究报告',	NULL,	2,	NULL),
(22,	'赛复创智杯上海市青少年科技创意设计评选活动',	'赛复创智杯',	'省市级',	'9-12月',	'设计方案、原型演示视频、研究报告',	'NULL',	2,	'<p><strong>第十六届“赛复创智杯”上海市青少年科技创意设计展示活动申报表</strong></p><p>区（地区）：&nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;项目申报编号（作品申报阶段无需填写）：</p><p><strong>申报类别（请打√，单选）</strong></p><p>□&nbsp;方案设计演示类作品&nbsp;&nbsp; &nbsp; □&nbsp;模型创意演示类作品&nbsp; &nbsp;</p><p>□&nbsp;虚拟动画演示类作品&nbsp; &nbsp; &nbsp;</p><p><strong>学科分类（请打√，单选）</strong></p><p>□&nbsp;化学、环境科学、生命科学&nbsp; □&nbsp;计算机科学、信息技术&nbsp;&nbsp;&nbsp;</p><p>□&nbsp;工程学、物理、天文学&nbsp; &nbsp; &nbsp; *社会科学项目不予评审&nbsp;</p><figure class=\"table\"><table><tbody><tr><td colspan=\"2\">创意名称</td><td colspan=\"4\">&nbsp;</td><td rowspan=\"5\"><p>一寸照*</p><p>（电子稿）</p></td></tr><tr><td colspan=\"2\"><p>创意口号</p><p>（限25字以内）</p></td><td colspan=\"4\">（写出对自己作品的概括或对“快乐创意”的体会，要求简洁明了、朗朗上口）</td></tr><tr><td colspan=\"2\">学生姓名</td><td>&nbsp;</td><td colspan=\"2\">性别</td><td>&nbsp;</td></tr><tr><td colspan=\"2\">学校名称（盖章）</td><td colspan=\"4\">&nbsp;</td></tr><tr><td colspan=\"2\">出生年月</td><td>&nbsp;</td><td colspan=\"2\">年级</td><td><i>（以2025年9月份开学后的年级为准）</i></td></tr><tr><td colspan=\"2\">手机号</td><td colspan=\"3\">&nbsp;</td><td>监护人手机号</td><td>&nbsp;</td></tr><tr><td colspan=\"2\">学籍号</td><td colspan=\"3\">&nbsp;</td><td>*身份证号</td><td>&nbsp;</td></tr><tr><td colspan=\"2\">合作者信息</td><td>姓名</td><td colspan=\"2\">性别</td><td>出生年月</td><td>一寸照*</td></tr><tr><td colspan=\"2\" rowspan=\"4\">合作者1</td><td>&nbsp;</td><td colspan=\"2\">&nbsp;</td><td>&nbsp;</td><td rowspan=\"4\">（电子稿）</td></tr><tr><td>学校名称</td><td colspan=\"3\">&nbsp;</td></tr><tr><td>*身份证号</td><td colspan=\"3\">&nbsp;</td></tr><tr><td>学籍号</td><td colspan=\"3\">&nbsp;</td></tr><tr><td colspan=\"2\">合作者信息</td><td>姓名</td><td colspan=\"2\">性别</td><td>出生年月</td><td>一寸照*</td></tr><tr><td colspan=\"2\" rowspan=\"4\">合作者2</td><td><p>&nbsp;</p><p>&nbsp;</p></td><td colspan=\"2\">&nbsp;</td><td>&nbsp;</td><td rowspan=\"4\">（电子稿）</td></tr><tr><td>学校名称</td><td colspan=\"3\">&nbsp;</td></tr><tr><td>*身份证号</td><td colspan=\"3\">&nbsp;</td></tr><tr><td>学籍号</td><td colspan=\"3\">&nbsp;</td></tr><tr><td colspan=\"2\">辅导教师姓名</td><td colspan=\"2\">工作单位</td><td colspan=\"2\">联系电话</td><td>申报者亲属（是/否）</td></tr><tr><td colspan=\"2\">&nbsp;</td><td colspan=\"2\">&nbsp;</td><td colspan=\"2\">&nbsp;</td><td>&nbsp;</td></tr><tr><td colspan=\"2\">&nbsp;</td><td colspan=\"2\">&nbsp;</td><td colspan=\"2\">&nbsp;</td><td>&nbsp;</td></tr><tr><td colspan=\"7\"><p>*申报表中请务必粘贴作者报名照的电子稿；中学生必须填写身份证号和学籍号，跨学段学生</p><p>可暂不填学籍号；参评学生及合作者不得超过3人，且必须是同一学段（小学、初中、高<br>中）的学生合作作品，按照对项目所负责任大小排列，第一作者必须是项目负责人；辅导教<br>师不得超过2人，并按对项目辅导所负责任大小排列，填写后该顺序将保持到赛事结束。</p><p>&nbsp;</p><p>&nbsp;</p><p>创意构思（约200字）阐述你的创意灵感，是如何想到这项创意设计的。</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td colspan=\"7\"><p>科学设计（约200字）阐述这项创意的科学原理、设计思路和实施途径，可以用哪些方式和步骤来实现它。</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td colspan=\"7\"><p>应用前景（约200字）如果这项创意设计得以实现，有哪些实用价值，它能为生活带来哪些便利。</p><p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p></td></tr><tr><td>项目准备采用的现场表现形式</td><td colspan=\"6\">□创意模型&nbsp; □虚拟动画 &nbsp;□小品表演&nbsp;□&nbsp;其它（含ppt讲解）</td></tr><tr><td><p>创意实施或试验所受条件的限制</p><p>（请打√）</p></td><td colspan=\"6\"><p>□&nbsp;项目有创意，但实施受到一定条件的限制（请在下面选项中勾选）</p><p>□有创意，但还没时间开展实验探究</p><p>□创意实施需要得到外界的设备或设施的支持</p><p>□创意实施在技术与设施上目前还有未能突破的难点（可在创意设计中具体说明）</p><p>□&nbsp;创意实施已获得一定外界的设备或设施的支持（请在下面简单描述）</p><p>&nbsp; 所依托机构或实验室为_________________，在该机构或实验室参与的科研项目名称为_________________，该项目参与时间_________________，该项目成果的用途为_________________，参与该项目的指导成员有_________________。</p></td></tr><tr><td><p>项目申报者</p><p>确认事宜</p></td><td colspan=\"6\"><p>&nbsp;</p><p>我（我们）遵守学术道德，崇尚严谨学风。在本届活动中所呈交的项目成果和资料，是本人在辅导老师的指导下，独立完成所取得的成果。除已明确注明和引用的内容外，本项目成果和资料不包含任何其他个人或集体已经发表或撰写过的作品及成果的内容。申报者有公开发表自己作品的权力，同时也同意无偿提供项目内容，由评选组织单位进行公益性展示并收入汇编。</p><p>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;<strong>申报者（或监护人）签名：</strong></p><p>&nbsp;</p><p><strong>辅导教师签名：</strong></p></td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></tbody></table></figure><p>&nbsp;</p><!-- wp:shortcode -->[shared_files file_upload=1 only_uploaded_files=1]<!-- /wp:shortcode -->'),
(23,	'iENA德国纽伦堡国际发明展',	'iENA国际赛',	'国际级',	'9月',	'创新实物、模型、方案、展板、PPT、视频',	NULL,	2,	NULL),
(24,	'IEYI世界青少年创新发明展',	'IEYI 国际赛',	'国际级',	'10月',	'作品说明书、视频',	NULL,	2,	NULL),
(25,	'长三角青年科技技能创新挑战赛',	'长三角青年科技技能创新挑战赛',	'省市级',	'7-8月',	'研究报告、摘要、视频',	NULL,	2,	NULL),
(26,	'未来科学家培养计划',	'未来科学家',	'国家级',	'1-3月',	'获奖证明',	'课题研究计划书',	2,	NULL),
(27,	'青少年科学研究院小研究员',	'小研究员',	'省市级',	'全年滚动',	'课题立项书、课题研究计划书',	'课题研究计划书',	2,	NULL),
(28,	'上海市青少年科学创新实践工作站',	'工作站',	'省市级',	'1-3月',	'自荐信、获奖证明、课题研究计划书',	'课题研究计划书',	2,	NULL);

DROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `sid` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `card_type` mediumint(8) unsigned zerofill DEFAULT NULL,
  `sex` enum('男','女') DEFAULT NULL,
  `birth` date DEFAULT NULL,
  `age` int DEFAULT NULL,
  `edu_primary_start` date DEFAULT NULL,
  `edu_primary_end` date DEFAULT NULL,
  `edu_primary_school` varchar(100) DEFAULT NULL,
  `edu_junior_start` date DEFAULT NULL,
  `edu_junior_end` date DEFAULT NULL,
  `edu_junior_school` varchar(100) DEFAULT NULL,
  `edu_senior_start` date DEFAULT NULL,
  `edu_senior_end` date DEFAULT NULL,
  `edu_senior_school` varchar(100) DEFAULT NULL,
  `current_grade` int DEFAULT NULL,
  `current_school` varchar(100) DEFAULT NULL,
  `father_name` varchar(50) DEFAULT NULL,
  `father_tel` varchar(20) DEFAULT NULL,
  `father_workplace` varchar(100) DEFAULT NULL,
  `father_position` varchar(50) DEFAULT NULL,
  `mother_name` varchar(50) DEFAULT NULL,
  `mother_tel` varchar(20) DEFAULT NULL,
  `mother_workplace` varchar(100) DEFAULT NULL,
  `mother_position` varchar(50) DEFAULT NULL,
  `has_researcher` tinyint(1) DEFAULT NULL,
  `wp_user_id` int DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='学生信息表';


DROP TABLE IF EXISTS `student_card`;
CREATE TABLE `student_card` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sid` int NOT NULL,
  `card_id` int NOT NULL,
  `card_count` int NOT NULL DEFAULT '0',
  `used_count` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sid` (`sid`),
  KEY `card_id` (`card_id`),
  CONSTRAINT `fk_student_card_card` FOREIGN KEY (`card_id`) REFERENCES `card` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_student_card_student` FOREIGN KEY (`sid`) REFERENCES `student` (`sid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `student_course`;
CREATE TABLE `student_course` (
  `sid` char(12) NOT NULL,
  `cid` char(6) NOT NULL,
  `score` int DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  PRIMARY KEY (`sid`,`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='学生课程报名情况';


DROP TABLE IF EXISTS `student_log`;
CREATE TABLE `student_log` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `sid` varchar(12) NOT NULL COMMENT '学生 ID',
  `cid` char(6) NOT NULL COMMENT '比赛 ID',
  `type` char(1) NOT NULL COMMENT '0=新建，1=修改',
  `reason` varchar(30) NOT NULL COMMENT '备注',
  `logdate` date NOT NULL COMMENT '记录时间',
  `addtime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`,`sid`,`cid`,`logdate`,`type`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='学生行为日志记录';


DROP TABLE IF EXISTS `user_admin`;
CREATE TABLE `user_admin` (
  `adminID` int unsigned NOT NULL,
  `adminName` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pwd` char(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permissions` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_student` json DEFAULT NULL,
  PRIMARY KEY (`adminID`),
  UNIQUE KEY `adminName` (`adminName`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_admin` (`adminID`, `adminName`, `pwd`, `permissions`, `access_student`) VALUES
(999,	'Admin',	'e10adc3949ba59abbe56e057f20f883e',	'addCourse,addStudent,changePassword,createAdmin,createCard,editAdmin,editAdminAccess,editStudent,editStudentCard,editStudentCourse,modifyCard,modifyCourse,queryLog,queueChoose,queueCourse,queueStudent,userManage',	NULL);

DROP TABLE IF EXISTS `user_student`;
CREATE TABLE `user_student` (
  `sid` char(12) NOT NULL,
  `pwd` char(32) DEFAULT NULL,
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='学生账户表';


-- 2025-08-14 09:45:15 UTC