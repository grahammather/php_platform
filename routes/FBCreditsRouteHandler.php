<?php
/*
 * Application side sample code
 *
 * params an app gets: order_id,
 *                     status,
 *                     method,
 *                     order details as a json_encoded array,
 *                     and optional extra params
 *
 * params an app should return: a json encoded array with
 *                              error_code if there is error,
 *                              order_id,
 *                              next_state,
 *                              comments if any
 *
 */

class FBCreditsRouteHandler {

    function abort($errorMsg = "generic") {
        error_log("aborting payments.  error: $errorMsg");
        header('HTTP/1.x 404 Not Found');
        exit ;
    }

    public function display() {

        try {

            // create a transaction for the whole operation
            R::begin();

            // prepare the return data array
            $data = array();
            $data['content'] = array();

            // validating fb_sig
            if (!App::parse_signed_request($_REQUEST['signed_request'])) {
                error_log('invalid signed request: ' . print_r($_REQUEST['signed_request'], true));
                $this->abort();
            }

            // retrieve all params passed in
            $func = $_POST['method'];
            $order_id = $_POST['order_id'];

            if ($func == 'payments_status_update') {
                $status = $_POST['status'];
                // write your logic here, determine the state you wanna move to

                // dispute callback:
                // status = "disputed"

                // callback #2
                if ($status == 'placed') {
                    $next_state = 'settled';
                    $data['content']['status'] = $next_state;

                } else if ($status == 'settled') {

                    // callback #3
                    // if status is 'settled' then actually give the virtual good

                    // remove escape characters
                    $order_details = stripcslashes($_POST['order_details']);

                    $order = json_decode($order_details, true);

                    // data in the order is bigints
                    $app = sprintf("%.0f", $order['app']);
                    $order_id = sprintf("%.0f", $order['order_id']);
                    $buyer = sprintf("%.0f", $order['buyer']);
                    $receiver = sprintf("%.0f", $order['receiver']);

                    $items = $order['items'];

                    // sanity check the order
                    if ($app != Cfg::instance()->fb_app_id) {
                        error_log('invalid order - wrong app id: order app = ' . $app . ' and this app = ' . Cfg::instance()->fb_app_id);
                        $this->abort();
                    }

                    //keep track of aggregated revenue
                    $revenue_amount_in_cents = 0;
                    // grant each item they've purchased
                    foreach ($items as $item) {

                        $item_id = $item['item_id'];

                        // find the purchased item from the store
                        $sc = new StoreController();
                        $purchased_item = $sc->getDatum($item_id);

                        if (!$purchased_item) {
                            $this->abort("no such item: $item_id");
                        }

                        $chips_granted = $purchased_item->chips_amount;
                        $uc = new UsersController();
                        $user = $uc->getCurrentUser();

                        error_log("GRANTING USER " . $user->user_id . " $chips_granted CHIPS");

                        if (!$user->chips)
                            $user->chips = 0;

                        $user->chips += $chips_granted;

                        // store it
                        $uc->saveUser($user);

                        // TODO: tell the app about the purchase

                        // TODO: record the transaction

                        $log_order = array();

                        $log_order['fb_order_id'] = $order_id;
                        $log_order['fbcredit'] = $order['amount'];
                        $log_order['user_id'] = $user->user_id;
                        $log_order['chips'] = $chips_granted;
                        $log_order['time'] = $order['update_time'];

                        try {
                            // store the order log
                        } catch (Exception $e) {
                            error_log('failed to store order ' . $order_id . ' for user ' . $user->user_id);
                            $this->abort();
                        }

                        //each credit is 10 cents
                        $revenue_amount_in_cents += $order['amount'] * 10;
                    }

                    // TODO: register the purchase with analytics
                }

                // compose returning data array
                $data['content']['order_id'] = $order_id;

            } else if ($func == 'payments_get_items') {

                error_log("payments_get_items");

                // first callback

                // remove escape characters
                $order_info = stripcslashes($_POST['order_info']);
                $item = json_decode($order_info, true);
                $item_id = $item['item_id'];

                // find the purchased item from the store
                $sc = new StoreController();
                $purchased_item = $sc->getDatum($item_id);

                if (!$purchased_item) {
                    $this->abort("no such item: $item_id");
                }

                $item['title'] = $purchased_item->description;
                $item['description'] = $purchased_item->description;
                $item['price'] = $purchased_item->credits_price;

                // prefix test-mode
                if ($_POST['test_mode']) {
                    $update_keys = array('title', 'description');
                    foreach ($update_keys as $key) {
                        $item[$key] = '[Test Mode] ' . $item[$key];
                    }
                }

                $data['content'] = array($item);
            }

            //required by api_fetch_response()
            $data['method'] = $func;

            R::commit();

            // send data back
            echo json_encode($data);

        } catch(Exception $e) {
            error_log($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
            R::rollback();
        }

    }

}
