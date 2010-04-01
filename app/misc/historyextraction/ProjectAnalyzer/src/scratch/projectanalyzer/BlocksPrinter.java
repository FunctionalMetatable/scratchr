package scratch.projectanalyzer;
/* BlocksPrinter -- prints Scratck block stacks */

import java.awt.Color;
import java.io.StringWriter;
import java.lang.Integer;
import java.util.HashSet;
import java.util.Hashtable;

public class BlocksPrinter {
	static Hashtable specs = null;
	static HashSet isBoolean = null;
	static StringWriter stream;
	static int indent = 0;

	static String printStack(Object[] stack) {
		initTables();
		stream = new StringWriter();
		indent = 0;
		printSequence(stack);
		return stream.toString();
	}

	static void initTables() {
		if (specs != null) return;

		specs = new Hashtable(500);
		for (int i = 0; i < blockSpecs.length; i++) {
			String[] row = blockSpecs[i];
			String key = row[0];
			String[] spec = new String[row.length - 1];
			for (int j = 0; j < spec.length; j++) spec[j] = row[j + 1];
			specs.put(key, spec);
		}

		isBoolean = new HashSet(100);
		for (int i = 0; i < booleanSpecs.length; i++) {
			isBoolean.add(booleanSpecs[i]);
		}
	}

	static void printIndentedSequence(Object obj) {
		if (obj == null) return;
		indent++;
		printSequence((Object[]) obj);
		indent--;
	}

	static void printSequence(Object[] seq) {
		for (int i = 0; i < seq.length; i++) {
			printIndent();
			printCommand((Object[]) seq[i]);
		}
	}

	static void printIndent() {
		for (int i = 0; i < indent; i++) stream.write("    ");
	}

	static void printCommand(Object[] cmd) {
		String op = (String) cmd[0];
		String[] spec;

		if ((op.equals("EventHatMorph")) ||
			(op.equals("KeyEventHatMorph")) ||
			(op.equals("MouseClickEventHatMorph")) ||
			(op.equals("WhenHatMorph"))) {
				printHat(cmd);
				return;
		}

		spec = (String []) specs.get(op);
		if (spec == null) {
			spec = new String[1];
			spec[0] = op;
		}
			
		if (op.equals("doForever")) {
			stream.write(spec[0] + "\n");
			printIndentedSequence(cmd[1]);
			return;
		}

		if ((op.equals("doForeverIf")) ||
			(op.equals("doIf")) ||
			(op.equals("doRepeat")) ||
			(op.equals("doUntil"))) {
				stream.write(spec[0] + " ");
				printArg(cmd[1]);
				stream.write("\n");
				printIndentedSequence(cmd[2]);
				return;
		}

		if (op.equals("doIfElse")) {
			Object trueBlocks = cmd[2];
			Object falseBlocks = cmd[3];
			stream.write(spec[0] + " ");
			printArg(cmd[1]);
			stream.write("\n");
			printIndentedSequence(trueBlocks);
			printIndent();
			stream.write("else\n");
			printIndentedSequence(falseBlocks);
			return;
		}

		if (op.equals("changeVariable")) {
			if (cmd[2].equals("setVar:to:")) {
				stream.write("set ");
				printArg(cmd[1]);
				stream.write(" to ");
				printArg(cmd[3]);
			}
			if (cmd[2].equals("changeVar:by:")) {
				stream.write("change ");
				printArg(cmd[1]);
				stream.write(" by ");
				printArg(cmd[3]);
			}
			stream.write("\n");
			return;
		}

		// normal command block
		int aIndex = 1; // argument index
		for (int i = 0; i < spec.length; i++) {
			String token = spec[i];
			if ((token.length() > 1) && (token.charAt(0) == '%')) {
				if (aIndex < cmd.length) printArg(cmd[aIndex++]);
			} else {
				stream.write(token);
			}
			if (i < (spec.length - 1)) stream.write(" ");
		}
		stream.write("\n");
	}

