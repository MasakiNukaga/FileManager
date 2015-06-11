var bc = ["#FFFFFF","#00BFFF","#7FFF00","#FFFF00","#000000","#FF0000"];
var fs = ["15px","20px","25px"];

function chBackGround(e) {
	e.style.color = bc[e.selectedIndex];
	e.style.backgroundColor = bc[e.selectedIndex];
}

function changeFileColor(e,number) {
	var id = "#fav"+number;
	$(id).css('background-color',bc[e.selectedIndex]);
}

function changeFontSize(e,number) {
	var file = ".file"+number;
	$(file).css('font-size',fs[e.selectedIndex]);
}

function changeFav(e) {
	if(e.style.color == 'white'){
		e.style.color = 'Gold';
	}else{
		e.style.color = 'white';
	}
}

$(function() {
	$('.color').css("background-color", bc[0]);
	$('.color').css("color", bc[0]);
});
