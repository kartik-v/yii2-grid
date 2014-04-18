version 1.0.0
=============
Initial release

version 1.1.0
=============

1. Export features added through a brand new custom JQuery plugin:
  - Save displayed grid as HTML
  - Save displayed grid as CSV
2. Templates to modify positioning of the export menu and the panel before and after contents
3. Ability to display toolbar in the header.

version 1.2.0
=============
1. Converted the extension into a module.
2. Export features enhanced for use across all browsers:
  - Save displayed grid as HTML
  - Save displayed grid as CSV
  - Save displayed grid as TEXT
  - Save displayed grid as XLS

version 1.3.0
=============
(enh #19): Gridview enhancements (export, toolbar, iframe)

- Enable rendering of export without panel by passing `{export}` variable to grid `layout` property.
- Enable rendering of toolbar without panel by passing `{toolbar}` variable to grid `layout` property.
- Revamp export form to be submitted inside a iframe (in a non-intrusive manner)