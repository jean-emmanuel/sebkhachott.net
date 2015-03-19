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

/**
 * Cette action permet de confirmer une inscription
 * @return void
 */
function action_confirmer_inscription() {
	$jeton = _request('jeton');
	$email = _request('email');

	include_spip('action/inscrire_auteur');
	if ($auteur = auteur_verifier_jeton($jeton)
	  AND $auteur['email']==$email
	  AND $auteur['statut']=='nouveau'){

		// OK c'est un nouvel inscrit qui confirme :
		// on le loge => ca va confirmer son statut et c'est plus sympa
		include_spip('inc/auth');
		auth_loger($auteur);

		// et on efface son jeton
		auteur_effacer_jeton($auteur['id_auteur']);

        $GLOBALS['redirect'] = generer_url_public('terminal','',false);
	}
	else {
		$GLOBALS['redirect'] = generer_url_public('terminal','',false);
	}

}

?>
