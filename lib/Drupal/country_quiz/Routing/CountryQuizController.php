<?php

/**
 * @file
 * Contains \Drupal\country_quiz\Routing\CountryQuizController.
 */

namespace Drupal\country_quiz\Routing;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\ControllerInterface;
use Drupal\Core\Database\Connection;

/**
 * Returns responses for country_quiz module routes.
 */
class CountryQuizController implements ControllerInterface {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection;
   */
  protected $database;

  /**
   * Constructs a object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Returns the ranking list.
   */
  public function ranking() {
    $result = db_query('SELECT * FROM {country_quiz_results} ORDER BY percent DESC');
    $header = array(t('Rank'), t('Name'), t('Result'), t('Percent'));

    $rows = array();
    $rank = 0;
    $previous_percent = NULL;
    foreach ($result as $row) {
       if ($previous_percent != $row->percent) {
        $rank++;
      }
      $rows[] = array(
        $rank,
        check_plain($row->name),
        $row->result,
        $row->percent . '%',
      );

      $previous_percent = $row->percent;
    }

    return array(
      '#type' => 'table',
      '#rows' => $rows,
      '#header' => $header,
      '#empty' => t('No results yet'),
    );
  }
}