-- make sure to vendor/bin/doctrine orm:schema-tool:create before!
-- USERS
-- admin : Admin123!
-- user : User123!
-- user2 : User123!
INSERT INTO user VALUES('1', 'admin', '$2y$10$NpW4tA8NVA68HDXO.p.lFutTbfD1G5Lw9GeR0S3sBuAY3TQ9t4FUO', 'Admin', 'Admin', 'it', '0911234567', 'email@domain.com', '1');
INSERT INTO user VALUES('2', 'user', '$2y$10$dg4hLL5gBt44OFHtNhnfM.4gpcNUkrZI3r3POfC1Un4w9mAbZaWG6', 'User', 'User', 'it', '0911111111', 'changed@blackpoints.ch', '1');
INSERT INTO user VALUES('3', 'user2', '$2y$10$dg4hLL5gBt44OFHtNhnfM.4gpcNUkrZI3r3POfC1Un4w9mAbZaWG6', 'User2', 'User2', 'it', '0919998877', 'email@domain2.net', '1');
-- PERMISSIONS
INSERT INTO permission VALUES('1', '1', '1', '1', '1');
INSERT INTO permission VALUES('2', '0', '0', '0', '0');
INSERT INTO permission VALUES('3', '0', '1', '0', '1');
-- FOLDERS
INSERT INTO folder (`folder_id`, `name`) VALUES ('1', 'folder');
INSERT INTO folder VALUES ('2', 'fld2', '1');
INSERT INTO folder VALUES ('3', 'fld', '1');
INSERT INTO folder VALUES ('4', 'fld4', '3');
INSERT INTO folder (`folder_id`, `name`) VALUES ('5', 'folder5');
INSERT INTO folder VALUES ('6', 'sub3fold', '3');
INSERT INTO folder VALUES ('7', 'test', '5');
INSERT INTO folder VALUES ('8', 'folder8', '7');
-- FOLDER_USER
INSERT INTO folder_user VALUES ('1', '1', '1', '1');
INSERT INTO folder_user VALUES ('2', '2', '1', '2');
INSERT INTO folder_user VALUES ('3', '2', '2', '1');
INSERT INTO folder_user VALUES ('4', '3', '2', '2');
INSERT INTO folder_user VALUES ('5', '5', '1', '2');
INSERT INTO folder_user VALUES ('6', '6', '2', '2');
INSERT INTO folder_user VALUES ('7', '3', '3', '1');
INSERT INTO folder_user VALUES ('8', '6', '3', '1');
INSERT INTO folder_user VALUES ('9', '5', '3', '1');
-- PASSWORDS
INSERT INTO password VALUES ('1', '1', 'pass1', 'icon', 'testpass', 'admin', 'csL5UFJpDdD2tMop3EKP4+q2rtfTK88McBhJjhuFVeMY0L1lcjgLH80RLEk=', 'http://www.talecraft.net', 'tag1 tag2', '2018-11-11 13:14:15',FALSE);
INSERT INTO password VALUES ('2', '3', 'pass2', 'ics', 'test2pass', 'user', 'csL5UFJpDdD2tMop3EKP4+q2rtfTK88McBhJjhuFVeMY0L1lcjgLH80RLEk=', 'http://www.bb.ch', 'tag', '2018-12-01 11:11:11',FALSE);
INSERT INTO password VALUES ('6', '2', 'pass6', 'icons', 'pass6description', 'user2', 'csL5UFJpDdD2tMop3EKP4+q2rtfTK88McBhJjhuFVeMY0L1lcjgLH80RLEk=', 'http://www.cc.ch', 'tog', '2018-12-02 08:30:30',FALSE);
INSERT INTO password VALUES ('4', '6', 'pass4', 'ics', 'pass4', 'admin', 'csL5UFJpDdD2tMop3EKP4+q2rtfTK88McBhJjhuFVeMY0L1lcjgLH80RLEk=', 'http://www.123.net', 'tug', '2018-12-03 08:09:10',FALSE);
-- LOGS
INSERT INTO log VALUES ('1', '4', '1', '2018-11-11 11:11:11', 'password viewed');
INSERT INTO log VALUES ('2', '1', '2', '2018-12-03', 'password modified');
