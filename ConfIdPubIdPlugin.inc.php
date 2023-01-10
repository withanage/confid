<?php


use PKP\components\forms\FieldHTML;
use PKP\components\forms\FieldPubId;
use PKP\components\forms\FieldText;

import('classes.plugins.PubIdPlugin');

class ConfIdPubIdPlugin extends PubIdPlugin
{

	public function register($category, $path, $mainContextId = null)
	{
		$success = parent::register($category, $path, $mainContextId);
		if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return $success;
		if ($success && $this->getEnabled($mainContextId)) {
			HookRegistry::register('Publication::getProperties::summaryProperties', array($this, 'modifyObjectProperties'));
			HookRegistry::register('Publication::getProperties::fullProperties', array($this, 'modifyObjectProperties'));
			HookRegistry::register('Publication::validate', array($this, 'validatePublicationDoi'));
			HookRegistry::register('Issue::getProperties::summaryProperties', array($this, 'modifyObjectProperties'));
			HookRegistry::register('Issue::getProperties::fullProperties', array($this, 'modifyObjectProperties'));
			HookRegistry::register('Galley::getProperties::summaryProperties', array($this, 'modifyObjectProperties'));
			HookRegistry::register('Galley::getProperties::fullProperties', array($this, 'modifyObjectProperties'));
			HookRegistry::register('Publication::getProperties::values', array($this, 'modifyObjectPropertyValues'));
			HookRegistry::register('Issue::getProperties::values', array($this, 'modifyObjectPropertyValues'));
			HookRegistry::register('Galley::getProperties::values', array($this, 'modifyObjectPropertyValues'));
			HookRegistry::register('Form::config::before', array($this, 'addPublicationFormFields'));
			HookRegistry::register('Form::config::before', array($this, 'addPublishFormNotice'));
		}
		return $success;
	}


	function getDisplayName()
	{
		return __('plugins.pubIds.confid.displayName');
	}

	function getDescription()
	{
		return __('plugins.pubIds.confid.description');
	}

	function constructPubId($pubIdPrefix, $pubIdSuffix, $contextId)
	{
		return $pubIdPrefix . '/' . $pubIdSuffix;
	}

	function getPubIdDisplayType()
	{
		return __('plugins.pubIds.confid.displayName');
	}

	function getPubIdFullName()
	{
		return 'Digital Object Identifier';
	}


	function getPubIdMetadataFile()
	{
		return $this->getTemplateResource('confidSuffixEdit.tpl');
	}

	function getPubIdAssignFile()
	{
		return $this->getTemplateResource('confidAssign.tpl');
	}

	function getResolvingURL($contextId, $pubId)
	{
		return 'https://doi.org/' . $pubId;
	}


	function instantiateSettingsForm($contextId)
	{
		$this->import('classes.form.ConfIDSettingsForm');
		return new ConfIDSettingsForm($this, $contextId);
	}

	function getFormFieldNames()
	{
		return array('confidSuffix');
	}

	function getAssignFormFieldName()
	{
		return 'assignDoi';
	}

	function getPrefixFieldName()
	{
		return 'confidPrefix';
	}

	function getSuffixFieldName()
	{
		return 'confidSuffix';
	}

	function getLinkActions($pubObject)
	{
		$linkActions = array();
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::get()->getRequest();
		$userVars = $request->getUserVars();
		$userVars['pubIdPlugIn'] = get_class($this);
		// Clear object pub id
		$linkActions['clearPubIdLinkActionConfId'] = new LinkAction(
			'clearPubId',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.confid.editor.clearObjectsDoi.confirm'),
				__('common.delete'),
				$request->url(null, null, 'clearPubId', null, $userVars),
				'modal_delete'
			),
			__('plugins.pubIds.confid.editor.clearObjectsDoi'),
			'delete',
			__('plugins.pubIds.confid.editor.clearObjectsDoi')
		);

		if (is_a($pubObject, 'Issue')) {
			// Clear issue objects pub ids
			$linkActions['clearIssueObjectsPubIdsLinkActionConfId'] = new LinkAction(
				'clearObjectsPubIds',
				new RemoteActionConfirmationModal(
					$request->getSession(),
					__('plugins.pubIds.confid.editor.clearIssueObjectsConfId.confirm'),
					__('common.delete'),
					$request->url(null, null, 'clearIssueObjectsPubIds', null, $userVars),
					'modal_delete'
				),
				__('plugins.pubIds.confid.editor.clearIssueObjectsConfId'),
				'delete',
				__('plugins.pubIds.confid.editor.clearIssueObjectsConfId')
			);
		}

