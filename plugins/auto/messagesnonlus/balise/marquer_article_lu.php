<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_MARQUER_ARTICLE_LU ($p, $nom = 'MARQUER_ARTICLE_LU') {
  $args = array('id_article','id_auteur','articles_lus');
  return  calculer_balise_dynamique($p ,$nom ,$args);
}

function balise_MARQUER_ARTICLE_LU_stat($args, $contexte) {
    
	$id_article = $args[0];
	
	if ($args[1]) {
	
	    $id_auteur = $args[1];
	    $articles_lus = $args[2];
	
	    if ($id_auteur) {
	

	        if (strlen($articles_lus)) {
		        $articles_lus = explode(",",$articles_lus);
	        } else { 
		        $articles_lus = array();
		
	        }

	        if (in_array($id_article,$articles_lus)) {
		        return;
            } else {
		        include_spip('abstract_sql');
		        $articles_lus = array_merge($articles_lus,array($id_article));
		        $articles_lus = implode(",",$articles_lus);

		        sql_updateq('spip_auteurs',array('articles_lus'=>$articles_lus), 'id_auteur=' . $id_auteur);
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