	static void printArg(Object arg) {
		if (arg instanceof Number) {
			stream.write("" + arg);
			return;
		}
		if (arg instanceof String) {
			stream.write("\"" + (String) arg + "\"");
			return;
		}
		if (arg instanceof Boolean) {
			stream.write(((Boolean) arg).toString());
			return;
		}
		if (arg instanceof Sprite) {
			Sprite sprite = (Sprite) arg;
			stream.write("s[" + sprite.id + ", ");
			printArg(sprite.name);
			stream.write("]");
			return;
		}
		if (arg instanceof Color) {
			Color c = (Color) arg;
			stream.write("c[");
			if (c.getRed() < 16) stream.write("0");
			stream.write(Integer.toString(c.getRed(), 16));
			if (c.getGreen() < 16) stream.write("0");
			stream.write(Integer.toString(c.getGreen(), 16));
			if (c.getBlue() < 16) stream.write("0");
			stream.write(Integer.toString(c.getBlue(), 16));
			stream.write("]");
			return;
		}
		if (arg instanceof Object[]) {  // reporter block
			Object[] reporter = (Object[]) arg;
			// TODO ArrayIndexOutOfBoundsException
			if (reporter.length < 1)
				return;
			
			String op = (String) reporter[0];
			if (op.equals("readVariable")) {
				printArg(reporter[1]);
				return;
			}

			String[] spec = (String []) specs.get(op);
			if (spec == null) {
				spec = new String[1];
				spec[0] = op;
			}
			String delims = (isBoolean.contains(op)) ? "<>" : "()";
			stream.write(delims.charAt(0));
			int aIndex = 1;  // argument index
			for (int i = 0; i < spec.length; i++) {
				String token = spec[i];
				if ((token.length() > 1) && (token.charAt(0) == '%')) {
					if (aIndex < reporter.length) printArg(reporter[aIndex++]);
				} else {
					stream.write(token);
				}
				if ((i < (spec.length - 1)) && (spec[i + 1] != "?")) stream.write(" ");
			}
			stream.write(delims.charAt(1));
			return;
		}
		stream.write("unknown arg: " + arg);
	}

	static void printHat(Object[] hat) {
		String op = (String) hat[0];
		Object evt = hat[1];

		if (op.equals("EventHatMorph")) {
			if (evt.equals("Scratch-StartClicked")) {
				stream.write("when green flag clicked");
			} else {
				stream.write("when I receive ");
				printArg(evt);
			}
		}
		if (op.equals("KeyEventHatMorph")) {
				stream.write("when ");
				printArg(evt);
				stream.write(" key pressed");
		}
		if (op.equals("MouseClickEventHatMorph")) {
				stream.write("when I am clicked");
		}
		if (op.equals("WhenHatMorph")) {
				stream.write("when ");
				printArg(evt);
		}
		stream.write("\n");
		indent++;
	}

	static void printit(Object x) {
		if (x instanceof Object[]) {
			Object [] list = (Object []) x;
			System.out.print(" [");
			for (int i = 0; i < list.length; i++) printit(list[i]);
			System.out.print("]");
			return;
		}
		System.out.print(" " + x);
	}

	static String[] booleanSpecs = {
		"<", "=", ">", "&", "|", "not", 
		"color:sees:", "isLoud", "keyPressed:", "mousePressed",
		"sensorPressed:", "touching:", "touchingColor:"
	};

