Version 1.8.0
=============

**Date:** 01-Jul-2014

1. (enh #58, #59): Russian language translation included
2. (enh #60): Added a new `EditableColumn` column to the grid that uses the enhanced `kartik\editable\Editable` widget to make the grid content editable.

Version 1.7.0
=============

**Date:** 14-Jul-2014

1. (enh #57): Added `containerOptions` to grid layout for allowing configuration of the grid table container. This can be set to
`false` to not display the container.

Version 1.6.0
=============

**Date:** 10-Jul-2014

1. (enh #54): Grid Export Enhancements
- Ability to preprocess and convert column data to your desired value before exporting. For example convert the HTML formatted icons for BooleanColumn to user friendly text like `Active` or `Inactive` after export.
- Hide any row or column in the grid by adding one or more of the following CSS classes:
    - `skip-export`: Will skip this element during export for all formats (`html`, `csv`, `txt`, `xls`).
    - `skip-export-html`: Will skip this element during export only for `html` export format.
    - `skip-export-csv`: Will skip this element during export only for `csv` export format.
    - `skip-export-txt`: Will skip this element during export only for `txt` export format.
    - `skip-export-xls`: Will skip this element during export only for `xls` (excel) export format.
    These CSS can be set virtually anywhere. For example `headerOptions`, `contentOptions`, `beforeHeader` etc.

2. (enh #52): Upgraded float header plugin

3. Enhanced panel footer to have a consistent height whether pagination is displayed or not.

4. `BooleanColumn` icons have been setup as `ICON_ACTIVE` and `ICON_INACTIVE` constants in GridView.

5. `ActionColumn` content by default has been disabled to appear in export output. The `skip-export` CSS class has been set as default in `headerOptions` and `contentOptions`.

Version 1.5.0
=============

**Date:** 04-Jul-2014

1. (enh #51): Enhanced GridView header and footer, to include additional headers/footers before or after default header/footer. 
   The properties below can be set as an array or string:
    - Added `beforeHeader` property to configure additional header rows before the default grid header. 
    - Added `afterHeader` property to configure additional header rows after the default grid header. 
    - Added `beforeFooter` property to configure additional footer rows before the default grid footer. 
    - Added `afterFooter` property to configure additional footer rows after the default grid footer. 
2. Fixes #26 to #50.

Version 1.4.0
=============

**Date:** 29-Apr-2014

1. (enh #25): Allow highlighting of selected row for a CheckboxColumn
    - Added `rowHighlight` property to set if a row needs to be highlighted
    - Added `rowSelectedClass` property to configure the CSS class for the highlighted row.
2. Fixes #20 to #24.

Version 1.3.0
=============

**Date:** 18-Apr-2014

1. (enh #19): Gridview enhancements (export, toolbar, iframe)
    - Enable rendering of export without panel by passing `{export}` variable to grid `layout` property.
    - Enable rendering of toolbar without panel by passing `{toolbar}` variable to grid `layout` property.
    - Revamp export form to be submitted in a new window (in a non-intrusive manner)
2. Fixes #1 to #19.

Version 1.2.0
=============

**Date:** 22-Mar-2014

1. Converted the extension into a module.
2. Export features enhanced for use across all browsers:
   - Save displayed grid as HTML
   - Save displayed grid as CSV
   - Save displayed grid as TEXT
   - Save displayed grid as XLS

Version 1.1.0
=============

**Date:** 15-Mar-2014

1. Export features added through a brand new custom JQuery plugin:
   - Save displayed grid as HTML
   - Save displayed grid as CSV
2. Templates to modify positioning of the export menu and the panel before and after contents
3. Ability to display toolbar in the header.

Version 1.0.0
=============

**Date:** 10-Mar-2014

Initial release
