[![Downloads](https://img.shields.io/packagist/dt/ssch/typo3-encore.svg?style=flat-square)](https://packagist.org/packages/ssch/typo3-encore)

TYPO3 integration with Webpack Encore!
======================================

This extension allows you to use the `splitEntryChunks()` feature
from [Webpack Encore](https://symfony.com/doc/current/frontend.html)
by reading an `entrypoints.json` file and helping you render all of
the dynamic `script` and `link` tags needed.

```
composer require ssch/typo3-encore
```

## How to use

1. First of all install Webpack Encore as stated in the [documentation](https://symfony.com/doc/current/frontend.html).
You should really be able to use all of the things described in the documentation.
Like Sass-Loader, Vue-Loader etc. These things are completely independent from this little extension.

You can also use the enableVersioning() of files (mostly used only in production context).
You can also use the enableIntegrityHashes(). This is taking into account if the files are included.

2. Define your entry path(s) and the output path (usually your Resource/Public/ folder in your Package extension) in the webpack.config.js

3. Afterwards set the two TypoScript constants to point to the manifest.json and the entrypoints.json located in the configured output folder
```php
plugin.tx_typo3encore {
    settings {
        entrypointJsonPath = EXT:typo3_encore/Resources/Public/entrypoints.json
        manifestJsonPath = EXT:typo3_encore/Resources/Public/manifest.json
    }
}
```

4. In your Page templates/layout you can then use the ViewHelpers to integrate the CSS- and JS-Files in your website
```html
{namespace encore = Ssch\Typo3Encore\ViewHelpers}

<encore:renderWebpackLinkTags entryName="app"/>
<encore:renderWebpackScriptTags entryName="app"/>
```

If you have defined multiple entries you can define the desired entryName in the ViewHelpers
```html
{namespace encore = Ssch\Typo3Encore\ViewHelpers}

<encore:renderWebpackLinkTags entryName="secondEntryName"/>
<encore:renderWebpackScriptTags entryName="secondEntryName"/>
```


Alternatively you can also include the files via TypoScript

```php
page.includeCSS {
    # Pattern typo3_encore:entryName
    app = typo3_encore:app
    # If you want to ensure that this file is loaded first uncomment the next line
    # app.forceOnTop = 1
}

page.includeJS {
    # Pattern typo3_encore:entryName
    app = typo3_encore:app
    # If you want to ensure that this file is loaded first uncomment the next line
    # app.forceOnTop = 1
}

page.includeJSFooter {
    # Pattern typo3_encore:entryName
    app = typo3_encore:app
}
```

Note the prefix typo3_encore: This is important in order to render the files correctly.
You can then use all other known settings to include your files.

You don´t have to care about including it only once. This will not happen during one request cycle unless you want to.

It is also possible to use the inclusion via the prefix typo3_encore in backend specific contexts. For example like so:

```html
<f:be.container includeCssFiles="{0: 'typo3_encore:backend'}" includeJsFiles="{0: 'typo3_encore:backend'}">

</f:be.container>
```

### HTTP/2 Preloading

All css and javascript files managed by the extension will be added to the AssetRegistry class during rendering.
For these assets a Link HTTP header is created, which are the key to optimize the application performance when using HTTP/2 and preloading capabilities of modern web browsers.

Technically this is done by a PSR-15 Middleware.

If you want to add additional files to the AssetRegistry you can use the PreloadViewHelper:

```html
{namespace encore = Ssch\Typo3Encore\ViewHelpers}

<encore:preload attributes="{type: 'font/woff2', crossOrigin: 'anonymous'}" as="font" uri="{encore:asset(pathToFile: 'EXT:typo3_encore/Resources/fonts/webfont.woff2')}" />
```

Watch out, the example also uses the AssetViewHelper. The AssetViewHelper behind the scenes makes a look up to the manifest.json file.
So you can also leverage the versioning feature provided by Webpack.

### Static assets

Sometimes you might need to reference static assets (like image files) directly in your fluid templates. You can use the Encore `copyFiles` function to instruct Webpack to copy static assets to your output folder (see https://symfony.com/doc/current/frontend/encore/copy-files.html#referencing-image-files-from-a-template).

To reference a static asset file from a fluid template, you can then use the AssetViewHelper to get the file path (including the hash if versioning is enabled).

Note that the AssetViewHelper does not render anything but just returns the path to the file, so you will probably use inline notation to, e.g., display an image:

```
{namespace encore = Ssch\Typo3Encore\ViewHelpers}


<img class="my-image" src="{encore:asset(pathToFile: 'EXT:my_extension/Resources/Public/Build/images/my_image.jpg')}" alt="My image" />
```

This way of using the AssetViewHelper is similar to the `asset` function used in Twig templates with Symfony.

## Additional

1. If you are in production mode and set enableVersioning(true) then you should set the option

```php
$GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'] = ''
```

2. Defining Multiple Webpack Configurations ([see](https://symfony.com/doc/current/frontend/encore/advanced-config.html#defining-multiple-webpack-configurations))

Then you have to define your builds in your TypoScript-Setup:

```php
plugin.tx_typo3encore {
    settings {
        builds {
            firstBuild = EXT:typo3_encore/Resources/Public/FirstBuild
            secondBuild = EXT:typo3_encore/Resources/Public/SecondBuild
        }
    }
}
```

Finally, you can specify which build to use:

```php
page.includeCSS {
    # Pattern typo3_encore:buildName:entryName
    app = typo3_encore:firstBuild:app
}
```

```html
{namespace encore = Ssch\Typo3Encore\ViewHelpers}

<encore:renderWebpackLinkTags entryName="app" buildName="firstBuild"/>
```

### CSS for CKEditor

It is possible to configure encore so that you can use a CSS file for the CKEditor which is generated by webpack and even works with enabled versioning.
Two steps are required to do so:

1. Define an entrypoint for the RTE in your `webpack.config.js`, e.g.
```js
Encore
    .addStyleEntry('rte', './assets/rte.scss')
```

2. Add in the CKEditor yaml configuration

```yaml
editor:
  config:
    contentsCss: "typo3_encore:rte"
```

## Getting Started with Webpack Encore

Although the documentation of Webpack Encore is awesome, i am going to provide a minimalistic how to install the frontend related things.
I assume some basic knowledge of modern frontend development.

### Install Encore into your project via Yarn or Npm:
First, make sure you install [Node.js](https://nodejs.org/en/download/) and also the [Yarn](https://yarnpkg.com/lang/en/docs/install/) or [npm](https://www.npmjs.com/get-npm) package manager.


```cli
yarn add @symfony/webpack-encore --dev
```

This command creates or modifies a package.json file and downloads dependencies into a node_modules/ directory.
Yarn also creates/updates a yarn.lock (called package-lock.json if you use npm).

You should commit package.json and yarn.lock (or package-lock.json if using npm) to version control, but ignore the node_modules/ folder.

### Creating the webpack.config.js File
Next, we are going to create a webpack.config.js file at the root of our project.
This is the main config file for both Webpack and Webpack Encore:

```javascript
var Encore = require('@symfony/webpack-encore');

Encore
    // the directory where compiled assets will be stored
    .setOutputPath('public/typo3conf/ext/my_sitepackage/Resources/Public/')

    // public path used by the web server to access the output path
    .setPublicPath('/typo3conf/ext/my_sitepackage/Resources/Public/')

    // only needed for CDN's or sub-directory deploy
    // .setManifestKeyPrefix('build/')

    // Copy some static images to your -> https://symfony.com/doc/current/frontend/encore/copy-files.html
    .copyFiles({
        from: './src/images',
        // Optional target path, relative to the output dir
        to: 'images/[path][name].[ext]',
        includeSubdirectories: false,
        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]',
        // only copy files matching this pattern
        pattern: /\.(png|jpg|jpeg)$/
    })

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .addEntry('app', './src/js/app.js')
    .addEntry('homepage', './src/js/homepage.js')

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // uncomment if you use TypeScript -> https://symfony.com/doc/current/frontend/encore/typescript.html
    // .enableTypeScriptLoader()

    // uncomment if you are using Sass/SCSS files -> https://symfony.com/doc/current/frontend/encore/css-preprocessors.html
    // .enableSassLoader()

    // uncomment if you're having problems with a jQuery plugin -> https://symfony.com/doc/current/frontend/encore/legacy-applications.html
    // .autoProvidejQuery()

    // uncomment if you use the postcss -> https://symfony.com/doc/current/frontend/encore/postcss.html
    // .enablePostCssLoader()


    // uncomment if you want to use vue -> https://symfony.com/doc/current/frontend/encore/vuejs.html
    // .enableVueLoader()

    // uncomment if you´re want to lint your sources
    // .enableEslintLoader()

    // uncomment if you´re want to have integrity hashes for your script tags, the extension takes care of it
    // .enableIntegrityHashes()

    // uncomment if you´re want to share general code for the different entries -> https://symfony.com/doc/current/frontend/encore/split-chunks.html
    // .splitEntryChunks()
    ;

// Uncomment if you are going to use a CDN -> https://symfony.com/doc/current/frontend/encore/cdn.html
// if (Encore.isProduction()) {
    //Encore.setPublicPath('https://my-cool-app.com.global.prod.fastly.net');

    // guarantee that the keys in manifest.json are *still*
    // prefixed with build/
    // (e.g. "build/dashboard.js": "https://my-cool-app.com.global.prod.fastly.net/dashboard.js")
    // Encore.setManifestKeyPrefix('build/');
// }

module.exports = Encore.getWebpackConfig();
```

### Working with typo3/cms-composer-installers 4+ or TYPO3 12

The `typo3/cms-composer-installers` library takes care of moving TYPO3-specific assets in the right place
in Composer-based installations, like copying extensions to `typo3conf/ext`. Starting with version 4.0
(currently at RC1 stage), the extensions will remain in `vendor/vendor_name`. The `Resources/Public`
directory of each extension is symlinked from the `public/assets` directory using hashes. While it is
possible to target the symlink, the fact that it is a hash makes it a bit flaky.

The recommendation is to use another build directory, not located inside an extension.
As an example, asuming that you use `public/build`, the configuration in `webpack.config.js`
would be modified as follows:

```javascript
var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('../../public/build')
    .setPublicPath('/build')
    ...
```

The TypoScript constants have to be modified accordingly:

```typo3_typoscript
plugin.tx_typo3encore {
    settings {
        # These paths are relative to the web root (public) directory
        entrypointJsonPath = build/entrypoints.json
        manifestJsonPath = build/manifest.json
    }
}
```

### The realm of Webpack plugins
Encore already ships with a lot of useful plugins for the daily work.
But someday you are gonna get to the point where you need more.

#### Generating icons and inject them automatically

Install [webapp-webpack-plugin](https://github.com/brunocodutra/webapp-webpack-plugin) and [html-webpack-plugin](https://github.com/jantimon/html-webpack-plugin).

```javascript
const WebappWebpackPlugin = require('webapp-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');

Encore.addPlugin(new HtmlWebpackPlugin(
            {
                inject: false,
                minify: false,
                template: 'public/typo3conf/ext/typo3_encore/Resources/Private/Templates/Favicons.html',
                filename: 'favicons.html',
            }
        ))
        .addPlugin(new WebappWebpackPlugin({
            inject: htmlPlugin => htmlPlugin.options.filename === 'favicons.html',
            logo: './src/images/logo.png',
            force: true,
            favicons: {
                start_url: null,
                lang: null,
                icons: {
                    android: true,
                    appleIcon: true,
                    appleStartup: true,
                    windows: true,
                    yandex: true,
                    favicons: true,
                    coast: true,
                    firefox: true,
                    opengraph: false,
                    twitter: false
                }
            }
        }))
```

In order to inject the html file in the header of your TYPO3 just include the template file:

```php
page.headerData.2039 = FLUIDTEMPLATE
page.headerData.2039 {
    file = EXT:typo3_encore/Resources/Public/favicons.html
}
```

#### Generating a svg sprite

Install [svg-sprite-loader](https://github.com/kisenka/svg-sprite-loader#installation)
```javascript
const SpritePlugin = require('svg-sprite-loader/plugin');

Encore.addLoader({
    test: /\src\/icons\/.svg$/,
    loader: 'svg-sprite-loader',
    options: {
        extract: true,
    }
}).addPlugin(new SpritePlugin())

```

Now you have to import all your svg files in your javascript

```
function requireAll(r) {
    r.keys().forEach(r);
}
requireAll(require.context('./relative-path-to-svg-folder/svg-sprite/', true, /\.svg$/));
```

The extension ships with a SvgViewHelper in order to simplify the usage of svg in fluid.

```html
{namespace encore = Ssch\Typo3Encore\ViewHelpers}

<encore:svg title="Title" description="Description" src="EXT:typo3_encore/Resources/Public/sprite.svg" name="icon-fax-contact"/>
```
