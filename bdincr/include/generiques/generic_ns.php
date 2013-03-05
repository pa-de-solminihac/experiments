<?php
/**
 * Fichier : generic_ns.php
 * Auteur : PA
 * Fonctions souvent simples mais bien pratiques 
 * A dispatcher en plusieurs fichiers 
 */


/****
  DEBUG
 ****/

/**
 * affiche le contenu de tableaux EGPCS PHP 
 *
 * 
 */
function print_debug ($color = 'gray') 
{
    echo '<div style="z-index: 9; ">';
    for ($i = 0; $i < 50; ++$i) {
        echo '<br />';
    }
    echo '<div style="padding: 2px; border: solid silver 1px; z-index: 9; ">';
    $repl_POST = str_replace(array('<','>'), array('&lt;','&gt;'), $_POST);
    $repl_FILES = str_replace(array('<','>'), array('&lt;','&gt;'), $_FILES);
    print_pre($repl_POST,           $color, 'POST');
    print_pre($repl_FILES,          $color, 'FILES');
    echo '</div>';
    echo '<div style="padding: 2px; border: solid silver 1px; z-index: 9; ">';
    $repl_SESSION = str_replace(array('<','>'), array('&lt;','&gt;'), $_SESSION);
    print_pre($repl_SESSION,        $color, 'SESSION');
    echo '</div>';
    echo '<div style="padding: 2px; border: solid silver 1px; z-index: 9; ">';
    $repl_SERVER = str_replace(array('<','>'), array('&lt;','&gt;'), $_SERVER);
    print_pre($repl_SERVER,         $color, 'repl_SERVER');
    echo '</div>';
    /*
       echo '<div style="padding: 2px; border: solid silver 1px; ">';
       phpinfo();
       echo '</div>';
     */
    echo '</div>';
}

/**
 * affiche le contenu de tableaux EGPCS PHP dans le TABLEAU_ERROR 
 *
 * 
 */
function print_debug_js () 
{
    $repl_POST = str_replace(array('<','>'), array('&lt;','&gt;'), $_POST);
    $repl_SESSION = str_replace(array('<','>'), array('&lt;','&gt;'), $_SESSION);
    trigger_error(serialize(array('<pre class="invisible">POST :    ' . print_r_ret($repl_POST)    . '</pre>', 'debug_post')),    E_USER_NOTICE);
    trigger_error(serialize(array('<pre class="invisible">SESSION : ' . print_r_ret($repl_SESSION) . '</pre>', 'debug_session')), E_USER_NOTICE);
}

/**
 * execute le code PHP contenu dans $str, et recupere la sortie de eval($str) dans une variable 
 *
 * 
 */
