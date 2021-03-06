<?php
/**
 * Created by PhpStorm.
 * User: drupal8
 * Date: 24/05/18
 * Time: 9:41
 */

namespace Drupal\thomas_more_ice_cream;


use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DatabaseManager {

  protected $database;

  protected $datetime;

  protected $state;

  public function __construct(StateInterface $state, Connection $database, TimeInterface $datetime) {
    $this->database = $database;
    $this->datetime = $datetime;
    $this->state = $state;
  }

  public function addSmaakKeuze($smaak) {
    $this->database->insert('thomas_more_ice_cream_smaakKeuze')
      ->fields([
        'smaak' => $smaak,
        'time_clicked' => $this->datetime->getRequestTime(),
      ])
      ->execute();
    $ijsTeller = $this->state->get('thomas_more_ice_cream.ijsTeller');
    $ijsThreshold = $this->state->get('thomas_more_ice_cream.ijsjes_threshold');
    $ijsTeller++;
    if ($ijsTeller >= $ijsThreshold) {
      $this->resetIjsTeller();
      return TRUE;
    }
    else {
      $this->state->set('thomas_more_ice_cream.ijsTeller', $ijsTeller);
      return FALSE;
    }
  }

  public function addSmaak($smaak) {
    if ($smaak != NULL || $smaak != 0) {
      $this->database->insert('thomas_more_ice_cream_smaak')
        ->fields(['smaak' => $smaak])
        ->execute();
    }
  }

  public function deleteSmaak($smaak) {
    $this->database->delete('thomas_more_ice_cream_smaak')
      ->condition('smaak', $smaak)
      ->execute();
    $this->database->delete('thomas_more_ice_cream_smaakKeuze')
      ->condition('smaak', $smaak)
      ->execute();
  }

  public function getAllSmaken() {
    $query = $this->database->select('thomas_more_ice_cream_smaak', 's');;
    $query->addField('s', 'smaak');
    return $query->execute()->fetchCol();
  }

  public function getCountSmaak($smaak) {
    return (int) $this->database->select('thomas_more_ice_cream_smaakKeuze')
      ->condition('smaak', $smaak)
      ->countQuery()
      ->execute()
      ->fetchfield();
  }

  public function addToppingKeuze($toppings) {
    foreach ($toppings as $topping) {
      if ($topping != 0) {
        $this->database->insert('thomas_more_ice_cream_toppingKeuze')
          ->fields([
            'topping' => $topping,
            'time_clicked' => $this->datetime->getRequestTime(),
          ])
          ->execute();
      }
    }
    $wafelTeller = $this->state->get('thomas_more_ice_cream.wafelTeller');
    $wafelThreshold = $this->state->get('thomas_more_ice_cream.wafels_threshold');
    $wafelTeller++;
    if ($wafelTeller >= $wafelThreshold) {
      $this->resetWafelTeller();
      return TRUE;
    }
    else {
      $this->state->set('thomas_more_ice_cream.wafelTeller', $wafelTeller);
      return FALSE;
    }

  }

  public function addTopping($topping) {
    $this->database->insert('thomas_more_ice_cream_topping')
      ->fields(['topping' => $topping])
      ->execute();
  }

  public function deleteTopping($topping) {
    $this->database->delete('thomas_more_ice_cream_topping')
      ->condition('topping', $topping)
      ->execute();
    $this->database->delete('thomas_more_ice_cream_toppingKeuze')
      ->condition('topping', $topping)
      ->execute();
  }

  public function getAllToppings() {
    $query = $this->database->select('thomas_more_ice_cream_topping', 't');;
    $query->addField('t', 'topping');
    return $query->execute()->fetchCol();
  }

  public function getCountTopping($topping) {
    return (int) $this->database->select('thomas_more_ice_cream_toppingKeuze')
      ->condition('topping', $topping)
      ->countQuery()
      ->execute()
      ->fetchfield();
  }

  public function getToppingKeuze($topping) {
    return (int) $this->database->select('thomas_more_ice_cream_toppingKeuze')
      ->condition('topping', $topping)
      ->countQuery()
      ->execute()
      ->fetchfield();
  }


  public function resetIjsTeller() {
    $ijsTeller = 0;
    $this->state->set('thomas_more_ice_cream.ijsTeller', $ijsTeller);

  }

  public function resetWafelTeller() {
    $wafelTeller = 0;
    $this->state->set('thomas_more_ice_cream.wafelTeller', $wafelTeller);
  }


}