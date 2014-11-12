Inline asset usage
==================

For rendering automatically all scripts and styles of all block, you must
used the twig functions:

- `inlineStylesPosition()` in global html style template
- `inlineScriptsPosition()` in global html script template
- `renderAssetTags()` in the end of tempalte

The `renderAssetTags()` can replace the tag position of assets
(`inlineStylesPosition()` and `inlineScriptsPosition()`) by the contents
of each asset. This is for this reason that it must be executed last.

### Twig tags

#### Twig tag "inline_style"

**Attributes available:**

| Name            | Type   | Default value | Description                                                                                        |
|-----------------|--------|---------------|----------------------------------------------------------------------------------------------------|
| `position`      | string | `null`        | The position of content in the global template (required the `inlineStylesPosition()` in template) |
| `keep_html_tag` | bool   | `false`       | Check if the `<style>` HTML tag must be removing or not                                            |

The twig tag `inline_style` must contain a body, and end with the tag end `endinline_style`.

#### Twig tag "inline_script"

**Attributes available:**

| Name            | Type   | Default value | Description                                                                                         |
|-----------------|--------|---------------|-----------------------------------------------------------------------------------------------------|
| `position`      | string | `null`        | The position of content in the global template (required the `inlineScriptsPosition()` in template) |
| `keep_html_tag` | bool   | `false`       | Check if the `<script>` HTML tag must be removing or not                                            |

The twig tag `inline_script` must contain a body, and end with the tag end `endinline_script`.

### Twig functions

#### Twig function "inlineStylesPosition"

**Arguments available:**

| Name       | Type   | Default value | Description                                           |
|------------|--------|---------------|-------------------------------------------------------|
| `position` | string | `null`        | The position of style contents in the global template |

#### Twig function "inlineScriptsPosition"

**Arguments available:**

| Name       | Type   | Default value | Description                                            |
|------------|--------|---------------|--------------------------------------------------------|
| `position` | string | `null`        | The position of script contents in the global template |

### Full example

```html+jinja
<html>
    <head>
        <style type="text/css">
        {{ inlineStylesPosition() }}
        </style>
        {{ inlineScriptsPosition('head') }}
    </head>
    <body>
        {% set foo: 'bar' %}

        {% inline_script position='head' keep_html_tag=true %}
            <script>
            alert("Head foo: {{ foo }}");
            </script>
        {% endinline_script %}

        {% inline_script %}
            <script>
            alert("Foo: {{ foo }}");
            </script>
        {% endinline_script %}

        <script type="text/javascript">
            $( document ).ready( function() {
                {{ inlineScriptsPosition() }}
            });
        </script>
    </body>
<html>
{{ renderAssetTags() }}
```
