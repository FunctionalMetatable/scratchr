package
{
	import flash.display.Shape;
	import flash.display.Sprite;
	import flash.external.ExternalInterface;
	import flash.net.SharedObject;
	
	public class Save_User extends Sprite
	{
		public function Save_User()
		{
			ExternalInterface.addCallback("saveUsername", addName);
			ExternalInterface.addCallback("removeUsername", remName);
			ExternalInterface.addCallback("getUsernames", getNames);
			ExternalInterface.call("readyToSave");
		}
		
		private var ar:Array;
		private var myLSO:SharedObject;
		
		
		public function getNames():Array {
			// Get value from LSO.
			myLSO = SharedObject.getLocal("CreatedUsers");
			if (myLSO == null) {
				return new Array;
			}
			if (myLSO.data.names == null){
				ar = new Array();
			}
			else {
				ar = myLSO.data.names;	
			}
			return ar;
		}
		
		public function addName(name:String):String {
			// Set value in LSO.
			var myar:Array = getNames();
			myLSO = SharedObject.getLocal("CreatedUsers");
			if (myLSO == null) {
				return name;
			}
			var newar:Array = myar.concat(name);
			//ar = newar;
			myLSO.data.names = newar;
			myLSO.flush();
			return name;
		}
		
		public function remName (name:String):Boolean {
			var myar:Array = getNames();
			myLSO = SharedObject.getLocal("CreatedUsers");
			if (myLSO == null) {
				return false;
			}
			function remHelpFunc (str:String, ind:int,arr:Array):Boolean {
				if (str == name){
					return false;
				}
				else {
					return true;
				}
			}
			var newar:Array = myar.filter(remHelpFunc);
			myLSO.data.names = newar;
			myLSO.flush();
			return true;
		}
		
	}
}