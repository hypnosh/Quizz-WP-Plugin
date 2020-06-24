=== Quizz ===
A simple quiz plugin
Contributors: 13llama
Tags: quiz, question
Author: Amit Sharma
Author URI: https://www.recaptured.in/
Version: 1.02
Stable tag: 1.02
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Date last updated: 31 March 2014

Create a sequential quiz on WordPress with the Quizz plugin.

== Description ==

Create a sequential quiz on WordPress with the Quizz plugin.

You can create rich questions, with rich text, images, videos, audio, as you would in any other WordPress post, and let the user answer in plain text, and move on to the next question if they've answered correctly.

The answer conditions can be either 'exact match & case-sensitive', or can be phrase-matched (eg. the list of correct answers can be "xyz, abc, def", and if the user enters "abc", it's counted as the right answer.

The plugin also raises the following hooks:
quizz_level_updated: raised when the user's answer is considered correct and they're pushed to the next question
quizz_ended: raised when the list of questions comes to an end, and the user is sent to a designated page (eg. a congratulations page)


== Installation ==

For an automatic installation through WordPress:
1. Go to the 'Add New' plugins screen in your WordPress admin area
1. Search for 'Quizz'
1. Click 'Install Now' and activate the plugin

For a manual installation via FTP:
1. Upload the addthis folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' screen in your WordPress admin area

To upload the plugin through WordPress, instead of FTP:
1. Upload the downloaded zip file on the 'Add New' plugins screen (see the 'Upload' tab) in your WordPress admin area and activate.


== Usage ==

1. Under Questions in the WordPress admin menu, click on Add New Question. 
2. Enter the question in the big post area. This can be plain text, images, or embedded multimedia. 
3. Enter the correct answer in the Answer field below the question field.
4. Choose whether you will accept only exact matches, or a part answer (eg. you enter a series of answers delimited by commas) is valid.
5. Select which question leads to the current question.
6. Select whether this is the final question of the series, and if it is, choose the Page which will be displayed when the player is done with the quiz. Eg. a thank you page, or a success page.


== Uninstall ==

In the Plugins screen of your WordPress admin area, navigate to Quizz, click on Deactivate. On the refreshed screen, click on Delete.
Note: You will lose all your questions if you uninstall the plugin. Take a backup of your data before you uninstall.


== Changelog ==

31 March 2014:	Using the WordPress permalink function instead of creating the URL structure for redirects. 