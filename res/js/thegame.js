try {
  console.log('init console... done');
}
catch (e) {
  console = { log:function () {
  } }
}

var Qurl
  ;

(function () {
  'use strict';
  
  var proto;
 
  Qurl = function () {
    if ( !(this instanceof Qurl) ) {
      return new Qurl();
    }
  }
 
  proto = Qurl.prototype;
 
  proto.query = function (key,value) {
    if ( !key ) {
      return getSearch();
    }

    if ( key && typeof(value) === 'undefined' ) {
      return getSearch()[key];
    }

    if ( key && typeof(value) !== 'undefined' ) {
      return setSearch(key,value);
    }
  };
  
  proto.toStr = function(){
    return getSearchString(getSearch());
  }

  function getSearch() {
    var string = window.location.search
      , pairs = []
      , obj = {}
      ;

    if ( !string ) {
      return obj;
    }
    string = string.replace('?','');

    pairs = string.split('&');
    pairs.forEach(function (p) {
      var pair = decodeURIComponent(p).split('=')
        , key = pair[0]
        , val = pair[1]
        ;

      obj[key] = val;
    });

    return obj;
  }

  function setSearch(key,value) {
    var search = getSearch()
      , string = window.location.search
      ;

    if ( value === false ) {
      delete search[key];
    }
    else {
      search[key] = value;
    }

    setSearchString(getSearchString(search));

    return search;
  }

  function setSearchString(string) {
    if ( history.pushState ) {
      history.pushState({}, document.title, string);
    }
  }

  function getSearchString(query) {
    var pairs = []
      ;

    Object.keys(query).forEach(function (key) {
      if ( typeof query[key] === 'undefined' ) {
        pairs.push(key);
      } else {
        pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(query[key] || '')); 
      }
    });

    if ( pairs.length === 0 ) {
      return '?';
    }
    else {
      return '?' + pairs.join('&');
    }
  }
  
  Qurl.create = Qurl;
}());

if (getTitle() && /\(([^\)]+)\)$/.exec(getTitle())) {
  servernameshort = (RegExp.$1);
}
var body = document.getElementsByTagName("body")[0];
var html = document.getElementsByTagName("html")[0];

var isIE = (navigator.appVersion.indexOf("MSIE") != -1) ? true : false;
var isWin = (navigator.appVersion.toLowerCase().indexOf("win") != -1) ? true : false;
var isOpera = (navigator.userAgent.indexOf("Opera") != -1) ? true : false;
var servernameshort = "";
var jsloader = {};
var popupIframe = {};

function reload() {
  window.location.reload();
}
function reload_iframe(id) {
  var iframe = document.getElementById(id);
  var url = iframe.src;
  if (url.match(/[^a-z]rnd=[0-9.]+/i)) {
    url.replace(/rnd=[0-9.]+/i, "rnd=" + Math.random());
  } else if (url.match(/[?]/)) {
    url = url + "&rnd=" + Math.random();
  } else {
    url = url + "?rnd=" + Math.random();
  }

  iframe.src = url;
}
function reload_rtl() {
  reload_iframe("branding_rtl");
}
function loadpixel(url) {
  var iframe;
  iframe = document.createElement('iframe');
  iframe.style.width = 0;
  iframe.style.height = 0;
  iframe.style.frameborder = 0;
  iframe.style.display = 'none';
  iframe.style.scrolling = 0;
  iframe.setAttribute("src", url);
  document.getElementById('body').appendChild(iframe);
}

function set_title(text) {
  var titleNode = document.getElementById("title");
  if ((servernameshort || branding_url) && /\(([^\)]+)\)$/.exec(text)) {
    if (branding_url != ""){
      text = text.replace("(" + RegExp.$1 + ")", "(" + serverid + " "+ country + ")");
    } else {
      text = text.replace("(" + RegExp.$1 + ")", "(" + servernameshort + ")");
    }
  }
  if (!isIframe) {
    document.title = text;
  } else if (titleNode) {
    text =  text + " - " + (branding_url!=""?branding_url:document.location.host);
    try{
      titleNode.replaceChild(document.createTextNode(text), titleNode.firstChild);
    } catch (e){
    }
  }

}

function getUniqueId(size)
{
	var chars = "0123456789abcdefghijklmnopqurstuvwxyzABCDEFGHIJKLMNOPQURSTUVWXYZ";
	var str = "";
	for(var i = 0; i < size; i++)
	{
	
		str += chars.substr( Math.floor(Math.random() * 62), 1 );
	}
	return str;
}


function getTitle() {
  if (isIframe) {
    if (document.getElementById("title")) {
      return "" + document.getElementById("title").firstChild;
      
    } else if (!document.title) {
      return "";
    }
  }
  return document.title;
}

