<?php


import('lib.pkp.classes.form.Form');

class ConfIDSettingsForm extends Form
{


	var $_contextId;
	var $_plugin;

	function __construct($plugin, $contextId)
	{
		$this->_contextId = $contextId;
		$this->_plugin = $plugin;

		parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));

		$this->addCheck(new FormValidatorRegExp($this, 'confidPrefix', 'required', 'plugins.pubIds.confid.manager.settings.confidPrefixPattern', '/^10\.[0-9]{4,7}$/'));
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));

		// for CONFID reset requests
		import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
		$request = Application::get()->getRequest();
		$this->setData('clearConfIdPubIdsLinkAction', new LinkAction(
			'reassignConfIDs',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.confid.manager.settings.confidReassign.confirm'),
				__('common.delete'),
				$request->url(null, null, 'manage', null, array('verb' => 'clearPubIds', 'plugin' => $plugin->getName(), 'category' => 'pubIds')),
				'modal_delete'
			),
			__('plugins.pubIds.confid.manager.settings.confidReassign'),
			'delete'
		));
		$this->setData('assignJournalWidePubIdsLinkAction', new LinkAction(
			'assignConfIDs',
			new RemoteActionConfirmationModal(
				$request->getSession(),
				__('plugins.pubIds.confid.manager.settings.confidAssignJournalWide.confirm'),
				__('plugins.pubIds.confid.manager.settings.confidAssignJournalWide'),
				$request->url(null, null, 'manage', null, array('verb' => 'assignPubIds', 'plugin' => $plugin->getName(), 'category' => 'pubIds')),
				'modal_confirm'
			),
			__('plugins.pubIds.confid.manager.settings.confidAssignJournalWide'),
			'advance'
		));
		$this->setData('pluginName', $plugin->getName());
	}

	function getName()
	{
		return 'confid';
	}


	function initData()
	{
		$contextId = $this->_getContextId();
		$plugin = $this->_getPlugin();
		foreach ($this->_getFormFields() as $fieldName => $fieldType) {
			$this->setData($fieldName, $plugin->getSetting($contextId, $fieldName));
		}
	}

	function _getContextId()
	{
		return $this->_contextId;
	}



	function _getPlugin()
	{
		return $this->_plugin;
	}

	function _getFormFields()
	{
		return array(
			'enableIssueDoi' => 'bool',
			'confidPrefix' => 'string',
			'confidSuffix' => 'string',
			'confidIssueSuffixPattern' => 'string'

		);
	}

	function readInputData()
	{
		$this->readUserVars(array_keys($this->_getFormFields()));
	}


	function execute(...$functionArgs)
	{
		$plugin = $this->_getPlugin();
		$contextId = $this->_getContextId();
		foreach ($this->_getFormFields() as $fieldName => $fieldType) {
			$plugin->updateSetting($contextId, $fieldName, $this->getData($fieldName), $fieldType);
		}
		parent::execute(...$functionArgs);
	}
}


