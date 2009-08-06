<?php
class UsersController extends AppController {

	var $components = array('PaginationTernary','PaginationSecondary', 'Pagination','RequestHandler','FileUploader','Email','Thumb');
	var $helpers = array('Pagination', 'Ajax', 'Javascript');
	var $uses = array('IgnoredUser', 'KarmaRating', 'GalleryProject', 'Flagger', 'Lover', 'Gcomment', 'Mpcomment', 'Mgcomment', 'Tag', 
	'ProjectTag', 'GalleryTag', 'MgalleryTag', 'MprojectTag', 'FeaturedProject','AdminComment', 'User','Project','Favorite', 'Pcomment',
	'UserStat', 'Relationship', 'RelationshipType', 'Theme', 'GalleryMembership', 'Gallery',  'ThemeRequest', 'FriendRequest', 'Notification',
	'Shariable','Thank', 'ViewStat','Curator','WhitelistedIpAddress','BlockedIp');
	

	function admin_index() {
	  if (!$this->isAdmin())
		$this->__err;

	  $this->set('users',$this->User->findAll());
	  $this->set('projects', $this->Project->findAll());
	  $this->set('comments', $this->Pcomment->findAll());
	}


	function beforeFilter() {
	  // Check for active session and valid action\
	  $action = $this->params['action'];
	  if ($this->activeSession())
		if ($action == 'login' || $action == 'signup')
			$this->redirect('/');


	  // remove all add to gallery cookies
	  // this probably should be done through javascript because it wouldn't slow
	  // things down, and because server time() is not equal to client time()
	  // ...oh well, will do for now by setting date far enough in the past.
	  // modified: now using javascript

	  /*
	  $cookie_subname = "projectselections";
	  foreach ($_COOKIE as $cookie => $value)
	  {
	    $cookie_array = explode("_", $cookie);
		if (!empty($cookie_array[1]) && $cookie_array[1] === $cookie_subname)
		{
			$val = setcookie($cookie, "", time() - 90000, "/");
		}
	  }
	  */

	  // trim all form data
	  parent::beforeFilter();
	  $content_status = $this->getContentStatus();
	  $this->set('content_status', $this->getContentStatus());
	}


	function __err() {
	  $this->render('uerror');
	  die;
	}
	
	function multiaccountwarn() {
		$this->pageTitle = ___("Scratch | Signup", true);
        $client_ip = $this->RequestHandler->getClientIP();
		$client_ip_long = ip2long($this->RequestHandler->getClientIP());

        $ip_whitelisted = $this->WhitelistedIpAddress->hasAny("WhitelistedIpAddress.ipaddress = $client_ip_long");
		$ip_blocked = false;
		$user_blcoked = false;

		if(!$ip_whitelisted){
			//find if there's any blocked user from this ip
			$ip_blocked = $this->User->find("User.ipaddress = $client_ip_long and User.status = 'locked'",
												array(), 'User.timestamp DESC');
	
			//check if any blocked user used this ip in last one month
			$user_blocked = $this->__checkLockedUser();
		}
        
        if(!$ip_whitelisted && ($ip_blocked || $user_blocked)) {
            //store to session
            if($user_blocked) {
                $this->redirect('/users/us_banned/'.$user_blocked);
            }
            else {
                $this->redirect('/users/us_banned');
            }
            exit;
        }
        
		/* First we find if the IP has been used before or not, if not then simply redirect him to sign up page */
		$creation_from_same_ip = $this->User->hasAny("User.ipaddress = $client_ip_long");
		$access_from_same_ip = $this->ViewStat->hasAny("ViewStat.ipaddress = INET_ATON('$client_ip')");
		
		/* Some Activity from Same IP in past */
		if(($creation_from_same_ip || $access_from_same_ip) && !$ip_whitelisted) {
	
			/* Get All the users who have accessed the projects or created a profile using same IP */
			$view_stats = $this->ViewStat->findAll("ViewStat.ipaddress = INET_ATON('$client_ip')", 'DISTINCT user_id');
			$user_ids_accessing_same_ip = array();
			foreach($view_stats as $view_stat) {
				array_push($user_ids_accessing_same_ip, $view_stat['ViewStat']['user_id']); 
			}

            $user_ids_accessing_same_ip = implode(',', $user_ids_accessing_same_ip);
            
			if(!empty($user_ids_accessing_same_ip)) {
                $user_records = $this->User->findAll("User.ipaddress = $client_ip_long or User.id in ($user_ids_accessing_same_ip)",'urlname','created DESC');
            }
			else {
                $user_records = $this->User->findAll("User.ipaddress = $client_ip_long ",'urlname','created DESC');
            }
            
			/* Some activity in past from same IP so we need to show the message with user records and ipaddress (send them to view) */
			$this->set('user_records', $user_records);
			$this->set('ip_address',$client_ip);
		}
		
		/* Same IP never used before */
		else {
			$this->redirect('/signup');
		}
	}

