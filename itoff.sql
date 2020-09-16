-- iT-off Event & Booking Database
-- Nick Giordano, 2018-01-27, version 2.0

-- remove all existing tables, to be re-created later
DROP TABLE IF EXISTS Booking, Delegate, Event, Venue, Course;

-- create table
CREATE TABLE Course(
	ID INT NOT NULL AUTO_INCREMENT,
	Course_Title VARCHAR(70),
	Course_Fee DECIMAL(7,2),
	Duration INT,
	PRIMARY KEY (ID),
	CHECK (csDuration <= 5)
);
-- create table
CREATE TABLE Venue(
	ID INT NOT NULL AUTO_INCREMENT,
	Venue_Name VARCHAR(70),
	Address VARCHAR(255),
	Telephone VARCHAR(11),
	Hotel_Fee DECIMAL(5,2),
	PRIMARY KEY (ID)
);
-- create table
CREATE TABLE Event(
	ID INT NOT NULL AUTO_INCREMENT,
	Start_Date DATE,
	Course_ID INT,
	Venue_ID INT,
	PRIMARY KEY (ID),
	FOREIGN KEY (Course_ID) REFERENCES Course(ID),
	FOREIGN KEY (Venue_ID) REFERENCES Venue(ID)
);
ALTER TABLE Event ADD UNIQUE INDEX (Start_Date, Course_ID, Venue_ID);
-- create table
CREATE TABLE Delegate(
	ID INT NOT NULL AUTO_INCREMENT,
	Surname VARCHAR(70),
	Forename VARCHAR(70),
	Address VARCHAR(255),
	Telephone VARCHAR(11),
	PRIMARY KEY (ID)
);
-- create unique index to prevent duplicate records
ALTER TABLE Delegate ADD UNIQUE INDEX (Surname, Forename, Telephone);
-- create table
CREATE TABLE Booking(
	ID INT NOT NULL AUTO_INCREMENT,
	Event_ID INT,
	Delegate_ID INT,
	Presenter BOOLEAN,
	Bed_and_Breakfast BOOLEAN,
	PRIMARY KEY (ID),
	FOREIGN KEY (Event_ID) REFERENCES Event(ID),
	FOREIGN KEY (Delegate_ID) REFERENCES Delegate(ID)
);
-- create unique index to prevent duplicate records
ALTER TABLE Booking ADD UNIQUE INDEX (Event_ID, Delegate_ID);

-- create view for PHP table
CREATE OR REPLACE VIEW viewEvent AS
SELECT Event.ID, Start_Date, Course_Title AS Course, Venue_Name AS Venue
FROM (Event INNER JOIN Course ON Course_ID = Course.ID)
INNER JOIN Venue ON Venue_ID = Venue.ID;

-- create view for PHP table
CREATE OR REPLACE VIEW viewBooking AS
SELECT Booking.ID, Start_Date, Course_Title AS Course, Venue_Name AS Venue,
CONCAT(Surname, ', ', Forename) AS Delegate, Presenter, Bed_and_Breakfast
FROM (((Event INNER JOIN Course ON Course_ID = Course.ID)
		INNER JOIN Venue ON Venue_ID = Venue.ID)
	INNER JOIN Booking ON Event.ID = Event_ID)
INNER JOIN Delegate ON Delegate_ID = Delegate.ID;

-- create view for form
CREATE OR REPLACE VIEW formEvent AS
SELECT Event.ID, Start_Date,
CONCAT(Course_Title, ' [', Course.ID, ']') AS Course_ID_fk,
CONCAT(Venue_Name, ' [', Venue.ID, ']') AS Venue_ID_fk
FROM (Event
INNER JOIN Course ON Course_ID = Course.ID)
INNER JOIN Venue ON Venue_ID = Venue.ID;

-- create view for form
CREATE OR REPLACE VIEW formBooking AS
SELECT Booking.ID,
CONCAT(Start_Date, ' -- ', Course_Title, ' @ ', Venue_Name, ' [', Event.ID, ']') AS Event_ID_fk,
CONCAT(Surname, ', ', Forename, ' [', Delegate.ID, ']') AS Delegate_ID_fk, Presenter, Bed_and_Breakfast
FROM (((Event INNER JOIN Course ON Course_ID = Course.ID)
		INNER JOIN Venue ON Venue_ID = Venue.ID)
	INNER JOIN Booking ON Event.ID = Event_ID)
