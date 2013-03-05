/* {{{ FONCTIONS PRATIQUES POUR FORMULAIRES */

/* renvoie l'objet du DOM coche (dans le cas d'un bouton radio), dans un formulaire donne eventuellement */
function getCheckedElementByTagName(tagname, form) {
    if (form) {
        var obj     = document.forms[form].elements[tagname];
    }
    else {
        var obj     = document.getElementsByName(tagname);
    }

    var lg      = obj.length;
    var valeur  = false;
    var found   = false;
    var i       = 0;

    for (; i < obj.length; ++i) {
        if (obj[i].checked) {
            found  = true;
            valeur = obj[i].value;
            break;
        }
    }
    if (found) return obj[i];
    else return false;
}

/* vide un input type=text quand on "clique" dessus ssi il est initialise a sa valeur par defaut */
function vide_onevent(event,id,name,val) {
  var cur_val;
  cur_val = document.getElementById(id).value;
  
  switch (event) {
    case 'focus': 
      if (cur_val == name) {
        document.getElementById(id).value = val;
      }
      break;
    case 'blur': 
      if (!cur_val.length || cur_val == name) {
        document.getElementById(id).value = name;
      }
      break;
  }
}

/* demande une confirmation avant de rediriger vers une url */
function if_confirm (url, message, blank) {
  if (confirm(message)) { document.location = url; }
}

/* compare 2 champs et met a jour un div selon qu'ils sont identiques ou non. pratique en onkeyup, onblur et onclick pour pass et pass_confirm */
function compare_fields (field1, field2, id_span_ok, inner_no, inner_yes, min_length) {
    val1 = document.getElementById(field1).value;
    val2 = document.getElementById(field2).value;
    if (val1 == val2 && (!min_length || (val1.length >= min_length))) {
        document.getElementById(id_span_ok).innerHTML = inner_yes;
        return 1;
    }
    else {
        document.getElementById(id_span_ok).innerHTML = inner_no;
        return 0;
    }
}

/* valide le format d'une adresse email */
function valide_email (email) {
    reg = /^\s*[a-z0-9\._-]+@([a-z0-9-]+\.)+[a-z0-9]+\s*$/i;
    return email.match(reg);
}

/* valide le format d'une adresse email avec 2e champ pour confirmation et met a jour un div selon qu'ils sont identiques ou non. pratique en onkeyup, onblur et onclick pour les email et email_confirm */
function valide_email_confirm (field1, field2, id_span_ok, inner_no, inner_yes, min_length, icase) {
    val1 = document.getElementById(field1).value;
    val2 = document.getElementById(field2).value;

    if (icase) {
        val1 = val1.toLowerCase();
        val2 = val2.toLowerCase();
    }

    if (val1 == val2 && (!min_length || (val1.length >= min_length)) && valide_email(val1) && valide_email(val2)) {
        document.getElementById(id_span_ok).innerHTML = inner_yes;
        return 1;
    }
    else {
        document.getElementById(id_span_ok).innerHTML = inner_no;
        return 0;
    }
}

/* valide les caractères d'un nombre, avec taille min */
function valide_num (field, id_span_ok, inner_no, inner_yes, min_length, max_length) {
    reg = /^[0-9]+$/i;
    return valide_champ(field, '', id_span_ok, inner_no, inner_yes, min_length, max_length, reg);
}

/* valide les caractères d'un login, avec taille min */
function valide_login (field, id_span_ok, inner_no, inner_yes, min_length, max_length) {
    reg = /^[a-z0-9@\._-]+$/i;
    return valide_champ(field, '', id_span_ok, inner_no, inner_yes, min_length, max_length, reg);
}

/* valide les caractères d'une chaine alphanumerique, avec taille min */
function valide_alnum (field, id_span_ok, inner_no, inner_yes, min_length, max_length) {
    reg = /^[a-z0-9]+$/i;
    return valide_champ(field, '', id_span_ok, inner_no, inner_yes, min_length, max_length, reg);
}

