var localize;
  var loc = "../languages/localize.php?part=installation";
  //console.log("Request to: "+loc);
  var getLang = $.get({
    url: loc
  });
	getLang.fail(function(){
		console.log("Localization Error!");
	});
	getLang.done(function(data){
		localize = JSON.parse(data);
    welcomeText();
  });
