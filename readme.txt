=== Encyclopedia / Lexicon / Glossary / Wiki / Dictionary / Knowledge base ===
Contributors: dhoppe
Tags: encyclopedia, lexicon, glossary, dictionary, knowledge base, wiki, wikipedia,        widget,Post,plugin,admin,posts,sidebar,comments,google,images,page,image,links
Requires at least: 3.7
Tested up to: 3.9.1
Stable tag: trunk
Donate link: http://dennishoppe.de/en/wordpress-plugins/encyclopedia
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This great Encyclopedia Lite plugin enables you to create an awesome Encyclopedia, Lexicon, Glossary, Wiki, Dictionary or Knowledge Base!

== Description ==
[Encyclopedia](http://dennishoppe.de/en/wordpress-plugins/encyclopedia) Lite is a state of the art [WordPress encyclopedia / lexicon / glossary / wiki / dictionary plugin](http://dennishoppe.de/en/wordpress-plugins/encyclopedia). It enables you to create, manage and present a knowledge base. Completely coalesced with your WordPress and fully compatible with all the cool publishing tools you like.

In this edition of the plugin you can create a small lexicon. On several places in the plugin you will find a notice that this function is included in the [Pro Version](http://dennishoppe.de/en/wordpress-plugins/encyclopedia) only.

= Main features =
* Create, manage and structure your terms as [encyclopedia](http://dennishoppe.de/en/wordpress-plugins/encyclopedia), lexicon, glossary, Wiki, dictionary or knowledge base
* Use tags and categories¹ to classify your terms
* Automatic association of related terms
* Automatic linking of terms in post and page contents
* Completely translatable - .pot file is included
* Supports the WordPress Theme Template hierarchy
* Supports user defined HTML templates
* Supports WordPress user rights and capabilities¹
* Supports RSS Feeds
* Clean and intuitive user interface
* Multiple Widgets to display the terms and taxonomies of your [Encyclopedia](http://dennishoppe.de/en/wordpress-plugins/encyclopedia)
* No ads or brandings anywhere - perfect white label solution¹

¹ Available in [Encyclopedia Pro](http://dennishoppe.de/en/wordpress-plugins/encyclopedia)


= Settings =
You can find the settings page in WP Admin Panel &raquo; Settings &raquo; [Encyclopedia](http://dennishoppe.de/en/wordpress-plugins/encyclopedia).


= Template files =
All plugin outputs can be changed via user defined HTML templates. Just put the templates you want to overwrite inside your theme folder (no matter if parent theme or child theme). You can find the default templates in the plugin folder in "templates/".
[You can find a list of the available templates here.](http://dennishoppe.de/en/wordpress-plugins/encyclopedia#templates) *Please do not modify the original templates! You would lose all your modifications when updating the plugin!*


= Limitations =
The most features are available but you cannot select every option. You will find a small notice for each unavailable option on the settings page. The maximal number of encyclopedia terms is limited to twelve.


= Questions / Support requests =
Please use the support forum on WordPress.org for this version of the Plugin. For the [Premium Version](http://dennishoppe.de/en/wordpress-plugins/encyclopedia) there is a separate support package [available](http://dennishoppe.de/en/wordpress-plugins/encyclopedia). Of course you can hire me for consulting, support, programming and customizations at any time.


= Language =
* This Plugin is available in English.
* Diese Erweiterung ist in Deutsch verfügbar. ([Dennis Hoppe](http://DennisHoppe.de/))
* Cette extension est traduite en français. ([Gilles Santacreu](http://wikiboursier.fr/))
* Ta wtyczka jest dostępna po Polsku. ([Andrzej Opejda](http://astrolog.pl/))
* Plugin ini tersedia dalam Bahasa Indonesia ([Nasrulhaq Muiz](http://al-badar.net/))
* Este plugin está disponible en español. ([Fátima Da Silva](http://www.fcdsbtraducciones.com/))


= Translate this plugin =
If you have translated this plugin in your language or updated the language file please feel free to send me the language file (.po file) via E-Mail with your name and this translated sentence: "This plugin is available in %YOUR_LANGUAGE_NAME%." So I can add it to the plugin and a link to your website to this page.

You can find the *Translation.pot* file in the *language/* folder in the plugin directory.

* Copy it.
* Rename it (to your language code).
* Translate everything.
* Send it via E-Mail to &lt;Mail [@t] [DennisHoppe](http://DennisHoppe.de/) [dot] de&gt;.
* Thats it. Thank you! =)


== Installation ==

= Minimum Requirements =

* WordPress 3.7 or greater
* PHP version 5.2.4 or greater
* MySQL version 5.0 or greater

= Automatic installation =

Automatic installation is the easiest option as WordPress handles the file transfers itself and you do not need to leave your web browser. To do an automatic install of Encyclopedia, log in to your WordPress dashboard, navigate to the Plugins menu and click "Add New".

In the search field type "Encyclopedia" and click "Search Plugins". Once you have found my plugin you can view details about it such as the point release, rating and description. Most importantly of course, you can install it by simply clicking "Install Now".

= Manual installation =

The manual installation method involves downloading the plugin and uploading it to your webserver via your favourite FTP application. The WordPress codex contains [instructions on how to do this here](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= Updating =

Automatic updates should work like a charm; as always though, ensure you backup your site just in case.


== Frequently Asked Questions ==
I am still collecting frequently asked questions. ;)


== Screenshots ==
1. Example Archive page in TwentyTwelve Theme
2. Example single term view in TwentyTwelve Theme
3. Encyclopedia tags
4. Edit screen of an encyclopedia tag
5. Edit screen of an encyclopedia term
6. Encyclopedia items in your WP menues
7. Encyclopedia options page
8. Encyclopedia "Related terms" widget
9. Encyclopedia Taxonomy widget
10. Encyclopedia taxonomy cloud
11. Encyclopedia term list

== Changelog ==

= 1.5.2 =
* Added upgrade button to post edit section

= 1.5.1 =
* Still cleaning up the options page
* Fixed a few typos

= 1.5 =
* Cleanup options page
* Changed number of available terms
* Removed post type icon support for WP <= 3.7.3

= 1.4.15 =
* Updates custom style sheets for WP default themes

= 1.4.14 =
* Added hebrew and turkish translation files

= 1.4.13 =
* Fixed: Terms will not link to itself in theier post contents anymore

= 1.4.12 =
* Fixed: Broken iFrame tags in post contents

= 1.4.11 =
* Updated the search widget front end

= 1.4.10 =
* Patched the widget forms for more WP 3.8 compliance

= 1.4.9 =
* Plugin works in symlinkes now

= 1.4.8 =
* Added Dashicons
* Patches default style for Twenty14 Theme

= 1.4.7 =
* Added encyclopedia_term_link_title filter

= 1.4.6 =
* Added term excerpt as link title for auto linked terms
* Changed the hierachical property of the encyclopedia term to false

= 1.4.5 =
* Fixed: post_title_like works now without using the ignore_filter_request parameter

= 1.4.4 =
* Added Workaround for BuddyPress "loop-start" Filter bug

= 1.4.3 =
* Fixed the pagination bug for filtered archive pages.
* Fixed the trailing Slash bug in Archive urls.

= 1.4.2 =
* Fixed Filter URL Generator for permalink structures which are not ending with "/"

= 1.4.1 =
* Fixed rewrite rules applying when activating the plugin

= 1.4.0 =
* Added SEO friendly Filter URLs
* Added the Term filter for single term views
* Small bug fixes

= 1.3.14 =
* Fixed the Empty-Post-Content bug which let appear the DocType definition in the posts content.

= 1.3.13 =
* Added the letter navigation to the terms single view.

= 1.3.12 =
* Added Support for the letter navigation in the single view of a term.

= 1.3.11 =
* Added the "Is_Main_Query" condition to display the term filter
* Cleanup the Get_Tag_Related_Terms function

= 1.3.10 =
* Cleaned up some HTML on the options page

= 1.3.9 =
* Added Wiki and Knowledge base to the encyclopedia types

= 1.3.8 =
* Fixed an encoding bug in the auto linking feature

= 1.3.7 =
* Added spanish translation by [Fátima Da Silva](http://www.fcdsbtraducciones.com/).
* Added PHP 5.4 and lower patch: Set UTF8 encoding for HTMLEntities.

= 1.3.6 =
* Added post type support: comments, trackbacks, featured image, revisions

= 1.3.5 =
* Made Taxonomy slugs translatable

= 1.3.4 =
* Fixed a small issue which occures the appearance of the term filter on taxonomy archives
* Added a management column for each taxonomy to the encyclopedia term management page

= 1.3.3 =
* Fixed the delayed appearance of the term filter

= 1.3.2 =
* Changed the usage of DOMDocument::saveHTML in the auto link feature
* Added title attribute to auto linked terms

= 1.3.1 =
* Fixed the auto link terms algorithm. Does not break iframes anymore.
* Added some new options

= 1.3 =
* Added Search Widget

= 1.2.8 =
* Improved filter navigation (faster!!!)
* Improved multibyte support

= 1.2.7 =
* Cleaned up the code to avoid PHP notices.

= 1.2.6 =
* Fixed the letter filter bug in genesis

= 1.2.5 =
* Rewriting permalinks when activating the plugin
* Improved term linking in contents

= 1.2.4 =
* Made the auto linked content terms working with WPML
* Made the term filter work with WPML
* Allow URL Slug translation via WPML now

= 1.2.3 =
* Fixed the "order by" list in the cloud widget
* Fixed some german translations

= 1.2.2 =
* Added the "encyclopedia" class to all auto generated links

= 1.2.1 =
* Fixed translation bug for the widgets
* Added Support for Windows machines

= 1.2 =
* Fixed an incompatibility with Jetpack
* Added the automatic term links in post contents
* Changed the labels of the taxonomies in the menu editor
* removed post meta information from search results page in Twenty Twelve

= 1.1.1 =
* Added French translation.

= 1.1 =
* Added Indonesian translation.
* Small bug fixes

= 1.0 =
* Everything works fine.
