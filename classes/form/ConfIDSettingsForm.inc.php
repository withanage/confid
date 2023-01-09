<?php

/**
 * @file plugins/pubIds/confid/classes/form/ConfIDSettingsForm.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ConfIDSettingsForm
 * @ingroup plugins_pubIds_doi
 *
 * @brief Form for journal managers to setup CONFID plugin
 */


import('lib.pkp.classes.form.Form');

class ConfIDSettingsForm extends Form {

	//
	// Private properties
	//
	/** @var integer */
	var $_contextId;

	/**
	 * Get the context ID.
	 * @return integer
	 */
	function _getContextId() {
		return $this->_contextId;
	}

	/** @var ConfIdPubIdPlugin */
	var $_plugin;

	/**
	 * Get the plugin.
	 * @return ConfIdPubIdPlugin
	 */
	function _getPlugin() {
		return $this->_plugin;
	}


	//
	// Constructor
	//
	/**
	 * Constructor
	 * @param $plugin ConfIdPubIdPlugin
	 * @param $contextId integer
	 */
	function __construct($plugin, $contextId) {
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

		$form = $this;
		$this->addCheck(new FormValidatorCustom($this, 'confidObjects', 'required', 'plugins.pubIds.confid.manager.settings.doiObjectsRequired', function($enableIssueDoi) use ($form) {
			return $form->getData('enableIssueDoi') || $form->getData('enablePublicationDoi') || $form->getData('enableRepresentationDoi');
		}));
		$this->addCheck(new FormValidatorRegExp($this, 'confidPrefix', 'required', 'plugins.pubIds.confid.manager.settings.confidPrefixPattern', '/^10\.[0-9]{4,7}$/'));
		/**
		$this->addCheck(new FormValidatorCustom($this, 'confidIssueSuffixPattern', 'required', 'plugins.pubIds.confid.manager.settings.doiIssueSuffixPatternRequired', function($doiIssueSuffixPattern) use ($form) {
			if ($form->getData('confidSuffix') == 'pattern' && $form->getData('enableIssueDoi')) return $doiIssueSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorCustom($this, 'confidPublicationSuffixPattern', 'required', 'plugins.pubIds.confid.manager.settings.doiPublicationSuffixPatternRequired', function($doiPublicationSuffixPattern) use ($form) {
			if ($form->getData('confidSuffix') == 'pattern' && $form->getData('enablePublicationDoi')) return $doiPublicationSuffixPattern != '';
			return true;
		}));
		$this->addCheck(new FormValidatorCustom($this, 'confidRepresentationSuffixPattern', 'required', 'plugins.pubIds.confid.manager.settings.doiRepresentationSuffixPatternRequired', function($doiRepresentationSuffixPattern) use ($form) {
			if ($form->getData('confidSuffix') == 'pattern' && $form->getData('enableRepresentationDoi')) return $doiRepresentationSuffixPattern != '';
			return true;
		}));
		 **/
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));

		// for CONFID reset requests
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::get()->getRequest();
		$this->setData('clearPubIdsLinkAction', new LinkAction(
			'reassignConfIDs',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.confid.manager.settings.doiReassign.confirm'),
				__('common.delete'),
				$request->url(null, null, 'manage', null, array('verb' => 'clearPubIds', 'plugin' => $plugin->getName(), 'category' => 'pubIds')),
				'modal_delete'
			),
			__('plugins.pubIds.confid.manager.settings.doiReassign'),
			'delete'
		));
		$this->setData('assignJournalWidePubIdsLinkAction', new LinkAction(
			'assignConfIDs',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.confid.manager.settings.doiAssignJournalWide.confirm'),
				__('plugins.pubIds.confid.manager.settings.doiAssignJournalWide'),
				$request->url(null, null, 'manage', null, array('verb' => 'assignPubIds', 'plugin' => $plugin->getName(), 'category' => 'pubIds')),
				'modal_confirm'
			),
			__('plugins.pubIds.confid.manager.settings.doiAssignJournalWide'),
			'advance'
		));
		$this->setData('pluginName', $plugin->getName());
	}

	function getName(){
		return 'confid';
	}


	//
	// Implement template methods from Form
	//
	/**
	 * @copydoc Form::initData()
	 */
	function initData() {
		$contextId = $this->_getContextId();
		$plugin = $this->_getPlugin();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($contextId, $fieldName));
		}
	}

	/**
	 * @copydoc Form::readInputData()
	 */
	function readInputData() {
		$this->readUserVars(array_keys($this->_getFormFields()));
	}

	/**
	 * @copydoc Form::execute()
	 */
	function execute(...$functionArgs) {
		$plugin = $this->_getPlugin();
		$contextId = $this->_getContextId();
		foreach($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($contextId, $fieldName, $this->getData($fieldName), $fieldType);
		}
		parent::execute(...$functionArgs);
	}


	//
	// Private helper methods
	//
	function _getFormFields() {
		return array(
			'enableIssueDoi' => 'bool',
			'enablePublicationDoi' => 'bool',
			'enableRepresentationDoi' => 'bool',
			'confidPrefix' => 'string',
			'pubid::confid' => 'string',
			'confidIssueSuffixPattern' => 'string',
			'confidPublicationSuffixPattern' => 'string',
			'confidRepresentationSuffixPattern' => 'string',
		);
	}
}


