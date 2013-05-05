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

    drupal_set_title(t('Capital quiz'));

    if (empty($_SESSION['country_quiz']['name'])) {
      $_SESSION['country_quiz']['name'] = '';
      $_SESSION['country_quiz']['correct'] = 0;
      $_SESSION['country_quiz']['wrong'] = 0;

      $form['name'] = array(
        '#type' => 'textfield',
        '#title' => t('Your name'),
        '#maxlength' => 20,
        '#required' => TRUE,
        '#description' => t('Hello, please provide your name.'),
      );
    }
    else {
      $args = array(
        '%name' => $_SESSION['country_quiz']['name'],
        '%correct' => $_SESSION['country_quiz']['correct'],
        '%total' => $_SESSION['country_quiz']['correct'] + $_SESSION['country_quiz']['wrong'],
      );
      $args['%percent'] = $args['%total'] ? round(100 / $args['%total'] * $args['%correct'], 0) . '%' : '100%';
      $form['quiz_name'] = array(
        '#type' => 'item',
        '#markup' => t('Hello %name, you have answered %correct out of %total (%percent) questions correctly.', $args),
      );
    }

    if (empty($_SESSION['country_quiz']['country'])) {
      $countries = json_decode(file_get_contents(drupal_get_path('module', 'country_quiz') . '/country-capital.json'));
      $_SESSION['country_quiz']['country'] = $countries[array_rand($countries)];
    }

    $form['question'] = array(
      '#type' => 'item',
      '#description' => '<h3>' . t('Name the capital of %country.', array('%country' => $_SESSION['country_quiz']['country']->country)) . '</h3>',
    );
    $form['answer'] = array(
      '#type' => 'textfield',
      '#title' => t('Answer'),
      '#maxlength' => 255,
      '#required' => TRUE,
      '#description' => t('Enter the name of the capital.'),
      '#attributes' => array('autofocus' => 'autofocus'),
    );
    $form['actions'] = array('#type' => 'actions');
    $form['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => t('Answer'),
    );
    $form['actions']['restart'] = array(
      '#type' => 'submit',
      '#value' => t('New game'),
      '#limit_validation_errors' => array(),
      '#submit' => array(array($this, 'restartSubmit')),
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
    if (isset($form_state['values']['name'])) {
      $_SESSION['country_quiz']['name'] = $form_state['values']['name'];
    }

    if (drupal_strtolower($form_state['values']['answer']) == drupal_strtolower($_SESSION['country_quiz']['country']->capital)) {
      $_SESSION['country_quiz']['correct']++;
      drupal_set_message(t('Correct answer!'));
    }
    else {
      $_SESSION['country_quiz']['wrong']++;
      drupal_set_message(t('Wrong answer, the capital of %country is %capital.', array('%country' => $_SESSION['country_quiz']['country']->country, '%capital' => $_SESSION['country_quiz']['country']->capital)), 'error');
    }
    $form_state['redirect'] = 'country_quiz';
    unset($_SESSION['country_quiz']['country']);
  }

  /**
   * Form callback to restart the form.
   */
  public function restartSubmit(array &$form, array &$form_state) {
    unset($_SESSION['country_quiz']);
    $form_state['redirect'] = 'country_quiz';
  }

}
