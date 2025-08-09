-- 数据库编码设置（可选）
drop database if EXISTS school;
create database school;
use school;
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;



-- 表: course
DROP TABLE IF EXISTS `course`;
CREATE TABLE `course` (
  `cid` INT AUTO_INCREMENT PRIMARY KEY COMMENT '课程ID',
  `competition_name` VARCHAR(255) NOT NULL COMMENT '比赛名称',
  `competition_short_name` VARCHAR(100) DEFAULT NULL COMMENT '比赛简称',
  `competition_level` VARCHAR(255) NOT NULL COMMENT '比赛级别',
  `submit_time` VARCHAR(100) NOT NULL COMMENT '申报时间',
  `submit_requirements` LONGTEXT COMMENT '申报要求',
  `student_requirements` LONGTEXT COMMENT '需要学生提交的材料',
  `card_requirement` INT DEFAULT 0 COMMENT '需要卡级别'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程/比赛信息表';

INSERT INTO `course` (`cid`, `competition_name`, `competition_short_name`, `competition_level`, `submit_time`, `submit_requirements`, `student_requirements`, `card_requirement`) VALUES
(1,	'Demo',	'Demo',	'宇宙级',	'时间尽头',	'This is a demo.',	'This is a demo.',	999),
(2,	'国际发明创新展览会（上交会）',	'上交会',	'国际级',	'3-4月',	'申报表、展板',	'作品说明文档',	1),
(3,	'上海市少年儿童乐创挑战系列活动',	'乐创挑战',	'省市级',	'9-12月',	'作品方案、视频',	'作品说明文档',	1),
(4,	'上海市青少年创新成果展',	'青少年成果展',	'省市级',	'4-5月',	'项目说明书、照片',	'作品说明文档',	1),
(5,	'iENA德国纽伦堡国际发明展（中国站）',	'iENA中国区',	'国际级',	'9月',	'创新实物、模型、方案、展板、PPT[200字英文介绍]、视频',	'作品说明文档、项目演示文稿',	1),
(6,	'IEYI世界青少年创新发明展（中国站）-创意设计',	'IEYI中国区',	'国际级',	'3-4月',	'作品说明书、视频',	'作品说明文档',	1),
(7,	'杂志期刊发表',	'期刊发表',	'国家级',	'不定期',	'文章',	'征文稿件',	1),
(8,	'科奇空间——家庭科创角',	'科奇空间',	'省市级',	'5-7月',	'设计稿',	'作品说明文档',	1),
(9,	'小小科普讲解员大赛',	'小小科普讲解员大赛',	'省市级',	'5-6月',	'讲解稿、讲解视频',	'讲解稿件',	1),
(10,	'青少年科学诠释者',	'青少年科学诠释者',	'省市级',	'6-9月',	'研究报告/设计方案/科普作品',	'作品说明文档',	1),
(11,	'IEYI世界青少年创新发明展（中国站）-科幻画',	'IEYI中国区（科幻画）',	'国际级',	'3-4月',	'科幻画作、作品说明',	'科幻画作',	1),
(12,	'上海市青少年科技创新大赛-科幻画',	'青创赛（科幻画）',	'声世界',	'1-3月',	'科幻画作、作品说明',	'科幻画作',	1),
(13,	'“天问杯”好问题大赛',	'天问杯',	'省市级',	'9-10月',	'申报表（含问题与实践过程说明）',	'作品说明文档',	1),
(14,	'青少年科技创新大赛',	'青创赛（科幻画）',	'国家级',	'10-12月',	'研究报告、研究日志、查新报告',	NULL,	2),
(15,	'上海市百万青少年争创“明日科技之星”评选活动',	'明日科技之星',	'省市级',	'10-次年5月',	'研究报告、研究日志、查新报告',	NULL,	2),
(16,	'上海市雏鹰杯红领巾科创达人挑战赛',	'雏鹰杯',	'省市级',	'9-12月',	'研究报告、研究日志、装置',	NULL,	2),
(17,	'顶尖科学家论坛“科学T大会”',	'科学T大会',	'国际级',	'10月',	'学术简历、创新提案、英文摘要',	'创新提案初稿',	2),
(18,	'上海市青少年创新成果展',	'青少年成果展',	'省市级',	'7-8月',	'作品视频、设计草图',	NULL,	2),
(19,	'国际发明创新展览会（上交会）',	'上交会',	'国际级',	'3-4月',	'申报表、展板',	NULL,	2),
(20,	'宋庆龄少年儿童发明奖',	'宋庆龄',	'国家级',	'3-5月',	'发明说明书、原型照片、研究报告',	NULL,	2),
(21,	'水科技发明赛',	'水科技',	'国际级',	'4-6月',	'发明设计图、水环境相关数据、研究报告',	NULL,	2),
(22,	'赛复创智杯上海市青少年科技创意设计评选活动',	'赛复创智杯',	'省市级',	'9-12月',	'设计方案、原型演示视频、研究报告',	NULL,	2),
(23,	'iENA德国纽伦堡国际发明展',	'iENA国际赛',	'国际级',	'9月',	'创新实物、模型、方案、展板、PPT、视频',	NULL,	2),
(24,	'IEYI世界青少年创新发明展',	'IEYI 国际赛',	'国际级',	'10月',	'作品说明书、视频',	NULL,	2),
(25,	'长三角青年科技技能创新挑战赛',	'长三角青年科技技能创新挑战赛',	'省市级',	'7-8月',	'研究报告、摘要、视频',	NULL,	2),
(26,	'未来科学家培养计划',	'未来科学家',	'国家级',	'1-3月',	'获奖证明',	'课题研究计划书',	2),
(27,	'青少年科学研究院小研究员',	'小研究员',	'省市级',	'全年滚动',	'课题立项书、课题研究计划书',	'课题研究计划书',	2),
(28,	'上海市青少年科学创新实践工作站',	'工作站',	'省市级',	'1-3月',	'自荐信、获奖证明、课题研究计划书',	'课题研究计划书',	2);
-- 2025-08-01 02:29:57 UTC
-- 表: studentDROP TABLE IF EXISTS `student`;
CREATE TABLE `student` (
  `sid` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(50) NOT NULL,
  `card_type` MEDIUMINT NOT NULL,
  `sex` ENUM('男','女'),
  `birth` DATE,
  `age` INT,
  `edu_primary_start` DATE,
  `edu_primary_end` DATE,
  `edu_primary_school` VARCHAR(100),
  `edu_junior_start` DATE,
  `edu_junior_end` DATE,
  `edu_junior_school` VARCHAR(100),
  `edu_senior_start` DATE,
  `edu_senior_end` DATE,
  `edu_senior_school` VARCHAR(100),
  `current_grade` INT,
  `current_school` VARCHAR(100),
  `father_name` VARCHAR(50),
  `father_tel` VARCHAR(20),
  `father_workplace` VARCHAR(100),
  `father_position` VARCHAR(50),
  `mother_name` VARCHAR(50),
  `mother_tel` VARCHAR(20),
  `mother_workplace` VARCHAR(100),
  `mother_position` VARCHAR(50),
  `has_researcher` TINYINT(1),
  `wp_user_id` INT -- 新增字段，用于存储 WordPress 用户 ID
  -- FOREIGN KEY (`wp_user_id`) REFERENCES wp_users(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生信息表';


-- 表: student_course
DROP TABLE IF EXISTS `student_course`;
CREATE TABLE `student_course` (
  `sid` CHAR(12),
  `cid` CHAR(6),
  `score` INT,
  `status` CHAR(1),
  PRIMARY KEY (`sid`, `cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生课程报名情况';

-- 表: student_log
DROP TABLE IF EXISTS `student_log`;
CREATE TABLE `student_log` (
  `id` INT AUTO_INCREMENT COMMENT 'ID',
  `sid` VARCHAR(12) NOT NULL COMMENT '学生 ID',
  `cid` CHAR(6) NOT NULL COMMENT '比赛 ID',
  `type` CHAR(1) NOT NULL COMMENT '0=新建，1=修改',
  `reason` VARCHAR(30) NOT NULL COMMENT '备注',
  `logdate` DATE NOT NULL COMMENT '记录时间',
  `addtime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`,`sid`, `cid`, `logdate`, `type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生行为日志记录';
-- ALTER TABLE student_log ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST;





-- 创建 user_admin 表
CREATE TABLE IF NOT EXISTS user_admin (
    adminID INT UNSIGNED NOT NULL PRIMARY KEY,          -- 管理员ID，整型，作为主键
    adminName VARCHAR(50) NOT NULL UNIQUE,              -- 管理员用户名，唯一约束
    pwd CHAR(32) NOT NULL,                              -- 密码，MD5加密后的32位字符串
    permissions VARCHAR(255) DEFAULT NULL               -- 以逗号分隔的权限列表字符串
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 创建默认管理员示例（可选）
INSERT INTO user_admin (adminID, adminName, pwd, permissions)
VALUES (999, 'Admin', MD5('123456'), 'addStudent,queueStudent,editStudent,queueCourse,addCourse,modifyCourse,queueChoose,editStudentCourse,queryLog,userManage,changePassword,createAdmin')
ON DUPLICATE KEY UPDATE adminName = VALUES(adminName);


-- 表: user_student
DROP TABLE IF EXISTS `user_student`;
CREATE TABLE `user_student` (
  `sid` CHAR(12) PRIMARY KEY,
  `pwd` CHAR(32)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='学生账户表';

SET FOREIGN_KEY_CHECKS = 1;


ALTER TABLE student_log ADD COLUMN url VARCHAR(255);
-- ALTER TABLE course
ALTER TABLE course ADD COLUMN default_content LONGTEXT DEFAULT NULL;


-- 先确保被引用表 student 的 sid 是 INT 或 INT UNSIGNED（示例：INT）
-- 然后创建 card、student_card（示例）
CREATE TABLE card (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  allowed_courses TEXT NOT NULL,
  max_courses INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE student_card (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sid INT NOT NULL,
  card_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 0,
  INDEX (sid),
  INDEX (card_id),
  CONSTRAINT fk_student_card_card FOREIGN KEY (card_id) REFERENCES card(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_student_card_student FOREIGN KEY (sid) REFERENCES student(sid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
