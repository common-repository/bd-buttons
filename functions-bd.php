<?php
/***
 * File info: Adds the BD PLugins main menu and aboutbd() section for all plugins.
 * Will only add it once, if it's not already present.
 */

/***
 * To ensure arrays are properly sanitized to WordPress Codex standards,
 * they encourage usage of sanitize_text_field(). That only works with a single
 * variable (string). This function allows for a full blown array to get sanitized
 * properly, while sanitizing each individual value in a key -> value pair.
 *
 * This file (and the three functions below) are used across several of my plugins.
 * I'm aware the WP Plugin Review team is not a fan of the !function_exist() usage,
 * however, I've tried to make sure the function names are unique enough, so that
 * I can ensure ALL of my plugins go into a sub menu of one main WP Admin nav link.
 * This helps ensure I am not cluttering up the WP Admin Nav menu on the left with my plugins.
 *
 * Source: https://wordpress.stackexchange.com/questions/24736/wordpress-sanitize-array
 * Author: Broshi, answered Feb 5 '17 at 9:14
 */
if(!function_exists('bearlydougplugins_recursive_sanitize_text_field')) {
	function bearlydougplugins_recursive_sanitize_text_field($array) {
		foreach($array as $key => &$value) {
			if(is_array($value)) {
				$value = recursive_sanitize_text_field($value);
			} else {
				$value = sanitize_text_field($value);
			}
		}
		return $array;
	}
}

/***
 * Adding the BD Plugins menu, if needed.
 */
if(!function_exists('bearlydougplugins_add_admin_panel')) {
	function bearlydougplugins_add_admin_panel(){
		add_menu_page( 
			'BearlyDoug Plugins',					// Page Title
			'BD Plugins',							// Menu Title
			'edit_posts',							// Capabilities
			'bearlydoug',							// Menu Slug
			'bearlydougplugins_about',				// Function name
			plugin_dir_url( __FILE__ ) . 'images/bear.gif'		// Menu icon
		);
	}
	add_action( 'admin_menu', 'bearlydougplugins_add_admin_panel' );
}

/***
 *	Check to see if About BD isn't already declared. If not, let's show it...
 */