function send(cmd) {
  try {
    var args = Array.prototype.slice.call(send.arguments);
    document.getElementById(project).doSend(cmd, args);
  }
  catch (err) {
    console.log(err);
  }
}

function showSocial(socialName) {
  if (sociallinks[project] && sociallinks[project][socialName]) {
    window.open(sociallinks[project][socialName], '_newtab');
  }
}

function require(file, callback) {
   var script = document.getElementsByTagName('script')[0],
   newjs = document.createElement('script');

  // IE
  newjs.onreadystatechange = function () {
     if (newjs.readyState === 'loaded' || newjs.readyState === 'complete') {
        newjs.onreadystatechange = null;
        callback();
     }
  };
  // others
  newjs.onload = function () {
     callback();
  }; 
  newjs.src = file;
  script.parentNode.insertBefore(newjs, script);
}

function ControlVersion() {
  var version;
  var axo;
  var e;
  // NOTE : new ActiveXObject(strFoo) throws an exception if strFoo isn't in the registry
  try {
    // version will be set for 7.X or greater players
    axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7");
    version = axo.GetVariable("$version");
  } catch (e) {
  }
  if (!version) {
    try {
      // version will be set for 6.X players only
      axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6");

      // installed player is some revision of 6.0
      // GetVariable("$version") crashes for versions 6.0.22 through 6.0.29,
      // so we have to be careful.

      // default to the first public version
      version = "WIN 6,0,21,0";
      // throws if AllowScripAccess does not exist (introduced in 6.0r47)
      axo.AllowScriptAccess = "always";
      // safe to call for 6.0r47 or greater
      version = axo.GetVariable("$version");
    } catch (e) {
    }
  }
  if (!version) {
    try {
      // version will be set for 4.X or 5.X player
      axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
      version = axo.GetVariable("$version");
    } catch (e) {
    }
  }
  if (!version) {
    try {
      // version will be set for 3.X player
      axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.3");
      version = "WIN 3,0,18,0";
    } catch (e) {
    }
  }
  if (!version) {
    try {
      // version will be set for 2.X player
      axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash");
      version = "WIN 2,0,0,11";
    } catch (e) {
      version = -1;
    }
  }

  return version;
}
// JavaScript helper required to detect Flash Player PlugIn version information
function GetSwfVer() {
  // NS/Opera version >= 3 check for Flash plugin in plugin array
  var flashVer = -1;

  if (navigator.plugins != null && navigator.plugins.length > 0) {
    if (navigator.plugins["Shockwave Flash 2.0"] || navigator.plugins["Shockwave Flash"]) {
      var swVer2 = navigator.plugins["Shockwave Flash 2.0"] ? " 2.0" : "";
      var flashDescription = navigator.plugins["Shockwave Flash" + swVer2].description;
      var descArray = flashDescription.split(" ");
      var tempArrayMajor = descArray[2].split(".");
      var versionMajor = tempArrayMajor[0];
      var versionMinor = tempArrayMajor[1];
      var versionRevision = descArray[3];
      if (versionRevision == "") {
        versionRevision = descArray[4];
      }
      if (versionRevision[0] == "d") {
        versionRevision = versionRevision.substring(1);
      } else if (versionRevision[0] == "r") {
        versionRevision = versionRevision.substring(1);
        if (versionRevision.indexOf("d") > 0) {
          versionRevision = versionRevision.substring(0, versionRevision.indexOf("d"));
        }
      }
      var flashVer = versionMajor + "." + versionMinor + "." + versionRevision;
    }
  }
  // MSN/WebTV 2.6 supports Flash 4
  else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.6") != -1) flashVer = 4;
  // WebTV 2.5 supports Flash 3
  else if (navigator.userAgent.toLowerCase().indexOf("webtv/2.5") != -1) flashVer = 3;
  // older WebTV supports Flash 2
  else if (navigator.userAgent.toLowerCase().indexOf("webtv") != -1) flashVer = 2;
  else if (isIE && isWin && !isOpera) {
    flashVer = ControlVersion();
  }
  return flashVer;
}
// When called with reqMajorVer, reqMinorVer, reqRevision returns true if that version or greater is available
function DetectFlashVer(reqMajorVer, reqMinorVer, reqRevision) {
  versionStr = GetSwfVer();
  if (versionStr == -1) {
    return false;
  } else if (versionStr != 0) {
    if (isIE && isWin && !isOpera) {
      // Given "WIN 2,0,0,11"
      tempArray = versionStr.split(" "); 	// ["WIN", "2,0,0,11"]
      tempString = tempArray[1];			// "2,0,0,11"
      versionArray = tempString.split(",");	// ['2', '0', '0', '11']
    } else {
      versionArray = versionStr.split(".");
    }
    var versionMajor = versionArray[0];
    var versionMinor = versionArray[1];
    var versionRevision = versionArray[2];
    // is the major.revision >= requested major.revision AND the minor version >= requested minor
    if (versionMajor > parseFloat(reqMajorVer)) {
      return true;
    } else if (versionMajor == parseFloat(reqMajorVer)) {
      if (versionMinor > parseFloat(reqMinorVer))
        return true;
      else if (versionMinor == parseFloat(reqMinorVer)) {
        if (versionRevision >= parseFloat(reqRevision))
          return true;
      }
    }
    return false;
  }
}
function AC_AddExtension(src, ext) {
  if (src.indexOf('?') != -1)
    return src.replace(/\?/, ext + '?');
  else
    return src + ext;
}
function AC_Generateobj(objAttrs, params, embedAttrs) {
  var str = '';
  if (isIE && isWin && !isOpera) {
    str += '<object ';
    for (var i in objAttrs) {
      str += i + '="' + objAttrs[i] + '" ';
    }
    str += '>';
    for (var i in params) {
      str += '<param name="' + i + '" value="' + params[i] + '" /> ';
    }
    str += '</object>';
  }
  else {
    str += '<embed ';
    str += 'style="z-index:0;" '
    for (var i in embedAttrs) {
      str += i + '="' + embedAttrs[i] + '" ';
    }
    str += '> </embed>';
  }
  document.write(str);
}
function AC_FL_RunContent() {
  var ret =
    AC_GetArgs
      (arguments, ".swf", "movie", "clsid:d27cdb6e-ae6d-11cf-96b8-444553540000"
        , "application/x-shockwave-flash"
      );
  AC_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
}
function AC_SW_RunContent() {
  var ret =
    AC_GetArgs
      (arguments, ".dcr", "src", "clsid:166B1BCA-3F9C-11CF-8075-444553540000"
        , null
      );
  AC_Generateobj(ret.objAttrs, ret.params, ret.embedAttrs);
}
function AC_GetArgs(args, ext, srcParamName, classid, mimeType) {
  var ret = new Object();
  ret.embedAttrs = new Object();
  ret.params = new Object();
  ret.objAttrs = new Object();
  for (var i = 0; i < args.length; i = i + 2) {
    var currArg = args[i].toLowerCase();
    switch (currArg) {
      case "classid":
        break;
      case "pluginspage":
        ret.embedAttrs[args[i]] = args[i + 1];
        break;
      case "src":
      case "movie":
        if(typeof language != "undefined" && language != ""){
          var url = Qurl.create();
          url.query('lang',language);
          args[i + 1] = AC_AddExtension(args[i + 1], ext + url.toStr());
        } else {
          args[i + 1] = AC_AddExtension(args[i + 1], ext + window.location.search);
        }
        ret.embedAttrs["src"] = args[i + 1];
        ret.params[srcParamName] = args[i + 1];
        break;
      case "onafterupdate":
      case "onbeforeupdate":
      case "onblur":
      case "oncellchange":
      case "onclick":
      case "ondblclick":
      case "ondrag":
      case "ondragend":
      case "ondragenter":
      case "ondragleave":
      case "ondragover":
      case "ondrop":
      case "onfinish":
      case "onfocus":
      case "onhelp":
      case "onmousedown":
      case "onmouseup":
      case "onmouseover":
      case "onmousemove":
      case "onmouseout":
      case "onkeypress":
      case "onkeydown":
      case "onkeyup":
      case "onload":
      case "onlosecapture":
      case "onpropertychange":
      case "onreadystatechange":
      case "onrowsdelete":
      case "onrowenter":
      case "onrowexit":
      case "onrowsinserted":
      case "onstart":
      case "onscroll":
      case "onbeforeeditfocus":
      case "onactivate":
      case "onbeforedeactivate":
      case "ondeactivate":
      case "type":
      case "codebase":
        ret.objAttrs[args[i]] = args[i + 1];
        break;
      case "width":
      case "height":
      case "align":
      case "vspace":
      case "hspace":
      case "class":
      case "title":
      case "accesskey":
      case "name":
      case "tabindex":
      case "id":
      case "secure":
        ret.embedAttrs[args[i]] = ret.objAttrs[args[i]] = args[i + 1];
        break;
      default:
        ret.embedAttrs[args[i]] = ret.params[args[i]] = args[i + 1];
    }
  }
  ret.objAttrs["classid"] = classid;
  if (mimeType) ret.embedAttrs["type"] = mimeType;
  return ret;
}
function encode_utf8(raw) {
  // dient der Normalisierung des Zeilenumbruchs
  var utftext = "";
  for (var n = 0; n < raw.length; n++) {
    var c = raw.charCodeAt(n);
    if (c < 0x80) {
      utftext += encodeURI(String.fromCharCode(c));
    } else if (c < 0xc2) {
    } else if (c <= 0xdf) {
      unicode = ((c & 0x1f) << 6 ) | (raw.charCodeAt(n + 1) & 0x3f);      
      if (unicode <= 0xff) {
        utftext += "%" + (new Array(2 + 1).join('0') + unicode.toString(16)).slice(-2);
      } else {
        utftext += "%u" + (new Array(4 + 1).join('0') + unicode.toString(16)).slice(-4);
      }
      n += 1;
    } else if (c <= 0xef) {
      unicode = (c & 0x0f) << 12 |
        (raw.charCodeAt(n + 1) & 0x3f) << 6 |
        (raw.charCodeAt(n + 2) & 0x3f);
      utftext += "%u" + (new Array(4 + 1).join('0') + unicode.toString(16)).slice(-4);
      n += 2;
    } else if (c <= 0xf4) {
      unicode = (c & 0x0f) << 18 |
        (raw.charCodeAt(n + 1) & 0x3f) << 12 |
        (raw.charCodeAt(n + 2) & 0x3f) << 6 |
        (raw.charCodeAt(n + 3) & 0x3f);
      utftext += "%u" + (new Array(4 + 1).join('0') + unicode.toString(16)).slice(-4);
      n += 3;
    } else {
      utftext += escape(String.fromCharCode(c));
    }
  }
  return utftext;
}

