=== Access Category Password ===
Contributors: jojaba
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5PXUPNR78J2YW&lc=FR&item_name=Jojaba&currency_code=EUR&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted
Tags: protect, password, category
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Protects posts in categories by setting a unique Password for all restricted categories.

== Description ==

This plugin makes it possible to restrict the access of posts contained in categries by setting a password and giving the impacted categories. The content and the excerpt of these posts are replaced by a password form that the user can fill out to get access. The WordPress generated feeds are removed and a new custom feed is created to avoid displaying informations about the protected posts (the description is replaced by a sentence that you can define).

Here's the list of the settings (see screenshots for further infos):

* Set the password.
* Check the categories that has to be protected
* Set the info message that display before the password form
* Set the error message when typing the wrong password
* Set the new custom feed name (slug)
* Set the text replacing the feed item description of protected posts

Availabe languages : english and french.

This plugin uses php Sessions (more secure than cookies) to keep in mind he authenticated users. The password is crypted before it is stored. On activation of the plugin you will get an error message about the sent headers, this won't affect his functionnalities.

== Installation ==

1. Upload `access-category-password` directory to the `/wp-content/plugins/` directory of your Wordpress installation
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings » Access Category Password to set up the plugin


== Frequently Asked Questions ==

= Could I protect more than one category =

Yes. You just have to check the right checkboxes in the plugin options screen.

= Could I set more than one Password? =

No, sorry, I wanted to keep the plugin simple. But this would be a functionnality that could be added later,…

= Are the attachments in the posts protected ? =

No, sorry, I didn't find yet a solution to solve this. So if someone gets the link to the attachment of a protected post, he will be able to download it. I'm searching for a fix, maybe you can help me ;)

== Screenshots ==

1. Access Category Password options page (French)
2. Protected content in Twenty Four Theme (French)

== Changelog ==

= 1.0 =
* First release. Thanks for your feedback!