/* valide les caractères d'un numero de telephone, avec taille min */
function valide_tel (field, id_span_ok, inner_no, inner_yes, min_length, max_length) {
    reg = /^[0-9()+ \.-]+$/i;
    return valide_champ(field, '', id_span_ok, inner_no, inner_yes, min_length, max_length, reg);
}

/* valide un code_postal */
function valide_cp (field, id_span_ok, inner_no, inner_yes) {
    reg = /^[a-z0-9]+$/i;
    return valide_champ(field, '', id_span_ok, inner_no, inner_yes, 5, 5, reg);
}

/* verifie que la valeur du champ field correspond a l'une des valeurs du tableau liste */
function value_is_in (field, liste, id_span_ok, inner_no, inner_yes, min_length, max_length) {
    var obj = document.getElementsByName(field);
    var lg = obj.length;
    var valeur;
    for (i = 0; i < obj.length; ++i) {
        if (obj[i].checked) {
            valeur = obj[i].value;
            break;
        }
    }

    if (liste.in_array(valeur)) {
        document.getElementById(id_span_ok).innerHTML = inner_yes;
        return 1;
    }
    else {
        document.getElementById(id_span_ok).innerHTML = inner_no;
        return 0;
    }
}

/* valide si un champ est rempli, avec taille min, taille max, et expreg de validation des caracteres autorises */
function valide_champ (field, form, id_span_ok, inner_no, inner_yes, min_length, max_length, reg) {
    // valeurs par defaut
    if (!id_span_ok) id_span_ok = field+'_ok';
    if (!inner_no)   inner_no  = '*';
    if (!inner_yes)  inner_yes = ' ';
    if (!min_length) min_length = 1;

    if (is_array(field)) { // valide_champ renvoie vrai si et seulement si au moins un element du tableau est ok
        valide = 0;
        cnt = field.length;
        for (i=0; i < cnt; ++i) {
            valide += parseInt(valide_champ(field[i], form, id_span_ok, inner_no, inner_yes, min_length, max_length, reg));
        }

        if (valide) {
            document.getElementById(id_span_ok).innerHTML = inner_yes;
            return 1;
        }
        else {
            document.getElementById(id_span_ok).innerHTML = inner_no;
            return 0;
        }
    }

    retour = 1;
    is_radio = 0;
    
    champ = '';

    // on essaye d'abord de recuperer la valeur par le name dans un formulaire donne, eventuellement
    obj = getCheckedElementByTagName(field, form);
    if (obj) {
        champ = obj.value;
        is_radio = 1;
    }
    else { // sinon on recupere par l'id
        obj = document.getElementById(field);
        if (obj) champ = obj.value;
    }

    champ = trim(champ);

    min_length = parseInt (min_length);
    max_length = parseInt (max_length);

    // teste la taille
    if ((max_length && (champ.length > max_length))
     || (min_length && (champ.length < min_length))) {
        retour = 0;
    }

    // evite que la fonction retourne vrai quand on n'a pas selectionne d'element pour des boutons radio. Ne fonctionne pas sous IE...
    if (is_radio && champ == false) retour = 0;

    // teste la conformite a l'expreg
    if (retour && (reg && champ.match(reg) || !reg)) {
        document.getElementById(id_span_ok).innerHTML = inner_yes;
        return 1;
    }
    else {
        document.getElementById(id_span_ok).innerHTML = inner_no;
        return 0;
    }
}

/* incremente la valeur numerique d'un champ input avec l'increment passe en parametre, eventuellement negatif. */
function increment (obj, increment, min_val) {
    var val = parseInt(obj.value); 
    if (isNaN(val)) val = 0; 
    if ((val > min_val && increment < 0) || (increment > 0)) val = val + increment; 
    return val; 
}

