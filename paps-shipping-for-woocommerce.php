<?php
/*
    Plugin Name: Paps Shipping for WooCommerce
    Description: Paps Shipping & Delivery Tracking Integration for WooCommerce
    Version: 3.0.5
    Author: Kiamet MAVOUNGOU
    Author URI: www.papslogistics.com
*/

class WC_Paps
{
    /**
     * Class Instance
     *
     * @var null
     */
    private static $instance = null;

    /**
     * Plugin Settings
     *
     * @var
     */
    protected $settings;

    /**
     * Paps API Instance
     *
     * @var null
     */
    private $api = null;

    /**
     * WC_Logger instance
     *
     * @var null
     */
    private $logger = null;

    /**
     * WC_Paps constructor.
     */
    private function __construct()
    {
        $this->init();
        $this->hooks();
    }

    /**
     * Init function
     */
    public function init()
    {
        $this->settings = get_option('woocommerce_paps_settings');
    }

    /**
     * Hooks
     */
    private function hooks()
    {
        add_action('woocommerce_shipping_init', [
          $this,
          'paps_woocommerce_shipping_init'
        ]);
        add_filter('woocommerce_shipping_methods', [
        $this,
        'paps_woocommerce_shipping_methods_standard'
    ]);

        add_filter(
            'woocommerce_shipping_calculator_enable_postcode',
            '__return_false'
        );

        add_action('woocommerce_thankyou', [$this, 'handle_order_status_change']);
        add_action('woocommerce_order_status_changed', [
          $this,
          'handle_order_status_change'
        ]);

        add_filter('manage_edit-shop_order_columns', [
          $this,
          'add_paps_delivery_column'
        ]);

        add_action(
            'woocommerce_order_details_after_order_table',
            array($this, 'show_delivery_details_on_order'),
            20
        );


        add_action("woocommerce_order_before_calculate_taxes", array($this, 'custom_order_before_calculate_taxes'), 10, 2);
    }



    /**
     * Get singleton instance
     */
    public static function get()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

      public function calculTva2($nombre)
      {
          return $nombre * 0.18;
      }

      public function custom_order_before_calculate_taxes($args, $order)
      {
          $pickup_adress = $this->settings['pickup_address'];
          $dropoff_address = $order->shipping_address_1.','.$order->shipping_city.','.$order->shipping_country;
          $total_weight = 0;
		  $unit = get_option( 'woocommerce_weight_unit' );

          foreach ($order->get_items() as $item_id => $product_item) {
              $quantity = $product_item->get_quantity();
              $product = $product_item->get_product(); 
              $product_weight = $product->get_weight();
			  if($unit == 'g'){
				  $product_weight /= 1000;
			  }
              $total_weight += floatval($product_weight * $quantity);
          }

          $orderDetails = array(
          'origin' => $pickup_adress,
          'destination' => $dropoff_address,
          'weight' => $total_weight
      );

          $make_call = $this->callAPI('POST', 'https://paps-api.papslogistics.com/marketplace', json_encode($orderDetails));
          $response = json_decode($make_call, true);
          $resultat  = $response['data']['price'];
          $new_shipping = $this->calculTva2($resultat) + $resultat;
          foreach ($order->get_items('shipping') as $item_id => $item) {
              $item->set_total($new_shipping);

              $item->calculate_taxes();

              $item->save();
          }
      }

    /**
   * WC_Shipping_Paps
   */
    public function paps_woocommerce_shipping_init()
    {
        require_once 'includes/shipping/class-wc-shipping-paps-standard.php';
    }

    public function action_woocommerce_checkout_update_order_review($array, $int)
    {
        WC()->cart->calculate_shipping();
        return;
    }

    /**
     * Add Paps as a Shipping method
     *
     * @param $methods
     * @return array
     */
    public function paps_woocommerce_shipping_methods_standard($methods)
    {
        $methods['paps'] = 'WC_Shipping_Paps';
        return $methods;
    }

     public function callTASK($data)
     {
         $curl = curl_init();
         $acces_token = $this->settings['api_key'];
         curl_setopt_array($curl, array(
         CURLOPT_URL => 'https://paps-api.papslogistics.com/tasks',
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => '',
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 0,
         CURLOPT_FOLLOWLOCATION => true,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => 'POST',
         CURLOPT_POSTFIELDS => json_encode($data),
         CURLOPT_HTTPHEADER => array(
             'Authorization: Bearer '.$acces_token,
             'Content-Type: application/json'
         ),
         ));


         $response = curl_exec($curl);

         curl_close($curl);
         return $response;
     }

public function callTEST($data)
{
    $curl = curl_init();
    $acces_token = $this->settings['api_key'];
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.papslogistics.com/tasks',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$acces_token,
        'Content-Type: application/json'
    ),
    ));
    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

