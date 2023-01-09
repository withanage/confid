{**
 * @file plugins/pubIds/confid/templates/doiAssignCheckBox.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Displayed only if the CONFID can be assigned.
 * Assign CONFID form check box included in confidSuffixEdit.tpl and doiAssign.tpl.
 *}

{capture assign=translatedObjectType}{translate key="plugins.pubIds.confid.editor.doiObjectType"|cat:$pubObjectType}{/capture}
{capture assign=assignCheckboxLabel}{translate key="plugins.pubIds.confid.editor.assignDoi" pubId=$pubId pubObjectType=$translatedObjectType}{/capture}
{fbvFormSection list=true}
	{fbvElement type="checkbox" id="assignDoi" checked="true" value="1" label=$assignCheckboxLabel translate=false disabled=$disabled}
{/fbvFormSection}
