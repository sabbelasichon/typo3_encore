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

1. First of all install Webpack encore as stated in the [documentation](https://symfony.com/doc/current/frontend.html)
You should really be able to use all of the things described in the documentation.
Like Sass-Loader, Vue-Loader etc. These things are completely independent from this little extension. 

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

