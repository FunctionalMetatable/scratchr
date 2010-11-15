-- MySQL dump 10.11
--
-- ------------------------------------------------------
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `analyzer_log`
--

DROP TABLE IF EXISTS `analyzer_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `analyzer_log` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `time_started` datetime default NULL,
  `arguments` text,
  KEY `project_id` (`project_id`),
  KEY `project_version` (`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_blocks_count`
--

DROP TABLE IF EXISTS `project_blocks_count`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_blocks_count` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `scratchComment` int(10) unsigned default '0',
  `KeyEventHatMorph` int(10) unsigned default '0',
  `EventHatMorph_StartClicked` int(10) unsigned default '0',
  `EventHatMorph` int(10) unsigned default '0',
  `MouseClickEventHatMorph` int(10) unsigned default '0',
  `WhenHatBlockMorph` int(10) unsigned default '0',
  `and_operator` int(10) unsigned default '0',
  `multiply_operator` int(10) unsigned default '0',
  `add_operator` int(10) unsigned default '0',
  `subtract_operator` int(10) unsigned default '0',
  `divide_operator` int(10) unsigned default '0',
  `isLessThan` int(10) unsigned default '0',
  `isEqualTo` int(10) unsigned default '0',
  `isGreaterThan` int(10) unsigned default '0',
  `mod_operator` int(10) unsigned default '0',
  `or_operator` int(10) unsigned default '0',
  `abs` int(10) unsigned default '0',
  `allMotorsOff` int(10) unsigned default '0',
  `allMotorsOn` int(10) unsigned default '0',
  `answer` int(10) unsigned default '0',
  `append_toList_` int(10) unsigned default '0',
  `backgroundIndex` int(10) unsigned default '0',
  `bounceOffEdge` int(10) unsigned default '0',
  `broadcast_` int(10) unsigned default '0',
  `changeBackgroundIndexBy_` int(10) unsigned default '0',
  `changeBlurBy_` int(10) unsigned default '0',
  `changeBrightnessShiftBy_` int(10) unsigned default '0',
  `changeCostumeIndexBy_` int(10) unsigned default '0',
  `changeFisheyeBy_` int(10) unsigned default '0',
  `changeGraphicEffect_by_` int(10) unsigned default '0',
  `changeHueShiftBy_` int(10) unsigned default '0',
  `changeMosaicCountBy_` int(10) unsigned default '0',
  `changePenHueBy_` int(10) unsigned default '0',
  `changePenShadeBy_` int(10) unsigned default '0',
  `changePenSizeBy_` int(10) unsigned default '0',
  `changePixelateCountBy_` int(10) unsigned default '0',
  `changePointillizeSizeBy_` int(10) unsigned default '0',
  `changeSaturationShiftBy_` int(10) unsigned default '0',
  `changeSizeBy_` int(10) unsigned default '0',
  `changeStretchBy_` int(10) unsigned default '0',
  `changeTempoBy_` int(10) unsigned default '0',
  `changeVar_by_` int(10) unsigned default '0',
  `changeVisibilityBy_` int(10) unsigned default '0',
  `changeVolumeBy_` int(10) unsigned default '0',
  `changeWaterRippleBy_` int(10) unsigned default '0',
  `changeWhirlBy_` int(10) unsigned default '0',
  `changeXposBy_` int(10) unsigned default '0',
  `changeYposBy_` int(10) unsigned default '0',
  `clearPenTrails` int(10) unsigned default '0',
  `color_sees_` int(10) unsigned default '0',
  `comeToFront` int(10) unsigned default '0',
  `comment_` int(10) unsigned default '0',
  `computeFunction_of_` int(10) unsigned default '0',
  `concatenate_with_` int(10) unsigned default '0',
  `contentsOfList_` int(10) unsigned default '0',
  `costumeIndex` int(10) unsigned default '0',
  `deleteLine_ofList_` int(10) unsigned default '0',
  `distanceTo_` int(10) unsigned default '0',
  `doAsk` int(10) unsigned default '0',
  `doBroadcastAndWait` int(10) unsigned default '0',
  `doForever` int(10) unsigned default '0',
  `doForeverIf` int(10) unsigned default '0',
  `doIf` int(10) unsigned default '0',
  `doIfElse` int(10) unsigned default '0',
  `doPlaySoundAndWait` int(10) unsigned default '0',
  `doRepeat` int(10) unsigned default '0',
  `doReturn` int(10) unsigned default '0',
  `doUntil` int(10) unsigned default '0',
  `doWaitUntil` int(10) unsigned default '0',
  `drum_duration_elapsed_from_` int(10) unsigned default '0',
  `filterReset` int(10) unsigned default '0',
  `forward_` int(10) unsigned default '0',
  `getAttribute_of_` int(10) unsigned default '0',
  `getLine_ofList_` int(10) unsigned default '0',
  `glideSecs_toX_y_elapsed_from_` int(10) unsigned default '0',
  `goBackByLayers_` int(10) unsigned default '0',
  `gotoSpriteOrMouse_` int(10) unsigned default '0',
  `gotoX_y_` int(10) unsigned default '0',
  `gotoX_y_duration_elapsed_from_` int(10) unsigned default '0',
  `heading` int(10) unsigned default '0',
  `heading_` int(10) unsigned default '0',
  `hide` int(10) unsigned default '0',
  `hideVariable_` int(10) unsigned default '0',
  `insert_at_ofList_` int(10) unsigned default '0',
  `isLoud` int(10) unsigned default '0',
  `keyPressed_` int(10) unsigned default '0',
  `letter_of_` int(10) unsigned default '0',
  `lineCountOfList_` int(10) unsigned default '0',
  `list_contains_` int(10) unsigned default '0',
  `lookLike_` int(10) unsigned default '0',
  `midiInstrument_` int(10) unsigned default '0',
  `motorOnFor_elapsed_from_` int(10) unsigned default '0',
  `mousePressed` int(10) unsigned default '0',
  `mouseX` int(10) unsigned default '0',
  `mouseY` int(10) unsigned default '0',
  `nextBackground` int(10) unsigned default '0',
  `nextCostume` int(10) unsigned default '0',
  `not` int(10) unsigned default '0',
  `noteOn_duration_elapsed_from_` int(10) unsigned default '0',
  `penColor_` int(10) unsigned default '0',
  `penSize_` int(10) unsigned default '0',
  `playSound_` int(10) unsigned default '0',
  `pointTowards_` int(10) unsigned default '0',
  `putPenDown` int(10) unsigned default '0',
  `putPenUp` int(10) unsigned default '0',
  `randomFrom_to_` int(10) unsigned default '0',
  `rest_elapsed_from_` int(10) unsigned default '0',
  `rewindSound_` int(10) unsigned default '0',
  `readVariable` int(10) unsigned default '0',
  `rounded` int(10) unsigned default '0',
  `say_` int(10) unsigned default '0',
  `say_duration_elapsed_from_` int(10) unsigned default '0',
  `sayNothing` int(10) unsigned default '0',
  `scale` int(10) unsigned default '0',
  `sensor_` int(10) unsigned default '0',
  `sensorPressed_` int(10) unsigned default '0',
  `setBlurTo_` int(10) unsigned default '0',
  `setBrightnessShiftTo_` int(10) unsigned default '0',
  `setFisheyeTo_` int(10) unsigned default '0',
  `setGraphicEffect_to_` int(10) unsigned default '0',
  `setHueShiftTo_` int(10) unsigned default '0',
  `setLine_ofList_to_` int(10) unsigned default '0',
  `setMosaicCountTo_` int(10) unsigned default '0',
  `setMotorDirection_` int(10) unsigned default '0',
  `setPenHueTo_` int(10) unsigned default '0',
  `setPenShadeTo_` int(10) unsigned default '0',
  `setPixelateCountTo_` int(10) unsigned default '0',
  `setPointillizeSizeTo_` int(10) unsigned default '0',
  `setSaturationShiftTo_` int(10) unsigned default '0',
  `setSizeTo_` int(10) unsigned default '0',
  `setStretchTo_` int(10) unsigned default '0',
  `setTempoTo_` int(10) unsigned default '0',
  `setVar_to_` int(10) unsigned default '0',
  `setVisibilityTo_` int(10) unsigned default '0',
  `setVolumeTo_` int(10) unsigned default '0',
  `setWaterRippleTo_` int(10) unsigned default '0',
  `setWhirlTo_` int(10) unsigned default '0',
  `show` int(10) unsigned default '0',
  `showBackground_` int(10) unsigned default '0',
  `showVariable_` int(10) unsigned default '0',
  `soundLevel` int(10) unsigned default '0',
  `sqrt` int(10) unsigned default '0',
  `stampCostume` int(10) unsigned default '0',
  `startMotorPower_` int(10) unsigned default '0',
  `stopAll` int(10) unsigned default '0',
  `stopAllSounds` int(10) unsigned default '0',
  `stringLength_` int(10) unsigned default '0',
  `tempo` int(10) unsigned default '0',
  `think_` int(10) unsigned default '0',
  `think_duration_elapsed_from_` int(10) unsigned default '0',
  `timer` int(10) unsigned default '0',
  `timerReset` int(10) unsigned default '0',
  `touching_` int(10) unsigned default '0',
  `touchingColor_` int(10) unsigned default '0',
  `turnAwayFromEdge` int(10) unsigned default '0',
  `turnLeft_` int(10) unsigned default '0',
  `turnRight_` int(10) unsigned default '0',
  `volume` int(10) unsigned default '0',
  `wait_elapsed_from_` int(10) unsigned default '0',
  `xpos` int(10) unsigned default '0',
  `xpos_` int(10) unsigned default '0',
  `yourself` int(10) unsigned default '0',
  `ypos` int(10) unsigned default '0',
  `ypos_` int(10) unsigned default '0',
  `askYahoo` int(10) unsigned default '0',
  `wordOfTheDay_` int(10) unsigned default '0',
  `jokeOfTheDay_` int(10) unsigned default '0',
  `synonym_` int(10) unsigned default '0',
  `info_fromZip_` int(10) unsigned default '0',
  `scratchrInfo_forUser_` int(10) unsigned default '0',
  `other` text,
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_drums`
--

DROP TABLE IF EXISTS `project_drums`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_drums` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `drum` int(10) unsigned default NULL,
  `other` char(30) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_info`
--

DROP TABLE IF EXISTS `project_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_info` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `hidden` tinyint(1) default '0',
  `author` text,
  `comment` longtext,
  `derived_from` text,
  `language` char(10) default NULL,
  `scratch_version` text,
  `history` text,
  `numsprites` int(10) unsigned default '0',
  `platform` text,
  `os_version` text,
  `hasMotorBlocks` tinyint(1) default '0',
  `isHosting` tinyint(1) default '0',
  `based_on_project_name` text,
  `based_on_username` text,
  `based_on_project_id` bigint(20) unsigned default NULL,
  `other` text,
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_media`
--

