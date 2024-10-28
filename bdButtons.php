<?php
/***
 * Plugin Name: BD Buttons
 * Plugin URI: https://wordpress.org/plugins/bd-buttons
 * Description: Adds the ability to "buttonize" a link. For an example of this, head to the main RichmondDiocese.org site and look at the red "Donate" button at the top of the site. For more help, look at the "BD Buttons" page under Tools.
 * Version: 1.0.5
 * Requires at least: 5.2
 * Author: Doug "BearlyDoug" Hazard
 * Author URI: https://wordpress.org/support/users/bearlydoug/
 * Text Domain: bdbuttons
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This program is free software; you can redistribute it and/or modify it under 
 * the terms of the [GNU General Public License](http://wordpress.org/about/gpl/)
 * as published by the Free Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, on an "AS IS", but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, see [GNU General Public Licenses](http://www.gnu.org/licenses/), or write to the
 * Free Software Foundation, Inc., 51 Franklin Street, 5th Floor, Boston, MA 02110, USA.
 */

/***
 * Internationalization, coming soon!
 */
// echo __('It worked! Now look for a directory named "a".', 'bdbuttons');

/***
 *	Setting up security stuff and paths...
 */
defined('ABSPATH') or die('Sorry, Charlie. No access for you!');
require_once(ABSPATH.'wp-admin/includes/file.php' );
require_once(ABSPATH.'wp-admin/includes/plugin.php');

/***
 * Including the BD functions file...
 */
require_once('functions-bd.php');

/***
 * DEFINE VERSION HERE
 */
define('bdButtonsVersion', '1.0.5');
define('bdbuttons', 'bdbuttons');

/***
 * BD Buttons Navigation link.
 */
function bdplugins_add_bdbuttons_submenu(){
	add_submenu_page(
		'bearlydoug',				// Parent Slug
		'BD Buttons',			// Page Title
		'BD Buttons',			// Menu Title
		'edit_posts',				// Capabilities
		'bdbuttons',				// Nav Menu Link
		'bdbuttons_admin_interface'	// Function name
	);
}
add_action('admin_menu', 'bdplugins_add_bdbuttons_submenu', 15);

/***
 * Loading both the Admin and Plugin CSS and JavaScript files here. Will also check to see if the main
 * BearlyDoug CSS file is enqueued. If not, then enqueue it.
 */
add_action('admin_enqueue_scripts', 'bdbuttons_enqueue_admin_files', 15);
function bdbuttons_enqueue_admin_files(){
	$bdbuttonsuploadFile = wp_upload_dir();

	wp_register_style('bdbuttons', plugins_url('/includes/_CSS-bdButtons.css',__FILE__ ));
	wp_enqueue_style('bdbuttons');
	wp_register_style('bdbuttonscust', $bdbuttonsuploadFile['baseurl'] . '/bdButtons/bdButtons.css');
	wp_enqueue_style('bdbuttonscust');
	add_editor_style(array(
		$bdbuttonsuploadFile['baseurl'] . '/bdButtons/bdButtons.css',
		plugins_url('/includes/_CSS-bdButtons.css',__FILE__ )
	));

	wp_register_style('bdbuttoncolors', plugins_url('/includes/_CSS-minicolors.css',__FILE__ ));
	wp_enqueue_style('bdbuttoncolors');

	/***
	 * This has to get loaded into the footer...
	 */
	wp_enqueue_script('bdbuttonscolors', plugins_url('/includes/_JS-minicolors.js',__FILE__ ), array(), false, true);

	if(!wp_style_is('bearlydougCSS', $list = 'enqueued')) {
		wp_register_style('bearlydougCSS', plugins_url('/includes/_CSS-bearlydoug.css',__FILE__ ));
		wp_enqueue_style('bearlydougCSS');
	}
}

/***
 * Loading only the Plugin CSS file here.
 */
