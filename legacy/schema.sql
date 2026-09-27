-- Source-only schema summary from the original DBMS project.
-- Sample INSERT statements and plaintext credentials are intentionally omitted.

CREATE TABLE admin (
    a_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    psw VARCHAR(30) NOT NULL
);

CREATE TABLE booking_det (
    bus_id INT NOT NULL,
    vacant INT NOT NULL,
    jdate VARCHAR(30) NOT NULL,
    bfrom VARCHAR(30) NOT NULL,
    bto VARCHAR(30) NOT NULL
);

CREATE TABLE bus_details (
    bus_id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    bname VARCHAR(30) NOT NULL,
    bno VARCHAR(20) NOT NULL,
    bfrom VARCHAR(30) NOT NULL,
    bto VARCHAR(30) NOT NULL,
    time VARCHAR(10) NOT NULL,
    type VARCHAR(10) NOT NULL,
    no_seat INT NOT NULL,
    fare INT NOT NULL
);

CREATE TABLE ticket (
    tid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    bus_id INT NOT NULL,
    uid INT NOT NULL,
    seat_no VARCHAR(30) NOT NULL,
    no_seat INT NOT NULL,
    ticket_status VARCHAR(30) NOT NULL,
    jdate VARCHAR(30) NOT NULL,
    booking_date DATE NOT NULL,
    pname VARCHAR(30) NOT NULL
);

CREATE TABLE user_info (
    uid INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    uname VARCHAR(30) NOT NULL,
    age VARCHAR(30) NOT NULL,
    nid_no VARCHAR(30) NOT NULL,
    psw VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL
);
