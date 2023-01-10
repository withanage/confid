(function ($) {


	$.pkp.plugins.pubIds.confid =
		$.pkp.plugins.pubIds.confid ||
		{js: {}};


	$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler =
		function ($form, options) {

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


	$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler.prototype.updatePatternFormElementStatus_ =
		function () {
			var $element = this.getHtmlElement(), pattern, $contentChoices;
			if ($('[id^="confidSuffix"]').filter(':checked').val() == 'pattern') {
				$contentChoices = $element.find(':checkbox');
				pattern = new RegExp('enable(.*)ConfID');
				$contentChoices.each(function () {
					var patternCheckResult = pattern.exec($(this).attr('name')),
						$correspondingTextField = $element.find('[id*="' +
							patternCheckResult[1] + 'SuffixPattern"]').filter(':text');

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
				$element.find('[id*="SuffixPattern"]').filter(':text').attr('disabled', 'disabled');
			}
		};


	$.pkp.plugins.pubIds.confid.js.ConfIDSettingsFormHandler.prototype.maybeReloadPage_ = function () {
		if ($('body').hasClass('pkp_op_importexport')) {
			window.location.reload();
		}
	};


}(jQuery));
