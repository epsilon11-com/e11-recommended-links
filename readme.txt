=== e11 Recommended Links ===
Contributors: er11
Tags: recommended, recommend, recommendation, links, urls, share
Requires at least: 4.7
Tested up to: 4.7.3
Stable tag: 1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Show admin-recommended links and descriptions within a page of post summaries
and/or a widget.

== Description ==

e11 Recommended Links seeks to provide an easy way to share links to things
you feel might be of interest to your users.

This plugin divides links into two categories: those that are displayed in
a widget, and those that are displayed within webpages with lists of posts
(generally the main page of the website.)

The widget will display a list of links at random, and, like the blogroll,
is meant to feature sites with lasting relevance.

The function for webpages with lists of posts will create a div displaying a
list of links that were added to the site around the same date range as
the posts on the webpage.  This is meant for links like news articles where
their relevance is tied in part to the date they're posted.

It's also possible to put a link in both categories so it gets highlighted
in the main area of the site when it's posted but will also occasionally
appear in the widget from that point forward.


== Installation ==

1. Upload the plugin files to the '/wp-content/plugins/e11-recommended-links'
   directory, or install the plugin through the WordPress plugins
   screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Add the "e11 Recommended Links" widget to your template using
   "Appearance / Customize / Widgets" from the admin sidebar.  The widget will
   display a list of randomly chosen links on each page load.
4. Add a call to "e11RecommendedLinks::display_links()" within the template
   code that outputs a list of posts.  This will create a div displaying a
   list of links that were added to the site around the same date range as
   the posts on the page.
5. Use "e11 Recommended Links / Add New" from the admin sidebar to begin adding
   links.  "Display mode" controls whether a link is displayed in the
   widget, the list of posts, or both.


== Screenshots ==

1. Widget "Recommended links" display a random set of links
2. Posts area "Recommended links" display links by date


== Changelog ==

= 1.0 =
* Initial release.


== Other notes ==

Comments and feature requests are welcomed here or on
[the project page on my site.](https://epsilon11.com/e11-recommended-links)
