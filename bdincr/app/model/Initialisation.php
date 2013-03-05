<?php

/**
 * Initialisation : classe dont héritent les autres classes
 * 
 * @package 
 * @version $id$
 * @copyright 
 * @author Pierre-Alexis <pa@quai13.com> 
 * @license 
 */
class Initialisation 
{

    /**
     * init : gère les valeurs de base, le login, etc... 
     * 
     * @access public
     * @return void
     */
    function init() 
    {
        if ($auth = Initialisation::getAuth()) {
            $this->view['login']  = isset($auth['login']) ? $auth['login'] : ''; 
            $this->view['groupe'] = isset($auth['groupe']) ? $auth['groupe'] : ''; 
        } else {
            $this->view['login']  = ''; 
            $this->view['groupe'] = ''; 
        }
        // Si le referer est bien sur le site même, il peut servir d'url retour 
        if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['HTTP_REFERER']) && preg_match('/^http:\/\/' . $_SERVER['HTTP_HOST'] . '/', $_SERVER['HTTP_REFERER'])) {
            $this->view['url_page_prec'] = $_SERVER['HTTP_REFERER']; 
        } else {
            // sinon, url_page_prec pointe vers l'accueil 
            $this->view['url_page_prec'] = __WWW_LANG_ROOT__; 
        }
    }

    /**
     * getAuth : renvoie les informations sur le login si l'utilisateur est loggue, faux sinon
     * 
     * @access public
     * @return void
     */
    function getAuth() 
    {
        $auth = isset($_SESSION['Default']['auth']) ? $_SESSION['Default']['auth'] : ''; 
        if (isset($auth['login']) && strlen($auth['login'])) {
            return $auth; 
        } else {
            return false; 
        }
    }

    /**
     * getUrlAuth : renvoie l'url pour se logguer
     * 
     * @access public
     * @return void
     */
    function getUrlAuth() 
    {
        return __WWW_LANG_ROOT__ . '/authentification?url_retour=' . urlencode($_SERVER['REQUEST_URI']);
    }

    /**
     * needAuth : renvoie vers la page de login si pas loggue
     * 
     * @param mixed $groupes_autorises 
     * @access public
     * @return void
     */
    function needAuth($groupes_autorises = null) 
    {
        if (!is_array($groupes_autorises)) {
            if (strlen($groupes_autorises)) {
                $groupes_autorises = array($groupes_autorises); 
            } else {
                $groupes_autorises = array(); 
            }
        }

        $auth = Initialisation::getAuth(); 

        // si l'utilisateur n'est pas loggue
        if (!$auth['login']) {
            redirection(__WWW_LANG_ROOT__ . '/authentification?url_retour=' . urlencode($_SERVER['REQUEST_URI']));
        }
        // si l'utilisateur n'a pas les droits
        if ($auth['login'] && $auth['groupe'] && count($groupes_autorises) && !in_array($auth['groupe'], $groupes_autorises)) {
            redirection(__WWW_LANG_ROOT__ . '/authentification?url_retour=' . urlencode($_SERVER['REQUEST_URI']));
        }
    }

    /**
     * traduire : traduit un texte dans la langue en cours en recuperant la traduction dans la table traduction
     * 
     * @param mixed $text 
     * @access public
     * @return void
     */
    function traduire($text) 
    {
        global $db;
        $sql  = 'SELECT texte FROM traduction '; 
        $sql .= 'WHERE lang = \'' . __LANG__ . '\' '; 
        $sql .= 'AND str = \'' . mysql_real_escape_string($text) . '\' '; 
        $sql .= 'LIMIT 1'; 
        $stmt = mysqlquery($sql, $db); 
        if (!mysql_num_rows($stmt)) {
            $sql  = 'SELECT texte FROM traduction '; 
            $sql .= 'WHERE lang = \'' . __DEFAULT_LANG__ . '\' '; 
            $sql .= 'AND str = \'' . mysql_real_escape_string($text) . '\' '; 
            $sql .= 'LIMIT 1'; 
            $stmt = mysqlquery($sql, $db); 
            if (!mysql_num_rows($stmt)) {
                return $text; 
            }
        }
        $traduction = mysql_fetch_array($stmt); 
        return $traduction['texte'];
    }

    /**
     * traduire_contenu : traduit un contenu dans la langue en cours en recuperant la traduction dans la table traduction_contenu
     * 
     * @param mixed $orig_table 
     * @param mixed $orig_field 
     * @param mixed $orig_id 
     * @access public
     * @return void
     */
    function traduire_contenu($orig_table, $orig_field, $orig_id) 
    {
        global $db;
        $sql  = 'SELECT texte FROM traduction_contenu '; 
        $sql .= 'WHERE lang = \'' . __LANG__ . '\' '; 
        $sql .= 'AND orig_table = \'' . $orig_table . '\' '; 
        $sql .= 'AND orig_field = \'' . $orig_field . '\' '; 
        $sql .= 'AND orig_id = \'' . $orig_id . '\' '; 
        $sql .= 'LIMIT 1'; 
        $stmt = mysqlquery($sql, $db); 
        $traduction = mysql_fetch_array($stmt); 
        if (mysql_num_rows($stmt)) {
            return $traduction['texte'];
        } else {
            return 'NULL';
        }
    }

}

?>
