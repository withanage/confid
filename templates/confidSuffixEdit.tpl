
{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectDoi value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`Doi")}

{if $enableObjectDoi}
    {assign var=storedPubId value=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
    {fbvFormArea id="pubIdConfIDFormArea" class="border" title="plugins.pubIds.confid.editor.confid"}
    {assign var=formArea value=true}
    {if $pubIdPlugin->getSetting($currentContext->getId(), 'confidSuffix') == 'customId' || $storedPubId}
        {if empty($storedPubId)} {* edit custom suffix *}
            {fbvFormSection}
				<p class="pkp_help">{translate key="plugins.pubIds.confid.manager.settings.confidSuffix.description"}</p>
            {fbvElement type="text" label="plugins.pubIds.confid.manager.settings.confidPrefix" id="confidPrefix" disabled=true value=$pubIdPlugin->getSetting($currentContext->getId(), 'confidPrefix') size=$fbvStyles.size.SMALL}
            {fbvElement type="text" label="plugins.pubIds.confid.manager.settings.confidSuffix" id="confidSuffix" value=$confidSuffix size=$fbvStyles.size.MEDIUM}
            {/fbvFormSection}
            {if $canBeAssigned}
                {if !$formDisabled}
                    {assign var=templatePath value=$pubIdPlugin->getTemplateResource('confidAssignCheckBox.tpl')}
                    {include file=$templatePath pubId="" pubObjectType=$pubObjectType}
                {/if}
            {else}
				<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.customSuffixMissing"}</p>
            {/if}
        {else} {* stored pub id and clear option *}
            {fbvFormSection}
				<p>
                    {$storedPubId|escape}<br/>
                    {capture assign=translatedObjectType}{translate key="plugins.pubIds.confid.editor.confidObjectType"|cat:$pubObjectType}{/capture}
                    {capture assign=assignedMessage}{translate key="plugins.pubIds.confid.editor.assigned" pubObjectType=$translatedObjectType}{/capture}
				<p class="pkp_help">{$assignedMessage}</p>
            {if !$formDisabled}
                {include file="linkAction/linkAction.tpl" action=$clearPubIdLinkActionConfId contextId="publicIdentifiersForm"}
            {/if}
				</p>
            {/fbvFormSection}
        {/if}
    {else} {* pub id preview *}
        {if !$formDisabled}
			<p>{$pubIdPlugin->getPubId($pubObject)|escape}</p>
        {/if}
        {if $canBeAssigned}
            {if !$formDisabled}
				<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.canBeAssigned"}</p>
                {assign var=templatePath value=$pubIdPlugin->getTemplateResource('confidAssignCheckBox.tpl')}
                {include file=$templatePath pubId="" pubObjectType=$pubObjectType}
            {else}
				<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.noDoiAssigned"}</p>
            {/if}

        {else}
			<p class="pkp_help">{translate key="plugins.pubIds.confid.editor.patternNotResolved"}</p>
        {/if}
    {/if}
    {/fbvFormArea}
{/if}
{* issue pub object *}
{if $pubObjectType == 'Issue'}
    {assign var=enablePublicationDoi value=$pubIdPlugin->getSetting($currentContext->getId(), "enablePublicationDoi")}
    {assign var=enableRepresentationDoi value=$pubIdPlugin->getSetting($currentContext->getId(), "enableRepresentationDoi")}
    {if $enablePublicationDoi || $enableRepresentationDoi}
        {if !$formArea}
            {assign var="formAreaTitle" value="plugins.pubIds.confid.editor.confid"}
        {else}
            {assign var="formAreaTitle" value=""}
        {/if}
        {fbvFormArea id="pubIdConfIDFormArea" class="border" title=$formAreaTitle}
        {fbvFormSection list="true" description="plugins.pubIds.confid.editor.clearIssueObjectsConfId.description"}
            {include file="linkAction/linkAction.tpl" action=$clearIssueObjectsPubIdsLinkActionConfId contextId="publicIdentifiersForm"}
        {/fbvFormSection}
        {/fbvFormArea}
    {/if}
{/if}
