<?php
require_once __MODEL_DIR__ . '/Initialisation.php'; 

/**
 * ErrorController : controleur d'erreurs HTTP
 * 
 * @uses Initialisation
 * @package 
 * @version $id$
 * @copyright 
 * @author Pierre-Alexis <pa@quai13.com> 
 * @license 
 */
class ErrorController extends Initialisation 
{

    /**
     * errorAction : gere les erreurs HTTP et fait les redirections 301 en cas de site multilingue
     * 
     * @access public
     * @return void
     */
    function errorAction() 
    {
        global $automvc_request;

        // verifie si c'est bien une erreur 404 ou si on a simplement omis le code langue, auquel cas on redirige automatiquement
        // si mode multilangue et qu'on ne trouve pas le code langue dans l'URL
        if ((__LANG_DISPOS__ != __DEFAULT_LANG__) && (!preg_match("@" . __BASE_URL__ . "/(" . __LANG_DISPOS__ . ")/.*@", $_SERVER['REQUEST_URI']))) {
            // langue non specifiee : on redirige vers la langue par defaut
            $url = __WWW_LANG_ROOT__ . '/' . $automvc_request['URI']; 
            redirection($url, 301);
        } else {
            // erreur 404 
            header("HTTP/1.0 404 Not Found"); 
        }
    }

}

?>
