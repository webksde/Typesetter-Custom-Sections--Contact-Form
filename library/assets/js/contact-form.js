(function() {
  //using regular expressions, validate email
  function ContactFormUtils($form){
    this.$form = $form;

    this.isValidEmail = function(email) {
      var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
      return regex.test(email);
    };
    //if no form errors, remove or hide error messages
    this.clearErrors = function() {
      $('#emailAlert', this.$form).remove();
      $('.help-block:not(".help-block--permanent")', this.$form).addClass('hidden');
      $('.form-group', this.$form).removeClass('has-error');
      // Callback
      $(document).trigger('contactFormClearErrors', [this.$form]);
    };

    //upon form clear remove the checked class and replace with unchecked class. Also reset Google ReCaptcha
    this.clearForm = function() {
      $('.contact-form-validation-icon', this.$form).removeClass(formValidationPassedIcon).addClass(formValidationInactiveIcon);
      $('input,textarea', this.$form).not('[type="hidden"]').val("");
      // Reset captcha.
      grecaptcha.reset();
      // Callback
      $(document).trigger('contactFormClearForm', [this.$form]);
    };

    //when error, show error messages and track that error exists
    this.addError = function($input) {
      var parentFormGroup = $input.parents('.form-group');
      parentFormGroup.children('.help-block:not(".help-block--permanent")').removeClass('hidden');
      parentFormGroup.addClass('has-error');
      // Callback
      $(document).trigger('contactFormAddedError', [this.$form]);
    };

    this.addAjaxMessage = function(msg, isError) {
      $(this.$form).prepend('<div id="emailAlert" class="alert alert-' + (isError ? 'danger' : 'success') + '">' + $('<div/>').text(msg).html() + '</div>');
      // Callback
      $(document).trigger('contactFormAddedMessage', [this.$form]);
    };
  }

  $(document).ready(function() {
    var $forms = $(".contact-form");

    $forms.each(function() {
      var $form = $(this);
      var formUtils = new ContactFormUtils($form);

      // Get icon classnames from data-attributes
      formValidationInactiveIcon = $form.attr('data-validation-inactive-icon');
      formValidationPassedIcon = $form.attr('data-validation-passed-icon');

      // Prevent submit, because we may not do this in the click
      // event of the button because otherwise the HTML5 validation does NOT work.
      $form.submit(function(e){ e.preventDefault(); });

      $(".feedbackSubmit", $form).click(function(e) {
        // Callback
        $(document).trigger('contactFormSubmitStart');

        var $btn = $(this);
        $btn.button('loading');
        formUtils.clearErrors();

        //do a little client-side validation -- check that each field has a value and e-mail field is in proper format
        //use bootstrap validator (https://github.com/1000hz/bootstrap-validator) if provided, otherwise a bit of custom validation
        var hasErrors = !$form.get(0).checkValidity();

        if ($form.validator) {
          hasErrors = $form.validator('validate').hasErrors;
        } else {
          $(':input[required]', $form).each(function() {
            var $this = $(this);
            // Check required checkboxes
            if (($this.is(':checkbox') && !$this.is(':checked')) || !$this.val()) {
              hasErrors = true;
              console.log('Checkbox field validation failed.');
              formUtils.addError($(this));
            }
          });
          // Mail field validation
          var $email = $('input[type="email"][required]', $form);
          if ($email.length > 0 && !formUtils.isValidEmail($email.val())) {
            hasErrors = true;
            console.log('Mail field validation failed.');
            formUtils.addError($email);
          }
          var $submitterEmail = $('input[type="email"]:first', $form);
          if ($submitterEmail.length > 0) {
            var submitterEmail = jQuery.trim($submitterEmail.val());
            if (submitterEmail.length > 0) {
              $('#formSubmitterMailAddress').val(submitterEmail);
            }
          }

          var $phone = $('input[type="tel"][required]', $form);
          if ($phone.length > 0 && $phone.val()) {
            hasErrors = true;
            console.log('Phone field validation failed.');
            formUtils.addError($phone.parent());
          }
        }

        //if there are any errors return without sending e-mail
        if (hasErrors) {
          $btn.button('reset');
          console.log('The form contains errors and could not be submitted: ' + hasErrors);
          return;
        }

        //send the feedback e-mail
        var formAction = $form.attr('action');
        if (!formAction) {
          formAction = 'CustomSectionContactFormSendHandler';
        }

        $.ajax({
          type: "POST",
          url: formAction,
          data: $form.serialize(),
          success: function(data) {
            // Callback
            $(document).trigger('contactFormSubmitSuccess', [$form]);
            formUtils.addAjaxMessage(data.message, false);
            formUtils.clearForm();
          },
          error: function(response) {
            console.log('response empty?');
            console.log(response);
            // Callback
            $(document).trigger('contactFormSubmitError', [$form]);

            if (!response.responseJSON || typeof response.responseJSON === undefined) {
              formUtils.addAjaxMessage("Error: You may not have filled out all required fields (correctly).", true);
            } else {
              formUtils.addAjaxMessage(response.responseJSON.message, true);
            }
          },
          complete: function() {
            console.log('complete');
            $btn.button('reset');
            // Callback
            $(document).trigger('contactFormSubmitComplete', [$form]);

          }
        });
      });
      $('input, textarea', $form).change(function() {
        var checkBox = $(this).siblings('span.input-group-addon').children('.contact-form-validation-icon');
        if ($(this).val()) {
          checkBox.removeClass(formValidationInactiveIcon).addClass(formValidationPassedIcon);
        } else {
          checkBox.removeClass(formValidationPassedIcon).addClass(formValidationInactiveIcon);
        }
      });
    });
  });
})();
