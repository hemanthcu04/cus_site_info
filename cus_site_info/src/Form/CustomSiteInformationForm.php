<?php
/**
 * Created by PhpStorm.
 * User: 212542639
 * Date: 09-08-2019
 * Time: 09:11
 */

namespace Drupal\cus_site_info\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\system\Form\SiteInformationForm;

/*
 * Custom Site Information form to extend the default Site Information form
 */

class CustomSiteInformationForm extends SiteInformationForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Retrieve the system.site configuration
    $site_config = $this->config('system.site');

    $form = parent::buildForm($form, $form_state);

    $site_api_key = $site_config->get('siteapikey');

    // Add a textfield to the site information section to store site api key
    $form['site_information']['site_api_key'] = [
      '#type' => 'textfield',
      '#title' => t("Site API Key"),
      '#default_value' => isset($site_api_key) ? $site_config->get('siteapikey') : "No API Key yet",
    ];

    // Code to update the submit button text.
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Update configuration'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    //Write logic to save the siteapikey to system.site config.
    $this->config('system.site')
      ->set('siteapikey', $form_state->getValue('site_api_key'))
      ->save();
    //Setting the drupal message
    drupal_set_message("Site API Key has been saved");
    parent::submitForm($form, $form_state);
  }
}