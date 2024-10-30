=== BookCover ===
Contributors: iwongu
Tags: post, formatting, book cover 
Requires at least: 2.0
Tested up to: 2.2
Stable tag: 1.2

This plugin display the book cover image from ISBN.

== Description ==

This plugin display the book cover image from ISBN. It has 3 pre-configured book store whose images are used. The first is for Korean book (89), the second
for Japan (4), and the others. You can change or add new book store URI in the plugin configuration screen. And you can reset to default setting at anytime.

This plugin includes sidebar widget for book cover display, also. 

= Usage =

*  First of all, you should know the book's ISBN. Usually, it is on the back of the book near by bar-code.
* Then add the bookcover markup to your post like the following.
* - [bookcover:1932394613]
* - [bookcover:1932394613(Ajax In Action)] 
* It is converted to <img class="bookcover" ... /> markup. So you can control the style in your css file. 

= Preview =

* [BookCover plugin test page](http://ideathinking.com/blog-v2/?p=72)

== Installation ==

1. Unzip the plugin archive.
1. Upload the directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Known issues ==

1.  There are ISBNs that ended with 'x' not a number. In that cases, you should take care of the case sensitivity of the 'x', because some book store use
the lower case 'x' for their image file name, and some the upper case. So, try one, and if it fail try another.
1. There is no validation check on plugin configuration page. So you can submit with empty country code and empty URI. It makes default URI for other
country code null. 

