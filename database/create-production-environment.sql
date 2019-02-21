-- creating admin
INSERT INTO user VALUES('1', 'admin', '$2y$10$NpW4tA8NVA68HDXO.p.lFutTbfD1G5Lw9GeR0S3sBuAY3TQ9t4FUO', 'Admin', 'Admin', 'it', null, 'admin@password_cockpit.ch', '1' ,'0');
-- adding all the permission to admin
INSERT INTO permission VALUES('1', '1', '1', '1', '1');