<?php
/**
 * PaymentWall Purchase Request
 */

namespace Omnipay\PaymentWall\Message;

use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * PaymentWall Purchase Request
 *
 * Paymentwall is the leading digital payments platform for globally monetizing
 * digital goods and services. Paymentwall assists game publishers, dating publics,
 * rewards publics, SaaS companies and many other verticals to monetize their
 * digital content and services.
 *
 * This uses the PaymentWall library at https://github.com/paymentwall/paymentwall-php
 * and the Brick API to communicate to PaymentWall.
 *
 * <h3>Examples</h3>
 *
 * <h4>Set Up and Initialise Gateway</h4>
 *
 * <code>
 *   // Create a gateway for the PaymentWall REST Gateway
 *   // (routes to GatewayFactory::create)
 *   $gateway = Omnipay::create('PaymentWall');
 *
 *   // Initialise the gateway
 *   $gateway->initialize(array(
 *       'apiType'      => $gateway::API_GOODS,
 *       'publicKey'    => 'YOUR_PUBLIC_KEY',
 *       'privateKey'   => 'YOUR_PRIVATE_KEY',
 *   ));
 * </code>
 *
 * <h4>Payment with Card Details</h4>
 *
 * <code>
 *   // Create a credit card object
 *   // This card can be used for testing.
 *   $card = new CreditCard(array(
 *               'firstName'             => 'Example',
 *               'lastName'              => 'Customer',
 *               'number'                => '4242424242424242',
 *               'expiryMonth'           => '01',
 *               'expiryYear'            => '2020',
 *               'cvv'                   => '123',
 *               'email'                 => 'customer@example.com',
 *               'billingPostcode'       => '4999',
 *   ));
 *
 *   // Do a purchase transaction on the gateway
 *   $transaction = $gateway->purchase(array(
 *       'amount'                    => '10.00',
 *       'accountId'                 => 12341234,
 *       'currency'                  => 'AUD',
 *       'clientIp'                  => '127.0.0.1',
 *       'packageId'                 => 1234,
 *       'description'               => 'Super Deluxe Excellent Discount Package',
 *       'fingerprint'               => '*token provided by Brick.js*',
 *       'browserDomain'             => 'SiteName.com',
 *       'card'                      => $card,
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Purchase transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * <h4>Payment with Card Token</h4>
 *
 * <code>
 *   // Do a purchase transaction on the gateway
 *   $transaction = $gateway->purchase(array(
 *       'amount'                    => '10.00',
 *       'accountId'                 => 12341234,
 *       'currency'                  => 'AUD',
 *       'clientIp'                  => '127.0.0.1',
 *       'packageId'                 => 1234,
 *       'description'               => 'Super Deluxe Excellent Discount Package',
 *       'fingerprint'               => '*token provided by Brick.js*',
 *       'browserDomain'             => 'SiteName.com',
 *       'cardReference'             => 'token_asdf1234asdf1234',
 *       'email'                     => 'customer@example.com',
 *   ));
 *   $response = $transaction->send();
 *   if ($response->isSuccessful()) {
 *       echo "Purchase transaction was successful!\n";
 *       $sale_id = $response->getTransactionReference();
 *       echo "Transaction reference = " . $sale_id . "\n";
 *   }
 * </code>
 *
 * @link https://www.paymentwall.com/en/documentation/getting-started
 * @link https://www.paymentwall.com/
 * @link https://github.com/paymentwall/paymentwall-php
 * @see Omnipay\PaymentWall\Gateway
 */
class PurchaseRequest extends AbstractLibraryRequest
{

    /**
     * Get the request packageId
     *
     * @return string
     */
    public function getPackageId()
    {
        return $this->getParameter('packageId');
    }

    /**
     * Set the request packageId
     *
     * Optional parameter, plan
     *
     * Identifies the product ID, sent back as goodsid parameter in Pingbacks
     *
     * @param mixed $value
     *
     * @return PurchaseRequest provides a fluent interface.
     */
    public function setPackageId($value)
    {
        return $this->setParameter('packageId', $value);
    }

    /**
     * Get the request accountId
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->getParameter('accountId');
    }

    /**
     * Set the request accountId
     *
     * Optional parameter, uuid
     *
     * Identifies the internal end-user ID within merchant's system. Used for uid
     * parameter in Pingbacks. If omitted, email is used as uid parameter in Pingbacks
     *
     * @param mixed $value
     *
     * @return PurchaseRequest provides a fluent interface.
     */
    public function setAccountId($value)
    {
        return $this->setParameter('accountId', $value);
    }

    /**
     * Get the request email
     *
     * The email can be in the parameter bag or the card data
     *
     * @return string
     */
    public function getEmail()
    {
        $email = $this->getParameter('email');
        $card = $this->getCard();
        if (empty($email) && ! empty($card)) {
            $email = $this->getCard()->getEmail();
        }
        return $email;
    }

    /**
     * Set the request email
     *
     * Required parameter, email
     *
     * End-user's email
     *
     * PaymentWall will use this email to send the transaction receipt
     *
     * @param mixed $value
     *
     * @return PurchaseRequest provides a fluent interface.
     */
    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    /**
     * Get the Brick.js fingerprint
     *
     * @return string
     */
    public function getFingerprint()
    {
        return $this->getParameter('fingerprint');
    }

    /**
     * Set the request FingerPrint
     *
     * Required parameter fingerprint, if browserIp and browserDomain are not supplied
     *
     * This value is produced by the Brick.js (if utilized) and contains both the
     * IP and Domain info
     *
     * @param string $value
     *
     * @return PurchaseRequest provides a fluent interface.
     */
    public function setFingerprint($value)
    {
        return $this->setParameter('fingerprint', $value);
    }

    /**
     * Get browserDomain
     *
     * @return string Site name or URL
     */
    public function getBrowserDomain()
    {
        return $this->getParameter('browserDomain');
    }

    /**
     * Set the request browserDomain
     *
     * Required parameter browserDomain, if fingerprint is not supplied
     *
     * Domain of the website where the payment is originating from
     *
     * @param string $value Name or URL of the site making the payment
     *
     * @return PurchaseRequest provides a fluent interface.
     */
    public function setBrowserDomain($value)
    {
        return $this->setParameter('browserDomain', $value);
    }

    /**
     * Get the capture flag
     *
     * This will only return false if the parameter is set AND false. The default
     * value is true.
     *
     * @return bool
     */
    public function getCapture()
    {
        if (!$this->parameters->has('capture')) {
            return true;
        }
        return $this->getParameter('capture');
    }

    /**
     * Set the capture flag
     *
     * optional parameter capture. Setting this to false allows for card validation/
     * authorization. The call to charge returns a charge object that
     *
     * Whether or not to immediately capture the charge. Default is true

     * @param $value
     *
     * @return PurchaseRequest
     */
    public function setCapture($value)
    {
        return $this->setParameter('capture', (bool) $value);
    }

    /**
     * Get the capture flag
     *
     * This will only return false if the parameter is set AND false. The default
     * value is true.
     *
     * @return array
     */
    public function getCustomParameters()
    {
        return $this->getParameter('customParameters');
    }

    /**
     * Set the custom parameters
     *
     * optional parameters custom. Array of custom parameters, e.g. custom[field1]=1, custom[field2]=2
     *
     * This allows us to pass data that will be returned in the callbacks, or used
     * for fraud prevention/detection
     *
     * @param array $value
     *
     * @return PurchaseRequest
     */
    public function setCustomParameters($value)
    {
        return $this->setParameter('customParameters', $value);
    }

    /**
     * Build an array from the ParameterBag object that is ready for sendData
     *
     * @throws InvalidRequestException directly for missing email, indirectly through validate
     * @link https://www.paymentwall.com/en/documentation/Brick/2968#charge_create
     * @return array
     */
    public function getData()
    {
        // verify that required parameters are provided
        // calls \Omnipay\Common\Message\AbstractRequest::validate()
        $requiredParams = ['amount', 'currency', 'accountId', 'description'];
        if ($this->getFingerprint()) {
            $requiredParams[] = 'fingerprint';
        } else {
            $requiredParams = array_merge($requiredParams, ['clientIp', 'browserDomain']);
        }

        // We need to have a token or a card
        $token = $this->getToken();
        if (empty($token)) {
            $token = $this->getCardReference();
        }
        if (empty($token)) {
            $requiredParams[] = 'card';
        }

        // pass the param list to the validate function
        call_user_func_array([$this,'validate'], $requiredParams);

        $data = [
            'purchase'  => [
                'token'                 => $token,
                'email'                 => $this->getEmail(),
                'uid'                   => $this->getAccountId(),
                'plan'                  => $this->getPackageId(),
                'amount'                => $this->getAmount(),
                'currency'              => $this->getCurrency(),
                'fingerprint'           => $this->getFingerprint(),
                'description'           => $this->getDescription(),
                'browser_ip'            => $this->getClientIp(),
                'browser_domain'        => $this->getBrowserDomain(),
                'options[capture]'      => $this->getCapture(),
            ]
        ];

        // if there is no authorization token we need to provide sendData with
        // the card data so that it can get a one-time token from PaymentWall
        if (empty($data['purchase']['token'])) {
            $card = $this->getCard();
            $data['card'] = [
                'public_key'        => $this->getPublicKey(),
                'card[number]'      => $card->getNumber(),
                'card[exp_month]'   => $card->getExpiryMonth(),
                'card[exp_year]'    => $card->getExpiryYear(),
                'card[cvv]'         => $card->getCvv(),
            ];

            // Fill some of the purchase data from the card data
            $data['purchase']['customer[firstname]'] = $card->getFirstName();
            $data['purchase']['customer[lastname]']  = $card->getLastName();
            $data['purchase']['customer[zip]']       = $card->getBillingPostcode();
        }

        // Callback URLs if they are set
        // PW expects them as part of the purchase data
        if ($this->getReturnUrl()) {
            $data['purchase']['success_url'] = $this->getReturnUrl();
        }
        if ($this->getNotifyUrl()) {
            $data['purchase']['pingback_url'] = $this->getNotifyUrl();
        }

        // apply any custom parameters
        // $this->getParameter() returns a value not compatible with foreach when not defined
        if ($this->getCustomParameters()) {
            foreach ($this->getCustomParameters() as $key => $value) {
                $data['purchase']['custom['.$key.']'] = $value;
            }
        }

        return $data;
    }

    /**
     * Submit a payment through the PaymentWall Library
     *
     * @param mixed $data
     *
     * @throws RuntimeException
     * @return Response
     */
    public function sendData($data)
    {
        // Initialise the PaymentWall configuration
        $this->setPaymentWallObject();

        // if no token exists, create one
        if (empty($data['purchase']['token'])) {
            // Create a one time token
            $tokenModel = new \Paymentwall_OneTimeToken();
            $tokenObject = $tokenModel->create($data['card']);

            if ($tokenObject->type == 'Error') {
                throw new RuntimeException($tokenObject->error, $tokenObject->code);
            }
            $data['purchase']['token'] = $tokenObject->getToken();
        }
        if (empty($data['purchase']['token'])) {
            throw new RuntimeException('Payment Token could not be created', 231);
        }

        // Now we know that we have an actual token (one time or
        // permanent), we can create the charge request.
        $charge = new \Paymentwall_Charge();
        $charge->create($data['purchase']);

        // Force the charge properties to be an array
        $properties = $charge->getProperties();
        $properties = json_decode(json_encode($properties), true);

        // Construct the response object
        $this->response = new Response($this, $properties);

        if ($charge->isSuccessful()) {
            if ($charge->isCaptured()) {
                $this->response->setCaptured(true);
            } elseif ($charge->isUnderReview()) {
                $this->response->setUnderReview(true);
            }
        }
        return $this->response;
    }
}
