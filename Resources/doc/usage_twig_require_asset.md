Require asset usage
===================

For rendering automatically all links of scripts and styles, you must
used the twig functions:

- `requireStylesPosition()` in global html style template
- `requireScriptsPosition()` in global html script template
- `renderAssetTags()` in the end of tempalte

The `renderAssetTags()` can replace the tag position of assets
(`requireStylesPosition()` and `requireScriptsPosition()`) by the links
of each asset. This is for this reason that it must be executed last.

### Twig tags

#### Twig tag "require_style"

**Attributes available:**

| Name       | Type   | Default value | Description                                                                                         |
|------------|--------|---------------|-----------------------------------------------------------------------------------------------------|
| `position` | string | `null`        | The position of content in the global template (required the `requireStylesPosition()` in template) |
| `href`     | string | `null`        | Specifies the location of the linked document (automatically added)                                 |
| `rel`      | string | `stylesheet`  | Specifies the relationship between the current document and the linked document                     |
| `media`    | string | `null`        | Specifies on what device the linked document will be displayed                                      |
| `type`     | string | `null`        | Specifies the media type of the linked document                                                     |
| `hreflang` | string | `null`        | Specifies the language of the text in the linked document                                           |
| `sizes`    | int    | `null`        | Specifies the size of the linked resource. Only for rel="icon"                                      |

The twig tag `require_style` required a `string` or a list of `string` containing the link of the source path (or name of common asset)
before the attributes.

#### Twig tag "require_script"

**Attributes available:**

| Name       | Type   | Default value | Description                                                                                          |
|------------|--------|---------------|------------------------------------------------------------------------------------------------------|
| `position` | string | `null`        | The position of content in the global template (required the `requireScriptsPosition()` in template) |
| `src`      | string | `null`        | Specifies the URL of an external script file (automatically added)                                   |
| `async`    | bool   | `null`        | Specifies that the script is executed asynchronously                                                 |
| `defer`    | bool   | `null`        | Specifies that the script is executed when the page has finished parsing                             |
| `charset`  | string | `null`        | Specifies the character encoding used in an external script file                                     |
| `type`     | string | `null`        | Specifies the media type of the script                                                               |

The twig tag `require_script` required a `string` or a list of `string` containing the link of the source path (or name of common asset)
before the attributes.

### Twig functions

#### Twig function "requireStylesPosition"

**Arguments available:**

| Name       | Type   | Default value | Description                                        |
|------------|--------|---------------|----------------------------------------------------|
| `position` | string | `null`        | The position of style links in the global template |

#### Twig function "requireScriptsPosition"

**Arguments available:**

| Name       | Type   | Default value | Description                                         |
|------------|--------|---------------|-----------------------------------------------------|
| `position` | string | `null`        | The position of script links in the global template |

### Usage

- [Usage with Webpack Manifest Plugin](usage_twig_require_asset_webpack_manifest.md)
- [Usage with Webpack Assets Plugin](usage_twig_require_asset_webpack_assets.md)
