/**
 * @defgroup plugins_pubIds_confid_js
 */
/**
 * @file plugins/pubIds/confid/js/ConfIdSettingsFormHandler.js
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2000-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class ConfIDSettingsFormHandler.js
 * @ingroup plugins_pubIds_confid_js
 *
 * @brief Handle the CONFID Settings form.
 */
(function($) {

	/** @type {Object} */
	$.pkp.plugins.pubIds.confid =
			$.pkp.plugins.pubIds.confid ||
			{ js: { } };



	/**
	 * @constructor
	 *
	 * @extends $.pkp.controllers.form.AjaxFormHandler
	 *
	 * @param {jQueryObject} $form the wrapped HTML form element.
	 * @param {Object} options form options.
	 */
	$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler =
			function($form, options) {

		this.parent($form, options);

		$(':radio, :checkbox', $form).click(
				this.callbackWrapper(this.updatePatternFormElementStatus_));
		//ping our handler to set the form's initial state.
		this.callbackWrapper(this.updatePatternFormElementStatus_());

		this.bind('formSubmitted', this.callbackWrapper(this.maybeReloadPage_));
	};
	$.pkp.classes.Helper.inherits(
			$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler,
			$.pkp.controllers.form.AjaxFormHandler);


	/**
	 * Callback to replace the element's content.
	 *
	 * @private
	 */
	$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler.prototype.
			updatePatternFormElementStatus_ =
			function() {
		var $element = this.getHtmlElement(), pattern, $contentChoices;
		if ($('[id^="confidSuffix"]').filter(':checked').val() == 'pattern') {
			$contentChoices = $element.find(':checkbox');
			pattern = new RegExp('enable(.*)ConfID');
			$contentChoices.each(function() {
				var patternCheckResult = pattern.exec($(this).attr('name')),
						$correspondingTextField = $element.find('[id*="' +
						patternCheckResult[1] + 'SuffixPattern"]').
						filter(':text');

				if (patternCheckResult !== null &&
						patternCheckResult[1] !== 'undefined') {
					if ($(this).is(':checked')) {
						$correspondingTextField.removeAttr('disabled');
					} else {
						$correspondingTextField.attr('disabled', 'disabled');
					}
				}
			});
		} else {
			$element.find('[id*="SuffixPattern"]').filter(':text').
					attr('disabled', 'disabled');
		}
	};


	/**
	* Reload the page if we're on an import/export page. The CONFID settings can be accessed from several
	* import/export screens. When the CONFID settings change, this can impact the import/export settings, so
	* we just reload the whole page.
	*
	* @private
	*/
	$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler.prototype.maybeReloadPage_ = function() {
		if ($('body').hasClass('pkp_op_importexport')) {
			window.location.reload();
		}
	};

/** @param {jQuery} $ jQuery closure. */
}(jQuery));
