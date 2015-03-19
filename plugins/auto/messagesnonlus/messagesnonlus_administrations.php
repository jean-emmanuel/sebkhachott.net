<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

  function messagesnonlus_upgrade($nom_meta_base_version, $version_cible){
   
  $maj = array();
  $maj['create'] = array(
		array('sql_alter',"TABLE spip_auteurs ADD articles_lus text DEFAULT '' NOT NULL"),
		array('sql_alter',"TABLE spip_auteurs ADD forums_lus text DEFAULT '' NOT NULL"),
  );
   
  include_spip('base/upgrade');
  maj_plugin($nom_meta_base_version, $version_cible, $maj);
  }
   
  function messagesnonlus_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_auteurs DROP COLUMN articles_lus");
	sql_alter("TABLE spip_auteurs DROP COLUMN articles_lus");
  effacer_meta($nom_meta_base_version);
  }
?>
