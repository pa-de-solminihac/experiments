<?php

/**
 * Gère les contenus du site
 */
abstract class Database 
{

    /**
     * Sauvegarde l'objet dans la base de données
     */
    function save ()
    {
        $table = strtolower(get_class($this));
        $champs = get_class_vars(get_class($this));
        unset($champs['id']);
        unset($champs['champ']);
        unset($champs['valeur']);
        unset($champs['date_enregistrement']);
        unset($champs['supprime']);

        global $db;
        $sql  = "LOCK TABLES $table WRITE ";
        $stmt = mysqlquery($sql, $db);

        // autoincrement
        if (!$this->id) {
            $sql = "SELECT MAX(id) id FROM $table";
            $stmt = mysqlquery($sql, $db);
            $res = mysql_fetch_array($stmt);
            $this->id = 1 + (int) $res['id'];
        }

        // requetes d'insertion ou modification : on enregistre juste une release pour l'enregistrement
        $now = date('Y-m-d H:i:s');
        foreach ($champs as $key => $val) {
            $sql = "INSERT INTO $table (`id`, `date_enregistrement`, `supprime`, `champ`, `valeur`)
                VALUES ('" . $this->id . "', '" . $now . "', '0', '" . $key . "', '" . mysql_real_escape_string($this->$key) . "')";
            // echo $sql . '<br />';
            $stmt = mysqlquery($sql, $db);
        }

        $sql  = "UNLOCK TABLES "; 
        $stmt = mysqlquery($sql, $db); 
        return $this;
    }

    /**
     * Charge la dernière release de l'objet depuis la base de données
     */
    function load ($id = null)
    {
        if ($id === null) {
            $id = $this->id;
        }
        $table = strtolower(get_class($this));

        $sql = "SELECT MAX(date_enregistrement) date_enregistrement FROM $table WHERE `id` = '" . $id . "'";
        $stmt = mysqlquery($sql, $db);
        $res = mysql_fetch_array($stmt);

        if (strlen($res['date_enregistrement'])) {
            $sql = "SELECT champ, valeur, supprime FROM $table WHERE `id` = '" . $id . "' 
                AND `date_enregistrement` = '" . $res['date_enregistrement'] . "'";
            $stmt = mysqlquery($sql, $db);
            for (; $res = mysql_fetch_array($stmt); true) {
                if ($res['supprime']) {
                    return false;
                }
                $this->$res['champ'] = $res['valeur'];
            }
            $this->id = $id;
            return $this;
        } 
        return false;
    }

    /**
     * Marque la dernière release de l'objet comme effacée dans la base de données
     */
    function delete ($id = null)
    {
        if ($id === null) {
            $id = $this->id;
        }
        $table = strtolower(get_class($this));

        // recupere la derniere date_enregistrement 
        $sql = "SELECT MAX(date_enregistrement) date_enregistrement FROM $table WHERE `id` = '" . $id . "'";
        $stmt = mysqlquery($sql, $db);
        $res = mysql_fetch_array($stmt);

        // marquage comme efface
        $sql = "UPDATE $table SET supprime = '1', date_enregistrement = '" . $res['date_enregistrement'] . "' WHERE `id` = '" . $id . "' AND date_enregistrement = '" . $res['date_enregistrement'] . "'";
        $stmt = mysqlquery($sql, $db);
    }

    /**
     * Annule le marquage de la dernière release de l'objet comme effacée dans la base de données
     */
    function undelete ($id = null)
    {
        if ($id === null) {
            $id = $this->id;
        }
        $table = strtolower(get_class($this));

        // recupere la derniere date_enregistrement 
        $sql = "SELECT MAX(date_enregistrement) date_enregistrement FROM $table WHERE `id` = '" . $id . "'";
        $stmt = mysqlquery($sql, $db);
        $res = mysql_fetch_array($stmt);

        // marquage comme non efface
        $sql = "UPDATE $table SET supprime = '0', date_enregistrement = '" . $res['date_enregistrement'] . "' WHERE `id` = '" . $id . "' AND date_enregistrement = '" . $res['date_enregistrement'] . "'";
        $stmt = mysqlquery($sql, $db);
    }

}
