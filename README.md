
=======================

This is a modified version of https://github.com/jonmbake/bootstrap3-contact-form for Typesetter CMS.

Bootstrap 3 Contact Form with Google's reCaptcha

A simple bootstrap 3 contact form using [Google's reCAPTCHA](https://developers.google.com/recaptcha/).  Submitted messages are sent to a specified email address using SMTP with support for SSL or TLS transport.

## Version History

| Versions | Major Enhancement |
| -------- | ----------------- |
| 1.0      | Initial release |

## Dependencies

### PHP
* version > 5.2.0

### HTML/JS
* [Bootstrap 3](https://github.com/twbs/bootstrap) version >3.1
* jQuery

### Custom Sections

This is not an Typesetter CMS standalone plugin, it useses Custom Sections (by a2exfr, juergen). 
So you have to put our "type" inside the "types" folder in the Custom Sections Plugin. You also need to modify the Addon.ini a little, as we currently have no other possibility to add our form handler script.

Simply add the following snippet to the bottom of Custom Sections Addon.ini: 

```
; Register callback handler for contact forms JS:
[Special_Link:CustomSectionContactFormSendHandler]
label = 'Callback Handler'
script = '_types/contact_form/CustomSectionContactFormSendHandler.php'
```


## Setting up reCAPTCHA

You must obtain a [Site Key and Secret Key from Google](http://www.google.com/recaptcha/admin). Our script uses the keys defined in the Typesetter core contact form configuration.

**Note:** Many web servers now force `allow_url_fopen=0` and `allow_url_include=0` due to security concerns (see: [Issue 26](https://github.com/jonmbake/bootstrap3-contact-form/issues/26)). reCAPTCHA verifying will use [cURL](http://php.net/manual/en/book.curl.php) is if it is installed. If you are having issues verifying reCAPTCHA, most likely you need to install [cURL](http://php.net/manual/en/book.curl.php). 

## Configuration

There are a lot of configuration options, simply discover them by yourself, they should all be more or less self explaining. We maybe add some more informations to the configuration later.

## Translation

Feel free to create a translation file for your langugage. Instructions are inside the lanugage/en.php file.

## Credits

Written by https://www.webks.de based on https://github.com/jonmbake/bootstrap3-contact-form