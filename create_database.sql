CREATE DATABASE IF NOT EXISTS school;
USE school;

CREATE TABLE student (
    sid INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
    -- 卡的类型
    card_type INT DEFAULT 0 COMMENT '1 -> S 2 -> SP',
    sex ENUM('男', '女') NOT NULL,
    birth DATE,
    age INT,  -- 改为普通字段

    -- 教育经历（小学、初中、高中）
    edu_primary_start DATE,
    edu_primary_end DATE,
    edu_primary_school VARCHAR(100),
    edu_junior_start DATE,
    edu_junior_end DATE,
    edu_junior_school VARCHAR(100),
    edu_senior_start DATE,
    edu_senior_end DATE,
    edu_senior_school VARCHAR(100),

    -- 当前年级和学校（年级改为手动选择）
    current_grade INT,  -- 1 ~ 12
    current_school VARCHAR(100),

    -- 父母信息
    father_name VARCHAR(50),
    father_tel VARCHAR(20),
    father_workplace VARCHAR(100),
    father_position VARCHAR(50),
    mother_name VARCHAR(50),
    mother_tel VARCHAR(20),
    mother_workplace VARCHAR(100),
    mother_position VARCHAR(50),

    -- 是否有科研人员
    has_researcher BOOLEAN DEFAULT FALSE

);


CREATE TABLE course (
    cid INT AUTO_INCREMENT PRIMARY KEY COMMENT '课程ID',
    competition_name VARCHAR(255) NOT NULL COMMENT '比赛名称',
    competition_level VARCHAR(255) NOT NULL COMMENT '比赛级别',
    submit_time VARCHAR(100) NOT NULL COMMENT '申报时间',
    submit_requirements LONGTEXT COMMENT '申报要求',
    student_requirements LONGTEXT COMMENT '需要学生提交的材料',
    card_requirement INT DEFAULT 0 COMMENT '需要级别>x的卡'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='课程/比赛信息表';

-- 表的结构 `student_course`
--

CREATE TABLE `student_course` (
  `sid` char(12) NOT NULL,
  `cid` char(6) NOT NULL,
  `score` int(3) DEFAULT NULL,
  `status` char(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- 表的结构 `student_log`
--

CREATE TABLE `student_log` (
  `sid` varchar(12) DEFAULT NULL,
  `type` char(1) DEFAULT NULL,
  `reason` varchar(30) DEFAULT NULL,
  `detail` varchar(100) DEFAULT NULL,
  `logdate` date DEFAULT NULL,
  `addtime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- 表的结构 `user_admin`
--

CREATE TABLE `user_admin` (
  `adminID` varchar(15) DEFAULT NULL,
  `adminName` varchar(15) DEFAULT NULL,
  `pwd` char(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `user_admin`
--

INSERT INTO `user_admin` (`adminID`, `adminName`, `pwd`) VALUES ('1', 'Admin1', 'e10adc3949ba59abbe56e057f20f883e');
INSERT INTO `user_admin` (`adminID`, `adminName`, `pwd`) VALUES ('999', 'Admin999', 'd41d8cd98f00b204e9800998ecf8427e');

-- --------------------------------------------------------

--
-- 表的结构 `user_student`
--

CREATE TABLE `user_student` (
  `sid` char(12) NOT NULL,
  `pwd` char(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-

--
-- 转储表的索引
--

--
-- 表的索引 `course`
--
ALTER TABLE `course`
  ADD UNIQUE KEY `cid_2` (`cid`),
  ADD KEY `cid` (`cid`);

--
-- 表的索引 `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`sid`),
  ADD UNIQUE KEY `sid` (`sid`);

--
-- 表的索引 `student_course`
--
ALTER TABLE `student_course`
  ADD KEY `sid` (`sid`),
  ADD KEY `cid` (`cid`);

--
-- 表的索引 `student_log`
--
ALTER TABLE `student_log`
  ADD KEY `sid` (`sid`);

--
-- 表的索引 `user_admin`
--
ALTER TABLE `user_admin`
  ADD KEY `adminID` (`adminID`);

--
-- 表的索引 `user_student`
--
ALTER TABLE `user_student`
  ADD UNIQUE KEY `sid` (`sid`),
  ADD KEY `sid_2` (`sid`);
COMMIT;
