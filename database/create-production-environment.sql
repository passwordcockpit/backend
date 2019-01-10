-- creating admin
INSERT INTO user VALUES('1', 'admin', '$2y$10$y86XYwrQAjBuD6NeSH3fw.4pVFxpDmSMmOCUDgXaIAYpQOCpqv82y', 'Admin', 'Admin', 'it', null, 'admin@password_cockpit.ch', '1');
-- adding all the permission to admin
INSERT INTO permission VALUES('1', '1', '1', '1', '1');