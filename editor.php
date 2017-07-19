<?php
/*
#############################################################################################
Editor values for section "Contact Form" for Typesetter CMS developer plugin - 'Custom Sections'
By: webks: websolutions kept simple
Based on Custom Sections by: J. Krausz and a2exfr
Date: 2017-02-21
Version 0.5b
#############################################################################################
*/


defined('is_running') or die('Not an entry point...');

global $addonPathData, $addonPathCode, $config;

// Load common variables, functions and initialize translation:
require($addonPathCode.'/_types/contact_form/common.php');

$editor = array(
  'custom_scripts' =>     false,  // use a custom editor Javascript for this section type?
  'custom_css' =>         false,  // use a custom editor CSS for this section type?
  'editor_components' =>  false,  // only when using custom editor script(s), we use this to load components like text, colorpicker, clockpicker, datepicker
  // the components value can be a string 'text', a csv 'text,colorpicker' or an array('text','colorpicker');
  // when the universal editor is used, we do not need to set this value, because components will be auto loaded with the respective control_type

  // js_on_content is a callback executed after content has been changed.
  // javascript code to be executed when the currently edited section is updated. Use e.g. for re-initing something.
  // 'js_on_content' => 'console.log("Section updated")',

  // TODO: Attributes currently not working - add regex if fixed (https://github.com/juek/CustomSections/issues/19):
  //     : - HTML allowed in mail headers & footers
  //     : - Only letters and digits component texts (button, messages, ...)

  'controls' => array(
    'form_items'=>array(
      'label' => '<i class="fa fa-file-text-o"></i> ' . $tString['formItemLabel'],
      'control_type' => 'bunch_control',
      'sub_controls'=>array(
        // Single field settings

        // Field required
        'required' => array(
          'label' => $tString['fieldRequiredLabel'],
          'control_type' => 'checkbox',
          'attributes' => array(),
          'on' => array(),
        ),
        // Field disabled
        'disabled' => array(
          'label' => $tString['fieldDisabledLabel'],
          'control_type' => 'checkbox',
          'attributes' => array(),
          'on' => array(),
        ),

        // Field types
        'field_type' => array(
          'label' => '<i class="fa fa-cube"></i> ' . $tString['fieldTypeLabel'],
          'control_type' => 'radio_group',
          'radio-buttons' => array(
            // Define selectable input types:
            'input_type_text' => 'Text',
            'input_type_textarea' => 'Textarea',
            'input_type_select' => 'Select',
            'input_type_radio' => 'Radio',
            'input_type_checkbox' => 'Checkbox',
            'input_type_date' => 'Date',
            'input_type_time' => 'Time',
            'input_type_datetime' => 'Datetime',
            'input_type_datetime_local' => 'Datetime (Local)',
            'input_type_email' => 'E-Mail',
            'input_type_phone' => 'Telephone Number',
            'input_type_url' => 'URL',
            'input_type_number' => 'Number',
          ),
          'attributes' => array(
            // TODO: Attributes not working on bunch_control sub items? ==> https://github.com/juek/CustomSections/issues/19
            'id' => 'field-types',
            'class' => 'contact-form-field-type',
          ),
          'on' => array(
            'change' => 'function(){
              console.log($(this).attr("value"));
              if($(this).attr("value") === "input_type_radio" || $(this).attr("value") === "input_type_select" || $(this).attr("value") === "input_type_checkbox"){
                // Show options field on multi value fields
                $(this).parents(".sub_controls_cont:first").find("textarea[id*=\"options\"]").parents(".editor-ctl-box:first").show();
                // Hide placeholder field on none text fields
                $(this).parents(".sub_controls_cont:first").find("input[id*=\"placeholder\"]").parents(".editor-ctl-box:first").hide();
              }else{
                // Hide options field on none multi value fields
                $(this).parents(".sub_controls_cont:first").find("textarea[id*=\"options\"]").parents(".editor-ctl-box:first").hide();
                // Show placeholder field on text fields
                $(this).parents(".sub_controls_cont:first").find("input[id*=\"placeholder\"]").parents(".editor-ctl-box:first").show();
              }
            }',
          ),
        ),
        // Field options (eg. Select options)
        'options' => array(
          'label' => '<i class="fa fa-cubes"></i> ' . $tString['fieldOptionsLabel'],
          'control_type' => 'textarea',
          'attributes' => array(
            'id' => 'form-field-subvalues',
            'class' => 'hidden', // hide initally (visibility depends on the field type) - TODO: Class not working?
            'placeholder' => 'value|Label',
          ),
          // 'on' => array(),
        ),
        // value 'option / select values' --end

        'label' => array(
          'label' => '<i class="fa fa-font"></i> ' . $tString['fieldLabelLabel'],
          'control_type' => 'text',
          'attributes' => array(
            // 'class' => '',
            'placeholder' => 'Label',
            // 'pattern' => '', // regex for validation
          ),
          'on' => array(
            'focus' => 'function(){ $(this).select(); }',
            'input' => 'function(){
              // Automaticly creates a machine readable name from the label value
              function convertToSlug(text)
              {
                  return text
                    .toLowerCase()
                    .replace(/[^\w ]+/g,"")
                    .replace(/ +/g,"_");
              }
              var currentLabel = convertToSlug($(this).val());
              $(this).parents(".sub_controls_cont:first").find("input[id*=\"machine_name\"]:first").attr("value", currentLabel);
            }',
          ),
        ),
        'machine_name' => array(
          'label' => '<i class="fa fa-terminal"></i> ' . $tString['fieldMachineNameLabel'],
          'control_type' => 'text',
          'attributes' => array(
            'class' => 'machine-name',
            'placeholder' => 'Machine Name',
            'pattern' => '^[a-z0-9_]*$', // regex: start of string, lowercase letters, digits and underline
          ),
          'on' => array(
            'focus' => 'function(){ $(this).select(); }',
          ),
        ),
        'placeholder' => array(
          'label' => '<i class="fa fa-font"></i> ' . $tString['fieldPlaceholderLabel'],
          'control_type' => 'text',
          'attributes' => array(
            // 'class' => '',
            'placeholder' => 'Placeholder',
            // 'pattern' => '', // regex for validation
          ),
          'on' => array(
            'focus' => 'function(){ $(this).select(); }',
          ),
        ),
        'description_on_error' => array(
          'label' => $tString['fieldDescOnErrorLabel'],
          'control_type' => 'checkbox',
          'attributes' => array(),
          'on' => array(),
        ),
        'description' => array(
          'label' => '<i class="fa fa-align-left"></i> ' . $tString['fieldDescLabel'],
          'control_type' => 'text',
          'attributes' => array(
            // 'class' => '',
            // 'placeholder' => 'A short description',
          ),
          'on' => array(),
        ),
        'prefix' => array(
          'label' => '<i class="fa fa-font"></i> ' . $tString['fieldPrefixLabel'],
          'control_type' => 'text',
          'attributes' => array(
            // 'class' => '',
            'placeholder' => 'Placeholder',
            // 'pattern' => '', // regex for validation
          ),
          'on' => array(
            'focus' => 'function(){ $(this).select(); }',
          ),
        ),
        'suffix' => array(
          'label' => '<i class="fa fa-font"></i> ' . $tString['fieldSuffixLabel'],
          'control_type' => 'text',
          'attributes' => array(
            // 'class' => '',
            'placeholder' => 'Placeholder',
            // 'pattern' => '', // regex for validation
          ),
          'on' => array(
            'focus' => 'function(){ $(this).select(); }',
          ),
        ),

      ),
    ),
    // Form settings
    'success_message_text' => array(
      'label' => '<i class="fa fa-font"></i> ' . $tString['successMessageTextLabel'],
      'control_type' => 'ck_editor',
      'attributes' => array(
        // 'class' => '',
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'unexpected_error_text' => array(
      'label' => '<i class="fa fa-font"></i> ' . $tString['unexpectedErrorTextLabel'],
      'control_type' => 'ck_editor',
      'attributes' => array(
        // 'class' => '',
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'button_text' => array(
      'label' => '<i class="fa fa-cc-stripe"></i> ' . $tString['submitButtonTextLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        'placeholder' => 'Click me!',
        // 'pattern' => '', // regex for validation
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'button_send_text' => array(
      'label' => '<i class="fa fa-cc-stripe"></i> ' . $tString['submitButtonSendTextLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        'placeholder' => 'Button text while sending',
        // 'pattern' => '', // regex for validation
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'validation_inactive_icon' => array(
      'label' => '<i class="fa fa-css3"></i> ' . $tString['validationInactiveIconLabel'],
      'control_type' => 'iconpicker',
      'attributes' => array(
        // 'class' => '',
        // 'placeholder' => 'Click me!',
        // 'pattern' => '', // regex for validation
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'validation_passed_icon' => array(
      'label' => '<i class="fa fa-css3"></i> ' . $tString['validationPassedIconLabel'],
      'control_type' => 'iconpicker',
      'attributes' => array(
        // 'class' => '',
        // 'placeholder' => '',
        // 'pattern' => '', // regex for validation
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'html_mails' => array(
      'label' => $tString['htmlMailsLabel'],
      'control_type' => 'checkbox',
      'attributes' => array(),
      'on' => array(),
    ),
    'from_address' => array(
      'label' => '<i class="fa fa-envelope"></i> ' . $tString['mailFromAddressLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        // 'pattern' => '', // regex for validation
        'placeholder' => $config['from_address'],
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'from_name' => array(
      'label' => '<i class="fa fa-envelope"></i> ' . $tString['mailFromNameLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        // 'pattern' => '', // regex for validation
        'placeholder' => $config['from_name'],
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'toemail' => array(
      'label' => '<i class="fa fa-envelope"></i> ' . $tString['mailToEmailLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        // 'pattern' => '', // regex for validation
        'placeholder' => $config['toemail'],
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'toname' => array(
      'label' => '<i class="fa fa-envelope"></i> ' . $tString['mailToNameLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        // 'pattern' => '', // regex for validation
        'placeholder' => $config['toname'],
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'admin_mail_subject' => array(
      'label' => '<i class="fa fa-envelope"></i> ' . $tString['adminMailSubjectLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        // 'pattern' => '', // regex for validation
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'admin_mail_header_text' => array(
      'label' => '<i class="fa fa-font"></i> ' . $tString['adminMailHeaderTextLabel'],
      'control_type' => 'ck_editor',
      'attributes' => array(
        // 'class' => '',
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'admin_mail_footer_text' => array(
      'label' => '<i class="fa fa-font"></i> ' . $tString['adminMailFooterTextLabel'],
      'control_type' => 'ck_editor',
      'attributes' => array(
        // 'class' => '',
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'send_copy_to_submitter' => array(
      'label' => $tString['sendCopyToSubmitterLabel'],
      'control_type' => 'checkbox',
      'attributes' => array(),
      'on' => array(
        'change' => 'function(){
          if($(this).prop("checked")){
            $("#editor-ctl-mail_subject").parents(".editor-ctl-box:first").show();
            $("#editor-ctl-mail_header_text").parents(".editor-ctl-box:first").show();
            $("#editor-ctl-mail_footer_text").parents(".editor-ctl-box:first").show();
          } else {
            $("#editor-ctl-mail_subject").parents(".editor-ctl-box:first").hide();
            $("#editor-ctl-mail_header_text").parents(".editor-ctl-box:first").hide();
            $("#editor-ctl-mail_footer_text").parents(".editor-ctl-box:first").hide();
          }
        }',
      ),
    ),
    'mail_subject' => array(
      'label' => '<i class="fa fa-envelope"></i> ' . $tString['mailSubjectLabel'],
      'control_type' => 'text',
      'attributes' => array(
        // 'class' => '',
        // 'pattern' => '', // regex for validation
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'mail_header_text' => array(
      'label' => '<i class="fa fa-font"></i> ' . $tString['mailHeaderTextLabel'],
      'control_type' => 'ck_editor',
      'attributes' => array(
        // 'class' => '',
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),
    'mail_footer_text' => array(
      'label' => '<i class="fa fa-font"></i> ' . $tString['mailFooterTextLabel'],
      'control_type' => 'ck_editor',
      'attributes' => array(
        // 'class' => '',
      ),
      'on' => array(
        'focus' => 'function(){ $(this).select(); }',
      ),
    ),

    // Form ID - we need this to get the section out of the page data file (inside the ContactformSectionSendHandler)
    // Actually we just use the php timestamp of the time the form is created / last edited.
    // So we are able to use multiple form sections on one page.
    // Form settings
    'form_id' => array(
      'label' => '<strong>#</strong> ' . $tstring['formIdLabel'],
      'control_type' => 'text',
      'attributes' => array(
        'readonly' => 'readonly',
        'style' => 'opacity:0.7;'
      )
    ),
    'attributes' => array(),
  ),

  );
