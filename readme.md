md-to-wp
===

this imports *most* markdown files in static site generators into wordpress. 

because not all static site generators are created equally, you will probably need to tweak the source of `md-to-wp.php` to fit with your generator's meta data. 

this is only tested on markdown files used in an site originally generated with [nanoc](http://nanoc.ws/).

### how to use

i'd clone this baby right into your wordpress installation's root directory. make sure the `require` calls all match to your respective directories and files (*see "hot to config" below*). if you're not already running `wp-markdown` you'll need to keep that `markdown-extra.php` file in the directory so the right markdown-to-html conversion can happen.

you can find an example markdown post that this script was originally written for in the `posts-to-import` directory. if your posts look a lot different, you'll *definitely* need to tweak the source. then move all your posts to import into that directory *or* change the directory (*see "how to config" below*).

then just direct your browser to `[your directory]/md-to-wp/md-to-wp.php` and watch the entire internet break hehehe

### how to config

* line 13: path to wp-blog-header.php
* line 14: path to  wp-includes/registration.php
* line 17: path to where the markdown files to import are
* line 20: email domain for new users to get (example "bocoup.com")
* line 21: default password for new users to get (example "password")

If you need to change/add/remove meta labels, check out line 168 for the switch statement that goes through all of that junk.

### what is going on when you go to md-to-wp.php

1. each `.md` or `.markdown` file in the given `posts-to-import` directory is imported
2. if the author given in the markdown doesn't exist yet, it's created with the name as username, first token of username @ `$email` as email, and `$password` as the password.  new users are given the role subscriber by default.
3. categories are actually added as wordpress tags. that's what i needed to do, but you can change line 208 to `post_category` if you want them imported as categories. i just think it's a good idea to convert tags to categories in the admin because sometimes in old posts we all get hella intense and cluttered with categories.

### why use this

i don't know, i made it for work lol. maybe you too want to convert your static site into wordpress.

#### note: please do not make a PR unless it specifically doesn't work for nanoc, issues are welcome though!
 
xoxo [j$](http://jennmoney.biz)