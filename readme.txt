=== XS2Content ===
Contributors: CodeKitchen
Tags: edit, admin, post, page, posts
Requires at least: 4.6
Tested up to: 6.2
Stable tag: 1.5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Text-to-speech platform for publishers and company newsrooms.

== Description ==

XS2Content wants to give publishers, governmental organizations and companies with a newsroom the tools and the possibility to transform their own existing textual content into audio with soundscaping. The TTS-platform XS2Content provides new revenue models and the possibility to reach the target groups at other times of use.

With XS2Content, publishers and content producing organizations can create a new owned media channel easily and with very low costs. XS2Content offers an end to end-platform for publishers with the possibility to start broadcasting within a week. This plugin directly integrated with XS2Content where you can submit every post or which you want.

You can add a player on the post itself through settings and you can use gutenberg block, shortcodes and widgets to add a player or playlist.

== Installation ==

1. Upload the folder `xs2radio` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to the Settings -> XS2Content to start connecting it to XS2Content
 
== Frequently Asked Questions ==


== Screenshots ==

1. How it would look like after enabling this plugin


== Changelog ==

= 1.5.4 ( 2023-04-28 ) =
* Prevent fatal error on older WordPress installations

= 1.5.3 ( 2023-04-20 ) =
* Improve displaying player inside content

= 1.5.2 ( 2023-04-13 ) =
* Added escaping of user provided data
* Small Javascript improvement

= 1.5.1 ( 2023-03-31 ) =
* Fix loading Gutenberg block

= 1.5.0 ( 2023-03-28 ) =
* Added Gutenberg block (Beta)
* Added shortcode xs2content-player and xs2content-playlist
* Be able to specify what info is displayed in the player on the post itself 
* Added a new player template: Simple
* Some styling fixes

= 1.4.1 ( 2023-03-15 ) =
* Fix downloading statistics
* Fix setting the color of title in certain themes

= 1.4.0 ( 2023-03-15 ) =
* Added new player template: Block
* Refactored player code
* Fixed bug where forward button might not work correctly
* Updated Wavesurfer library to 6.6.0

= 1.3.1 ( 2022-08-04 ) =
* Use dataLayer.push() instead of gtag() to allow wider support

= 1.3.0 ( 2022-07-20 ) =
* Rebranded from XS2Radio to XS2Content
* Send Analytics events to Google Analytics when it's enabled
* Fix changing playlist itemwhen no image is displayeed
* Fix showing player in widget admin area when current page is selected
* Updated Wavesurfer library to 6.2.0

= 1.2.4 ( 2021-3-17 ) =
* Added post creation date in export of analytics

= 1.2.3 ( 2021-12-23 ) =
* Improved loading audio file
* Add ability to show/hide image in widgets
* Show correct image in playlist after changing item
* Load styling and filter settings in playlist
* Fix showing player widget when showing current post in widget admin area

= 1.2.2 ( 2021-12-20 ) =
* Fixed typos
* Updated "requires at least" to 4.6

= 1.2.1 ( 2021-12-15 ) =
* New version after direct fix after releasing 1.2.0 with missing file

= 1.2.0 ( 2021-12-11 ) =
* Added analytics when audio is started, hallway and finished
* Fix minor styling issue with play button icon

= 1.1.0 ( 2021-11-01 ) =
* Add ability to specify player title when added to a post
* Hide player when there is no content to play
* Hide player when audio file isn't playable
* Increased timeout for API calls
* Check directly if API connection still works when visiting the settings page
* Updated Wavesurfer library to 5.2.0

= 1.0.2 ( 2021-03-13 ) =
* Fix showing correct default value of transferring post
* Fix not transferring the post when not required

= 1.0.1 ( 2021-03-13 ) =
* Be able to remove the API token
* Add additional error handling for adding API token
* Fix getting options when nothing is set

= 1.0.0 ( 2021-03-12 ) =
* First version of the plugin
* Integrates with XS2Radio
* Two widgets: Player for single post (current/latest) and playlist
