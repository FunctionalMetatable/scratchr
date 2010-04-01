package scratch.projectanalyzer;
/**
 * All constants related to Scratch that is used in the project analysis
 * TODO Obtain this information from an external text file
 */
public class ScratchConstants {
	final static int SCRATCHSPRITEMORPH_CLASSID = 124;  
	final static int SCRATCHSTAGEMORPH_CLASSID = 125;  
	final static int IMAGEMEDIA_CLASSID = 162; 
	final static int MOVIEMEDIA_CLASSID = 163;
	final static int SOUNDMEDIA_CLASSID = 164;
	
	/**
	 * Internal names of all Scratch Blocks
	 */
	final static String[] BLOCKS = {"scratchComment", "KeyEventHatMorph","EventHatMorph_StartClicked", "EventHatMorph", "MouseClickEventHatMorph",
		"WhenHatBlockMorph", "and_operator", "multiply_operator", "add_operator", "subtract_operator", "divide_operator",
		"isLessThan", "isEqualTo", "isGreaterThan", "mod_operator", "or_operator",
		"abs", "allMotorsOff", "allMotorsOn", "answer", "append_toList_", "backgroundIndex", "bounceOffEdge", "broadcast_", "changeBackgroundIndexBy_",
		"changeBlurBy_", "changeBrightnessShiftBy_", "changeCostumeIndexBy_", "changeFisheyeBy_", "changeGraphicEffect_by_",
		"changeHueShiftBy_", "changeMosaicCountBy_", "changePenHueBy_", "changePenShadeBy_", "changePenSizeBy_",
		"changePixelateCountBy_", "changePointillizeSizeBy_", "changeSaturationShiftBy_", "changeSizeBy_", "changeStretchBy_",
		"changeTempoBy_", "changeVar_by_", "changeVisibilityBy_", "changeVolumeBy_", "changeWaterRippleBy_", "changeWhirlBy_",
		"changeXposBy_", "changeYposBy_", "clearPenTrails", "color_sees_", "comeToFront", "comment_",
		"computeFunction_of_", "concatenate_with_", "contentsOfList_", "costumeIndex", "deleteLine_ofList_", "distanceTo_", "doAsk", "doBroadcastAndWait",
		"doForever", "doForeverIf", "doIf", "doIfElse", "doPlaySoundAndWait",
		"doRepeat", "doReturn", "doUntil", "doWaitUntil", "drum_duration_elapsed_from_",
		"filterReset", "forward_", "getAttribute_of_", "getLine_ofList_", "glideSecs_toX_y_elapsed_from_",
		"goBackByLayers_", "gotoSpriteOrMouse_", "gotoX_y_", "gotoX_y_duration_elapsed_from_", "heading",
		"heading_", "hide", "hideVariable_", "insert_at_ofList_", "isLoud",
		"keyPressed_", "letter_of_", "lineCountOfList_", "list_contains_", "lookLike_", "midiInstrument_", "motorOnFor_elapsed_from_", "mousePressed",
		"mouseX", "mouseY", "nextBackground", "nextCostume", "not",
		"noteOn_duration_elapsed_from_", "penColor_", "penSize_", "playSound_", "pointTowards_",
		"putPenDown", "putPenUp", "randomFrom_to_", "rest_elapsed_from_", "rewindSound_",
		"readVariable", "rounded", "say_", "say_duration_elapsed_from_", "sayNothing", "scale",
		"sensor_", "sensorPressed_", "setBlurTo_", "setBrightnessShiftTo_", "setFisheyeTo_",
		"setGraphicEffect_to_", "setHueShiftTo_", "setLine_ofList_to_", "setMosaicCountTo_", "setMotorDirection_", "setPenHueTo_",
		"setPenShadeTo_", "setPixelateCountTo_", "setPointillizeSizeTo_", "setSaturationShiftTo_", "setSizeTo_",
		"setStretchTo_", "setTempoTo_", "setVar_to_", "setVisibilityTo_", "setVolumeTo_", "setWaterRippleTo_",
		"setWhirlTo_", "show", "showBackground_", "showVariable_", "soundLevel",
		"sqrt", "stampCostume", "startMotorPower_", "stopAll", "stopAllSounds", "stringLength_", "tempo",
		"think_", "think_duration_elapsed_from_", "timer", "timerReset", "touching_",
		"touchingColor_", "turnAwayFromEdge", "turnLeft_", "turnRight_", "volume",
		"wait_elapsed_from_", "xpos", "xpos_", "yourself", "ypos", "ypos_",
		"askYahoo", "wordOfTheDay_", "jokeOfTheDay_", "synonym_", "info_fromZip_", "scratchrInfo_forUser_"
		};
	
