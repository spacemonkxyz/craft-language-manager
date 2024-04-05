# Language Manager for Craft CMS

The Language Manager plugin adds easy integration for a language switcher and for the correct hreflang attributes in the header.

- The [language switcher](#language-switcher) is a simple menu that links to all parallel language versions of the same page
- The [hreflang tags](#hreflang-tags) add the correct hreflang attributes in the header of the page (including setting the `x-default` tag for the primary version)

## Requirements

This plugin requires Craft CMS 4.0 or later.

## Installation

You can install this plugin from the plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Language Manager”. Then press Install in its modal window.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project

# tell Composer to load the plugin
composer require spacemonk/language-manager

# tell Craft to install the plugin
php craft plugin/install language-manager
```

## Language Switcher

The sites have to be added in the same site group in Craft CMS.

The language switcher will switch to the parallel site in a different language, if there is no parallel site, it will fall back to the main page. The labels can be set to [different styles or you can provide your own](#labels). The plugin supports keeping query parameters for the url (default `off`)

#### Easy &rarr; via hook

In your twig template at the position where you want to have the language switcher menu, add the following hook:

```twig
{% hook 'languageManagerNavigation' %}
```

You can also use caching (be careful when using cache together with the activated setting to keep query parameters).

```twig
{% cache %}
    {% hook 'languageManagerNavigation' %}
{% endcache %}
```

The main nav has the class `language-manager` for easy CSS targeting.

The output will look like this:

```html
<nav aria-label="Change language" class="language-manager">
  <ul>
    <li class="active">
      <a href="https://yourwebsite.domain/" hreflang="en" lang="en">
        English
      </a>
    </li>
    <li>
      <a href="https://yourwebsite.domain/de/" hreflang="de" lang="de">
        German
      </a>
    </li>
  </ul>
</nav>
```

#### Advanced &rarr; custom integration

The plugin exposes a twig function `getLanguageManagerPages()`, which returns an array of page objects. [For details see below](#twig-function).

As a starting point you can copy the code from [the hook template here](https://github.com/spacemonkxyz/craft-language-manager/tree/main/src/templates/_frontend/language-navigation.twig).

### Labels

You can choose from several options to display the language switcher labels.

1. Language Code (e.g. 'EN')
2. Long Language Code (e.g. 'EN-US')
3. Language Name (e.g. 'English')
4. Long Language Name (e.g. 'English (United States)')
5. Your own custom label translations, [see here](#custom-label-translations)

## Hreflang Tags

#### Easy &rarr; via hook

In your template (in the html head), add the following hook:

```twig
{% hook 'languageManagerHreflang' %}
```

You can also use caching.

```twig
{% cache %}
    {% hook 'languageManagerHreflang' %}
{% endcache %}
```

The output will look like this:

```html
<link rel="alternate" hreflang="en" href="https://yourwebsite.domain/" />
<link rel="alternate" hreflang="x-default" href="https://yourwebsite.domain/" />
<link rel="alternate" hreflang="de" href="https://yourwebsite.domain/de/" />
```

The `x-default` attribute is set to the primary site.

#### Advanced &rarr; custom integration

The plugin exposes a twig function `getLanguageManagerPages()`, which returns an array of page objects. [For details see below](#twig-function).

As a starting point you can copy the code from [the hook template here](https://github.com/spacemonkxyz/craft-language-manager/tree/main/src/templates/_frontend/hreflang.twig).

## Custom Label Translations

If you choose to provide your own label names, you can do so via the translations.
The plugin uses the language codes (e.g. `en` or `en-US`) as translation key for the labels.
To add your labels create a translation file with the name `language-manager.php` and place it in the folder with the specified language (e.g. `yourprojectroot/translations/en/language-manager.php`.
Then add your translations:

```php
<?php

return [
    'en' => 'English',
    'en-US' => 'American',
    'de' => 'German',
    'de-CH' => 'Swiss German',
];
```

## Twig function

The plugin exposes a twig function `getLanguageManagerPages()`, which returns an array of page objects. Each page object has the following fields:

```
'url' => The url
'queryParameters' => The query parameters to concatenate with the url (always empty if setting 'keepQueryParameters' == false)
'label' => The label to show in the navigation
'isoCountryCode' => The ISO country code (e.g. 'EN', 'DE' or 'CH' (ISO 3166-1 Alpha-2 code))
'isoLanguageCode' => The ISO language code (e.g. 'en', 'de' or 'de-CH' (ISO 639-1 and two letter country code where relevant))
'isActive' => true if this is the current active language version, else false
'isPrimarySite' => true if this language version is the primary site in the control panel
'isFallback' => true if no language version exists (this means the url points to the base path)
```

## Settings

#### Control Panel

Settings can be easily adjusted in the control panel.

#### Config

Settings can also be set via config and will overwrite and disable the Control Panel settings.

Create a file called `language-manager.php` and place it in the config folder (e.g. `yourprojectroot/config/language-manager.php`).
Then add your config:

```php
<?php

return [
    'keepQueryParameters' => false,
    'showLanguageInThatLanguage' => false,
    'labelType' => 'code'
];
```

Following options are possible:

- `keepQueryParameters` &rarr; `true`, `false`
- `showLanguageInThatLanguage` &rarr; `true`, `false`
- `labelType` &rarr; `code`, `code-long`, `name`, `name-long`, `custom`
  - They represent each of the [options explained here](#labels)
