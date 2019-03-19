[titleEn]: <>(Depending on other plugins)
[wikiUrl]: <>(../how-to/depending-on-other-plugins?category=platform-en/how-to)

## Overview

New in the Shopware platform is the possibility to properly require on other plugins to be in the system.
This is done using the `require` feature from composer.
Further information about this can be found in the [official composer documentation](https://getcomposer.org/doc/04-schema.md#package-links).

## Setup

Each plugin for the Shopware platform has to own a `composer.json` file for it to be a valid plugin.
Creating a plugin is not explained here, make sure to read our [plugin quick start guide](../2-platform/4-extending-the-platform/1-plugins/010-plugin-quick-start.md) first.

Since every plugin has to own a `composer.json` file, you can simply refer to this plugin by its technical name and its version
mentioned in the respective plugin's `composer.json`.

So, those are example lines of the `Plugin quick start` plugin's `composer.json`:
```
{
    "name": "swag/plugin-quick-start",
    "description": "Plugin quick start plugin",
    "version": "v1.0.0",
    ...
}
```

Important to note is the `name` as well as the `version` mentioned here, the rest of the file is not important for this
case here. You're going to need those two information to require them in your own plugin.

In order to require the `Plugin quick start` plugin now, you simply have to add these two information to your
own `composer.json` as a key value pair:

```xml
{
    "name": "swag/plugin-dependency",
    "description": "Plugin requiring other plugins",
    "version": "v1.0.0",
    "type": "shopware-plugin",
    "license": "MIT",
    "authors": [
        {
            "name": "shopware AG"
        }
    ],
    "require": {
        "shopware/platform": "dev-master",
        "swag/plugin-quick-start": "v1.0.0"
    },
    "extra": {
        "installer-name": "PluginDependency",
        "label": {
            "de_DE": "Plugin mit Plugin-Abhängigkeiten",
            "en_GB": "Plugin with plugin dependencies"
        },
        "description": {
            "de_DE": "Plugin mit Plugin-Abhängigkeiten",
            "en_GB": "Plugin with plugin dependencies"
        }
    }
}
```

Have a detailed look at the `require` keyword, which now requires both the Shopware platform version, which **always**
has to be mentioned in your `composer.json`, as well as the previously mentioned plugin and it's version.
Just as in composer itself, you can also use version wildcards, such as `v1.0.*` to only require the other plugin's
minor version to be 1.1, not considering the patch version.

Now your plugin isn't installable anymore, until that requirement is fulfilled.