/* appelee en onkeyup sur un textarea source, met a jour de le div destination avec le contenu du textarea. */
function maj_div (id_source, id_destination) {

    var reg_Open  = 0; 
    var reg_Close = 0; 

    /*
    var reg_Open  = new RegExp(/\[(big|small)\]/); 
    var reg_Close = new RegExp(/\[\/(big|small)\]/); 
    */

    var src = document.getElementById(id_source); 
    var dst = document.getElementById(id_destination); 
    var contenu = src.value; 

    // on utilise la regexp pour pouvoir utiliser le flag "g" !
    var reg_Script  = new RegExp("</*script.*", "g"); 
    var reg_Spaces  = new RegExp(" ", "g"); 
    var reg_Html1   = new RegExp("<", "g"); 
    var reg_Html2   = new RegExp(">", "g"); 
    var reg_Br      = new RegExp("\n", "g"); 

    // filtre le contenu html
    contenu = contenu.replace(reg_Script, ""); 
    contenu = contenu.replace(reg_Spaces, "&nbsp;"); 
    contenu = contenu.replace(reg_Html1, "&lt;"); 
    contenu = contenu.replace(reg_Html2, "&gt;"); 

    // remplacements sur le contenu récupéré
    if (reg_Open && reg_Close) {
        // ENCODE LES BALISES OUVRANTES
        var i = 100;    // on utilise une boucle car on ne peut pas utiliser le flag "g" sur l'expression régulière
        while (contenu != contenu.replace(reg_Open, '<$1>') && i) {
            contenu = contenu.replace(reg_Open, '<$1>'); 
            --i; 
        }
        // ENCODE LES BALISES FERMANTES
        var i = 100;    // on utilise une boucle car on ne peut pas utiliser le flag "g" sur l'expression régulière
        while (contenu != contenu.replace(reg_Close, '</$1>') && i) {
            contenu = contenu.replace(reg_Close, '</$1>'); 
            --i; 
        }
    }

    // ENCODE LES RETOURS A LA LIGNE
    contenu = contenu.replace(reg_Br, "<br />"); 

    /* foireux... sous IE autant que sous FF, perturbe la saisie. 
    // LIMITATIONS DE LA SOURCE 
    var reg_Src = new RegExp(/(.{42}).*$/); 
    src.value = src.value.replace (reg_Src, '$1'); 
    */

    dst.innerHTML = contenu; 

}

/* encadre le texte selectionné dans un textarea avec des balises prédéfinies (sorte de bbcode). */
function modif_selection (txtarea, effet) {
    
    txtarea = document.getElementById(txtarea); 

    switch (effet) {
        /*
        case  'big' :
            tagOpen = new Array('\[big\]', '<big>'); 
            tagClose = new Array('\[/big\]', '</big>'); 
            break; 
        case  'small' :
            tagOpen = new Array('\[small\]', '<small>'); 
            tagClose = new Array('\[/small\]', '</small>'); 
            break; 
        */
        default :   // clean
            tagOpen = new Array('\\\[[^\\\] ]*\\\]', ''); 
            tagClose = new Array('\\\[/[^\\\] ]*\\\]', ''); 
            break; 
    }

    var reg_Open  = new RegExp(tagOpen[0],  "g");     // on utilise la regexp pour pouvoir utiliser le flag "g" !
    var reg_Close = new RegExp(tagClose[0], "g");     // on utilise la regexp pour pouvoir utiliser le flag "g" !

    if (document.selection  && document.selection.createRange) { // IE/Opera
        txtarea.focus();
        var range = document.selection.createRange();
        selText = range.text;
        if (effet == 'clean') {
            selText = selText.replace (reg_Open, tagOpen[1]); 
            selText = selText.replace (reg_Close, tagClose[1]); 
            range.text = selText; 
        }
        else {
            range.text = tagOpen[0] + selText + tagClose[0]; 
        }
    }
    else { // Mozilla
        var startPos = txtarea.selectionStart;
        var endPos = txtarea.selectionEnd;
        beforeText = txtarea.value.substring(0, startPos); 
        selText = txtarea.value.substring(startPos, endPos);
        afterText = txtarea.value.substring(endPos, txtarea.value.length);
        if (effet == 'clean') {
            selText = selText.replace (reg_Open, tagOpen[1]); 
            selText = selText.replace (reg_Close, tagClose[1]); 
            txtarea.value = beforeText + selText + afterText; 
        }
        else {
            txtarea.value = beforeText + tagOpen[0] + selText + tagClose[0] + afterText; 
        }
    }

    maj_div ('src_promo', 'dst_promo'); 
    txtarea.focus(); 

}

/* }}} */
