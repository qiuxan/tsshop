<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductSku;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use Auth;
use Illuminate\Support\Facades\DB;


/** All Paypal Details class **/
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use Redirect;
use Session;
use URL;
//use Config;

class PaymentController extends Controller{

    private $_api_context;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        /** PayPal api context **/


        $paypal_conf = config('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);

    }
    public function index()
    {

//        dd(config('paypal'));
        return view('paywithpaypal');

//        return view('welcome');
    }
    public function payWithpaypal(Request $request)
    {


       $accept_url = config('paypal');//返回地址

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();

        $item_1->setName('Item 1') /** item name **/
        ->setCurrency('AUD')
            ->setQuantity(1)
            ->setPrice($request->get('amount')); /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('AUD')
            ->setTotal($request->get('amount'));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
//        $redirect_urls->setReturnUrl(URL::to('status')) /** Specify return URL **/
//        ->setCancelUrl(URL::to('status'));

        $redirect_urls->setReturnUrl($accept_url . '?success=true')->setCancelUrl($accept_url . '/?success=false');

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {

            $payment->create($this->_api_context);

        } catch (\PayPal\Exception\PPConnectionException $ex) {

            if (\Config::get('app.debug')) {

                \Session::put('error', 'Connection timeout');
                return Redirect::to('/');

            } else {

                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::to('/');

            }

        }

        foreach ($payment->getLinks() as $link) {

            if ($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
                break;

            }

        }

        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());

        if (isset($redirect_url)) {

            /** redirect to paypal **/
            return Redirect::away($redirect_url);

        }

        Session::put('error', 'Unknown error occurred');
        return Redirect::to('/');

    }

    public function callback($id)
    {

        $success = trim($_GET['success']);

        if ($success == 'false' && !isset($_GET['paymentId']) && !isset($_GET['PayerID'])) {

            session()->flash('error', 'Cancel Payment');

        }

        $paymentId = trim($_GET['paymentId']);
        $PayerID = trim($_GET['PayerID']);

        if (!isset($success, $paymentId, $PayerID)) {
            session()->flash('error', 'Payment Failed');

        }

        if ((bool)$_GET['success'] === 'false') {
            session()->flash('error', 'Payment Failed');
            session()->flash('payment_id', $paymentId);
        }

        $payment = Payment::get($paymentId, $this->_api_context);

        $execute = new PaymentExecution();

        $execute->setPayerId($PayerID);

        try {
            $payment->execute($execute, $this->_api_context);
        } catch (Exception $e) {
            session()->flash('error', 'Payment Failed');
            session()->flash('payment_id', $paymentId);
            session()->flash('payer_id', $PayerID);
        }
        session()->flash('success', 'Payment Success');
        session()->flash('payment_id', $paymentId);
        session()->flash('payer_id', $PayerID);

        $order = Order::where('id', $id)->first();

        $order->update([
            'paid_at'        => Carbon::now(),
            'payment_method' => 'paypal',
            'payment_no'=>$paymentId
        ]);

        $order_items=Order::find($id)->items()->get();


        foreach ($order_items as $key => $value) {


            $product_id=ProductSku::where('id', $value['product_sku_id'])->first()->product_id;

            $product=Product::where('id',$product_id)->first();

            $product->update([
                'sold_count'=>$product->sold_count+$value['amount'],

            ]);

        }

        return redirect(route('orders.index'));


    }
    public function getPaymentStatus()
    {
        /** Get the payment ID before session clear **/
        $payment_id = Session::get('paypal_payment_id');

        /** clear the session payment ID **/
        Session::forget('paypal_payment_id');
        if (empty(Input::get('PayerID')) || empty(Input::get('token'))) {

            \Session::put('error', 'Payment failed');
            return Redirect::to('/');

        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId(Input::get('PayerID'));

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);

        if ($result->getState() == 'approved') {

            \Session::put('success', 'Payment success');
            return Redirect::to('/');

        }

        \Session::put('error', 'Payment failed');
        return Redirect::to('/');

    }


    public function getPay(Request $request)
    {
//        dd(env('APP_URL'));

//        dd(config('paypal')['accept_url']);


        $accept_url = config('paypal')['accept_url'];

//        dd($_ENV['APP_URL']);

//        return ($request);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();

        $item_1->setName('Item 1') /** item name **/
        ->setCurrency('AUD')
            ->setQuantity(1)
            ->setPrice($request->get('total_amount')); /** unit price **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

        $amount = new Amount();
        $amount->setCurrency('AUD')
            ->setTotal($request->get('total_amount'));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Your transaction description');

        $redirect_urls = new RedirectUrls();
//        $redirect_urls->setReturnUrl(URL::to('status')) /** Specify return URL **/
//        ->setCancelUrl(URL::to('status'));

        $redirect_urls->setReturnUrl($accept_url . '/'.$request->order_id.'?success=true')->setCancelUrl($accept_url . '/?success=false');

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        /** dd($payment->create($this->_api_context));exit; **/
        try {

            $payment->create($this->_api_context);

        } catch (\PayPal\Exception\PPConnectionException $ex) {

            if (\Config::get('app.debug')) {

                \Session::put('error', 'Connection timeout');
                return Redirect::to('/');

            } else {

                \Session::put('error', 'Some error occur, sorry for inconvenient');
                return Redirect::to('/');

            }

        }

        foreach ($payment->getLinks() as $link) {

            if ($link->getRel() == 'approval_url') {

                $redirect_url = $link->getHref();
                break;

            }

        }

        /** add payment ID to session **/
        Session::put('paypal_payment_id', $payment->getId());

        if (isset($redirect_url)) {

            /** redirect to paypal **/
            return Redirect::away($redirect_url);

        }

        Session::put('error', 'Unknown error occurred');
        return Redirect::to('/');



            return ($request);
//        return Auth::user(); //this user info.

    }

}