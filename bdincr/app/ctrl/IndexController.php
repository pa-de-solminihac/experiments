<?php

require_once (__MODEL_DIR__ . '/Initialisation.php');

/**
 * IndexController : controleur de la page d'index
 * 
 * @uses Initialisation
 * @package 
 * @version $id$
 * @copyright 
 * @author Pierre-Alexis <pa@quai13.com> 
 * @license 
 */
class IndexController extends Initialisation
{

    /**
     * indexAction : page d'accueil du site
     * 
     * @access public
     * @return void
     */
    public function indexAction ()
    {
        $this->view['message'] = 'message de l\'index';

        require_once __MODEL_DIR__ . '/Test.php';
        $toto = new Test();

            /*
            $toto->id = '6';
            $toto->nom = 'Testeur';
            $toto->prenom = 'Toto';
            $toto->login = 'ttest';
            $toto->pass = 'monpass';
            $toto->save();
             */

        echo '<h1>load</h1>';
        $test = $toto->load(5);
        echo '<pre>';
        print_r($test);
        echo '</pre>';

            /*
            echo '<h1>delete</h1>';
            $toto->delete(6);
            $test = $toto->load(6);
            echo '<pre>';
            print_r($test);
            echo '</pre>';

            echo '<h1>undelete</h1>';
            $toto->undelete(6);
            $test = $toto->load(6);
            echo '<pre>';
            print_r($test);
            echo '</pre>';
             */

    }

}

?>
