<?php

defined('is_running') or die('Not an entry point...');

// --------------- START ------------------------
global $addonPathData, $addonPathCode, $config;

// Load common variables, functions and initialize translation:
require($addonPathCode.'/_types/contact_form/common.php');

// Check if a form (and a right form) was submitted
if(empty($_POST) || empty($_POST['pageTitle']) || empty($_POST['formId'])) {
  errorResponse($tString['handlerBlockError']);
}

// Check global configuration is set:
if(! isset($config)) {
  errorResponse('The global configuration could not be loaded.');
}

// Retrieve section configuration:
$page_title = $_POST['pageTitle'];
$form_id = $_POST['formId'];
try {
  $section_config = GetSectionContactFormConfiguration($page_title, $form_id);
  if(empty($section_config)){
    throw new Exception('No section config returned.');
  }
} catch (Exception $e){
  errorResponse('Error: Contact section configuration could not be loaded: ' . $e->getMessage());
}

// ----------------- CONFIGURATION: ----------------------------
$fromMail =  !empty($section_config['from_address']) ? filter_var($section_config['from_address'], FILTER_SANITIZE_EMAIL) : filter_var($config['from_address'], FILTER_SANITIZE_EMAIL);  // Use global default if not overridden.
$fromName = !empty($section_config['from_name']) ? filter_var($section_config['from_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : filter_var($config['from_name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Use global default if not overridden.
$toMail = !empty($section_config['toemail']) ? filter_var($section_config['toemail'], FILTER_SANITIZE_EMAIL) : filter_var($config['toemail'], FILTER_SANITIZE_EMAIL);  // Use global default if not overridden.
$toName = !empty($section_config['toname']) ? filter_var($section_config['toname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS) : filter_var($config['toname'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);  // Use global default if not overridden.
$recaptchaSecretKey = $config['recaptcha_private'];
$captcha_url = 'https://www.google.com/recaptcha/api/siteverify';

header('Content-type: application/json');
//do Captcha check, make sure the submitter is not a robot:)...
//$captcha_header = 'Content-type: application/x-www-form-urlencoded';
$captcha_post_data = http_build_query(array('secret' => $recaptchaSecretKey, 'response' => filter_var($_POST["g-recaptcha-response"], FILTER_SANITIZE_FULL_SPECIAL_CHARS)));
// prefer cURL over #file_get_contents...
if (function_exists('curl_init')) {
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $captcha_url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array($captcha_header));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $captcha_post_data);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $result = json_decode(curl_exec($ch));
  curl_close($ch);
} else {
  $opts = array('http' =>
    array(
      'method'  => 'POST',
      'header'  => $captcha_header,
      'content' => $captcha_post_data
    )
  );
  $context  = stream_context_create($opts);
  $result = json_decode(file_get_contents($captcha_url, false, $context, -1, 40000));
}
if (!$result->success) {
  errorResponse($tString['mailReCaptchaFailed'] . join(', ', $result->{"error-codes"}));
}

includeFile('tool/email_mailer.php');

// Send Admin Mail
$messageBody = constructMessageBody($section_config['admin_mail_header_text'], $section_config['admin_mail_footer_text'], !empty($section_config['html_mails']));
// $mail = new PHPMailer;
$mail = new \gp_phpmailer();
$mail->SetLanguage($language);
$mail->CharSet = 'UTF-8';
// TODO #JPTF: Feature request: add tls option to global TS configuration for SMTP
// $mail->SMTPSecure = 'tls';
$mail->SetFrom($fromMail, $fromName);
$mail->AddAddress($toMail, $toName);
$mail->Subject = $section_config['admin_mail_subject'];
$mail->Body  = $messageBody;
$mail->IsHTML(!empty($section_config['html_mails']));

// Try to send the (admin)message
if($mail->Send()) {
  echo json_encode(array('message' => $tString['successMessageTextValue']));
} else {
  errorResponse($section_config['unexpected_error_text']);
}

// Send mail copy to the submitter
if($section_config['send_copy_to_submitter'] == true && !empty($_POST['formSubmitterMailAddress'])){
  $messageCopyBody = constructMessageBody($section_config['mail_header_text'], $section_config['mail_footer_text'], !empty($section_config['html_mails']));

  // We delete the admin mail addresses
  $mail->ClearAddresses();
  $mail->AddAddress(filter_var(trim($_POST['formSubmitterMailAddress']), FILTER_SANITIZE_EMAIL));
  $mail->Subject = $section_config['mail_subject'];
  $mail->Body = $messageCopyBody;

  sleep(5); // Some providers impose restrictions on the number of messages that can be sent within a specific time span
  if($mail->Send()) {
    // HINT: Responding any message here, will cause an error ("Error: You may not have filled out all required fields (correctly)").
    //       Whatever, we currently dont need any response here.
  }
}
exit(0);

/**
* Sets error header and json error message response.
*
* @param  String $messsage error message of response
* @return void
*/
function errorResponse ($messsage) {
  header('HTTP/1.1 400 Bad Request');
  echo json_encode(array('message' => $messsage));
  exit(0);
}

/**
* Pulls posted values for all fields in $fields_req array.
* If a required field does not have a value, an error response is given.
*/

function constructMessageBody ($message_header_text, $message_footer_text, $html_mails) {
  // Define technical fields, which will no printed out
  $technicalFields = [
    "verified",
    "g-recaptcha-response",
    "formSubmitterMailAddress",
    "pageTitle",
    "formId"
  ];

    if(!empty($message_header_text)){
      $message_body = $message_header_text . "\n\n";
    }else{
      $message_body = "";
    }
    foreach ($_POST as $name => $required) {
      $postedValue = strip_tags($_POST[$name]);
      // if ($required && empty($postedValue)) {
      if (empty($postedValue) && (! in_array($name, $technicalFields))) {
        // errorResponse("$name is empty.");
        $message_body .= '<strong>' . ucfirst($name) . '</strong>' . ":  -\n";
      } else {
        // Print out none technical fields
        if(! in_array($name, $technicalFields)){
          // Print out fields - except technical fields
          $message_body .= '<strong>' . ucfirst($name) . '</strong>' .": ". $postedValue . "\n";
        }
      }
    }
    if(!empty($message_footer_text)){
      $message_body .= "\n\n" . $message_footer_text;
    }

  if($html_mails){
    // Linebreaks to html
    $message_body = nl2br($message_body);
  } else {
    $message_body = strip_tags($message_body);
  }

  return $message_body;
}

/**
 * Helper function to return the sections configuration.
 *
 * @param String $page_title The page title which this form belongs to. Is used in typesetter to identify a page.
 * @param String $form_id    [description]
 */
function GetSectionContactFormConfiguration($page_title, $form_id){
  // load section definition file by page title (the typesetter way)
  $section_data_file = \gp\tool\Files::PageFile($page_title);
  if(\gp\tool\Files::Exists($section_data_file,'file_sections')){
    // Page configuration file exists. Now get the file_sections configuration:
    $file_sections = \gp\tool\Files::Get($section_data_file, 'file_sections');
    // Get the file sections of type "contact_form". There may be more than one.
    $pageContactFormSectionKeys = array_keys(array_column($file_sections, type), 'contact_form');
    if(!empty($pageContactFormSectionKeys)){
      foreach($pageContactFormSectionKeys as $pageContactFormSectionKey){
        $pageContactFormSection = $file_sections[$pageContactFormSectionKey];
        // Now compare the form ID to find the matching form.
        if(!empty($pageContactFormSection['values']['form_id']) && $pageContactFormSection['values']['form_id'] == $form_id){
          return $pageContactFormSection['values'];
        }
      }
      // Could not find this form id on the page.
      throw new Exception('No contact_form sections found for this form id on this page.');
    } else {
      // No fitting section found
      throw new Exception('No contact_form sections found for this page.');
    }
  } else {
    // File does not exist
    throw new Exception('Page section configuration could not be found');
  }
}
