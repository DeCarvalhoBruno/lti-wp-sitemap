# Wordpress Sitemap Plugin [![Build Status](https://travis-ci.org/DeCarvalhoBruno/lti-wp-sitemap.svg)](https://travis-ci.org/DeCarvalhoBruno/lti-wp-sitemap) [![Code Climate](https://codeclimate.com/github/DeCarvalhoBruno/lti-wp-sitemap/badges/gpa.svg)](https://codeclimate.com/github/DeCarvalhoBruno/lti-wp-sitemap)

**Note:** this is the development repository for the plugin. If you're installing it on your Wordpress site please use the [Wordpress plugin repository](https://wordpress.org/plugins/lti-sitemap/) instead.

## Installation ##

### In Wordpress: ###
The easiest way to install the plugin is to use the plugins management page in your administration panel.

Also, the package can be downloaded manually and unzipped in the /wp-content/plugins/ directory.

When resources have been copied, the plugin can be activated by looking for a "LTI Sitemap" entry in the plugins page and clicking on **"Activate"**.

Configure the options through LTI->LTI Sitemap.

Clicking on the **"Deactivate"** button will disable the post editing box information associated with the plugin. The **"Delete"** button will remove any LTI Sitemap related field in the database.

###In a dev environment: ###

- Unzip the archive downloaded from github or git checkout the code in your wordpress plugin directory.
- Install composer dependencies (only one tiny package at time of this writing)
```
    $ composer install
```
- Optionally, if you want to tinker with CSS and JS:
```
    $ npm install
```

## Contribute ##

You can help us by:
- Translating the plugin in your own language (get in touch with me for details),
- Submitting bugs and feature requests in this project's [issue tracker](https://github.com/DeCarvalhoBruno/lti-wp-sitemap/issues),
- Submitting code via [pull requests](https://github.com/DeCarvalhoBruno/lti-wp-sitemap/pulls),
- [Visiting our blog](http://dev.linguisticteam.org) to interact with us and have awesome discussions around dev issues.

## Thank You ##

- To [The WordPress Plugin Boilerplate](http://wppb.io/) which was used to kickstart this project.