	/**
	 * All costume names in the Animals category  (used to determine if a costume has the same name as one in the provided libraries)
	 */
	final static String[] COSTUMES_ANIMALS = {"bat1-a","bat1-b","bat2-a","bat2-b","bee1","bird-flying","buffalo1","butterfly1-a","butterfly1-b",
		"butterfly2","butterfly3","cat1-a","cat1-b","cat2","cat3","cat4","cat5","clam1","cow1","crab1-a","crab1-b","crab2","cub1","dinosaur1",
		"dinosaur2","dog1-a","dog1-b","dog2-a","dog2-b","dog2-c","duck1","elephant","elephant1-a","elephant1-b","fish1-a","fish1-b","fish2",
		"fish3","fish4","fox1","frog1","giraffe1-a","giraffe1-b","grasshopper1","grasshopper2","hippo1","horse1-a","horse1-b","horse2","insect1-a",
		"insect1-b","insect2","lion1-a","lion1-b","lioness1","lobster1","monkey1","mouse1","octopus1-a","octopus1-b","parrot1-a","parrot1-b",
		"rabbit1","shark1-a","shark1-b","shark1-c","squirrel1","starfish1-a","starfish1-b","whale1","zebra1"
	};
	
	/**
	 * All costume names in the Letters category  (used to determine if a costume has the same name as one in the provided libraries)
	 */
	final static String[] COSTUMES_LETTERS = {"0","1","2","3","4","5","6","7","8","9","a","b","c","d","e",
		"f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z"
	};
	
	/**
	 * All costume names in the Things category  (used to determine if a costume has the same name as one in the provided libraries)
	 */
	final static String[] COSTUMES_THINGS = {"bananas1","baseball1","basketball","beachball1","button","buttonPressed","cheesy-puffs",
		"drum1","drum2","fire-hydrant","flower-vase","fortune_cookie","fruit_platter","hair1","hair2","key1","lamp","laptop","lego",
		"manhole","marble-building","mic","palmtree","partyhat1","partyhat2","partyhat3","rock","scoreboard","soccer1","sunglassF1",
		"sunglassF2","sunglassF3","sunglassF4","tennisball","trampoline","umbrella","wizardhat"
	};
	
	/**
	 * All costume names in the Fantasy category  (used to determine if a costume has the same name as one in the provided libraries)
	 */
	final static String[] COSTUMES_FANTASY = {"cloak1-a","cloak1-b","demon1-a","demon1-b","demon2-a","demon2-b","devilguy","dragon1-a",
		"dragon1-b","dragon2","fairy","fantasy1-a","fantasy1-b","fantasy10","fantasy11","fantasy12","fantasy13","fantasy14","fantasy2",
		"fantasy3","fantasy4","fantasy5","fantasy6","fantasy7","fantasy8","fantasy9","ghost1","ghost2-a","ghost2-b","gobo1","gobo2","gobo3",
		"knight1","monkeyking","robot1","robot2","robot3","skull1","snowman1","snowman2","sun","troll1","unicorn1","wild1","witch1","wizard1"
	};
	
