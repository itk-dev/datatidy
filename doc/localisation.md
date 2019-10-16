# Localisation

We write our translations in the XLIFF format. This format is widely used for writing translations, and there are
many tools that supports this format, so managing translations becomes a breeze. You could even set non-technicals
to do the translations.

There are two rules we adheres to when creating and writing translations:

1. A translation file should only contain translations for a specific domain.
   - This break downs the translation files in more manageable files instead of one large file.
2. Use the word or sentence as key in the translation file.
   - So we describe what we are translating instead of where we are translating.

## Workflow

1. Use the translations in your Twig template before creating or defining translation files:

```twig
{# Set the default translation domain for the Twig template #}
{% trans_default_domain 'users' %}

{{ 'Change password'|trans }}

{# Or with a specific domain: #}
{{ 'Change password'|trans({}, 'users') }}
```

2. Generate/update the translation files:

```bash
# Remember to set the DEFAULT_LOCALE environment variable so the XLF-files will have the correct source-language
docker-compose exec phpfpm DEFAULT_LOCALE=en bin/console translation:update --force da
```

3. Open and edit the translations/users.da.xlf file in your favorite editor for this kind of files.

## Editors

- [POEdit](https://poedit.net/)
