<?php

// ----------------------------------------------------
// Register the autoloader from Composer.
// ----------------------------------------------------

if (file_exists($autoloader = __DIR__.'/vendor/autoload.php')) {
    require $autoloader;
}

require __DIR__.'/Gust/functions.php';

// ----------------------------------------------------
// Load config values.
// ----------------------------------------------------
\Gust\Config::init();

// ----------------------------------------------------
// Load core framework functionality.
// ----------------------------------------------------
\Gust\Component::init();

\Gust\WordPress\Admin::init();
\Gust\WordPress\Cleanup::init();
\Gust\WordPress\Comments::init();
\Gust\WordPress\EditHomepage::init();
\Gust\WordPress\Emails::init();
\Gust\WordPress\Enqueue::init();
\Gust\WordPress\Escaping::init();
\Gust\WordPress\Editor::init();
\Gust\WordPress\Colors::init();
\Gust\WordPress\Head::init();
\Gust\WordPress\Images::init();
\Gust\WordPress\PostsPT::init();
\Gust\WordPress\Roles::init();
\Gust\WordPress\Security::init();
\Gust\WordPress\ThemeSetup::init();
\Gust\Router::init();
\Gust\WordPress\Updates::init();
\Gust\WordPress\UploadMimes::init();
\Gust\Dev\DevRoutes::init();

// ----------------------------------------------------
// Load Theme Modules.
// ----------------------------------------------------
\Gust\Module::init();

// ----------------------------------------------------
// Load Theme Utilities.
// ----------------------------------------------------
\Theme\Utils\YearShortcode::init();