if(!function_exists('bearlydougplugins_about')) {
	function bearlydougplugins_about() {

	global $wpdb;

	/***
	 * Processing the timezone offset...
	 */
	if(isset($_REQUEST['ldotwSubmit']) && ($_REQUEST['ldotwSubmit'] == "1") && (filter_var($_REQUEST['TZOffset'], FILTER_VALIDATE_INT) !== FALSE)) {
		$TZOffset = sanitize_text_field($_REQUEST['TZOffset']);
		update_option('bdtzoffset', $TZOffset, 'yes' );
	}

	/***
	 * We need to get the timezone offset and correct any time inconsistencies...
	 */
	$TZOffset = get_option('bdtzoffset', '0');

	/***
	 * Going to go ahead and set up some variables to handle the server time and corrected WP time
	 * notations for the timezone offset stuff. We'll also set up an array for the corrected WP time correction
	 * feature in the Shortcode Builder area.
	 *
	 * Setting up the offset minutes...
	 */
	$offsetMinutes = str_replace("-", "", $TZOffset) * 3600;

	/***
	 * If TZOffset is less than 0, then we'll subtract the time. If greater, add it. $tNOW is always based on GMT.
	 */
	$timeNOW = time();
	if($TZOffset >= 0) {
		$correcedTime = $timeNOW + $offsetMinutes;
	} else {
		$correcedTime = $timeNOW - $offsetMinutes;
	}

	$ServerTime = date("h:i:s A", $timeNOW);
	$CorrectedTime = date("h:i:s A", $correcedTime);
	$TZones = array("-12", "-11", "-10", "-9.5", "-9", "-8", "-7", "-6", "-5", "-4", "-3.5", "-3", "-2.5", "-2", "-1", "0", "1", "2", "3", "3.5", "4", "4.5", "5", "5.5", "5.75", "6", "6.5", "7", "8", "9", "9.5", "10", "10.5", "11", "12", "12.75", "13", "13.75", "14");

	echo '
	<h1 class="bdCTR">About BD Plugins and Doug "BearlyDoug" Hazard</h1>
	<div class="bdTabs">
<!-- bdTabs Navigation Tabs -->
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab3" checked>
		<label class="bdLabel" for="bdTab3"><i class="dashicons dashicons-clock"></i> <span>Time Zone Settings</span></label>
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab2">
		<label class="bdLabel" for="bdTab2"><i class="dashicons dashicons-thumbs-down"></i> <span>Gutenberg</span></label>
		<input type="radio" class="bdRadio" name="bdTabs" id="bdTab1">
		<label class="bdLabel" for="bdTab1"><img alt="BearlyDoug" src="' . plugin_dir_url( __FILE__ ) . 'images/bear.gif" class="bdBear" /> <span>About BearlyDoug</span></label>

<!-- bdTabs Content Tabs -->
		<div id="bdTab-content1" class="bdTab-content">
			<div class="bdWrapper">
				<div class="bdRow">
					<div class="bdDColumn">
						<div><img alt="" src="' . plugin_dir_url( __FILE__ ) . 'images/bear.gif' . '" class="alignright" /><strong>The "BearlyDoug" story</strong>: Many years ago, shortly after my wife and I started dating, she remarked that I was an absolute bear until I woke up. Since I had to leave fairly early for work, she started making coffee (I wasn\'t much of a coffee drinker back then) to help wake me up. Once I woke up, I wasn\'t so grumpy. The nickname (bear) then stuck</div>
						<div><br />Through the years, I\'ve embraced that personality and even located my beloved stomping bear GIF in the mid 2000s. Today, the above bear continues to represent a significant portion of my identity, both online and off. I\'m literally everywhere as @BearlyDoug. It\'s now FOUR tattoos on my upper left arm. It\'s a 14" tall stuffed bear (same outfit, and a suit outfit). There\'s even a rumor that I will *occasionally* dress up in the same outfit you see above.</div>
						<div><br />Today, I\'m still a bear until I\'ve had coffee, or when someone gets on my bad side. I\'ll continue to be "Bearly Doug" as long as I am breathing. MUCH <span class="dashicons dashicons-heart" style="color: red;"></span> to my wife, Sonia, for this!</div>
						<div><br />As a full time WordPress developer, responsible for nearly 200 websites, I\'m run pretty ragged at times. In addition to full time WP work, I\'m the technical advisor/administrator to two sports networks (Started in 2003 and 2013, respectively), helping advise on web tech issues, development, as needed, bodyguard as needed, jack of all trades, master of none (HAH!)</div>
						<div><br />When I\'m not sitting behind the computer, you\'ll usually find me on an American football field or basketball court with my cameras (and remotes) in hand, taking photos, and building relationships with fellow media folks, incredible people (who also happen to be athletes), Coaches, Sports Information Directors, and most importantly, fans.</div>
						<div><br />In my past life, I\'ve been a DSL services provisioner, worked for two web hosting companies, and owned my own web hosting/development company, a Y2K Software Upgrade specialist, a Loan Origination Software Support specialist, office assistant, Medicare Insurance department clerk and so much more.</div>
						<div><br />And, yes, the rumors are true. I even have one professional wrestling match under my belt (13 man Battle Royale in 1991, thrown over the top rope by my childchood idol, "Iceman" King Parsons.</div>
						<br /><div class="bdCT">
							<div class="bdBox2 bdCTR"><b>Doug, in a side headlock, 2010, Austin, TX.</b><br /><img alt="" src="' . plugin_dir_url( __FILE__ ) . 'images/DougInRing.jpg" style="width: 100%; height: auto;" /></div>
							<div class="bdBox2 bdCTR"><b>Doug, the "Enforcer", 2010, Austin, TX.</b><br /><img alt="" src="' . plugin_dir_url( __FILE__ ) . 'images/DougTheEnforcer.jpg" style="width: 100%; height: auto;" /></div>
						</div>
						<div><br />Been with my wife for over two decades and adore both of my kids (technically, step-kids, but love them as if they are mine). One is in the Navy (8+ years) and the other does what he wants, when he wants and how he wants. BOTH are a chip off their Mom\'s shoulder, and I love \'em for it!</div>
						<hr>
						<div><br /><strong>Some quick shout-outs:</strong><br />&bull; <a href="https://www.linkedin.com/in/thomasjuberg/" target="_blank">Thomas Juberg</a>, AKA "Brother Bear". One of my best men when I got married, the one that opened my eyes on semantic and proper CSS development, one of my best friends, who lives doggone near half a world away.<br />&bull; <a href="https://www.facebook.com/JohnJDonnaII" target="_blank">John Donna</a>. My WordPress Mentor (and sometimes Mentee, now), former boss, former co-worker, good friend. He\'s there when I need him, and even when I don\'t. ;)<br />&bull; My wife, Sonia, and my kidlings, Andria and Kaleb. Cause no shout-out would be complete without them!<br />&bull; Andor Nagy (@AndorNagy) with WebDesignHut, for the beautiful tabbed content structure you see here.<br />&bull; YOU, for downloading and installing one of my plugins!</div>
					</div>
					<div class="bdColumn">
						<div>
							<h3 class="bdCTR">Want to show some love?</h3>
							<img class="bdFloatLeft" src="' . plugin_dir_url( __FILE__ ) . 'images/CodeForCoffee.jpg" />
							<a href="https://paypal.me/BearlyDoug" target="_blank"><img src="' . plugin_dir_url( __FILE__ ) . 'images/PayPal.jpg" /></a><br />
							<a href="https://cash.app/$BearlyDoug" target="_blank"><img src="' . plugin_dir_url( __FILE__ ) . 'images/CashApp.jpg" /></a>
							<div><br />If this plugin has helped you out, why not chip in a few bucks to buy me a cup of coffee or something?&ensp;I have no plans on making this plugin (or any others) a commercial (paid upgrade) one, so kicking in a few bucks (via the PayPal or CashApp images/links above) helps keep this plugin (and others) movin\' forward.&ensp;Thanks!</div>
						</div>

						<div><br />
							<ul class="bdList">
								<li>Let\'s get SOCIAL!</li>
								<li><span class="dashicons dashicons-twitter"></span> <a href="https://twitter.com/BearlyDoug" target="_blank">@BearlyDoug</a></li>
								<li><span class="dashicons dashicons-facebook"></span> <a href="https://www.facebook.com/BearlyDoug/" target="_blank">@BearlyDoug</a></li>
								<li><img alt="" src="' . plugin_dir_url( __FILE__ ) . 'images/vimeo.png" /> <a href="https://vimeo.com/bearlydoug" target="_blank">@BearlyDoug</a></li>
								<li><span class="dashicons dashicons-linkedin"></span> <a href="https://www.linkedin.com/in/douglashazard/" target="_blank">/in/DouglasHazard</a></li>
								<li><span class="dashicons dashicons-admin-site-alt3"></span> <a href="https://bearlydoug.com" target="_blank">BearlyDoug.com</a></li>
							</ul>
							<div>Yep, I\'m all over the place! Twitter is where I am most active, but I do keep an eye on the others, below.</div>
							<div><br />While I am always responsive via Social Media, I will not provide support for my plugins through Social Media. Each of my Social Media pages typically is for different aspects of my life. I ask that you please respect my desire to not provide support outside of the official WordPress plugins support area. Thanks!</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="bdTab-content2" class="bdTab-content">
			<div class="bdWrapper">
				<div class="bdRow">
					<div class="bdDColumn">
						<div class="bdCTR"><img alt="" src="' . plugin_dir_url( __FILE__ ) . 'images/gutenborg.png' . '" style="max-width: 500px; width: 100%;" /></div>
						<div><br />Since I\'ve gotten into WordPress plugin development, and with the arrival of the new Gutenberg editor (released with WordPress 5.0 on December 6, 2018), I\'ve been wrangling with whether to support the Gutenberg interface or not. Of my current five (soon to be six) plugins, only one actually interacts with the WordPress visual editor interface: BD Buttons.</div>
						<div><br />As a PHP and WordPress developer, I\'ve gone back and forth about whether I like or dislike it. Honestly, and much to the chagrin of some, I am surprisingly anti page builder... and that\'s essentially what Gutenberg is. It\'s a competitor to the Divi theme (which is its own page builder), BeaverBuilder, Elementor, WP Bakery (AKA Visual Composer) and others like that.</div>
						<div><br />For the average user, having the ability to click and drag what you want, where you want is a great idea, in theory. When you make a mistake, though, is where the fun can (sometimes) begin.</div>
						<div><br />From a developer\'s standpoint, and as someone who is dialed in on CLEAN and OPTIMIZED HTML, CSS and JavaScript coding, page builders are the bane of our existence. The amount of bloated coding added to your site as a result of page builders can be mind boggling. There is also a risk that page builders will not properly close an open tag, which could break your site\'s layout.</div>
						<div><br />With the WordPress 5.8 version release (on  July 20, 2021), WordPress continues its GutenBORGification of their entire platform.</div>
						<div><br />Look, I get it. Sites like Wix, SquareSpace and others make it really attractive and super easy to build your own site. Click click here, drag drag there, type type here, click click clickety click click click and your\'e done. Now you have a brand new site that you think looks good!</div>
						<div><br />Except that it doesn\'t.</div>
						<div><br />The average site visitor may or may not know you used a page builder or a site like Wix or SquareSpace, but a savvy site visitor will. And for some of us, that also leads credence to your own business/website\'s reputation.</div>
						<div><br />If it sounds like I\'m bitter about this potentially taking website development work away from me, trust me, I\'m not. I\'m a full time salaried developer. I get paid the same whether I churn out a new website every week, or spending a few months to develop something truly unique.</div>
						<div><br />Yes, I do sometimes freelance. Most of my freelancing work centers around optimizing sites to speed them up, or to fix something broken. The #1 cause of site slowdowns? Page Builder plugins, themes and stuff like Gutenberg.</div>
						<div><br />Give me two identical sites, one built manually, and the other with any kind of a page builder. I guarantee you the site I build manually will be about five times faster than one built using a page builder. And site speed tests consistently bear this out.</div>
						<div><br />Take a look at this article: <a href="https://www.angoramedia.com/blog/seo/does-wordpress-website-builder-affect-page-speed/" target="_blank">Beaver Builder vs Elementor vs Brizy vs Gutenberg: How Much Does Your WordPress Website Builder Affect Page Speed?</a></div>
						<div><br />The other thing to look at is bringing people up to speed with the inner-workings of content management. For my day job, a considerable amount of time has been invested/spent in training our coworkers on how to manage their own department\'s pages and content. We would have to completely revise ALL training material, videos and methods if we were to switch over to using the Gutenberg interface. For smaller companies, this isn\'t cost/time prohibitive, but it is for a larger organization.</div>
						<div><br />Bottom line, for me, is that I refuse to be assimilated by the GutenBORGification of WordPress. I will continue to develop plugins that are not reliant on block editors, but that can still work within those constraints.</div>
					</div>
					<div class="bdColumn">
						<h3 class="bdCTR">Doug recommends...</h3>
						<div class="bdCTR"><a href="https://wordpress.org/plugins/disable-gutenberg/" target="_blank"><img src="' . plugin_dir_url( __FILE__ ) . 'images/disable-gutenberg-plugin-icon.png" /></a></div>
						<div><a href="https://wordpress.org/plugins/disable-gutenberg/" target="_blank">Disable Gutenberg Plugin</a><br />by Jeff Starr</div>
						<div><br />I heavily recommend the above plugin to disable the Gutenberg interface and to restore the Classic Editor and Classic Widgets interfaces. After you install and activate the "Disable Gutenberg" plugin, head over to <a href="/wp-admin/options-general.php?page=disable-gutenberg">Settings -> Disable Gutenberg</a> and disable the Block widgets, as well (not disabled by default).</div>
						<div><br />
							<h3 class="bdCTR">Resources</h3>
							<ul class="bdList">
								<li><a href="https://wptavern.com/where-gutenberg-went-wrong-theme-developer-edition" target="_blank">Where Gutenberg Went Wrong: Theme Developer Edition</a></li>
								<li><a href="https://www.uplers.com/blog/gutenberg-vs-page-builders/" target="_blank">Gutenberg vs. WordPress Page Builders: What\'s the Difference?</a></li>
								<li><a href="https://www.namecheap.com/blog/wordpress-developers-on-gutenberg/" target="_blank">What WordPress Developers Think of Gutenberg</a></li>
								<li><a href="https://www.woodst.com/web-design-development/gutenberg-is-invading-your-wordpress-website-wait-who/" target="_blank">Gutenberg Has Invaded Your WordPress Website. Wait... Who?</a></li>
								<li><a href="https://www.kristinfalkner.com/to-gutenberg-or-not-to-gutenberg/" target="_blank">To Gutenberg or Not to Gutenberg, Is That Still a Question?</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="bdTab-content3" class="bdTab-content">
			<form action="admin.php?page=bearlydoug" method="post">
				<input type="hidden" name="ldotwSubmit" value="1" />
				<div><br /><strong>NOTE 1:</strong> We do not automatically adjust for Daylight Savings Time / Standard Time.</div>
				<div><br /><strong>NOTE 2:</strong> Setting this sets it for each of the plugins listed here:<br /> &bull; Liturgical Day of the Week<br /> &bull; BD Business Hours</div>
				<br />
				<fieldset>
					<legend>Time zone setting</legend>
					<div>This plugin is time-zone dependent. On some servers, you\'ll need to set the "Offset Correction" to match your local time.</div>
					<div><br />For most US based folks, the range should be somewhere between 0 and -8.</div>
					<dl class="fancyList2">
						<dt>Server Time</dt><dd>' . $ServerTime . '</dd>
						<dt>Fixed Time</dt><dd>' . $CorrectedTime . '</dd>
						<dt>Offset Correction</dt>
						<dd>
							<select name="TZOffset">';
	foreach($TZones as &$theTimeZone) {
		$tzSelected = ($TZOffset == $theTimeZone) ? ' selected' : '';
		echo '
								<option value="' . $theTimeZone . '"' . $tzSelected . '>' . $theTimeZone . '</option>';
	}

	echo '
							</select> hrs
						</dd>
					</dl>
					<div class="bdCTR"><input type="submit" value="Save Time Offset" /></div>
				</fieldset>
			</form>
		</div>
	</div>
		';
	}
}
?>