#
# Table structure for table 'tx_twitterfrontend_cache'
#
CREATE TABLE tx_twitterfrontend_cache (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	twitterid varchar(30) DEFAULT '' NOT NULL,
	showview varchar(10) DEFAULT '' NOT NULL,
	lastupdate int(11) DEFAULT '0' NOT NULL,
	content text NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);