	static String[][] blockSpecs = {
		{"&", "%b", "and", "%b"},
		{"*", "%n", "*", "%n"},
		{"+", "%n", "+", "%n"},
		{"-", "%n", "-", "%n"},
		{"/", "%n", "/", "%n"},
		{"<", "%s", "<", "%s"},
		{"=", "%s", "=", "%s"},
		{">", "%s", ">", "%s"},
		{"\\", "%n", "mod", "%n"},
		{"abs", "abs", "%n"},
		{"append:toList:", "add", "%s", "to", "%L"},
		{"backgroundIndex", "background #"},
		{"bounceOffEdge", "if on edge, bounce"},
		{"broadcast:", "broadcast", "%e"},
		{"changeBackgroundIndexBy:", "change background by", "%n"},
		{"changeBlurBy:", "change blur by", "%n"},
		{"changeBrightnessShiftBy:", "change brightness-shift by", "%n"},
		{"changeCostumeIndexBy:", "change costume by", "%n"},
		{"changeFisheyeBy:", "change fisheye by", "%n"},
		{"changeGraphicEffect:by:", "change", "%g", "effect by", "%n"},
		{"changeHueShiftBy:", "change color-effect by", "%n"},
		{"changeMosaicCountBy:", "change mosaic by", "%n"},
		{"changePenHueBy:", "change pen color by", "%n"},
		{"changePenShadeBy:", "change pen shade by", "%n"},
		{"changePenSizeBy:", "change pen size by", "%n"},
		{"changePixelateCountBy:", "change pixelate by", "%n"},
		{"changePointillizeSizeBy:", "change pointillize drop by", "%n"},
		{"changeSaturationShiftBy:", "change saturation-shift by", "%n"},
		{"changeSizeBy:", "change size by", "%n"},
		{"changeStretchBy:", "change stretch by", "%n"},
		{"changeTempoBy:", "change tempo by", "%n"},
		{"changeVisibilityBy:", "change visibility by", "%n"},
		{"changeVolumeBy:", "change volume by", "%n"},
		{"changeWaterRippleBy:", "change water ripple by", "%n"},
		{"changeWhirlBy:", "change whirl by", "%n"},
		{"changeXposBy:", "change x by", "%n"},
		{"changeYposBy:", "change y by", "%n"},
		{"clearPenTrails", "clear"},
		{"color:sees:", "color", "%C", "is touching", "%C", "?"},
		{"comeToFront", "go to front"},
		{"computeFunction:of:", "%f", "of", "%n"},
		{"concatenate:with:", "%s", "with", "%s"},
		{"costumeIndex", "costume #"},
		{"deleteLine:ofList:", "delete", "%y", "of", "%L"},
		{"distanceTo:", "distance to", "%m"},
		{"doBroadcastAndWait", "broadcast", "%e", "and wait"},
		{"doForever", "forever"},
		{"doForeverIf", "forever if", "%b"},
		{"doIf", "if", "%b"},
		{"doIfElse", "if", "%b"},
		{"doPlaySoundAndWait", "play sound", "%S", "until done"},
		{"doRepeat", "repeat", "%n"},
		{"doReturn", "stop script"},
		{"doUntil", "repeat until", "%b"},
		{"doWaitUntil", "wait until", "%b"},
		{"drum:duration:elapsed:from:", "play drum", "%D", "for", "%n", "beats"},
		{"filterReset", "clear graphic effects"},
		{"forward:", "move", "%n", "steps"},
		{"getAttribute:of:", "%a", "of", "%m"},
		{"getLine:ofList:", "item", "%i", "of", "%L"},
		{"glideSecs:toX:y:elapsed:from:", "glide", "%n", "secs to x:", "%n", "y:", "%n"},
		{"goBackByLayers:", "go back", "%n", "layers"},
		{"gotoSpriteOrMouse:", "go to", "%m"},
		{"gotoX:y:", "go to x:", "%n", "y:", "%n"},
		{"gotoX:y:duration:elapsed:from:", "glide x:", "%n", "y:", "%n", "in", "%n", "secs"},
		{"heading", "direction"},
		{"heading:", "point in direction", "%d"},
		{"hide", "hide"},
		{"hideVariable:", "hide variable", "%v"},
		{"insert:at:ofList:", "insert", "%s", "at", "%i", "of", "%L"},
		{"isLoud", "loud?"},
		{"keyPressed:", "key", "%k", "pressed?"},
		{"lineCountOfList:", "length of", "%L"},
		{"lookLike:", "switch to costume", "%l"},
		{"midiInstrument:", "set instrument to", "%I"},
		{"mousePressed", "mouse down?"},
		{"mouseX", "mouse x"},
		{"mouseY", "mouse y"},
		{"nextBackground", "next background"},
		{"nextCostume", "next costume"},
		{"not", "not", "%b"},
		{"noteOn:duration:elapsed:from:", "play note", "%N", "for", "%n", "beats"},
		{"penColor:", "set pen color to", "%c"},
		{"penSize:", "set pen size to", "%n"},
		{"playSound:", "play sound", "%S"},
		{"pointTowards:", "point towards", "%m"},
		{"putPenDown", "pen down"},
		{"putPenUp", "pen up"},
		{"randomFrom:to:", "pick random", "%n", "to", "%n"},
		{"rest:elapsed:from:", "rest for", "%n", "beats"},
		{"rewindSound:", "rewind sound", "%S"},
		{"rounded", "round", "%n"},
		{"say:", "say", "%s"},
		{"say:duration:elapsed:from:", "say", "%s", "for", "%n", "secs"},
		{"sayNothing", "say nothing"},
		{"scale", "size"},
		{"sensor:", "%H", "sensor value"},
		{"sensorPressed:", "sensor", "%h", "?"},
		{"setBlurTo:", "set blur to", "%n"},
		{"setBrightnessShiftTo:", "set brightness-shift to", "%n"},
		{"setFisheyeTo:", "set fisheye to", "%n"},
		{"setGraphicEffect:to:", "set", "%g", "effect to", "%n"},
		{"setHueShiftTo:", "set color-effect to", "%n"},
		{"setLine:ofList:to:", "replace item", "%i", "of", "%L", "with", "%s"},
		{"setMosaicCountTo:", "set mosaic to", "%n"},
		{"setPenHueTo:", "set pen color to", "%n"},
		{"setPenShadeTo:", "set pen shade to", "%n"},
		{"setPixelateCountTo:", "set pixelate to", "%n"},
		{"setPointillizeSizeTo:", "set pointillize drop to", "%n"},
		{"setSaturationShiftTo:", "set saturation-shift to", "%n"},
		{"setSizeTo:", "set size to", "%n", "%"},
		{"setStretchTo:", "set stretch to", "%n", "%"},
		{"setTempoTo:", "set tempo to", "%n", "bpm"},
		{"setVisibilityTo:", "set visibility to", "%n", "%"},
		{"setVolumeTo:", "set volume to", "%n", "%"},
		{"setWaterRippleTo:", "set water ripple to", "%n"},
		{"setWhirlTo:", "set whirl to", "%n"},
		{"show", "show"},
		{"showBackground:", "switch to background", "%l"},
		{"showVariable:", "show variable", "%v"},
		{"soundLevel", "loudness"},
		{"sqrt", "sqrt", "%n"},
		{"stampCostume", "stamp"},
		{"stopAll", "stop all"},
		{"stopAllSounds", "stop all sounds"},
		{"tempo", "tempo"},
		{"think:", "think", "%s"},
		{"think:duration:elapsed:from:", "think", "%s", "for", "%n", "secs"},
		{"timer", "timer"},
		{"timerReset", "reset timer"},
		{"touching:", "touching", "%m", "?"},
		{"touchingColor:", "touching color", "%C", "?"},
		{"turnAwayFromEdge", "point away from edge"},
		{"turnLeft:", "turn", "%n", "degrees"},
		{"turnRight:", "turn", "%n", "degrees"},
		{"volume", "volume"},
		{"wait:elapsed:from:", "wait", "%n", "secs"},
		{"xpos", "x position"},
		{"xpos:", "set x to", "%n"},
		{"ypos", "y position"},
		{"ypos:", "set y to", "%n"},
		{"|", "%b", "or", "%b"},
	};
}
