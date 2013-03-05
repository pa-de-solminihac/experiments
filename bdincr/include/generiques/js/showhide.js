/* {{{ FONCTIONS SHOW/HIDE */

/* show/hide un element du DOM, et change la source d'une image en fonction de l'état caché/affiché */
function showhide (eltid, eltimg, img_reduire, img_restaurer, path, forceshow, visible) {
    if (!img_reduire) img_reduire = 'img/reduire.png';
    if (!img_restaurer) img_restaurer = 'img/restaurer.png';

    if (!visible) {
        oldval = document.getElementById(eltid).style.display;
        newval = (oldval == 'none') ? 'block' : 'none';
        if ('block' == forceshow) newval = 'block';
        if ('none'  == forceshow) newval = 'none';
        newimg = (newval == 'block') ? path+img_reduire : path+img_restaurer;
        if (eltimg) eltimg.src= newimg;
         zindex = (newval == 'block') ? '7' : '1';
        document.getElementById(eltid).style.display = 'none';
        document.getElementById(eltid).style.zIndex = zindex;
        document.getElementById(eltid).style.display = newval;
        // if ('block' == forceshow) fade_element('tableau_error_handler');
    }
    else {
        oldval = document.getElementById(eltid).style.visibility;
        newval = (oldval == 'hidden') ? 'visible' : 'hidden';
        if ('visible' == forceshow) newval = 'visible';
        if ('hidden'  == forceshow) newval = 'hidden';
        newimg = (newval == 'visible') ? path+img_reduire : path+img_restaurer;
        if (eltimg) eltimg.src= newimg;
        zindex = (newval == 'visible') ? '7' : '1';
        document.getElementById(eltid).style.visibility = 'hidden';
        document.getElementById(eltid).style.zIndex = zindex;
        document.getElementById(eltid).style.visibility = newval;
        // if ('block' == forceshow) fade_element('tableau_error_handler');
    }
}

/* open (display:'block') un element du DOM */
function opendiv (eltid) { if (document.getElementById(eltid)) document.getElementById(eltid).style.display = 'block'; }

/* close (display:'none') un element du DOM */
function closediv (eltid) { document.getElementById(eltid).style.display = 'none'; }

/* vide (innerHTML = '') un element du DOM */
function empty_div (eltid) { document.getElementById(eltid).innerHTML = ''; }

/* }}} */
