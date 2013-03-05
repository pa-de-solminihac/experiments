<?php
if (!defined('__EMAIL_DEV__')) { 
    define('__EMAIL_DEV__', 'pa@quai13.com'); 
}
header('Content-type: text/html; charset="utf-8"');
?>
<h1>Prérequis</h1>
<h2>Courriel</h2>
<?php
if (!mail(__EMAIL_DEV__, __EMAIL_DEV__,__EMAIL_DEV__)) {
    echo '<strong style="color:red">La fonction mail() ne fonctionne pas (impossible d\'envoyer un courriel à ' . __EMAIL_DEV__ . ').</strong>'; 
} else {
    echo 'Le courriel est bien parti'; 
} 
?>
<hr />
<h2>Lecture de fichiers</h2>
<?php 
$d = opendir('/tmp'); 
$dir = array(); 
for ($i = 0; (false !== ($fich = readdir($d))) && $i < 10; ++$i) {
    $dir[] = $fich; 
}
if (!($nb = count($dir))) {
    echo 'Impossible de lister le contenu d\'un répertoire avec les fonctions PHP (les fonctions opendir() et readdir() ne fonctionnent pas)'; 
} else {
    echo 'Le répertoire /tmp a bien été lu (il contient ' . $nb . ' éléments)'; 
}
?>
<hr />
<h2>Exécution de commandes système</h2>
<?php
if (!strlen($str = exec('/bin/ls -al /tmp'))) {
    echo 'Impossible de lister le contenu d\'un répertoire avec une commande système (la fonction exec() ne fonctionne pas)'; 
} else {
    echo 'Le répertoire /tmp a bien été lu'; 
}
?>
<hr />
<h2>Lib GD</h2>
<?php 
if (!extension_loaded('gd')) {
    if (!@dl('gd.so')) {
        echo '<strong style="color:red">La librairie GD n\'est pas chargée (ni chargeable)</strong>'; 
    } else {
        echo '<strong>La librairie GD a du être chargée explicitement</strong>'; 
    }
} else {
    echo 'La librairie GD est bien chargée'; 
}
?>
<hr />
<h2>$_SERVER[]</h2> 
<pre style="text-align: left; ">
<?php 
    print_r($_SERVER); 
?>
</pre>
<hr />
<h2>phpinfo()</h2> 
<?php
phpinfo(); 
?>