function openUrl(url) {
  var basetarget = "_blank";
  url = encode_utf8(url);

  if (isIframe && !url.match(/^(http[s]?:\/\/)?forum./i)) {
    basetarget = "_self";
  }

  window.open(url, basetarget);
}
var hasRightVersion = DetectFlashVer(requiredMajorVersion, requiredMinorVersion, requiredRevision);

if (hasRightVersion) {  // if we've detected an acceptable version
  // embed the flash movie
  AC_FL_RunContent(
    'codebase', 'http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,124,0',
    'width', swfwidth,
    'height', swfheight,
    'src', project,
    'quality', swfquality,
    'pluginspage', 'http://www.adobe.com/go/getflashplayer',
    'align', 'middle',
    'play', 'true',
    'loop', 'true',
    'scale', swfscale,
    //'wmode', 'window',
    //'wmode', 'opaque',
    'wmode', wmode,
    'devicefont', 'false',
    'id', project,
    'bgcolor', bgcolor,
    'name', project,
    'menu', 'true',
    'allowFullScreen', 'false',
    'allowScriptAccess', 'always',
    'movie', movie,
    'salign', 't',
    'secure', 'false'
  ); //end AC code
}
else {


  document.write('<style type="text/css">');
  document.write('body {background:url(' + bgimage + '); background-repeat:no-repeat; background-position:top center; background-color:#000000;}');
  document.write('.noscript{filter:alpha(opacity=60);-moz-opacity: 0.60;opacity: 0.60;background-color: #000000;}');
  document.write('a:link { text-decoration:none; color:#ffd9aa; }');
  document.write('a:visited { text-decoration:none; color:#ffd9aa; }');
  document.write('a:hover { text-decoration:underline; color:#ffd9aa; }');
  document.write('a:active { text-decoration:none; color:#ffd9aa; }');
  document.write('a:focus { text-decoration:none; color:#ffd9aa; }');
  document.write('.bigfont {font-size: 30px; font-weight: bold;}');
  document.write('</style> ');
  document.write('<table align="center" width="100%" height="100%" style="font-family: Tahoma, Arial, Helvetica, sans-serif; color: #FFFFFF;"> ');
  document.write('<tr>');
  document.write('<td align="center" valign="middle" height="100" class="noscript">');
  document.write('<span class="bigfont">' + message1 + ': <A href="http://www.adobe.com/go/getflashplayer/">' + message2 + '</A></span> ');
  document.write('</td>');
  document.write('</tr>');
  document.write('</table>');

}

