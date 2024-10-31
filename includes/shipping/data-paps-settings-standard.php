<?php

if (!defined('ABSPATH')) {
  exit();
}

/*
 * WooCommerce Order Statuses to be used in settings
 */
$delivery_submission_statuses = array_filter(
  wc_get_order_statuses(),
  function ($el) {
    if (in_array($el, ['wc-cancelled', 'wc-refunded', 'wc-failed'])) {
      return false;
    }

    return $el;
  },
  ARRAY_FILTER_USE_KEY
);

$delivery_cancellation_statuses = array_filter(
  wc_get_order_statuses(),
  function ($el) {
    if (in_array($el, ['wc-cancelled', 'wc-refunded', 'wc-failed'])) {
      return $el;
    }

    return false;
  },
  ARRAY_FILTER_USE_KEY
);

/**
 * Array of settings
 */
return array(
  'enabled' => array(
    'title' => __('Paps Shipping', 'paps-wc'),
    'type' => 'checkbox',
    'label' => __('Activé', 'paps-wc'),
    'default' => 'no'
  ),
  'paps_test' => array(
    'title' => __('Mode Test', 'paps-wc'),
    'label' => __('Activer le mode Test', 'paps-wc'),
    'type' => 'checkbox',
    'default' => 'no',
    'desc_tip' => true,
    'description' => __(
      'Activer le mode test pour voir si l\'envoi de courses à Paps se passe sans problème. Notez que la course sera créée mais la prise en charge ne sera pas effectuée',
      'paps-wc'
    )
  ),
  'api_key' => array(
    'title' => __('Token', 'paps-wc'),
    'type' => 'text',
    'description' => __(
      'Le Token vous a été envoyée dans l\'email de confirmation après avoir obtenue un compte MYPAPS ici https://myapp.papslogistics.com/register',
      'paps-wc'
    ),
    'default' => ''
  ),
  'pickup_address' => array(
    'title' => __('Adresse de Pickup', 'paps-wc'),
    'type' => 'text',
    'description' => __(
      'Adresse de votre entreprise où on effectuera les ramassages des colis à livrer.',
      'paps-wc'
    ),
    'default' => ''
  ),
  'task_type' => array(
    'title' => __(
      'Type de tache:',
      'paps-wc'
    ),
    'type' => 'select',
    'description' => __(
      'Quand la tache est mise dans cet état, la requête est envoyée immédiatement à Paps',
      'paps-wc'
    ),
    'default' => '',
    'options' => array(
      'PICKUP' => _x('Ramassage', 'paps-wc'),
      'FROM_STOCK' => _x('Stocker a PAPS', 'paps-wc'),
      'DROPOFF' => _x('Dépôt', 'paps-wc'),
    ),
    'desc_tip' => true
  ),
  'delivery_submission' => array(
    'title' => __(
      'Envoyer la requête à Paps quand la commande à l\'état suivant:',
      'paps-wc'
    ),
    'type' => 'select',
    'description' => __(
      'Quand la commande est mise dans cet état, la requête est envoyée immédiatement à Paps',
      'paps-wc'
    ),
    'default' => '',
    'options' => array(
      'pending' => _x('Payement en attente', 'paps-wc'),
      'processing' => _x('En cours', 'paps-wc'),
      'on-hold' => _x('En pause', 'paps-wc'),
      'completed' => _x('Terminé', 'paps-wc')
    ),
    'desc_tip' => true
  )
);
