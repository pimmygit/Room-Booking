DROP DATABASE IF EXISTS meetingrooms;

CREATE DATABASE meetingrooms;

USE meetingrooms;

#
# Name: users
# Desc: Contains user accounts
# Last: 26.09.2007
#
CREATE TABLE IF NOT EXISTS users(
name			VARCHAR(50) NOT NULL,			# Users E-mail
mail			VARCHAR(50) NOT NULL PRI,		# Users Name
type			CHAR(5),				# Users account type
datetime		TIMESTAMP				# Time of creation
);

#
# Name: stat_access
# Desc: Contains site statistic
# Last: 22.04.2008
#
CREATE TABLE IF NOT EXISTS stat_access(
user			VARCHAR(50) NOT NULL,			# Users E-mail
host			VARCHAR(23),				# IP of the source host
page			VARCHAR(30) NOT NULL,			# Page accessed
os_name			VARCHAR(10),				# Name of the OS used
os_type			VARCHAR(10),				# Type of the OS used
browser_name		VARCHAR(5),				# Name of the browser used
browser_type		VARCHAR(5),				# Type of the browser used
datetime		TIMESTAMP				# Time of access
);

#
# Name: stat_online
# Desc: Contains site statistic
# Last: 23.04.2008
#
CREATE TABLE IF NOT EXISTS stat_online(
user			VARCHAR(50) NOT NULL,			# Users E-mail
cntr			INT					# Minutes logged in (relies on the fact that the page gets refreshed every minute)
);

#
# Name: rooms
# Desc: Contains rooms and relative information
# Last: 24.09.2007
#
CREATE TABLE IF NOT EXISTS rooms(
name			VARCHAR(20) NOT NULL PRIMARY KEY,	# Name of the room
capacity                INT,					# Capacity of the room (number of people)
location                VARCHAR(30),				# Location as section/zone/floor in the building
description		VARCHAR(90)				# Description of the room
);

#
# Name: kepler
# Desc: Contains timeslots as columns and days for rows for room Kepler
# Last: 24.09.2007
#
CREATE TABLE IF NOT EXISTS kepler(
day                     DATE NOT NULL PRIMARY KEY,              # Day when the meeting should be scheduled
slot0800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0930                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1000                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1030                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1100                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1130                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1200                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1230                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1300                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1330                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1400                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1430                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1500                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1530                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1600                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1630                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1700                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1730                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1930                VARCHAR(30) DEFAULT 'free'              # Contains the name of the person who booked this timeslot ('free' if not booked)
);

#
# Name: feynman
# Desc: Contains timeslots as columns and days for rows for room Feynman
# Last: 24.09.2007
#
CREATE TABLE IF NOT EXISTS feynman(
day                     DATE NOT NULL PRIMARY KEY,              # Day when the meeting should be scheduled
slot0800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0930                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1000                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1030                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1100                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1130                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1200                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1230                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1300                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1330                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1400                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1430                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1500                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1530                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1600                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1630                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1700                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1730                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1930                VARCHAR(30) DEFAULT 'free'              # Contains the name of the person who booked this timeslot ('free' if not booked)
);

#
# Name: einstein
# Desc: Contains timeslots as columns and days for rows for room Einstein
# Last: 24.09.2007
#
CREATE TABLE IF NOT EXISTS einstein(
day                     DATE NOT NULL PRIMARY KEY,              # Day when the meeting should be scheduled
slot0800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0930                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1000                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1030                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1100                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1130                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1200                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1230                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1300                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1330                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1400                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1430                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1500                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1530                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1600                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1630                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1700                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1730                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1930                VARCHAR(30) DEFAULT 'free'              # Contains the name of the person who booked this timeslot ('free' if not booked)
);

#
# Name: heisenberg
# Desc: Contains timeslots as columns and days for rows for room Heisenberg
# Last: 24.09.2007
#
CREATE TABLE IF NOT EXISTS heisenberg(
day                     DATE NOT NULL PRIMARY KEY,              # Day when the meeting should be scheduled
slot0800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot0930                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1000                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1030                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1100                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1130                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1200                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1230                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1300                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1330                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1400                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1430                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1500                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1530                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1600                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1630                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1700                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1730                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1800                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1830                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1900                VARCHAR(30) DEFAULT 'free',             # Contains the name of the person who booked this timeslot ('free' if not booked)
slot1930                VARCHAR(30) DEFAULT 'free'              # Contains the name of the person who booked this timeslot ('free' if not booked)
);

CREATE USER admin IDENTIFIED BY 'm1cr0mus3';
CREATE USER user IDENTIFIED BY 'n3tc00l';

GRANT ALL PRIVILEGES ON meetingrooms.* TO admin@localhost IDENTIFIED BY 'm1cr0mus3' WITH GRANT OPTION;
GRANT INSERT, SELECT, UPDATE ON meetingrooms.* TO user@localhost IDENTIFIED BY 'n3tc00l';
GRANT DELETE ON meetingrooms.users TO user@localhost IDENTIFIED BY 'n3tc00l';