function getOffset( el ) {
  var _x = 0;
  var _y = 0;
  while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
    try{
      _x += el.offsetLeft - el.scrollLeft;
      _y += el.offsetTop - el.scrollTop;
      el = el.offsetParent;
    } catch(e){
    }
  }
  return { top: _y, left: _x };
}
function setOpacity (myElement, opacityValue) {
    if (window.ActiveXObject) {
        myElement.style.filter = "alpha(opacity="
             + opacityValue*100 + ")"; // IE
    } else {
        myElement.style.opacity = opacityValue; // Gecko/Opera
    }
}

function paymentUltimatepay(param){
  var path = window.location.pathname;
  if (window.location.pathname.lastIndexOf('/') >= 0){ 
    path = window.location.pathname.substr(0, window.location.pathname.lastIndexOf('/') + 1);
  }

	var if_ulp = document.createElement('iframe');
	if_ulp.setAttribute("id", "if_ulp"); 
	//if_ulp.setAttribute("src", "../../payment/ultimatepay.php?"+param);
	if_ulp.setAttribute("src", (("https:" == document.location.protocol) ? "https" : "http") + "://"  + window.location.hostname + path + "/payment/ultimatepay.php?"+param);
	//if_ulp.style.display = "none";
	//if_ulp.style.padding = "10px";
	if_ulp.style.position = "absolute";
	if_ulp.style.top = "50%";
	if_ulp.style.left = "50%"; 
	if_ulp.style.marginLeft = "-351px";
	if_ulp.style.marginTop = "-212px"; 
	if_ulp.scrolling = "no";
	if_ulp.width = "715px";
	if_ulp.height = "450px";
	if_ulp.frameBorder = "0";
	if_ulp.style.border = "0px";
	if_ulp.style.zIndex = "500";
	if (document.getElementById("if_ulp")){
		(elem=document.getElementById("if_ulp")).parentNode.replaceChild(if_ulp);
	} else {
		document.getElementById('body').appendChild(if_ulp);
	}
}

