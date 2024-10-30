<?php
/*
Plugin Name: BookCover
Plugin URI: http://ideathinking.com/wiki/index.php/WordPress:BookCoverPlugin
Description: Show book cover image from ISBN.
Version: 1.2
Author: Wongoo Lee
Author URI: http://ideathinking.com/

Copyright 2006  Wongoo Lee  (email : iwongu_at_gmail_dot_com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define('BOOKCOVER_NAME', 'ideathinking_bookcover');
define('BOOKCOVER_DESC', __('ideathinking_bookcover_configuration_data'));

function ideathinking_bookcover_getdefaultdata() {
	/*
	country code 2 book store image url
	89: korean
	4 : japan
	  : other countries
	*/
	$cc2bookstore = array(
		'89'  => 'http://isbn.sfreaders.org/cgi-bin/isbnview?isbn=${isbn}',
		'4'   => 'http://bookweb.kinokuniya.co.jp/imgdata/${isbn}.jpg',
		''    => 'http://images.amazon.com/images/P/${isbn}.01.MZZZZZZZ.gif'
		);
	return $cc2bookstore;
}

function ideathinking_bookcover_preg_callback($matches) {
	$cc2bookstore = get_option(BOOKCOVER_NAME);
	if ($cc2bookstore == null) {
		$cc2bookstore = ideathinking_bookcover_getdefaultdata();		
	} 
		
	foreach ($cc2bookstore as $key => $value) {
		if ($key == '') {
			$pos = 0; 
		} else {
			$pos = strpos("$matches[1]", "$key");
		} 
				
		if ($pos === 0) {
			$url = str_replace('${isbn}', $matches[1], $value);
			
			$alt = "ISBN: $matches[1]";
			if ($matches[3] != '') {
				$alt = $matches[3];
			}
			return '<img class="bookcover" alt="' . $alt . '" src="' . $url . '" />';
		}
	}
}

function ideathinking_bookcover($content) {
	$pattern = '/\[bookcover\s*:\s*(\d+[xX]?)\s*(\s*\(([^)]*)\s*\))?\s*\]/';
	$content = preg_replace_callback($pattern, "ideathinking_bookcover_preg_callback", $content);
	
	return $content;
}
add_filter('the_content', 'ideathinking_bookcover');

function ideathinking_bookcover_config_page() {
	if (function_exists('add_submenu_page')) {
		add_submenu_page('plugins.php', 'BookCover', 'BookCover', 8, basename(__FILE__), 'ideathinking_bookcover_subpanel');
	}
}
 
add_action('admin_menu', 'ideathinking_bookcover_config_page');

