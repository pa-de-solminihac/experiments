/* {{{ FONCTIONS AJAX OU D'INTERACTIVITE */

/* envoie des données en GET ou POST en utilisant XmlHttpRequest */
function send_data (id, data, page, method, appel_posteriori) {
    if (!appel_posteriori) appel_posteriori = "content.innerHTML = XHR_object.responseText ;";

    // Cree un objet XMLHttpRequest
    if(document.all) { // Internet Explorer
        var XHR_object = new ActiveXObject("Microsoft.XMLHTTP") ;
    }
    else { // Mozilla
        var XHR_object = new XMLHttpRequest();
    }
    
    // Emplacement pour affichage
    var content = document.getElementById(id);
    
    // Méthode GET
    if ("GET" == method) {
        if ("null" == data) { // Ouverture du fichier demandé
            XHR_object.open("GET", page, true);
        }
        else { // Ouverture du fichier en methode GET
            XHR_object.open("GET", page+"?"+data, true);
        }
    }
    // Méthode POST
    else if ("POST" == method) { // Ouverture du fichier en methode POST
        XHR_object.open("POST", page, true);
    }

    // Ok pour la page cible
    XHR_object.onreadystatechange = function() {
        if (XHR_object.readyState == 4 
         && XHR_object.status == 200) {
            appel_posteriori = appel_posteriori.replace(/{_AJAXDATA_}/g, XHR_object.responseText);
            eval(appel_posteriori);
        }
    }

    if (method == "GET") {
        XHR_object.send (null);
    }
    else if(method == "POST") {
        XHR_object.setRequestHeader('Content-Type',
                                    'application/x-www-form-urlencoded');
        XHR_object.send(data);
    }

} // send_data ()

/* ouvre le fichier désiré en GET */
function get_file (id, page, appel_posteriori) {
    send_data (id, 'null', page, 'GET', appel_posteriori);
} 

/* renvoie les coordonnees de la souris dans un tableau. fonction non testee pour le moment. */
function get_mouse(e) {
    var x = (navigator.appName.substring(0,3) == "Net") ? e.pageX : event.clientX+document.body.scrollLeft;
    var y = (navigator.appName.substring(0,3) == "Net") ? e.pageY : event.clientY+document.body.scrollTop;
    return [x,y];
}

/* }}} */
