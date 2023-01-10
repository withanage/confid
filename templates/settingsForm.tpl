{**
 * plugins/pubIds/confid/templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * CONFID plugin settings
 *
 *}

<div id="description">{translate key="plugins.pubIds.confid.manager.settings.description"}</div>

<script src="{$baseUrl}/plugins/pubIds/confid/js/ConfIDSettingsFormHandler.js"></script>
<script>
	$(function () {ldelim}
		// Attach the form handler.
		$('#confidSettingsForm').pkpHandler('$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler');
        {rdelim});
</script>
<form class="pkp_form" id="confidSettingsForm" method="post"
	  action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="pubIds" plugin=$pluginName verb="save"}">
    {csrf}
    {include file="common/formErrors.tpl"}
    {fbvFormArea id="confidObjectsFormArea" title="plugins.pubIds.confid.manager.settings.confidObjects"}
    {fbvFormSection list="true"}
		<p class="pkp_help">{translate key="plugins.pubIds.confid.manager.settings.explainDois"}</p>
    {fbvElement type="checkbox" id="enableIssueDoi" label="plugins.pubIds.confid.manager.settings.enableIssueDoi" maxlength="40" checked=$enableIssueDoi|compare:true}
    {fbvElement type="checkbox" id="enablePublicationDoi" label="plugins.pubIds.confid.manager.settings.enablePublicationDoi" maxlength="40" checked=$enablePublicationDoi|compare:true}
    {fbvElement type="checkbox" id="enableRepresentationDoi" label="plugins.pubIds.confid.manager.settings.enableRepresentationDoi" maxlength="40" checked=$enableRepresentationDoi|compare:true}
    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormArea id="confidPrefixFormArea" title="plugins.pubIds.confid.manager.settings.confidPrefix"}
    {fbvFormSection}
		<p class="pkp_help">{translate key="plugins.pubIds.confid.manager.settings.confidPrefix.description"}</p>
    {fbvElement type="text" id="confidPrefix" value=$confidPrefix required="true" label="plugins.pubIds.confid.manager.settings.confidPrefix" maxlength="40" size=$fbvStyles.size.MEDIUM}
    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormArea id="confidSuffixFormArea" title="plugins.pubIds.confid.manager.settings.confidSuffix"}
		<p class="pkp_help">{translate key="plugins.pubIds.confid.manager.settings.confidSuffix.description"}</p>
    {fbvFormSection list="true"}
    {if !in_array($confidSuffix, array("pattern", "customId"))}
        {assign var="checked" value=true}
    {else}
        {assign var="checked" value=false}
    {/if}
    {fbvElement type="radio" id="confidSuffixDefault" name="confidSuffix" value="default" required="true" label="plugins.pubIds.confid.manager.settings.confidSuffixDefault" checked=$checked}
		<span class="instruct">{translate key="plugins.pubIds.confid.manager.settings.confidSuffixDefault.description"}</span>
    {/fbvFormSection}
    {fbvFormSection list="true"}
    {fbvElement type="radio" id="confidSuffixCustomId" name="confidSuffix" value="customId" required="true" label="plugins.pubIds.confid.manager.settings.confidSuffixCustomIdentifier" checked=$confidSuffix|compare:"customId"}
    {/fbvFormSection}
    {fbvFormSection list="true"}
    {fbvElement type="radio" id="confidSuffixPattern" name="confidSuffix" value="pattern" label="plugins.pubIds.confid.manager.settings.confidSuffixPattern" checked=$confidSuffix|compare:"pattern"}
		<p class="pkp_help">{translate key="plugins.pubIds.confid.manager.settings.confidSuffixPattern.example"}</p>
    {fbvElement type="text" id="confidIssueSuffixPattern" value=$confidIssueSuffixPattern label="plugins.pubIds.confid.manager.settings.confidSuffixPattern.issues" maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
    {fbvElement type="text" id="confidPublicationSuffixPattern" value=$confidPublicationSuffixPattern label="plugins.pubIds.confid.manager.settings.confidSuffixPattern.submissions" maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
    {fbvElement type="text" id="confidRepresentationSuffixPattern" value=$confidRepresentationSuffixPattern label="plugins.pubIds.confid.manager.settings.confidSuffixPattern.representations" maxlength="40" inline=true size=$fbvStyles.size.MEDIUM}
    {/fbvFormSection}
    {/fbvFormArea}
    {fbvFormArea id="confidReassignFormArea" title="plugins.pubIds.confid.manager.settings.confidReassign"}
    {fbvFormSection}
		<div class="instruct">{translate key="plugins.pubIds.confid.manager.settings.confidReassign.description"}</div>
        {include file="linkAction/linkAction.tpl" action=$clearPubIdsLinkAction contextId="confidSettingsForm"}
    {/fbvFormSection}
    {/fbvFormArea}
    {if ($enableIssueDoi || $enablePublicationDoi || $enableRepresentationDoi) && $confidPrefix && $confidSuffix && $confidSuffix != 'customId' }
        {fbvFormArea id="confidAssignJournalWideFormArea" title="plugins.pubIds.confid.manager.settings.confidAssignJournalWide"}
        {fbvFormSection}
			<div class="instruct">{translate key="plugins.pubIds.confid.manager.settings.confidAssignJournalWide.description"}</div>
            {include file="linkAction/linkAction.tpl" action=$assignJournalWidePubIdsLinkAction contextId="confidSettingsForm"}
        {/fbvFormSection}
        {/fbvFormArea}
    {/if}
    {fbvFormButtons submitText="common.save"}
</form>
<p><span class="formRequired">{translate key="common.requiredField"}</span></p>