function ideathinking_bookcover_subpanel() {
	$isdefault = false;
	$cc2bookstore = get_option(BOOKCOVER_NAME);
	if ($cc2bookstore == null) {
		$cc2bookstore = ideathinking_bookcover_getdefaultdata();
		$isdefault = true;
	}
	
	$updated = false;
	if (isset($_POST['submit'])) {
		$cc2bookstore[$_POST['country_code']] = $_POST['bookimage_url'];
		krsort($cc2bookstore);
		if ($isdefault == true) {
			add_option(BOOKCOVER_NAME, $cc2bookstore, BOOKCOVER_DESC);			
			$isdefault = false;
		} else {
			update_option(BOOKCOVER_NAME, $cc2bookstore);
		}
		
		if (isset($_POST['reset2default'])) {
			delete_option(BOOKCOVER_NAME);
			$cc2bookstore = ideathinking_bookcover_getdefaultdata();
			$isdefault = true;
		}
		
		$updated = true;
	}	

	if ($updated) {
		echo "<div id='message' class='updated fade'><p>";
		_e('Configuration updated.');
		echo "</p></div>";
	}
?>

<div class="wrap">    
<h2><?php _e('BookCover Configurations'); ?></h2>
<p><?php _e('The table has the relationships between country code and URI for book image.');?></p>


<div id="bookcover">

<table width="100%" summary="country code to book image url">
	<caption><?php _e('Country Code to Book Image URL'); if ($isdefault) _e(' (default)'); ?></caption>
	<tr>
		<th><?php _e('Country Code'); ?></th>
		<th><?php _e('Book Image URL'); ?></th>
	</tr>
<?php
foreach ($cc2bookstore as $key => $value) {
	echo "
	<tr>
		<td>$key</td>
		<td>$value</td>
	</tr>
	";
}
?>
</table>

<h2><?php _e('Update BookCover Configurations'); ?></h2>
<p><?php _e('If you know better URI for the specific country code of ISBN, you can add it. Or you can change an existing entry also. Lastly, you can reset to its default configurations.');?></p>
<p><?php _e('You should use ${isbn} in the position of ISBN in the URI. If you leave the Country Code entry, the URI will be applied for other country code.');?></p>
<p><?php _e('Here is <a href="http://www.isbn-international.org/en/identifiers/allidentifiers.html">complete country code list</a>.');?></p>

<form action="" id="bookcoverform" method="post">
	<input type="hidden" name="submit" />
	<p>
		<label for="country_code" accesskey="9" ><?php _e('Country Code'); ?>:</label><br />
		<input type="text" id="country_code" name="country_code" tabindex="1" />
	</p>
	<p>
		<label for="bookimage_url"><?php _e('Book Image URL'); ?>:</label><br />
		<input type="text" id="bookimage_url" name="bookimage_url" tabindex="2" size="80" />
	</p>
	<p>
		<input type="checkbox" id="reset2default" name="reset2default" tabindex="3" />
		<label for="reset2default"><?php _e('Reset to default?'); ?></label>
	</p>
	<p>
		<input type="submit" value="<?php _e('Update &raquo;'); ?>" tabindex="4" />
	</p>
</form>

</div>
</div>

<?php
}

function ideathinking_bookcover_admin_css() {
	echo "
	<style type='text/css'>		
	#bookcover {
		}
		
	#bookcover table {	
		border-top: 1px solid #999;
		border-left: 1px solid #999;
		border-collapse: collapse;
		}
	
	#bookcover caption {
		padding: 10px 0 0 20px;
		font-family: Georgia, serif;
		font-size: 12px;
		color: #999;
		}
		
	#bookcover th, #bookcover td {
		padding: 10px;
		border-right: 1px solid #999;
		border-bottom: 1px solid #999;
		}		

	</style>
	";
}
add_action('admin_head', 'ideathinking_bookcover_admin_css');

/*
bookcover widget support.
*/

function ideathinking_bookcover_widget_init() {

if ( function_exists('register_sidebar_widget') && function_exists('register_widget_control') ) {

function ideathinking_bookcover_widget($args) {
	extract($args);
	$options = get_option('ideathinking_bookcover_widget');
	$title = empty($options['title']) ? __('Books Reading') : $options['title'];
	$text= empty($options['text']) ? __('') : $options['text'];
	
	echo $before_widget;
	echo $before_title . $title . $after_title; 
	
	$pattern = '/\[bookcover\s*:\s*(\d+[xX]?)\s*(\s*\(([^)]*)\s*\))?\s*\]/';
	$text = preg_replace_callback($pattern, "ideathinking_bookcover_preg_callback", $text);
  
	echo $text;
		
	echo $after_widget;
}

function ideathinking_bookcover_widget_control() {
	$options = $newoptions = get_option('ideathinking_bookcover_widget');
	if ( $_POST['text-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['text-title']));
		$newoptions['text'] = stripslashes($_POST['text-text']);
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('ideathinking_bookcover_widget', $options);
	}
	$title = attribute_escape($options['title']);
	$text = attribute_escape($options['text']);
?>
	<p>
	<input style="width: 360px;" id="text-title" name="text-title" type="text" value="<?php echo $title; ?>" />
	<textarea style="width: 360px; height: 180px;" id="text-text" name="text-text"><?php echo $text; ?></textarea>
	</p>
	<input type="hidden" id="text-submit" name="text-submit" value="1" />

<?php
}

register_sidebar_widget('BookCover', 'ideathinking_bookcover_widget');
register_widget_control('BookCover', 'ideathinking_bookcover_widget_control', 360, 240);

}

}

/* Delays plugin execution until Dynamic Sidebar has loaded first. */
add_action('plugins_loaded', 'ideathinking_bookcover_widget_init');

?>
