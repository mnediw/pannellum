CREATE TABLE tx_pannellum_scene (
  uid int(11) NOT NULL auto_increment,
  pid int(11) DEFAULT '0' NOT NULL,

  tstamp int(11) unsigned DEFAULT '0' NOT NULL,
  crdate int(11) unsigned DEFAULT '0' NOT NULL,
  cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
  sorting int(11) unsigned DEFAULT '0' NOT NULL,
  deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
  hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
  starttime int(11) unsigned DEFAULT '0' NOT NULL,
  endtime int(11) unsigned DEFAULT '0' NOT NULL,

  sys_language_uid int(11) DEFAULT '0' NOT NULL,
  l10n_parent int(11) DEFAULT '0' NOT NULL,
  l10n_diffsource mediumblob,

  identifier varchar(64) DEFAULT '' NOT NULL,
  title varchar(255) DEFAULT '' NOT NULL,
  type varchar(32) DEFAULT 'equirectangular' NOT NULL,
  panorama varchar(1024) DEFAULT '' NOT NULL,
  hotspot_debug tinyint(1) unsigned DEFAULT '0' NOT NULL,
  hotspots mediumtext NULL,

  PRIMARY KEY (uid),
  KEY parent (pid),
  KEY language (l10n_parent,sys_language_uid)
);
