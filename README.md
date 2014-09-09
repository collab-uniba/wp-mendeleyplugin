wp-mendeleyplugin
=================
A WordPress plugin to list an always up-to-date list of scientific publications from your Mendeley account.
You can create a Mendeley account and download their client apps here: http://www.mendeley.com

Installation & usage notes
==========================
To install, simply clone this repository into the plugin directory of your WordPress installation.

~~~
git clone https://github.com/davideparisi/wp-mendeleyplugin
~~~

After completing installation, go to `Settings>Mendeley Settings` to configure it.
Configuration allows to set you Mendeley id and secret, request an OAUTH2 Access Token and import your authored publications.
A Mendeley button will appear in WordPress editor to enter the shortcode in the form `[mendeley titletag=h2 sectiontag=h3]My Publications[/mendeley]` wherever you wish the list to appear.
