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

function test_inscription($statut, $mail, $nom, $options) {
	include_spip('inc/filtres');
	if (!$r = email_valide($mail)) return 'info_email_invalide';
	$nom = trim(corriger_caracteres($nom));
	$res = array('email' => $r, 'nom' => $nom, 'prefs' => $statut);
	if (isset($options['login'])) {
		$login = trim(corriger_caracteres($options['login']));
		if((strlen ($login) >= _LOGIN_TROP_COURT) AND (strlen($nom) <= 64))
			$res['login'] = $login;
	}
	if(!isset($res['login']) AND ((strlen ($nom) < _LOGIN_TROP_COURT) OR (strlen($nom) > 64)))
		return 'forum_login_trop_court';
	return $res;
}

function formulaires_inscription_charger_dist($mode='', $id=0) {
	global $visiteur_session;
	
	// fournir le mode de la config ou tester si l'argument du formulaire est un mode accepte par celle-ci
	// pas de formulaire si le mode est interdit
	include_spip('inc/autoriser');
	if (!autoriser('inscrireauteur', $mode, $id))
		return false;

	// pas de formulaire si on a déjà une session avec un statut égal ou meilleur au mode
	if(isset($visiteur_session['statut']) && ($visiteur_session['statut'] <= $mode))
		return false;
	
	$valeurs = array('nom_inscription'=>'','mail_inscription'=>'', 'id'=>$id, '_mode'=>$mode);
	
	return $valeurs;
}

// Si inscriptions pas autorisees, retourner une chaine d'avertissement
function formulaires_inscription_verifier_dist($mode='', $id=0) {
	
	include_spip('inc/filtres');
	$erreurs = array();

	include_spip('inc/autoriser');
	if (!autoriser('inscrireauteur', $mode, $id)
	  OR (strlen(_request('nobot'))>0))
		$erreurs['message_erreur'] = _T('pass_rien_a_faire_ici');

	if (!$nom = _request('nom_inscription'))
		$erreurs['nom_inscription'] = _T("info_obligatoire");
	elseif (!nom_acceptable(_request('nom_inscription')))
		$erreurs['nom_inscription'] = _T("forum_nom_pas_conforme");
	if (!$mail = strval(_request('mail_inscription')))
		$erreurs['mail_inscription'] = _T("info_obligatoire");
	
	// compatibilite avec anciennes fonction surchargeables
	// plus de definition par defaut
	if (!count($erreurs)){
		include_spip('action/inscrire_auteur');
		if (function_exists('test_inscription'))
			$f = 'test_inscription';
		else 
			$f = 'test_inscription_dist';
		$declaration = $f($mode, $mail, $nom, $id);
		if (is_string($declaration)) {
			$k = (strpos($declaration, 'mail')  !== false) ?
			  'mail_inscription' : 'nom_inscription';
			$erreurs[$k] = _T($declaration);
		} else {
			include_spip('base/abstract_sql');
			
			if ($row = sql_fetsel("statut, id_auteur, login, email", "spip_auteurs", "email=" . sql_quote($declaration['email']))){
				if (($row['statut'] == '5poubelle') AND !$declaration['pass'])
					// irrecuperable
					$erreurs['message_erreur'] = _T('form_forum_access_refuse');	
				else if (($row['statut'] != 'nouveau') AND !$declaration['pass'])
					// deja inscrit
					$erreurs['message_erreur'] = _T('form_forum_email_deja_enregistre');
				spip_log($row['id_auteur'] . " veut se resinscrire");
			}
		}
	}
	return $erreurs;
}

function formulaires_inscription_traiter_dist($mode='', $id=0) {
	
	include_spip('inc/filtres');
	include_spip('inc/autoriser');
	if (!autoriser('inscrireauteur', $mode, $id))
		$desc = "rien a faire ici";
	else {
		$nom = _request('nom_inscription');
		$mail_complet = _request('mail_inscription');

		$inscrire_auteur = charger_fonction('inscrire_auteur','action');
		$desc = $inscrire_auteur($mode, $mail_complet, $nom, array('id'=>$id));
	}

	// erreur ?
	if (is_string($desc)){
		return array('message_erreur'=> $desc);
	}
	// OK
	else
		return array('message_ok' => _T('forum_identifiant_mail'), 'id_auteur' => $desc['id_auteur']);
}

?>
