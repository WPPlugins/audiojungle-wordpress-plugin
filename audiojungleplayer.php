<?php
/*
Plugin Name: AudioJungle Wordpress Music Player
Description: The AudioJungle Wordpress Music Player allows you to embed a dynamic music player in any of your widget areas on your wordpress site. The player allows you to do multiple things such as list search results, top selling files, new files etc. You can include your referral ID to gain commission when prospective buyers click links. Many more features are included, so go ahead and start earning!
Version: 1.0.2
Author: Reaper-Media
Author URI: http://reaper-media.com/
*/

class AudioJungleWidget extends WP_Widget {
    function AudioJungleWidget() {
        parent::WP_Widget(false, $name = 'Audio Jungle Player');	
    }

    function widget($args, $instance) {		
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);
    	if($instance['margin'] == ''){
			$instance['margin'] = '0';
		}
        ?>
              <?php echo $before_widget; ?>
                  <?php if ( $instance['display-title'] == 'true' )
                        echo $before_title . $title . $after_title; ?>
                  <object width="100%" height="<?php if($instance['height'] != ''){print $instance['height']; }else{print '212';} ?>" style="margin-top:<?php print $instance['margin']; ?>px;" >
  <param name="movie" value="http://envato.com/personal_player/aj_preview.swf">
  <param name="allowscriptaccess" value="always">
  <param name="wmode" value="transparent">
  <param name="FlashVars" value="<?php echo $instance['flashvars']; ?>">
  <embed src="http://envato.com/personal_player/aj_preview.swf" type="application/x-shockwave-flash" allowscriptaccess="always" wmode="transparent" flashvars="<?php echo $instance['flashvars']; ?>" width="100%" height="<?php if($instance['height'] != ''){print $instance['height']; }else{print '212';} ?>">
</object>
              <?php echo $after_widget; ?>
        <?php
    }

    function update($new_instance, $old_instance) {
	$instance = $old_instance;
	//Transfer over settings
	foreach($new_instance as $index => $value){
		$instance[$index] = strip_tags($value);
	}
	
	//Set defaults if they are left blank
	$default_settings = array(
		'height' => '212',
		'margin' => '0',
		'color' => '6B9B2B',
		'collectionid' => '380517',
		'itemid' => '119960',
		'volume' => '0.7',
		'itemlimit' => '20'
	);
	foreach($default_settings as $index => $value){
		if(!isset($instance[$index]) || preg_replace('/\ /','',$instance[$index]) == ''){
			$instance[$index] = $value;
		}
	}
	
	//Validate numerical values
	$numerical_settings = array(
		'height' => array(
			'min' => 0
		),
		'margin' => array(),
		'volume' => array(
			'min' => 0,
			'max' => 1
		),
		'itemlimit' => array(
			'min' => 1
		)
	);
	foreach($numerical_settings as $index => $value){
		$valid = false;
		if(is_numeric($instance[$index])){
			if(!isset($value['min']) || (isset($value['min']) && $instance[$index] >= $value['min']) ){
				if(!isset($value['max']) || (isset($value['max']) && $instance[$index] <= $value['max']) ){
					$valid = true;
				}
			}
		}
		if(!$valid){
			$instance[$index] = $default_settings[$index];
		}
	}
	
	//Check boolean properties
	if($new_instance['display-title']){
		$instance['display-title'] = 'true';
	} else {
		$instance['display-title'] = 'false';
	}
    if($new_instance['autoplay']){
		$instance['autoplay'] = 'true';
	} else {
		$instance['autoplay'] = 'false';
	}
	
	//Capitalise Color
	$instance['color'] = strtoupper($instance['color']);
	
	//Compute the flash vars for the set settings
	$instance['flashvars'] = 'api=http://marketplace.envato.com/api/edge/' . (
		$instance['listtype']=='collection'?'collection:' . $instance['collectionid']:
		($instance['listtype']=='search'?'search:audiojungle,,' . $instance['searchquery']:
		($instance['listtype']=='new'?'new-files:audiojungle,' . $instance['newcategory']:
		($instance['listtype']=='randomnew'?'random-new-files:audiojungle':
		($instance['listtype']=='popular'?'popular:audiojungle':
		($instance['listtype']=='author'?'new-files-from-user:' . $instance['author']:
		($instance['listtype']=='singleitem'?'item:'.$instance['itemid']:
		''))))))
	) . '.xml&amp;autoplay='.($instance['autoplay']=='true'?'1':'0').'&amp;refid=' . $instance['refid'] . '&amp;txtColor=0x' . $instance['color'] . '&amp;itemLimit=' . $instance['itemlimit'] . '&amp;initVolume=' . $instance['volume'];
							
    return $instance;
    }
    

    function form($instance) {
    	echo $instance['debug'];
        ?>
	<div style="padding:5px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#DDDDDD;color:#666666;text-align:center;margin-bottom:15px;">Appearance</div>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>" ><?php _e('Widget Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>"></input>
	</p>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('display-title'); ?>" name="<?php echo $this->get_field_name('display-title'); ?>"<?php if($instance['display-title'] == 'true'){echo ' checked="checked"';}?> />
		<label for="<?php echo $this->get_field_id('display-title'); ?>">Display title (above widget)</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('height'); ?>" >Player Height (in pixels):</label>
		<input class="widefat" id="<?php echo $this->get_field_id('height'); ?>" name="<?php echo $this->get_field_name('height'); ?>" type="text" value="<?php echo esc_attr($instance['height']); ?>"></input>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('margin'); ?>">Top Margin (in pixels):</label>
		<input class="widefat" id="<?php echo $this->get_field_id('margin'); ?>" name="<?php echo $this->get_field_name('margin'); ?>" type="text" value="<?php echo esc_attr($instance['margin']); ?>"></input>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('color'); ?>">Color (hex):</label>
		<input class="widefat" id="<?php echo $this->get_field_id('color'); ?>" name="<?php echo $this->get_field_name('color'); ?>" type="text" value="<?php echo esc_attr($instance['color']); ?>"></input>
	</p>
	<div style="padding:5px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#DDDDDD;color:#666666;text-align:center;margin-bottom:15px;">Main Options</div>
	<p>
		<label for="<?php echo $this->get_field_id('refid'); ?>" >Referral ID / Username:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('refid'); ?>" name="<?php echo $this->get_field_name('refid'); ?>" type="text" value="<?php echo esc_attr($instance['refid']); ?>"></input>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('listtype'); ?>" >Track List Type:</label>
		<select class="widefat" id="<?php echo $this->get_field_id('listtype'); ?>" name="<?php echo $this->get_field_name('listtype'); ?>" unset="true">
			<option value="collection" <?php if($instance['listtype'] == 'collection'){echo ' selected="selected"';} ?>>A Collection</option>
			<option value="search" <?php if($instance['listtype'] == 'search'){echo ' selected="selected"';} ?>>Search Results</option>
			<option value="new" <?php if($instance['listtype'] == 'new'){echo ' selected="selected"';} ?>>New Files</option>
			<option value="randomnew" <?php if($instance['listtype'] == 'randomnew'){echo ' selected="selected"';} ?>>Random New Files</option>
			<option value="popular" <?php if($instance['listtype'] == 'popular'){echo ' selected="selected"';} ?>>Popular Files</option>
			<option value="author" <?php if($instance['listtype'] == 'author'){echo ' selected="selected"';} ?>>New Files From Author</option>
			<option value="singleitem" <?php if($instance['listtype'] == 'singleitem'){echo ' selected="selected"';} ?>>Single Item</option>
		</select>
	</p>
	<div listtype="collection" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>List the songs in a collection on AudioJungle. Make sure that only songs are listed in the collection. </p>
		<p>Enter the Collection ID below, you can find this in the URL of the collection.</p>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id('collectionid'); ?>" >Collection ID:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('collectionid'); ?>" name="<?php echo $this->get_field_name('collectionid'); ?>" type="text" value="<?php echo esc_attr($instance['collectionid']); ?>"></input>
		</p>
	</div>
	<div listtype="search" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>List the songs that would show up with a specific search query. Enter the search query below.</p>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id('searchquery'); ?>" >Search Query:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('searchquery'); ?>" name="<?php echo $this->get_field_name('searchquery'); ?>" type="text" value="<?php echo esc_attr($instance['searchquery']); ?>"></input>
		</p>
	</div>
	<div listtype="new" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>List the latest files that have been uploaded to AudioJungle. You are required to choose a category.</p>
		</div>
		<p>
		<label for="<?php echo $this->get_field_id('newcategory'); ?>" >Category:</label>
		<select class="widefat" id="<?php echo $this->get_field_id('newcategory'); ?>" name="<?php echo $this->get_field_name('newcategory'); ?>"'>
			<!-- <option value="all" <?php if($instance['newcategory'] == 'all'){print ' selected="selected"';} ?>>All Files</option>-->
			<option value="music" <?php if($instance['newcategory'] == 'music'){print ' selected="selected"';} ?>>Music</option>
			<option value="music-packs" <?php if($instance['newcategory'] == 'music-packs'){print ' selected="selected"';} ?>>Music Packs</option>
			<option value="sound" <?php if($instance['newcategory'] == 'sound'){print ' selected="selected"';} ?>>Sound</option>
			<option value="source-files" <?php if($instance['newcategory'] == 'source-files'){print ' selected="selected"';} ?>>Source Files</option>
			<option value="logos-idents" <?php if($instance['newcategory'] == 'logos-idents'){print ' selected="selected"';} ?>>Logos &amp; Idents</option>
		</select>
	</p>
	</div>
	<div listtype="randomnew" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>List a random list of newly uploaded files from AudioJungle.</p>
		</div>
	</div>
	<div listtype="popular" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>List the popular files from AudioJungle.</p>
		</div>
	</div>
	<div listtype="author" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>List the latest 10 Audio files from an AudioJungle Author.</p>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id('author'); ?>" >Author Username:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('author'); ?>" name="<?php echo $this->get_field_name('author'); ?>" type="text" value="<?php echo esc_attr($instance['author']); ?>"></input>
		</p>
	</div>
	<div listtype="singleitem" hideable="true">
		<div style="padding-left:5px;padding-right: 5px; padding-top: 7px;padding-bottom:1px;-webkit-border-radius:3px;border-radius:3px;-moz-border-radius:3px;background-color:#EEEEEE;color:#444444;text-align:center;margin-bottom:15px;">
		<p>Play a single file in the audio jungle audio player. Choose an individual item number e.g. &quot;119960&quot; will play the item on <a href="http://audiojungle.net/item/endless-summer/119960">http://audiojungle.net/item/endless-summer/119960</a></p>
		</div>
		<p>
			<label for="<?php echo $this->get_field_id('itemid'); ?>" >Item ID:</label>
			<input class="widefat" id="<?php echo $this->get_field_id('itemid'); ?>" name="<?php echo $this->get_field_name('itemid'); ?>" type="text" value="<?php echo esc_attr($instance['itemid']); ?>"></input>
		</p>
	</div>
	<p>
		<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>"<?php if($instance['autoplay'] == 'true'){echo ' checked="checked"';}?> />
		<label for="<?php echo $this->get_field_id('autoplay'); ?>">Autoplay Music</label>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('volume'); ?>">Volume (between 0 and 1):</label>
		<input class="widefat" id="<?php echo $this->get_field_id('volume'); ?>" name="<?php echo $this->get_field_name('volume'); ?>" type="text" value="<?php echo esc_attr($instance['volume']); ?>"></input>
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('itemlimit'); ?>">Max Number of Songs:</label>
		<input class="widefat" id="<?php echo $this->get_field_id('itemlimit'); ?>" name="<?php echo $this->get_field_name('itemlimit'); ?>" type="text" value="<?php echo esc_attr($instance['itemlimit']); ?>"></input>
	</p>
	
	<script type="text/javascript">	
	//Change the visible form elements when the select value is changed
	jQuery('select[unset=true]').change(function(){
    	var $this = jQuery(this);
    	//make all elements invisible
    	$this.parent().parent().children('[hideable=true]').css('display','none');
    	$this.parent().parent().children('[listtype='+$this.val()+']').css('display','block');
    	
    }).attr('unset','false').change();
	</script>
        <?php 
    }

}


add_action('widgets_init', create_function('', 'return register_widget("AudioJungleWidget");'));


?>