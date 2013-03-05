<?php

    // CONFIGURATION DE BASE : URL ET BASE DE DONNES

    if (!defined('__DEBUG__')) {
        // mode debug : activé par défaut, à désactiver en prod
        define('__DEBUG__',               '1');
    }
    if (!defined('__BASE_URL__')) {
        define('__BASE_URL__',            '/automvc');
    }
    if (!defined('__MYSQL_HOST__')) {
        define('__MYSQL_HOST__',          'localhost');
    }
    if (!defined('__MYSQL_USERNAME__')) {
        define('__MYSQL_USERNAME__',      '');
    }
    if (!defined('__MYSQL_PASSWORD__')) {
        define('__MYSQL_PASSWORD__',      '');
    }
    if (!defined('__MYSQL_DBNAME__')) {
        define('__MYSQL_DBNAME__',        '');
    }
    if (!defined('__EMAIL_PROD__')) {
        define('__EMAIL_PROD__', 'prod@quai13.com');
    }
    if (!defined('__EMAIL_DEV__')) {
        define('__EMAIL_DEV__', 'prod@quai13.com');
    }

    // CONFIGURATION AVANCEE

    // gestion des langues disponibles

    if (!defined('__LANG_DISPOS__')) {
        // format : 'fr|en|de'
        define('__LANG_DISPOS__', 'fr');
    }

    // gestion de l'encodage

    if (!defined('__PHP_ENCODING__')) {
        define('__PHP_ENCODING__',        'UTF8');
    }
    if (!defined('__HTML_ENCODING__')) {
        define('__HTML_ENCODING__',       'utf-8');
    }
    if (!defined('__SQL_ENCODING__')) {
        define('__SQL_ENCODING__',        'utf8');
    }

    // os et chemins de base

    if (!defined('__OS__')) {
        define('__OS__',                  'linux');
    } // 'linux' ou 'win'
    if (!defined('__FILES_ROOT__')) {
        define('__FILES_ROOT__',          $_SERVER['DOCUMENT_ROOT'] . __BASE_URL__);
    }
    if (!defined('__WWW_ROOT__')) {
        define('__WWW_ROOT__',            'http://' . $_SERVER['SERVER_NAME'] . __BASE_URL__);
    }

    // gestion de la release : afin de mieux controler les problemes de cache sur les postes clients.

    if (!defined('__SVN__')) {
        if (is_dir(__FILES_ROOT__ . "/.svn") && is_file(__FILES_ROOT__ . "/.svn/entries")) {
            $automvc_svn = file(__FILES_ROOT__ . "/.svn/entries");
            $automvc_svn = (int) $automvc_svn[3];    // le numero de revision se trouve sur la 4e ligne
            define('__SVN__', $automvc_svn);
            unset($automvc_svn);
        }
    }

    // chemins

    if (!defined('__CTRL_DIR__')) {
        define('__CTRL_DIR__',            __FILES_ROOT__ . '/app/ctrl');
    }
    if (!defined('__MODEL_DIR__')) {
        define('__MODEL_DIR__',           __FILES_ROOT__ . '/app/model');
    }
    if (!defined('__VIEW_DIR__')) {
        define('__VIEW_DIR__',            __FILES_ROOT__ . '/app/views');
    }
    if (!defined('__TMPL_DIR__')) {
        define('__TMPL_DIR__',            __FILES_ROOT__ . '/app/tmpl');
    }

    require_once 'configure.php';
    require_once 'generiques/generic_ns.php';

?>
