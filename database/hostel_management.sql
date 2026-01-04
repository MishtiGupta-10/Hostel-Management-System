CREATE DATABASE HMS;
USE HMS;

CREATE TABLE HOSTEL (
	Hostel_Id INT PRIMARY KEY AUTO_INCREMENT,
    Name varchar(25),
    Type varchar(10),
    Capacity int,
    Warden_Id INT
);

CREATE TABLE Student (
	Student_Id INT PRIMARY KEY AUTO_INCREMENT,
    Fname VARCHAR(50) NOT NULL,
    Lname varchar(50),
    Gender varchar(5) NOT NULL,
    Contact_Info INT UNIQUE,
    Fee_Status boolean,
    Bdate date NOT NULL,
    Email varchar(50),
    Hostel_Id INT,
    Room_Id INT
);

DROP TABLE HMS.Student, HMS.complaint, HMS.home_entry, HMS.hostel, HMS.mess_record, HMS.mess_fees, HMS.room , HMS.warden;
DROP TABLE HMS.fee;

CREATE TABLE Room (
	Room_Id INT PRIMARY KEY AUTO_INCREMENT,
    Room_No INT UNIQUE NOT NULL,
    Hostel_Id INT,
    Capacity INT,
    Room_Type varchar(25),
    Fees int
);

CREATE TABLE Mess_Record(
	Mess_Id INT PRIMARY KEY AUTO_INCREMENT,
    Name Varchar(50) Unique,
    Hostel_Id INT,
    Type varchar(50)
);

CREATE TABLE Mess_Fees(
	MessFee_Id INT PRIMARY KEY auto_increment,
    Mess_Id INT,
    Student_Id INT,
    Meal varchar(25),
    Fess INT,
    Type varchar(25)
);

CREATE TABLE Home_Entry(
	Entry_Id int primary key auto_increment,
    Student_Id INT,
    Name varchar(50),
    Exit_Time datetime,
    Entry_Time datetime
);

CREATE TABLE Fee (
	Fee_Id int primary key auto_increment,
    Student_Id int,
    Amount INT,
    Status boolean
);

CREATE TABLE Complaint(
	Complaint_Id int primary key auto_increment,
    Hostel_Id int,
    Type varchar(100),
    Status BOOLEAN,
    Applied_On datetime,
    Resolved_On datetime,
    Remarks VARCHAR(100)
);

CREATE TABLE Warden (
	Warden_Id int primary key auto_increment,
    Name varchar(25),
    Contact_Info int unique,
    Gender varchar(25),
    Email VARCHAR(25)
);

ALTER TABLE Student
add constraint fk_hostel_id
foreign key (Hostel_Id)
references hostel (Hostel_Id);

alter table student
add constraint fk_room
foreign key (Room_Id)
references room (Room_Id);

alter table hostel 
add constraint fk_warden
foreign key (Warden_Id)
references warden(Warden_Id);

alter table mess_record
add constraint fk_Hostel_Mess
foreign key (Hostel_Id)
references hostel (Hostel_Id);

alter table home_entry
add constraint fk_student_entry
foreign key (Student_Id)
references Student (Student_Id);

alter table home_entry
drop foreign key fk_home;

alter table fee
add constraint fk_student_fee
foreign key (Student_Id)
references Student (Student_Id);

alter table mess_fees
add constraint fk_mess_fees
foreign key (Mess_Id)
references mess_record (Mess_Id);

alter table room
add constraint fk_HostelRoom
foreign key (Hostel_Id)
references Hostel (Hostel_Id);

alter table complaint 
add constraint fk_HostelComplaint
foreign key (Hostel_Id)
references Hostel (Hostel_Id);

alter table student 
add column password varchar(25);

create table admin (
	user_id int primary key auto_increment,
    username varchar(25) not null unique,
    password varchar(25) not null unique,
    type varchar(10) default 'admin'
);

alter table warden 
modify column contact_info varchar(10);

alter table student
modify column contact_info varchar(10);

alter table student 
modify column gender varchar(10);

alter table home_entry
add column status boolean;

alter table admin
drop column password;

alter table admin 
modify column password varchar(225);

alter table student
modify column password varchar(225);

alter table complaint
add column description varchar(250);

alter table fee
add column month varchar(15);

alter table fee 
drop column month;