function createPopup(name, url, width, height, closebuttonposition, scrolling, transparency, xOffset, yOffset, backgroundColor){
  var maxWidth = width;
  var maxHeight = height;
  var iframe = document.getElementById(name);
  var html = document.getElementsByTagName("html")[0];
  var border = 0;

  if (!xOffset) {
    xOffset = 0;
  }
  if (!yOffset) {
    yOffset = 0;
  }
  
	if (html.offsetHeight > 0 && html.offsetHeight < height){
	 height = html.offsetHeight - 40;
	 border = 1;
  }
	if (html.offsetWidth > 0 && html.offsetWidth < width){
	 width = html.offsetWidth - 40;
	 border = 1;
  }
  
  if (!iframe) {
    var docmode = 0;
    if(window.navigator.appName == "Microsoft Internet Explorer" && document.documentMode){
      docmode = parseInt(document.documentMode);
    }
    iframe = document.createElement("iframe");
    iframe.id = name;
    iframe.setAttribute("name", name);
    iframe.setAttribute("allowTransparency", transparency?"true":"false");
    iframe.setAttribute("width", width);
    iframe.setAttribute("height", height);
    iframe.setAttribute("frameborder", border);
    iframe.setAttribute("border", 0);
    iframe.setAttribute("scrolling", (scrolling==true?"yes":"no"));

    iframe.style.border = "0px";
    iframe.style.position = "absolute";

    if (docmode == 9){
      iframe.style.display = "block";
    } else {
      iframe.style.display = "table";
    }
    
    iframe.style.zIndex = "499";
    
    if (xOffset == 0){
      xOffset = 0.5;
    }
    
    if (xOffset > 0 && xOffset <= 1){
      iframe.style.left = String(Math.round(xOffset*100)) + "%";
      width = width * xOffset;  
    } else {
      iframe.style.left = String(xOffset) + "px"; 
      width = width - xOffset
    }

    if (yOffset == 0){
      yOffset = 0.5;
    }

    if (yOffset > 0 && yOffset <= 1){
      iframe.style.top = String(Math.round(yOffset*100)) + "%";
      height = height * yOffset;
    } else {
      iframe.style.top = String(yOffset) + "px";
      height = height - yOffset
    }
    
    iframe.style.backgroundColor = "transparent";
    if (backgroundColor) {
        iframe.style.backgroundColor = "#ffffff";
    }

    iframe.style.margin = "-"+Math.round(height)+"px 0 0 -" + Math.round(width)+"px";

    //iframe.style.backgroundColor= "#ffffff";

    body.insertBefore(iframe, body.lastChild);
    var closeButton = "";

    if (closebuttonposition > 0){ 
  	  var position = getOffset(iframe);
  	  closeButton = document.createElement('img');
      closeButton.src= (("https:" == document.location.protocol) ? "https://playagames.akamaized.net" : "http://img.playa-games.com") + "/res/legal/generic/close.png";
      closeButton.id = "popupCloseButton";
      closeButton.className = "popupCloseButton" + (closebuttonposition).toString();
      closeButton.className +=" id"+name;
      closeButton.style.zIndex = "500";
      closeButton.style.position = "absolute";
      closeButton.style.top = (position.top-20) + "px";
  	  if (closebuttonposition == 1){
        closeButton.style.left = (position.left-20) + "px";
  	  } else {
        closeButton.style.left = (parseInt(position.left)+parseInt(iframe.width)-20) + "px";
  	  }

      closeButton.style.cursor = "pointer";
      closeButton.onclick = function(){
  	    el = document.getElementById(name);
  	    if (el) {
  	     el.parentNode.removeChild(el);
  	    }
  	    el = this;
  	    el.parentNode.removeChild(el);
  	    location.hash = 0;
  	  }
  	  body.insertBefore(closeButton, body.lastChild);
    }
    popupIframe = {"iframe": iframe, "closeButton": closeButton, "name":name, "width": maxWidth, "height": maxHeight}; 
  }
  iframe.src=url;
  location.hash = 2;
}

