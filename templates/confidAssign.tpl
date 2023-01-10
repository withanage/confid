{**
 * @file plugins/pubIds/confid/templates/confidAssign.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Assign CONFID to an object option.
 *}

{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectDoi value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`Doi")}
{if $enableObjectDoi}
    {fbvFormArea id="pubIdConfIDFormArea" class="border" title="plugins.pubIds.confid.editor.confid"}
    {if $pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
        {fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.assignDoi.assigned" pubId=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}</p>
        {/fbvFormSection}
    {else}
        {assign var=pubId value=$pubIdPlugin->getPubId($pubObject)}
        {if !$canBeAssigned}
            {fbvFormSection}
            {if !$pubId}
				<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.assignDoi.emptySuffix"}</p>
            {else}
				<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.assignDoi.pattern" pubId=$pubId}</p>
            {/if}
            {/fbvFormSection}
        {else}
            {assign var=templatePath value=$pubIdPlugin->getTemplateResource('confidAssignCheckBox.tpl')}
            {include file=$templatePath pubId=$pubId pubObjectType=$pubObjectType}
        {/if}
    {/if}
    {/fbvFormArea}
{/if}
