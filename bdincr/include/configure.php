<?php

    // modifications automatique du comportement de PHP selon la configuration choisie

    if (__DEBUG__) {
        ini_set('display_errors',  'on');
        error_reporting(E_ALL);
        require_once __FILES_ROOT__ . '/externals/FirePHPCore-0.3/lib/FirePHPCore/fb.php';
    } else {
        // la fonction fb de firebug ne doit (presque) rien faire en production...
        function fb($message = null, $titre = null) {
            if (isset($titre) && $titre !== null) {
                error_log($titre . ' : ' . $message);
            } elseif (isset($message) && $message !== null) {
                error_log($message);
            }
            return false;
        }
    }

    switch (__OS__) {
        case 'win' :
            $separateur = ';';
            break;
        default :
            $separateur = ':';
            break;
    }

    set_include_path('.' . $separateur . __MODEL_DIR__ . $separateur . get_include_path());

    // Teste la présence de la librairie GD !
    if (!extension_loaded('gd')) {
        if (!@dl('gd.so')) {
            if (!defined('__ACTIVE_GD__')) {
                define('__ACTIVE_GD__', '0');
            }
        } else {
            if (!defined('__ACTIVE_GD__')) {
                define('__ACTIVE_GD__', '1');
            }
        }
    } else {
        if (!defined('__ACTIVE_GD__')) {
            define('__ACTIVE_GD__', '1');
        }
    }

    // Session
    session_start(); 
    if (!isset($_SESSION['Default'])) {
        $_SESSION['Default'] = array(); 
    }

    // Connexion et selection de la BD si nécessaire
    global $db; 
    if (strlen(__MYSQL_USERNAME__)) {
        $db = mysql_connect(__MYSQL_HOST__, __MYSQL_USERNAME__, __MYSQL_PASSWORD__); 
        if (!$db) {
            if (__DEBUG__) {
                die('La connexion à la base de données à échoué.');  
            } else {
                die();
            }
        }
        mysql_query('USE ' . __MYSQL_DBNAME__, $db); // compatibilite PHP4
        mysql_select_db(__MYSQL_DBNAME__); 
        mysql_query('SET NAMES ' . __SQL_ENCODING__, $db); // compatibilité UTF8
        mysql_query('SET CHARACTER_SET ' . __SQL_ENCODING__, $db); // compatibilité UTF8
    }

    // Localisation de PHP 
    setlocale(LC_ALL, 'fr_FR.' . __PHP_ENCODING__); 
    mb_internal_encoding(__PHP_ENCODING__); 

    // Force l'encodage du site 
    header('Content-type: text/html; charset="' . __HTML_ENCODING__ . '"');

?>
