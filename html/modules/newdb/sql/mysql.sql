# 2005 Takuto Nishioka

#
# Data (label) management
#

CREATE TABLE newdb_master(

	label_id int(10) NOT NULL auto_increment,

	label char(255) NOT NULL,
	reg_date int(10) unsigned NOT NULL default '0',
	users text NOT NULL,
	author char(30) NOT NULL,
	keyword text,
	views int(10) NOT NULL default '0',
	
	primary key(label_id)
);

#
# Component
#
# type : 1 system, 2 radio, 3 checkbox, 4 text

# old 
#CREATE TABLE newdb_component_master(
#
#	comp_id int(10) NOT NULL auto_increment, 
#
#	tag char(30) NOT NULL,
#	name char(30) NOT NULL,
#	exp char(120),
#	type int(2) NOT NULL,
#	default_value char(200),
#	select_value text,
#	onoff int(2) NOT NULL default '0',
#	sort int(10) NOT NULL default '0',
#	
#	primary key(comp_id)
#);

CREATE TABLE newdb_component_master(

	comp_id int(10) NOT NULL auto_increment, 

	tag char(100) NOT NULL,
	name char(100) NOT NULL,
	exp char(120),
	type int(2) NOT NULL,
	default_value char(200),
	select_value text,
	onoff int(2) NOT NULL default '0',
	sort int(10) NOT NULL default '0',
	nonull int(2) NOT NULL default '0',
	textmax int(2) NOT NULL default '0',
	onoff_refine int(2) NOT NULL default '0',
	
	primary key(comp_id)
);

CREATE TABLE newdb_component(

	comp_id int(10) NOT NULL, 
	label_id int(10) NOT NULL,

	value text
);

#
# Data (file) management
#

CREATE TABLE newdb_item(

	item_id int(10) NOT NULL auto_increment,
	label_id int(10) NOT NULL,
	type char(10) NOT NULL,
	
	name char(100) NOT NULL,
	path text,
	reg_date int(10) unsigned NOT NULL default '0',
	reg_user char(30) NOT NULL,

	primary key(item_id)
);

#
# Comment things
#

CREATE TABLE newdb_comment_topic(

	topic_id int(10) NOT NULL auto_increment,
	label_id int(10) NOT NULL,
	com_id int(10) NOT NULL,
	
	type char(10) NOT NULL,
	
	primary key(topic_id)
);

CREATE TABLE newdb_comment(
	
	com_id int(10) NOT NULL auto_increment,
	pcom_id int(10),
	
	subject char(100) NOT NULL default 'no subject',
	message text NOT NULL,
	reg_date int(10) unsigned NOT NULL default '0',
	reg_user int(10) NOT NULL,
	
	primary key(com_id)
);

#
# Keyword
#

CREATE TABLE newdb_keyword(

	kw_id int(10) NOT NULL auto_increment, 

	keyword char(128) NOT NULL,
	path text,
	sort int(10) NOT NULL default '0',

	primary key (kw_id)
);

#
# List
#
# type : 1 list, 2 thumbnail

CREATE TABLE newdb_list(
	
	list_id int(10) NOT NULL auto_increment,
	
	name char(30) NOT NULL,
	type int(2) NOT NULL default '1',	
	list_th text,
	thumb_dir char(30),
	thumb_size text,
	template text,
	onoff int(2) NOT NULL default '0',
	sort int(2) NOT NULL default '0',
	
	primary key(list_id)
);


#
# list refine
#

CREATE TABLE newdb_list_refine(
	
	ref_id int(20) NOT NULL auto_increment,
	user char(32) NOT NULL,
	labels text,
	primary key(ref_id)
);

CREATE TABLE newdb_list_refine_option(

	opt_id int(20) NOT NULL auto_increment,
	user char(32) NOT NULL,
	keywords text,
	primary key(opt_id)
);

CREATE TABLE newdb_list_textsearch(
	
	ref_id int(20) NOT NULL auto_increment,
	user char(32) NOT NULL,
	text char(255),
	labels text,
	primary key(ref_id)
);

#
# full text / file search
#

CREATE TABLE newdb_fulltext_search(

	user char(32) NOT NULL,
	label_id int(10) NOT NULL,
	pcom_id int(10) NOT NULL,
	
	subject char(100) NOT NULL,
	message text NOT NULL,
	info char(128) NOT NULL,
	type char(6) NOT NULL
);

CREATE TABLE newdb_file_search(

	user char(32) NOT NULL,
	label_id int(10) NOT NULL,
	
	name char(30) NOT NULL,
	path text,
	info char(128) NOT NULL
);

#
# Detail
#

CREATE TABLE newdb_detail(

	detail_id int(10) NOT NULL auto_increment,
	template text,
	primary key(detail_id)
);

#
# Bookmark
#

CREATE TABLE newdb_bookmark_dir(

	bd_id int(10) NOT NULL auto_increment,
	pbd_id int(10),

	directory char(30) NOT NULL,
	uid int(2) NOT NULL,
	sort int(2) NOT NULL default '0',

	primary key (bd_id)
);

CREATE TABLE newdb_bookmark_file(

	bf_id int(10) NOT NULL auto_increment,
	bd_id int(10),

	label_id int(10) NOT NULL,
	note char(255),
	uid int(2) NOT NULL,

	primary key (bf_id)
);


#
# Link
#
# type: 1 inside, 2 outside

CREATE TABLE newdb_link(

	link_id int(10) NOT NULL auto_increment,
	label_id int(10) NOT NULL,
	
	type int(2) NOT NULL default '1',
	uid int(2) NOT NULL,
	name char(64),
	href char(255),
	note char(255),
	
	primary key (link_id)
);
