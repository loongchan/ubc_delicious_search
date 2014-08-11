=== UBC Delicious Search ===
Contributors: loongchan
Tags: Delicious, Delicious shortcode, del.icio.us, del.icio.us shortcode, searching, filtering
Requires at least: 3.9
Tested up to: 3.9.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Allows you to create searching and filtering from delicious to your WordPress site.

== Description ==
This plugin uses a series or shortcodes to allow one to create searching and filtering of links from delicious.

To use, there are a number of shortcodes working together to make it all work.
[ubc_delicious_results_once] - allows user to create any number of results area that is not searchable/filterable.
[ubc_delicious_results] - creates the area which will contain the search results
[ubc_delicious_search] - creates a search input box as well as search button
[ubc_delicious_dropdown] - meant to be used to create dropdown of tags
[ubc_delicious_checkbox] - creates a checkbox list of tags

[ubc_delicious_results_once limit=20 defaulttag=\"defaul_tag\" defaultuser=\"default_user\" useor=\"false\" sort=\"rank\" view=\"list\"]
Attributes:
* limit = maximum number of items to load (max is 200)
* defaulttag = tag to load up when page first loads
* defaultuser = (REQUIRED) field to determine which account to query from
* useor - if true, or all tags, else and all tags
* sort - sort list returned from delicious (valid alpha and rank)
* view - default is list(alias for list_unordered), but other valid values are: links, list_ordered
* showcomments - shows comment of each resource, default is true

[ubc_delicious_results limit=20 defaulttag=\"defaul_tag\" defaultuser=\"default_user\" useor=\"false\" sort=\"rank\" view=\"list\"]
Attributes:
* limit = maximum number of items to load (max is 200)
* defaulttag = tag to load up when page first loads
* defaultuser = (REQUIRED) field to determine which account to query from
* useor - if true, or all tags, else and all tags
* sort - sort list returned from delicious (valid alpha and rank)
* view - default is list(alias for list_unordered), but other valid values are: links, list_ordered
* showcomments - shows comment of each resource, default is true

[ubc_delicious_search placeholder=\"Search Words\" submittext=\"Submit\" searchtitle=\"Search\" extraclasses=\"classes_for_dropdown\" butonclasses=\"classes_for_button\"]
Attributes:
* placeholder = shows up in the input field to suggest what to type in
* submittext = replaces what text the submit button will show
* searchtitle = title of the search area
* extraclasses = classes that will be added to the search container class
* buttonclasses = classes that will be added to the submit button

[ubc_delicious_dropdown useshowall=\'Show All\' optionslist=\'one, two, three::Three\' defaultoption=\"two\" optiontitle=\"dropdown Label\" extraclasses=\"extra_classes\"]
Attributes:
* useshowall = if false or empty, then don\'t have a show all option, else show the text passed in
* optionslist = comma separated list of options in the dropdown in format: \"value, value\" or \"value::label, value2::label2\" or a mix of the two formats
* defaultoption = preselects the value if it matches a value in the optionslist
* optiontitle = title of the dropdown
* extraclasses = classes to be add to the dropdown

[ubc_delicious_checkbox optionslist=\'one, two, three::Three\' defaultoption=\"two,three\" optiontitle=\"checkbox main Label\" extraclasses=\"extra_classes\"]
* optionslist = comma separated list of options in the dropdown in format: \"value, value\" or \"value::label, value2::label2\" or a mix of the two formats
* defaultoption = comma separated list of values, if it matches a value in the optionslist, it will be checked by default
* optiontitle = title of the checkbox section
* extraclasses = classes to be add to the checkbox

== Installation ==

1. download plugin (and if compressed, uncompress)
2. move contents of "ubc-delicious-search" the `/wp-content/plugins/` directory
3. Start using the shortcodes above!

== Frequently Asked Questions ==

= I see the dropdown and search shortcode, but can't see the results =

You also have to make sure that somewhere on the page, that you also use the [ubc_delicious_results] shortcode (with the required defaultuser attribute)

= Why do I have to use a defaultuser attribute for results? =
 
It's cause I still haven't figured out how to do a search with delicious without using default user.

= How come it stopped working? =

I actually use the same call as what delicious does when you search in the serach field when viewing within a delicious account.  Since it's not that well documented (or officially documented for that mater as far as I know), it can stop working at anytime!

== Changelog ==

= 0.1 =

* Initial public release
