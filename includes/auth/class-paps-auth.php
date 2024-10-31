<?php

if (!defined('ABSPATH')) {
  exit();
}

use Paps\PapsClient;
use Paps\PapsException;
use Paps\PapsWebhook;
use Paps\Resources\DeliveryRate;
use Paps\Resources\Delivery;

/**
 * Class Paps_API
 */
class Paps_AUTH
{
  /**
   * Paps Client
   *
   * @var PapsClient
   */
  private $client;

  /**
   * Paps_API constructor.
   * @param $config
   */
  public function __construct(array $config=[])
  {
    $this->client = new PapsClient($config);
  }

  public static function getDeliveryStatus($status, $type = 'user')
  {
    if ($type == 'admin') {
      switch ($status) {
        case 'UNASSIGNED':
          return __('Envoyée à Paps', 'paps-wc');
          break;
        case 'CANCELLED':
          return __('Livraison Annulée', 'paps-wc');
          break;
        case 'ASSIGNED':
          return __('Pickup commmencé', 'paps-wc');
          break;
        case 'DONE':
          return __('Pickup Terminé', 'paps-wc');
          break;
        case 'WAREHOUSE':
          return __('Livraison commencée', 'paps-wc');
          break;
        case 'DELIVERY':
          return __('Livrée', 'paps-wc');
          break;
        default:
          return __('En cours', 'paps-wc');
          break;
      }
    }

    if ($type == 'user') {
      switch ($status) {
        case 'UNASSIGNED':
          return __(
            'La commande a été reçue par Paps. La livraison ne tardera pas à démarrer.',
            'paps-wc'
          );
          break;
        case 'CANCELLED':
          return __(
            'La livraison a été annulée, contactez le propriétaire du site pour en savoir plus.',
            'paps-wc'
          );
          break;
        case 'ASSIGNED':
          return __(
            'Le coursier de Paps est en chemin pour aller recupérer le colis et vous le livrer.',
            'paps-wc'
          );
          break;
        case 'DONE':
          return __(
            'Le colis a été bien recupéré par Paps. La livraison va démarrer bientôt.',
            'paps-wc'
          );
          break;
        case 'WAREHOUSE':
          return __(
            'Le coursier est en route pour vous livrer le colis.',
            'paps-wc'
          );
          break;
        case 'DELIVERY':
          return __('Le colis a été livré.', 'paps-wc');
          break;
        default:
          return __('Le colis est en préparation.', 'paps-wc');
          break;
      }
    }
  }

  /**
   * Get quote functionality
   *
   * @param $quotes_params
   * @return mixed|WP_Error
   */
  public function marketplace(array $quotes_params = [])
  {
    $delivery_quote = new Delivery($this->client);

    try {
      return $delivery_quote->submitQuotesRequest($quotes_params);
    } catch (PapsException $e) {
      return new WP_Error('paps_error', $e->getMessage());
    }
  }

  /**
   * Submit a delivery to Paps API
   *
   * @param $params
   * @return mixed|WP_Error
   */
  public function tasks($params)
  {
    $delivery = new Delivery($this->client);

    try {
      return $delivery->tasks($params);
    } catch (PapsException $e) {
      return new WP_Error(
        'paps_error',
        $e->getMessage(),
        $e->getInvalidParams()
      );
    }
  }

  /**
   * view a delivery by ID
   *
   * @param $delivery_id
   * @return mixed|WP_Error
   */
  public function getDelivery($delivery_id)
  {
    $delivery = new Delivery($this->client);

    try {
      return $delivery->fetchSubtaskByClient($delivery_id);
    } catch (PapsException $e) {
      return new WP_Error(
        'paps_error',
        $e->getMessage(),
        $e->getInvalidParams()
      );
    }
  }

   /**
   * view a pickup by ID
   *
   * @param $pickup_id
   * @return mixed|WP_Error
   */
  public function getPicked($pickup_id)
  {
    $pickup_id = new Delivery($this->client);

    try {
      return $pickup_id->fetchTasksByClient($pickup_id);
    } catch (PapsException $e) {
      return new WP_Error(
        'paps_error',
        $e->getMessage(),
        $e->getInvalidParams()
      );
    }
  }

  /**
   * Cancel a delivery by ID
   *
   * @param $delivery_id
   * @return mixed|WP_Error
   */
  public function cancelDelivery($delivery_id)
  {
    $delivery = new Delivery($this->client);

    try {
      return $delivery->cancel($delivery_id);
    } catch (PapsException $e) {
      return new WP_Error(
        'paps_error',
        $e->getMessage(),
        $e->getInvalidParams()
      );
    }
  }

  /**
   * Format phone number as needed by Paps
   *
   * @param $number
   * @return string
   */
  public function formatPhoneNumber($number)
  {
    $number = str_replace('(', '', $number);
    $number = str_replace(')', '', $number);
    $number = str_replace('-', '', $number);
    $number = str_replace(' ', '', $number);
    $number = str_replace('.', '', $number);

    return substr($number, 0, 3) .
      "-" .
      substr($number, 3, 3) .
      "-" .
      substr($number, 6);
  }
}
