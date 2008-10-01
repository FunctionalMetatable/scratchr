function showMenu(id_menu){
var my_menu = document.getElementById(id_menu);
if(my_menu.style.display=="none" || my_menu.style.display==""){
	my_menu.style.display="block";
	} else { 
	my_menu.style.display="none";
	}
}
function swapImage(idStatus){
	if(idStatus==0){
		document.arrow_profile.src ="arrow_hover.png";
	} else if(idStatus==1){
		document.arrow_profile.src ="arrow_select.png";
	} else if(idStatus==2){
		document.arrow_profile.src ="arrow.png";
	}
	
}