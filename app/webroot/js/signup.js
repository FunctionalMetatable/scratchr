 jQuery(document).ready(function() {
   jQuery("#UserUsername").click(function() {
     showInfo("name");
   });
   jQuery("#UserUsername").blur(function() {
     hideInfo("name");
   });
   
   jQuery("#UserPassword").click(function() {
     showInfo("password");
   });
   jQuery("#UserPassword").blur(function() {
     hideInfo("password");
   });
   
   jQuery("#UserPassword2").click(function() {
     showInfo("confirmation");
   });
   jQuery("#UserPassword2").blur(function() {
     hideInfo("confirmation");
   });
   
   jQuery("#UserBmonth").click(function() {
     showInfo("birthdate");
   });
   jQuery("#UserBmonth").blur(function() {
     hideInfo("birthdate");
   });
   
   jQuery("#UserByear").click(function() {
     showInfo("birthdate");
   });
   jQuery("#UserByear").blur(function() {
     hideInfo("birthdate");
   });
   
   jQuery("#UserEmail").click(function() {
     showInfo("email");
   });
   jQuery("#UserEmail").blur(function() {
     hideInfo("email");
   });
   
   jQuery("#UserGender").click(function() {
     showInfo("gender");
   });
   jQuery("#UserGender").blur(function() {
     hideInfo("gender");
   });
   
   jQuery("#UserCountry").click(function() {
     showInfo("country");
   });
   jQuery("#UserCountry").blur(function() {
     hideInfo("country");
   });
   
   jQuery("#UserState").click(function() {
     showInfo("state");
   });
   jQuery("#UserState").blur(function() {
     hideInfo("state");
   });
   
   jQuery("#UserProvince").click(function() {
     showInfo("state");
   });
   jQuery("#UserProvince").blur(function() {
     hideInfo("state");
   });
   
   jQuery("#UserCity").click(function() {
     showInfo("city");
   });
   jQuery("#UserCity").blur(function() {
     hideInfo("city");
   });
 });
 
function showInfo(target) {
	var divID = "signup_info_" + target;
	var errorID = "signup_error_" + target;
	if ($(divID)) {
		$(divID).style.display = "block";
	}
	if ($(errorID)) {
		$(errorID).style.display = "none";
	}
}

function hideInfo(target) {
	var divID = "signup_info_" + target;
	if ($(divID)) {
		$(divID).style.display = "none";
	}
}

function changeEmailVis(){
	// quit if no year has been selected
	if (document.getElementById('UserByear').selectedIndex < 1)
		return false;
	var age = getAge();
	if (document.getElementById){ 
		obj = document.getElementById('email_under_13'); 
		obj2 = document.getElementById('email_default'); 
	}		
	if (age < 13) {
		//then ask for parent's email
		obj.style.display = '';
		obj2.style.display = 'none';
	}else {
		//then ask for user's email
		obj.style.display ='none';
		obj2.style.display = '';
	}
} 

function getAge() {
	var curryear = document.downform.curryear.value;
	var currmonth = parseInt(document.downform.currmonth.value);
	elyear = document.getElementById('UserByear');
	elmonth = document.getElementById('UserBmonth');
	var byear = elyear.options[elyear.selectedIndex].value; 
	var bmonth = parseInt(elmonth.options[elmonth.selectedIndex].value);
	

	var age = curryear - byear;
	if (age == 13) {
		if (bmonth >= currmonth) {
			return 12;
		} else {
			return 13;
		}	
	}
	return age;
}