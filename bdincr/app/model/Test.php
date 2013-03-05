<?php

require_once __MODEL_DIR__ . '/Database.php'; 

/**
 * Teste la classe Database
 */
class Test extends Database
{

    public $id = 0;
    public $nom = '';
    public $prenom = '';
    public $login = '';
    public $pass = '';

        /*
        private $schema = '
DROP TABLE IF EXISTS test; 
CREATE TABLE IF NOT EXISTS test        (id                       INT NOT NULL DEFAULT 0,
                                        champ                    VARCHAR(128),
                                        valeur                   VARCHAR(128),
                                        date_enregistrement      TIMESTAMP,
                                        supprime                 TINYINT(1) NOT NULL DEFAULT 0); ';
         */

}

