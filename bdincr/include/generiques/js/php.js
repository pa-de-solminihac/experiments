/* {{{ FONCTIONS A LA PHP */

// fonctions pour (de)serializer un tableau javascript
function serialize(a)    { return a.toString(); }
function unserialize(s)  { return s.split(","); }
function trim(s)         { return s.replace(/^\s+/, '').replace(/\s+$/, ''); }
function ltrim(s)        { return s.replace(/^\s+/, ''); }
function rtrim(s)        { return s.replace(/\s+$/, ''); }
function is_array(obj) { return (obj.constructor.toString().indexOf("Array") != -1) }

function strpad(val, longueur, remplissage) {
  if (!isNaN(val)) {
    while ((val.toString().length < longueur)) 
      val = remplissage+val;
    return val;
  }
  else return false;
}

function print_r (theObj, stripfunctions) {
  if (theObj.constructor == Array || theObj.constructor == Object) {
    document.write('<ul>');
    for (var p in theObj) {
      if (theObj[p].constructor == Array || theObj[p].constructor == Object) {
        document.write('<li>['+p+'] => '+typeof(theObj)+'<\/li>');
        document.write('<ul>');
        print_r(theObj[p], stripfunctions);
        document.write('<\/ul>');
      } 
      else {
        if ((stripfunctions && theObj[p].substr(0, 8) != 'function') || !stripfunctions)
          document.write('<li>['+p+'] => '+theObj[p]+'<\/li>');
      }
    }
    document.write('<\/ul>');
  }
  else {
    document.write(theObj);
  }
}

function print_pre(theObj, color, name, stripfunctions) {
    if (stripfunctions !== false) stripfunctions = true;
    document.write('<pre');
    if (color) document.write(' style="color: '+color+'"');
    document.write('>');
    if (name) document.write(name+' : ');
    print_r(theObj, stripfunctions);
    document.write('<\/pre>');
}

function strip_tags (tags) {
    stripped = tags.replace(/<[^<>]*>/gi, "");
    return stripped;
}

Array.prototype.in_array = function(search_term) {
   for (var i = 0; i < this.length; ++i) {
      if (this[i] === search_term) {
         return true;
      }
   }
   return false;
}

function now () { // incomplet mais tout de même utile 
    var today = new Date(); 
    today_str = today.getFullYear()+'-'+strpad(today.getMonth()+1,2,'0')+'-'+strpad(today.getDate(),2,'0'); 
    return today_str; 
}

function substr_count (haystack, needle, offset, length) {
    var pos = 0, cnt = 0;
 
    haystack += '';
    needle += '';
    if (isNaN(offset)) {
        offset = 0; 
    }
    if (isNaN(length)) {
        length = 0;
    }
    offset--;
 
    while ((offset = haystack.indexOf(needle, offset+1)) != -1 ) {
        if (length > 0 && (offset + needle.length) > length) {
            return false;
        } else {
            cnt++;
        }
    }
 
    return cnt;
}

function array_unique (array) {
 
    var p, i, j, tmp_arr = array;
    for (i = tmp_arr.length; i; true) {
        for (p = --i; p > 0; true) {
            if (tmp_arr[i] === tmp_arr[--p]) {
                for (j = p; --p && tmp_arr[i] === tmp_arr[p]; true);
                i -= tmp_arr.splice(p + 1, j - p).length;
            }
        }
    }
 
    return tmp_arr;
}

/* }}} */
