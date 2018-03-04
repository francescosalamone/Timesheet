# Timesheet
Simple timesheet in PHP + mysql

# Database
For use this web app, you need first of all to create a mysql DB, with two tables:

## project:
id = int, primary, auto_increment, not Null

namename = varchar(45), not Null

## times:
id = int, primary, auto_increment, not Null

project = int, not Null

time_start = Datetime, not Null

time_end = Datetime, can be Null

# Configuration
In the file backend/conf/conf.php you need to change the database name, the database host, the username and password with your data.
