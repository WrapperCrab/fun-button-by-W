# Fun Button By W
## A Communal Click Counter for WordPress
See this plugin in action on my website at https://www.mowinpeople.com/the-button/

This is a WordPress Plugin that creates a button on your site which keeps track of all the times it has been clicked and displays that value. 
It's a fun attraction for a website I think.

### How to Use
This plugin is activated with shortcodes:\

You create the button on your site by using the shortcode `[fun-button]`

To display the number of clicks a logged in user has performed, use the shortcode `[fun-button-user-clicks]`

When the plugin is activated, it creates a new table in the WordPress database system which stores the total number of clicks.\
When a logged in user enters a page with the fun button present, it will check if a `numClicks` entry has been made for that user. 
If not it creates it right then and there.

Every 1 second, the fun button updates the total number of clicks as well as the user's personal clicks value with any clicks that the user has made in the previous second. 

This plugin only creates one fun button for the site. Major changes would need to be made to allow the creation of more distinct buttons.
