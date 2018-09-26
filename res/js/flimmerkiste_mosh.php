var flimmerkisteMothership = new function() {
    var libLoadState = "uninitialized";
    var api = null;
    var userid = "";
    var gender = "m";
    var lastCheck = 0;
    var offerState = 0; // 0 = init; 1 = offer; 2 = no offer
    var videoID = 0;
    
    var getCookie = function(name){
      var i, n, v, cookies=document.cookie.split(";");
  
      for (i=0;i<cookies.length;i++){
        n = cookies[i].substr(0, cookies[i].indexOf("="));
        v = cookies[i].substr(cookies[i].indexOf("=") + 1);
        n = n.replace(/^\s+|\s+$/g,"");
        if (n == name){
          return unescape(v);
        }
      }
      return "";
    }
    var setCookie = function (name,value){
      var d = new Date();
      d = new Date(d.getTime() + 1000 * 60 * 60 * 24 * 1); 
      document.cookie=name + "=" + escape(value) + "; expires=" + d.toGMTString()+";";
    }
    var loadApi = function(){
      if (api){
        return;
      }
      api=mship_video;
      backgroundLoad();
    };
    var loadLibs = function(){
      if (libLoadState != "uninitialized"){
        return;
      }
      
      libLoadState = "initializing";
      loadJs("http://video.mship.de/video.php", "s=1&r="+Math.floor(Math.random()*99999999999), {
        "onComplete": function (){
          libLoadState = "initialized";
          loadApi();
        }
      }, false);
    };
    var backgroundLoad = function(){
      if (!api){
        return;
      }
      if (offerState == 1){
        return;
      }
      if (!lastCheck){
        lastCheck = getCookie("to_flimmerkiste");
      }
      if (!lastCheck){
        lastCheck = new Date().getTime();
        setCookie("to_flimmerkiste", lastCheck);
      } else if (new Date().getTime() - lastCheck > flimmerkistePoll * 1000){

        lastCheck = new Date().getTime();
        setCookie("to_flimmerkiste", lastCheck);
      } else {
        return;
      }
      
      loadJs("payment/mosh.php", "?rnd=" + Math.floor(Math.random()*99999999999), {
          "onComplete": function (){
            if (moshid == "") {
              return false
            } 
            var moshvideoid = moshid + "&uid=" + instance.userid + "_" + getUniqueId(5) + "&gender=" + instance.gender ;
            api.video_request({
              onAvailable: function(id) {
                videoID = id;
                offerState = 1;
              },
              onNotAvailable: function(){
                offerState = 2;
              }
            }, moshvideoid);
            
          }
        }, true);
      
    }
    this.initialize = function(userid, gender){
      if (!api){
        this.userid = userid;
        this.gender = (gender==2)?"f":"m";
        loadLibs();
      } else {
        backgroundLoad()
      }
    };
    this.isAvailable = function(){
      if (offerState == 1){
        return 1;
      }
      return 0;
    };
    this.show = function () {
      if (offerState == 1){
        api.video_play({
          onVideoEnd: function() {
            videoID = 0;
            offerState = 0;
          }
        }, videoID);
      }
    };
    var instance = this;
}

function paymentFlimmerkiste2(mode, playerid, gender){
  switch (mode){
    case "requesttv":
      flimmerkisteMothership.initialize(playerid, gender);
      if (flimmerkisteMothership.isAvailable()){
        return 1;
      }
      break;
    case "showtv":
      if (flimmerkisteMothership.isAvailable()){
        flimmerkisteMothership.show();
      }
      break;
  }
  return 0;
}

function paymentMothership(playerid){
  flimmerkisteMothership.initialize(playerid, "m");
  if (flimmerkisteMothership.isAvailable()){
    flimmerkisteMothership.show();
  } else {
    var text = "Sorry, no offer available";
    if (strings["brandengage.no_offers"]){
      text = strings["brandengage.no_offers"];
    }
    createTextPopup("brandengageNoOffers", text, 200, 40, 1);
  }
  return 0;
}