function createTextPopup(name, text, width, height, closebuttonposition){
  var div = document.getElementById(name);
  var maxWidth = width;
  var maxHeight = height;
  if (!div) {
    var docmode = 0;
    if(window.navigator.appName == "Microsoft Internet Explorer" && document.documentMode){
      docmode = parseInt(document.documentMode);
    }
    div = document.createElement("div");
    div.id = name;
    div.setAttribute("name", name);

    div.style.border = "0px";
    div.style.position = "absolute";
    div.style.width = width;
    div.style.height = height;
    div.style.color = "#000000"
    div.style.backgroundColor="#ffffff"

    div.style.display = "block";
    div.style.padding = "10px"
    
    setOpacity(div, .8);

    
    if (docmode == 9){
//      div.style.display = "block";
    } else {
//      div.style.display = "table";
    }
    
    div.style.zIndex = "499";
    //div.style.top = "50%";
    div.style.bottom = "5px";
    div.style.left = "50%";
    div.style.margin = "-"+Math.round(height/2)+"px 0 0 -" + Math.round(width/2)+"px";
		div.appendChild(document.createTextNode(text));
    body.insertBefore(div, body.lastChild);
    var closeButton = "";
	  if (closebuttonposition > 0){ 
		  var position = getOffset(div);
		  closeButton = document.createElement('img');
	    closeButton.src=(("https:" == document.location.protocol) ? "https://playagames.akamaized.net" : "http://img.playa-games.com") + "/res/legal/generic/close.png";
	    closeButton.id = "popupCloseButton" + name;
	    closeButton.className = "popupCloseButton" + (closebuttonposition).toString();
	    closeButton.className +=" id"+name;
	    closeButton.style.zIndex = "500";
	    closeButton.style.position = "absolute";
	    closeButton.style.top = (position.top-20) + "px";
		  if (closebuttonposition == 1){
	      closeButton.style.left = (position.left-20) + "px";
		  } else {
	      closeButton.style.right = (position.left-20) + "px";
		  }
	
	    closeButton.style.cursor = "pointer";
	    closeButton.onclick = function(){
		    el = document.getElementById(name);
		    el.parentNode.removeChild(el);
		    el = this;
		    el.parentNode.removeChild(el);
	      location.hash = 0;
		  }
		  var textPopupTimeout = window.setTimeout(function(){
		    el = document.getElementById(name);
		    if(el){
		      el.parentNode.removeChild(el);
		    }
		    el = document.getElementById("popupCloseButton" + name);
		    if (el){
		      el.parentNode.removeChild(el);
		    }
			}, 3000)
		  
		  body.insertBefore(closeButton, body.lastChild);
	  }
    popupIframe = {"iframe": iframe, "closeButton": closeButton, "name":name, "width": maxWidth, "height": maxHeight}; 
    location.hash = 2;

  }

}

function loadJs(file, params, callbacks, overwrite){

  if (overwrite || !jsloader[file]){
    jsloader[file] = document.createElement('scr' + 'ipt');
    
  }
  jsloader[file].setAttribute("type","text/javascript");
  jsloader[file].setAttribute("sr" + "c", file + "?" + params);
  jsloader[file].onreadystatechange = function(){
    if (this.readyState == 'complete'){
      callbacks["onComplete"].call();
    }
  }
  
  jsloader[file].onload = function(){callbacks["onComplete"].call();}
  
  if (typeof jsloader[file] != "undefined"){
    document.getElementsByTagName("head")[0].appendChild(jsloader[file]);
  }
}

function paymentAdyen(url){
	createPopup("paymentAdyen", url, 700, 680, 2, true, false, 0, 0, "#ffffff");
}
function paymentSponsorpay(url){
	createPopup("paymentSponsorpay", url, 550, 400, 1, true, false);
}
function paymentBoku(url){
	createPopup("paymentBoku", url, 550, 400, 2, true, true, 0, 0, "#ffffff");
}
function paymentDaopay(url){
	createPopup("paymentDaopay", url, 550, 400, 2, false, false, 0, 0, "#ffffff");
}
function paymentZong(urlParams){
  var path = window.location.pathname;
  if (window.location.pathname.lastIndexOf('/') >= 0){ 
    path = window.location.pathname.substr(0, window.location.pathname.lastIndexOf('/') + 1);
  }

	var url = (("https:" == document.location.protocol) ? "https" : "http") + "://" + window.location.hostname + path + "/payment/zong.php?" + urlParams;
	createPopup("paymentZong", url, 490, 350, 2, false, false);
}
function paymentBoacompra(urlParams){
	var url = urlParams;
	createPopup("paymentBoacompra", url, 1024, 700, 2, true, true);
}
function paymentMicropaymentLastschrift(urlParams){
	var url = urlParams;
	createPopup("paymentMicropaymentLastschrift", url, 900, 640, 2, true, false);
}
function paymentMicropaymentCreditcard(urlParams){
	var url = urlParams;
	createPopup("paymentMicropaymentCreditcard", url, 900, 640, 2, true, false);
}

