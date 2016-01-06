#### aklatzke/asset-funnel
----

A tiny and flexible asset minifier and cache for CSS and Javascript.

####Installation

Install [Composer](http://getcomposer.org/ "Composer").

Add the following to the `require` block in your `composer.json`:

`"aklatzke/asset-funnel" : "dev-master"`

Run `composer update` from your shell.

`require_once('/path/to/vendor/autoload.php');` within your application.

--
####Requirements

A way to load environment variables, and PHP >= 5.6.

If using `require-dev`, [josegonzalez/phpdotenv](https://github.com/josegonzalez/php-dotenv "dotenv") will be installed as a dependency.

--
####Setup

First, make certain that you have the following variable defined in your environment:

`FUNNEL_STORAGE_PATH`

You can set this to any temporary or storage folder within your application. This is where the library will store the cached and concatenated files.

Import the namespace: `use Aklatzke\Funnel\Funnel`;

Next, create an instance of the funnel, with the specified output file type:

`$jsFunnel = ( new Funnel('.js') );`
`$cssFunnel = ( new Funnel('.css') );`

You may want to load the classes into a service container so that you have access to them throughout your application in order to add assets from multiple pages (to, for example, defe scripts in individual partials or pages). You should refer to your individual container's documentation, just remember to create it as a singletonn.

-- 
####Adding Assets

The `Funnel` class accepts both local and external resources. When a unique combination of files (both external and local) are combined using the `->bundle()` method, a unique hash is created to reference that combination of files. If this same combination is requested again, the cached and concatenated version will be used instead, resulting in a single file request rather than multiple.

Adding local resources:

`$jsFunnel->add('/absolute/path/to/file.js');`

Adding external resources:

`$jsFunnel->addExternal('http://path-to-remote/file-version-1.0.js');`

External resources are downloaded automatically, meaning that once the cache file is built, you will no longer have to send an HTTP request for that file.

--
####Loading Resources

There are two methods to load your assets, but the first step of either is running the `bundle` method:

`$jsFunnel->bundle('/application/base/path');`

This returns a string of the file contents, so you can echo it directly into your page if you so choose:
```php
  <?php $criticalCSSFunnel->add('/path/to/critical.css'); ?>
  <head>
    .. head content ..
    <style><?php echo $criticalCSSFunnel->bundle(); ?></style>
  </head>
```
If that's not your style, you can also get the file path and drop it into a tag:
```php
<?php 
  $jsFunnel->addExternal('http://some-jquery-cdn/jquery-x.x.js'); 
  $jsFunnel->bundle();
  
  $jsPath = $jsFunnel->getPath();
?>

  <script type="text/javascript" src="<?php echo $jsPath; ?>"></script>
</body>
```










