# Theme Check

The theme check plugin is an easy way to test your theme and make sure it's up to spec with the latest [theme review](http://codex.wordpress.org/Theme_Review) standards. With it, you can run all the same automated testing tools on your theme that WordPress.org uses for theme submissions.

[See the readme.txt for the full plugin description.](https://github.com/ryelle/theme-check/blob/cli/readme.txt)

# Theme Check with WP-CLI

	wp theme review check <theme-name>

This version of Theme Check adds a [WP-CLI](http://wp-cli.org) command to `wp theme`, to perform the theme check at the command line.

![Theme Check run on a failing theme](http://redradar.net/wp-content/uploads/2014/07/theme-check.png)

	wp theme review list

You can also list the themes, which shows the display name and folder name for each theme in the site. The check command expects the folder name, so this is helpful to grab the correct name.

![Theme list](http://redradar.net/wp-content/uploads/2014/07/theme-list.png)

# Grunt Task

With this version of Theme Check, you can also use my grunt task, [wp_theme_check](https://github.com/ryelle/grunt-wp-theme-check) to automate testing your theme as you build.