function paymentMicropaymentOnlinetransfer(urlParams){
	var url = urlParams;
	createPopup("paymentMicropaymentOnlinetransfer", url, 900, 640, 2, true, false);
}

function paymentMicropaymentIVR(urlParams){
	var url = urlParams;
	createPopup("paymentMicropaymentIVR", url, 900, 640, 2, true, false);
}

function paymentWiretransfer(url){
	createPopup("paymentWiretransfer", url, 900, 640, 2, false, false);
}

function getUrlParameters(url) {
   var params = url.replace(/^[^?]+/, "");
   var params_tmp = {};
   params = params.replace(/^[&?]+/, "").split("&");

   for(key in params) {
     tmp = params[key].split("=");
     params_tmp[tmp[0]] = tmp[1];
   }
   return params_tmp;
}

function paymentPaymentwall(url){
  var params = getUrlParameters(url);
  var width = 750;
  var height = 800;

  if (params.width) {
    width = params.width;
  }

  if (params.height) {
    height = params.height
  }
  
  createPopup("paymentPaymentwall", url, width, height, 2, true, false);
}

function facebookLikeFn(){
  var path = window.location.pathname;
  if (window.location.pathname.lastIndexOf('/') >= 0){ 
    path = window.location.pathname.substr(0, window.location.pathname.lastIndexOf('/') + 1);
  }

	var url = (("https:" == document.location.protocol) ? "https" : "http") + "://" + window.location.hostname + path + "/like.php?project=" + project;
	createPopup("facebookLike", url, 500, 382, 0, false, false, 0.6);
}

function twitterFn(){
  var path = window.location.pathname;
  if (window.location.pathname.lastIndexOf('/') >= 0){ 
    path = window.location.pathname.substr(0, window.location.pathname.lastIndexOf('/') + 1);
  }

	var url = (("https:" == document.location.protocol) ? "https" : "http") + "://" + window.location.hostname + path + "/like.php?type=twitter&project=" + project;
	createPopup("twitterLike", url, 500, 382, 0, false, false, 0.6);
}


function sso_get_email() {
  console.log("sso_api_user_email", sso_api_user_email);
  return sso_api_user_email;
}

function sso_get_uid() {
  console.log("sso_api_user_uid", sso_api_user_uid);
  return sso_api_user_uid;
}

// context, countryCode, languageCode, playerid 
function showSupport(context, country, language, playerid, serverid, username, paymentid) {
  var path = window.location.pathname;
  if (window.location.pathname.lastIndexOf('/') >= 0){ 
    path = window.location.pathname.substr(0, window.location.pathname.lastIndexOf('/') + 1);
  }
  var urlParams = "playerid="+playerid+"&serverid="+serverid+"&playername="+encodeURIComponent(username);//+"&style=2";
	var url = (("https:" == document.location.protocol) ? "https" : "http") + "://" + window.location.hostname + path + "/support?" + urlParams;
  //createPopup("support", url, 800, 600, 0, true, false);
  window.open(url, '_blank');
}

function showForum() {
  window.open(forum_url, '_blank');
}

function showManual() {
  window.open(manual_url, '_blank');
}

// context, country, lang, playerid, serverid
function showLegal(context, country, language, playerid, serverid) {
  var path = window.location.pathname;
  if (window.location.pathname.lastIndexOf('/') >= 0){ 
    path = window.location.pathname.substr(0, window.location.pathname.lastIndexOf('/') + 1);
  }
  var type = "imprint";
  switch (context.toLowerCase()){
    case "tos":
    case "terms":
      type = "terms";
      break;
    case "privacy":
      type = "privacy";
      break;
    case "imprint":
    default:
      type = "imprint";
      break;
  }
  var urlParams = "type="+type+"&lang=" + language + "&country=" + country;
	var url = (("https:" == document.location.protocol) ? "https" : "http") + "://" + window.location.hostname + path + "/legal/index_new.php?" + urlParams;
  createPopup("legal", url, 520, 600, 0, true, false, 0.6);
}

function verifyFacebookPayment(data) {
  if(!data) {
    alert("There was an error processing your payment. Please try again!");
    return;
  }
  
  if (data.error_message) {
    alert(data.error_message);
    return;
  }

  // IMPORTANT: You should pass the data object back to your server to validate
  // the contents, before fulfilling the order.

  console.log(data);
}

