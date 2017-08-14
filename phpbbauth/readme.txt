=== phpbbauth ===
Contributors: ocroquette
Tags: phpbb, authentication, password, login
Requires at least: 2.7.0
Tested up to: 2.9.2
Stable tag: 1.0

Authenticate in Wordpress using passwords from a phpBB installation

== Description ==

This plugin allows to use the password from a working phpBB installation
to log into a Wordpress blog.

You still need to create users in the Wordpress administration panel, but
the users may use their phpBB password to log in.

Wordpress passwords are still valid to log in.

It's important that the Wordpress logins match the <code>username_clean</code>
of the Phpbb users. username_clean doesn't contain any uppercase
characters, nor special ones. Check directly in the DB if in doubt.


== Installation ==

1. Extract the archive
1. upload the phpbbauth directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

