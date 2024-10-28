=== BD Buttons ===
Contributors: BearlyDoug
Plugin URI: TBD
Donate link: https://paypal.me/BearlyDoug
Tags: Links, stylize links, button link, link buttons
Requires at least: 5.2
Tested up to: 6.4.1
Stable tag: 1.0.5
Requires PHP: 7.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

BD Buttons was developed to empower the every day person to be able to buttonize any link with an attention grabbing design.

== Description ==
BD Buttons was developed to empower the every day person to be able to buttonize any link with an attention grabbing design.

For my day job, I had a location's site that needed to have a way to create their own eye-catching call to action link design. Other work sites also utilize these types of catchy link styles, so I came up with a plugin that provides an easy to use interface for creating your own link "button" styles, and an even easier way to get them deployed on any page or post (including custom post types).

Comes with 7 default link buttons, and allows you to customize your own color combinations, via a color palette or HTML color codes (if you already know what color combinations you want).

**Current Version 1.0.5**

= Features: = 
* Built into the Visual Editor, just type a few things, click a few things and your link is stylized.
* Easy to use admin interface for creating your own unique link button color combinations.
* CSS and "BD Button" configuration reside inside wp-content/uploads, to avoid permission issues.

This plugin is not compatible with WordPress versions less than 5.0. Requires PHP 5.6+.

= TROUBLESHOOTING: =
* Upon activation, the "bdbuttons" folder inside the main plugin folder gets moved to wp-content/uploads. If this fails, you can manually move that folder over. It'll need to be either owned by the Apache user, or the entire folder needs to be CHMOD'd to 777, so that the two files (one .css and one .txt) can be web-writeable. No PHP code and no JS code can be executed directly from either file, FYI.
* On some systems, the CSS file is cached. You will need to clear your server's cache to reflect any changes you make via custom link button additions.

== Installation ==

= If you downloaded this plugin: =
1. Upload the 'bdbuttons' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Once activated, locate the "BD Plugins" section in WP Admin, and then click on "BD Buttons".
4. Follow the directions on the various tabs.

= If you install this plugin through WordPress 2.8+ plugin search interface: =
1. Click "Install", once you find the "BD BUttons" plugin.
2. Activate the plugin through the 'Plugins' menu.
3. Once activated, locate the "BD Plugins" section in WP Admin, and then click on "BD BUttons".
4. Follow the directions on the various tabs.

== Frequently Asked Questions ==
** As this is the first release of BD Buttons, FAQs are a little minimal right now ** 

= Where's the widget for this? Gutenberg block?! =
Coming in a future version, I promise!

= Why is the Admin interface not in [LANGUAGE] language? =
Internationalization will be coming very soon.

= What's with the animated bear icon / Why "BearlyDoug"? =
You'll need to check out the plugin and click on "BD Plugins" after you activate this plugin. :)

= Why free? Do you have a commercial version, too? =
Because I want to give back to the community that has given so much to me, no. What you see is what you get.WordPress has allowed me to advance my career and put me into a position where I'm doing okay. That said, you can still support this plugin (and others, as I release them) by hittin' that "Donate" link over on the right.

== Screenshots ==
1. Admin interface.
2. Color picker for text and background colors.
3. Instructions and maintenance tab.
4. BD Buttons inside a page. Note the "BD Buttons" link at the top, which takes you to...
5. BD Buttons link interface. Click click here, type type there, done!
6. Public display of buttonized links.

== Changelog ==
= TODO =
* Minifying all core CSS and JS files.
* Configurable option to minify custom CSS and JS files.
* Gutenberg Block support (urgh!)
* Link Text integration with WordPress' DashIcons and/or Font Awesome icons.
* Proper Internationalization

= 1.0.5 =
* Bumped supported WordPress version to 6.4.1
* Changed news handling under "More BD Plugins" to centralize it to a single file for ALL plugins.
* Minor changes to functions-bd.php

= 1.0.4 =
* When you have more than one button (especially on Mobile), or really long text (again, on Mobile), the layout was a bit off. Added some additional CSS and increased margins to smooth things out a bit more.
* Brought up to WordPress version 5.8

= 1.0.3 =
* On some sites, the button height would be all over the place. This version resets the line-height to initial settings via _CSS-bdButtons.css.

= 1.0.2 =
* First Public release on WordPress.org (May 20th, 2021)

= 1.0.1 =
* Initially developed for my work account; rebranded as a BD Plugin
* Switched to DB driven
* Implemented bdButtons.txt file updates with button additions/deletions.
* Standardized the main wp-admin side CSS file for usage across all my plugins.
* Introduced a "More BD Plugins" tab, linking to current plugins, announcing future planned plugins and relocated the "Support me/this plugin!" request to that page.

= 1.0.0 =
* Initial Plugin development and launch, not released. (May 3rd, 2021)

== Upgrade Notice ==
* Coming soon!