function paymentFacebook(params) {
  var params = params.replace(/^[&?]+/, "").split("&");
  var params_tmp = {};
  
  for(key in params) {
    tmp = params[key].split("=");
    params_tmp[tmp[0]] = tmp[1];
  }
  params = params_tmp;
  //console.log(params);
  requestID = params.userid + "_" + params.coins + "_" + params.amount + "_" + params.currency + "_d" + Date.now();
  quantity = params.coins;
  quantity_min = Math.round(parseInt(params.coins)-(params.coins * 0.15));
  quantity_max = Math.round(parseInt(params.coins)+(params.coins * 0.15));
  
  console.log(quantity_min, quantity_max);
  
  FB.ui({
        method: 'pay',
        action: 'purchaseitem',
        product: 'https://w1.sfgame.net/payment/facebook_payment.php',
        request_id: requestID
        /*,
        quantity: params.coins,
        quantity_min: 1,
        quantity_max: 3600
        */
      },
      verifyFacebookPayment
    );
  return;
}

function closeIframe(name) {
    el = document.getElementById(name);
    if (!el) {
      return;
    }
    el.parentNode.removeChild(el);
    location.hash = 0;
}

window.onhashchange = function() {
  if (popupIframe.closeButton) {
    var hash = 0;
    if (location.hash){
      hash = parseInt(location.hash.replace('#',''));
    }
    if (hash == 0 || !hash){
	    var el = document.getElementById(popupIframe.name);
	    if (el && el.parentNode){
	     el.parentNode.removeChild(el);
	    }
	    el = popupIframe.closeButton;
	    if (el && el.parentNode){
	     el.parentNode.removeChild(el);
	    }
    } 
  }
}
if (body && html) {
  body.onload = function () {
    if (window.location.search.match(/type=facebook/)){
      console.log("Running inside Facebook!");
    }

    if (isIframe) {
      var div1 = document.createElement("div");
      var text = document.createTextNode(document.title);
      div1.setAttribute("id", "title");
      //div1.style.color = "#ffffff";
      div1.style.height = "20px";
      div1.style.width = "100%";
      div1.style.textAlign = "center";
      div1.style.display = "block";
      div1.style.zIndex = "1000";
      div1.style.top = "0";
      div1.style.left = "0";
      div1.style.fontFamily = "arial, helvetica, sans-serif";
      div1.appendChild(text);
      body.insertBefore(div1, body.lastChild);
      body.style.height = (html.offsetHeight - 20) + "px";
      set_title(document.title);
    }
  };
  window.onresize = function () {
    if (isIframe) {
      var body = document.getElementsByTagName("body")[0];
      var html = document.getElementsByTagName("html")[0];
      body.style.height = (html.offsetHeight - 20) + "px";
    }
    var closeButton = document.getElementById("popupCloseButton");
    if (closeButton){
      var classNames = closeButton.className.split(' ');
      var popupElementId = "";
      var closeButtonClass = "";
      for ( var i in classNames){
        if (classNames[i].match(/^id.+/)){
          popupElementId = classNames[i].substr(2);
        } else if (classNames[i].match(/^popupCloseButton[0-9]+/)){
          closeButtonClass = classNames[i];
        }
      }

      if (popupElementId == ""){
        return;
      }

      var popupElement = document.getElementById(popupElementId);
      if (!popupElement){
        return;
      } else if(popupIframe){
        var width = popupIframe.width;
        var height = popupIframe.height;

        var html = document.getElementsByTagName("html")[0];
        if (html.offsetHeight > 0 && html.offsetHeight < height){
          height = html.offsetHeight - 40;
          popupIframe.iframe.setAttribute("frameborder", 1);
          popupIframe.iframe.setAttribute("scrolling", "yes");
        }
        if (html.offsetWidth > 0 && html.offsetWidth < width){
          width = html.offsetWidth - 40;
          popupIframe.iframe.setAttribute("frameborder", 1);
          popupIframe.iframe.setAttribute("scrolling", "yes");
        }
        popupIframe.iframe.setAttribute("width", width);
        popupIframe.iframe.setAttribute("height", height);
        popupIframe.iframe.style.margin = "-"+Math.round(height/2)+"px 0 0 -" + Math.round(width/2)+"px";

      }

      var position = getOffset(popupElement);
      closeButton.style.top = (position.top-20) + "px";
      switch (closeButtonClass){
        case "popupCloseButton1":
          closeButton.style.left = (position.left-20) + "px";
          break;
        case "popupCloseButton2":
          closeButton.style.left = (parseInt(position.left)+parseInt(popupElement.width)-20) + "px";
          break;
        default:
          return;
      }
    }
  }
}
