<?php
    if (!defined('_ECRIRE_INC_VERSION')) return;
	//define('_NO_CACHE', -1); 
	
    // ajouter le critère {tout} aux boucles rubriques si pas de présence d'un critère {statut}
    $GLOBALS['spip_pipeline']['pre_boucle'] .= "|tout_partout";
    function tout_partout($boucle){
            if ($boucle->type_requete == 'rubriques' AND !isset($boucle->modificateur['criteres']['statut']))
                    $boucle->modificateur['criteres']['statut'] = true;
            return $boucle;
    }
    
    // ajouter le critère {!lang_select} à toutes les boucles
    $GLOBALS['spip_pipeline']['pre_boucle'] .= "|multilang_partout";
    function multilang_partout($boucle){
            $boucle->lang_select = 'non';
            return $boucle;
    }
        
    // Typo pratique
    $GLOBALS['spip_pipeline']['pre_typo'] .= "|customs_pre_typo";
    function customs_pre_typo($flux) {
        $flux = str_replace('////','<div class="clear"></div>',$flux);
        $flux = preg_replace('/-:-(.*)-:-/','<p class="align-center">$1</p>',$flux);
        $flux = preg_replace('/--:(.*)--:/','<p class="align-right">$1</p>',$flux);
        $flux = preg_replace('/<ref>(.*)<\/ref>/','<p class="align-right reference"><em>-- $1</em></p>',$flux);
        $flux = preg_replace('/====(.*)====/','<p class="strike-through">$1</p>',$flux);
        $flux = preg_replace('/==(.*)====/','<p class="strike-through align-left">$1</p>',$flux);
        $flux = preg_replace('/====(.*)==/','<p class="strike-through align-right">$1</p>',$flux);
        return $flux;
        
    }
    // Typo pratique
    $GLOBALS['spip_pipeline']['post_typo'] .= "|customs_post_typo";
    function customs_post_typo($flux) {

        $flux = str_replace('\[','[',$flux);
        $flux = str_replace('\]',']',$flux);
        
        return $flux;
        
    }

    $GLOBALS['spip_pipeline']['autoriser'] .= "|autoriser_rubrique_creerarticledans";
    function autoriser_rubrique_creerarticledans($faire, $type, $id, $qui, $opt) {
      	return
      	    in_array($qui['statut'], array('6forum'))
		    AND in_array($id, array(5,9))
		    OR in_array($qui['statut'], array('0minirezo','1comite'))
		    AND autoriser('voir','rubrique',$id)
		    AND autoriser('creer', 'article');
    }  
    
    // Definir la langue selon le navigateur sauf si le parametre de langue est précisé dans l'url 
    if (!$_GET['lang']) {
        $lang = explode(',',explode(';',$_SERVER['HTTP_ACCEPT_LANGUAGE'])[0])[0];
        $lang = preg_replace('/\-[a-z]+/','',$lang);
        if (strtolower($lang) == 'fr') {
            set_request('lang', 'fr');
        } else {
            set_request('lang', 'en');
        }
    }
     
    $flag_preserver = true;
    define('_NOTES_OUVRE_REF',' <sup>');
    define('_NOTES_FERME_REF','</sup> ');
    define('_NOTES_OUVRE_NOTE','<span>');
    define('_NOTES_FERME_NOTE','</span> ');
?>
