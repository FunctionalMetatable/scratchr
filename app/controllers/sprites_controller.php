<?php
class SpritesController extends AppController {
    var $name = 'Sprites';
    var $helpers = array('PaginationSecondary', 'Pagination','Ajax','Javascript');
    var $components = array('PaginationSecondary', 'Email',  'Pagination', 'RequestHandler', 'FileUploader');
	var $uses = array('Sprite', 'SpriteTag', 'Tag');
	
	function upload_interface() {
		$this->autoRender = false;
		$this->render('upload_interface');
	}
	
	
	function index() {
	}
	
	/**
	* Handles sprite uploads
	**/
	function handle_upload() {
		$this->autoRender = false;
		$this->layout = 'scratchr_spritepage';
		
		$this->pageTitle = ___("Scratch | Sprite Upload", true);
		$user_id = $this->getLoggedInUserID();
		
		$sprite_name = $this->data['Sprite']['name'];
		$sprite_description = $this->data['Sprite']['description'];
		$sprite_tag1 = $this->data['Sprite']['tag1'];
		$sprite_tag2 = $this->data['Sprite']['tag2'];
		$sprite_tag3 = $this->data['Sprite']['tag3'];
		$sprite_tag4 = $this->data['Sprite']['tag4'];
		$sprite_tag3 = $this->data['Sprite']['tag5'];
		$sprite_tag4 = $this->data['Sprite']['tag6'];
		$sprite_cont1 = $this->data['Sprite']['cont1'];
		$sprite_cont2 = $this->data['Sprite']['cont2'];
		$sprite_image = $this->params['form']['sprite_icon'];
		
		if (empty($sprite_name) || empty($sprite_image)) {
			
		} else {
			if (empty($sprite_description)) {
				$sprite_description = "";
			}
			
			$info = Array('Sprite'=>array('id'=>null, 'name'=>$sprite_name, 'description'=>$sprite_description));
			$this->Sprite->save($info);
			$sprite_id = $this->Sprite->getLastInsertID();
			
			/**
			* Determines which tags have been filled
			**/
			$sprite_tags = Array();
			$counter = 0;
			if (!empty($sprite_tag1)) {
				$sprite_tags[$counter] = $sprite_tag1;
				$counter++;
			}
			if (!empty($sprite_tag2)) {
				$sprite_tags[$counter] = $sprite_tag2;
				$counter++;
			}
			if (!empty($sprite_tag3)) {
				$sprite_tags[$counter] = $sprite_tag3;
				$counter++;
			}
			if (!empty($sprite_tag4)) {
				$sprite_tags[$counter] = $sprite_tag4;
				$counter++;
			}
			if (!empty($sprite_tag5)) {
				$sprite_tags[$counter] = $sprite_tag5;
				$counter++;
			}
			if (!empty($sprite_tag6)) {
				$sprite_tags[$counter] = $sprite_tag6;
				$counter++;
			}
			
			/**
			* Inserts each tag
			**/
			foreach($sprite_tags as $current_tag) {
				$tag_record = $this->Tag->find("name = '$current_tag'");
				
                if (!empty($tag_record))
				{
				    $tag_id = $tag_record['Tag']['id'];
					$sprite_tags = $this->SpriteTag->findAll("user_id = $user_id AND tag_id = $tag_id AND sprite_id = $sprite_id");
                    
					if (empty($sprite_tags)) {
                        $this->SpriteTag->save(array('SpriteTag' => array('id' => null, 'user_id' => $user_id, 'sprite_id' => $sprite_id, 'tag_id' => $tag_id)));
                    }
				} else {
					// create tag record
					$this->Tag->save(array('Tag'=>array('id'=> null, 'name'=>$current_tag)));
					$new_tag_id = $this->Tag->getLastInsertID();
					$this->SpriteTag->save(array('SpriteTag'=>array('id' => null, 'user_id' => $user_id, 'sprite_id' => $gallery_id, 'tag_id' => $new_tag_id)));
				}
			}
			
			$root = "/llk/scratchr/beta/app/webroot/";
			$sprite_image_file = $root . getSpriteIcon($sprite_id, false, DS);
			mkdirR(dirname($sprite_image_file) . DS);
			$error = $this->FileUploader->handleFileUpload($sprite_image, $sprite_image_file);
			
			$new_sprite_id = $this->Sprite->getLastInsertID();
		}
		$sprite = $this->Sprite->find("Sprite.id = $new_sprite_id");
		
		$sprite_tags = $this->SpriteTag->findAll("sprite_id = $sprite_id");
		$final_tags = $this->getTagSizes($sprite_tags, $sprite_id);
		
		$this->set('sprite_tags', $final_tags);
		$this->set('sprite', $sprite);
		$this->render('view');
	}
	
	function view($sprite_id) {
		$this->autoRender = false;
		$this->layout = 'scratchr_spritepage';
		
		$user_id = $this->getLoggedInUserID();
		$this->Sprite->id = $sprite_id;
		$sprite = $this->Sprite->read();
		//sets the tags relating to this sprite
		$sprite_tags = $this->SpriteTag->findAll("sprite_id = $sprite_id");
		$final_tags = $this->getTagSizes($sprite_tags, $sprite_id);
		
		$this->set('sprite_tags', $final_tags);
		$this->set('sprite', $sprite);
		$this->render('view');
	}
	
	/**
	* Gets the size of a single set of tags based on $tag_count
	**/
	function getTagSize($tag_count) {
		$size = 1;
		if ($tag_count > 27) {
			$size = 4;
		} elseif ($tag_count > 9) {
			$size = 3;
		} elseif ($tag_count > 1) {
			$size = 2;
		}
		return $size;
	}
	
	function getTagSizes($tag_array, $sprite_id) {
		$final_tags = Array();
		$counter = 0;
		foreach ($tag_array as $current_tag) {
			$current_id = $current_tag['Tag']['id'];
			$tag_count = $this->SpriteTag->findCount("sprite_id = $sprite_id AND tag_id = $current_id");
			$current_tag['SpriteTag']['size'] = $this->getTagSize($tag_count);

			$final_tags[$counter] = $current_tag;
			$counter++;
		}
		return $final_tags;
	}
}
?>