public function callAPI($method, $url, $data)
{
    $curl = curl_init();
    switch ($method) {
        case "POST":
            curl_setopt($curl, CURLOPT_POST, 1);
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            break;
        case "PUT":
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            if ($data) {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            break;
        default:
            if ($data) {
                $url = sprintf("%s?%s", $url, http_build_query($data));
            }
    }

    $acces_token = $this->settings['api_key'];
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      'Authorization: Bearer '.$acces_token,
      'Content-Type: application/json'
));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    // EXECUTE:
    $result = curl_exec($curl);
    if (!$result) {
        die("Connection Failure");
    }
    curl_close($curl);
    return $result;
}
    /**
   * Order Status Handle to created or delete Paps delivery
   *
   * @param $order_id
   */
    public function handle_order_status_change($order_id)
    {
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $time = date('H:i', gmdate('U'));
        $date = date('Y-m-d');
		 $unit = get_option( 'woocommerce_weight_unit' );

        if ($order->status == $this->settings['delivery_submission']) {
            $task_status = get_post_meta($order_id, 'paps_task_status', true);

            if (!$task_status) {
                $dropoff_address = $order->shipping_address_1.','.$order->shipping_city.','.$order->shipping_country;
                $note = $order->customer_note;
                $shipping_total = intval($order->get_shipping_total());
                $receiverData = [
                    "firstname"=> $order->shipping_first_name,
                    "lastname"=> $order->shipping_last_name,
                    "phoneNumber"=> $order->billing_phone,
                    "email"=> $order->shipping_email,
                    "entreprise"=> $order->shipping_company,
                    "address"=> $dropoff_address,
                    "specificationAddress"=> $note,
                ];

                $description = " ";
                foreach ($items as $item) {
                    $price_order = $order->total;
                    $product_object = $item->get_product();
                    $product_name = $product_object->name;
                    $product_weight= intval($product_object->weight);
					if($unit == 'g'){
						$product_weight = $product_weight/1000;
					}
                    $totalQuantity = $product_weight * $item["quantity"];
                    $description .= ' '.$item["quantity"].'(X)'. $product_name.' '.$item['subtotal'].$order->currency. ',';
                }
                $parcels_data =[
                 "packageSize"=> $this->get_package_size($totalQuantity),
                 "description"=>  $description,
                 "price"=> intval($price_order - $shipping_total),
                 "amountCollect"=> intval($price_order),
                ];
				
                if ($time< "11:00") {
                    $date = date('Y-m-d');
                    $time = "11:00";
                } elseif ($time > "15:00") {
                    $date = date('Y-m-d', strtotime("+1 day"));
                    $time = "11:00";
                } else {
                    $date = date('Y-m-d');
                    $time = "15:00";
                };
                $paramsPaps = [
                  "type"=> $this->settings['task_type'],
                  "datePickup"=> $date,
                  "timePickup"=> $time,
                  "vehicleType"=> "SCOOTER",
                  "address"=> $this->settings['pickup_address'],
                  "receiver" => $receiverData,
                  "parcels" => array($parcels_data),

                ];

                if ($this->settings['paps_test']) {
                    $make_call = $this->callTEST($paramsPaps);
                    $apiResult = json_decode($make_call, true);
                }
                $make_call = $this->callTASK($paramsPaps);
                $apiResult = json_decode($make_call, true);

                $delivery = $apiResult['data']['job'];


                wc_paps()->debug(
                    'Delivery submitted with this parameters: ' . $paramsPaps
                );

                wc_paps()->debug('Paps response: ' . $delivery);
            }
        }

        if ($order->status == $this->settings['delivery_cancellation']) {
            if ($apiResult["error"] === null) {
                echo '<div class="error">
            <p>' .
                  __(
                      'Une tâche déjà en cours ne peut pas être annulée, contactez support@paps-app.com pour toutes réclamations.',
                      'paps-wc'
                  ) .
                  '</p>
          </div>';
            }
        }
    }

    /**
     * @return null|Paps_API
     */
    public function api()
    {
        if (is_object($this->api)) {
            return $this->api;
        }

        $apiOption = [
          'api_key' => $this->settings['api_key']
        ];

        if ($this->settings['test'] == 'yes') {
            $apiOption['mode'] = "test";
        }

        $this->api = new Paps_API($apiOption);

        return $this->api;
    }

    public function get_package_size($weight)
    {
        $package_size = "S";
        if ($weight > 5 && $weight < 30) {
            $package_size = "M";
        } elseif ($weight >= 30 && $weight < 60) {
            $package_size = "L";
        } elseif ($weight >= 60 && $weight < 100) {
            $package_size = "XL";
        } elseif ($weight >= 100) {
            $package_size = "XXL";
        }
        return $package_size;
    }


    /**
     * Debug Function to log messages or shown on frontend
     *
     * @param $message
     * @param string $type
     */
    public function debug($message, $type = 'notice')
    {
        if ($this->settings['debug'] == 'yes' && !is_admin()) {
            wc_add_notice($message, $type);
        }

        if (!is_object($this->logger)) {
            $this->logger = new WC_Logger();
        }

        if ($this->settings['logging_enabled'] == 'yes') {
            $this->logger->add('paps', $message);
        }
    }

    /**
     * Show shipping information on order view
     *
     * @param $order
     */
    public function show_delivery_details_on_order($order)
    {
        $shipping_method = @array_shift($order->get_shipping_methods());
        $shipping_method_id = $shipping_method['method_id'];

        if (!($shipping_method_id == 'paps')) {
            /* ?> <?php echo '<pre>', print_r($shipping_method_id, 1), '</pre>'; ?> <?php */
            return;
        }
        $text_status =
          'La commande est bien transmise. La livraison ne devrait pas tarder à commencer.';
        # code...
        ?>

    <h2>Expédition</h2>

    <table class="shop_table paps_delivery">
      <tbody>
        <tr>
          <th>Livraison par:</th>
          <td><?php echo $shipping_method['name']; ?></td>
        </tr>
      </tbody>
    </table>

  <?php
    }

    /**
     * Add Paps Column on Backend
     *
     * @param $columns
     * @return mixed
     */
    public function add_paps_delivery_column($columns)
    {
        $columns['paps_delivery'] = 'Paps';
        return $columns;
    }
}

/**
 * @return null|WC_Paps
 */
function wc_paps()
{
    return WC_Paps::get();
}

/**
 * Load Libraries and load main class
 */
require_once 'vendor/autoload.php';
wc_paps();