delete from hostel 
where Warden_Id = 6;

delete from hostel 
where Warden_Id = 5;

select * from student;
desc student;

desc warden;

ALTER TABLE Student 
ADD Age INT;

CREATE TRIGGER Student_age_insert
BEFORE INSERT ON Student
FOR EACH ROW
SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.Bdate, CURDATE());

SELECT * FROM Student;

UPDATE Student 
SET Bdate = Bdate
WHERE Student_Id > 0;

CREATE TRIGGER Student_age_update
BEFORE UPDATE ON Student
FOR EACH ROW
SET NEW.age = TIMESTAMPDIFF(YEAR, NEW.Bdate, CURDATE());

DROP TRIGGER IF EXISTS Student_age;
DROP TRIGGER IF EXISTS Student_age_update;


SELECT timestampdiff(YEAR, Bdate, CURDATE()) AS Age
FROM Student;

SELECT 
	Student_Id AS Id,
    CONCAT(Fname, ' ', Lname) AS Name,
    timestampdiff(YEAR, Bdate, CURDATE()) AS Age,
    contact_info AS Contact,
    gender,
    Bdate,
    Email
FROM Student;

desc complaint;

alter table complaint
add column student_id int;

alter table complaint
add constraint fk_complaint_student
foreign key (student_id)
references student (Student_Id);

alter table room
add column occupied int;
    
desc room;

create table Notice (
	Notice_Id INT PRIMARY KEY auto_increment,
    Hostel_Id int,
    Warden_Id int,
    Title varchar(100),
    Description varchar(250),
    Date datetime
);

alter table Notice 
add constraint fk_notice_warden
foreign key (Warden_Id)
references Warden (Warden_Id);


alter table Notice 
add constraint fk_notice_hostel
foreign key (Hostel_Id)
references Hostel (Hostel_Id);

alter table Notice 
drop foreign key fk_notice_warden;

alter table Notice
drop column Warden_Id;

ALTER TABLE warden
ADD username VARCHAR(50),
ADD password VARCHAR(255);

CREATE TABLE Outing_Log (
    Log_Id INT AUTO_INCREMENT PRIMARY KEY,
    Entry_Id INT,
    Student_Id INT,
    Name VARCHAR(50),
    Entry_Time DATETIME,
    Exit_Time DATETIME,
    Completed_On DATETIME DEFAULT NOW()
);

ALTER TABLE Outing_Log
ADD CONSTRAINT fk_log_student
FOREIGN KEY (Student_Id)
REFERENCES Student(Student_Id)
ON DELETE SET NULL;

DELIMITER $$

CREATE TRIGGER after_outing_completed
AFTER UPDATE ON Home_Entry
FOR EACH ROW
BEGIN
    -- Check if outing is now completed
    IF NEW.Entry_Time IS NOT NULL 
       AND NEW.Exit_Time IS NOT NULL THEN

        -- Insert into log
        INSERT INTO Outing_Log (
            Entry_Id,
            Student_Id,
            Name,
            Entry_Time,
            Exit_Time,
            Completed_On
        )
        VALUES (
            NEW.Entry_Id,
            NEW.Student_Id,
            NEW.Name,
            NEW.Entry_Time,
            NEW.Exit_Time,
            NOW()
        );

        -- Remove from Home_Entry
        DELETE FROM Home_Entry
        WHERE Entry_Id = NEW.Entry_Id;
    END IF;
END $$

DELIMITER ;

select * from home_entry;
select * from complaint;
desc home_entry;
select * from home_entry;
select * from outing_log;

delete from home_entry where Entry_Id = 8;


select * from student;

create table attendance (
	Attendance_Id INT primary key auto_increment,
    Student_Id int,
    Name varchar(50),
    Room_Id int,
    time datetime
);

alter table attendance 
add constraint fk_attendance_student
foreign key (Student_Id)
references student (Student_Id);

alter table attendance 
add constraint fk_attendance_room
foreign key (Room_Id)
references room (Room_Id);

alter table attendance 
add column Room_No int;

desc attendance;
    
select * from attendance;

desc fee;

desc admin;
desc attendance;
desc complaint;
desc fee;
desc home_entry;
desc hostel;
desc notice;
desc room;
desc student;
desc warden;
    

select * from warden;
select * from home_entry;



