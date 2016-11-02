
alter table xoops_newdb_component_master add (
	nonull int(2) NOT NULL default '0',
	textmax int(2) NOT NULL default '0',
	onoff_refine int(2) NOT NULL default '0'
);

