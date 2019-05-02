[![Build Status](https://travis-ci.org/sabbelasichon/typo3_encore.png)](https://travis-ci.org/sabbelasichon/typo3_encore)

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
Due to a hook for the ResourceFactory we make a lookup of file paths in the manifest.json and if it matches return the versioned path instead.

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
{namespace e = Ssch\Typo3Encore\ViewHelpers}

<e:renderWebpackLinkTags entryName="app"/>
<e:renderWebpackScriptTags entryName="app"/>
```

If you have defined multiple entries you can define the desired entryName in the ViewHelpers
```html
{namespace e = Ssch\Typo3Encore\ViewHelpers}

<e:renderWebpackLinkTags entryName="secondEntryName"/>
<e:renderWebpackScriptTags entryName="secondEntryName"/>
```


Alternatively you can also include the files via TypoScript

```php
page.includeCSS {
    # Pattern typo3_encore:entryName
    app = typo3_encore:app 
}

page.includeJS {
    # Pattern typo3_encore:entryName
    app = typo3_encore:app
}

page.includeJSFooter {
    # Pattern typo3_encore:entryName
    app = typo3_encore:app
}
```

Note the prefix typo3_encore: This is important in order to render the files correctly.
You can then use all other known settings to include your files.

You don´t have to care about including it only once. This will not happen during one request cycle unless you want to. 

## Additional 

1. If you are in production mode and set enableVersioning(true) then you should set the option 

```php
$GLOBALS['TYPO3_CONF_VARS']['FE']['versionNumberInFilename'] = ''
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
    test: /\.svg$/,
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
{namespace e = Ssch\Typo3Encore\ViewHelpers}

<e:svg title="Title" description="Description" src="EXT:typo3_encore/Resources/Public/sprite.svg" name="icon-fax-contact"/>
```