	/**
	 * All costume names in the People category  (used to determine if a costume has the same name as one in the provided libraries)
	 */
	final static String[] COSTUMES_PEOPLE = {"amon1","anjuli-1","anjuli-2","anjuli-3","anjuli-4","anjuli-5","anna-1","anna-2","anna-3",
		"baby","ballerina-a","ballerina-b","ballerina-c","ballerina-d","boy1-standing","boy1-walking","boy2","boy3","boy4-laughing",
		"boy4-shrugging","boy4-walking-a","boy4-walking-b","boy4-walking-c","boy4-walking-d","boy4-walking-e","boycurly","boyshorts",
		"breakdancer-1","breakdancer-2","breakdancer-3","breakdancer-4","breakdancer1","breakdancer2","breakdancer3","calvrett-jumping",
		"calvrett-thinking","calvrett","cassy-chillin","cassy-dancing-1","cassy-dancing-2","cassy-dancing-3","cassy-dancing-4","cassy-jumping-1",
		"cassy-jumping-2","cassy-phone-1","cassy-sitting-1","cassy-standing-1","chamak1","chamak2","dan1","dan2","dan3","dan4","dan5","dan6",
		"diver1","diver2","ed","football-run","football-stand","girl1-standing","girl1-walking","girl2-shouting","girl2-standing",
		"girl3-basketball","girl3-running","girl3-standing","girl4-sitting","girl4-standing","girl5","jay","jodi1","jodi2","lee-fan",
		"mako-laughing","mako-screaming","mako-stop","marissa-crouching","marissa-sitting","marissa","nathan","nina1","paul1","prince1","prince2",
		"princess1","referee1","referee2","ribbon-dancer","roundman","royalperson","sam","singer1","squaregirl","squareguy","thai-dancer","thai-dancer2",
		"womanbun","womanwaving"
	};
	
	/**
	 * All costume names in the Transportation category  (used to determine if a costume has the same name as one in the provided libraries)
	 */
	final static String[] COSTUMES_TRANSPORTATION = {"airplane","airplane1","bike","bus","car-blue","car-bug","car-cow","car1","car2",
		"convertible1","convertible2","convertible3","helicopter1","magiccarpet","sail-boat","street-cleaner-mit","train","tug-boat","yacht"
	};

	/**
	 * All costume names in the provided libraries
	 */
	final static String[][] COSTUMES = {COSTUMES_ANIMALS,COSTUMES_LETTERS,COSTUMES_THINGS,COSTUMES_FANTASY,COSTUMES_PEOPLE,COSTUMES_TRANSPORTATION
	};

	/**
	 * All background names in the Indoors category  (used to determine if a background has the same name as one in the provided libraries)
	 */	
	final static String[] BACKGROUNDS_INDOORS = {"bedroom1","bedroom2","chalkboard","clothing-store","hall","kitchen","party-room","room1",
		"room2","room3","spotlight-stage","the-movies-inside"
	};
	
	/**
	 * All background names in the Nature category  (used to determine if a background has the same name as one in the provided libraries)
	 */	
	final static String[] BACKGROUNDS_NATURE = {"beach-malibu","canyon","desert","flower-bed","flowers","forrest","garden-rock","grand-canyon",
		"gravel-desert","hill","lake","moon","pathway","stars","tree","underwater","water-and-rocks","wave","woods-and-bench","woods"
	};
	
	/**
	 * All background names in the Outdoors category  (used to determine if a background has the same name as one in the provided libraries)
	 */	
	final static String[] BACKGROUNDS_OUTDOORS = {"all-sports-mural","atom-playground","bench-with-view","berkeley-mural","boardwalk",
		"brick-wall-and-stairs","brick-wall1","brick-wall2","building-at-mit","building-fireproof","castle","city-with-water","city-with-water2",
		"driveway","graffiti","hallway_outdoors","hay_field","houses","mansion","mural-faro","night-city-with-street","night-city","parking-ramp",
		"pool","route66","school1","school2","silos","the-movies","train-tracks1","train-tracks2","train-tracks3","village","wooden-house"
	};
	
	/**
	 * All background names in the Sports category  (used to determine if a background has the same name as one in the provided libraries)
	 */	
	final static String[] BACKGROUNDS_SPORTS = {"baseball-field","basketball-court1-a","basketball-court1-b","basketball-court2","football-field",
		"playing-field","tennis-backboard"
	};

