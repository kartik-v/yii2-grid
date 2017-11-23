Change Log: `yii2-grid`
=======================

## Version 3.1.7

**Date:** 23-Nov-2017

- (bug #726, #727): Fixed translation key for French language.
- (enh #724): Update Farsi translations.
- (enh #723): Update Chinese translations.
- (enh #721): Update Turkish translations.
- (enh #720): Update Czech translations.
- (bug #716): Correct init of `ActionColumn` delete confirmation message. 
    Now `data-confirm` can be passed instead of `message` to set the delete confirmation message 
    within `ActionColumn::deleteOptions`.
- (enh #713): Correct message translations.
- (enh #712): Enhance Gridview loading styling.
- (enh #711): Correct German translations.
- (enh #710): `GridView` translation enhancements.
    - New GridView properties `itemLabelFew` &`itemLabelMany` 
    - Enhance translations for all message files
- Update contribution templates.
- (enh #702, #703): Update German, Italian, and French translations.

## Version 3.1.6

**Date:** 22-Oct-2017

- (enh #701): Enhance and simplify `ActionColumn` delete action.
- (enh #700): Update Russian Translations.
- (enh #699): Enhance ActionColumn to consider new `pjaxDelete` flag.
- (enh #698): Update Spanish Translations.
- Simplify `kv-merged-header` CSS in `kv-grid.css`.
- (enh #696): New properties `itemLabelSingle` and `itemLabelPlural` to be allowed for use in grid summary and action column.
- (bug #695): Correct perfect scrollbar JS script.
- (enh #693): Add Brazilian Portugese Translations.
- (enh #690): Add ability to configure one's own module identifier.
- (enh #689, #688, #609): Correct expand row column behavior when used with grid grouping (_to be tested for all grouping use cases_).
- (enh #687): Update to the latest `floatThead` plugin version.
- (enh #684): Enhancements to `ActionColumn` button rendering.
- (enh #672): Simplify module code.
    - Eliminate dependency on Yii Session to generate `exportEncryptSalt`
    - Create new `Demo` class to manage grid demo message translations
- (bug #671): Initialize checkbox column asset more correctly.
- (enh #666, #658): Enhance export to render header with `perfectScrollbar` and `floatOverflowContainer`.
- (enh #664): Allow to specify Editable class in `editableOptions`.

## Version 3.1.5

**Date:** 09-Jun-2017

- (bug #659): Fix session issue that is not applicable for console apps.
- (enh #654): Update Polish Translations.
- (enh #649): Update Hebrew Translations.
- (enh #644): Do not show the button if there are no actions.
- (enh #635): Update Chinese Traditional Translations.

## Version 3.1.4

**Date:** 08-Jun-2017

- Updates to copyright year.
- (enh #626): Correct expand row jquery events to start with `kvexprow:` instead of `kvexprow.`.
- (bug #624): Call floatThead('reflow') after resizing columns so that the floating head is also resized.
- (enh #619): Correct nested expanded rows using `GridView::ROW_EXPANDED`.
- (enh #604): Fix PDF class name check error.
- (enh #601, #572): Silently disable PDF when dependency is not available.
- (enh #600): Enhance security for ExportController actions using a stateless signature to prevent data tampering:
    - New property `Module::exportEncryptSalt` available to generate a stateless hashed signature.
    - If `exportEncryptSalt` is not set, it will be randomly generated and stored in a session variable.
    - Export settings will be signed and the same data via POST will be cross checked using `yii\base\Security::hashData` and `yii\base\Security::validateData`.  
- Code enhancements for grid group.
- (enh #592): Convert encoding for non UTF-8 content in CSV and Text exports.
- (enh #588): Update Hungarian Translations.
- (enh #587, #586): Fix for expanding row on init.
- (enh #542): More correct group summation.

## Version 3.1.3

**Date:** 22-Oct-2016

- Update message config to include all default standard translation files.
- (enh #584): Update Vietnamese Translations.
- (enh #583): Add Gujarati and Hindi Translations.
- (enh #581): Update Chinese Translations.
- (enh #580): Update Dutch Translations.
- (enh #579, #542): Allow `thousandSep` config for grid group summary.
- (enh #578): Update Italian Translations.
- (enh #565): Better exported content parsing for header, footer, and page summary.
- Enhance PHP Documentation for all classes and methods in the extension.
- (enh #564): Enhance page summary to render within table body and add new property`GridView::pageSummaryContainer`.
- (enh #562): Enhance `EditableColumnAction` to support model scenario as a parameter.
- (enh #561): Enhance `ExpandRowColumn` to better support nested grid views and nested tree expansion.
- (bug #557): Update Ukranian Translations.
- (bug #556): Correct expandRow behavior when using with `detailUrl` pjax form.
- (enh #554): Add ability to configure delete confirmation message within `ActionColumn::deleteOptions`.

## Version 3.1.2

**Date:** 17-Aug-2016

- (enh #541, #543): Update French translations.
- (bug #538): Correct export callback validation
- (bug #537): Correct export arg validation
- Add github contribution and issue/PR logging templates.
- (bug #532): Correct export download when using without confirm alert.
- (kartik-v/yii2-editable#124): Set output value after model save in `EditableColumnAction`.
- (enh #519): Toggle all data correction for non pjax grids.
- (enh #517): Allow nested grids as part of `ExpandRowColumn`.
- (enh #515): Implement ajax delete with pjax refresh for default delete action in `ActionColumn`.
- (enh #514): Implement Krajee Dialog for all alerts and confirmation.
- (enh #513): Fix `renderColumnGroup` for `DataColumn` with options and groupedRow.
- (enh #511): Select all option in grid view.
- (enh #510): Update French translations.
- (enh #507): Purify HTML content for GridView HTML export.
- (enh #506): Correct toggle data confirmation.
- (enh #508, #505): Replaced `ExportController::getPostData` by native `Yii::$app->request->post()`.
- (bug #504): Fix toggle data minCount validation.
- (enh #500): Raw data value attribute for grid columns grouping.
- (enh #499): Option to set the value of the checkbox.
- (enh #498): Option to set the checkbox attribute of checkboxColumn.
- (enh #496): Add BOM to UTF-8 encoded text/CSV exports.
- (enh #494): Add Latvian translations.
- (enh #490): Update Russian translations.
- (enh #485): Add Estonian translations.
- (enh #481): Add Ukranian translations.
- (enh #480): Enhance `EditableColumnAction` to find model better.
- (enh #479): Update default bootstrap css for HTML export of grid.
- (enh #476): Improve responsiveness and control resizable columns for smaller devices.
- (enh #475): Correct grid grouping `formatNumber` JS method.
- (enh #472): Update Spanish translations.
- (enh #470): Having different editable models in one gridview column.

## Version 3.1.1

**Date:** 10-Apr-2016

- (enh #462): Fix responsiveness for smaller devices (resizableColumns overflow).
- (enh #461): Export configuration font awesome enhancements.
- (enh #458): Add Slovak Translations.
- (enh #457): Implement `array_replace_recursive` instead of `ArrayHelper::merge` for overriding defaults.
- (enh #455): Update German Translations.
- (enh #450): Update Hungarian Translations.
- (enh #445): Set default pjaxSettings `enablePushState` to match yii pjax defaults.
- (enh #444): Set default PDF export encoding to utf8.
- (enh #443): Enhance to show header in exported content when setting `floatHeader`.

## Version 3.1.0

**Date:** 13-Jan-2016

- (bug #438): Correct pjax validation for DataColumn.
- (enh #437): Update Brazilian Portuguese Translations
- (enh #436): Add branch alias for dev-master latest release.
- (bug #434): Correct pjax validation for DataColumn.
- (enh #407): Correct casting of primary key in ExpandRowColumn for composite and MongoId scenario.
- (enh #402): Correct casting of primary key in EditableColumn for composite and MongoId scenario.

## Version 3.0.9

**Date:** 10-Jan-2016

- (enh #432): Enhancements for PJAX reinitialization.
- (enh #431): Disable PJAX pushState by default to avoid plugin conflict on browser back forward.
- (enh #420): Enhance EditableColumn to pass current model `attribute` as ajax posted data.
- Sort entries in message files.
- (enh #419): Create new `EditableColumnAction` class.
- (bug #415): Fix double quote replace in csv export.
- (enh #413, #410): Add Thai translations.

## Version 3.0.8

**Date:** 05-Dec-2015

- Update to release v1.3.2 of the `mkoryak/floatThead` plugin.
- (enh #410): Updated Czech Translations.
- (enh #399): Correct resizableColumns initialization for PJAX.
- (enh #397): Updated Dutch Translations.
- Regenerate and update message translations.
- (enh #391): Toggle data enhancement with confirmation dialog for show all and hide maxCount.
- (enh #390): Perfect scroll bars plugin enhancement.
- Various coding style enhancements, optimizations, and fixes.
- (enh #389): Various enhancements to table float header.
- (bug #387): Rename Czech translation message folder from `cz` to `cs`.
- (enh #380): Allow toolbar and panel classes/layouts to be overridden.
- (bug #371): Allow `tag` to be set for rendering `beforeHeader`.
- (bug #370): Enhance `EditableColumn::refreshGrid` validation.
- (enh #347): New `defaultPagination` property to allow setting default to 'page' or 'all'.
- (enh #303): Correct Safari specific limitation for jQuery slideDown animation in ExpandRowColumn.

## Version 3.0.7

**Date:** 13-Sep-2015

- (enh #365): Validate disabled checkbox rows for highlight in CheckboxColumn.
- Better styling for revamped Select2 widget.
- (enh #354): Add options to set resizableColumns plugin options.
- (enh #352): Display expanded ajax content in ExpandRowColumn when initial value = ROW_EXPANDED.
- (enh #336): Add `enableCache` property in `ExpandRowColumn`.

## Version 3.0.6

**Date:** 15-Jul-2015

- (enh #338): Various enhancements for grid excel export formatting.

## Version 3.0.5

**Date:** 07-Jul-2015

- (enh #334): Add grid grouping functionalities.
- (enh #328): Add Turkish translations.
- (enh #326): Zero width joiner for excel exports.
- (enh #325): Various enhancements to client script registrations.
- (enh #323): Prevent `pjax:complete` init script being called multiple times.
- (enh #322): Send serialized `data-key` when ExpandRowColumn has a composite key.

## Version 3.0.4

**Date:** 24-Jun-2015

- (enh #321): Add Indonesian translations.
- (enh #320): Trim trailing whitespaces from text/csv exports.

## Version 3.0.3

**Date:** 15-Jun-2015

- (enh #318): Fix post pjax `setTimeout` JS function.
- (enh #317): Add missing spanish translations.
- (enh #313): Add Czech translations.
- (enh #311): Better defaulting of Select2 `pluginOptions['width']`.
- (enh #310): Updated German Translations.
- (enh #301): Add Greek Translations.
- (enh #300): Add Lithuanian Translations.

## Version 3.0.2

**Date:** 11-May-2015

- (enh #296): Responsively wrap table columns for smaller screen devices.
- (enh #291): ExpandRowColumn styling enhancements.
- (enh #290): Allow `expandOneOnly` property behavior even if `allowBatchToggle` is set to `false`.
- (enh #288): Enhance grid export plugin to clean up hyperlink tags within table header.
- (enh #287): Allow columns to be highlighted on initialization of `CheckboxColumn`.
- (enh #284, #282): Allow disabling click behavior for specific elements when ExpandRowColumn::enableRowClick is `true`.
- (enh #272): New property `ExpandRowColumn::expandOneOnly` to allow only one row to expand at a time.
- (enh #268): Fix `BooleanColumn::falseIcon` default.
- (enh #263): Added fa-IR (Farsi) translations.
- (enh #261): Allow initialization of ExpandRowColumn cells even if they are hidden.

## Version 3.0.1

**Date:** 14-Mar-2015

- (enh #257): Fix for `detailOptions` to be set as Closure in ExpandRowColumn.
- (enh #256): New property `extraData` for sending extra data to ExpandRowColumn via ajax load call.
- (enh #255): Enhance ExpandRowColumn to allow expand/collapse on row click.
- (enh #253): Enhance EditableColumn `refreshGrid` behavior for multiple editable columns on the grid.
- (bug #252): Fix undefined `$filterInputOptions` in DataColumn.
- (bug #251): Fix ExpandRowColumn bug with disabled closure and unnecessary check for title.
- (enh #250): Parse pjax setting in `toggleData` button to enable toggling pagination via pjax.
- (enh #249): Add new properties `toggleDataContainer` and `exportDataContainer` for controlling button group options.
- (enh #247): Add ability to set `dropdownOptions` for `ActionColumn` dropdown.
- (enh #245): Various enhancements to grid pagination toggle.
- (enh #239): Updated Russian translations.
- (enh #237): Parse valueIfNull correctly within EditableColumn editableOptions.
- (enh #229): Ability to set readonly rows in EditableColumn.
- (enh kartik-v/yii2-dynagrid#47): Set a timeout for plugin reinitialization on pjax complete.
- (enh #176): Allow displayValue to be overridden for editable column.

## Version 3.0.0

**Date:** 13-Feb-2015

- (enh #227): New grid column extension RadioColumn.
- (enh #226): Updated Russian Translations.
- (enh #221): Trim json exported fields by default.
- (enh #218): Allow gridview to be used as a sub-module.
- (bug #216): Fix resizable columns container identifier.
- (bug #215): Add Simplified Chinese message translations.
- Set copyright year to current.
- (bug #214): Fix EditableColumn Closure use bug.
- (enh #213): Default `persistResize` to false to prevent client caching of column widths.
- (enh #209): Code cleanup and restructure for various JS lint changes (using JSHint Code cleanup library).
- (enh #207): Fix EditableColumn to have absolute reference to Closure.
- (enh #202, #203): Fix grid container overflow and responsive property.
- (enh #200): Expand row loading indicator reset for ajax load.
- (enh #198): Better container for initializing `resizableColumns`.
- (bug #192): Correct value callback in `FormulaColumn`.
- (bug #191): Correct Closure namespace for `value` validation in GridView.
- (bug #190): Allow editable beforeInput in EditableColumn to be passed as Closure.
- (enh #189): Various performance enhancements to client script and plugin registrations.
- (enh #188): Eliminate filter row and all form inputs from export.
- (enh #186): New feature - Allow resizing of columns like a spreadsheet.
- (bug #185): Set right jQuery selector for grid table export.
- (bug #184): Correct the dependency on kartik\mpdf\Pdf for export.
- (bug #183): Implement alignment validation for content within ActionColumn.
- Revamp to use new Krajee base Module and TranslationTrait.
- (bug #180): Fix namespaces of classes.
- Code formatting updates as per Yii2 coding style.
- (bug #178): Enhance CheckboxColumn to allow contentOptions to be set as Closure.
- (enh #178): New validation of contentOptions for all the extended grid Columns.

## Version 2.9.0

**Date:** 26-Dec-2014

- (bug #173): Ability to configure display of confirmation alert dialog before export.
- (bug #172): Ability to configure export form submission target.

## Version 2.8.0

**Date:** 16-Dec-2014

- (enh #169): Enable itemsBefore and itemsAfter to be added for export dropdown menu.
- (enh #168): Prevent user to rapidly toggle and break the expand row before expansion or collapse.
- (bug #167): Fix Yii message parsing for Html5Input '$this->noSupport'.

## Version 2.7.0

**Date:** 02-Dec-2014

- (enh #158): Include demo messages for auto generating via config.
- (enh #157): Recursively replace/merge PDF export configuration correctly.
- (enh #156): Separate all JS / CSS assets and load only if the relevant functionality is needed.
- (enh #154): Trap alert confirm dialog to allow export even after being hidden by browser **do not show** option.
- (enh #152): Included `prepend` and `append` settings within `pageSummaryOptions` to prepend/append content to page summary.
- (enh #150): New `ExpandRowColumn` added - allows to expand grid rows, show details, and load content via ajax.

## Version 2.6.0

**Date:** 19-Nov-2014

- (enh #145): Enhance style to enable floated header wrapper tables to autofit/expand inside panel.
- (enh #144): Revamp templates to easily configure different parts of the grid panel layout. (_BC breaking change_).
- (bug #143): Ability to disable / hide GridView panel footer.
- (bug #142): Fix missing headers in export, due to improper floatThead settings.
- (bug #141): Enhance EditableColumn to have unique attribute input ids yet the same name.
- (enh #140): French translations added.
- (enh #139): German translations updated.

#### BC Breaking Changes

- Removed `showFooter` from `panel` array configuration. This can be now configured with `footer` option within the `panel`.
- Removed `layout` from `panel` array configuration. This can be now configured with `panelTemplate` at the GridView level.
- Renamed `beforeTemplate` property to `panelBeforeTemplate`.
- Renamed `afterTemplate` property to `panelAfterTemplate`.
- Renamed `beforeContent` tag used in `panelBeforeTemplate` to `before`.
- Renamed `afterContent` tag to `panelAfterTemplate` to `after`.
- EditableColumn attribute naming convention has changed. Developers do not need to use `Model::loadMultiple` method anymore and have the ability to directly use the `$model->load` method.

#### Additions
- Templates have been simplified and consolidated to the following configurable properties:
    - `panelTemplate`: Template to render the complete grid panel.
    - `panelHeadingTemplate`: Template to render the heading block part of the panel.
    - `panelBeforeTemplate`: Template to render the before block part of the panel.
    - `panelAfterTemplate`: Template to render the after block part of the panel.
    - `panelFooterTemplate`: Template to render the footer block part of the panel.
- The `heading`, `footer`, `before`, and `after` properties in the `panel` typically accepts a string to render in that particular block. All of these can be set to boolean `false` to hide them.
- HTML attributes for each of the above containers are now configurable i.e via `headingOptions`, `footerOptions`, `beforeOptions`, and `afterOptions` properties in the `panel` array configuration.
- Vast enhancements to CSS styling when using Float Table Header wrapper. This now ensures tables auto fits and expand rightly to fit inside the panel.

## Version 2.5.0

**Date:** 17-Nov-2014

- (bug #136): Fix IE specific errors in floatHeader when columns are hidden.
- (bug #135): Upgrade to latest release of floatTHeader plugin.

## Version 2.4.0

**Date:** 14-Nov-2014

- (bug #133): Skip mPDF dependency when export is set to false.
- (bug #132): Correct page summary calculation.

## Version 2.3.0

**Date:** 07-Nov-2014

- (bug #131): Fix missing `options` in `toggleDataOptions` initialization.
- (enh #127): Enhance dependency validation and ability to install optional packages.
- (enh #122): Hungarian translations added.
- (enh #121): Portugese translations added.
- (enh #116): Vietnamese translations added.
- (bug #87): Fix key as object in mongodb.

## Version 2.2.0

**Date:** 04-Nov-2014

- (enh #122): Add Hungarian translations.
- (enh #121): Add Portugese translations.
- (bug #118): Validate if `toggleGridData` is used in the extension.
- (enh #115): Add ability to plugin `yii2-export` extension for full grid data export.
- (enh #114): Add `hiddenFromExport` property for all grid columns.
- (enh #113): Enhance the PDF export generation method to allow generation of formatted reports.
- (enh #112): Toggle data button to allow toggling between **all data** and **paginated data**.
- (enh #110): Various export functionality enhancements):
    - Add a separate export popup progress window.
    - Setup a confirmation prompt to allow user to confirm if file is to be downloaded.
    - Separate `messages` configuration for all export related notifications.
    - Asynchronous export process on the separate window - and avoid any grid refresh
    - Set export mime types to be configurable
    - Add support for exporting new file types):
        - JSON export
        - PDF export (using `yii2-mpdf` extension)
    - Add functionality for full data export
    - Enhance icons formatting for export file types (and beautify optionally using font awesome)

## Version 2.1.0

**Date:** 25-Oct-2014

- (enh #111): Fix export button dropdown menu display for IE.
- (enh #107): Cleanup and refactor GridView class code for better extensibility.
- (enh #106): Set right class for GridView):):FILTER_DATE_RANGE.
- (enh #99): Grid Export Plugins): Add ability to extend export dropdown.
- (enh #96): Grid Plugins): Add ability to replace tags in gridview rendered layout.
- (enh #95): Enhance export button dropdown feature.
- (enh #94): Enhance and revamp toolbar.

## Version 2.0.0

**Date:** 14-Sep-2014

- (bug #92): Bug fix for generating multiple rows in header/footer.
- PSR 4 alias change
- (bug #88, #87, #85): Enhance EditableColumn to capture keys of various data types
- (enh #83): Upgraded jQuery floatTheader plugin to latest version.
- (enh #82): Created a reusable `ColumnTrait` for all custom yii2-grid columns.
- (bug #81): CSS class `kv-grid-hide` configured for hidden columns.
- (enh #80): Add hidden property for columns to be hidden from display but available on export.

## Version 1.9.0

**Date:** 21-Aug-2014

- (enh #74,76): Enhance EditableColumn to allow grid refresh on successful update.
- (enh #73): Enhancement for EditableColumn options to be configured as callback.
- (enh #72): Enhancement for EditableColumn `beforeInput` and `afterInput`.
- (enh #67): Fix Chrome bug for displaying loading indicator on tbody.
- (enh #65): Various enhancements to the widget to work with Pjax

## Version 1.8.0

**Date:** 01-Aug-2014

- (enh #60): Added a new `EditableColumn` column to the grid that uses the enhanced `kartik\editable\Editable` widget to make the grid content editable.
- (enh #59, #58): Russian language translation included

## Version 1.7.0

**Date:** 14-Jul-2014

- (enh #57): Added `containerOptions` to grid layout for allowing configuration of the grid table container. This can be set to
`false` to not display the container.

## Version 1.6.0

**Date:** 10-Jul-2014

- (enh #54): Grid Export Enhancements
- Ability to preprocess and convert column data to your desired value before exporting. For example convert the HTML formatted icons for BooleanColumn to user friendly text like `Active` or `Inactive` after export.
- Hide any row or column in the grid by adding one or more of the following CSS classes):
    - `skip-export`): Will skip this element during export for all formats (`html`, `csv`, `txt`, `xls`).
    - `skip-export-html`): Will skip this element during export only for `html` export format.
    - `skip-export-csv`): Will skip this element during export only for `csv` export format.
    - `skip-export-txt`): Will skip this element during export only for `txt` export format.
    - `skip-export-xls`): Will skip this element during export only for `xls` (excel) export format.
    These CSS can be set virtually anywhere. For example `headerOptions`, `contentOptions`, `beforeHeader` etc.
- (enh #52): Upgraded float header plugin

- Enhanced panel footer to have a consistent height whether pagination is displayed or not.

- `BooleanColumn` icons have been setup as `ICON_ACTIVE` and `ICON_INACTIVE` constants in GridView.

- `ActionColumn` content by default has been disabled to appear in export output. The `skip-export` CSS class has been set as default in `headerOptions` and `contentOptions`.

## Version 1.5.0

**Date:** 04-Jul-2014

- (enh #51): Enhanced GridView header and footer, to include additional headers/footers before or after default header/footer.
   The properties below can be set as an array or string):
    - Added `beforeHeader` property to configure additional header rows before the default grid header.
    - Added `afterHeader` property to configure additional header rows after the default grid header.
    - Added `beforeFooter` property to configure additional footer rows before the default grid footer.
    - Added `afterFooter` property to configure additional footer rows after the default grid footer.
- Fixes #26 to #50.

## Version 1.4.0


**Date:** 29-Apr-2014

- (enh #25): Allow highlighting of selected row for a CheckboxColumn
    - Added `rowHighlight` property to set if a row needs to be highlighted
    - Added `rowSelectedClass` property to configure the CSS class for the highlighted row.
- Fixes #20 to #24.

## Version 1.3.0

**Date:** 18-Apr-2014

- (enh #19): Gridview enhancements (export, toolbar, iframe)
    - Enable rendering of export without panel by passing `{export}` variable to grid `layout` property.
    - Enable rendering of toolbar without panel by passing `{toolbar}` variable to grid `layout` property.
    - Revamp export form to be submitted in a new window (in a non-intrusive manner)
- Fixes #1 to #19.

## Version 1.2.0

**Date:** 22-Mar-2014

- Converted the extension into a module.
- Export features enhanced for use across all browsers):
   - Save displayed grid as HTML
   - Save displayed grid as CSV
   - Save displayed grid as TEXT
   - Save displayed grid as XLS

## Version 1.1.0

**Date:** 15-Mar-2014

- Export features added through a brand new custom JQuery plugin):
   - Save displayed grid as HTML
   - Save displayed grid as CSV
- Templates to modify positioning of the export menu and the panel before and after contents
- Ability to display toolbar in the header.

## Version 1.0.0

**Date:** 10-Mar-2014

Initial release
