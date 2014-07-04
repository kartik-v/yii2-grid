Version 1.5.0
=============

Date: 04-Jul-2014

1. (enh #51): Enhanced GridView header and footer, to include additional headers/footers before or after default header/footer. 
   The properties below can be set as an array or string:
    - Added `beforeHeader` property to configure additional header rows before the default grid header. 
    - Added `afterHeader` property to configure additional header rows after the default grid header. 
    - Added `beforeFooter` property to configure additional footer rows before the default grid footer. 
    - Added `afterFooter` property to configure additional footer rows after the default grid footer. 
2. Fixes #26 to #50.

Version 1.4.0
=============

Date: 29-Apr-2014

1. (enh #25): Allow highlighting of selected row for a CheckboxColumn
    - Added `rowHighlight` property to set if a row needs to be highlighted
    - Added `rowSelectedClass` property to configure the CSS class for the highlighted row.
2. Fixes #20 to #24.

Version 1.3.0
=============

Date: 18-Apr-2014

1. (enh #19): Gridview enhancements (export, toolbar, iframe)
    - Enable rendering of export without panel by passing `{export}` variable to grid `layout` property.
    - Enable rendering of toolbar without panel by passing `{toolbar}` variable to grid `layout` property.
    - Revamp export form to be submitted in a new window (in a non-intrusive manner)
2. Fixes #1 to #19.

Version 1.2.0
=============

Date: 22-Mar-2014

1. Converted the extension into a module.
2. Export features enhanced for use across all browsers:
   - Save displayed grid as HTML
   - Save displayed grid as CSV
   - Save displayed grid as TEXT
   - Save displayed grid as XLS

Version 1.1.0
=============

Date: 15-Mar-2014

1. Export features added through a brand new custom JQuery plugin:
   - Save displayed grid as HTML
   - Save displayed grid as CSV
2. Templates to modify positioning of the export menu and the panel before and after contents
3. Ability to display toolbar in the header.

Version 1.0.0
=============

Date: 10-Mar-2014

Initial release
