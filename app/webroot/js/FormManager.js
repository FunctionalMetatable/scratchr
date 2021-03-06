/*
	Form Manager: A simple method of constructing complex dynamic forms.
	Written by Twey, http://www.twey.co.uk/.
	Use, copying, and modification allowed, so long as credit
	remains intact, under the terms of the GNU General Public License,
	version 2 or later. See http://www.gnu.org/copyleft/gpl.html for details.
*/


var FORM_MANAGER_CONDITION_SEPARATOR = " AND ";
var FORM_MANAGER_POSSIBILITY_SEPARATOR = " OR ";
var FORM_MANAGER_NAME_VALUE_SEPARATOR = " BEING ";
var FORM_MANAGER_DEPENDS = "DEPENDS ON ";
var FORM_MANAGER_CONFLICTS = "CONFLICTS WITH ";
var FORM_MANAGER_EMPTY = "EMPTY";
var FORM_MANAGER_EVAL_EXPRESSION = true;

function addEvent(el, ev, f) {
  if(el.addEventListener)
    el.addEventListener(ev, f, false);
  else if(el.attachEvent) {
    var t = function() {
      f.apply(el);
    };
    addEvent.events.push({'element': el, 'event': ev, 'handler': f});
    el.attachEvent("on" + ev, t);
  } else
    el['on' + ev] = f;
}

function addEvents(els, evs, f) {
  for(var i = 0; i < els.length; ++i)
    for(var j = 0; j < evs.length; ++j)
      addEvent(els[i], evs[j], f);
}

addEvent.events = [];

if(typeof window.event !== "undefined")
  addEvent(window, "unload", function() {
      for(var i = 0, e = addEvent.events; i < e.length; ++i)
        e[i].element.detachEvent("on" + e[i].event, e[i].handler);
    }
  );

function getRadioValue(el) {
  if(!el.length) return null;
  for(var i = 0; i < el.length; ++i)
    if(el[i].checked) return el[i].value;
  return null;
}

function getSelectValue(el) {
  if(!el.tagName  || el.tagName.toLowerCase() !== "select")
    return null;
  return el.options[el.selectedIndex].value;
}

function getNormalValue(el) {
  if(el.tagName && el.tagName.toLowerCase() !== "select")
    return el.value;
  else return null;
}

function isElementValue(el, v) {
  if(v === FORM_MANAGER_EMPTY) v = '';
  for(var i = 0, f = ["Radio", "Select", "Normal"], func; func = f[i]; ++i) {
    var val = window['get' + func + 'Value'](el);
    try {
      if(!FORM_MANAGER_EVAL_EXPRESSION)
        throw "Erk";
      else if(eval(val + " " + v))
        return true;
    } catch(e) {
    } finally {
      if(val == v)
        return true;
    }
  }
  return false;
}

function setupDependencies() {
  var showEl = function() {
    this.style.display = "";
	this.disabled = false;
    if(this.parentNode.tagName.toLowerCase() == "label")
      this.parentNode.style.display = "";
  };
  var hideEl = function() {
    this.style.display = "none";
	this.disabled = true;
    if(typeof this.checked !== "undefined") this.checked = false;
    else this.value = "";
    if(this.parentNode.tagName.toLowerCase() == "label")
      this.parentNode.style.display = "none";
    this.hidden = true;
  };
  var calcDeps = function() {
    for(var i = 0, e = this.elements; i < e.length; ++i) {
      e[i].hidden = false;
      for(var j = 0, f = e[i].className.split(FORM_MANAGER_CONDITION_SEPARATOR); j < f.length; ++j)
        if(f[j].indexOf(FORM_MANAGER_DEPENDS) === 0) {
          for(var k = 0, g = f[j].substr(FORM_MANAGER_DEPENDS.length).split(FORM_MANAGER_POSSIBILITY_SEPARATOR); k < g.length; ++k)
            if(g[k].indexOf(FORM_MANAGER_NAME_VALUE_SEPARATOR) === -1) {
	      if(e[g[k]] && e[g[k]].checked) break;
            else if(k + 1 == g.length)
                e[i].hide();
            } else {
              var n = g[k].split(FORM_MANAGER_NAME_VALUE_SEPARATOR),
                v = n[1];
              n = n[0];
	      if(e[n])
	        if(isElementValue(e[n], v)) break;
	        else if(k + 1 == g.length) e[i].hide();
	    }
        } else if(f[j].indexOf(FORM_MANAGER_CONFLICTS) === 0) {
          if(f[j].indexOf(FORM_MANAGER_NAME_VALUE_SEPARATOR) === -1) {
	    if(e[f[j].substr(FORM_MANAGER_CONFLICTS.length)] && e[f[j].substr(FORM_MANAGER_CONFLICTS.length)].checked) {
              e[i].hide();
              break;
            }
          } else {
            var n = f[j].substr(FORM_MANAGER_CONFLICTS.length).split(FORM_MANAGER_NAME_VALUE_SEPARATOR),
              v = n[1];
            n = n[0];
            if(e[n]) {
              if(isElementValue(e[n], v)) {
                e[i].hide();
                break;
              }
            }
          }
        }
      if(!e[i].hidden) e[i].show();
    }
  };
  var changeHandler = function() {
    this.form.calculateDependencies();
    return true;
  };
  for(var i = 0; i < arguments.length; ++i) {
    for(var j = 0, e = window.document.forms[arguments[i]].elements; j < e.length; ++j) {
      addEvents([e[j]], ["change", "keyup", "focus"], changeHandler);
      e[j].hide = hideEl;
      e[j].show = showEl;
    }

    (e = window.document.forms[arguments[i]]).calculateDependencies = calcDeps;
    e.calculateDependencies();
  }
}