DROP TABLE IF EXISTS `project_media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_media` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `sprite_id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `size` int(10) unsigned default NULL,
  `type` enum('image','sound') default NULL,
  `library` tinyint(1) default '0',
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_midi_instruments`
--

DROP TABLE IF EXISTS `project_midi_instruments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_midi_instruments` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `instrument` int(10) unsigned default NULL,
  `other` char(30) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_save_history`
--

DROP TABLE IF EXISTS `project_save_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_save_history` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `date` datetime default NULL,
  `filename` text,
  `local_name` char(255) default NULL,
  `share_name` char(255) default NULL,
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_share_history`
--

DROP TABLE IF EXISTS `project_share_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_share_history` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `date` datetime default NULL,
  `filename` text,
  `local_name` char(255) default NULL,
  `share_name` char(255) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_sprite_blocks_stack`
--

DROP TABLE IF EXISTS `project_sprite_blocks_stack`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_sprite_blocks_stack` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `sprite_id` int(10) unsigned default NULL,
  `stack` longtext,
  `human_readable` longtext,
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_sprite_disconnected_blocks`
--

DROP TABLE IF EXISTS `project_sprite_disconnected_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_sprite_disconnected_blocks` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `sprite_id` int(10) unsigned default NULL,
  `block` char(30) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_sprites`
