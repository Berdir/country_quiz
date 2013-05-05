<?php

/**
 * @file
 * Contains \Drupal\country_quiz\Form\CapitalQuizForm.
 */

namespace Drupal\country_quiz\Form;
use Drupal\Core\ControllerInterface;
use Drupal\Core\Form\FormInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 *
 */
class CapitalQuizForm implements ControllerInterface, FormInterface {

  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'country_quiz_capital';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, array &$form_state) {

    if (empty($form_state['country'])) {
      $countries = json_decode(file_get_contents(drupal_get_path('module', 'country_quiz') . '/country-capital.json'));
      dpm($countries);
    }

    $form['question'] = array(
      '#type' => 'item',
      '#title' => t('Question'),
      '#description' => t('Upload an OPML file containing a list of feeds to be imported.'),
    );
    $form['remote'] = array(
      '#type' => 'url',
      '#title' => t('OPML Remote URL'),
      '#maxlength' => 1024,
      '#description' => t('Enter the URL of an OPML file. This file will be downloaded and processed only once on submission of the form.'),
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Import'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, array &$form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, array &$form_state) {

  }

}
