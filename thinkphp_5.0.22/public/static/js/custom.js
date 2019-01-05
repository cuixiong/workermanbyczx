$(function(){
	$(".accordion a:first-child").addClass("active");
	$(".accordion a:first-child").next(".nav").slideDown(100);
	
	$(".accordion a").click(function(){
		clearActive(this);
	});
});

var clearActive=function(object){
	if($(object).attr("class")!="active"){
		$(object).parent().find("a").each(function(){
			if($(this).attr("class")=="active"){
				$(this).removeClass("active");
				$(this).next(".nav").slideUp(100);
				$(this).find(".prompt").css("background","#665e51");
			}
		});
		$(object).addClass("active");
		$(object).find(".prompt").css("background","#B77F24");
		$(object).next(".nav").slideDown(100);
	}
}