CREATE TABLE $tableposts ( 
    ID int(10) unsigned NOT NULL auto_increment, 
    post_author int(4) DEFAULT '0' NOT NULL, 
    post_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, 
    post_content text NOT NULL, 
    post_title text NOT NULL, 
    post_category int(4) DEFAULT '0' NOT NULL, 
    post_karma int(11) DEFAULT '0' NOT NULL, 
    PRIMARY KEY (ID), 
    UNIQUE ID (ID) 
);
INSERT INTO $tableposts (post_author, post_date, post_content, post_title, post_category) VALUES ('1', '$now', 'This is the first post. Edit or delete it, then start blogging !', 'Hello world !', '1');
CREATE TABLE $tablecategories (
    cat_ID int(4) NOT NULL auto_increment, 
    cat_name TINYTEXT not null , 
    KEY (cat_ID)
);
INSERT INTO $tablecategories (cat_ID, cat_name) VALUES ('0', 'General');
UPDATE $tableposts SET post_category="1";
CREATE TABLE $tablecomments ( 
    comment_ID int(11) unsigned NOT NULL auto_increment, 
    comment_post_ID int(11) DEFAULT '0' NOT NULL, 
    comment_author tinytext NOT NULL, 
    comment_author_email varchar(100) NOT NULL, 
    comment_author_url varchar(100) NOT NULL, 
    comment_author_IP varchar(100) NOT NULL, 
    comment_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, 
    comment_content text NOT NULL, 
    comment_karma int(11) DEFAULT '0' NOT NULL, 
    PRIMARY KEY (comment_ID) 
);
INSERT INTO $tablecomments (comment_post_ID, comment_author, comment_author_email, comment_author_url, comment_author_IP, comment_date, comment_content) VALUES ('1', 'miss b2', 'missb2@example.com', 'http://example.com', '127.0.0.1', '$now', 'Hi, this is a comment.<br />To delete a comment, just log in, and view the posts\' comments, there you will have the option to edit or delete them.');
CREATE TABLE $tablesettings ( 
    ID tinyint(3) DEFAULT '1' NOT NULL, 
    posts_per_page int(4) unsigned DEFAULT '7' NOT NULL, 
    what_to_show varchar(5) DEFAULT 'days' NOT NULL, 
    archive_mode varchar(10) DEFAULT 'weekly' NOT NULL, 
    time_difference tinyint(4) DEFAULT '0' NOT NULL, 
    AutoBR tinyint(1) DEFAULT '1' NOT NULL, 
    time_format varchar(20) DEFAULT 'H:i:s' NOT NULL, 
    date_format varchar(20) DEFAULT 'Y/m/d' NOT NULL, 
    PRIMARY KEY (ID), KEY ID (ID) 
);
INSERT INTO $tablesettings ( ID, posts_per_page, what_to_show, archive_mode, time_difference, AutoBR, time_format, date_format) VALUES ( '1', '20', 'posts', 'monthly', '0', '1', 'H:i:s', 'd.m.y');
CREATE TABLE $tableusers ( 
    ID int(10) unsigned NOT NULL auto_increment, 
    user_login varchar(20) NOT NULL, 
    user_pass varchar(20) NOT NULL, 
    user_firstname varchar(50) NOT NULL, 
    user_lastname varchar(50) NOT NULL, 
    user_nickname varchar(50) NOT NULL, 
    user_icq int(10) unsigned DEFAULT '0' NOT NULL, 
    user_email varchar(100) NOT NULL, 
    user_url varchar(100) NOT NULL, 
    user_ip varchar(15) NOT NULL, 
    user_domain varchar(200) NOT NULL,
    user_browser varchar(200) NOT NULL, 
    dateYMDhour datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, 
    user_level int(2) unsigned DEFAULT '0' NOT NULL, 
    user_aim varchar(50) NOT NULL, 
    user_msn varchar(100) NOT NULL, 
    user_yim varchar(50) NOT NULL, 
    user_idmode varchar(20) NOT NULL, 
    PRIMARY KEY (ID), 
    UNIQUE ID (ID), 
    UNIQUE (user_login) 
);
INSERT INTO $tableusers (ID, user_login, user_pass, user_firstname, user_lastname, user_nickname, user_icq, user_email, user_url, user_ip, user_domain, user_browser, dateYMDhour, user_level, user_aim, user_msn, user_yim, user_idmode) VALUES ( '1', 'admin', '$random_password', '', '', 'admin', '0', '$admin_email', '', '127.0.0.1', '127.0.0.1', '', '00-00-0000 00:00:01', '10', '', '', '', 'nickname');
