<?php

    // SCSS to CSS compiler 
    require "php/scss.inc.php";
    function scsscss($stylesheet) {
        $scss = new scssc;
        try {
            $stylesheet = $scss->compile($stylesheet);
        } catch (Exception $ex) {
            return "scssphp fatal error: \n".$ex->getMessage();
        }
        $stylesheet = preg_replace('/\n+/','',$stylesheet);
        $stylesheet = preg_replace('/\s?+{\s+/','{',$stylesheet);
        $stylesheet = preg_replace('/\s?+;}/','}',$stylesheet);
        $stylesheet = preg_replace('/:\s+/',':',$stylesheet);
        $stylesheet = preg_replace('/;\s+/',';',$stylesheet);
        $stylesheet = preg_replace('/\s+/',' ',$stylesheet);
        return $stylesheet;
    }

    include('php/simple_html_dom.php');
    function gethelloassodata($collecte) {
        $url = 'http://www.helloasso.com/associations/ammd/collectes/' . $collecte;
        $html = file_get_html($url);
        $data['montant'] = preg_replace('/[^0-9]+/','',$html->find('div#collecteMoney', 0)->plaintext);
        //$data['objectif'] = preg_replace('/[^0-9]+/','',$html->find('div#objectifMoney', 0)->find('div.amount', 0)->plaintext);
        return $data;
    }
    
    
    ;
    $GLOBALS['puce'] = '<i class="fa fa-angle-right"></i>&nbsp;';
    $GLOBALS['class_spip_plus'] = '';
    $GLOBALS['ligne_horizontale'] = '<hr/>';
    $GLOBALS['debut_intertitre'] = '<h3>';
    $GLOBALS['fin_intertitre'] = '</h3>';
    $GLOBALS['debut_italique'] = '<em>';
    $GLOBALS['fin_italique'] = '</em>';
    		
    function smallcaps($str){
        $str = preg_replace('([A-Z]{1})','<span class="smallcap">$0</span>',$str);
        return $str;
    }
    function tinylinks($str){
        $str = preg_replace('(http[s]?:\/\/[^\s]*)','<a href="$0"><i class="fa fa-link"></i></a>',$str);
        return $str;
    }
    
    
    function cleanLines($html) {
        $html = preg_replace('/\n[\s]*\n/',"\n\n",$html);
        return $html;
    
    }

    // Necessite d'ajouter le suffixe _dist à la fonction originale dans plugins/auto/saisies/.../saisies_pipeline.php
    //function saisies_affichage_final($flux){
	//    return $flux;
    //}


?>