	function signup() {
				
		$this->pageTitle = ___("Scratch | Signup", true);
        $client_ip = $this->RequestHandler->getClientIP();
        $client_ip_long = ip2long($client_ip);
        $server_ip = ip2long($_SERVER['SERVER_ADDR']);
        $local_ip = 2130706433;
        
		$user_data = $this->data;
		$errors = Array(); 
		$signup_interval = SIGNUP_INTERVAL;

        $ipNotAllowed =false;
        //server is not localhost and client ip is 127.0.0.1
		if($server_ip != $local_ip && $client_ip_long == $local_ip)
		{
			$ipNotAllowed =true;
			$this->setFlash(___("We are unable to identify your IP address. Please  <a href='/contact/us/'>contact us</a>.", true));
		}
				
		$creation_from_same_ip = $this->User->hasAny("User.ipaddress = $client_ip_long");
		$access_from_same_ip = $this->ViewStat->hasAny("ViewStat.ipaddress = INET_ATON('$client_ip')");
        
		/* Some Activity from Same IP in past */
		if($creation_from_same_ip || $access_from_same_ip) {
			/* Get All the users who have accessed the projects or created a profile using same IP */
			$view_stats = $this->ViewStat->findAll("ViewStat.ipaddress = INET_ATON('$client_ip')", 'DISTINCT user_id');

            $user_ids_accessing_same_ip = array();
			foreach($view_stats as $view_stat) {
				array_push($user_ids_accessing_same_ip, $view_stat['ViewStat']['user_id']); 
			}
			$user_ids_accessing_same_ip = implode(',', $user_ids_accessing_same_ip);
			
			if(!empty($user_ids_accessing_same_ip)) {
                $user_records = $this->User->findAll("User.ipaddress = $client_ip_long or User.id in ($user_ids_accessing_same_ip)",'urlname','created DESC');
            }
            else {
                $user_records = $this->User->findAll("User.ipaddress = $client_ip_long ",'urlname','created DESC');
            }
			/* Some activity in past from same IP so we need to show the message with user records and ipaddress (send them to view) */
			$this->set('user_records', $user_records);
			$this->set('ip_address', $client_ip);
				
			if(isset($_SERVER['HTTP_REFERER']))
			{
			 	$referers = $_SERVER['HTTP_REFERER'];
				$referer_array = explode('/',$referers);
				$refer_from =$referer_array['3'];
			}	
			if(!isset($referers)&& $refer_from!="multiaccountwarn")
			$this->redirect('/multiaccountwarn');
		
		}
		
		$this->set('username_error', ___('username must be at least 3 letters and/or numbers, no spaces', true));
		if(!empty($this->data['User'])) {
			$this->data['User']['username']  = str_replace(" ", "", $this->data['User']['username']);
			if(strlen($this->data['User']['username']) < 3 || strlen($this->data['User']['username']) > 19) {
				$this->User->invalidate('username');
				$errors['name_length'] = ___('Username must be between 3 to 20 characters', true);
			}
			
			if (eregi("^[a-z0-9_\-]+$", $this->data['User']['username'])) {
			} else {
				$this->User->invalidate('username');
				$errors['name_characters'] = ___('Username cannot contain special characters or spaces except _ and -', true);
			}
			
			if($this->User->findByUsername($this->data['User']['username'])) {
				$this->User->invalidate('username');
				$errors['name_taken'] = ___('Username is taken', true);
			}
			
			if (isInappropriate($this->data['User']['username'])) {
				$this->User->invalidate('username');
				$errors['name_invalid'] = ___('Invalid username', true);
			}
			
			if(strlen($this->data['User']['password']) < 6) {
				$this->User->invalidate('password');
				$errors['password_length'] =  ___('Password too short', true);
			}
			
			if (strcmp($this->data['User']['password'], $this->data['User']['password2']) == 0) {
			} else {
				$this->User->invalidate('password2');
				$errors['password_confirmation'] = ___("Passwords don't match", true);
			}
			$email = $this->data['User']['email'];
			$byear = $this->data['User']['byear'];
			$bmonth = $this->data['User']['bmonth'];
			$gender = $this->data['User']['gender'];
			$country = $this->data['User']['country'];
			
			if ($byear == 0 || $bmonth == 0) {
				$this->User->invalidate('bmonth');
				$this->User->invalidate('byear');
				$errors['birthdate_invalid'] =  ___('You must enter a valid birthdate', true);
			}
			
			if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			} else {
				$this->User->invalidate('email');
				$errors['email_invalid'] =  ___('Invalid email address', true);
			}
			
			if ($gender == "") {
				$this->User->invalidate('gender');
				$errors['gender_empty'] =  ___('You must select your gender', true);
			}
			
			if ($country == "") {
				$this->User->invalidate('country');
				$errors['country_empty'] =  ___('You must select your country', true);
			}
				
			$age = date("Y") - $this->data['User']['byear'];

			if ($this->User->validates($this->data['User']) && !$ipNotAllowed) {
				$this->data['User']['password'] = sha1($this->data['User']['password']);

				//These fields don't have default values and should be validated
				$this->data['User']['villager'] = 0;
				$this->data['User']['firstname'] = 'test';
				$this->data['User']['lastname'] = 'test';

				 //at some point we though of having a urlname
				 $this->data['User']['urlname'] =  $this->data['User']['username'];
				 $this->data['User']['ipaddress'] = $client_ip_long;
				 
					if ($this->User->save($this->data['User'], false)) {
						$this->data['User']['id'] = $this->User->getLastInsertID();
						$this->setFlash(___("Welcome!", true) . " <a href='/pages/download'>" . ___('Download Scratch', true) . "</a>", FLASH_NOTICE_KEY);
						$saved_user_id = $this->data['User']['id'];
						$user_record = $this->User->find("User.id = $saved_user_id");
						$this->Session->write('User', $user_record['User']);
						$this->redirect('/users/'.$this->data['User']['username']);
					} else {
						$this->data['User']['password'] = '';
						$this->data['User']['password2'] = '';
						$this->setFlash(___("could not save information, try again", true), FLASH_ERROR_KEY);
					}
				
				
			} else {
				if($this->data['User']['email'] == 'rather-not-say@scratchr.org') {
					$this->data['User']['email'] = '';
				}
				$this->validateErrors($this->User);
			}
		}
		$this->set('ipNotAllowed',$ipNotAllowed);
		$this->set_signup_variables();
		$this->set('errors', $errors);
		$this->render('signup');
	}
	
	function set_signup_variables() {
		$months = Array(0 => "", 1 => ___('January', true), 
						2 => ___('February', true), 3 => ___('March', true), 
						4 => ___('April', true), 5 => ___('May', true), 6 => ___('June', true), 
						7 => ___('July', true), 8 => ___('August', true), 9 => ___('September', true), 
						10 => ___('October', true), 11 => ___('November', true), 12 => ___('December', true));
						
		$years = Array();
		$curryear = date("Y"); 
		$years[0] = '';
		for($i = $curryear; $i >= $curryear - 128; $i--) {
			$years[$i] = $i;
		}
		$genders = Array('' => '', 'female' => ___('female', true), 'male' => ___('male', true));
		
		$countries = Array(''=>'', "Afghanistan"=>___("Afghanistan", true),  
							"Albania"=>___("Albania", true),  "Algeria"=>___("Algeria", true),  
							"American Samoa"=>___("American Samoa", true),  "Andorra"=>___("Andorra", true),   
						"Angola"=>___("Angola", true),  "Anguilla"=>___("Anguilla", true),  "Antarctica"=>___("Antarctica", true),  
						"Antigua and Barbuda"=>___("Antigua and Barbuda", true),  "Argentina"=>___("Argentina", true),  
						"Armenia"=>___("Armenia", true),  "Aruba"=>___("Aruba", true),  
						"Australia"=>___("Australia", true),  "Austria"=>___("Austria", true),    
						"Azerbaijan"=>___("Azerbaijan", true),  "Bahamas"=>___("Bahamas", true),  
						"Bahrain"=>___("Bahrain", true),  "Bangladesh"=>___("Bangladesh", true),  
						"Barbados"=>___("Barbados", true),  "Belarus"=>___("Belarus", true),  
						"Belgium"=>___("Belgium", true),  "Belize"=>___("Belize", true),  
						"Benin"=>___("Benin", true),    "Bermuda"=>___("Bermuda", true),  
						"Bhutan"=>___("Bhutan", true),  "Bolivia"=>___("Bolivia", true),  
						"Bosnia and Herzegovina"=>___("Bosnia and Herzegovina", true), 
						"Botswana"=>___("Botswana", true),  "Bouvet Island"=>___("Bouvet Island", true),  
						"Brazil"=>___("Brazil", true),  "British Indian Ocean Territory"=>___("British Indian Ocean Territory", true),  
						"Brunei Darussalam"=>___("Brunei Darussalam", true),    "Bulgaria"=>___("Bulgaria", true),  
						"Burkina Faso"=>___("Burkina Faso", true),  "Burundi"=>___("Burundi", true),  
						"Cambodia"=>___("Cambodia", true),  "Cameroon"=>___("Cameroon", true),  
						"Canada"=>___("Canada", true),  "Cape Verde"=>___("Cape Verde", true),  
						"Cayman Islands"=>___("Cayman Islands", true),  "Central African Republic"=>___("Central African Republic", true),   
						"Chad"=>___("Chad", true),  "Chile"=>___("Chile", true),  "China"=>___("China", true),  
						"Christmas Island"=>___("Christmas Island", true),  "Cocos (Keeling) Islands"=>___("Cocos (Keeling) Islands", true),  
						"Colombia"=>___("Colombia", true),  "Comoros"=>___("Comoros", true),  "Congo"=>___("Congo", true),  
						"Congo, Dem. Rep. of The"=>___("Congo, Dem. Rep. of The", true),    "Cook Islands"=>___("Cook Islands", true),  
						"Costa Rica"=>___("Costa Rica", true),  "Cote D'ivoire"=>___("Cote D'ivoire", true),  "Croatia"=>___("Croatia", true),  
						"Cuba"=>___("Cuba", true),  "Cyprus"=>___("Cyprus", true),  "Czech Republic"=>___("Czech Republic", true),  
						"Denmark"=>___("Denmark", true),  "Djibouti"=>___("Djibouti", true),    "Dominica"=>___("Dominica", true),  
						"Dominican Republic"=>___("Dominican Republic", true),  "Ecuador"=>___("Ecuador", true),  
						"Egypt"=>___("Egypt", true),  "El Salvador"=>___("El Salvador", true),  
						"Equatorial Guinea"=>___("Equatorial Guinea", true),  "Eritrea"=>___("Eritrea", true),  
						"Estonia"=>___("Estonia", true),  "Ethiopia"=>___("Ethiopia", true),    
						"Falkland Islands (Malvinas)"=>___("Falkland Islands (Malvinas)", true),  
						"Faroe Islands"=>___("Faroe Islands", true),  "Fiji"=>___("Fiji", true),  
						"Finland"=>___("Finland", true),  "France"=>___("France", true),  "French Guiana"=>___("French Guiana", true),  
						"French Polynesia"=>___("French Polynesia", true),  "French Southern Territories"=>___("French Southern Territories", true),  
						"Gabon"=>___("Gabon", true),    "Gambia"=>___("Gambia", true),  
						"Georgia"=>___("Georgia", true),  "Germany"=>___("Germany", true),  
						"Ghana"=>___("Ghana", true),  "Gibraltar"=>___("Gibraltar", true),  "Greece"=>___("Greece", true),  
						"Greenland"=>___("Greenland", true),  "Grenada"=>___("Grenada", true),  "Guadeloupe"=>___("Guadeloupe", true),    
						"Guam"=>___("Guam", true),  "Guatemala"=>___("Guatemala", true),  "Guinea"=>___("Guinea", true),  
						"Guinea-bissau"=>___("Guinea-bissau", true),  "Guyana"=>___("Guyana", true),  "Haiti"=>___("Haiti", true),  
						"Holy See (Vatican City State)"=>___("Holy See (Vatican City State)", true),  "Honduras"=>___("Honduras", true),    
						"Hong Kong"=>___("Hong Kong", true),  "Hungary"=>___("Hungary", true),  "Iceland"=>___("Iceland", true),  
						"India"=>___("India", true),  "Indonesia"=>___("Indonesia", true),  
						"Iran, Islamic Republic of"=>___("Iran, Islamic Republic of", true),  "Iraq"=>___("Iraq", true),  
						"Ireland"=>___("Ireland", true),  "Israel"=>___("Israel", true),    "Italy"=>___("Italy", true),  
						"Jamaica"=>___("Jamaica", true),  "Japan"=>___("Japan", true),  "Jordan"=>___("Jordan", true),  
						"Kazakhstan"=>___("Kazakhstan", true),  "Kenya"=>___("Kenya", true),  "Kiribati"=>___("Kiribati", true), 
						"Korea, Dem. People's Rep."=>___("Korea, Dem. People's Rep.", true),  "Korea, Republic of"=>___("Korea, Republic of", true),    
						"Kuwait"=>___("Kuwait", true),  "Kyrgyzstan"=>___("Kyrgyzstan", true),  "Laos"=>___("Laos", true),  
						"Latvia"=>___("Latvia", true),  "Lebanon"=>___("Lebanon", true),  "Lesotho"=>___("Lesotho", true),  
						"Liberia"=>___("Liberia", true),  "Libyan Arab Jamahiriya"=>___("Libyan Arab Jamahiriya", true),  
						"Liechtenstein"=>___("Liechtenstein", true),    "Lithuania"=>___("Lithuania", true),  
						"Luxembourg"=>___("Luxembourg", true),  "Macao"=>___("Macao", true),  
						"Macedonia"=>___("Macedonia", true),  "Madagascar"=>___("Madagascar", true),  
						"Malawi"=>___("Malawi", true),  "Malaysia"=>___("Malaysia", true),  "Maldives"=>___("Maldives", true),  
						"Mali"=>___("Mali", true),    "Malta"=>___("Malta", true),  "Marshall Islands"=>___("Marshall Islands", true),  
						"Martinique"=>___("Martinique", true),  "Mauritania"=>___("Mauritania", true),  "Mauritius"=>___("Mauritius", true),  
						"Mayotte"=>___("Mayotte", true),  "Mexico"=>___("Mexico", true),  
						"Micronesia, Federated States of"=>___("Micronesia, Federated States of", true),  
						"Moldova, Republic of"=>___("Moldova, Repubc of", true),    "Monaco"=>___("Monaco", true),  
						"Mongolia"=>___("Mongolia", true),  "Montserrat"=>___("Montserrat", true),  "Morocco"=>___("Morocco", true),  
						"Mozambique"=>___("Mozambique", true),  "Myanmar"=>___("Myanmar", true),  
						"Namibia"=>___("Namibia", true),  "Nauru"=>___("Nauru", true),  "Nepal"=>___("Nepal", true),    
						"Netherlands"=>___("Netherlands", true),  "Netherlands Antilles"=>___("Netherlands Antilles", true),  
						"New Caledonia"=>___("New Caledonia", true),  "New Zealand"=>___("New Zealand", true),  
						"Nicaragua"=>___("Nicaragua", true),  "Niger"=>___("Niger", true),  "Nigeria"=>___("Nigeria", true),  
						"Niue"=>___("Niue", true),  "Norfolk Island"=>___("Norfolk Island", true),    
						"Northern Mariana Islands"=>___("Northern Mariana Islands", true),  "Norway"=>___("Norway", true), 
						"Oman"=>___("Oman", true),  "Pakistan"=>___("Pakistan", true),  "Palau"=>___("Palau", true),  "Palestine"=>___("Palestine", true),  
						"Panama"=>___("Panama", true),  "Papua New Guinea"=>___("Papua New Guinea", true),  "Paraguay"=>___("Paraguay", true),    
						"Peru"=>___("Peru", true),  "Philippines"=>___("Philippines", true),  "Pitcairn"=>___("Pitcairn", true),  
						"Poland"=>___("Poland", true),  "Portugal"=>___("Portugal", true),  "Puerto Rico"=>___("Puerto Rico", true),  
						"Qatar"=>___("Qatar", true),  "Reunion"=>___("Reunion", true),  "Romania"=>___("Romania", true),    
						"Russian Federation"=>___("Russian Federation", true),  "Rwanda"=>___("Rwanda", true),  
						"Saint Helena"=>___("Saint Helena", true),  "Saint Kitts and Nevis"=>___("Saint Kitts and Nevis", true),  
						"Saint Lucia"=>___("Saint Lucia", true),  "Saint Pierre and Miquelon"=>___("Saint Pierre and Miquelon", true),  
						"St. Vincent"=>___("St. Vincent", true),  "Samoa"=>___("Samoa", true),  "San Marino"=>___("San Marino", true),    
						"Sao Tome and Principe"=>___("Sao Tome and Principe", true),  "Saudi Arabia"=>___("Saudi Arabia", true),  
						"Senegal"=>___("Senegal", true),  "Serbia and Montenegro"=>___("Serbia and Montenegro", true),  
						"Seychelles"=>___("Seychelles", true),  "Sierra Leone"=>___("Sierra Leone", true),  
						"Singapore"=>___("Singapore", true),  "Slovakia"=>___("Slovakia", true),  "Slovenia"=>___("Slovenia", true),    
						"Solomon Islands"=>___("Solomon Islands", true),  "Somalia"=>___("Somalia", true),  
						"South Africa"=>___("South Africa", true),  "Spain"=>___("Spain", true),  "Sri Lanka"=>___("Sri Lanka", true),  
						"Sudan"=>___("Sudan", true),  "Suriname"=>___("Suriname", true),  
						"Svalbard and Jan Mayen"=>___("Svalbard and Jan Mayen", true),    "Swaziland"=>___("Swaziland", true),  
						"Sweden"=>___("Sweden", true),  "Switzerland"=>___("Switzerland", true),  
						"Syrian Arab Republic"=>___("Syrian Arab Republic", true),  
						"Taiwan"=>___("Taiwan", true),  
						"Tajikistan"=>___("Tajikistan", true),  
						"Tanzania, United Republic of"=>___("Tanzania, United Republic of", true),  "Thailand"=>___("Thailand", true),  
						"Timor-leste"=>___("Timor-leste", true),    "Togo"=>___("Togo", true),  "Tokelau"=>___("Tokelau", true),  
						"Tonga"=>___("Tonga", true),  "Trinidad and Tobago"=>___("Trinidad and Tobago", true),  "Tunisia"=>___("Tunisia", true),  
						"Turkey"=>___("Turkey", true),  "Turkmenistan"=>___("Turkmenistan", true),  
						"Turks and Caicos Islands"=>___("Turks and Caicos Islands", true),  "Tuvalu"=>___("Tuvalu", true),    
						"Uganda"=>___("Uganda", true),  "Ukraine"=>___("Ukraine", true),  
						"United Arab Emirates"=>___("United Arab Emirates", true),  "United Kingdom"=>___("United Kingdom", true),  
						"United States"=>___("United States", true),  "US Minor"=>___("US Minor", true),  "Uruguay"=>___("Uruguay", true),  
						"Uzbekistan"=>___("Uzbekistan", true),  "Vanuatu"=>___("Vanuatu", true),    "Venezuela"=>___("Venezuela", true),  
						"Viet Nam"=>___("Viet Nam", true),  "Virgin Islands, British"=>___("Virgin Islands, British", true),  
						"Virgin Islands, U.S."=>___("Virgin Islands, U.S.", true),  "Wallis and Futuna"=>___("Wallis and Futuna", true),  
						"Western Sahara"=>___("Western Sahara", true),  "Yemen"=>___("Yemen", true),  "Zambia"=>___("Zambia", true),  
						"Zimbabwe"=>___("Zimbabwe", true));
		asort($countries);
		
		$states = array("" => "", "AL"=>"Alabama","AK"=>"Alaska","AZ"=>"Arizona","AR"=>"Arkansas",
						"CA"=>"California","CO"=>"Colorado","CT"=>"Connecticut","DE"=>"Delaware","DC"=>"D.C.",
						"FL"=>"Florida","GA"=>"Georgia","HI"=>"Hawaii","ID"=>"Idaho","IL"=>"Illinois","IN"=>"Indiana",
						"IA"=>"Iowa","KS"=>"Kansas","KY"=>"Kentucky","LA"=>"Louisiana","ME"=>"Maine","MD"=>"Maryland",
						"MA"=>"Massachusetts","MI"=>"Michigan","MN"=>"Minnesota","MS"=>"Mississippi","MO"=>"Missouri",
						"MT"=>"Montana","NE"=>"Nebraska","NV"=>"Nevada","NH"=>"New Hampshire","NJ"=>"New Jersey","
						NM"=>"New Mexico","NY"=>"New York","NC"=>"North Carolina","ND"=>"North Dakota","OH"=>"Ohio",
						"OK"=>"Oklahoma","OR"=>"Oregon","PA"=>"Pennsylvania","RI"=>"Rhode Island","SC"=>"South Carolina",
						"SD"=>"South Dakota","TN"=>"Tennessee","TX"=>"Texas","UT"=>"Utah","VT"=>"Vermont","VA"=>"Virginia",
						"WA"=>"Washington","WV"=>"West Virginia","WI"=>"Wisconsin","WY"=>"Wyoming");
						
		$this->set('states', $states);
		$this->set('countries', $countries);
		$this->set('genders', $genders);
		$this->set('months', $months);
		$this->set('years', $years);
	}
	
	function logout() {
	  $this->Session->delete('User'); //kill session info
	  $this->Session->delete('UsersPermission'); //kill users permission
	  $this->redirect('/');
	  die;
	}


	function login() {
	 
	  $this->pageTitle = "Scratch | Login";
	  $errors = Array();
	  
		if (!empty($this->params['form']['User'])) {
			$submit_username = $this->params['form']['User'];
			$submit_pwd = $this->params['form']['Pass'];
			$this->User->bindPermission();
			$users_permission = array();
			$user_record = $this->User->findByUsername($submit_username);
			foreach($user_record['Permission'] as $user_permission) {
				$id = $user_permission['id'];
				$url_name =$user_permission['short_name'];
				$users_permission[$url_name] = 1;
			}

			//determine user status
			$user_status = 'normal';
			if (!empty($user_record)) {
				$user_status = $user_record['User']['status']; 
			}
			
			if ($user_status == 'delbyadmin') {
				array_push($errors, ___("Invalid username and password pair", true));
				$this->setFlash(___("Invalid username and password pair", true), FLASH_ERROR_KEY);
			}
			else if (!empty($user_record['User']['password']) 
			&& $user_record['User']['password'] == sha1($submit_pwd)) {
				$this->Session->write('User', $user_record['User']);
				$this->Session->write('UsersPermission', $users_permission);
				$userID = $user_record['User']['id'];
				$statID = $this->UserStat->field("id", "user_id = $userID");
				$time = date("Y-m-d G:i:s");
				if ($statID) {
					$this->UserStat->id = $statID;
					$this->UserStat->saveField("lastin",$time);
				}
				else {
					$this->UserStat->save(array('UserStat'=>array("user_id"=>$userID, "lastin"=>$time)));
				}
				//for these action redirect to Mystuff page.
				$action = array('/','/multiaccountwarn');
				//Now, let's figure out where to redirect this person to.
				if($user_record['User']['email']=="" || $user_record['User']['email']=="rather-not-say@scratchr.org"){
					$this->redirect('/users/set_email/'.$user_record['User']['urlname']);
				}
				else if(isset($_REQUEST['refer'])&& !in_array($_REQUEST['refer'],$action)){
					$this->redirect($_REQUEST['refer']);
				}
				else {
				  $this->redirect('/users/'.$user_record['User']['urlname']);
				}
			}
			else {
				array_push($errors, ___("Invalid username and password pair", true));
				$this->setFlash(___("Invalid username and password pair", true), FLASH_ERROR_KEY);
			}
		}
	  
		if (empty($errors)) {
			$isError = false;
		} else {
			$isError = true;
		}
		
		$this->set('errors', $errors);
		$this->set('isLoginError', $isError);
	}
	
	/*************set_email******/
	
	function set_email($urlname=null)
	{
		if(!isset($this->data['referrer'])) $this->set('referrer', $_SERVER['HTTP_REFERER']);
		else $this->set('referrer', $this->data['referrer']);
		
		$this->pageTitle = "Scratch | Set Email";
	  	$errors = Array();
	    $session_user_id = $this->getLoggedInUserID();
                if (!$session_user_id)
                   $this->redirect('/login');
		$user_record = $this->User->findByUsername($urlname);		   
		 if(!empty($this->data))
		 {
			 if (empty($this->data['email']))
			 {
			 	array_push($errors, ___("Enter Email Id", true));
			 }
			 else
			 {
			 	if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $this->data['email'])) {
					$this->User->id=$user_record['User']['id'];
					$this->User->saveField("email",$this->data['email']);
					$this->redirect($this->data['referrer']);
					
				} else {
				
					array_push($errors, ___("Invalid Email Id", true));
					
				}
			}	
		  }	 
		if (empty($errors)) {
			$isError = false;
	  } else {
			$isError = true;
	  }
	  
	  $this->set('errors', $errors);
	  $this->set('isError', $isError);
	  $this->set('age', $this->getUserAge($user_record) );	
	}
	
	/*************end set_email******/

	function loginsu($user) {
		if($this->isAdmin()) {
			$this->Session->delete('User');
			$this->User->bindPermission();
			$users_permission = array();
			$user_record = $this->User->findByUsername($user);
			
			foreach($user_record['Permission'] as $user_permission) {
				$id = $user_permission['id'];
				$url_name =$user_permission['short_name'];
				$users_permission[$url_name] = 1;
			}
			$this->Session->write('User', $user_record['User']);
			$this->Session->write('UsersPermission', $users_permission);
			$userID = $user_record['User']['id'];
			$statID = $this->UserStat->field("id", "user_id = $userID");
			echo "now you're $user"; die;
		} else {
			echo "you're not admin"; die;
		}
	}
	
	/**Password recovery***/
	function password_recovery()
	{
		 $this->pageTitle = "Scratch | Password Recovery";
		  $errors = Array();
		  if(!empty($this->data)){
		  	if(!empty($this->data['UsernameEmail']))
			{
				$isEmail =0;
				$useremail = $this->data['UsernameEmail'];
				if (eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $useremail)) {
				$isEmail =1;
				} 
				if($isEmail ==1)
				{
					//submitted data is email
					$user_records=$this->User->findAll(array('email'=>$this->data['UsernameEmail']));
					
					if(!empty($user_records))
					{
						foreach($user_records as $user_record)
						{
							$userid = $user_record['User']['id'];
							$username = $user_record['User']['username'];
							$useremail = $user_record['User']['email'];
							$password = $user_record['User']['password'];
							$subject = ___("Password recovery requested",true);
							$msg = ___("The Scratch Website received a request to reset the password",true). "\r\n";
							$msg = $msg.___("for the username:".$username,true). "\r\n";
							$msg =$msg.___("If you did not submit this request, do not worry, just ignore this message.",true). "\r\n";
							$msg =$msg.___("If you want to reset your password follow this link:",true). "\r\n";
							$msg = $msg.___("http://".$_SERVER['SERVER_NAME']."/users/pwdreset/$userid/$password/$username",true). "\r\n";
							$this->Email->email(REPLY_TO_PASSWORD_RECOVERY,'Scratch Website', $msg, $subject,$useremail,DEFAULT_EMAIL_FROM);
						}
						$this->Session->setFlash(___("A confirmation has been sent to the e-mail address you used to register. If you did not register with a valid e-mail address or you no longer have access to it, you might have to create a new account.",true));	
					}
					else
					{
						array_push($errors, ___("Sorry, username doesn't exist", true));
					}
					
					
				}
				else
				{
				  //submitted data is username
				  $someone=$this->User->findByUsername($this->data['UsernameEmail']);
					if(!empty($someone))
					{
						$userid = $someone['User']['id'];
						$username = $someone['User']['username'];
						$useremail = $someone['User']['email'];
						$password = $someone['User']['password'];
						$subject = ___("Password recovery requested",true);
						$msg = ___("The Scratch Website received a request to reset the password.",true). "\r\n";
						$msg =$msg.___("If you did not submit this request, do not worry, just ignore this message.",true). "\r\n";
						$msg =$msg.___("If you want to reset your password follow this link:",true). "\r\n";
					    $msg = $msg.___("http://".$_SERVER['SERVER_NAME']."/users/pwdreset/$userid/$password/$username",true). "\r\n";
						
						$this->Email->email(REPLY_TO_PASSWORD_RECOVERY,'Scratch Website', $msg, $subject,$useremail,DEFAULT_EMAIL_FROM);
						$this->Session->setFlash(___("A confirmation has been sent to the e-mail address you used to register. If you did not register with a valid e-mail address or you no longer have access to it, you might have to create a new account.",true));	
					}
					else
					{
						array_push($errors, ___("Sorry, Username doesn't exists", true));
					}
				}
			
			}
		  
			  else
			  {
			  array_push($errors, ___("Enter a Username or email address.", true));
			  }
			}//this->data  
		  if (empty($errors)) {
			$isError = false;
	  } else {
			$isError = true;
	  }
	  $this->set('errors', $errors);
	  $this->set('isError', $isError);
		  
	}//function

	/***End Password recovery***/
	function pwdreset($id=null, $code=null,$username=null)
	{	
		
		$this->pageTitle = "Scratch | Resetting the password for username "."'".$username."'";
		$errors =Array();
		$errorFlag=false;
			if(!empty($this->data))
				{
					if(strlen($this->data['User']['password']) < 6) {
					array_push($errors, ___("Password too short.", true));
					$errorFlag=true;
					}
					
					if (strcmp($this->data['User']['password'], $this->data['User']['password2']) == 0) {
					} else {
					array_push($errors, ___("Passwords don't match.", true));
					$errorFlag=true;	
						}
					if($errorFlag){
					}
					else
					{	
					$user_record=$this->User->find("User.id =$id and User.password='$code'");	
					
					if(!empty($user_record))
						{
							$user_record['User']['password'] = sha1($this->data['User']['password']);
							
							$this->User->save($user_record);
							$this->Session->write('User', $user_record['User']);
							$userID = $user_record['User']['id'];
							$statID = $this->UserStat->field("id", "user_id = $userID");
							$time = date("Y-m-d G:i:s");
							if ($statID) {
							  $this->UserStat->id = $statID;
							  $this->UserStat->saveField("lastin",$time);
							} else {
							  $this->UserStat->save(array('UserStat'=>array("user_id"=>$userID, "lastin"=>$time)));
							}
							$this->Session->setFlash(___("New password successfully saved. You are now logged in.",true));
							$this->redirect('/users/pwdreset/'.$username);
							
						}
						
						//if empty user_record
						else
						{
							$this->cakeError('error404');
						}
					}
			}
			
		if($id && $code)
		{
			$isId = true;	
		}
		else
		{
			 $isId = false;
		}
		 if (empty($errors)) {
			$isError = false;
	  } else {
			$isError = true;
	  }
	  $this->set('errors', $errors);
	  $this->set('isError', $isError);
	  $this->set('isId',$isId);
	  $this->set('username',$username);
	}//function pwdreset

	function renderProjects($urlname=null, $option = "projects") {
		$this->autoRender = false;
	
		$content_status = $this->getContentStatus();
		//init user variables
		$isLoggedIn = $this->isLoggedIn();
		$this->User->bindMyThemes();
		$this->Project->bindUser();
		$user_record = $this->User->find("urlname = '$urlname'", null, null, 2);
		$user_id = $user_record['User']['id'];
		$isMe = $this->activeSession($user_id);
		$username = $user_record['User']['username'];	
	
		if (empty($user_record)){
		$this->cakeError('error404');
		}
		
		//showing rebbon on featured project for a particular user
		$this->Project->bindFeatured();
		$allUserProject = $this->Project->findAll("Project.user_id = $user_id");
		
		$featureProlectList = array();
		$image_name='';
		if(SHOW_RIBBON ==1):
		foreach($allUserProject as $userProject)
		{
			if(isset($userProject['FeaturedProject']['id'] ))
			{
				$image_name =$this->ribbonImageName($userProject['FeaturedProject']['timestamp']);
				
				$id = $userProject['Project']['id'];
				$image_name =$image_name; 
				$featureProlectList[$id] = $image_name;
				
			}
		}
		endif;
		$this->set('featureProlectList',$featureProlectList);
		$options = Array("sortBy"=>"created", "sortByClass" => "Project", "direction"=> "DESC");	
		if ($option == "projects") {
			$this->Pagination->show = 15;
			$this->Pagination->url = "/users/renderProjects/".$username . "/" . $option;
			// get all projects from user
			$options = Array("sortBy"=>"created", "sortByClass" => "Project", "direction"=> "DESC");	
			$this->modelClass = "Project";
			list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
			$myProjects = $this->Project->findAll("user_id = $user_id", NULL, $order, $limit, $page, NULL, ($isMe ? 'all' : $content_status));
		
			//count the number of visible comments on each project
			foreach($myProjects as &$project)
			{
				$pid = $project['Project']['id'];
				$project['Project']['totalComments']=$this->Pcomment->findCount("project_id = $pid && visibility = 1");
			}
			$this->set('data',$myProjects);
		}
		if ($option == "favorites") {
			$this->PaginationSecondary->show = 15;
			$this->PaginationSecondary->url = "/users/renderProjects/".$username . "/" . $option;
			$options = Array("sortBy"=>"created", "sortByClass" => "Project", "direction"=> "DESC");	
			$this->modelClass = "Favorite";
			
			//find all favorite projects
			$this->Favorite->bindProject(($isMe ? "Project.status = 'all'" : "Project.status = '$content_status'"));
			if($isMe) {
				$condition = "";
			} else {
				$condition = "Project.status = '$content_status'";
			}
			
			list($order,$limit,$page) = $this->PaginationSecondary->init("Favorite.user_id = $user_id", Array(), $options);
			$this->Favorite->bindProject();
			$this->Project->bindUser(); 
			$favorites = $this->Favorite->findAll("Favorite.user_id=".$user_id, null, $order, $limit, $page, 2 );
			$final_favorites = Array();
			$counter = 0;
			foreach ($favorites as $favorite) {
				$current_favorite = $favorite;
			
				if ($favorite['Project']['status'] != 'safe') {
					if ($content_status == 'all') {
						$final_favorites[$counter] = $current_favorite;
						$counter++;
					} else {
				
					}
				} else {
					$final_favorites[$counter] = $current_favorite;
					$counter++;
				}
			}
			$this->set('data', $final_favorites);
		}
		
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$feed_link = "/feeds/getRecentUserProjects/$user_id";
		
		$this->set('feed_link', $feed_link);
		$this->set('option', $option);
		$this->set('user', $user_record['User']);
		$this->set('urlname', $user_record['User']['urlname']);
		$this->set('age', $this->getUserAge($user_record) );
		$this->set('isMe', $isMe);
		$this->render('render_projects_ajax', 'ajax');
		return;
	}
	/**
	/* MyStuff
	**/
	function view($urlname=null, $option = "projects") {
		$this->autoRender = false;
		$this->modelNames =array('User','ForumUser');
		if(!$urlname)
		$this->cakeError('error404');
		$content_status = $this->getContentStatus();
		//init user variables
		$isLoggedIn = $this->isLoggedIn();
		$this->User->bindMyThemes();
		$this->Project->bindUser();
		$this->User->bindThank();
		$user_record = $this->User->find("urlname = '$urlname'", null, null, 2);
		if(empty($user_record)) {
			$this->cakeError('error404');
        }

		$user_id = $user_record['User']['id'];
		$isMe = $this->activeSession($user_id);
		$username = $user_record['User']['username'];
		
		$user_status = $user_record['User']['status'];
		if ($user_status == 'delbyadmin') {
            $this->cakeError('error404');
        }
		//Find number of featured project for a particular user
		$this->Project->bindFeatured();
		$allUserProject = $this->Project->findAll("Project.user_id = $user_id");
		$featured_count = 0;
		$featureProlectList = array();
		$image_name='';
		foreach($allUserProject as $userProject) {
			if(isset($userProject['FeaturedProject']['id'] )) {
				$featured_count+=1;
				if(SHOW_RIBBON ==1):
				$text =$this->convertDate($userProject['FeaturedProject']['timestamp']);
			 	$image_name =$this->ribbonImageName($userProject['FeaturedProject']['timestamp']);
			 	$this->Thumb->generateThumb($ribbon_image='ribbon.gif',$text,$dir="small_ribbon",$image_name,$dimension='40x30',125,125);
				
				$id = $userProject['Project']['id'];
				$image_name =$image_name; 
				$featureProlectList[$id] = $image_name;
				endif;
			}
		}
		$this->set('featureProlectList',$featureProlectList);
		
		$this->set('featured_count', $featured_count);
		$this->Pagination->show = 15;
		$this->Pagination->url = "/users/renderProjects/".$username . "/" . "projects";
		// get all projects from user
		$options = Array("sortBy"=>"created", "sortByClass" => "Project", "direction"=> "DESC");		
		$this->modelClass = "Project";
		if ($content_status == 'all') {
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id", Array(), $options);
		} else {
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id AND Project.status = 'safe'", Array(), $options);
		}
		$myProjects = $this->Project->findAll("Project.user_id = $user_id", NULL, $order, $limit, $page, NULL, ($isMe ? 'all' : $content_status));
		
		//count the number of visible comments on each project
		$final_projects = Array();
		$counter = 0;
		foreach($myProjects as $project)
		{
			$pid = $project['Project']['id'];
			$total_comments = $this->Pcomment->findCount(array('Pcomment.project_id'=>$pid,'Pcomment.comment_visibility'=>'visible'));
			$temp_project = $project;
			$temp_project['Project']['totalComments'] = $total_comments;
			$final_projects[$counter] = $temp_project;
			$counter++;
		}
		$this->set('projects', $final_projects);
		$this->set('user_id', $user_id);
		
		$this->PaginationSecondary->show = 15;
		$this->modelClass = "Favorite";
		$options = Array("sortBy"=>"timestamp", "sortByClass" => "Favorite", "direction"=> "DESC", "url"=>"/users/renderProjects/".$username . "/" . "favorites");	

		list($order,$limit,$page) = $this->PaginationSecondary->init("Favorite.user_id = $user_id AND Project.proj_visibility = 'visible'", Array(), $options);
		$favorites = $this->Favorite->findAll("Favorite.user_id= $user_id AND Project.proj_visibility = 'visible'", null, $order, $limit, $page, 2 );
		
		$final_favorites = Array();
		$counter = 0;
		foreach ($favorites as $favorite) {
		$current_favorite = $favorite;
			
		if ($favorite['Project']['status'] != 'safe') {
		if ($content_status == 'all') {
			$final_favorites[$counter] = $current_favorite;
			$counter++;
			} else {
				
			}
			} else {
				$final_favorites[$counter] = $current_favorite;
				$counter++;
			}
		}
		$this->set('favorites', $final_favorites);

		// get all friends of user
		$this->Relationship->bindFriend();
		$relations = $this->Relationship->findAll("Relationship.user_id = $user_id AND Relationship.friend_id > 0", NULL, "Relationship.timestamp DESC", 5, 1, NULL);
		$this->set('friends', $relations);
		
		$isIgnored=false;

        //determines if the profile page being viewed is that of a friend
		if ($isLoggedIn) {
			$session_UID = $this->getLoggedInUserID();
			$isIgnored = $this->IgnoredUser->hasAny("IgnoredUser.blocker_id = $session_UID AND IgnoredUser.user_id = $user_id");
			
			$isMyFriend = false;
			if ($isMe) {
				$this->set('session_user_themes', $user_record['Theme']);
				$friend_requests = $this->FriendRequest->findAll(array("to_id"=>$session_UID, "FriendRequest.status"=>"pending"));
				$theme_requests = $this->ThemeRequest->findAll(array("to_id"=>$session_UID, "ThemeRequest.status"=>"pending"), null, null, null, null, 1);
				$this->set('theme_requests', $theme_requests);
				$this->set('friend_requests', $friend_requests);
			}
			else {
				if ($this->Relationship->hasAny("user_id = ".$session_UID." AND friend_id = ".$user_id)) {
					$isMyFriend = true;
				}
				else {
					$this->set("friendPending", false);
					$this->set("friendDeclined", false);

					$fr = $this->FriendRequest->find(array("user_id"=>$user_id, "to_id"=>$session_UID));

					if (!empty($fr)) {
						$this->set("friendPending", ($fr['FriendRequest']['status'] == "pending"));
						$this->set("friendDeclined", ($fr['FriendRequest']['status'] == "declined"));
					}
				}

				$this->User->bindMyThemes();
				$session_user = $this->User->find("id = $session_UID");
				$this->set('session_user_themes', $session_user['Theme']);

				// find all themes owned by current session user
				// where currently viewed user is not a member


				$joinable_themes = $this->Theme->query(
					"SELECT * FROM themes
					WHERE (themes.user_id = ".$session_UID.")
						AND ( NOT EXISTS (SELECT * FROM theme_memberships WHERE theme_memberships.theme_id = themes.id AND theme_memberships.user_id = ".$user_record['User']['id']."))
						AND ( NOT EXISTS (SELECT * FROM theme_requests WHERE theme_requests.user_id = ".$session_UID." AND theme_requests.to_id = ".$user_record['User']['id']." AND theme_requests.theme_id = themes.id))");

				$this->set('joinable_themes', $joinable_themes);
			}

			$this->set('isMyFriend', $isMyFriend);
		}
		
		
		
		/*
        // set user thanks info
        $similar_sender = false;;
        if ($isLoggedIn) {
            $thanks_interval =THANKS_INTERVAL;
            $client_ip = ip2long($this->RequestHandler->getClientIP());
            $similar_sender = $this->Thank->hasAny("Thank.timestamp > now() - interval $thanks_interval HOUR AND Thank.sender_id = $session_UID ");

            $similar_ip_sender = $this->Thank->hasAny("Thank.timestamp > now() - interval $thanks_interval HOUR AND Thank.ipaddress = $client_ip");
            $thanks = 	$this->Thank->find("Thank.timestamp > now() - interval $thanks_interval HOUR AND Thank.sender_id = $session_UID ");
            $thanks_to_username = $thanks['RecieverUser']['username'];
            $thanks_reciever_id = $thanks['Thank']['reciever_id'];
        }

        if( $isMe ) {
		  $thanked_people_count = $this->Thank->query("SELECT count(DISTINCT(sender_id)) as cnt from thanks where `reciever_id`=$user_id");
          $this->set('thanked_count', $thanked_people_count['0']['0']['cnt']);
		}
		else {
            $this->set('thanked_count',0);
        }
        
        $this->set('similar_sender', $similar_sender);
		$this->set('thanks_to_username', $thanks_to_username);
		$this->set('thanks_reciever_id', $thanks_reciever_id);
		$this->set('similar_ip_sender', $similar_ip_sender);
        */
        
		//init shariables
		$myShariables = $this->Shariable->findAll("user_id = $user_id");
		$shariables_used = $this->Shariable->findCount("user_id = $user_id");
		$shariables_left = MAX_SHARIABLES - $shariables_used;


		// set user's galleries
		$final_galleries = $this->set_galleries($user_id);
		$final_galleries = $this->finalize_galleries($final_galleries);
		
		$num_friends = $this->Relationship->findAll("user_id = $user_id");
		$num_galleries = $this->GalleryMembership->findCount("GalleryMembership.user_id = $user_id");
		
		if (count($num_friends) > 5) {
			$this->set('showmorefriends' , true);
		} else {
			$this->set('showmorefriends' , false);
		}
		
		if ($num_galleries > 5) {
			$this->set('showmoregalleries' , true);
		} else {
			$this->set('showmoregalleries' , false);
		}
		
		//populate friends 3 latest project
		/*$project_list = array();
		$friends_project =array();
		
		$config = $this->User->getdbName();
		$mysqli = new mysqli($config['host'], $config['login'], $config['password'], $config['database']);
		$rs = $mysqli->query( "CALL top3friendproject($user_id)" );
        while($row = $rs->fetch_object())
        {
            array_push($project_list,$row->project_id);
        }
        mysqli_free_result($rs);
		mysqli_close($mysqli); 
		$key = 'friends-project-';
        $ttl = FRIENDS_PROJECT_CACHE_TTL;  
		
        $project_ids = implode(',',$project_list);
		$page_limit = NUM_FRIEND_PROJECTS;
		$this->PaginationTernary->show = $page_limit;
		$this->modelClass = "Project";
		$options = Array("sortBy"=>"created", "sortByClass" => "Project", "direction"=> "DESC", "url"=>"/users/renderFriendProjects/".$user_id );*/
		#fetch friends froject without memchached
		/*if(!empty($project_ids)):
		list($order,$limit,$page) = $this->PaginationTernary->init("Project.id in (".$project_ids.") ". " AND Project.proj_visibility = 'visible'", Array(), $options);
		$friends_project = $this->Project->findAll("Project.id in (".$project_ids.") ",null,$order, $limit, $page);
		endif;
		$this->set('friends_project_list',$friends_project);
		*/
		#fetch friends froject using memchached.
		/*if(!empty($project_ids)):
		$projects_count = $this->_getFriendsProjectsCount("Project.id in (".$project_ids.") ". " AND Project.proj_visibility = 'visible'",
                                                $key, $ttl);
		list($order,$limit,$page) = $this->PaginationTernary->init("Project.id in (".$project_ids.") ". " AND Project.proj_visibility = 'visible'", Array(), $options, $projects_count);
		$this->Project->mc_connect();
		$mc_key = $key.$limit.'-'.$page;
		$friends_project = $this->Project->mc_get($mc_key, $user_id);
		if(!$friends_project){
			$friends_project = $this->Project->findAll("Project.id in (".$project_ids.") ",null,$order, $limit, $page);
			$this->Project->mc_set($mc_key, $friends_project, $user_id, $ttl);
			$this->Project->mc_close();
		}
		endif;
		$this->set('friends_project_list',$friends_project);*/		
		
		//sets the admin_comment if one exists for this user
		$admin_comment_record = $this->AdminComment->findCount("user_id = $user_id");
		$admin_comment_full = $this->AdminComment->find("user_id = $user_id");
		if ($admin_comment_record > 0) {
			$admin_comment_full = $this->AdminComment->find("user_id = $user_id");
			$admin_comment = $admin_comment_full['AdminComment']['content'];
		} else {
			$admin_comment = "";
		}
		
		$url = env('SERVER_NAME');
		$url = strtolower($url);
		$feed_link = "/feeds/getRecentUserProjects/$user_id";
		// commenting out karma due to high impact in performance
		// $karma = $this->calculateKarma($user_id);
		$karma_ratings = 0; // $this->KarmaRating->find("KarmaRating.user_id = $user_id");
		$ignore_count = $this->IgnoredUser->findCount("IgnoredUser.blocker_id = $user_id");
		$comment_count = $this->Pcomment->findCount(array('Pcomment.user_id' => $user_id,'comment_visibility'=>'visible')) + $this->Gcomment->findCount(array('Gcomment.user_id' => $user_id,'comment_visibility'=>'visible'));
		
		if ($this->isAdmin()) {
			$admin_notifications = $this->Notification->NotificationType->find('all', array('conditions' => array('is_admin' => 1), 'order' => 'type ASC'));
			$this->set('admin_notifications', $admin_notifications);
		}

        $upload_error = $this->Session->read('upload_error');
		if(!empty($upload_error)) {
			$this->set('upload_error', $upload_error);
			$this->Session->del('upload_error');
		}
		
		$this->set('isCuratored', $this->Curator->hasAny("user_id = $user_id"));
		$this->set('isIgnored',$isIgnored);
		$this->set('comment_count', $comment_count);
		$this->set('ignore_count', $ignore_count);
		$this->set('karma_ratings', $karma_ratings);
		$this->set('feed_link', $feed_link);
		$this->set('admin_comment', $admin_comment);
		$this->set('option', $option);
		$this->set('myShariables', $myShariables);
		$this->set('shariables_left', $shariables_left);
		$this->set('themes', $final_galleries);
		$this->set('user', $user_record['User']);
		$this->set('urlname', $user_record['User']['urlname']);
		$this->set('age', $this->getUserAge($user_record) );
		$this->set('isMe', $isMe);
		
		$this->render('myscratchr', 'scratchr_userpage');
	}


	/**
     * Ajax-updates user properties
	 * city, state, country
     */
    function update() {
	    $this->exitOnInvalidArgCount(0);
		if (!$this->RequestHandler->isAjax())
			$this->__err();

		$user_id = $this->getLoggedInUserID();
		if (!$user_id)
			$this->__err();

        $this->autoRender=false;

		$form_user_id = $this->params["form"]["id"];

		if (!$this->isAdmin())
			if ($user_id !== $form_user_id)
				$this->__err();

		// save user info
		$valid_updates = Array("email","city", "state", "country", "bmonth", "byear");
		$form_keys = array_keys($this->params["form"]);
		$form_field = $form_keys[1];
		if (!in_array($form_field, $valid_updates))
			$this->__err();

		$form_value = htmlspecialchars( $this->params["form"][$form_field] );
		$this->User->id = $form_user_id;
		$this->User->saveField($form_field,$form_value);
        echo $this->User->field($form_field);
        exit();
    }



	/**
     * updates user picture
     */
	function updatepic($user_id) {
		//$this->exitOnInvalidArgCount(1);
		$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
			$this->__err();

		if (empty($this->params["form"]))
			$this->__err();

		$this->User->id = $user_id;
        $user = $this->User->read();

		if (empty($user)){
			$this->cakeError('error404');
		}
		if (!$this->isAdmin() && $user['User']['id'] !== $session_user_id) {
			$this->__err();
		}
		
		// assumini'_ and 'med' types will reside in the same directory
		$icon_array = $this->params["form"]["user_icon"];

		$buddy_icon_file_orig = "static".DS."icons".DS."buddy".DS."tmp".DS;

		if(!file_exists($buddy_icon_file_orig))
			mkdir($buddy_icon_file_orig);

		$error = $this->FileUploader->handleFileUpload($icon_array, $buddy_icon_file_orig, true);
		if ($error[0])
		{
			$this->Session->write('upload_error', $error[0]);
		}
		else
		{
			$buddy_icon_file_orig.=$error[1];
			$this->resizeImage($buddy_icon_file_orig, $user_id);
			//$this->deleteMovedFile($buddy_icon_file_orig);
            $this->User->id = $user_id;
			$this->User->saveField('timestamp', date( 'Y-m-d H:i:s'));
			$this->setFlash(___("Picture uploaded.", true), FLASH_NOTICE_KEY);
		}
		$this->redirect('/users/'.$user['User']['urlname']);
	}

	/**
	* updates user password
	*/
	function updatepass($user_id) {
		//$this->exitOnInvalidArgCount(1);
		$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
		   $this->__err();

		if (empty($this->params["form"]))
		   $this->__err();

		$this->User->id=$user_id;
		$user = $this->User->read();

		if (empty($user)){
		   $this->cakeError('error404');
		}
		if (!$this->isAdmin())
		   if ($user['User']['id'] !== $session_user_id)
		      $this->__err;

		if(!$this->isAdmin())
		{
			$submit_pwd_old = $this->params['form']['password_old'];
		}
		$submit_pwd_new = $this->params['form']['password_new'];
		$submit_pwd_confirm = $this->params['form']['password_confirm'];
		$user_record = $this->User->findById($user_id);

		if ($this->isAdmin() || (!empty($user_record['User']['password']) &&  $user_record['User']['password'] == sha1($submit_pwd_old))) {
		   if ($submit_pwd_new == $submit_pwd_confirm) {
		      $this->User->saveField("password",sha1($submit_pwd_new));
		      $this->setFlash(___("Updated password.", true), FLASH_NOTICE_KEY);
		   }
		   else {
		   	$this->setFlash(___("New password and confirm password do not match.", true), FLASH_NOTICE_KEY);
		   }
		}
		else {
		     $this->setFlash(___("Wrong old password.", true), FLASH_NOTICE_KEY);
		}
		
		$this->redirect('/users/'.$user['User']['urlname']);
	}
	
	
	/*
	updates user email 
        */
        function updateemail($user_id) {
                $this->exitOnInvalidArgCount(1);
                $session_user_id = $this->getLoggedInUserID();
                if (!$session_user_id)
                   $this->__err();

                if (empty($this->params["form"]))
                   $this->__err();

                $this->User->id=$user_id;
                $user = $this->User->read();
				
                if (empty($user)){
                   $this->cakeError('error404');
				}
                if (!$this->isAdmin())
                   if ($user['User']['id'] !== $session_user_id)
                      $this->__err;

                
		if(!$this->isAdmin())
		{
                	$submit_pwd = $this->params['form']['current_password'];
		}

		$submit_email_new = $this->params['form']['new_email'];
                $user_record = $this->User->findById($user_id);
				 if ($this->isAdmin() || (!empty($user_record['User']['password']) &&  $user_record['User']['password'] == sha1($submit_pwd))) {
		     	 $this->User->saveField("email",$submit_email_new);
				 if(ENABLE_TO_CHANGE_FORUM_EMAIL == 1)
				 {
					$this->loadModel('ForumUser');
					$forum_data =$this->ForumUser->find(array('username'=>$user_record['User']['urlname'])); 
					 if(count($forum_data) > 0){
						 $this->ForumUser->id = $forum_data['ForumUser']['id'];
						 $this->ForumUser->saveField("email",$submit_email_new);
					 }
				  }	 
	              $this->setFlash(___("Updated email.", true), FLASH_NOTICE_KEY);
                }
                else { 
                     $this->setFlash(___("Your password is incorrect.", true), FLASH_NOTICE_KEY);
                }

                $this->redirect('/users/'.$user['User']['urlname']);
        }




	/**
	* sends notification to specified user
	*/
	function notify($user_id) {
		$this->exitOnInvalidArgCount(1);
		$session_user_id = $this->getLoggedInUserID();
		if (!$session_user_id)
		   $this->__err();

		if (empty($this->params["form"]))
		   $this->__err();

		$this->User->id = $user_id;
		$user = $this->User->read();

		if (empty($user)){
		   $this->cakeError('error404');
		}
		if (!$this->isAdmin()) {
		      $this->__err;
		}
			
		$username = $user['User']['username'];
		$type = $this->params['form']['type'];
		$msg = $this->params['form']['custom_message'];
		if(!empty($type) && !empty($msg)) {
			//if message is not same as the template, then we need to store the message
			$extra = array();
			if($this->params['form']['template'] != $msg) {
				$extra = array($msg);
			}
			
			$this->Notification->addNotification($type, $user_id, array(), $extra);
			$this->setFlash(___("Notification sent.", true), FLASH_NOTICE_KEY);
			$this->redirect('/users/'.$username);
		}
		else {
			$this->setFlash(___("Empty message or type, notification not sent", true), FLASH_ERROR_KEY);
			$this->redirect('/users/'.$username);
		}
	}


	/**
	 * Removes user's favorites
	 */
	function removefavorites() {
		if (empty($this->params["form"]))
			$this->__err();

		$formData = $this->params["form"];
		if  ($this->isAdmin() || $formData["UID"] == $this->getLoggedInUserID())
		{	echo $formData["UID"];
			$this->User->id = $formData["UID"];
			$urlname = $this->User->field("urlname");
			if (!$urlname){
				$this->cakeError('error404');
			}	
			array_shift($formData);

			foreach ($formData as $favorite_id_str)
			{
				$favorite_id_vals = explode('_',$favorite_id_str);
				$PID = trim($favorite_id_vals[1]);
				$favoriteID = trim($favorite_id_vals[2]);
				if ($favoriteID !== "") {
					// remove favorite record
					$this->Favorite->del($favoriteID);
					// update project record
					$this->Project->id=$PID;
					$num_favoriters = (int)$this->Project->field('num_favoriters') - 1;
					// apparently db can't save '0', use 'null'
					$this->Project->saveField('num_favoriters', ($num_favoriters ? $num_favoriters : null));
				}
			}
			$this->setFlash(___("Your favorites is updated", true), FLASH_NOTICE_KEY);
			$this->redirect('/users/'.$urlname);
		}
		return;
	}
	
	/**
	**/
	function getUserAge($user_record) {
			if (date('m', time()) >  $user_record['User']['bmonth']) {
				return date('Y', time()) - $user_record['User']['byear'] - 1;
			} else {
				return date('Y', time()) - $user_record['User']['byear'];
			}
	}
	
	
	function removefriend($relationship_id) {
		$isLoggedIn = $this->isLoggedIn();
		$urlname = $this->User->field("urlname");
		
		if ($isLoggedIn) {
			$session_UID = $this->getLoggedInUserID();
			$this->User->id = $session_UID;
			$user = $this->User->read();
			$url_name = $user['User']['urlname'];
			$user_id = $user['User']['id'];
			$record = $this->Relationship->find("id = $relationship_id");
			if ($record == null) {
				
			} else {
				$record_id = $record['Relationship']['id'];
				$this->Relationship->del($record_id);
				$this->redirect('/users/showfriends/' . $user_id);
			}
		}
	}
	
	/**
	*
	**/
	function removegallery($gallery_id, $option) {
		$isLoggedIn = $this->isLoggedIn();
		$urlname = $this->User->field("urlname");
		if ($isLoggedIn) {
			$session_UID = $this->getLoggedInUserID();
			$this->User->id = $session_UID;
			$user = $this->User->read();
			$url_name = $user['User']['urlname'];
			$user_id = $user['User']['id'];
			$record = $this->GalleryMembership->find("GalleryMembership.gallery_id = $gallery_id AND GalleryMembership.user_id = $session_UID");
			$this->Gallery->id = $gallery_id;
			$current_gallery = $this->Gallery->read();
			$owner_id = $current_gallery['User']['id'];
			$isOwner = $owner_id == $session_UID;
			
			if ($record == null) {
				$this->cakeError('error404');
			} else {
				$membership_id = $record['GalleryMembership']['id'];
				if ($isOwner) {
					$this->hide_gallery($gallery_id);
				} else {
					$this->GalleryMembership->del($membership_id);
				}
				$this->redirect('/users/showgalleries/' . $user_id . "/" . $option);
			}
		}
	}
	
	/**
	**/
	function showfriends($user_id) {
		$isMe = $this->activeSession($user_id);
		$this->User->id=$user_id;
        $user = $this->User->read();
		if(empty($user)){
		$this->cakeError('error404');
		}
		$user_name = $user['User']['username'];
		
		$this->Pagination->show = 210;
		$this->Pagination->url = "/users/showfriends/".$user_id;
		// get all projects from user
		$options = Array();			
		$this->modelClass = "Relationship";
		list($order,$limit,$page) = $this->Pagination->init("user_id = $user_id", Array(), $options);
	
		// get all friends of user
		$this->Relationship->bindFriend();
		$this->pageTitle = "Scratch | My Stuff |$user_name | Friends";
		$relations = $this->Relationship->findAll("user_id = $user_id", NULL, $order, $limit, $page, NULL);
		$this->set('friends', $relations);
		$this->set('isMe', $isMe);
		$this->set('user', $user);
	}
	
	/**
	**/
	function showgalleries($user_id, $option = "all") {
		$isMe = $this->activeSession($user_id);
		$this->User->id=$user_id;
        $user = $this->User->read();
		if(empty($user)){
		$this->cakeError('error404');
		}
		
		$user_name = $user['User']['username'];
		
		if ($option == "all") {
			$this->Pagination->show = 80;
			$this->Pagination->url = "/users/showgalleries/".$user_id . "/$option";
            $options = Array("sortBy"=>"timestamp", "direction" => "DESC");
			$this->modelClass = "GalleryMembership";
			list($order,$limit,$page) = $this->Pagination->init("GalleryMembership.user_id = $user_id", Array(), $options);
		
			// set user's galleries
			$this->GalleryMembership->bindGallery();
			$gallery_membership = $this->GalleryMembership->findAll("GalleryMembership.user_id = $user_id", NULL, $order, $limit, $page, NULL);
			$final_membership = $gallery_membership;
		} elseif ($option == "owned") {
			$this->Pagination->show = 80;
			$this->Pagination->url = "/users/showgalleries/".$user_id . "/$option";
            $options = Array("sortBy"=>"timestamp", "direction" => "DESC");
			$this->modelClass = "GalleryMembership";
			list($order,$limit,$page) = $this->Pagination->init("GalleryMembership.user_id = $user_id AND GalleryMembership.type = 0", Array(), $options);
		
			// set user's galleries
			$this->GalleryMembership->bindGallery();
			$gallery_membership = $this->GalleryMembership->findAll("GalleryMembership.user_id = $user_id AND GalleryMembership.type = 0", NULL, $order, $limit, $page, NULL);
			$final_membership = $gallery_membership;
		} elseif ($option == "memberof") {
			$this->Pagination->show = 80;
			$this->Pagination->url = "/users/showgalleries/".$user_id . "/$option";
            $options = Array("sortBy"=>"timestamp", "direction" => "DESC");
			$this->modelClass = "GalleryMembership";
			list($order,$limit,$page) = $this->Pagination->init("GalleryMembership.user_id = $user_id AND GalleryMembership.type = 2", Array(), $options);
		
			// set user's galleries
			$this->GalleryMembership->bindGallery();
			$gallery_membership = $this->GalleryMembership->findAll("GalleryMembership.user_id = $user_id AND GalleryMembership.type = 2", NULL, $order, $limit, $page, NULL);
			$final_membership = $gallery_membership;
		} elseif ($option == "bookmarked") {
			$this->Pagination->show = 80;
			$this->Pagination->url = "/users/showgalleries/".$user_id . "/$option";
            $options = Array("sortBy"=>"timestamp", "direction" => "DESC");
			$this->modelClass = "GalleryMembership";
			list($order,$limit,$page) = $this->Pagination->init("GalleryMembership.user_id = $user_id AND GalleryMembership.type = 3", Array(), $options);
		
			// set user's galleries
			$this->GalleryMembership->bindGallery();
			$gallery_membership = $this->GalleryMembership->findAll("GalleryMembership.user_id = $user_id AND GalleryMembership.type = 3", NULL, $order, $limit, $page, NULL);
			$final_membership = $gallery_membership;
		} elseif ($option == "contributed") {
			$this->Pagination->show = 80;
			$this->modelClass = "GalleryProject";
			$this->Pagination->url = "/users/showgalleries/".$user_id . "/$option";
            $options = Array("sortBy"=>"timestamp", "direction" => "DESC");
			$gallery_projects = $this->GalleryProject->findAll("Project.user_id = $user_id GROUP BY gallery_id");
			$actual_count = sizeof($gallery_projects);
			list($order,$limit,$page) = $this->Pagination->init("Project.user_id = $user_id GROUP BY gallery_id", Array(), $options, $actual_count);
		
			$gallery_membership = $this->GalleryProject->findAll("Project.user_id = $user_id GROUP BY gallery_id", NULL, $order, $limit, $page);
			$final_membership = $gallery_membership;
		}
		
		$final_membership = $this->finalize_galleries($final_membership);
		$this->pageTitle = "Scratch | My Stuff |$user_name | Galleries";
		$this->set('title_for_layout', " ");
		$this->set('galleries', $final_membership);
		$this->set('isMe', $isMe);
		$this->set('option', $option);
		$this->set('user', $user);
	}
	
	/********************************* COMMENTLIST ***********************************/
	function comment_list($user_id, $option = "projects") {
		$this->autoRender = false;
		$this->Pagination->show = 30;
		$isMe = $this->activeSession($user_id);
		$logged_id = $this->getLoggedInUserID();
		
		if ($isMe || $this->isAdmin()) {
			
		} else {
			$this->cakeError('error404');
		}
		
		if ($option == 'projects') {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"id", "sortByClass" => "Pcomment", 
							"direction"=> "DESC", "url" => "/users/render_comment_list/" . $user_id . "/" . $option);
							
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", Array(), $options);
			$final_comments = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
		}
		
		if ($option == 'galleries') {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"id", "sortByClass" => "Gcomment", 
							"direction"=> "DESC", "url" => "/users/render_comment_list/" . $user_id . "/" . $option);
							
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", Array(), $options);
			$final_comments = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
		}
		if(empty($final_comments))$this->cakeError('error404');
		$final_comments = $this->set_comments($final_comments, $option);
		$this->set('option', $option);
		$this->set('final_comments', $final_comments);
		$this->set('user_id', $user_id);
		
		$user = $this->User->find("User.id = $user_id");
		$user_name = $user['User']['username'];
		$this->pageTitle = "Scratch | My Stuff | Comments by {$user['User']['username']}";
		$this->render('comment_list');
	}
	
	function render_comment_list($user_id, $option = "projects") {
		$this->autoRender = false;
		$this->Pagination->show = 30;
		$isMe = $this->activeSession($user_id);
		$logged_id = $this->getLoggedInUserID();
		
		if ($isMe || $this->isAdmin()) {
			
		} else {
			$this->__err();
		}
		
		if ($option == 'projects') {
			$this->modelClass = "Pcomment";
			$options = Array("sortBy"=>"id", "sortByClass" => "Pcomment", 
							"direction"=> "DESC", "url" => "/users/render_comment_list/" . $user_id . "/" . $option);
							
			list($order,$limit,$page) = $this->Pagination->init("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", Array(), $options);
			$final_comments = $this->Pcomment->findAll("Pcomment.user_id = $user_id AND Pcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
		}
		
		if ($option == 'galleries') {
			$this->modelClass = "Gcomment";
			$options = Array("sortBy"=>"id", "sortByClass" => "Gcomment", 
							"direction"=> "DESC", "url" => "/users/render_comment_list/" . $user_id . "/" . $option);
							
			list($order,$limit,$page) = $this->Pagination->init("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", Array(), $options);
			$final_comments = $this->Gcomment->findAll("Gcomment.user_id = $user_id AND Gcomment.comment_visibility = 'visible'", null, $order, $limit, $page);
		}
		
		$final_comments = $this->set_comments($final_comments, $option);
		$this->set('option', $option);
		$this->set('final_comments', $final_comments);
		$this->set('user_id', $user_id);
		
		$user = $this->User->find("User.id = $user_id");
		$user_name = $user['User']['username'];
		$this->pageTitle = "Scratch | My Stuff | Comments by {$user['User']['username']}";
		$this->render('comment_list_ajax');
	}
	
	function set_comments($comments, $option = 'projects') {
		if ($option == 'projects') {
			$counter = 0;
			$final_comments = Array();
			foreach ($comments as $current_comment) {
				$temp_comment = $current_comment;
				$current_id = $temp_comment['Pcomment']['id'];
				$commenter_id = $temp_comment['Pcomment']['user_id'];
				
				$project_id = $temp_comment['Pcomment']['project_id'];
				$comment_content = $current_comment['Pcomment']['content'];
				$comment_content = $this->set_comment_content($comment_content);
				$temp_comment['Pcomment']['content'] = $comment_content;
				
				$current_project = $this->Project->find("Project.id = $project_id");
				if (empty($current_project)) {
					$temp_comment['Project']['username'] = Array();
				} else {
					$temp_user_id = $current_project['User']['id'];
				
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Project']['username'] = $temp_user_name;
				}
					
				$final_comments[$counter] = $temp_comment;
				$counter++;
			}
		}
		if ($option == 'galleries') {
			$counter = 0;
			$final_comments = Array();
			foreach ($comments as $current_comment) {
				$temp_comment = $current_comment;
				$current_id = $temp_comment['Gcomment']['id'];
				$commenter_id = $temp_comment['Gcomment']['user_id'];
				
				$gallery_id = $temp_comment['Gcomment']['gallery_id'];
				$comment_content = $current_comment['Gcomment']['content'];
				$comment_content = $this->set_comment_content($comment_content);
				$temp_comment['Gcomment']['content'] = $comment_content;
				
				$current_gallery = $this->Gallery->find("Gallery.id = $gallery_id");
				if (empty($current_gallery)) {
				} else {
					$temp_user_id = $current_gallery['User']['id'];
				
					$temp_user = $this->User->find("User.id = $temp_user_id");
					$temp_user_name = $temp_user['User']['username'];
					$temp_comment['Gallery']['username'] = $temp_user_name;
				}
					
				$final_comments[$counter] = $temp_comment;
				$counter++;
			}
		}
		
		return $final_comments;
	}
	
	/********************************* BLACKLIST ***********************************/
	function ignore_user($user_id, $option = "null") {
		$this->autoRender = false;
		$this->Pagination->show = 20;
		$isMe = $this->activeSession($user_id);
		if (!($this->isAdmin() || $isMe)) {
			$this->__err();
		}
		
		$this->modelClass = "IgnoredUser";
		$options = Array("sortBy"=>"id", "sortByClass" => "IgnoredUser", 
							"direction"=> "DESC", "url" => "/users/render_ignores/" . $user_id);
							
		list($order,$limit,$page) = $this->Pagination->init("IgnoredUser.blocker_id = $user_id", Array(), $options);
		$final_users = $this->IgnoredUser->findAll("IgnoredUser.blocker_id = $user_id", null, $order, $limit, $page);
		
		$this->set('isError', false);
		$this->set('errors', Array());
		$this->set('user_id', $user_id);
		$this->set('data', $final_users);
		$this->render('ignore_user');
	}
	
	function add_ignore_user($user_id, $option = "null") {
		$this->autoRender = false;
		$errors = Array();
		$logged_id = $this->getLoggedInUserID();
		
		if (!empty($this->params['form']['blacklist_username'])) {
			$new_username = $this->params['form']['blacklist_username'];
			$new_reason = htmlspecialchars($this->params['form']['blacklist_reason']);
			$user_record = $this->User->find("username = '$new_username'");
			if (empty($user_record)) {
				$this->cakeError('error404');
			} else {
				if ($new_reason == "") {
					array_push($errors, "You must provide a valid reason for ignoring this user.");
				} else {
					$ignore_id = $user_record['User']['id'];
					$record = $this->IgnoredUser->find("IgnoredUser.user_id = $ignore_id AND IgnoredUser.blocker_id = $logged_id");
					if (empty($record)) {
						$info = Array('IgnoredUser' => Array('id' => null, 'user_id' => $ignore_id, 'blocker_id'=>$user_id, 'reason' => $new_reason));
						$this->IgnoredUser->save($info);
					} else {
						array_push($errors, "That user is already on your ignore list.");
					}
				}
			}	
        } else {
			array_push($errors, "Please enter a valid username.");
		}
		
		$this->Pagination->show = 20;
	
		$this->modelClass = "IgnoredUser";
		$options = Array("sortBy"=>"id", "sortByClass" => "IgnoredUser", 
							"direction"=> "DESC", "url" => "/users/render_ignores/" . $user_id);
							
		list($order,$limit,$page) = $this->Pagination->init("IgnoredUser.user_id = $user_id", Array(), $options);
		$final_users = $this->IgnoredUser->findAll("IgnoredUser.blocker_id = $user_id", null, $order, $limit, $page);
		
		if (empty($errors)) {
			$isError = false;
		} else {
			$isError = true;
		}
		
		$this->set('isError', $isError);
		$this->set('errors', $errors);
		$this->set('user_id', $user_id);
		$this->set('data', $final_users);
		$this->render('render_ignores_ajax', 'ajax');
	}
	
	function remove_ignore_user($user_id, $ignore_id) {
		$this->autoRender = false;
		$user_id = $this->getLoggedInUserID();
		$this->Pagination->show = 20;
		$this->IgnoredUser->del($ignore_id);
		
		$this->modelClass = "IgnoredUser";
		$options = Array("sortBy"=>"id", "sortByClass" => "IgnoredUser", 
							"direction"=> "DESC", "url" => "/users/render_ignores/" . $user_id);
							
		list($order,$limit,$page) = $this->Pagination->init("IgnoredUser.user_id = $user_id", Array(), $options);
		$final_users = $this->IgnoredUser->findAll("IgnoredUser.blocker_id = $user_id", null, $order, $limit, $page);
		
		$this->set('isError', false);
		$this->set('errors', Array());
		$this->set('user_id', $user_id);
		$this->set('data', $final_users);
		$this->render('render_ignores_ajax', 'ajax');
	}
	
	function render_ignores($user_id, $option = "null") {
		$this->autoRender = false;
		$this->Pagination->show = 20;
		
		$this->modelClass = "IgnoredUser";
		$options = Array("sortBy"=>"id", "sortByClass" => "IgnoredUser", 
							"direction"=> "DESC", "url" => "/users/render_ignores/");
							
		list($order,$limit,$page) = $this->Pagination->init("IgnoredUser.user_id = $user_id", Array(), $options);
		$final_users = $this->IgnoredUser->findAll("IgnoredUser.blocker_id = $user_id", null, $order, $limit, $page);
		
		$this->set('isError', false);
		$this->set('errors', Array());
		$this->set('user_id', $user_id);
		$this->set('data', $final_users);
		$this->render('render_ignores_ajax', 'ajax');
	}
	
	function set_galleries($user_id) {
		$gallery_membership = $this->GalleryMembership->findAll("Gallery.visibility = 'visible' AND GalleryMembership.user_id = $user_id");
		
		$return_array = array_splice($gallery_membership, 0, 5);
		return $return_array;
	}
	/********************************* KARMA ***********************************/
	/**
	* Calculates the current karma rating of a particular user
	**/
	function calculateKarma($user_id) {
		$this->autoRender = false;
		$this->User->id = $user_id;
		$current_user = $this->User->read();
		if(empty($current_user)){
		$this->cakeError('error404');
		}
		$project_rating = $this->calculateProjectRating($user_id);
		$gallery_rating = $this->calculateGalleryRating($user_id);
		$comment_rating = $this->calculateCommentRating($user_id);

		$rating_count = $this->KarmaRating->findCount("user_id = $user_id");
		if ($rating_count == 0) {
			$base_rating = 0;
			$info = Array('KarmaRating' => Array('id' => null, 'user_id' => $user_id, 'base' => 0, 
												'project_rating' => $project_rating,
												'gallery_rating' => $gallery_rating,
												'comment_rating' => $comment_rating));
			$this->KarmaRating->save($info);
		} else {
			$base_rating = $this->getBaseKarmaRating($user_id);
			$rating = $this->KarmaRating->find("user_id = $user_id");
			$this->KarmaRating->id = $rating['KarmaRating']['id'];
			$final_rating = $this->KarmaRating->read();	
			$this->KarmaRating->saveField('project_rating', $project_rating);
			$this->KarmaRating->saveField('gallery_rating', $gallery_rating);
			$this->KarmaRating->saveField('comment_rating', $comment_rating);
		}
		
		$final_rating = $project_rating + $gallery_rating + $comment_rating + $base_rating;
		return $final_rating;
	}
	
	/**
	* Returns the base karma rating of a user
	**/
	function getBaseKarmaRating($user_id) {
		$rating = $this->KarmaRating->find("user_id = $user_id");
		$base_rating = $rating['KarmaRating']['base'];
		return $base_rating;
	}
	
	function updateKarma($user_id, $action_id) {
	}
	
	/**
	* Karma rating from projects
	**/
	function calculateProjectRating($user_id) {
		$project_rating = 100;
		$projects = $this->Project->findAll("Project.user_id = $user_id");
		foreach ($projects as $project) {
			$project_id = $project['Project']['id'];
			$vis = $project['Project']['proj_visibility'];
			if ($vis == 'visible') {
				$project_rating = $project_rating + 35;
			} else if ($vis == "censbyadmin") {
				$project_rating = $project_rating - 10;
			} else if ($vis == "censbycomm") {
				$project_rating = $project_rating - 10;
			} else {
				$project_rating = $project_rating - 20;
			}
			/**
			* project flags
			**/
			$flags = $this->Flagger->findCount("project_id = $project_id");
			if ($flags == null) { 
			} else { 
				$project_rating = $project_rating - $flags * 15;
			}
			/**
			* project loves
			**/
			$loves = $this->Lover->findCount("project_id = $project_id");
			if ($loves == null) { 
			} else { 
				$project_rating = $project_rating + $loves * 5;
			}
		}
		/**
		* project tags (not in use until new tags are added to the system)
		**/
		$tags = $this->ProjectTag->findAll("ProjectTag.user_id = $user_id");
		return $project_rating;
	}
	
	/**
	** Karma rating from galleries
	**/
	function calculateGalleryRating($user_id) {
		$gallery_rating = 100;
		$galleries = $this->Gallery->findAll("Gallery.user_id = $user_id");
		foreach ($galleries as $gallery) {
			$gallery_id = $gallery['Gallery']['id'];
			$project_count = $this->GalleryProject->findCount("gallery_id = $gallery_id");
			$gallery_rating = $gallery_rating + 70 + $project_count * 0.1;
		}
		return $gallery_rating;
	}
	
	/**
	** Karma rating from comments
	**/
	function calculateCommentRating($user_id) {
		$comment_rating = 50;
		$project_comments = $this->Pcomment->findAll("Pcomment.user_id = $user_id");
		foreach ($project_comments as $comment) {
			$vis = $comment['Pcomment']['visibility'];
			if ($vis == 1) {
				$comment_rating = $comment_rating + 1;
			} else {
				$comment_rating = $comment_rating - 10;
			}
		}
		$gallery_comments = $this->Gcomment->findAll("Gcomment.user_id = $user_id");
		foreach ($gallery_comments as $comment) {
			$vis = $comment['Gcomment']['visibility'];
			if ($vis == 1) {
				$comment_rating = $comment_rating + 1;
			} else {
				$comment_rating = $comment_rating - 10;
			}
		}
		return $comment_rating;
	}
	
	function updateBaseRating($user_id, $amount) {
	}
	
	
	function flag_list(){
	$this->pageTitle = ___("Scratch | Countries List", true);
	$this->set_signup_variables();
	
	$users = $this->User->query("SELECT count(*)as cnt, country FROM `users` group by country");
	foreach($users as $user){
		$cnt = $user['0']['cnt'];
		$country =$user['users']['country']; 
		$user_list[$country] = $cnt;
	}
	$this->set('user_list',$user_list);
	}
	
	function countries($name=null){
	$this->pageTitle = "Scratch | ".$name. " | All User";
	$this->Pagination->show = 50;
		$this->Pagination->url = "/users/countries/".$name ;
		
		$options = Array("sortBy"=>"created", "sortByClass" => "User", "direction"=> "DESC");		
		$this->modelClass = "User";
		list($order,$limit,$page) = $this->Pagination->init(array('User.country'=>$name), Array(), $options);
		$user_record = $this->User->findAll(array('User.country'=>$name), NULL, $order, $limit, $page, NULL);
		$this->set('user_record',$user_record);
		$this->set('country',$name);
	}
	
	function curator($user_id=null){
		$this->User->id = $user_id;
        $user = $this->User->read();
		if (empty($user)){
			$this->cakeError('error404');
		}
		if (!$this->isAdmin()) {
			$this->__err();
		}
		
		if ($this->isAdmin()){
		$this->data['Curator']['user_id'] = $user_id;
		$this->Curator->save($this->data);
		}
	$this->set('isCuratored', $this->Curator->hasAny("user_id = $user_id"));
	$this->redirect('/users/'.$user['User']['urlname']);
	}
	function uncurator($user_id=null){
		$this->User->id = $user_id;
        $user = $this->User->read();
		if (empty($user)){
			$this->cakeError('error404');
		}
		if (!$this->isAdmin()) {
			$this->__err();
		}
		
		if ($this->isAdmin()){
		$curator = $this->Curator->find("user_id=$user_id");
		$this->Curator->del($curator['Curator']['id']);
		}
	$this->set('isCuratored', $this->Curator->hasAny("user_id = $user_id"));
	$this->redirect('/users/'.$user['User']['urlname']);
	}
	
	//function to send request for create multiple account from same ip.
	function whitelistip(){
		$this->pageTitle = ___('Scratch | Whitelistip', true);
		if (!empty($this->data)) {
			$contact_name = $this->data['WhitelistedIpAddress']['contact_name'];
			$email = $this->data['WhitelistedIpAddress']['email'];
			$school_name =$this->data['WhitelistedIpAddress']['school_name'];
			$comments = $this->data['WhitelistedIpAddress']['comments'];
			$no_of_students = $this->data['WhitelistedIpAddress']['no_of_student'];
			if(empty($this->data['WhitelistedIpAddress']['contact_name']))
			$this->WhitelistedIpAddress->invalidate('contact_name',___('Enter contact name.',true));
			if(!empty($this->data['WhitelistedIpAddress']['email']))
			{
				if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $this->data['WhitelistedIpAddress']['email']))
				$this->WhitelistedIpAddress->invalidate('email',___('Enter valid Email id.',true));
			}
			else
			$this->WhitelistedIpAddress->invalidate('email',___('Enter  Email id.',true));
			if(empty($this->data['WhitelistedIpAddress']['school_name']))
			$this->WhitelistedIpAddress->invalidate('school_name',___('Enter Schhol name.',true));
			if(empty($this->data['WhitelistedIpAddress']['comments']))
			$this->WhitelistedIpAddress->invalidate('comments',___('Enter Comments.',true));
			if(empty($this->data['WhitelistedIpAddress']['no_of_student']))
			$this->WhitelistedIpAddress->invalidate('no_of_student',___('Enter No. of student.',true));
			if($this->WhitelistedIpAddress->validates($this->data['WhitelistedIpAddress'])){
				$this->data['WhitelistedIpAddress']['ipaddress'] = ip2long($this->data['WhitelistedIpAddress']['ipaddress']);
				if ($this->WhitelistedIpAddress->save($this->data)) {
							$subject = ___("Request for creating multiple account from same Ip",true);
							$msg = $comments. "<BR>";
							$msg = $msg.___("School Name : ".$school_name,true). "<BR>";
							$msg = $msg.___("No. of Student : ".$no_of_students,true). "<BR>";
							
							$this->Email->email($email,$contact_name, $msg, $subject,'TO_REQUEST_FOR_MULTIPLE_ACCOUNT',$email);
				$this->Session->setFlash(___('Request has been send.Now you can create multiple account from same Ip.', true));
				$this->redirect('/signup');
				}
			}
		}//!empty
	  $this->set('client_ip' ,$this->RequestHandler->getClientIP());
	}//function
	
	
	function us_banned($locked_id =null){
			$this->autoRender = false;
			$this->pageTitle = ___('Scratch | Blocked Account | Contact us', true);
			$client_ip = ip2long($this->RequestHandler->getClientIP());
			if($locked_id)
			$blocked_record =$this->User->find("User.id = $locked_id AND User.status='locked'");
			else
			$blocked_record =$this->User->find("User.ipaddress = $client_ip and User.status='locked'",array(),'User.timestamp DESC');
			if($blocked_record){
				$blocked_user_id =$blocked_record['User']['id'];
				$blockedusername =$blocked_record['User']['urlname'];
				$author_ip =$blocked_record['User']['ipaddress'];
				$ban_record = $this->BlockedUser->find("BlockedUser.user_id = $blocked_user_id");
				$reasonsforblocking = $ban_record['BlockedUser']['reason'];
				$blocked_date = stampToDate($ban_record['BlockedUser']['timestamp']);
				$this->set('blockedon',stampToDate($ban_record['BlockedUser']['timestamp']));
				$this->set('reasonsforblocking',$reasonsforblocking);
				$this->set('blockedusername',$blockedusername);
				$this->set('isBannedUser',true);
			}
			else
			$this->set('isBannedUser',false);
			
			
			if (!empty($this->data)) {
			$user_agent = $_SERVER['HTTP_USER_AGENT'];
			$name = $this->data['User']['name'];
			$email = $this->data['User']['email'];
			$subject = ___("Message from " . $name . ' at IP: '. long2ip($client_ip),true);
			$message = $this->data['User']['message']. "<br/><br/>";
			$message = $message.___("Blocked Account Name : ". $blockedusername,true). "<br/>";
			$message = $message.___("Blocked On : ".$blocked_date,true). "<br/>";
			$message = $message.___("Reason : "."<div style='border:#CCCCCC 1px solid; padding:2px;margin:5px 0px 5px 0px;'>".$reasonsforblocking."</div>",true);
			
			$message = $message.___("Author's Ip address : ".long2ip($author_ip),true). " ";
			$message = $message.___("User agent Info : ".$user_agent,true);
			
			
			if(empty($this->data['User']['name']))
			$this->User->invalidate('name',___('Enter contact name.',true));
			if(!empty($this->data['User']['email']))
			{
				if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $this->data['User']['email']))
				$this->User->invalidate('email',___('Enter valid Email id.',true));
			}
			else
			$this->User->invalidate('email',___('Enter  Email id.',true));
			
			if(empty($this->data['User']['message']))
			$this->User->invalidate('message',___('Enter Message.',true));
			
			if($this->User->validates($this->data['User'])){
				$this->Email->email($email,$name, $message, $subject, TO_REQUEST_FOR_MULTIPLE_ACCOUNT,$email);
				$this->Session->setFlash(___('The message was sent.', true));
				$this->set('isBannedUser', false);
				$this->data['User']['name'] = '';
				$this->data['User']['email'] = '';
				$this->data['User']['message'] ='';
			}
		}//!empty
			$this->render('us_banned');
	}//function
	
	function _getFriendsProjectsCount($condition, $key, $ttl, $recursion = -1) {
        $this->Project->mc_connect();
        $model_class = 'Project';
        $mc_key = $key.'count';
        $projects_count = $this->Project->mc_get($mc_key);
        if(!$projects_count) {
            $projects_count = $this->$model_class->findCount($condition, $recursion, 'all', true);
            $this->Project->mc_set($mc_key, $projects_count, false, $ttl);
        }
        $this->Project->mc_close();
        return $projects_count;
    }
	
	function __checkLockedUser()
	{
		$client_ip = $this->RequestHandler->getClientIP();
        $ndays = USED_IP_BY_BLOCKED_ACCOUNT_IN_PAST_N_DAYS;
        $this->ViewStat->unbindModel(array('belongsTo' => array('Project')));
        $users_from_this_ip =
        $this->ViewStat->findAll(
            "ViewStat.timestamp > DATE_SUB(NOW(), INTERVAL $ndays DAY) AND  ViewStat.ipaddress = INET_ATON('$client_ip')",
            'DISTINCT user_id', "ViewStat.timestamp DESC");
        
        $users_from_this_ip =  Set::extract('/ViewStat/user_id', $users_from_this_ip);
        $users_from_this_ip = array_chunk($users_from_this_ip, 20);

        $locked_user_id = 0;
        foreach($users_from_this_ip as $users) {
            $user_ids = implode($users, ',');
            $locked_user_id = $this->User->find("User.status='locked' AND User.id IN ($user_ids)",
                            'id','User.timestamp DESC');
            if($locked_user_id) {
                $locked_user_id = $locked_user_id['User']['id'];
                break;
            }
        }
        return $locked_user_id;
	}
  }
?>
