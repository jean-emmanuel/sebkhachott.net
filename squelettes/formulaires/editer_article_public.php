<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_article_public_charger_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='public_articles_edit_config', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('article',$id_article,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
	// il faut enlever l'id_rubrique car la saisie se fait sur id_parent
	// et id_rubrique peut etre passe dans l'url comme rubrique parent initiale
	// et sera perdue si elle est supposee saisie
	return $valeurs;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui
 * ne representent pas l'objet edite
 */
function formulaires_editer_article_public_identifier_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='public_articles_edit_config', $row=array(), $hidden=''){
	return serialize(array(intval($id_article),$lier_trad));
}

// Choix par defaut des options de presentation
// http://doc.spip.org/@public_articles_edit_config
function public_articles_edit_config($row)
{
	global $spip_lang;

	$config = $GLOBALS['meta'];
	$config['lignes'] = 2;
	$config['langue'] = $spip_lang;

	$config['restreint'] = ($row['statut'] == 'publie');
	return $config;
}

function formulaires_editer_article_public_verifier_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='public_articles_edit_config', $row=array(), $hidden=''){
	// auto-renseigner le titre si il n'existe pas
	titre_automatique('titre',array('descriptif','chapo','texte'));
	
	// on ne demande pas le titre obligatoire : il sera rempli a la volee dans editer_article si vide
	$erreurs = formulaires_editer_objet_verifier('article',$id_article,array('id_parent'));
	/*$min_length = (defined('_FORUM_LONGUEUR_MINI') ? _FORUM_LONGUEUR_MINI : 10);
	if (strlen($texte = _request('texte'))<$min_length
	){
		$erreurs['texte'] = _T($min_length==10 ? 'forum:forum_attention_dix_caracteres' : 'forum:forum_attention_nb_caracteres_mini', array('min' => $min_length));
	}*/
	if (!function_exists('autoriser'))
		include_spip('inc/autoriser');	 // si on utilise le formulaire dans le public
		
	if (!isset($erreurs['id_parent'])
	  AND !autoriser('creerarticledans','rubrique',_request('id_parent'))){
		$erreurs['id_parent'] = _T('info_creerdansrubrique_non_autorise');
	}
	return $erreurs;
}

// http://doc.spip.org/@inc_editer_article_dist
function formulaires_editer_article_public_traiter_dist($id_article='new', $id_rubrique=0, $retour='', $lier_trad=0, $config_fonc='public_articles_edit_config', $row=array(), $hidden=''){
	// ici on ignore changer_lang qui est poste en cas de trad,
	// car l'heuristique du choix de la langue est pris en charge par article_inserer
	// en fonction de la config du site et de la rubrique choisie
	set_request("changer_lang");
	$ret = formulaires_editer_objet_traiter('article',$id_article,$id_rubrique,$lier_trad,$retour,$config_fonc,$row,$hidden);
    //return array($ret['id_article'],$ret);
    $res['redirect'] = parametre_url(generer_url_public('article','',false),'id_article',$ret['id_article']);
    return $res;    
    
}

?>
