<?php

    // Décompose la requête en elements et crée le tableau $automvc_request correspondant...
    global $automvc_request;
    $automvc_request             = Array ();
    $automvc_request['URI']      = substr($_SERVER['REQUEST_URI'], (strlen(__BASE_URL__) + 1));
    $automvc_request['BASE_URI'] = preg_replace('/[\/\?#].*/', '', $automvc_request['URI']);

    // Gestion de l'url rewriting : simpliste pour l'instant, a ameliorer avec des expressions regulieres
    require_once __FILES_ROOT__ . '/include/url_rewriting.php';
    if (isset($automvc_url_rewriting[$automvc_request['BASE_URI']])) {
        $automvc_request['URI']     = $automvc_url_rewriting[$automvc_request['BASE_URI']];
    }

    $args_pos           = strpos($automvc_request['URI'], '?');
    $automvc_request['PAGE']    = $args_pos ? substr($automvc_request['URI'], 0, $args_pos) : $automvc_request['URI'];
    $automvc_request_tmp        = explode('/', $automvc_request['PAGE']);

    $lang_dispos = explode('|', __LANG_DISPOS__);
    if (!defined('__DEFAULT_LANG__')) {
        define('__DEFAULT_LANG__', $lang_dispos[0]);
    }

    if (!defined('__LANG__')) {

        if (strtolower(trim((isset($automvc_request_tmp[0]) && strlen(trim($automvc_request_tmp[0]))))) && in_array(trim($automvc_request_tmp[0]), $lang_dispos)) {
            $automvc_request['LANG'] = trim($automvc_request_tmp[0]);
        } else {
            $automvc_request['LANG'] = __DEFAULT_LANG__;
            if (__LANG_DISPOS__ != __DEFAULT_LANG__) {
                if (!in_array(trim($automvc_request_tmp[0]), $lang_dispos)) {
                    $automvc_request['CTRL'] = 'Error';
                    $automvc_request['ACT'] = 'error';
                    /*
                    $automvc_request['PAGE'] = substr($automvc_request['PAGE'], strpos($automvc_request['PAGE'], '/') + 1);
                    $automvc_request['URI'] = substr($automvc_request['URI'], strpos($automvc_request['URI'], '/') + 1);
                    */
                }
            }
        }
        if (isset($_GET['lang']) && in_array($_GET['lang'], $lang_dispos)) {
            define('__LANG__', $_GET['lang']);
        } elseif (in_array($automvc_request['LANG'], $lang_dispos)) {
            define('__LANG__', $automvc_request['LANG']);
        } else {
            define('__LANG__', $lang_dispos[0]);
        }

    }

    if (!(isset($automvc_request['CTRL']) && $automvc_request['CTRL'] == 'Error' && isset($automvc_request['ACT']) && $automvc_request['ACT'] == 'error')) {
        if (__LANG_DISPOS__ == __DEFAULT_LANG__) {
            // une seule langue dispo, pas de langue dans l'URL
            $automvc_request['CTRL']    = ucfirst(strtolower(trim((isset($automvc_request_tmp[0]) && strlen(trim($automvc_request_tmp[0]))) ? trim($automvc_request_tmp[0]) : '')));
            $automvc_request['ACT']     = strtolower(trim((isset($automvc_request_tmp[1]) && strlen(trim($automvc_request_tmp[1]))) ? trim($automvc_request_tmp[1]) : ''));
            $automvc_request['LANG']    = __DEFAULT_LANG__;
        } else {
            // si plusieurs langues dispo, la langue doit etre dans l'URL
            $automvc_request['CTRL']    = ucfirst(strtolower(trim((isset($automvc_request_tmp[1]) && strlen(trim($automvc_request_tmp[1]))) ? trim($automvc_request_tmp[1]) : '')));
            $automvc_request['ACT']     = strtolower(trim((isset($automvc_request_tmp[2]) && strlen(trim($automvc_request_tmp[2]))) ? trim($automvc_request_tmp[2]) : ''));
        }
    }

    if (!strlen($automvc_request['CTRL'])) {
        $automvc_request['CTRL'] = 'Index';
    }
    if (!strlen($automvc_request['ACT'])) {
        $automvc_request['ACT'] = 'index';
    }

    if (!defined('__BASE_LANG_URL__')) {
        define('__BASE_LANG_URL__',            __BASE_URL__ . '/' . __LANG__);
    }
    if (!defined('__WWW_LANG_ROOT__')) {
        define('__WWW_LANG_ROOT__',            __WWW_ROOT__ . '/' . __LANG__);
    }

    // A partir de l'URL on va inclure les bons fichiers (ctrl), appeler la bonne fonction, puis afficher la bonne vue
    if (!file_exists(__CTRL_DIR__ . '/' . $automvc_request['CTRL'] . 'Controller.php')) {
        $automvc_request['CTRL'] = 'Error';
        $automvc_request['ACT'] = 'error';
    }

    require_once __CTRL_DIR__ . '/' . 'ErrorController.php';   // CONTROLEUR D'ERREURS
    require_once __CTRL_DIR__ . '/' . $automvc_request['CTRL'] . 'Controller.php';   // CONTROLEUR
    $appel_classe = $automvc_request['CTRL'] . 'Controller';
    $appel_fonction = $automvc_request['ACT'] . 'Action';
    $obj = new $appel_classe();

    // si l'action n'existe pas et que la vue non plus, erreur 404
    if (!method_exists($obj, $appel_fonction) && !file_exists(__VIEW_DIR__ . '/' . strtolower($automvc_request['CTRL']) . '/' . strtolower($automvc_request['ACT']) . '.phtml')) {
        $automvc_request['CTRL'] = 'Error';
        $automvc_request['ACT'] = 'error';
    }

    // création de la vue sous la forme d'un tableau
    $obj->view = Array();
    if (method_exists($obj, 'init')) {
        $obj->init();
    }
    if (method_exists($obj, 'init_suite')) {
        $obj->init_suite();
    }
    // on n'appelle le controleur que s'il existe. Sinon on affiche simplement la vue...
    if (method_exists($obj, $appel_fonction)) {
        $obj->$appel_fonction();
    }
    $view = $obj->view;
    // n'affiche le template de la vue que s'il existe
    @include __TMPL_DIR__ . '/' . strtolower($automvc_request['CTRL']) . '/' . strtolower($automvc_request['ACT']) . '.phtml';
?>
