var localize;
  if(typeof part == "undefined") localize = false;
  if(typeof $.cookie("el_lang") == "undefined") var curlang = "en-us";
    else var curlang = $.cookie("el_lang");
  var curURL = $(location).prop("href");
  //console.log("Current URL: "+curURL);
  if(curURL.indexOf("/admin/") >= 0){
    var part = "admin";
    var path = "../"
  } else {
    var part = "main";
    var path = "./";
  }
  var loc = path+"languages/localize.php?part="+part;
  //console.log("Request to: "+loc);
  var getLang = $.get({
    url: loc
  });
	getLang.fail(function(){
		console.log("Localization Error!");
	});
	getLang.done(function(data){
		localize = JSON.parse(data);
  });
