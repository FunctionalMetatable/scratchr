/*
SimpleJS ver 0.02 beta
----------------------
SimpleJS is developed by Christophe "Dyo" Lefevre (http://bleebot.com/)

$ajax function is based on Simple AJAX Code-Kit (SACK)
Gregory Wild-Smith (http://www.twilightuniverse.com/)
*/
var enableCache=true;
var jsCache=new Array();
var DynObj=new Array();
function $ajax(_1){
this.xmlhttp=null;
this.resetData=function(){
this.method="POST";
this.queryStringSeparator="?";
this.argumentSeparator="&";
this.URLString="";
this.encodeURIString=true;
this.execute=false;
this.element=null;
this.elementObj=null;
this.requestFile=_1;
this.vars=new Object();
this.responseStatus=new Array(2);
};
this.resetFunctions=function(){
this.onLoading=function(){
};
this.onLoaded=function(){
};
this.onInteractive=function(){
};
this.onCompletion=function(){
};
this.onError=function(){
};
this.onFail=function(){
};
};
this.reset=function(){
this.resetFunctions();
this.resetData();
};
this.crAjx=function(){
try{
this.xmlhttp=new ActiveXObject("Msxml2.XMLHTTP");
}
catch(e1){
try{
this.xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
catch(e2){
this.xmlhttp=null;
}
}
if(!this.xmlhttp){
if(typeof XMLHttpRequest!="undefined"){
this.xmlhttp=new XMLHttpRequest();
}else{
this.failed=true;
}
}
};
this.setVar=function(_2,_3){
this.vars[_2]=Array(_3,false);
};
this.encVar=function(_4,_5,_6){
if(true==_6){
return Array(encodeURIComponent(_4),encodeURIComponent(_5));
}else{
this.vars[encodeURIComponent(_4)]=Array(encodeURIComponent(_5),true);
}
};
this.processURLString=function(_7,_8){
encoded=encodeURIComponent(this.argumentSeparator);
regexp=new RegExp(this.argumentSeparator+"|"+encoded);
varArray=_7.split(regexp);
for(i=0;i<varArray.length;i++){
urlVars=varArray[i].split("=");
if(true==_8){
this.encVar(urlVars[0],urlVars[1]);
}else{
this.setVar(urlVars[0],urlVars[1]);
}
}
};
this.createURLString=function(_9){
if(this.encodeURIString&&this.URLString.length){
this.processURLString(this.URLString,true);
}
if(_9){
if(this.URLString.length){
this.URLString+=this.argumentSeparator+_9;
}else{
this.URLString=_9;
}
}
this.setVar("rndval",new Date().getTime());
urlstringtemp=new Array();
for(key in this.vars){
if(false==this.vars[key][1]&&true==this.encodeURIString){
encoded=this.encVar(key,this.vars[key][0],true);
delete this.vars[key];
this.vars[encoded[0]]=Array(encoded[1],true);
key=encoded[0];
}
urlstringtemp[urlstringtemp.length]=key+"="+this.vars[key][0];
}
if(_9){
this.URLString+=this.argumentSeparator+urlstringtemp.join(this.argumentSeparator);
}else{
this.URLString+=urlstringtemp.join(this.argumentSeparator);
}
};
this.runResponse=function(){
eval(this.response);
};
this.runAJAX=function(_a){
if(this.failed){
this.onFail();
}else{
this.createURLString(_a);
if(this.element){
this.elementObj=$(this.element);
}
if(this.xmlhttp){
var _b=this;
if(this.method=="GET"){
totalurlstring=this.requestFile+this.queryStringSeparator+this.URLString;
this.xmlhttp.open(this.method,totalurlstring,true);
}else{
this.xmlhttp.open(this.method,this.requestFile,true);
try{
this.xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
}
catch(e){
}
}
this.xmlhttp.onreadystatechange=function(){
switch(_b.xmlhttp.readyState){
case 1:
_b.onLoading();
break;
case 2:
_b.onLoaded();
break;
case 3:
_b.onInteractive();
break;
case 4:
_b.response=_b.xmlhttp.responseText;
_b.responseXML=_b.xmlhttp.responseXML;
_b.responseStatus[0]=_b.xmlhttp.status;
_b.responseStatus[1]=_b.xmlhttp.statusText;
if(_b.execute){
_b.runResponse();
}
if(_b.elementObj){
elemNodeName=_b.elementObj.nodeName;
elemNodeName.toLowerCase();
if(elemNodeName=="input"||elemNodeName=="select"||elemNodeName=="option"||elemNodeName=="textarea"){
_b.elementObj.value=_b.response;
}else{
_b.elementObj.innerHTML=_b.response;
}
}
if(_b.responseStatus[0]=="200"){
_b.onCompletion();
}else{
_b.onError();
}
_b.URLString="";
break;
}
};
this.xmlhttp.send(this.URLString);
}
}
};
this.reset();
this.crAjx();
}
function ajax_installScript(_c){
if(!_c){
return;
}
if(window.execScript){
window.execScript(_c);
}else{
if(window.jQuery&&jQuery.browser.safari){
STO(_c,0);
}else{
STO(_c,0);
}
}
}
function $ajax_show(_d,_e,_f,_10,_11){
if(_11=="appear"){
$opacity(_d,0,101,600);
}
if(_11=="highlight"){
$highlight(_d);
}
var _12=$(_d);
_12.innerHTML=DynObj[_e].response;
if(_11=="blind"){
$(_d).style.position="";
$blinddown(_d);
}
if(enableCache){
jsCache[_f]=DynObj[_e].response;
}
DynObj[_e]=false;
ajax_parseJs(_12);
}
function $ajaxreplace(_13,url){
$opacity(_13,100,0,400);
$(_13).style.height="";
scr="$ajaxload('"+_13+"','"+url+"',false,'appear',false)";
STO(scr,400);
}
function $ajaxload(_15,url,_17,_18,_19){
if(_18=="appear"){
changeOpac(0,_15);
}
if(_18=="blind"){
var ids=$(_15).style;
ids.overflow="hidden";
ids.display="block";
ids.height="0px";
}
if(_19){
if(enableCache&&jsCache[url]){
if(_18=="appear"){
$opacity(_15,0,101,600);
}
if(_18=="highlight"){
$highlight(_15);
}
$(_15).innerHTML=jsCache[url];
if(_18=="blind"){
$(_15).style.position="";
$blinddown(_15);
}
return;
}
}
var _1b=DynObj.length;
if(_17!=false){
$(_15).innerHTML=_17;
}
DynObj[_1b]=new $ajax();
DynObj[_1b].requestFile=url;
DynObj[_1b].onCompletion=function(){
$ajax_show(_15,_1b,url,_17,_18);
};
DynObj[_1b].runAJAX();
}
function ajax_parseJs(obj){
var _1d=obj.getElementsByTagName("SCRIPT");
var _1e="";
var _1f="";
for(var no=0;no<_1d.length;no++){
if(_1d[no].src){
var _21=document.getElementsByTagName("head")[0];
var _22=document.createElement("script");
_22.setAttribute("type","text/javascript");
_22.setAttribute("src",_1d[no].src);
}else{
if(DHTMLSuite.clientInfoObj.isOpera){
_1f=_1f+_1d[no].text+"\n";
}else{
_1f=_1f+_1d[no].innerHTML;
}
}
}
if(_1f){
ajax_installScript(_1f);
}
}
function $(id){
return document.getElementById(id);
}
function STO(_24,_25){
return window.setTimeout(_24,_25);
}
function DecToHexa(_26){
var _27=parseInt(_26).toString(16);
if(_26<16){
_27="0"+_27;
}
return _27;
}
function addslashes(str){
str=str.replace(/\"/g,"\\\"");
str=str.replace(/\'/g,"\\'");
return str;
}
function $toggle(id){
if(act_height(id)==0){
$blinddown(id);
}else{
$blindup(id);
}
}
function act_height(id){
height=$(id).clientHeight;
if(height==0){
height=$(id).offsetHeight;
}
return height;
}
function act_width(id){
width=$(id).clientWidth;
if(width==0){
width=$(id).offsetWidth;
}
return width;
}
function max_height(id){
var ids=$(id).style;
ids.overflow="hidden";
if(act_height(id)!=0){
return act_height(id);
}else{
origdisp=ids.display;
origheight=ids.height;
origpos=ids.position;
origvis=ids.visibility;
ids.visibility="hidden";
ids.height="";
ids.display="block";
ids.position="absolute";
height=act_height(id);
ids.display=origdisp;
ids.height=origheight;
ids.position=origpos;
ids.visibility=origvis;
return height;
}
}
function $blindup(id,_2f){
if(!_2f){
_2f=200;
}
acth=act_height(id);
maxh=max_height(id);
if(acth==maxh){
$(id).style.display="block";
var _30;
_30=Math.ceil(_2f/acth);
for(i=0;i<=acth;i++){
newh=acth-i;
STO("$('"+id+"').style.height='"+newh+"px'",_30*i);
}
}
}
function $blinddown(id,_32){
if(!_32){
_32=200;
}
acth=act_height(id);
if(acth==0){
maxh=max_height(id);
$(id).style.display="block";
$(id).style.height="0px";
var _33;
_33=Math.ceil(_32/maxh);
for(i=1;i<=maxh;i++){
STO("$('"+id+"').style.height='"+i+"px'",_33*i);
}
}
}
function $opacity(id,_35,_36,_37){
if($(id).style.width==0){
$(id).style.width=act_width(id);
}
var _38=Math.round(_37/100);
var _39=0;
if(_35>_36){
for(i=_35;i>=_36;i--){
STO("changeOpac("+i+",'"+id+"')",(_39*_38));
_39++;
}
}else{
if(_35<_36){
for(i=_35;i<=_36;i++){
STO("changeOpac("+i+",'"+id+"')",(_39*_38));
_39++;
}
}
}
}
function changeOpac(_3a,id){
var ids=$(id).style;
ids.opacity=(_3a/100);
ids.MozOpacity=(_3a/100);
ids.KhtmlOpacity=(_3a/100);
ids.filter="alpha(opacity="+_3a+")";
}
function $shiftOpacity(id,_3e){
if($(id).style.opacity<0.5){
$opacity(id,0,100,_3e);
}else{
$opacity(id,100,0,_3e);
}
}
function currentOpac(id,_40,_41){
var _42=100;
if($(id).style.opacity<100){
_42=$(id).style.opacity*100;
}
$opacity(id,_42,_40,_41);
}
function $highlight(id,_44,_45,_46){
if(_44){
milli=_44;
}else{
milli=900;
}
if(_45){
endcol=_45;
}else{
endcol="#FFFFFF";
}
if(_46){
origcol=_46;
}else{
origcol="#FFFFA6";
}
$colorize(origcol,endcol,id,milli,"high");
}
function $textColor(id,_48,_49,_4a){
if(_4a){
milli=_4a;
}else{
milli=900;
}
$colorize(_48,_49,id,milli,"text");
}
function $morphColor(id,_4c,_4d,_4e,_4f,_50,_51,_52){
if(_52){
milli=_52;
}else{
milli=900;
}
$colorize(_4c,_4d,id,milli,"text");
$colorize(_4e,_4f,id,milli,"back");
if(_50!=false){
$colorize(_50,_51,id,milli,"border");
}
}
function $colorize(_53,end,id,_56,_57){
dr=parseInt(_53.substring(1,3),16);
dg=parseInt(_53.substring(3,5),16);
db=parseInt(_53.substring(5,7),16);
fr=parseInt(end.substring(1,3),16);
fg=parseInt(end.substring(3,5),16);
fb=parseInt(end.substring(5,7),16);
steps=_56/10;
cr=dr;
cg=dg;
cb=db;
sr=(fr-dr)/steps;
sg=(fg-dg)/steps;
sb=(fb-db)/steps;
var zzi=10;
for(var x=0;x<steps;x++){
color="#"+DecToHexa(cr)+DecToHexa(cg)+DecToHexa(cb);
if(x==(steps-1)){
if(_57=="high"){
color="";
}else{
color=end;
}
}
mytime=(x);
if(_57=="back"||_57=="high"){
newfonc="$(\""+id+"\").style.backgroundColor=\""+color+"\";";
}else{
if(_57=="text"){
newfonc="$(\""+id+"\").style.color=\""+color+"\";";
}else{
if(_57=="border"){
newfonc="$(\""+id+"\").style.borderColor=\""+color+"\";";
}
}
}
STO(newfonc,zzi);
cr+=sr;
cg+=sg;
cb+=sb;
zzi+=10;
}
}