--

DROP TABLE IF EXISTS `project_sprites`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_sprites` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `sprite_id` int(10) unsigned default NULL,
  `name` char(255) default NULL,
  `scripts` int(10) unsigned default '0',
  `sounds` int(10) unsigned default '0',
  `images` int(10) unsigned default '0',
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `project_user_generated_strings`
--

DROP TABLE IF EXISTS `project_user_generated_strings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `project_user_generated_strings` (
  `project_id` bigint(20) unsigned default NULL,
  `project_version` int(10) unsigned default NULL,
  `sprite_id` int(10) unsigned default NULL,
  `string` longtext,
  `type` enum('comment','say','think','broadcast','variable_name','variable_value','list_name','list_value') default NULL,
  KEY `project_id` (`project_id`,`project_version`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `remix_comparisons`
--

DROP TABLE IF EXISTS `remix_comparisons`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `remix_comparisons` (
  `remix_pid` int(11) NOT NULL,
  `original_pid` int(11) NOT NULL,
  `same_language` tinyint(1) default NULL,
  `identical_stacks` tinyint(1) default NULL,
  `blocks_added` int(11) default NULL,
  `blocks_removed` int(11) default NULL,
  `sprites_added` int(11) default NULL,
  `sprites_removed` int(11) default NULL,
  `sprites_renamed` int(11) default NULL,
  `images_added` int(11) default NULL,
  `images_removed` int(11) default NULL,
  `images_renamed` int(11) default NULL,
  `images_edited` int(11) default NULL,
  `images_bytes_changed` int(11) default NULL,
  `sounds_added` int(11) default NULL,
  `sounds_removed` int(11) default NULL,
  `sounds_renamed` int(11) default NULL,
  `sounds_edited` int(11) default NULL,
  `sounds_bytes_changed` int(11) default NULL,
  `strings_added` int(11) default NULL,
  `strings_removed` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-11-15 14:50:19
