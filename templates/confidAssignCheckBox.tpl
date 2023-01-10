
{capture assign=translatedObjectType}{translate key="plugins.pubIds.confid.editor.confidObjectType"|cat:$pubObjectType}{/capture}
{capture assign=assignCheckboxLabel}{translate key="plugins.pubIds.confid.editor.assignDoi" pubId=$pubId pubObjectType=$translatedObjectType}{/capture}
{fbvFormSection list=true}
{fbvElement type="checkbox" id="assignDoi" checked="true" value="1" label=$assignCheckboxLabel translate=false disabled=$disabled}
{/fbvFormSection}