add_action('wp_enqueue_scripts', 'bdbuttons_enqueue_shortcode_files', 15);
function bdbuttons_enqueue_shortcode_files(){
	$bdbuttonsuploadFile = wp_upload_dir();
	$bdbuttonsCSTCSSFile = $bdbuttonsuploadFile['baseurl'] . '/bdButtons/bdButtons.css';
	wp_register_style('bdbuttons', plugins_url('/includes/_CSS-bdButtons.css',__FILE__ ));
	wp_enqueue_style('bdbuttons');
	wp_register_style('bdbuttonscust', $bdbuttonsCSTCSSFile);
	wp_enqueue_style('bdbuttonscust');
}

/***
 * Handling the BD Buttons admin page and tags saving function...
 */
function bdbuttons_admin_interface(){
	global $wpdb;
	$bdbuttonDBTable = $wpdb->prefix . 'bdButtons';

	/***
	 * Need to set the Update message to null...
	 */
	$bdbuttonUpdateMsg = null;

	/***
	 * Processing the new BD Button...
	 */
	if(isset($_REQUEST) && isset($_REQUEST['bdbuttonName']) && isset($_REQUEST['bdbuttonText']) && isset($_REQUEST['bdbuttonBG']) && isset($_REQUEST['bdbuttonBold']) && isset($_REQUEST['bdbuttonFS']) ) {
		/***
		 * First, we're going sanitize each of the  $_REQUEST['bdbuttonXX'] items...
		 */
		$bdbuttonName = sanitize_text_field($_REQUEST['bdbuttonName']);
		$bdbuttonCSS = 'bdp_' . sanitize_title_with_dashes($_REQUEST['bdbuttonName']);
		$bdbuttonText = sanitize_text_field($_REQUEST['bdbuttonText']);
		$bdbuttonBG = sanitize_text_field($_REQUEST['bdbuttonBG']);
		$bdbuttonBold = sanitize_text_field(intval($_REQUEST['bdbuttonBold']));
		$bdbuttonFS = sanitize_text_field(floatval($_REQUEST['bdbuttonFS']));

		/***
		 * Count remaining questions for that language...
		 */
		$bdbuttonRowCNT = $wpdb->get_var("
			SELECT COUNT(*) 
			FROM $bdbuttonDBTable 
			WHERE bdbuttonCSS = '$bdbuttonCSS';
		");

		if(is_object($bdbuttonRowCNT->num_rows)) {
			$bdbuttonResultCNT = $bdbuttonRowCNT->num_rows;
		} else {
			$bdbuttonResultCNT = 0;
		}

		if($bdbuttonResultCNT == 0) {
			$bdbuttonData = array(
				'bdbuttonName'	=> $bdbuttonName,
				'bdbuttonCSS'	=> $bdbuttonCSS,
				'bdbuttonText'	=> $bdbuttonText,
				'bdbuttonBG'	=> $bdbuttonBG,
				'bdbuttonBold'	=> $bdbuttonBold,
				'bdbuttonFS'	=> $bdbuttonFS
			);
			$bdbuttonFormat = array('%s', '%s', '%s', '%s', '%d', '%f');
			$wpdb->insert($bdbuttonDBTable, $bdbuttonData, $bdbuttonFormat);
			/***
			 * Need to generate an updated bdButtons.txt file...
			 */
			bdbuttons_update_bdbuttonstxt();
			bdbuttons_generate_custom_css();
			$bdbuttonUpdateMsg = '<u><i>' . $bdbuttonName . '</i></u> added into the BD Buttons system.<br /><br />You might need to refresh this page to see the proper styling for this button (known issue, only on the Settings page).';
		} else {
			$bdbuttonUpdateMsg = '<strong>NOTICE:</strong> There is already a button with the name of "<u><i>' . $bdbuttonName . '</i></u> in BD Buttons. Please change the name.';
		}
	}

	/***
	 * Regenerating updated files...
	 * /wp-content/uploads/bdButtons/bdButtons.txt
	 * /wp-content/uploads/bdButtons/bdButtons.css
	 */
	if(isset($_REQUEST) && isset($_REQUEST['bdbuttonGenerate']) && ($_REQUEST['bdbuttonGenerate'] == "Yes")) {
		$bdbuttonGenerate = sanitize_text_field($_REQUEST['bdbuttonGenerate']);
		bdbuttons_update_bdbuttonstxt();
		bdbuttons_generate_custom_css();
		$bdbuttonUpdateMsg = 'Updated bdButtons.txt and bdButtons.css generated!<br /><br />You might need to refresh this page to see the proper styling for this button (known issue, only on the Settings page).';
	}

	/***
	 * Deleting a custom button......
	 */
	if(isset($_REQUEST) && isset($_REQUEST['bdpdelete'])) {
		$bdpdelete = sanitize_title_with_dashes($_REQUEST['bdpdelete']);
		$bdbuttonData = array(
			'bdbuttonCSS'	=> $bdpdelete
		);
		$bdbuttonFormat = array('%s');
		$wpdb->delete($bdbuttonDBTable, $bdbuttonData, $bdbuttonFormat);
		bdbuttons_update_bdbuttonstxt();
		bdbuttons_generate_custom_css();
		$bdbuttonUpdateMsg = '<u><i>' . $bdpdelete . '</i></u> has been removed from the BD Buttons system.<br /><br />You might need to refresh this page to see the proper styling for this button (known issue, only on the Settings page).';
	}

	/***
	 * Let's show the WP Admin interface!
	 */
	echo '
	<div class="bdTabs">
<!-- bdTabs Navigation Tabs -->
		<input type="radio" name="bdTabs" class="bdRadio" id="bdTab1" checked >
		<label class="bdLabel" for="bdTab1"><i class="dashicons dashicons-admin-generic"></i><span>BD Buttons, v' . constant("bdButtonsVersion") . '</span></label>

		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab2">
		<label class="bdLabel" for="bdTab2"><i class="dashicons dashicons-editor-quote"></i><span>About BD Buttons</span></label>
<!--
	Commented out, for now. Coming in a future version.
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab3">
		<label class="bdLabel" for="bdTab3"><i class="dashicons dashicons-info-outline"></i><span>TBD</span></label>
-->
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab4">
		<label class="bdLabel" for="bdTab4"><i class="dashicons dashicons-universal-access"></i><span>More BD Plugins</span></label>

<!-- bdTabs Content Tabs -->
		<div id="bdTab-content1" class="bdTab-content">
			<div class="bdWrapper">
				<div class="bdRow">
					<div class="bdDColumn">
						<fieldset>
							<legend>Default Buttons (non deletable)</legend>
							<div class="bdCTR">
								<a class="bdButton bdpGreen">Green Button</a> 
								<a class="bdButton bdpRed">Red Button</a> 
								<a class="bdButton bdpBlue">Blue Button</a> 
								<a class="bdButton bdpWhite">White Button</a>
								<br /><br />
								<a class="bdButton bdpBlack">Black Button</a>
								<a class="bdButton bdpPurple">Purple</a>
								<a class="bdButton bdpGold">Gold</a> 
							</div>
						</fieldset>

						<br />
						<fieldset>
							<legend>Your custom buttons...</legend>';
	/***
	 * Need to show the confirmation or error message here...
	 */
	if(!empty($bdbuttonUpdateMsg)) {
		echo '
							<div class="bdCTR">' . $bdbuttonUpdateMsg . '<br />&nbsp;</div>';
	}

	/***
	 * Continuing on...
	 */
	echo '
							<div class="bdCTR">';
	/***
	 * Get a list of all custom BD Buttons...
	 */
	$bdbuttonList = $wpdb->get_results("
		SELECT * 
		FROM $bdbuttonDBTable 
		ORDER BY `bdbuttonName` ASC
	");

	$bdbuttonCNT = 1;
	if(!empty($bdbuttonList)) {
		foreach($bdbuttonList as $thebdbutton){
		/***
		 * We want to sanitize each and every item being pulled out of the DB (which is only two things)
		 */
			$bdbuttonName = esc_html($thebdbutton->bdbuttonName);
			$bdbuttonCSS = esc_html($thebdbutton->bdbuttonCSS);

		/***
		 * Output the buttons list... four per row.
		 */
			echo '
								<a href="admin.php?page=bdbuttons&bddelete=' . $bdbuttonCSS . '" class="bdButton ' . $bdbuttonCSS . '"onclick="return confirm(\'Are you sure you want to delete ' . $bdbuttonName . '?\r\n\r\nThis is not reversable.\')">' . $bdbuttonName . '</a> ';
		/***
		 * We've hit our 4th button... line-break this... and iterate to the next one!
		 */
			if($bdbuttonCNT % 5 == 4) {
				echo '
								<br /><br />';
			}
			$bdbuttonCNT++;
		}
	}
	echo '
							<div class="bdCTR"><br />To delete any custom button, simply click on it and confirm the decision.</div>
						</fieldset>
					</div>
					<div class="bdColumn">
						<div id="bdSCcontainer">
							<form action="admin.php?page=bdbuttons" method="post">
								<dl class="bdfancyList">
									<dt class="bdCTR"><input type="submit" name="submit" value="Create!"></dt><dd class="bdCTR" style="font-weight: bold;">Create new button</dd>
									<dt>Button Name</dt><dd><input type="text" id="bdbuttonName" name="bdbuttonName" maxlength="12"></dd>
									<dt>Text Color</dt><dd><input type="text" id="bdbuttonText" name="bdbuttonText" class="bdbuttonbuilder" value="#544cc4" maxlength="7"></dd>
									<dt>Background</dt><dd><input type="text" id="bdbuttonBG" name="bdbuttonBG" class="bdbuttonbuilder" value="#70c24a" maxlength="7"></dd>
									<dt>Bold Text?</dt><dd><label><input type="radio" name="bdbuttonBold" value="0" checked /> No</label>&emsp;<label><input type="radio" name="bdbuttonBold" value="1" /> <strong>Yes</strong></label></dd>
									<dt>Text Size</dt><dd><label style="font-size: 1em;"><input type="radio" name="bdbuttonFS" value="1" checked /> Normal</label>&emsp;<label style="font-size: 1.1em;"><input type="radio" name="bdbuttonFS" value="1.1" /> 1.1</label>&emsp;<label style="font-size: 1.2em;"><input type="radio" name="bdbuttonFS" value="1.2" /> 1.2</label>&emsp;<label style="font-size: 1.3em;"><input type="radio" name="bdbuttonFS" value="1.3" /> 1.3</label>&emsp;<label style="font-size: 1.4em;"><input type="radio" name="bdbuttonFS" value="1.4" /> 1.4</label>&emsp;<label style="font-size: 1.5em;"><input type="radio" name="bdbuttonFS" value="1.5" /> 1.5</label></dd>
								</dl>
							</form>
						</div>
						<div><strong>IMPORTANT:</strong> See the "<u>About</u>" tab for button creation instructions first!</div>
						<div><br />To use any of the buttons you see to the left, head over to your page or post editor and make sure you\'re on visual mode. Click the "BD Buttons" text to open the pop up window to use these buttons.</div>
					</div>
				</div>
			</div>
		</div>
		<div id="bdTab-content2" class="bdTab-content">
			<div class="bdWrapper">
				<div class="bdRow">
					<div class="bdDColumn">
						<h2 class="bdCTR">About BD Buttons</h2>
						<div>For my day job, I had a location\'s site that needed to have a way to create their own eye-catching call to action link design. Other work sites also utilize these types of catchy link styles, so I came up with a plugin that provides an easy to use interface for creating your own link "button" styles, and an even easier way to get them deployed on any page or post (including custom post types).</div>
						<div><br />BD Buttons was developed to empower the every day person to be able to buttonize any link with an attention grabbing design..</div>
						<br />
						<ul class="bdList">
							<li><strong>Potential BD Buttons issues/suggestions</strong></li>
							<li>Upon activation, the "bdbuttons" folder inside the main plugin folder gets moved to wp-content/uploads. If this fails, you can manually move that folder over. It\'ll need to be either owned by the Apache user, or the entire folder needs to be CHMOD\'d to 777, so that the two files (one .css and one .txt) can be web-writeable. No PHP code and no JS code can be executed directly from either file, FYI.</li>
						</ul>
						<br />
						<ul class="bdList">
							<li><strong>What\'s next for BD Buttons?</strong></li>
							<li>Internationalization.</li>
							<li>Minifying all core CSS and JS files.</li>
							<li>Configurable option to minify custom CSS and JS files.</li>
							<li>Gutenberg Block support (urgh!)</li>
							<li>Link Text integration with WordPress\' DashIcons and/or Font Awesome icons.</li>
							<li>Suggestions from you?</li>
						</ul>
						<div>The above items are planned updates/enhancements, as this plugin moves forward. Not all of them will be implemented in the next release.&ensp;As I "tick off the checklist", I\'ll note the date/version that feature was added and move it to the bottom of each section.</div>
					</div>
					<div class="bdColumn">
						<div>
							<h3 class="bdCTR">Creating NEW buttons</h3>
							<div><strong>Button Name:</strong> Maximum of 12 characters, containing numbers, letters, spaces, dashes and underscores, only.</div>
							<div><br /><strong>Text and Background colors:</strong> Need to be selected. Must have Javascript enabled on your browser to use the color picker.</div>
							<div><br /><strong>BD Buttons:</strong> The button selector/generator functionality should show up on any page where you have the ability to use the Visual Editor (including custom post types).</div>
							<div><br /><strong>Quick Maintenance Note:</strong> You shouldn\'t need to use this, but if you ever need to regenerate the button\'s configuration file or CSS file, you can click on the button below.</div>
							<form action="admin.php?page=bdbuttons" method="post">
								<input type="hidden" name="bdbuttonGenerate" value="Yes" />
								<div class="bdCTR"><input type="submit" name="submit" value="Regenerate files..." /></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
<!--
	Tabs for future content/update?
		<div id="bdTab-content3" class="bdTab-content">
		</div>
-->
		<div id="bdTab-content4" class="bdTab-content">';

	/***
	 * Centralizing the latest news from BD Plugins...
	 */
	include plugin_dir_path( __FILE__ ) . "includes/BDPluginsNews.php";

	echo '
		</div>
	</div>';
}

/***
 * Get a list of all BD CSS Button styles and enqueue it into the TinyMCE editor CSS
 */
function bdbuttons_tinymce_inline_style($settings) {
	global $wpdb;
	$bdbuttonDBTable = $wpdb->prefix . 'bdButtons';
	$bdbuttonList = $wpdb->get_results("
		SELECT * 
		FROM $bdbuttonDBTable 
		ORDER BY `bdbuttonName` ASC
	");

	if(!empty($bdbuttonList)) {
		$thebdbuttonCSS = "";
		$bdbuttonCNT = 1;
		foreach($bdbuttonList as $thebdbutton){
		/***
		 * We want to sanitize each and every item being pulled out of the DB (which is only two things)
		 */
			$bdbuttonCSS = esc_html($thebdbutton->bdbuttonCSS);
			$bdbuttonText = esc_html($thebdbutton->bdbuttonText);
			$bdbuttonBG = esc_html($thebdbutton->bdbuttonBG);
			$bdbuttonBold = esc_html($thebdbutton->bdbuttonBold);
			$bdbuttonFS = esc_html($thebdbutton->bdbuttonFS);

		/***
		 * Output the css for each custom button...
		 */
			if($bdbuttonCNT > 1) {
				$thebdbuttonCSS .=' ';
			}

			$thebdbuttonCSS .= '.' . $bdbuttonCSS . '{background: ' . $bdbuttonBG . '; color: ' . $bdbuttonText . ' !important;';

			if($bdbuttonBold == 1) {
				$thebdbuttonCSS .= ' font-weight: bold;';
			}
			$thebdbuttonCSS .= ' font-size: ' . $bdbuttonFS . 'em;}';
			$bdbuttonCNT++;
		}
	}

	/***
	 * Now, we need to add the CSS styling list into the TinyMCE Editor...
	 */
	$settings["content_style"] = $thebdbuttonCSS;
	return $settings;
}
add_filter('tiny_mce_before_init', 'bdbuttons_tinymce_inline_style');

/***
 * Upon activation, we need to create a bdbuttons DB table, if it doesn't exist.
 */
function bdbuttons_install() {
	global $wpdb;
	$bdbuttonDBTable = $wpdb->prefix . 'bdButtons';

	/***
	 * Sets up the DB table for custom BD Buttons.
	 */
	if($wpdb->get_var("SHOW TABLES LIKE '$bdbuttonDBTable'") != $bdbuttonDBTable) {
		$charset_collate = $wpdb->get_charset_collate();
		$sql = "
			CREATE TABLE $bdbuttonDBTable (
				id int(8) NOT NULL AUTO_INCREMENT,
				bdbuttonName varchar(12) NOT NULL,
				bdbuttonCSS varchar(32) NOT NULL,
				bdbuttonText varchar(16) NOT NULL,
				bdbuttonBG varchar(16) NOT NULL,
				bdbuttonBold varchar(16) NOT NULL,
				bdbuttonFS varchar(16) NOT NULL,
				UNIQUE KEY id (id)
			) $charset_collate;
		";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	/***
	 * Checks to see if the bdbuttons folder exists in wp-content/uploads. If it doesn't,
	 * we'll copy the folder over. We'll deal with creation errors in a future version.
	 */
	$bdbuttonsPluginFolder = plugin_dir_path( __FILE__) . 'bdButtons/';
	$uploadDir = wp_get_upload_dir();
	$bdbuttonsFolder = $uploadDir['basedir'] . '/bdButtons/';
	if(!file_exists($bdbuttonsFolder)) {
		if(wp_mkdir_p($bdbuttonsFolder)) {
			bdbuttons_move_files_to_upload($bdbuttonsPluginFolder, $bdbuttonsFolder);
		}
	}
}
register_activation_hook(__FILE__, 'bdbuttons_install');

/***
 * Function to copy files around.
 */
function bdbuttons_move_files_to_upload($source_dir, $destination_dir) {
	$dir = opendir($source_dir);
	while($file = readdir($dir)) {
		if(($file != '.') && ($file != '..')) {
			if(is_dir($source_dir.'/'.$file)) {
				recursive_files_copy($source_dir.'/'.$file, $destination_dir.'/'.$file);
			} else {
				copy($source_dir.'/'.$file, $destination_dir.'/'.$file);
			}
		}
	}
	closedir($dir);
}

/***
 * This function is called to update the /wp-content/uploads/bdButtons/bdButtons.txt
 * for use in the Visual Post/Page editor (TinyMCE)...
 */
function bdbuttons_update_bdbuttonstxt() {
	global $wpdb;
	$bdbuttonDBTable = $wpdb->prefix . 'bdButtons';
	$thebdbuttonOutput = "";

	$bdbuttonList = $wpdb->get_results("
		SELECT * 
		FROM $bdbuttonDBTable 
		ORDER BY `bdbuttonName` ASC
	");

	if(!empty($bdbuttonList)) {
		$bdbuttonRows = array_chunk($bdbuttonList, 5);
		$bdbuttonCNT = 2;

		foreach($bdbuttonRows as $bdbuttonRow) {
			$thebdbuttonOutput .= '
					{
						type   : \'buttongroup\',
						name   : \'bdbuttongroup' . $bdbuttonCNT . '\',
						id: \'bdbuttongroup' . $bdbuttonCNT . '\',
						label: \'Select a Button\',
						items: [';

			foreach ($bdbuttonRow as $bdbuttonValue) {
				/***
				 * We want to sanitize each and every item being pulled out of the DB (which is only two things)
				 */
				$bdbuttonName = esc_html($bdbuttonValue->bdbuttonName);
				$bdbuttonCSS = esc_html($bdbuttonValue->bdbuttonCSS);

				$thebdbuttonOutput .= '	
							{
								text: \'' . $bdbuttonName . '\', 
								value: \'' . $bdbuttonCSS . '\', 
								classes: \'bdButton ' . $bdbuttonCSS . '\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'' . $bdbuttonCSS . '\';
								}
							},';
			}
			$thebdbuttonOutput .= '
						]
					},';
			$bdbuttonCNT++;
		}
	}

	$bdbuttonsFileOutput = '(function () {
	tinymce.PluginManager.add(\'custom_mce_button1\', function(editor, url) {
		editor.addButton(\'custom_mce_button1\', {
			icon: false,
			text: \'BD Buttons\',
			onclick: function (e) {
				editor.windowManager.open( {
					title: \'Stylize your link with a button-ish type look\',
					body: [{
						type: \'textbox\',
						name: \'linktext\',
						label: \'Link Text\',
						placeholder: \'Link Text\',
						minWidth: 100,
						id: \'bdplinktext\'
					},
					{
						type: \'textbox\',
						name: \'linkurl\',
						label: \'Link URL\',
						placeholder: \'Link URL\',
						minWidth: 100,
						id: \'bdplinkurl\'
					},
					{
						type: \'checkbox\',
						name: \'bdpnewwindow\',
						label: \'Open in New Window\',
						text: \'If the URL is not part of the site, check this box.\',
						id: \'bdpnewwindow\'
					},
					{
						type   : \'buttongroup\',
						name   : \'bdbuttongroup1\',
						id: \'bdbuttongroup1\',
						label: \'Select Button 1\',
						items: [
							{
								text: \'Green\', 
								value: \'bdpGreen\', 
								classes: \'bdButton bdpGreen\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpGreen\';
								}
							},
							{
								text: \'Red\', 
								value: \'bdpRed\', 
								classes: \'bdButton bdpRed\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpRed\';
								}
							},
							{
								text: \'Blue\', 
								value: \'bdpBlue\', 
								classes: \'bdButton bdpBlue\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpBlue\';
								}
							},
							{
								text: \'White\', 
								value: \'bdpWhite\', 
								classes: \'bdButton bdpWhite\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpWhite\';
								}
							},
							{
								text: \'Black\', 
								value: \'bdpBlack\', 
								classes: \'bdButton bdpBlack\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpBlack\';
								}
							},
							{
								text: \'BD Purple\', 
								value: \'bdpPurple\', 
								classes: \'bdButton bdpPurple\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpPurple\';
								}
							},
							{
								text: \'BD Gold\', 
								value: \'bdpGold\', 
								classes: \'bdButton bdpGold\',
								onclick: function() {
									document.getElementById(\'bdbuttoncolor\').value = \'bdpGold\';
								}
							}
						]
					},';
	$bdbuttonsFileOutput .= $thebdbuttonOutput;
	$bdbuttonsFileOutput .= '
					{
						type: \'textbox\',
						name: \'bdbuttoncolor\',
						label: \'Selected Colors\',
						minWidth: 100,
						id: \'bdbuttoncolor\',
						value: \'bdpBlack\'
					}],
					onsubmit: function(e) {
						if(e.data.bdbuttoncolor == null) {
							buttoncolorbd = \'bdpBlack\';
						} else {
							buttoncolorbd = e.data.bdbuttoncolor;
						}

						send_to_editor = \'<a class="bdButton \' + buttoncolorbd + \'" href="\' + e.data.linkurl + \'"\';

						if(e.data.bdpnewwindow === true) {
							send_to_editor += \' target="_blank"\';
						}

						send_to_editor +=\'>\' + e.data.linktext + \'</a> \';

						editor.insertContent(send_to_editor);
					}
				});
			}
		});
	});
})();';

	/***
	 * Now that we have the updated content for the wp-content/uploads/bdButtons/bdButtons.txt
	 * file set, let's update it!
	 */
	$bdbuttonsuploadDir = wp_upload_dir();
	$thebdbuttonsFile = $bdbuttonsuploadDir['basedir'] . '/bdButtons/bdButtons.txt';
	file_put_contents($thebdbuttonsFile, $bdbuttonsFileOutput);
}

