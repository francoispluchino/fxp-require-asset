Embed asset usage
=================

For rendering automatically all javascripts and stylesheets of all block, you must
used the twig functions:

- `embedStylesheetsPosition()` in global html stylesheet template
- `embedJavascriptsPosition()` in global html javascript template
- `renderEmbedAssets()` in the end of tempalte

The `renderEmbedAssets()` can replace the tag position of assets
(`embedStylesheetsPosition()` and `embedJavascriptsPosition()`) by the contents
of each asset. This is for this reason that it must be executed last.

**Twig example:**

```html+jinja
<html>
    <head>
        <style type="text/css">
        {{ embedStylesheetsPosition() }}
        </style>
    </head>
    <body>
        {% set foo: 'bar' %}

        {% embed_javascript %}
            <script>
            alert("Foo: {{ foo }}");
            </script>
        {% endembed_javascript %}

        <script type="text/javascript">
            $( document ).ready( function() {
                {{ embedJavascriptsPosition() }}
            });
        </script>
    </body>
<html>
{{ renderEmbedAssets() }}
```
