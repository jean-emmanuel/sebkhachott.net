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

// chargement des valeurs par defaut des champs du formulaire
function formulaires_oubli_charger(){
	$valeurs = array('mail_oubli'=>'','nobot'=>'');
	return $valeurs;
}

// http://doc.spip.org/@message_oubli
function message_oubli2($email, $param)
{
	$r = formulaires_oubli_mail2($email);
	if (is_array($r) AND $r[1]) {
		include_spip('inc/texte'); # pour corriger_typo

		include_spip('action/inscrire_auteur');
		$cookie = auteur_attribuer_jeton($r[1]['id_auteur']);

		$msg = recuperer_fond(
			"modeles/mail_oubli",
			array(
				'url_reset'=>parametre_url(generer_url_public('terminal',"$param=$cookie", true, false),'newpass','1')
			)
		);
		include_spip("inc/notifications");
		notifications_envoyer_mails($email, $msg);
	  return _T('forum_nouveau_pass_mail');
	}
	return  _T('forum_nouveau_pass_erreur_tech');
}

// la saisie a ete validee, on peut agir
function formulaires_oubli_traiter(){
	$message = message_oubli2(_request('mail_oubli'),'p');
	return array('message_ok'=>$message);
}


function formulaires_oubli_verifier(){
	$erreurs = array();

	$email = strval(_request('mail_oubli'));

	$r = formulaires_oubli_mail2($email);

	if (!is_array($r))
		$erreurs['mail_oubli'] = $r;
	else {
		if (!$r[1])
			$erreurs['mail_oubli'] = _T('pass_erreur_non_enregistre', array('email_oubli' => spip_htmlspecialchars($email)));

		elseif ($r[1]['statut'] == '5poubelle' OR $r[1]['pass'] == '')
			$erreurs['mail_oubli'] =  _T('pass_erreur_acces_refuse');
	}

	if (_request('nobot'))
		$erreurs['message_erreur'] = _T('pass_rien_a_faire_ici');

	return $erreurs;
}
function formulaires_oubli_mail2($email)
{
	if (function_exists('test_oubli'))
		$f = 'test_oubli';
	else
		$f = 'test_oubli_dist';
	$declaration = $f($email);

	if (!is_array($declaration))
		return $declaration;
	else {
		include_spip('base/abstract_sql');
		return array($declaration, sql_fetsel("id_auteur,statut,pass", "spip_auteurs", "email =" . sql_quote($declaration['mail_oubli'])));
	}
}

// fonction qu'on peut redefinir pour filtrer les adresses mail
// http://doc.spip.org/@test_oubli
function test_oubli($email)
{
	include_spip('inc/filtres'); # pour email_valide()
	if (!email_valide($email) )
		return _T('forum_email_non_valide', array('email_oubli' => spip_htmlspecialchars($email)));
	return array('mail_oubli' => $email);
}

?>
