<?php
 
	class TagcloudHelper extends Helper
	{
		var $helpers = array('Html');
		
		function getTagcloud($tags, $linkBase = '', $cssClass = 'tag', $levels = 7, $level_style='style', $delimiter=" ", $outputTag = 'span', $multiplyer = 2, $maxLimit = 6)
		{   
			$tagcloud = '';
			
			if (!empty($tags))
			{
				$min = $this->_findMinTagCount($tags);
				$max = $this->_findMaxTagCount($tags);

				$counter=0;
				foreach ($tags as $tag)
				{
					$names[$counter]=$tag['Tag']['name'];
					$counter++;
				}
				array_multisort($tags, $names);

				$counter_loop = 0;
				foreach ($tags as $tag)
				{
					$counter = $tag[0]['tagcounter'];
					
					$currentLevel = intval(($levels * ($counter - ($min - 1)))/(($max - ($min - 1)) + 1)) + 1;
					$swappedLevel = $levels - ($levels - $currentLevel); // reverse order
		    			$swappedLevel *= $multiplyer;
					if($swappedLevel>$maxLimit)
					{
						$swappedLevel = $maxLimit;
					}
                    			//$swappedLevel = ($levels - 1) - ($levels - $currentLevel); // zero-index & reverse order
					//$swappedLevel = ($levels + 1) - $currentLevel);
                    
					$tagname = $tag['Tag']['name'];
                    			$tagstyle = " $level_style$swappedLevel";
					if($counter_loop!=0)
					{
						$tagcloud .= $delimiter;
					}
                    			$tagcloud .= "<a rel=\"tag\" href=\"$linkBase/$tagname\"><font size=\"$swappedLevel\" >" . $tagname . "</font></a>";
					$counter_loop++;
					
					//$tagcloud .= '<' . $outputTag . ' class="' . $cssClass . $swappedLevel . '">';
					//$tagcloud .= $this->Html->link($tag['Tag']['tag'], $linkBase . '/' . $tag['Tag']['tag']);
					//$tagcloud .= '</' . $outputTag . '>';
				}
			}
			
			return $this->output($tagcloud);
		}
		
		function _findMaxTagCount($tags)
		{
			$max = 0;
			
			foreach($tags as $tag)
			{
				$counter = $tag[0]['tagcounter'];
		
				if ($counter > $max)
				{
					$max = $counter;
				}
			}
			
			return $max;
		}
		
		function _findMinTagCount($tags)
		{
			$min = 999999999999999999999999;
			
			foreach($tags as $tag)
			{
				$counter = $tag[0]['tagcounter'];
		
				if ($counter < $min)
				{
					$min = $counter;
				}
			}
			
			return $min;
		}
	}
 
?>
