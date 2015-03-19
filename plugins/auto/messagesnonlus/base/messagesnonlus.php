<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function messagesnonlus_declarer_tables_objets_sql($tables) {

	$tables['spip_auteurs']['field']['articles_lus'] = "text NOT NULL DEFAULT ''";
	$tables['spip_auteurs']['field']['forums_lus'] = "text NOT NULL DEFAULT ''";

	return $tables;
}



?>
