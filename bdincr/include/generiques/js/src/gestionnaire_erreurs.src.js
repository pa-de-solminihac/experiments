/* {{{ FONCTIONS DU GESTIONNAIRE D'ERREURS UNIFIE */

/* trigger_error en javascript pour faire sortir la console d'erreurs utilisateur */
function trigger_error (str, errtype, div_name) {
    errmsg = str;

    switch (errtype) {
        case 'raz': 
            if (div_name) {
                mondiv = document.getElementById(div_name);
                if (mondiv) {
                    mondiv.innerHTML = "";
                    mondiv.style.display = "none";
                    mondiv.style.width = "1px";
                    mondiv = null;
                }
            }
            else {
                if (document.getElementById('div_error_handler_showhide') && (document.getElementById('div_error_handler_showhide').innerHTML.length))
                    document.getElementById('div_error_handler_showhide').innerHTML = "";
            }
            break;

        case 'notice': 
            str = "<div class='tableau_error_handler_divspacer'><div class='tableau_error_handler_notice'>\n"+str+"<br \/>\n<\/div><\/div>";
            break;

        case 'warning': 
            str = "<div class='tableau_error_handler_divspacer'><div class='tableau_error_handler_warning'>\n"+str+"<br \/>\n<\/div><\/div>";
            break;

        case 'fatal': 
            str = "<div class='tableau_error_handler_divspacer'><div class='tableau_error_handler_fatal'>\n"+str+"<br \/>\n<\/div><\/div>";
            break;

        default: 
            str = "<div class='tableau_error_handler_divspacer'><div class='tableau_error_handler_fatal'>\n"+str+"<br \/>\n<\/div><\/div>";
            break;
    }

    if (div_name && errtype != 'raz') {
        mondiv = document.getElementById(div_name);
        if (!mondiv) {
            str = '<div class="invisible" id="'+div_name+'" style="float:left; width:100%; padding:0px; margin:0px; border:solid black 0px;" >'+str+'<\/div>';
            if (document.getElementById('div_error_handler_showhide'))
                document.getElementById('div_error_handler_showhide').innerHTML += str;
        }
        else {
            mondiv.innerHTML = str;
            if (mondiv.style.width == "1px") mondiv.style.width = "100%";
            if (errmsg.length) mondiv.style.display = "block";
        }
    }
    else {
        if (str.length) {
            str = '<div class="invisible" style="float:left; width:100%; padding:0px; margin:0px; border:solid black 0px;" >'+str+'<\/div>';
            if (document.getElementById('div_error_handler_showhide') && (document.getElementById('div_error_handler_showhide').innerHTML.length))
                document.getElementById('div_error_handler_showhide').innerHTML += str;
        }
    }

    // auto hide la console d'erreurs si elle est vide 
    if (document.getElementById('div_error_handler_showhide') && (document.getElementById('div_error_handler_showhide').innerHTML.length)) {
        txt_erreurs = trim(strip_tags(document.getElementById('div_error_handler_showhide').innerHTML));
        if(!txt_erreurs.length) {
            closediv('tableau_error_handler');
            closediv('div_error_handler_showhide');
        }
    }
}

function focus_serie_elts (serie) {
    for (i = parseInt(serie.length-1); i >= 0; --i) {
        document.getElementById('id_'+serie[i][0]+'_field_'+serie[i][1]).focus();
    }
}

/* renvoie le resultat de la condition, et affiche err_msg dans le div champ_trigger */
function validation_champ (condition, champ_trigger, err_msg, afficher, ask_if_login_free) {
    if (afficher) {
        if (condition) {
            trigger_error (err_msg, 'warning', champ_trigger);
        }
        else {
            trigger_error ('',      'raz',     champ_trigger)
        }

        affichage_validation(ask_if_login_free);
    }

    return condition; 
}

/* affichage du tableau de validation */
function affichage_validation (ask_if_login_free) {
    if (document.getElementById('tableau_error_handler') && (document.getElementById('tableau_error_handler').innerHTML.length)) {
        opendiv('div_error_handler_showhide'); 
        opendiv('tableau_error_handler'); 

        if (document.getElementById('div_error_handler_showhide')) txt_erreurs = trim(strip_tags(document.getElementById('div_error_handler_showhide').innerHTML));
        else txt_erreurs = '';

        if(!txt_erreurs.length && !ask_if_login_free) {
            closediv('tableau_error_handler');
            closediv('div_error_handler_showhide');
        }
    }
}

/* }}} */