		return $linkActions;
	}

	function getSuffixPatternsFieldNames()
	{
		return array(
			'Issue' => 'confidIssueSuffixPattern',
			'Publication' => 'confidPublicationSuffixPattern',
			'Representation' => 'confidRepresentationSuffixPattern'
		);
	}

	function getDAOFieldNames()
	{
		return array('pub-id::confid');
	}

	function isObjectTypeEnabled($pubObjectType, $contextId)
	{
		return (boolean)$this->getSetting($contextId, "enable${pubObjectType}Doi");
	}

	function getNotUniqueErrorMsg()
	{
		return __('plugins.pubIds.confid.editor.confidSuffixCustomIdentifierNotUnique');
	}

	function validatePubId($pubId)
	{
		return preg_match('/^\d+(.\d+)+\//', $pubId);
	}

	public function getCitationData($hookname, $args)
	{
		$citationData = $args[0];
		$article = $args[2];
		$issue = $args[3];
		$journal = $args[4];

		if ($issue && $issue->getPublished()) {
			$pubId = $article->getStoredPubId($this->getPubIdType());
		} else {
			$pubId = $this->getPubId($article);
		}

		if (!$pubId) {
			return;
		}

		$citationData->CONFID = $pubId;
	}

	function getPubIdType()
	{
		return 'confid';
	}

	public function validatePublicationDoi($hookName, $args)
	{
		$errors =& $args[0];
		$action = $args[1];
		$props =& $args[2];

		if (empty($props['pub-id::confid'])) {
			return;
		}

		if ($action === VALIDATE_ACTION_ADD) {
			$submission = Services::get('submission')->get($props['submissionId']);
		} else {
			$publication = Services::get('publication')->get($props['id']);
			$submission = Services::get('submission')->get($publication->getData('submissionId'));
		}

		$contextId = $submission->getData('contextId');
		$confidPrefix = $this->getSetting($contextId, 'confidPrefix');

		$confidErrors = [];
		if (strpos($props['pub-id::confid'], $confidPrefix) !== 0) {
			$confidErrors[] = __('plugins.pubIds.confid.editor.missingPrefix', ['confidPrefix' => $confidPrefix]);
		}

		if (!empty($confidErrors)) {
			$errors['pub-id::confid'] = $confidErrors;
		}
	}

	public function modifyObjectProperties($hookName, $args)
	{
		$props =& $args[0];

		$props[] = 'pub-id::confid';
	}


	public function modifyObjectPropertyValues($hookName, $args)
	{
		$values =& $args[0];
		$object = $args[1];
		$props = $args[2];

		// ConfIDs are not supported for IssueGalleys
		if (get_class($object) === 'IssueGalley') {
			return;
		}

		// ConfIDs are already added to property values for Publications and Galleys
		if (get_class($object) === 'Publication' || get_class($object) === 'ArticleGalley') {
			return;
		}

		if (in_array('pub-id::confid', $props)) {
			$pubId = $this->getPubId($object);
			$values['pub-id::confid'] = $pubId ? $pubId : null;
		}
	}

	public function addPublicationFormFields($hookName, $form)
	{

		if ($form->id !== 'publicationIdentifiers') {
			return;
		}

		if (!$this->getSetting($form->submissionContext->getId(), 'enablePublicationDoi')) {
			return;
		}

		$prefix = $this->getSetting($form->submissionContext->getId(), 'confidPrefix');

		$suffixType = $this->getSetting($form->submissionContext->getId(), 'confidSuffix');
		$pattern = '';
		if ($suffixType === 'default') {
			$pattern = '%j.v%vi%i.%a';
		} elseif ($suffixType === 'pattern') {
			$pattern = $this->getSetting($form->submissionContext->getId(), 'confidPublicationSuffixPattern');
		}

		// Add a text field to enter the CONFID if no pattern exists
		if (!$pattern) {
			$form->addField(new FieldText('pub-id::confid', [
				'label' => __('metadata.property.displayName.confid'),
				'description' => __('plugins.pubIds.confid.editor.confid.description', ['prefix' => $prefix]),
				'value' => $form->publication->getData('pub-id::confid'),
			]));
		} else {
			$fieldData = [
				'label' => __('metadata.property.displayName.confid'),
				'value' => $form->publication->getData('pub-id::confid'),
				'prefix' => $prefix,
				'pattern' => $pattern,
				'contextInitials' => PKPString::regexp_replace('/[^-._;()\/A-Za-z0-9]/', '', PKPString::strtolower($form->submissionContext->getData('acronym', $form->submissionContext->getData('primaryLocale')))) ?? '',
				'separator' => '/',
				'submissionId' => $form->publication->getData('submissionId'),
				'assignIdLabel' => __('plugins.pubIds.confid.editor.confid.assignDoi'),
				'clearIdLabel' => __('plugins.pubIds.confid.editor.clearObjectsDoi'),
			];
			if ($form->publication->getData('pub-id::publisher-id')) {
				$fieldData['publisherId'] = $form->publication->getData('pub-id::publisher-id');
			}
			if ($form->publication->getData('pages')) {
				$fieldData['pages'] = $form->publication->getData('pages');
			}
			if ($form->publication->getData('issueId')) {
				$issue = Services::get('issue')->get($form->publication->getData('issueId'));
				if ($issue) {
					$fieldData['issueNumber'] = $issue->getNumber() ?? '';
					$fieldData['issueVolume'] = $issue->getVolume() ?? '';
					$fieldData['year'] = $issue->getYear() ?? '';
				}
			}
			if ($suffixType === 'default') {
				$fieldData['missingPartsLabel'] = __('plugins.pubIds.confid.editor.missingIssue');
			} else {
				$fieldData['missingPartsLabel'] = __('plugins.pubIds.confid.editor.missingParts');
			}
			$form->addField(new FieldPubId('pub-id::confid', $fieldData));
		}
	}

	public function addPublishFormNotice($hookName, $form)
	{

		if ($form->id !== 'publish' || !empty($form->errors)) {
			return;
		}

		$submission = Services::get('submission')->get($form->publication->getData('submissionId'));
		$publicationDoiEnabled = $this->getSetting($submission->getData('contextId'), 'enablePublicationDoi');
		$galleyDoiEnabled = $this->getSetting($submission->getData('contextId'), 'enableRepresentationDoi');
		$warningIconHtml = '<span class="fa fa-exclamation-triangle pkpIcon--inline"></span>';

		if (!$publicationDoiEnabled && !$galleyDoiEnabled) {
			return;

			// Use a simplified view when only assigning to the publication
		} else if (!$galleyDoiEnabled) {
			if ($form->publication->getData('pub-id::confid')) {
				$msg = __('plugins.pubIds.confid.editor.preview.publication', ['confid' => $form->publication->getData('pub-id::confid')]);
			} else {
				$msg = '<div class="pkpNotification pkpNotification--warning">' . $warningIconHtml . __('plugins.pubIds.confid.editor.preview.publication.none') . '</div>';
			}
			$form->addField(new FieldHTML('confid', [
				'description' => $msg,
				'groupId' => 'default',
			]));
			return;

			// Show a table if more than one CONFID is going to be created
		} else {
			$confidTableRows = [];
			if ($publicationDoiEnabled) {
				if ($form->publication->getData('pub-id::confid')) {
					$confidTableRows[] = [$form->publication->getData('pub-id::confid'), 'Publication'];
				} else {
					$confidTableRows[] = [$warningIconHtml . __('submission.status.unassigned'), 'Publication'];
				}
			}
			if ($galleyDoiEnabled) {
				foreach ((array)$form->publication->getData('galleys') as $galley) {
					if ($galley->getStoredPubId('confid')) {
						$confidTableRows[] = [$galley->getStoredPubId('confid'), __('plugins.pubIds.confid.editor.preview.galleys', ['galleyLabel' => $galley->getGalleyLabel()])];
					} else {
						$confidTableRows[] = [$warningIconHtml . __('submission.status.unassigned'), __('plugins.pubIds.confid.editor.preview.galleys', ['galleyLabel' => $galley->getGalleyLabel()])];
					}
				}
			}
			if (!empty($confidTableRows)) {
				$table = '<table class="pkpTable"><thead><tr>' .
					'<th>' . __('plugins.pubIds.confid.editor.confid') . '</th>' .
					'<th>' . __('plugins.pubIds.confid.editor.preview.objects') . '</th>' .
					'</tr></thead><tbody>';
				foreach ($confidTableRows as $confidTableRow) {
					$table .= '<tr><td>' . $confidTableRow[0] . '</td><td>' . $confidTableRow[1] . '</td></tr>';
				}
				$table .= '</tbody></table>';
			}
			$form->addField(new FieldHTML('confid', [
				'description' => $table,
				'groupId' => 'default',
			]));
		}
	}
}