function eval_ret ($str = null) 
{
    ob_start();
    eval($str);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/**
 * recupere la sortie de var_dump($mixed) dans une variable 
 *
 * 
 */
function var_dump_ret ($mixed = null) 
{
    ob_start();
    var_dump($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/**
 * recupere la sortie de print_r($mixed) dans une variable 
 *
 * 
 */
function print_r_ret ($mixed = null) 
{
    ob_start();
    print_r($mixed);
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
}

/**
 * var_dump($mixed) entre balises <pre></pre> 
 *
 * 
 */
function var_dump_pre ($mixed = null, $color = 'black') 
{
    echo '<pre style="font-size:11px; color:' . $color . '">';
    var_dump($mixed);
    echo '</pre>';
    return null;
}

/**
 * print_r($mixed) entre balises <pre></pre> 
 *
 * 
 */
function print_pre ($mixed = null, $color = 'black', $nom = null) 
{
    echo '<pre style="text-align: left; font-size:11px; color:' . $color . '">';
    if (strlen($nom)) {
        echo $nom . " : ";
    }
    print_r($mixed);
    echo '</pre>';
    return null;
}

/**
 * affiche ce qu'on connait sur un objet
 *
 * 
 */
function print_obj ($mixed = null, $color = 'black', $nom = null) 
{
    echo '<pre style="text-align: left; font-size:11px; color:' . $color . '">';
    if (strlen($nom)) {
        echo $nom . " : ";
    }
    // recupere toutes les classes parentes de l'objet
    $classes = array(); 
    $classe = get_class($mixed); 
    $classes[] = $classe; 
    for ($parclass = $classe; $parclass = get_parent_class($parclass); $classes[] = $parclass) {
    } 
    // affiche les methodes de chacune des classes
    foreach ($classes as $uneclasse) {
        echo '<strong>class ' . $uneclasse . '</strong> : '; 
        print_r(get_class_methods($uneclasse)); 
        echo '<br /><br />'; 
    }
    echo '</pre>';
    return null;
}

/** 
 * Decrit (format XML) un objet
 *
 *
 */
function describe_obj ($mixed = null) 
{
    try {
        header('content-type: text/xml');
        $doc = new DocGen2XML($mixed);
        $display = $doc->toXML();
    }
    catch (Exception $e) {
        $display = $e;
    }

    echo $display;

}

/** 
 * Decrit le plus completement possible un objet
 *
 *
 */
function detaille_obj ($mixed = null) 
{
    // recupere les informations sur l'objet 
    ob_start();
    Reflection::export(new ReflectionObject($mixed));
    $content = ob_get_contents();
    ob_end_clean();
    // affiche les informations sur l'objet
    highlight_string('<?php ' . $content . ' ?>'); 
}


/***************************
  SECURITE ET BONNES PRATIQUES 
 ***************************/

/**
 * remplace les quotes et double quotes par leurs equivalents HTML 
 *
 * 
 */
function htmlquotes ($str) 
{
    $str = str_replace("'", '&#039;', $str);
    $str = str_replace('"', '&quot;', $str);
    return $str;
}

/**
 * wrapper pour la fonction mysql_query pour afficher ou masque les erreurs SQL en environnement dev/production 
 *
 * 
 */
function mysqlquery ($sql, $id = null, $debug = null) 
{
    // on passe automatiquement en mode debug si la constante correspondante est définie et qu'on n'a pas spécifié explicitement si on le voulait 
    if ($debug === null) {
        $debug = constant('__DEBUG__'); 
    }

    switch ($debug) {
        case 1 : 
            if ($id) {
                $ret = mysql_query($sql, $id) or die ("SQL : " . $sql . "<br />" . mysql_error());
            } else {
                $ret = mysql_query($sql) or die ("SQL : " . $sql . "<br />" . mysql_error());
            }
            break;
        default : 
            if ($id) {
                $ret = mysql_query($sql, $id);
            } else {
                $ret = mysql_query($sql);
            }
            break;
    }
    return $ret;
}

/**
 * wrapper pour la fonction mysql_query  qui permet de gérer plus simplement la pagination
 *
 * 
 */
function mysqlquery_pagination ($sql, $id = null, $page = null, $nb_par_page = 10, $debug = null) 
{
    // on passe automatiquement en mode debug si la constante correspondante est définie et qu'on n'a pas spécifié explicitement si on le voulait 
    if ($debug === null) {
        $debug = constant('__DEBUG__'); 
    }

    // modifie la requete SQL pour tenir compte des besoins de la pagination
    if ($page) {
        if ($page < 1) {
            $page = 1; 
        }
        $offset = ($page - 1) * $nb_par_page; 

        $sql  = str_ireplace('SELECT ', 'SELECT SQL_CALC_FOUND_ROWS ', $sql); 
        $sql .= ' LIMIT ' . $nb_par_page . ' OFFSET ' . $offset; 

        // fonction en cours
        // de développement 
        // (a terminer)

    }

    $ret = mysqlquery($sql, $id, $debug); 
    $cnt = mysql_num_rows($ret); 

    if ($page) {
        $sql_cnt        = 'SELECT FOUND_ROWS() as cnt';
        $stmt_cnt       = mysqlquery($sql_cnt, $db); 
        $cnt_total      = mysql_fetch_array($stmt_cnt); 
        $cnt_total      = $cnt_total['cnt']; 
        $page_max       = ceil($cnt_total / $nb_par_page); 
    }

    return ($ret); 

}

/**
 * recupere la variable dans $_POST[] si elle y existe. Sinon, recupere la variable dans $_GET[] 
 *
 * 
 */
function ifPostThenGet ($type, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    if (isset($_POST[$key])) {
        $r = ifPost($type, $key, $ifset, $ifnotset, $non_vide, $trim, $striptags);
    } else {
        $r = ifGet($type, $key, $ifset, $ifnotset, $non_vide, $trim, $striptags);
    }
    return $r;
}

/**
 * verifie si la variable existe dans $_GET[], s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose a la place. 
 *
 * 
 */
function ifGet ($type, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    $r = ifSetGet($type,$_GET,$key,$ifset,$ifnotset,$non_vide,$trim,$striptags);
    if (!get_magic_quotes_gpc()) {
        $r = addslashes($r);
    }
    // ATTENTION A L'ENCODAGE : pour s'assurer que les caractères accentués (ou le sigle euro par exemple) sont bien transmis, il FAUT encoder l'URL
    // En JAVASCRIPT on utilisera la fonction encodeURIComponent
    // En PHP on utilisera la fonction rawurlencode() pour obtenir le meme encodage (et non urlencode(), qui n'encode pas les espaces pareil)
    $r = htmlentities($r, ENT_QUOTES, 'UTF-8'); 
    return $r;
}

/**
 * verifie si la variable existe dans $_POST[], s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose a la place. 
 *
 * 
 */
function ifPost ($type, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    $r = ifSetGet($type,$_POST,$key,$ifset,$ifnotset,$non_vide,$trim,$striptags);
    if (!get_magic_quotes_gpc()) {
        $r = addslashes($r);
    }
    $r = htmlentities($r, ENT_QUOTES, 'UTF-8'); 
    return $r;
}

/**
 * verifie si la variable existe dans $_COOKIE[], s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose a la place. 
 *
 * 
 */
function ifCookie ($type, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    $r = ifSetGet($type,$_COOKIE,$key,$ifset,$ifnotset,$non_vide,$trim,$striptags);
    if (!get_magic_quotes_gpc()) {
        $r = addslashes($r);
    }
    $r = htmlentities($r, ENT_QUOTES, 'UTF-8'); 
    return $r;
}

/**
 * verifie si la variable existe dans $_REQUEST[], s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose a la place. 
 *
 * 
 */
function ifRequest ($type, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    $r = ifSetGet($type,$_REQUEST,$key,$ifset,$ifnotset,$non_vide,$trim,$striptags);
    if (!get_magic_quotes_gpc()) {
        $r = addslashes($r);
    }
    $r = htmlentities($r, ENT_QUOTES, 'UTF-8'); 
    return $r;
}

/**
 * verifie si la variable existe dans $_SESSION[], s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose a la place. 
 *
 * 
 */
function ifSession ($type, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    return isset($_SESSION) ? ifSetGet($type,$_SESSION,$key,$ifset,$ifnotset,$non_vide,$trim,$striptags) : false;
}

/**
 * verifie si la variable existe, s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose a la place. 
 *
 * 
 */
function ifSet ($type, $var, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    if (!isset($var)) {
        return $ifnotset;
    } else {
        if ($striptags) {
            $var = strip_tags($var);
        }
        if ($trim) {
            $var = trim($var);
        }
        if ($trim == 2) {
            $var = trim(preg_replace("/\r/", '', preg_replace("/\n/", '', $var)));
        }
        if (!(!$non_vide || ($non_vide && strlen($var)))) {
            return $ifnotset;
        }
        if (isset($ifset) && !is_array($ifset)) {
            return $ifset;
        } else {
            $ret = $var;
            @settype($ret, $type); // securite !
            // si ifset est un tableau, on renvoie la concatenation de ses elements, en concatenant $var a chacun
            if (is_array($ifset)) {
                $retour = '';
                $ifset_sz = count($ifset) - 1;
                if ($ifset_sz < 1) {
                    $ifset_sz = count($ifset);
                }
                for ($i = 0; $i < $ifset_sz; ++$i) {
                    $retour .= $ifset[$i] . $ret;
                }
                if ($ifset_sz == count($ifset) - 1) {
                    $retour .= $ifset[$ifset_sz];
                }
                return $retour;
            } else {
                return $ret;
            }
        }
    }
} 

/**
 * verifie si la variable existe dans le tableau passe en parametre, s'assure de son type, et la renvoie, a moins qu'on ne veuille renvoyer autre chose  
 *
 * 
 */
function ifSetGet ($type, $tableau, $key, $ifset = null, $ifnotset = null, $non_vide = 0, $trim = 0, $striptags = 1) 
{
    if (!isset($tableau[$key])) {
        // securite !
        @settype($ifnotset, $type); 
        return $ifnotset;
    } else {
        if ($striptags) { 
            $striptags_tags = ((strlen($striptags) && $striptags != 1) ? $striptags : ''); // tags a preserver
            $tableau[$key]  = strip_tags($tableau[$key], $striptags_tags); 
        } 
        if ($trim) {
            $tableau[$key] = trim($tableau[$key]);
        }
        if ($trim == 2) {
            $tableau[$key] = trim(preg_replace("/\r/", '', preg_replace("/\n/", '', $tableau[$key])));
        }
        if (!(!$non_vide || ($non_vide && strlen($tableau[$key])))) {
            return $ifnotset;
        }
        if (isset($ifset) && !is_array($ifset)) {
            return $ifset;
        } else {
            $ret = $tableau[$key];
            // securite !
            @settype($ret, $type); 
            // si ifset est un tableau, on renvoie la concatenation de ses elements, en concatenant $var a chacun
            if (is_array($ifset)) {
                $retour = '';
                $ifset_sz = count($ifset) - 1;
                if ($ifset_sz < 1) {
                    $ifset_sz = count($ifset);
                }
                for ($i = 0; $i < $ifset_sz; ++$i) {
                    $retour .= $ifset[$i] . $ret;
                }
                if ($ifset_sz == count($ifset) - 1) {
                    $retour .= $ifset[$ifset_sz];
                }
                return $retour;
            } else {
                return $ret;
            }
        }
    }
}

/**
 * retourne la taille maximale de fichier qu'on peut uploader sur ce serveur ! 
 *
 * 
 */
function get_max_filesize () 
{
    $tab = array(); 
    $tab[] = strtoupper(ini_get('upload_max_filesize')); 
    $tab[] = strtoupper(ini_get('post_max_size')); 
    // max_input_time ? comment le gérer ?
    // memory_limit ? comment le gérer ?

    // on convertit tout en octets ! 
    $cnt_tab = count($tab); 
    for ($i = 0; $i < $cnt_tab; ++$i) { 
        $unite = preg_replace('/[0-9]*/', '', $tab[$i]);
        switch ($unite) { 
            case '' : 
                break; 
            case 'K' : 
                $tab[$i] = $tab[$i]*1024; 
                break; 
            case 'M' : 
                $tab[$i] = $tab[$i]*1024*1024; 
                break; 
            case 'G' : 
                $tab[$i] = $tab[$i]*1024*1024*1024; 
                break; 
            default :
                trigger_error('Unite inconnue dans la fonction utilisateur get_max_filesize() '); 
                break; 
        }
    }
    $min = min($tab); 
    return $min;
}

/**********************
  TRAITEMENT DE VARIABLES
 **********************/

/**
 * effectue une sorte de substr sur le texte passe en parametre, en ignorant le code HTML et en coupant proprement !
 *
 * 
 */
function crop_html ($texte, $max, $trim = false, $specialchar = "\xef") 
{ 

    // encode toutes les entites possibles en HTML
    $texte = htmlentities($texte, ENT_QUOTES, 'UTF-8', false); // double_encode à false pour eviter de reconvertir des caractères qui le seraient deja. Necessite PHP5.2.3

    // on travaille en ISO pour que les caractères aient tous strlen() à 1
    $utf_converti = 0; 
    if (utf8_encode(utf8_decode($texte)) == $texte) { // si on est en utf8
        $texte = utf8_decode($texte); // on convertit en iso-8859-1 
        $utf_converti = 1; 
    }

    $texte = html_entity_decode($texte, ENT_QUOTES, 'UTF-8'); // il faut TOUT convertir, guillemets et quotes y compris

    // DETERMINER OU IL FAUT COUPER PAR RAPPORT AUX BALISES HTML... 
    $pile_balises = array(); 
    preg_match_all('/<[^>]*>/', $texte,$pile_balises); 
    $texte_htmlscrambled = $texte; 
    foreach ($pile_balises[0] as $balise) {
        $replacement  = ''; 
        for ($i = (strlen($balise)); $i; --$i) {
            $replacement .= $specialchar; 
        }
        $texte_htmlscrambled = str_replace($balise, $replacement, $texte_htmlscrambled); 
    }
    // calcul de la position translatee de max
    $translated_max = 0; 
    $texte_length = strlen($texte); 
    $compteur = 0; 
    for ($i = 0; $i < $texte_length; ++$i) {
        if ($texte_htmlscrambled[$i] != $specialchar) {
            ++$compteur; 
            if ($compteur == $max) {
                $translated_max = $i + 1; 
                break; 
            }
        }
    }

    // FERMER LES BALISES QUI LE NÉCESSITENT

    // tronque le texte a la position determinee
    $crop = substr($texte, 0, $translated_max); 
    // remplace le dernier mot par une elipse
    $crop = preg_replace('/<a [^>]*>[^> ]$/', '', $crop); // si on coupe au milieu d'un lien, on vire tout le texte !
    $crop = preg_replace('/#[a-zA-Z0-9]{0,7}$/', '', $crop); // on remplace toute entite HTML tronquee par du vide. 
    $crop = preg_replace('/[^> ]$/', '...', $crop); 

    // nettoie le texte s'il a ete coupe au milieu d'une balise
    $crop = preg_replace('/<[^>]*$/', '', $crop); 

    // detecte les balises qui restent à fermer
    $pile_balises = array(); 
    preg_match_all('/<[^>]*>/',$crop,$pile_balises); 

    // inverser le tableau
    $pile_balises = array_reverse($pile_balises[0]); 

    $str = ''; 
    foreach ($pile_balises as $balise) {
        $balise_courte = preg_replace('/<([^> ]*)[^>]*>/', '<$1>', $balise); 
        $str .= $balise_courte; 
    }
    foreach ($pile_balises as $balise) {
        $type_balise = preg_replace('/<\/*([^> ]*)[^>]*>/', '$1', $balise); 
        $str = preg_replace('/<' . $type_balise . '><\/' . $type_balise . '>/', '', $str); 
    }

    // construit la chaine de fermeture
    $balises_a_fermer = explode('><', substr($str, 1, 0 - 1)); 
    $balises_a_ignorer = array('br', 'hr', 'img', 'input'); // balises qu'on ne ferme pas (du moins pas comme les autres)
    $chaine_fermeture = ''; 
    $nb_balises_a_fermer = count($balises_a_fermer); 
    for (; $nb_balises_a_fermer; --$nb_balises_a_fermer) {
        $curbal = $balises_a_fermer[$nb_balises_a_fermer - 1]; 
        // balises a ignorer, pas à fermer... 
        if ($curbal == '' || $curbal[0] == '/' || in_array($curbal, $balises_a_ignorer)) {
            continue; 
        }
        $chaine_fermeture .= '</' . $curbal . '>'; 
    }

    // supprime les retours à la ligne et autres balises vides a la fin du texte
    if ($trim) {
        // supprime les balises mal fermees
        $crop = preg_replace('/<[^>]*>$/', '', $crop); 
        // supprime les balises fermees mais vides
        $crop = preg_replace('/<[^>]*><\/[^>]*>$/', '', $crop); 
    }

    $crop .= $chaine_fermeture; 

    if ($utf_converti) {
        // on travaille en ISO pour que les caractères aient tous strlen() à 1
        return utf8_encode($crop); 
    } else {
        return $crop; 
    }
}

/** 
 * renvoie la chaine sans accents 
 *
 */
function remove_accents ($string, $german = false) 
{
    $string = html_entity_decode($string, ENT_QUOTES, 'UTF-8');
    // Single letters
    $single_fr = explode(" ", "À Á Â Ã Ä Å &#260; &#258; Ç &#262; &#268; &#270; &#272; Ð È É Ê Ë &#280; &#282; &#286; Ì Í Î Ï &#304; &#321; &#317; &#313; Ñ &#323; &#327; Ò Ó Ô Õ Ö Ø &#336; &#340; &#344; ¦ &#346; &#350; &#356; &#354; Ù Ú Û Ü &#366; &#368; Ý ´ &#377; &#379; à á â ã ä å &#261; &#259; ç &#263; &#269; &#271; &#273; è é ê ë &#281; &#283; &#287; ì í î ï &#305; &#322; &#318; &#314; ñ &#324; &#328; ð ò ó ô õ ö ø &#337; &#341; &#345; &#347; ¨ &#351; &#357; &#355; ù ú û ü &#367; &#369; ý ÿ ¸ &#378; &#380;");
    $single_to = explode(" ", "A A A A A A A A C C C D D D E E E E E E G I I I I I L L L N N N O O O O O O O R R S S S T T U U U U U U Y Z Z Z a a a a a a a a c c c d d e e e e e e g i i i i i l l l n n n o o o o o o o o r r s s s t t u u u u u u y y z z z");
    $single = array();
    for ($i = 0; $i < count($single_fr); ++$i) {
        $single[$single_fr[$i]] = $single_to[$i];
    }
    // Ligatures
    $ligatures = array("Æ"=>"Ae", "æ"=>"ae", "œ"=>"oe", "Œ"=>"Oe", "ß"=>"ss");
    // German umlauts
    if ($german) {
        $umlauts = array("Ä"=>"Ae", "ä"=>"ae", "Ö"=>"Oe", "ö"=>"oe", "Ü"=>"Ue", "ü"=>"ue");
    }
    // Replace
    $replacements = array_merge($single, $ligatures);
    if ($german) {
        $replacements = array_merge($replacements, $umlauts);
    }
    $string = strtr($string, $replacements);

    return $string;
}

/**
 * supprime les retours a la ligne d'une chaine. 
 *
 */
function strip_crlf (&$txt) 
{
    $txt = str_replace("\n", "", $txt); 
    $txt = str_replace("", "", $txt); 
}

/**
 * supprime les tabulations d'une chaine. 
 *
 */
function strip_tabulations (&$txt) 
{
    $txt = str_replace("\t", "", $txt); 
}

/**
 * decode les caracteres HTML d'une chaine. 
 *
 */
function entity_decode (&$txt) 
{
    $txt = html_entity_decode($txt, ENT_QUOTES, 'UTF-8'); 
}

/**
 * passe une chaine en majuscules SANS ACCENTS. 
 *
 */
function toupper (&$txt) 
{
    $txt = strtoupper(remove_accents($txt)); 
    return $txt;
}

/**
 * passe une chaine en minuscules SANS ACCENTS. 
 *
 */
function tolower (&$txt) 
{
    $txt = strtolower(remove_accents($txt)); 
    return $txt;
}

/**
 * affiche un nombre au format fr  
 *
 */
function formate_nombre ($nb) 
{
    return number_format(round($nb, 2), 2, ',', ' ');
}

/**
 * convertit une date du format fr au format en 
 *
 */
function date_fr_to_en ($madate, $shorten = null) 
{
    $ret = preg_replace('/([[:digit:]]{2}).([[:digit:]]{2}).([[:digit:]]{4})( *.*)/', '\3-\2-\1\4', $madate);
    return $shorten ? substr($ret, 0, 10) : $ret;
}

/**
 * convertit une date du format en au format fr 
 *
 */
function date_en_to_fr ($madate, $shorten = null) 
{
    $ret = preg_replace('/([[:digit:]]{4}).([[:digit:]]{2}).([[:digit:]]{2})( *.*)/', '\3/\2/\1\4', $madate);
    return $shorten ? substr($ret, 0, 10) : $ret;
}

/**
 * convertit une date au format date_time de mysql par exemple (YYYY-MM-DD hh:ii:ss) en timestamp
 *
 */
function date_time_stamp ($date) 
{
    if (!trim($date)) {
        return false; 
    }
    $expl = explode(' ' , $date); 
    if (isset($expl[0])) {
        $expl[0] = strtr($expl[0], '_/.:', '----'); 
        $expl_date = explode('-', $expl[0]); 
    } else {
        $expl_date = array(0,0,0); 
    }
    if (isset($expl[1])) {
        $expl[1] = strtr($expl[1], '_/.-', '::::'); 
        $expl_time = explode(':', $expl[1]); 
    } else {
        $expl_time = array(0,0,0); 
    }
    return mktime($expl_time[0], $expl_time[1], $expl_time[2], $expl_date[1], $expl_date[2], $expl_date[0]); 
}

/**
 * supprime le premier et le dernier caractère d'une chaine. fonction utilisee dans db_get_enum_values 
 *
 */
function strip_extremites (&$txt) 
{
    $txt = substr($txt, 1, 0 - 1); 
}

/**
 * dedoublonne un tableau, avec sensibilite ou non a la casse // EN COURS DE DEVELOPPEMENT 
 *
 */
function trim_value (&$value, $params) 
{
    if (is_array($value)) {
        if ($params['recurs']) {
            dedoublonner($value, $params['insensible'], $params['trim'], $params['recurs']);
        }
    } else {
        $value = trim($value); 
    }
}

/**
 * dedoublonnage de tableau 
 *
 */
function dedoublonner (&$tableau, $insensible = 0, $trim = 1, $recurs = 0) 
{
    if ($trim) {
        // TRIM
        array_walk($tableau, 'trim_value', array('insensible' => $insensible, 'trim' => $trim, 'recurs' => $recurs)); 
    }
    // UNIQ
    $tableau = array_unique($tableau);
    $cnt = count($tableau);
    if ($insensible) {
        for ($i = 0; $i < $cnt; ++$i) {
            // ejecter les valeurs deja presentes (sans tenir compte de la casse) a un autre index du tableau
            for ($j = $i + 1; $j < $cnt; ++$j) {
                // conditions d'egalite
                // - insensibilite a la casse
                // - on pourrait aussi supprimer les accents...
                if (isset($tableau[$i]) && (@strtoupper($tableau[$i]) == @strtoupper($tableau[$j]))) {
                    unset ($tableau[$i]); 
                }
            }
        }
    }
    if ($insensible) {
        // SORT
        natcasesort($tableau);
    } else {
        // SORT
        asort($tableau);
    }
}

/**
 * renvoie vrai si le nombre est pair
 *
 */
function est_pair ($i) 
{
    return (!($i % 2)); 
}

/**
 * Sort DB result
 *
 * @param array $data Result of sql query as associative array
 *
 * Rest of parameters are optional
 * [, string $name  [, mixed $name or $order  [, mixed $name or $mode]]]
 * $name string - column name i database table
 * $order integer - sorting direction ascending (SORT_ASC) or descending (SORT_DESC)
 * $mode integer - sorting mode (SORT_REGULAR, SORT_STRING, SORT_NUMERIC)
 *
 * <code>
 * <?php
 * // You can sort data by several columns e.g.
 * $data = array();
 * for ($i = 1; $i <= 10; $i++) {
 *     $data[] = array( 'id' => $i,
 *                      'first_name' => sprintf('first_name_%s', rand(1, 9)),
 *                      'last_name' => sprintf('last_name_%s', rand(1, 9)),
 *                      'date' => date('Y-m-d', rand(0, time()))
 *                  );
 * }
 * $data = sort_db_result($data, 'date', SORT_DESC, SORT_NUMERIC, 'id');
 * printf('<pre>%s</pre>', print_r($data, true));
 * $data = sort_db_result($data, 'last_name', SORT_ASC, SORT_STRING, 'first_name', SORT_ASC, SORT_STRING);    
 * printf('<pre>%s</pre>', print_r($data, true));
 * ?>
 * </code>
 *
 * @return array $data - Sorted data
 */
function sort_db_result (array $data /*$name, $order, $mode*/) 
{
    $_argList = func_get_args();
    $_data = array_shift($_argList);
    if (empty($_data)) {
        return $_data;
    }
    $_max = count($_argList);
    $_params = array();
    $_cols = array();
    $_rules = array();
    for ($_i = 0; $_i < $_max; $_i += 3) {
        $_name = (string) $_argList[$_i];
        if (!in_array($_name, array_keys(current($_data)))) {
            continue;
        }
        if (!isset($_argList[($_i + 1)]) || is_string($_argList[($_i + 1)])) {
            $_order = SORT_ASC;
            $_mode = SORT_REGULAR;
            $_i -= 2;
        } else if (3 > $_argList[($_i + 1)]) {
            $_order = SORT_ASC;
            $_mode = $_argList[($_i + 1)];
            $_i--;
        } else {
            $_order = $_argList[($_i + 1)] == SORT_ASC ? SORT_ASC : SORT_DESC;
            if (!isset($_argList[($_i + 2)]) || is_string($_argList[($_i + 2)])) {
                $_mode = SORT_REGULAR;
                $_i--;
            } else {
                $_mode = $_argList[($_i + 2)];
            }
        }
        $_mode = $_mode != SORT_NUMERIC
                    ? $_argList[($_i + 2)] != SORT_STRING ? SORT_REGULAR : SORT_STRING
                    : SORT_NUMERIC;
        $_rules[] = array('name' => $_name, 'order' => $_order, 'mode' => $_mode);
    }
    foreach ($_data as $_k => $_row) {
        foreach ($_rules as $_rule) {
            if (!isset($_cols[$_rule['name']])) {
                $_cols[$_rule['name']] = array();
                $_params[] = &$_cols[$_rule['name']];
                $_params[] = $_rule['order'];
                $_params[] = $_rule['mode'];
            }
            $_cols[$_rule['name']][$_k] = $_row[$_rule['name']];
        }
    }
    $_params[] = &$_data;
    call_user_func_array('array_multisort', $_params);
    return $_data;
} 


/****************
  TRAITEMENT D'URLS
 ****************/

/**
 * remet en forme une url pour ajouter le http:// devant si necessaire, et le / a la fin si necessaire
 *
 */
function formate_url ($url) 
{
    if ((substr($url, 0, 7) != 'http://') && (substr($url, 0, 8) != 'https://')) {
        $url = 'http://' . $url; 
    }
    $parse = @parse_url($url); 
    if (!is_array($parse)) {
        return false; 
    } elseif (!trim($parse['host'])) {
        return false; 
    } else {
        $url = $parse['scheme'] . '://' .  $parse['host'] . '/'; 
        if (isset($parse['path']) && strlen($parse['path'])) {
            // evitons de rajouter des / inutiles ! 
            $url .= substr($parse['path'], 1); 
        }
        if (isset($parse['query']) && strlen($parse['query'])) {
            $url .= '?' . $parse['query']; 
        }
        if (isset($parse['fragment']) && strlen($parse['fragment'])) {
            $url .= '#' . $parse['fragment']; 
        }
        return $url; 
    }
}

/**
 * filtre un parametre d'une url. renvoie l'url SANS ce parametre (quelle que soit sa valeur si on ne la precise pas) 
 *
 */
function filtre_param ($url, $param, $valeur = null) 
{
    if ($valeur == null) {
        $valeur = '[^&]*';
    }
    $recherche = array('/(.*?)' . $param . '=' . $valeur . '(.*)/', '/&&/', '/&$/', '/\?&/');
    $remplace  = array('$1$2', '&', '', '?');
    return preg_replace($recherche, $remplace, $url);
}

/**
 * ajoute un parametre dans une url  
 *
 */
function add_param ($url, $param, $valeur) 
{
    if ((strpos($url, '&')) !== false || (strpos($url, '?')) !== false) {
        $url .= '&' . $param . '=' . $valeur;
    } else {
        $url .= '?' . $param . '=' . $valeur;
    }
    $recherche = array('/&&/', '/&$/', '/\?&/');
    $remplace  = array('&', '', '?');
    return preg_replace($recherche, $remplace, $url);
}

/**
 * change un parametre dans une url 
 *
 */
function change_param ($url, $param, $valeur) 
{
    $url = filtre_param($url, $param);
    $url = add_param($url, $param, $valeur);
    //$url = preg_replace ('/(.*)([?&]*)'.$param.'=([^&]*)(.*)/', '/$1$2'.$param.'='.$valeur.'$4/', $url);
    return $url;
}

/**
 * effectue une redirection en PHP, Javascrip et au pire, écrit le lien si le UserAgent ne suit aucune des redirections précédentes. Termine bien sur par un die(); . 
 *
 */
function redirection ($url, $code_http = 302) 
{
    // header('X-Requested-With: XMLHttpRequest', true); 
    header('Location: ' . $url, true, $code_http); 
    if (!__DEBUG__) {
        die('<script type="text/Javascript">setTimeout("document.location = \'' . $url . '\'",3000);</script><noscript>Redirection dans 3 secondes vers : <a href="' . $url . '">' . $url . '</a></noscript>'); 
    } else {
        die(); 
    }
}

/**
 * supprime les caracteres qui ne doivent pas etre presents dans les mots cles d'une URL 
 *
 */
function urlize ($element) 
{
    $element_clean = html_entity_decode(stripslashes($element), ENT_QUOTES, 'UTF-8');
    $element_clean = strip_tags($element_clean);
    $element_clean = remove_accents($element_clean);
    $element_clean = strtolower($element_clean);
    $element_clean = preg_replace('/[^a-zA-Z0-9 ]*/', '', $element_clean);
    $element_clean = preg_replace('/ /',  '-', $element_clean);
    $element_clean = preg_replace('/--*/', '-', $element_clean);
    return $element_clean;
}



/**************
  BASE DE DONNEES
 **************/

/**
 * renvoie dans un tableau les differentes valeurs que peut prendre un champ enum 
 *
 */
function db_get_enum_values ($table, $field) 
{
    $sql = 'show columns from ' . $table . ' where field="' . $field . '";';
    $res_id = mysqlquery($sql, __DEBUG__);
    $res = mysql_fetch_array($res_id);
    $types = explode(',', substr($res['Type'], 5, 0 - 1));

    array_walk($types, 'strip_extremites');

    return $types;
}

/**
 * renvoie la taille d'champ 
 *
 */
function db_get_field_size ($table, $field) 
{
    $sql = 'show columns from ' . $table . ' where field="' . $field . '";';
    $res_id = mysqlquery($sql, __DEBUG__);
    $res = mysql_fetch_array($res_id);
    $size = (int) preg_replace('/.*\((.*)\)/', '\1', $res['Type']); 
    return $size; 
}

/**
 * petit raccourci pour acceder a un champ clairement identifie dans une table 
 *
 */
function db_get_val ($field, $table, $where = '1 = 1', $debug = 0) 
{
    $sql    = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $where;
    if ($debug) {
        print_pre($sql, 'red');
    }
    $res_id = mysqlquery($sql, __DEBUG__);
    $res    = mysql_fetch_array($res_id);
    return $res[$field];
}

/**
 * petit raccourci pour faire des requetes SELECT 
 *
 */
function db_get_array ($field, $table, $where = null) 
{
    $sql    = 'SELECT ' . $field . ' FROM ' . $table;
    if (strlen($where)) {
        $sql .= ' WHERE ' . $where;
    }
    $res_id = mysqlquery($sql, __DEBUG__);
    $res    = mysql_fetch_array($res_id, MYSQL_ASSOC);
    return $res;
}

/**
 * recupere recursivement une arborescence d'identifiants dans un tableau. format du tableau : array (array (profondeur, identifiant), ..., ..., ...) 
 *
 */
function db_get_hierarchy ($table, $field, $parent_field, $start_value, $where = null, $a_plat = 0, $profondeur = 0) 
{
    $liste = array();
    $sql = 'SELECT ' . $field . ' FROM ' . $table . ' WHERE ' . $parent_field . ' = \'' . $start_value . '\' ' . $where . ' ORDER BY ' . $field . '; ';
    $res_id = mysqlquery($sql, __DEBUG__);

    if (!mysql_num_rows($res_id)) {
        return false; 
    }

    for (; $res = mysql_fetch_array($res_id); true) {
        $liste[] = array($profondeur, $res[$field]);

        // RECURSIVITE ICI 
        if ($fils = db_get_hierarchy($table, $field, $parent_field, $res[$field], $where, $a_plat, $profondeur + 1)) {
            if ($a_plat) {
                $liste = array_merge($liste, $fils);
            } else {
                $liste[] = array($fils, $profondeur);
            }
        }
    }
    return $liste;
}


/****************************
  PRATIQUE POUR LES FORMULAIRES
 ****************************/

/**
 * cree un champ input du type choisi, le nom choisi, la classe et le style choisis, et les fonctionnalites de validation JS/PHP 
 *
 */
function create_input ($name, $formulaire = null, $requis = 1, $afficher = null, $type = "text", $value = "", $class = null, $style = null, $style_err = null, $regexp = null, $taille_min = 2, $taille_max = null) 
{
    $validation_js = "validation_champ(!valide_champ('id_" . $formulaire . "_field_" . $name . "','','id_" . $formulaire . "_div_is_" . $name . "_ok','" . htmlquotes(__REQUIREMENT__) . "','" . htmlquotes(__REQUIREMENT_OK__) . "', '" . $taille_min . "', '" . $taille_max . "', '" . $regexp . "'),'id_" . $formulaire . "_err_is_" . $name . "_ok','" . constant('__ERR_' . strtoupper($name) . '__') . "', " . $afficher . "); ";
?>
<span id="id_<?php echo $formulaire; ?>_div_is_<?php echo $name; ?>_ok" <?php echo $requis ? "" : "style=\"display: none; \" "; ?>><?php echo __REQUIREMENT__; ?></span>
<input 
    id="id_<?php echo $formulaire; ?>_field_<?php echo $name; ?>" 
    name= "<?php echo $name;  ?>" 
    type= "<?php echo $type;  ?>" 
    class="<?php echo $class; ?>" 
    style="<?php echo $style; ?>" 
    value="<?php echo $value; ?>" 
    onfocus=" <?php echo $validation_js; ?>" 
    onblur="  <?php echo $validation_js; ?>" 
    onclick=" <?php echo $validation_js; ?>" 
    onkeyup=" <?php echo $validation_js; ?>" 
    onchange="<?php echo $validation_js; ?>" 
/>
<?php
    if ($style_err) {
        global $tableau_error_handler;
        $id_div = 'id_' . $formulaire . '_err_is_' . $name . '_ok';
        $message = @$tableau_error_handler[$id_div]; 
        if (strlen($message)) {
            echo '<div class="tableau_error_handler_look" style="' . $style_err . (__BORDERLESS_ERRORS__ ? ' background-color: transparent; border: solid black 0px; ' : '') . ' ">';
            trigger_error(serialize(array($message, $id_div, 'width: 100%; ')), E_USER_WARNING); 
            echo '</div>'; 
        }
        unset($tableau_error_handler[$id_div]);
    }
}

/**
 * renvoie un identifiant unique, genere aleatoirement 
 *
 */
function get_new_trans () 
{
    return md5(microtime() . rand(0, getrandmax()));
}

/**
 * verifie le format d'un email : retourne vrai si la chaine est une adresse email 
 *
 */
function valide_email ($email) 
{
    return preg_match('/^[a-z0-9\._-]+@([a-z0-9-]+\.)+[[:alpha:]]+$/i', $email);
}

/**
 * verifie le format d'un login 
 *
 */
function valide_login ($login, $taille_min, $taille_max = null) 
{
    $retour =  (preg_match('/^[a-z0-9@\._-]+$/',$login))
            && (strlen($login) >= $taille_min);

    if (($taille_max) && strlen($retour) > $taille_max) {
        return false;
    }

    return $retour;

}

/**
 * verifie le format d'un numero de telephone 
 *
 */
function valide_tel ($tel) 
{
    return preg_match('/^[0-9()+ \.-]+$/i',$tel);
    // return preg_match('/^[0-9]+$/i',$tel);
}


/**
 * verifie le format d'un code postal francais : 5 chiffres
 *
 */
function valide_cp ($cp) 
{
    return preg_match('/^[0-9]{5}$/i', $cp);
}

/**
 * gere la pagination des resultats 
 *
 */
function pagination ($reload, $start, $nb_res_total, $nb_res_prevu = 20, $precedent = '&lt; Pr&eacute;c&eacute;dent', $suivant = ' Suivant &gt') 
{
    if ($start < $nb_res_total) {
        if ($start != 0) {
            echo "<a  class=''
                href='$reload&start=" . (ifGet('int', 'start') - $nb_res_prevu)
                . (ifGet('string', 'alpha',    array("&alpha="), ''))
                . (ifGet('string', 'id_genre', array("&id_genre="), ''))
                . (ifGet('string', 'order',    array("&order="), ''))
                . "'>";
            if (is_array($precedent)) {
                echo $precedent[1];
            } else {
                echo $precedent;
            }
            echo "</a>";
        } else {
            if (is_array($precedent)) {
                echo $precedent[0];
            } else {
                echo $precedent;
            }
        }

        if (($start + $nb_res_prevu) < $nb_res_total) {
            echo "<a class='' href='$reload&start=" . ($start + $nb_res_prevu)
                . (ifGet('string', 'alpha',    array("&alpha="), ''))
                . (ifGet('string', 'id_genre', array("&id_genre="), ''))
                . (ifGet('string', 'order',    array("&order="), ''))
                . "'>";
            if (is_array($suivant)) {
                echo $suivant[1];
            } else {
                echo $suivant;
            }
            echo "</a>";
        } else {
            if (is_array($suivant)) {
                echo $suivant[0];
            } else {
                echo $suivant;
            }
        }
        echo '<br>R&eacute;sultats ' . $start . ' &agrave; ' . min(($start + $nb_res_prevu), $nb_res_total) . ' sur ' . $nb_res_total . '<br />';
    } 
}


/*************
  EXPORT ET MAIL
 *************/

/**
 * une fonction pour envoyer un mail, securisee, avec contenu HTML alternatif 
 *
 */
function envoie_mail ($dest, $exp, $societe, $titre, $message_text, $message_html = null, $style = 'font-family:Verdana, Arial, Geneva, sans-serif; font-size:12px; color:#444444;') 
{

    // MIME BOUNDARY

    $mime_boundary = "---- " . $societe . " ----" . md5(time());

    // MAIL HEADERS

    $headers  = "From: " . $societe . " <" . $exp . ">\n";
    $headers .= "Reply-To: " . $societe . " <" . $exp . ">\n";
    $headers .= "Return-Path: " . $societe . " <" . $exp . ">\n";
    $headers .= "MIME-Version: 1.0\n";
    if (strlen($message_html)) {
        $headers .= "Content-Type: multipart/alternative; charset=\"utf-8\"; boundary=\"$mime_boundary\"\n";
    }

    // TEXT EMAIL PART

    $message = "";

    if (strlen($message_html)) {
        $message .= "\n--$mime_boundary\n";
        $message .= "Content-Type: text/plain; charset=\"utf-8\"\n";
        $message .= "Content-Transfer-Encoding: 8bit\n\n";
    }

    $message .= html_entity_decode(stripslashes($message_text), ENT_QUOTES, 'UTF-8');

    // HTML EMAIL PART

    if (strlen($message_html)) {
        $message .= "\n--$mime_boundary\n";
        $message .= "Content-Type: text/html; charset=\"utf-8\"\n";
        $message .= "Content-Transfer-Encoding: 8bit\n\n";

        $message .= "<html>\n";
        $message .= "<body style=\"" . $style . "\">\n";

        $message .= stripslashes($message_html);

        $message .= "</body>\n";
        $message .= "</html>\n";

        // FINAL BOUNDARY

        $message .= "\n--$mime_boundary--\n\n";
    }

    ini_set("sendmail_from",$exp); 
    $resultat = mail($dest, $titre, $message, $headers); 
    ini_restore("sendmail_from"); 

    return $resultat; 

}

/**
 * envoie un rapport d'erreur par mail aux developpeurs 
 *
 */
function alerter_developpeurs ($dst, $societe, $titre, $message) 
{
    envoie_mail($dst, 
                '', 
                $societe, 
                $titre, 
                $message
               . "\n\n\nInformations de debug : "
               . "\n\nGET : " . var_dump_ret($_GET)
               . "\n\nPOST : " . var_dump_ret($_POST)
               . "\n\nSERVER : " . var_dump_ret($_SERVER)
               . "\n\nCOOKIE : " . var_dump_ret($_COOKIE)
               . "\n\nSESSION : " . var_dump_ret($_SESSION)
               . "\n");
}

/**
 * exporte un tableau dans un fichier XLS qu'on propose en telechargement 
 *
 */
function exporte_xls ($tab, $filename = 'export.xls', $aff_ligne_titres = 1) 
{

    $retour = '';
    $separateur = "\t";

    // Important dans la mesure ou nous utilisons les tabulations comme separateur
    // print_pre($tab, 'green');
    // supprime les tabulations qu'on pourrait trouver dans certains champs
    array_walk_recursive($tab, 'strip_tabulations');
    // print_pre($tab, 'red');
    // decode les caracteres HTML
    array_walk_recursive($tab, 'entity_decode');

    // Premiere ligne = nom des champs (si besoin)
    if ($aff_ligne_titres && count($tab)) {
        $entete = array_keys($tab[0]);
        $retour .= implode($separateur, $entete);
        $retour .= "\n";
    }

    // Contenu du fichier
    foreach ($tab as $ligne) {
        if (count($ligne)) {
            foreach ($ligne as $val) {
                $retour .= $val . $separateur;
            }
            // passe a la ligne suivante 
            $retour = substr($retour, 0, 0 - 1) . "\n"; 
        }
    }

    // Ajoute les header et envoie en telechargements 
    header("Content-type: application/vnd.ms-excel; charset=\"utf-8\"\n");
    header("Content-disposition: attachment; filename=" . $filename);
    echo $retour;
}


/**********
  MULTILINGUE
 **********/

/**
 * id de la langue pour la page en cours (dans le cas d'un site multilingue) 
 *
 */
function get_id_langue () 
{
    // detecte la demande de changement de langue / l'absence de langue choisie, et change de langue
    $old_id_langue = ifSession('int', 'id_langue');
    if (ifGet('string','langue')) {
        unset($_SESSION['id_langue']);
        unset($_SESSION['id_pays']);
    }
    $id_langue = ifSession('int', 'id_langue');
    if (!$id_langue) {
        // securite
        $langue    = preg_replace('/[^a-zA-Z]/', '', ifGet('string', 'langue')); 
        $sql       = 'SELECT id_langue, id_pays FROM langue WHERE langue="' . $langue . '";';
        $id_res    = mysqlquery($sql, __DEBUG__);
        $id_array  = mysql_fetch_array($id_res);
        $id_langue = $id_array[0];
        $id_pays   = $id_array[1];
        if (!$id_langue) {
            // par defaut : premiere langue de la BD - Le français dans mon cas
            $sql       = 'SELECT id_langue, id_pays FROM langue ORDER BY id_langue LIMIT 0, 1;';
            $id_res    = mysqlquery($sql, __DEBUG__);
            $id_array  = mysql_fetch_array($id_res);
            $id_langue = $id_array[0];
            $id_pays   = $id_array[1];
        }
        /* // 'affiche plus la box en cas de changement de langue 
           if (ifGet('string', 'langue', 1)
           && (($old_id_langue  && $old_id_langue != $id_langue) 
           || !$old_id_langue))
           trigger_error (serialize(array('__LANGUAGE_CHANGE__','chg_lng')), E_USER_NOTICE);
         */

    }
    $_SESSION['id_langue'] = $id_langue;
    if (isset($id_pays) && $id_pays) {
        $_SESSION['id_pays']   = $id_pays;
    }
    return $id_langue;
}

/**
 * affichage des liens pour changer de langue 
 *
 */
function affiche_change_langue ($id_langue) 
{
    global $debug;
    $sql       = 'SELECT id_langue, langue FROM langue;';
    $id_res    = mysqlquery($sql, __DEBUG__);
    for (; $langues = mysql_fetch_array($id_res); true) {
        // si pas de query_string ou query_string pour choisir la langue uniquement
        $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = htmlspecialchars(change_param($url, 'langue', $langues['langue']));
        echo '<a href="' . $url . '" title="Changer de langue"><img class="invisible highlight ' . ($id_langue == $langues['id_langue'] ? 'highlighted' : '') . '" src="' . __IMG_WWW_ROOT__ . '/' . $langues['langue'] . '.png" alt="' . $langues['langue'] . '" /></a> ';
    }
}


/********************
  TABLEAU ERROR_HANDLER
 ********************/

/**
 * affichage du div de la console d'erreurs 
 *
 */
function affiche_div_erreurs ($divmasque = false, $fademasque = false, $redim = false, $sansbordure = true, $invisible = false) 
{

    global $tableau_error_handler;
    // affichage systematique, meme si masque. ainsi, les div sont tout de meme initialises.
    // $display = (count($tableau_error_handler) ? 'block' : 'none');
    // si tous les elements sont vides, on n'affiche rien !
    $total_strlen = 0;
    foreach ($tableau_error_handler as $key => $val) {
        $val = trim(strip_tags($val));
        if (strlen($val)) {
            ++$total_strlen;
        }
    }
    $display = ($total_strlen ? 'block' : 'none');

    if ($invisible) {
        $display = 'none';
    }

    echo "\n" . '        <div id="tableau_error_handler" class="tableau_error_handler_look tableau_error_handler_position" style="display:' . $display . '; ' . ($sansbordure ? 'background-color: transparent; border: solid black 0px; ' : '') . ' " >';
    if (!$sansbordure) {
        echo "\n" . '            <img style="display: none; " id="tableau_error_handler_TL_img" onclick="showhide(\'div_error_handler_showhide\', this, \'\', \'\', \'' . __WWW_ROOT__ . '/\'); closediv(\'tableau_error_handler_masque\'); " src=\'' . __IMG_WWW_ROOT__ . '/reduire.png\' class=\'tableau_error_handler_TL invisible highlight\' alt=\'reduire\/maximiser\' /> ';
        echo "\n" . '            <div class=\'invisible tableau_error_handler_Tspacer\' >' . ($sansbordure ? '' : '__TITRE_TABLEAU_ERROR_HANDLER__') . '</div>';
        echo "\n" . '            <img id="tableau_error_handler_TR_img" onclick="closediv(\'tableau_error_handler\'); closediv(\'tableau_error_handler_masque\'); " src=\'' . __IMG_WWW_ROOT__ . '/fermer.png\' class=\'tableau_error_handler_TR invisible highlight\' alt=\'fermer\' />';
    } elseif (!$invisible) {
        echo "\n" . '            <img id="tableau_error_handler_TR_img" onclick="closediv(\'tableau_error_handler\'); closediv(\'tableau_error_handler_masque\'); document.getElementById(\'tableau_error_handler\').innerHTML = \'\'; " src=\'' . __IMG_WWW_ROOT__ . '/fermer.png\' class=\'tableau_error_handler_TR invisible highlight\' alt=\'fermer\' style=\'position: absolute; top: 1px; right: 3px; \' /><br />';
    }
    echo "\n" . '            <div id="div_error_handler_showhide" class="tableau_error_handler_content invisible">' . "\n";
    foreach ($tableau_error_handler as $err) {
        echo $err;
    }
    echo "\n" . '            </div>' . "\n" . '        </div>';
    echo "\n" . '        <div id="tableau_error_handler_masque" class="tableau_error_handler_masque" onclick="closediv(\'tableau_error_handler_masque\')" ></div>';
    if ($display != 'none') {
        if ($fademasque) {
            echo "\n" . '<script type="text/Javascript">fade_element(\'tableau_error_handler\'); </script>';
        }

        /* ACTIVE LE DIV MASQUANT LA PAGE ET SON EFFET FADEOUT */
        if ($divmasque) {
            echo "\n" . '<script type="text/Javascript">opendiv(\'tableau_error_handler_masque\'); </script>';
        }
        if ($fademasque) {
            echo "\n" . '<script type="text/Javascript">fade_element_alpha(\'tableau_error_handler_masque\', 20, 1000); </script>';
        }
        if ($redim) {
            echo "\n" . '<script type="text/Javascript">redim_element(\'tableau_error_handler\', 20, 1000); </script>';
        }
    }
}

/**
 * affiche un formulaire pour la suppression de plusieurs remplacements 
 *
 */
function get_form_remplacement_suppr_multiple ($tab_balises, $id_page = null, $id_langue = null, $nbrows = 0, $default_content = "") 
{
    $masquer_choix_page   = 0;
    $masquer_choix_langue = 0;
    if (!$id_page) {
        // valeur par defaut
        $id_page   = 1; 
    } else {
        $masquer_choix_page = 1;
    }
    if (!$id_langue) {
        // valeur par defaut
        $id_langue = ifSession('int', 'id_langue'); 
    } else {
        $masquer_choix_langue = 1;
    }

    // pour avoir eventuellement plusieurs formulaires de ce type sur une meme page
    global $template_nb_forms;
    global $template_nb_tags;
    // ++$template_nb_tags;
    // ++$template_nb_forms;

?>
    <div style="border: solid black 0px; margin: 0px; padding: 0px; left: 0px; right:0px; ">
<?php 
    if (!$masquer_choix_page) {
?>
        <!-- PAGE -->
        SUR LA PAGE
        <select id="template-id_page-multidel"   name="template-id_page-<?php echo $template_nb_tags; ?>" >
<?php
        $sql    = 'SELECT id_page, page, lien FROM page ORDER BY id_page;';
        $id_res = mysqlquery($sql, __DEBUG__);
        for (; $pages = mysql_fetch_array($id_res); true) {
            echo '<option value="' . $pages['id_page'] . '" ' . ($pages['id_page'] == $id_page ? 'selected="selected"' : '') . ' >' . $pages['page'] . '</option>';
        }
?>
        </select>
<?php

    } else {
?>
        <input type="hidden" name="template-id_page-<?php echo $template_nb_tags; ?>" value="<?php echo $id_page; ?>" />
<?php

    }

    if (!$masquer_choix_langue) {
?>
        <!-- LANGUE -->
        EN LANGUE
        <select id="template-id_langue-multidel"   name="template-id_langue-<?php echo $template_nb_tags; ?>" >
<?php
        $sql    = 'SELECT id_langue, langue FROM langue;';
        $id_res = mysqlquery($sql, __DEBUG__);
        for (; $lang = mysql_fetch_array($id_res); true) {
            echo '<option value="' . $lang['id_langue'] . '" ' . ($lang['id_langue'] == ifSession('int', 'id_langue') ? 'selected="selected"' : '') . ' >' . $lang['langue'] . '</option>';
        }
?>
        </select>
<?php 
    } else {
?>
        <input type="hidden" name="template-id_langue-<?php echo $template_nb_tags; ?>" value="<?php echo ifSession('int', 'id_langue'); ?>" />
<?php

    }

    if (!$masquer_choix_page && !$masquer_choix_page) {
        echo '<br />';
    }

    // print_pre($tab_balises, 'green');
    $str_balises = str_replace('_', '&#95;', implode(';', $tab_balises));

?>

    <input    id="template-tag-multidel"       name="template-tag-multidel"       type="hidden" value="<?php echo $str_balises; ?>" /> 
    <input    id="template-del-multidel"       name="template-del-multidel"       type="checkbox" style="width: auto; " /> __EFFACER__
    <br />
    </div>
<?php
}


/*****
  IMAGES 
 *****/

/**
 * ouvre l'image quel que soit son format : jpg, gif ou png 
 *
 */
function imagecreatefrom_x ($filename) 
{
    $path_parts = pathinfo($filename); 
    $ext = (strtolower($path_parts['extension'])); 
    switch ($ext) {
        case 'jpg' : 
            return imagecreatefromjpeg($filename); 
            break; 
        case 'jpeg' : 
            return imagecreatefromjpeg($filename); 
            break; 
        case 'gif' : 
            return imagecreatefromgif($filename); 
            break; 
        case 'png' : 
            return imagecreatefrompng($filename); 
            break; 
        default : 
            return false; 
            break; 
    }
}

/**
 * redimensionne l'image, et la sauvegarde ou l'affiche 
 *
 */
function img_resize ($filename, $maxwidth = 0, $maxheight = 0, $forcedwidth = 0, $forcedheight = 0, $save_filename = null, $color = "255,255,255") 
{
    if (!extension_loaded('gd')) {
        error_log('Extension PHP GD manquante'); 
    }
    // si save_filename est null on affiche directement le resultat 
    if (null === $save_filename) {
        header('Content-type: image/jpeg');
    }

    // redimensionnement ssi necessaire 
    if ($maxwidth || $maxheight) {

        // Get new dimensions
        list($width_orig, $height_orig) = getimagesize($filename);

        $ratio_orig = $width_orig / $height_orig;

        // recalcule maxwidth en fonction de maxheight si celui ci est specifie
        if ($maxheight && $maxwidth / $maxheight > $ratio_orig) {
            $maxwidth = $maxheight * $ratio_orig;
        } else {
            // sinon recalcule maxheight en fonction de maxwidth 
            // error_log("$maxwidth $maxheight $ratio_orig"); 
            $maxheight = round($maxwidth / $ratio_orig);
        }

        // valeurs par defaut : l'image est mise a la taille de la miniature
        if (!$forcedwidth || !$forcedheight) {
            $forcedwidth = $maxwidth; 
            $forcedheight = $maxheight; 
        }

        // Resample
        $image_p = imagecreatetruecolor($forcedwidth, $forcedheight);
        $color = explode(',', $color, 3); 
        $color_red   = $color[0]; 
        $color_green = $color[1]; 
        $color_blue  = $color[2]; 
        $color   = imagecolorallocate($image_p, $color_red, $color_green, $color_blue); 
        imagefill($image_p, 0, 0, $color); 

        $image = imagecreatefrom_x($filename);
        imagecopyresampled($image_p, $image, 0 - ($maxwidth / 2) + ($forcedwidth / 2), 0 - ($maxheight / 2) + ($forcedheight / 2), 0, 0, $maxwidth, $maxheight, $width_orig, $height_orig);

    } else {
        $image_p = imagecreatefrom_x($filename);
    }

    // Output
    imagejpeg($image_p, $save_filename, 100); // si save_filename est null on affiche directement le resultat 

}

/***
  AJAX
 ****/

/**
 * renvoie vrai si la page a ete appellee en AJAX, faux sinon 
 *
 */
function isajax () 
{ 
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'); 
}







abstract class DocGen 
{

    public $method = array();
    protected $methods = array();
    protected $constants = array();
    protected $properties = array();

    public function __construct($object) 
    {
        $this->className = get_class($object);
        $this->class = new ReflectionClass(get_class($object));
    }

    public function getClassName() 
    {
        return $this->className;
    }

    protected function getClassDefinition() 
    {
        $T = array();
        if ($this->class->isInterface()) {
            $T['interface'] = 'interface';
        }
        if ($this->class->isAbstract()) {
            $T['abstract'] = 'abstract';
        }
        if ($this->class->isFinal()) {
            $T['final'] = 'final';
        }
        return $T;
    }

    protected function getProperties() 
    {
        if (empty($this->properties)) {
            foreach ($this->class->getProperties() as $prop) {
                $this->properties[] = $prop;
                $this->property[$prop->name] = new ReflectionProperty($this->className, $prop->name);
            }
        }
        return $this->properties;
    }

    protected function getPropertyValues($name) 
    {
        $T = array();
        if ($this->property[$name]->isPublic()) {
            $T['visibility'] = 'public';
        } elseif ($this->property[$name]->isPrivate()) {
            $T['visibility'] = 'private';
        } elseif ($this->property[$name]->isProtected()) {
            $T['visibility'] = 'protected';
        } else {
            $T['visibility'] = '';
        }
        if ($this->property[$name]->isStatic()) {
            $T['static'] = 'static';
        }

        return $T;
    }

    protected function getConstants() 
    {
        if (empty($this->constants)) {
            $this->constants = $this->class->getConstants();
        }
        return $this->constants;
    }

    protected function getMethods() 
    {
        if (empty($this->methods)) {
            foreach ($this->class->getMethods() as $method) {
                $this->method[$method->name] = new reflectionMethod($method->class, $method->name);
                $this->methods[] = $method;
            }
        }
        return $this->methods;
    }

    protected function getMethodProperties($methodName) 
    {
        $T = array();
        if ($this->method[$methodName]->isConstructor()) {
            $T['constructor'] = 'Constructeur de la classe';
        }

        // http://bugs.php.net/bug.php?id=32076
        if ($this->method[$methodName]->isDestructor()) {
            /* $T[] = 'Destructeur de la classe'; */
        }

        if ($v = $this->_getMethodVisibility($methodName)) {
            $T['visibility'] = $v;
        }
        if ($this->method[$methodName]->isFinal()) {
            $T['final'] = 'final';
        }
        if ($this->method[$methodName]->isAbstract()) {
            $T['abstract'] = 'abstract';
        }
        if ($this->method[$methodName]->isStatic()) {
            $T['static'] = 'static';
        }

        return $T;
    }

    protected function getMethodParameters($methodName) 
    {
        return $this->method[$methodName]->getParameters();
    }

    private function _getMethodVisibility($methodName) 
    {
        if ($this->method[$methodName]->isPublic()) {
            return 'public';
        } elseif ($this->method[$methodName]->isPrivate()) {
            return 'private';
        } elseif ($this->method[$methodName]->isProtected()) {
            return 'protected';
        }
        return false;
    }

}

class DocGen2XML extends DocGen 
{

    public function toXML() 
    {
        $r = '<class name="';
        foreach ($this->getClassDefinition() as $key => $v) {
            $r .= ' ' . $key . '="1" ';
        }
        $r .= $this->getClassName() . '">';

        // Constantes de la classe
        $C = $this->getConstants();
        if (!empty($C)) {
            $r .= '<constants>';
            foreach ($C as $name => $value) {
                $r .= '<constant name="' . $name . '">';
                $r .= '<value>' . $value . '</value>';
                $r .= '</constant>';
            }
            $r .= '</constants>';
        }

        // Méthodes de la classe
        $M = $this->getMethods();
        $methodes = array('public'    => array(), 
                          'protected' => array(), 
                          'private'   => array()); 
        foreach ($M as $method) {
            $props = $this->getMethodProperties($method->name); 
            $methodes[$props['visibility']][] = $method; 
        }
        sort($methodes['public']);
        sort($methodes['protected']);
        sort($methodes['private']);
        $M = array_merge($methodes['public'], $methodes['protected'], $methodes['private']); 
        if (!empty($M)) {
            $r .= '<methods>';
            foreach ($M as $method) {
                $pValues = $this->getMethodProperties($method->name);
                $visibility = $pValues['visibility'];  
                $r .= '<' . $visibility . '_method name="' . $method->name . '"';
                if (isset($pValues['static'])) {
                    $r .= ' static="1" ';
                }
                if (isset($pValues['final'])) {
                    $r .= ' final="1" ';
                }
                if (isset($pValues['abstract'])) {
                    $r .= ' abstract="1" ';
                }
                $r .= '>';
                $Param = $this->getMethodParameters($method->name);
                if (!empty($Param)) {
                    foreach ($Param as $parameter) {
                        $r .= '<parameter>' . $parameter->name . '</parameter>';
                    }
                }
                $r .= '</' . $visibility . '_method>';
            }
            $r .= '</methods>';
        }

        // Propriétés de la classe
        $P = $this->getProperties();
        if (!empty($P)) {
            $r .= '<properties>';
            foreach ($P as $prop => $Pvalue) {
                if ($Pvalue->class == $this->className) {
                    $PV = $this->getPropertyValues($Pvalue->name);
                    $r .= '<property name="' . $Pvalue->name . '" ';
                    $r .= ' visibility="' . $PV['visibility'] . '" ';
                    if (isset($PV['static'])) {
                        $r .= ' static="1" ';
                    }
                    $r .= '>';    
                    $r .= '</property>';
                }
            }
            $r .= '</properties>';
        }

        $r .= '</class>';
        return $r;
    }
}

?>