	/**
	 * All background names in the provided libraries
	 */	
	final static String[][] BACKGROUNDS = {BACKGROUNDS_INDOORS, BACKGROUNDS_NATURE, BACKGROUNDS_OUTDOORS, BACKGROUNDS_SPORTS
	};
	
	/**
	 * All sound names in the Animal category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_ANIMAL = {"Bird","Cat","Cricket","Crickets","Dog1","Dog2","Duck","Goose","Horse","HorseGallop","Kitten",
		"Meow","Owl","Rooster","SeaLion","WolfHowl"
	};
	
	/**
	 * All sound names in the Electronic category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_ELECTRONIC = {"AlienCreak1","AlienCreak2","ComputerBeeps1","ComputerBeeps2","DirtyWhir","Fairydust",
		"Laser1","Laser2","Peculiar","Screech","SpaceRipple","Spiral","Whoop","Zoop"
	};
	
	/**
	 * All sound names in the Instruments category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_INSTRUMENTS = {"AfroString","Chord","Dijjeridoo","GuitarStrum","SpookyString","StringAccent","StringPluck",
		"Suspense","Tambura","Trumpet1","Trumpet2"
	};	
	
	/**
	 * All sound names in the Percussion category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_PERCUSSION = {"CymbalCrash","DrumBuzz","Gong","HandClap","RideCymbal","Shaker"
	};
	
	/**
	 * All sound names in the Effects category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_EFFECTS = {"BalloonScratch","BellToll","Bubbles","CarPassing","DoorClose","DoorCreak","MotorcyclePassing",
		"Plunge","Pop","Rattle","Ripples","SewingMachine","Typing","WaterDrop","WaterRunning"
	};
	
	/**
	 * All sound names in the Human category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_HUMAN = {"BabyCry","Cough-female","Cough-male","FingerSnap","Footsteps-1","Footsteps-2","Laugh-female",
		"Laugh-male1","Laugh-male2","Laugh-male3","PartyNoise","Scream-female","Scream-male1","Scream-male2","Slurp","Sneeze-female","Sneeze-male"
	};
	
	/**
	 * All sound names in the Music Loops category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_MUSIC_LOOPS = {"Cave","DripDrop","Drum","DrumMachine","DrumSet1","DrumSet2","Eggs","Garden","GuitarChords1",
		"GuitarChords2","HipHop","HumanBeatbox1","HumanBeatbox2","Jungle","Medieval1","Medieval2","Techno","Techno2","Triumph","Xylo1","Xylo2",
		"Xylo3","xylo4"
	};
	
	/**
	 * All sound names in the Vocals category  (used to determine if a sound has the same name as one in the provided libraries)
	 */	
	final static String[] SOUNDS_VOCALS = {"BeatBox1","BeatBox2","Come-and-play","Doy-doy-doy","Got-inspiration","Hey-yay-hey","Join-us",
		"Oooo-badada","Sing-me-a-song","Singer1","Singer2","Ya"
	};

	/**
	 * All sound names in the provided libraries
	 */	
	final static String[][] SOUNDS = {SOUNDS_ANIMAL, SOUNDS_ELECTRONIC, SOUNDS_INSTRUMENTS, SOUNDS_PERCUSSION, SOUNDS_EFFECTS, SOUNDS_HUMAN,
		SOUNDS_MUSIC_LOOPS, SOUNDS_VOCALS
	};
	
	/**
	 * Converts internal Scratch Block names to names that are more descriptive and friendly for
	 * the database tables.
	 * @param string
	 * @return
	 */
	static String convertBlocksString(String string) {
		if (string.equals("&"))
			return "and_operator";
		if (string.equals("|"))
			return "or_operator";
		if (string.equals("*"))
			return "multiply_operator";
		if (string.equals ("+"))
			return "add_operator";
		if (string.equals ("-"))
			return "subtract_operator";
		if (string.equals ("/"))
			return "divide_operator";
		if (string.equals ("<"))
			return "isLessThan";
		if (string.equals ("="))
			return "isEqualTo";
		if (string.equals (">"))
			return "isGreaterThan";
		if (string.equals ("\\\\"))
			return "mod_operator";
		
		return string.replace(':', '_');
	}


}
