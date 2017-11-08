<?php
defined('is_running') or die('Not an entry point...');

/*
#############################################################################################
Section "Contact Form" for Typesetter CMS developer plugin - 'Custom Sections'
By: webks: websolutions kept simple
Based on Custom Sections by: J. Krausz and a2exfr
Date: 2017-02-21
Version 0.5b
#############################################################################################
*/

global $addonPathData, $addonPathCode, $config, $page;

// Load common variables, functions and initialize translation:
require($addonPathCode.'/_types/contact_form/common.php');

$section = array();

// Required: default values for new sections
// We merge $sectionCurrentValues right here, so we can use $section['values'] for conditional rendering down * in the 'content' key.
$section['values'] = array_merge(array(
  'form_items' => array(
    array(
      'required'            => '1', // control_type = checkbox
      'disabled'            => '0', // control_type = checkbox
      'field_type'          => 'input_type_text', // control_type = radio group
      'options'             => $tString['fieldOptionsValue'], // control_type = textarea
      'label'               => $tString['fieldLabelValue'], // control_type = text
      'machine_name'        => $tString['fieldMachineNameValue'],
      'placeholder'         => $tString['fieldPlaceholderValue'], // control_type = text
      'description_on_error'=> '0', // control_type = checkbox
      'description'         => $tString['fieldDescValue'], // control_type = text
      'prefix'              => $tString['fieldPrefixLabel'], // control_type = text
      'suffix'              => $tString['fieldSuffixLabel'], // control_type = text
    ),
  ),
  'success_message_text' 	 	=> $tString['successMessageTextValue'],
  'unexpected_error_text'	 	=> $tString['unexpectedErrorTextValue'],
  'button_text'     				=> $tString['submitButtonTextValue'], // control_type = text
  'button_send_text' 				=> $tString['submitButtonSendTextValue'], // control_type = text
  'validation_inactive_icon'=> 'fa fa-square-o', // control_type = text
  'validation_passed_icon'	=> 'fa fa-check-square-o', // control_type = text
  'html_mails'              => '1',
  'from_address'            => '',
  'from_name'               => '',
  'toemail'                 => '',
  'toname'                  => '',
  'admin_mail_subject'			=> $tString['adminMailSubjectValue'], // control_type = text
  'admin_mail_header_text'	=> $tString['adminMailHeaderTextValue'], // control_type = ck_editor
  'admin_mail_footer_text'	=> $tString['adminMailFooterTextValue'], // control_type = ck_editor
  'send_copy_to_submitter' 	=> '1', // control_type = checkbox
  'mail_subject'						=> $tString['mailSubjectValue'], // control_type = text
  'mail_header_text' 				=> $tString['mailHeaderTextValue'], // control_type = ck_editor
  'mail_footer_text' 				=> $tString['mailFooterTextValue'], // control_type = ck_editor
  'form_id'                 => 'form-id-' . time(),
), $sectionCurrentValues );


// Required: we should always include an attributes array, even when it's empty
$section['attributes'] = array(
  'class' => '',  // optional: 'filetype-shop_item' class will be added by the system
  // 'style' => '', // optional inline styles
);

// Required: Predefined section content
// use {{value key}} for simple value placeholders/replacements
// use $section['values']['xyz'], e.g. for conditional rendering * whole elements