INNER JOIN Delegate ON Delegate_ID = Delegate.ID;

-- create view for select list
CREATE OR REPLACE VIEW listCourse AS
SELECT CONCAT(Course_Title, ' [', ID, ']') AS list
FROM Course
ORDER BY list;

-- create view for select list
CREATE OR REPLACE VIEW listVenue AS
SELECT CONCAT(Venue_Name, ' [', ID, ']') AS list
FROM Venue
ORDER BY list;

-- create view for select list
CREATE OR REPLACE VIEW listEvent AS
SELECT CONCAT(Start_Date, ' -- ', Course_Title, ' @ ', Venue_Name, ' [', Event.ID, ']') AS list
FROM (Event INNER JOIN Course ON Course_ID = Course.ID) INNER JOIN Venue ON Venue_ID = Venue.ID
ORDER BY list;

-- create view for select list
CREATE OR REPLACE VIEW listDelegate AS
SELECT CONCAT(Surname, ', ', Forename, ' [', ID, ']') AS list
FROM Delegate
ORDER BY list;

-- populate table with sample data
INSERT INTO Course (Course_Title, Course_Fee, Duration) VALUES
('Computer Architecture',		800,	2),
('Systems Analysis',			350,	3),
('Object-oriented Programming',	2000,	5),
('Networks',					800,	1),
('Databases',					1400,	3),
('Web Programming',				1000,	4);
-- populate table with sample data
INSERT INTO Venue (Venue_Name, Address, Telephone, Hotel_Fee) VALUES
('Premier Inn Maidstone',	'20 Hadley Road, Maidstone, ME14 1RH',	'07196287100',	90),
('Travelodge Slough',		'1 Emmett Hill, Slough, SL7 3HH',		'07356735213',	80),
('Premier Inn Swindon',		'100 Ocktram Road, Swindon, SN5 1HE',	'07863822518',	75),
('Holiday Inn Exeter',		'60 Gordon Lane, Exeter, EX4 1EY',		'07718056789',	75),
('Premier Inn Burton',		'18 Colesmith Road, Burton, DE14 1BW',	'07451364105',	70);
-- populate table with sample data
INSERT INTO Event (Start_Date, Course_ID, Venue_ID) VALUES
('2017-04-12',	2,	4),
('2017-04-24',	3,	1),
('2017-05-08',	4,	2),
('2017-06-20',	1,	1),
('2017-06-21',	2,	1),
('2017-07-04',	6,	4),
('2017-07-10',	3,	5),
('2017-07-31',	6,	2),
('2017-08-14',	1,	4),
('2017-09-25',	2,	1),
('2018-02-26',	4,	1),
('2018-02-28',	1,	4),
('2018-03-09',	4,	3),
('2018-04-24',	6,	1),
('2018-04-30',	3,	5),
('2018-05-02',	1,	5),
('2018-05-23',	2,	1),
('2018-06-04',	5,	2),
('2018-07-02',	3,	4),
('2018-08-27',	3,	3);
-- populate table with sample data
INSERT INTO Delegate (Surname, Forename, Address, Telephone) VALUES
('Ferguson', 'Philip', '58 Bunting Road, Chatham, ME4 627', '07163895189'),
('Downey', 'Damien', '24 Horsfield Close, London, SE1 1NR', '07743420533'),
('Winfield', 'Tara', '107 Shoreline Road, Plymouth, PL5 2HB', '07560983754'),
('Garrick', 'Robar', '19B Dandelion Avenue, Swadlincote, DE11 8JE', '07507246473'),
('Reynolds', 'Marvin', '23 Scott Lane, London, N7 4RT', '07113385176'),
('Tomlins', 'Charlie', 'The Crofthouse, Swindon Street, Swindon, SN5 1HE', '07845361187'),
('Menjivar', 'Rebecca', '95 Langley Road, Belper, DE56 0DA', '07755673533'),
('Smith', 'Sol', '18 Primrose Street, Cambridge, CB1 6YH', '07402548762'),
('Horvath', 'Kieran', '79 Fieldshaw Avenue, Slough, SL3 6GH', '07300557238'),
('Giggs', 'Peter', '1 Bluebell Road, Cardiff, CF14 4XW', '07560073963'),
('Magana', 'Julianna', '34 Threepwood Estates, Leeds, BD11 1NQ', '07545905267'),
('Lake', 'Jon', '60 Riverside Close, Birmingham, B18 8JK', '07456189064'),
('Lyons', 'Sinead', '601 Main Street, Colchester, CO1 1DF', '07131293930'),
('Hamilton', 'Yolanda', '18 Juniper Street, Colchester, CO5 9HD', '07165103884'),
('Colt', 'Harrison', '29 Action Avenue, Great Yarmouth, NR31 2NE', '07398614238'),
('Dandelion', 'Darren', '9 Elevation Close, Liverpool, L5 7SD', '07664022559'),
('Linderman', 'Beckie', '1 Quick Street, Blackpool, FY1 3NE', '07570563511'),
('Atfield', 'Ginger', '48 Bond Road, Sheffield, S17 1DE', '07232781124'),
('Burke', 'Fred', '17 Church Street, Torquay, TQ1 1AH', '07962628337'),
('Cooke', 'Yasmin', '100 Park Road, Manchester, M5 6UQ', '07346920707'),
('Longley', 'Richard', '16 Starlet Avenue, Slough, SL3 0NH', '07353241834'),
('Barkson', 'Crane', '34 Driver Street, Leeds, LS8 6DR', '07567292643'),
('Phillips', 'Amy', '77 Golden Ridge Street, Carlisle, CA1 1DN', '07409918123'),
('Barrels', 'John', '123 Church Street, Cheltenham, GL52 3JE', '07917677171'),
('Haywood', 'Geraldine', '67 Agile Street, Mansfield, NG18 7FG', '07628239451'),
('Ackman', 'Stuart', '12 Mint Road, Burton upon Trent, DE14 9OP', '07701352003'),
('Musiev', 'Alexander', '23 Coldsworth Road, Warwick, CV34 4BT', '07666694486'),
('Bentley', 'Shauna', '13 Violet Avenue, Exeter, EX2 3RR', '07151240094'),
('Franklin', 'Forrest', '275 Kilburn Road, Burton upon Trent, DE14 3ER', '07618074807'),
('Charlton', 'Ian', '23 James Avenue, Liverpool, L2 6CC', '07486925463'),
('Fenceman', 'Jack', '6 Teapot Lane, Sheffield, S9 4KK', '07583933290'),
('Meek', 'John', '20 Porterman Road, Aberdeen, AB10 6FG', '07160882784'),
('Bluvane', 'Lauren', '35 Grave Street, Birmingham, B13 8NM', '07529552426'),
('Jones', 'Benedict', '83 Spondon Lane, Maidenhead, SL8 1KN', '07172598831'),
('Whitman', 'Janet', '50 Selby Road, Chippenham, SN14 0JE', '07727575767'),
('Greenwood', 'Thomas', '182 Entley Street, Birmingham, B25 2AB', '07403372738'),
('Winthorpe', 'Emma', 'Echo Manor, Title Street, Maidstone, ME16 9UJ', '07450960281'),
('Lawson', 'Sophie', '45 Fairweather Lane, Appleby Magna, DE12 7AL', '07599647900'),
('Denby', 'Harriet', '12 Carrot Close, Swindon, SN3 6EX', '07565866057'),
('Rahman', 'Andrew', '61 Shrub Road, Gillingham, ME8 1BG', '07929057817'),
('Spiby', 'Kenneth', '33 Kellogg Road, Bradford, BD2 2PW', '07958681242'),
('Cornflour', 'Sarah', '89 Worthington Crescent, Nottingham, NG7 1FW', '07589740195'),
('Lampley', 'Loretta', '50 Tenpence Road, Manchester, M21 5GH', '07775125588'),
('Toldo', 'Immacolata', '78 Queen Road, Glasgow, G43 6TB', '07199953675'),
('Fischer', 'Jurgen', '14 Clockwood Street, Sunderland, SR1 1XG', '07189317081'),
('Dempsey', 'River', '15 Fork Road, Slough, SL7 8IJ', '07576927250'),
('Wright', 'Katie', '2 Marlborough Street, Exeter, EX4 1LE', '07795591042'),
('Carlton-Jones', 'Pam', '5 Guard Road, Tonbridge, TN9 1JP', '07253097973'),
('Maleenon', 'Thaksin', '20 Hallwood Street, Hull, HU1 8DV', '07525199382'),
('Redfield', 'Roxanne', '87 Fish Street, Lincoln, LN1 6SO', '07392136657');
-- populate table with sample data
INSERT INTO Booking (Event_ID, Delegate_ID, Presenter, Bed_and_Breakfast) VALUES
(1, 15, 1, 1),
(1, 47, 0, 0),
(1, 36, 0, 0),
(1, 22, 0, 1),
(1, 43, 0, 0),
(1, 44, 0, 1),
(1, 45, 0, 0),
(1, 11, 0, 1),
(1, 19, 0, 0),
(1, 28, 0, 0),
(2, 2, 1, 0),
(2, 40, 0, 0),
(2, 48, 0, 0),
(2, 16, 0, 1),
(2, 27, 0, 1),
(2, 23, 0, 1),
(2, 31, 0, 1),
(2, 20, 0, 1),
(2, 1, 0, 0),
(2, 37, 0, 0),
(3, 46, 0, 0),
(3, 9, 1, 0),
(3, 6, 0, 0),
(3, 21, 0, 0),
(3, 16, 0, 0),
(3, 20, 0, 0),
(3, 33, 0, 0),
(3, 43, 0, 0),
(3, 49, 0, 0),
(3, 34, 0, 0),
(3, 5, 0, 0),
(4, 37, 0, 0),
(4, 40, 0, 0),
(4, 3, 1, 1),
(4, 48, 0, 1),
(4, 50, 0, 1),
(4, 17, 0, 1),
(4, 31, 0, 1),
(4, 36, 0, 1),
(4, 11, 0, 1),
(5, 15, 1, 1),
(5, 4, 0, 1),
(5, 1, 0, 0),
(5, 18, 0, 1),
(5, 33, 0, 1),
(5, 23, 0, 1),
(5, 37, 0, 0),
(5, 48, 0, 0),
(5, 40, 0, 1),
(6, 5, 1, 1),
(6, 10, 0, 1),
(6, 47, 0, 0),
(6, 49, 0, 1),
(6, 30, 0, 1),
(6, 18, 0, 1),
(6, 45, 0, 1),
(6, 28, 0, 0),
(6, 6, 0, 1),
(6, 19, 0, 0),
(7, 26, 0, 0),
(7, 2, 1, 1),
(7, 12, 0, 0),
(7, 45, 0, 1),
(7, 41, 0, 0),
(7, 30, 0, 1),
(7, 18, 0, 1),
(7, 32, 0, 1),
(7, 29, 0, 0),
(7, 38, 0, 0),
(8, 46, 0, 0),
(8, 8, 0, 1),
(8, 9, 1, 0),
(8, 34, 0, 0),
(8, 23, 0, 1),
(8, 25, 0, 1),
(8, 50, 0, 1),
(8, 22, 0, 1),
(8, 41, 0, 0),
(8, 21, 0, 0),
(8, 13, 0, 1),
(9, 3, 1, 0),
(9, 19, 0, 1),
(9, 47, 0, 0),
(9, 10, 1, 1),
(9, 7, 0, 1),
(9, 45, 0, 1),
(9, 25, 0, 1),
(9, 17, 0, 1),
(9, 44, 0, 0),
(9, 20, 0, 1),
(9, 33, 0, 0),
(9, 28, 0, 0),
(10, 15, 1, 1),
(10, 37, 0, 0),
(10, 48, 0, 0),
(10, 44, 0, 0),
(10, 31, 0, 0),
(10, 50, 0, 0),
(10, 36, 0, 1),
(10, 25, 0, 1),
(10, 14, 0, 0),
(11, 40, 0, 0),
(11, 10, 1, 0),
(11, 23, 0, 0),
(11, 44, 1, 0),
(11, 50, 0, 0),
(11, 32, 0, 0),
(11, 33, 0, 0),
(11, 2, 0, 0),
(11, 8, 0, 0),
(11, 30, 0, 0),
(11, 15, 0, 0),
(12, 19, 0, 0),
(12, 11, 1, 1),
(12, 3, 0, 1),
(12, 47, 0, 0),
(12, 16, 0, 1),
(12, 17, 0, 1),
(12, 31, 0, 1),
(12, 32, 0, 1),
(12, 20, 0, 0),
(12, 8, 0, 1);