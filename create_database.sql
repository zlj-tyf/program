CREATE TABLE student (
    sid INT PRIMARY KEY AUTO_INCREMENT COMMENT '学生编号',
    name VARCHAR(50) NOT NULL COMMENT '姓名',
    sex ENUM('男', '女') NOT NULL COMMENT '性别',
    birth DATE COMMENT '出生年月',
    age INT GENERATED ALWAYS AS (TIMESTAMPDIFF(YEAR, birth, CURDATE())) STORED COMMENT '年龄',

    -- 教育经历：小学、初中、高中（可为空）
    primary_start_year YEAR NULL,
    primary_end_year YEAR NULL,
    primary_school_name VARCHAR(100) NULL,

    junior_start_year YEAR NULL,
    junior_end_year YEAR NULL,
    junior_school_name VARCHAR(100) NULL,

    senior_start_year YEAR NULL,
    senior_end_year YEAR NULL,
    senior_school_name VARCHAR(100) NULL,

    current_grade VARCHAR(50) GENERATED ALWAYS AS (
        CASE
            WHEN YEAR(CURDATE()) - senior_start_year BETWEEN 0 AND 2 THEN
                CONCAT('高', YEAR(CURDATE()) - senior_start_year + 1, '年级')
            WHEN YEAR(CURDATE()) - junior_start_year BETWEEN 0 AND 2 THEN
                CONCAT('初', YEAR(CURDATE()) - junior_start_year + 1, '年级')
            WHEN YEAR(CURDATE()) - primary_start_year BETWEEN 0 AND 5 THEN
                CONCAT('小', YEAR(CURDATE()) - primary_start_year + 1, '年级')
            ELSE '未知'
        END
    ) STORED COMMENT '当前年级',

    current_school VARCHAR(100) GENERATED ALWAYS AS (
        CASE
            WHEN senior_end_year IS NULL THEN senior_school_name
            WHEN junior_end_year IS NULL THEN junior_school_name
            WHEN primary_end_year IS NULL THEN primary_school_name
            ELSE NULL
        END
    ) STORED COMMENT '当前学校',

    father_name VARCHAR(50),
    father_tel VARCHAR(50),
    father_work_unit VARCHAR(100),
    father_title VARCHAR(100),

    mother_name VARCHAR(50),
    mother_tel VARCHAR(50),
    mother_work_unit VARCHAR(100),
    mother_title VARCHAR(100),

    has_researcher BOOLEAN DEFAULT FALSE COMMENT '家中是否有科研人员',

    email VARCHAR(100),
    tel VARCHAR(20)
);