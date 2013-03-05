/* {{{ FONCTIONS FLASH */

/* ecrire le code HTML pour integrer un flash, et contourne la restriction IE, en conservant la validite XHTML */
function integrer_flash (largeur, hauteur, chemin, title) {
    document.write("<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,19,0' width='"+largeur+"' height='"+hauteur+"' title='"+title+"'>\n");
    document.write("<param name='movie' value='"+chemin+"' />\n");
    document.write("<param name='quality' value='high' />\n");
    document.write("<embed src='"+chemin+"' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' width='"+largeur+"' height='"+hauteur+"'></embed> \n");
    document.write("</object>\n");
}

/* }}} */