// Define some markup
$section['content'] = '<form class="form contact-form" role="form" id="' . filter_var($section['values']['form_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '" data-toggle="validator" data-disable="false" method="post" action="CustomSectionContactFormSendHandler"';
$section['content'] .= ' data-validation-inactive-icon="' . filter_var($section['values']['validation_inactive_icon'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '"';
$section['content'] .= ' data-validation-passed-icon="' . filter_var($section['values']['validation_passed_icon'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '"';
$section['content'] .= '>';

// $section['content'] .= '<pre>'.print_r($section).'</pre>';
// $allTsVariables = get_defined_vars();
// $section['content'] .= '<h1>'.print().'</h1>';

foreach($section['values']['form_items'] as $form_item) {
  // Settings array for this field (reset on each loop)
  $settings = array(
    // Global settings:
    'validationIndicator' => '<span class="input-group-addon input-group-addon--validation"><i class="contact-form-validation-icon form-control-feedback fa ' . filter_var($section['values']['validation_inactive_icon'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '"></i></span>',
  );

  // Preprocess common values:
  $machine_name = filter_var($form_item['machine_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $placeholder  = filter_var(trim($form_item['placeholder']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $description  = filter_var(trim($form_item['description'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

  $settings['helpText'] = '';
  if (!empty($description)) {
    $settings['helpText'] = '<span class="help-block'.($form_item['description_on_error'] !== '1' ? ' help-block--permanent' : ' help-block--on-error hidden').'">' . $description . '</span>';
  }

  // Define Prefix and Suffix markup (Bootstrap input-group-addon's)
  if (!empty($form_item['prefix'])) {
    $settings['prefix'] = '<span class="input-group-addon input-group-addon--prefix">' . filter_var($form_item['prefix'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '</span>';
  }
  if (!empty($form_item['suffix'])) {
    $settings['suffix'] = '<span class="input-group-addon input-group-addon--suffix">' . filter_var($form_item['suffix'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '</span>';
  }

  // Get Subvalues (Select Options, Radio Buttons, Multiple Checkboxes ...)
  $settings['options'] = filter_var_array(explode("\n", $form_item['options']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  // Print form items
  $section['content'] .= '<div id="' . 'field-' . filter_var($machine_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '" class="form-group form-item form-item--' . filter_var($form_item['field_type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '' . ($form_item['required'] == '1' ? ' form-item--required' : '') . '">';

  // Label
  if(!empty($form_item['label'])){
    $section['content'] .= '<label class="control-label" for="' . filter_var($machine_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '">' . filter_var($form_item['label'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '</label>';
  }

  // =========================================================================
  // ================== Type: Text, Mail, Phone, ... =========================
  // =========================================================================

  // Define text fieldtype and similiar (basicly same markup)
  $textLikeFieldTypes = array(
    'input_type_text' => 'text',
    'input_type_date' => 'date',
    'input_type_time' => 'time',
    'input_type_datetime' => 'datetime',
    'input_type_datetime_local' => 'datetime-local',
    'input_type_email' => 'email',
    'input_type_phone' => 'tel',
    'input_type_url' => 'url',
    'input_type_number' => 'number',
  );

  if(array_key_exists($form_item['field_type'], $textLikeFieldTypes)){
    $section['content'] .= '<div class="input-group">';
    $section['content'] .= $settings['prefix'];
    $section['content'] .= '<input type="' . $textLikeFieldTypes[$form_item['field_type']] . '" id="' . $machine_name . '" name="' . $machine_name . '" class="form-control" ' . ($form_item['required'] == '1' ? ' required' : '') . ((!empty($placeholder)) ? ' placeholder="' . $placeholder . '"' : '') . (($form_item['disabled'] == '1') ? ' disabled' : '') . '>';
    $section['content'] .= $settings['suffix'];
    $section['content'] .= $settings['validationIndicator'];
    $section['content'] .= '</div>';
    $section['content'] .= $settings['helpText'];

  } elseif($form_item['field_type'] === 'input_type_textarea') {
    // =========================================================================
    // ======================== Type: Textarea =================================
    // =========================================================================
    $section['content'] .= '<div class="input-group">';
    $section['content'] .= $settings['prefix'];
    $section['content'] .= '<textarea id="' . $machine_name . '" name="' . $machine_name . '" class="form-control" ' . ($form_item['required'] == '1' ? ' required' : '') . (strlen($placeholder) > 0 ? " placeholder='" . $placeholder . "'" : "") . ($form_item['disabled'] == '1' ? " disabled" : "") . '></textarea>';
    $section['content'] .= $settings['suffix'];
    $section['content'] .= $settings['validationIndicator'];
    $section['content'] .= '</div>';
    $section['content'] .= $settings['helpText'];

  } elseif($form_item['field_type'] === 'input_type_select') {
    // =========================================================================
    // ======================== Type: Select ===================================
    // =========================================================================
    $section['content'] .= '<select id="' . $machine_name  . '" name="' . $machine_name . '" class="form-control" '. ($form_item['required'] == '1' ? ' required' : '') . ($form_item['disabled'] == '1' ? ' disabled' : '') . '>';
    if(!empty($settings['options']) && is_array($settings['options'])){
      foreach($settings['options'] as $option){
        $optionArr = explode("|", $option, 2);
        $optionKey = filter_var(trim($optionArr[0]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(count($optionArr >= 1)){
          $optionLabel = filter_var(trim($optionArr[1]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        } else {
          // Set label = key if not existing.
          $optionLabel = $optionKey;
        }
        $section['content'] .= '<option value="' . $optionKey . '">' . $optionLabel . '</option>';
      }
    }
    $section['content'] .= '</select>';
    $section['content'] .= $settings['helpText'];

  } elseif($form_item['field_type'] === 'input_type_radio') {
    // =========================================================================
    // ======================== Type: Radio ====================================
    // =========================================================================
    if(!empty($settings['options']) && is_array($settings['options'])){
      foreach($settings['options'] as $option){
        $optionArr = explode("|", $option, 2);
        $optionKey = filter_var(trim($optionArr[0]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(count($optionArr >= 1)){
          $optionLabel = filter_var(trim($optionArr[1]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        } else {
          // Set label = key if not existing.
          $optionLabel = $optionKey;
        }
        if(!empty($optionKey) && !empty($optionLabel)){
          $section['content'] .= '<div class="radio-group-option">';
          $section['content'] .= '<label><input type="radio" id="' . $machine_name . '-' . $optionKey.'" name="' . $machine_name . '" value="' . $optionLabel . '"' . ($form_item['required'] == '1' ? ' required' : '') . ($form_item['disabled'] == '1' ? " disabled" : "") . '><span class="radio-label">' . $optionLabel . '</span></label>';
          $section['content'] .= '</div>';
        } else {
          // Throw wrong notation error
          $section['content'] .= '<div class="alert alert-warning">Creating Radio Button / Group: ' . $machine_name . ' failed. Wrong key|label notation?</div>';
        }
      }
    }
    $section['content'] .= $settings['helpText'];

  } elseif($form_item['field_type'] === 'input_type_checkbox') {
    // =========================================================================
    // ======================== Type: Checkbox =================================
    // =========================================================================
    if(!empty($settings['options']) && is_array($settings['options'])){
      foreach($settings['options'] as $option){
        $optionArr = explode("|", $option, 2);
        $optionKey = filter_var(trim($optionArr[0]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if(count($optionArr >= 1)){
          $optionLabel = filter_var(trim($optionArr[1]), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        } else {
          // Set label = key if not existing.
          $optionLabel = $optionKey;
        }

        $section['content'] .= '<div class="checkbox-group-option checkbox">';
        $section['content'] .= '<label><input type="checkbox" id="' . $machine_name . '-' . $optionKey . '" name="' . $machine_name . '-' . $optionKey . '" value="' . $optionKey . '"'. ($form_item['required'] == '1' ? ' required' : '') . ($form_item['disabled'] == '1' ? " disabled" : "") . '><span class="radio-label">' . $optionLabel . '</span></label>';
        $section['content'] .= '</div>';
      }
    }
    $section['content'] .= $settings['helpText'];
  }
  $section['content'] .= '</div>'; // .form-item
}

// Recaptcha
$recaptchaSiteKey = $config['recaptcha_public'];

$section['content'] .= '<div class="form-group">';
$section['content'] .= '<div  id="gcaptcha-' . filter_var($section['values']['form_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '" class="g-recaptcha" data-sitekey="' . filter_var($recaptchaSiteKey, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '"></div>';
// TODO @TF: Text übersetzbar machen!
$section['content'] .= '<span class="help-block" style="display: none;">Bitte bestätigen Sie, dass Sie ein Mensch sind.</span>';
$section['content'] .= '</div>';

// Submit button
$section['content'] .= '<button type="submit" id="feedbackSubmit-' . filter_var($section['values']['form_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '" class="feedbackSubmit btn btn-primary btn-lg" data-loading-text="' . filter_var($section['values']['button_send_text'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '" style="display: block; margin-top: 10px;">{{button_text}}</button>';

// Hidden Fields
$section['content'] .= '<input type="hidden" name="formId" value="' . filter_var($section['values']['form_id'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '">';
$section['content'] .= '<input type="hidden" name="pageTitle" value="' . filter_var($page->title, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . '">';
$section['content'] .= '<input type="hidden" id="formSubmitterMailAddress" name="formSubmitterMailAddress" value="[JS-PLACEHOLDER]">';

$section['content'] .= ' </form>';

/* ###################### */
/* ### end of content ### */
/* ###################### */

// Recommended: Section Label. If not defined, it will be generated from the folder name.
$section['gp_label'] = 'Contact Form';

// Optional: Always process values - if set to true, content will always be generated by processing values, even when not logged-in.
// Using this option, sections may have dynamic content.
$section['always_process_values'] = true;

// Optional: Admin UI color label. This is solely used in the editor's section manager ('Page' mode)
$section['gp_color'] = '#2e90d0';

// Optional: Loadable Components, see https://github.com/Typesetter/Typesetter/blob/master/include/tool/Output/Combine.php#L111
$components = 'fontawesome'; // comma separated string. If 'colorbox' is included \gp\tool::AddColorBox() will be called

// Ootional: Additional CSS and JS if needed
$css_files = array( 'style.css');

// $style = 'body { background:red!important; }';

$js_files = array('script.js', 'library/assets/js/contact-form.js');

$javascript = '$.getScript(\'https://www.google.com/recaptcha/api.js?onload=recaptchaCallback&render=explicit&hl=de\');'; // this will add a global js variable
$javascript .= "// Make multiple recaptchas possible on the same page:
  var recaptchaCallback = function(){
      $('.g-recaptcha').each(function(){
        grecaptcha.render(this,{'sitekey' : '" . filter_var($recaptchaSiteKey, FILTER_SANITIZE_FULL_SPECIAL_CHARS) . "'});
      })
  };";

// $jQueryCode = '$(".hello").on("click", function(){ alert("Click: " + hello_world); });';

// echo '<pre>';
// print_r($sectionCurrentValues);
// echo '</pre>';

/* ############################################################## */
/* ## EXAMPLES for JS to be executed when a section is updated ## */
/* ############################################################## */

/* Example for CurrenSections.onUpdate() function. 'this' refers to the section's jQuery object in the functions context: */
// $javascript = 'CustomSections = { onUpdate : function(){ console.log("CustomSections.onUpdate function called for ", $(this));} };';

/* Example for using the delegated CustomSection:updated event */
// $jQueryCode = '$(document).on("CustomSection:updated", "div.GPAREA", function(){ console.log("The event \"CustomSection:updated\" was triggered on section ", $(this)); });';
