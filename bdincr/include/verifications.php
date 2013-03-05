<?php
    // tableau associatif : fichier qui doit exister => message d'information sur la facon de le configurer s'il n'existe pas
    $verifications = Array ("include/settings.php"  => "remplir au moins la section CONFIGURATION DE BASE");
    
    // on vérifie que tous les fichiers existent
    $fichiers = array_keys($verifications);
    $fichiers_manquants = Array ();
    foreach ($fichiers as $fichier) {
        if (!file_exists($fichier)) {   
            $fichiers_manquants[] = $fichier;
        }
    }

    // affichage du message d'erreur avec les fichiers a configurer
    if (count($fichiers_manquants)) {
        sort($fichiers_manquants);
        echo '<h1>Installation</h1>';
        echo '<em>Les fichiers suivants doivent etre configures en se basant sur les fichiers sample_* :</em>';
        echo '<pre>';
        foreach ($fichiers_manquants as $fichier_manquant) {
            echo $fichier_manquant . " : " . "<em>" . $verifications[$fichier_manquant] . "</em>\n";
        }
        echo '</pre>';
        die();
    }
?>
