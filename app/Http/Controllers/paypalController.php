<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use PayPal\Api\Payer;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Details;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;




class paypalController extends Controller
{



    //

//    public $clientId = $_ENV[''];//ID

//
//    public $clientSecret =CS;//秘钥



    const accept_url = 'http://tsshop.test/callback';//返回地址
    const Currency = 'AUD';//币种
    protected $PayPal;

    public function __construct()
    {

        $this->PayPal = new ApiContext(
            new OAuthTokenCredential(
                $_ENV['PAYPALCID'],
                $_ENV['PAYPALCSECRET']
            )
        );
        //如果是沙盒测试环境不设置，请注释掉
//        $this->PayPal->setConfig(
//            array(
//                'mode' => 'live',
//            )
//        );
    }


    public function pay()
    {
        dd('h');
        $product = '1123';
        $price = 100;
        $shipping = 0;
        $description = '1123123';
        $paypal = $this->PayPal;
        $total = $price + $shipping;//总价

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item = new Item();
        $item->setName($product)->setCurrency(self::Currency)->setQuantity(1)->setPrice($price);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $details = new Details();
        $details->setShipping($shipping)->setSubtotal($price);

        $amount = new Amount();
        $amount->setCurrency(self::Currency)->setTotal($total)->setDetails($details);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription($description)->setInvoiceNumber(uniqid());

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(self::accept_url . '?success=true')->setCancelUrl(self::accept_url . '/?success=false');

        $payment = new Payment();
        $payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions([$transaction]);

        try {
            $payment->create($paypal);
        } catch (PayPalConnectionException $e) {
            echo $e->getData();
            die();
        }

        $approvalUrl = $payment->getApprovalLink();
        header("Location: {$approvalUrl}");
    }


    public function Callback()
    {
        $success = trim($_GET['success']);

//        dd($_GET);

        if ($success == 'false' && !isset($_GET['paymentId']) && !isset($_GET['PayerID'])) {
            echo 'Payment Canceled';die;
        }

        $paymentId = trim($_GET['paymentId']);
        $PayerID = trim($_GET['PayerID']);

        if (!isset($success, $paymentId, $PayerID)) {
            echo 'Payment Fail';die;
        }

        if ((bool)$_GET['success'] === 'false') {
            echo  'Payment Fail，Payment ID【' . $paymentId . '】,Payer ID【' . $PayerID . '】';die;
        }

        $payment = Payment::get($paymentId, $this->PayPal);

        $execute = new PaymentExecution();

        $execute->setPayerId($PayerID);

        try {
            $payment->execute($execute, $this->PayPal);
        } catch (Exception $e) {
            echo ',Payment Fail，Payment ID【' . $paymentId . '】,Payer ID【' . $PayerID . '】';die;
        }
        echo 'Payment Success，Payment ID【' . $paymentId . '】,Payer ID【' . $PayerID . '】';die;
    }

    public function index(){

        return view('welcomesave');
    }
}
