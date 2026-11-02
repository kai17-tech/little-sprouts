-- =========================================================
-- Little Sprouts Learning Center - Student Table
-- Run this in phpMyAdmin (Import tab) or the MySQL console.
-- =========================================================

CREATE DATABASE IF NOT EXISTS little_sprouts;
USE little_sprouts;

DROP TABLE IF EXISTS student;

CREATE TABLE student (
    StudentID   INT          NOT NULL PRIMARY KEY,
    FirstName   VARCHAR(50)  NOT NULL,
    LastName    VARCHAR(50)  NOT NULL,
    BirthDate   DATE         NOT NULL,
    Gender      ENUM('Male', 'Female') NOT NULL,
    GradeLevel  VARCHAR(20)  NOT NULL,
    Status      ENUM('Active', 'Pending', 'Inactive') NOT NULL DEFAULT 'Pending'
);

-- 30 sample records (22 Active, 5 Pending, 3 Inactive)
INSERT INTO student (StudentID, FirstName, LastName, BirthDate, Gender, GradeLevel, Status) VALUES
(1001, 'Sofia',     'Reyes',       '2019-04-12', 'Female', 'Kinder',  'Active'),
(1002, 'Ethan',     'Dizon',       '2018-11-23', 'Male',   'Grade 1', 'Active'),
(1003, 'Liam',      'Santos',      '2017-02-15', 'Male',   'Grade 2', 'Active'),
(1004, 'Mia',       'Cruz',        '2016-08-05', 'Female', 'Grade 3', 'Active'),
(1005, 'Noah',      'Garcia',      '2015-01-30', 'Male',   'Grade 4', 'Active'),
(1006, 'Aria',      'Mendoza',     '2014-06-18', 'Female', 'Grade 5', 'Pending'),
(1007, 'Lucas',     'Torres',      '2013-12-10', 'Male',   'Grade 6', 'Active'),
(1008, 'Isabella',  'Ramos',       '2019-09-01', 'Female', 'Kinder',  'Active'),
(1009, 'Gabriel',   'Navarro',     '2017-05-25', 'Male',   'Grade 2', 'Pending'),
(1010, 'Chloe',     'Bautista',    '2015-03-14', 'Female', 'Grade 4', 'Inactive'),
(1011, 'Mateo',     'Aquino',      '2014-07-09', 'Male',   'Grade 5', 'Active'),
(1012, 'Hannah',    'Del Rosario', '2013-02-28', 'Female', 'Grade 6', 'Active'),
(1013, 'Daniel',    'Mercado',     '2019-01-11', 'Male',   'Kinder',  'Active'),
(1014, 'Samantha',  'Villanueva',  '2018-05-19', 'Female', 'Grade 1', 'Active'),
(1015, 'Jacob',     'Castillo',    '2017-10-04', 'Male',   'Grade 2', 'Active'),
(1016, 'Ella',      'Fernandez',   '2016-03-22', 'Female', 'Grade 3', 'Active'),
(1017, 'Nathan',    'Gonzales',    '2015-09-08', 'Male',   'Grade 4', 'Pending'),
(1018, 'Sophia',    'Lopez',       '2014-11-30', 'Female', 'Grade 5', 'Active'),
(1019, 'Elijah',    'Rivera',      '2013-04-17', 'Male',   'Grade 6', 'Inactive'),
(1020, 'Amara',     'Pascual',     '2019-06-25', 'Female', 'Kinder',  'Pending'),
(1021, 'Caleb',     'Domingo',     '2018-02-13', 'Male',   'Grade 1', 'Active'),
(1022, 'Zoe',       'Salazar',     '2017-08-29', 'Female', 'Grade 2', 'Active'),
(1023, 'Adrian',    'Manalo',      '2016-12-03', 'Male',   'Grade 3', 'Active'),
(1024, 'Leah',      'Valdez',      '2015-07-21', 'Female', 'Grade 4', 'Active'),
(1025, 'Joshua',    'Ocampo',      '2014-01-16', 'Male',   'Grade 5', 'Active'),
(1026, 'Bianca',    'Soriano',     '2013-10-05', 'Female', 'Grade 6', 'Inactive'),
(1027, 'Miguel',    'Lacson',      '2019-03-14', 'Male',   'Kinder',  'Active'),
(1028, 'Andrea',    'Dela Cruz',   '2018-09-27', 'Female', 'Grade 1', 'Pending'),
(1029, 'Rafael',    'Tolentino',   '2016-05-02', 'Male',   'Grade 3', 'Active'),
(1030, 'Camille',   'Bernardo',    '2017-12-19', 'Female', 'Grade 2', 'Active');
