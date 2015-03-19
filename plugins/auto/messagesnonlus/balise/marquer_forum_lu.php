<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_MARQUER_FORUM_LU ($p, $nom = 'MARQUER_FORUM_LU') {
  $args = array('id_forum','id_auteur','forums_lus');
  return  calculer_balise_dynamique($p ,$nom ,$args);
}

function balise_MARQUER_FORUM_LU_stat($args, $contexte) {

	$id_forum = $args[0];

	if ($args[1]) {
	
	    $id_auteur = $args[1];
	    $forums_lus = $args[2];
	
	    if ($id_auteur) {
	

	        if (strlen($forums_lus)) {
		        $forums_lus = explode(",",$forums_lus);
	        } else { 
		        $forums_lus = array();
		
	        }

	        if (in_array($id_forum,$forums_lus)) {
		        return;
            } else {
		        include_spip('abstract_sql');
		        $forums_lus = array_merge($forums_lus,array($id_forum));
		        $forums_lus = implode(",",$forums_lus);

		        sql_updateq('spip_auteurs',array('forums_lus'=>$forums_lus), 'id_auteur=' . $id_auteur);
		        return;
	        }
	
	    } else {
	        return false;
	    }
            
    } else {
        return false;
    }

} 

?>
