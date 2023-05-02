DROP TABLE Create_Quote;
DROP TABLE Quote_Note;
DROP TABLE Quote_Item;
DROP TABLE User;
DROP TABLE Quote;
DROP TABLE Note;
DROP TABLE Item;


CREATE TABLE User(
	user_id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
	is_associate BOOLEAN DEFAULT(1),
	is_hq BOOLEAN DEFAULT(0),
	is_admin BOOLEAN DEFAULT(0),
	name CHAR(20) NOT NULL,
	password CHAR(16) NOT NULL,
	commission FLOAT NULL,
    address CHAR(50) NULL
);

CREATE TABLE Quote(
	quote_id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
	customer INTEGER NOT NULL,
	price DECIMAL(14,2) NOT NULL DEFAULT(0),
	customerEmail CHAR(50) NOT NULL DEFAULT 'example@gmail.com',
	status CHAR(15) NOT NULL DEFAULT 'Unfinalized'
);

CREATE TABLE Note(
	note_id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
	text_field CHAR(250)
);

CREATE TABLE Item(
	item_id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
	price DECIMAL(7,2) NOT NULL,
	item_name CHAR(20) NOT NULL
);

CREATE TABLE Create_Quote(
	associate_id INTEGER NOT NULL,
	foreign_quote_id INTEGER NOT NULL,
	date DATE NOT NULL,
	FOREIGN KEY(associate_id) REFERENCES User(user_id),
	FOREIGN KEY(foreign_quote_id) REFERENCES Quote(quote_id)
);

CREATE TABLE Quote_Note(
	foreign_quote_id INTEGER NOT NULL,
	foreign_note_id INTEGER NOT NULL,
	FOREIGN KEY(foreign_quote_id) REFERENCES Quote(quote_id),
	FOREIGN KEY(foreign_note_id) REFERENCES Note(note_id)
);

CREATE TABLE Quote_Item(
	foreign_quote_id INTEGER NOT NULL,
	foreign_item_id INTEGER NOT NULL,

	FOREIGN KEY(foreign_quote_id) REFERENCES Quote(quote_id),
	FOREIGN KEY(foreign_item_id) REFERENCES Item(item_id)
);


INSERT INTO User (name, password, commission, address) VALUES ('brad', '1234', 1000.00, '900 Crane Dr.');
INSERT INTO User (name, password, commission, address) VALUES ('another associate', '1234', 999.00, '123 Some St.');
INSERT INTO User (is_associate, is_hq, name, password, commission, address) VALUES (0, 1, 'bradhq', '1234', 0, '475 Laurel St.');
INSERT INTO User (is_associate, is_admin, name, password, commission, address) VALUES (0, 1, 'bradadmin', '1234', 0, '475 Admin St.');

INSERT INTO Quote (customer, price, customerEmail, status) VALUES ('18', 315, 'rottenbutwhole@gmail.com', 'Unfinalized');
INSERT INTO Create_Quote (associate_id, foreign_quote_id, date) VALUES (1, 1, '2023-04-22');
INSERT INTO Item (price, item_name) VALUES (315, 'desk');
INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (1, 1);

INSERT INTO Quote (customer, price, customerEmail, status) VALUES ('94', 600, 'buschlight@gmail.com', 'Finalized');
INSERT INTO Create_Quote (associate_id, foreign_quote_id, date) VALUES (1, 2, '2023-04-23');
INSERT INTO Item (price, item_name) VALUES (400, 'Table');
INSERT INTO Item (price, item_name) VALUES (200, 'Coffee');
INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (2, 2);
INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (2, 3);
INSERT INTO Note(text_field) VALUES ('This is a note');
INSERT INTO Quote_Note(foreign_note_id, foreign_quote_id) VALUES (1, 2);


INSERT INTO Quote (customer, price, customerEmail, status) VALUES ('113', 600, 'anotheremail@gmail.com', 'Sanctioned');
INSERT INTO Create_Quote (associate_id, foreign_quote_id, date) VALUES (2, 3, '2022-08-15');
INSERT INTO Item (price, item_name) VALUES (400, 'SomeItem');
INSERT INTO Item (price, item_name) VALUES (200, 'SomeOtherItem');
INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (3, 4);
INSERT INTO Quote_Item(foreign_quote_id, foreign_item_id) VALUES (3, 5);
INSERT INTO Note(text_field) VALUES ('This is another note');
INSERT INTO Quote_Note(foreign_note_id, foreign_quote_id) VALUES (2, 3);