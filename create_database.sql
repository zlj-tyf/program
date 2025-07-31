CREATE DATABASE IF NOT EXISTS school;
USE school;

CREATE TABLE student (
    sid INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL,
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