/***
 * This function is called to update the /wp-content/uploads/bdButtons/bdButtons.css
 * for use in the Visual Post/Page editor (TinyMCE), back and front ends...
 */
function bdbuttons_generate_custom_css() {
	global $wpdb;
	$bdbuttonsuploadDir = wp_upload_dir();
	$bdbuttonsCSTCSSPath = $bdbuttonsuploadDir['basedir'] . '/bdButtons/bdButtons.css';

	$bdbuttonDBTable = $wpdb->prefix . 'bdButtons';
	$bdbuttonCustomCSSList = "";

	$bdbuttonList = $wpdb->get_results("
		SELECT * 
		FROM $bdbuttonDBTable 
		ORDER BY `bdbuttonName` ASC
	");

	if(!empty($bdbuttonList)) {
		$bdbuttonCNT = 1;
		foreach($bdbuttonList as $thebdbutton){
		/***
		 * We want to sanitize each and every item being pulled out of the DB (which is only two things)
		 */
			$bdbuttonName = esc_html($thebdbutton->bdbuttonName);
			$bdbuttonCSS = esc_html($thebdbutton->bdbuttonCSS);
			$bdbuttonText = esc_html($thebdbutton->bdbuttonText);
			$bdbuttonBG = esc_html($thebdbutton->bdbuttonBG);
			$bdbuttonBold = esc_html($thebdbutton->bdbuttonBold);
			$bdbuttonFS = esc_html($thebdbutton->bdbuttonFS);

		/***
		 * Output the css for each custom button...
		 */
			if($bdbuttonCNT > 1) {
				$bdbuttonCustomCSSList .= '

';
			}

			$bdbuttonCustomCSSList .= '/*** '. $bdbuttonName . ' ***/
.mce-' . $bdbuttonCSS . ' button, .' . $bdbuttonCSS . ' {
	background: ' . $bdbuttonBG . ';
	color: ' . $bdbuttonText . ' !important;';

			if($bdbuttonBold == 1) {
				$bdbuttonCustomCSSList .= '
	font-weight: bold;';
			}
			$bdbuttonCustomCSSList .= '
	font-size: ' . $bdbuttonFS . 'em;
}';
			$bdbuttonCNT++;
		}
	file_put_contents($bdbuttonsCSTCSSPath, $bdbuttonCustomCSSList);
	}
}

/***
 * This will be activated in a future version. Will give the option to purge data, upon deactivation.
 */
//function bdbuttons_deactivate() {
//	/***
//	 * Clearing out the timezone offset option.
//	 */
//	$option_name = 'rpuAllowedTags';
//	delete_option($option_name);
//	delete_site_option($option_name);
//}
//register_deactivation_hook(__FILE__, 'bdbuttons_deactivate');

/***
 * This adds a button to the WordPress WYSIWYG interface to buttonize text links.
 */
function bdbuttons_mce_buttons() {
	// Check if WYSIWYG is enabled
	if(get_user_option('rich_editing') == 'true') {
		add_filter('mce_external_plugins', 'bdbuttons_tinymce_plugin');
		add_filter('mce_buttons', 'bdbuttons_register_mce_buttons');
	}
}
add_action('admin_head', 'bdbuttons_mce_buttons');

// Add the path to the js file with the custom button function
function bdbuttons_tinymce_plugin($plugin_array) {
	$bdbuttonsuploadDir = wp_upload_dir();
	$plugin_array['custom_mce_button1'] = $bdbuttonsuploadDir['baseurl'] . '/bdButtons/bdButtons.txt';
	return $plugin_array;
}

// Register and add new button in the editor
function bdbuttons_register_mce_buttons($buttons) {
	array_push($buttons, 'custom_mce_button1');
	return $buttons;
}
?>