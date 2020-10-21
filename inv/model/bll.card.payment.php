<?php
namespace NsINV;
class ClsBllCardPayment extends ClsBllPayment{
    const PAYMENT_TYPE_ID = 23;
    const RESPONSE_TYPE_SUCCESS ='PAID';
    const RESPONSE_TYPE_FAILURE = 'UNPAID';

    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalPayment';
        $this->_strClsDalSave = '\NsINV\ClsDalPayment';
        $this->_data = array(
            'intInvoiceID'=>'',
            'strMerchantCode'=>'', // From payment method description
            'strSecureHash'=>'', // From payment method description
            'intCustomerMerchantProfileID'=>'', // Encrypted  value of customer ID
            'strPaymentMethod'=>'', // From payment method details (possible values: CARD, Fawry, etc)
            'strCardNumber'=>'', // From user form
            'strExpiryYear'=>'', // From user form
            'strExpiryMonth'=>'', // From user form
            'strCVV'=>'', // From user form
            'boolSaveCard'=>'', // From payment method details
            'strCustomerName'=>'', //From customer profile
            'strCustomerNameOnCard'=>'', // From user form (to be used in logs)
            'strCustomerPhone'=>'', //From customer profile
            'strCustomerPhoneFromForm'=>'', // From user form (to be used in logs)
            'strCustomerEmail'=>'', //From customer profile
            'strCurrencyCode'=>'', // From payment method details
            'strDescription'=>'', // From invoice reference
            'arrChargeItems'=>'', // From invoice rows
            'strSignature'=>'', //Generated
            'intOrderNumber' => '', // Comes from payment gateway
            'intPaymentGatewayReferenceID'=>'', // Comes from payment gateway
            'strMerchentReferenceID'=>'', // Hashed value of invoice id
            'strPaymentType'=>'' // Cowpay card
        );
        @parent::__construct(func_get_args());
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){ 
            // UPDATE
            return false;
        } else {
            // NEW
            $objDAL->fldCreatedOnTimestamp = date("Y-m-d H:i:s");
        }
        $objDAL->fkPaymentMethodID = $this->_data['intPaymentMethodID'];
        $objDAL->fkCustomerID = $this->_data['intCustomerID'];
        $objDAL->fldPaymentNumber = $this->_data['intPaymentNumber'];
        $objDAL->fldReference = $this->_data['strReference'];
        $objDAL->fldAmount = $this->_data['decAmount'];  
        $rslt = $objDAL->Save();
        if($rslt){
            $this->_data['intID'] = $objDAL->pkPaymentID;
        }
        return $rslt;
    }

    protected function createSignature(){
        //The SHA-256 digested for the following concatenated string merchant_code + merchant_reference_id +                  customer_merchant_profile_id + payment_method + amount(in two decimal format 10.00) +card_token (required in          case of CARD payment method) + secure_hash (provided in the merchant's dashboard at Cowpay) 
        $strSignature = $this->_data['strMerchantCode'];
        $strSignature .= $this->_data['strMerchentReferenceID'];
        $strSignature .= $this->_data['intCustomerMerchantProfileID'];
        $strSignature .= $this->_data['strPaymentMethod'];
        $strSignature .= $this->_data['decAmount'];
        $strSignature .= $this->_data['strSecureHash'];
        $strSignature = hash('sha256', $strSignature);
        return $strSignature;
    }

    protected static function createLog($strAction, $strObj, $strHashedInvoiceID){
        $objLog = new \NsCMN\ClsBllLog();
        $objLog->strObjectType = "Online Payment";  
        $objLog->strAction = $strAction;
        $objLog->objObjectID = "{strHashedInvoiceID:".$strHashedInvoiceID."}"; 
        $objLog->strObject = $strObj; 
        $objLog->intUserID = 1;
        $rsltSave = $objLog->Save();
        if($rsltSave){
            return true;
        } else {
            return false;
        }  
    }

    protected function CreateCardPayment(){
        //Connect to DB 
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $DB->StartTrans();
        $rslt = $this->Save();
        if($rslt){
            $intPaymentID = $this->intID;
        }else{
            // Log failure creating payment record
            self::createLog("INSERT", "{result:false, title:failuer, message: error inserting new payment in db}",'{}', -1);
            return $DB->CompleteTrans(false);
        }
        // 3. Add record in table inv_invoice_payment
        $objInvoice = new ClsBllInvoice();
        $rslt = $objInvoice->LoadByID($this->intInvoiceID);
        if(!$rslt){
            // Log failure
            self::createLog("INSERT", "{result:false, title:failuer, message: error loading invoice from db}",'{}', -1);
            return $DB->CompleteTrans(false); // Internal server error happened
        }
        $decAmount =  $this->decAmount;
        $rslt = $objInvoice->AddPayment($decAmount, $intPaymentID, 'CARD');
        if(!$rslt){             
            // Log failure
            self::createLog("INSERT", "{result:false, title:failuer, message: error inserting new invoice payment in db}",'{}', -1);
            return $DB->CompleteTrans(false);; // Internal server error happened
        }
        $arrJsonData = array(
            "result"=>true,
            "title"=>"success",
            "message"=>"Payment added in our database successfully!",
            "object"=>json_encode($this->_data)
        );
        self::createLog("INSERT", json_encode($arrJsonData), $this->strMerchentReferenceID);
        $DB->CompleteTrans(true);
        return true;
    }
    // This function handles the callback cowpay sends to us to tell us how the payment request was handled.
    // Request is in the following format
    // {
    //    "cowpay_reference_id": "506642", // This number was sent to us as a response to out payment request and is saved in logs 
    //    "merchant_reference_id": "1081", // Represents payment number in our system
    //    "order_status": "PAID", // PAID or UNPAID
    //    "signature": "18b438c5b750eb9e7dfe6bbbf24dba4821fd94393a042542b7e8cf1b0f9cd394" // Calculated in our system while sending the request and is saved in logs
    //    }
    //} 
    public static function HandleCallback($arrRequest){
        // 0. Check if this payment is already paid and was sent before(In case they sent the same callback twice)
        $boolPaymentExists = ClsBllPayment::GetByPaymentNumber($arrRequest['merchant_reference_id']);
        if($boolPaymentExists){
            self::createLog("INSERT", "{result:false, title:failuer, message: Payment number is already paid! Duplicate callback.}" ,'{}', -1);
            return false;
        }
        // 1. Get object payment details from logs given merchant_reference_id as fldObjectID
        $strObjectID = "{intPaymentNumber:".$arrRequest['merchant_reference_id']."}";
        $objPaymentLog = new \NsCMN\ClsBllLog();
        $objLog = $objPaymentLog->LoadByObjectID($strObjectID);
        $arrPaymentDetails = (array)json_decode($objLog['strObject']);
        if(!count($arrPaymentDetails)){
            self::createLog("INSERT", "{result:false, title:failuer, message: Invalid payment number}" ,'{}', -1);
            return false;
        }
        if($arrPaymentDetails['strSignature'] == $arrRequest['signature']){
            if($arrPaymentDetails['intPaymentGatewayReferenceID'] == $arrRequest['cowpay_reference_id']){
                if($arrPaymentDetails['intPaymentNumber'] == $arrRequest['merchant_reference_id']){
                } else{
                    // Create log record for payment failure
                    self::createLog("INSERT", "{result:false, title:failuer, message: Invalid merchent reference id}",'{}', -1);
                    return false; // Merchant reference id is not valid  
                }
            } else {
                // Create log record for payment failure
                self::createLog("INSERT", "{result:false, title:failuer, message: Invalid referenece number}", '{}', -1);
                return false; // Cowpay reference number is not valid
            }
        } else {
            // Create log record for payment failure
            self::createLog("INSERT", "{result:false, title:failuer, message: Invalid signature}",'{}', -1);
            return false; // Signature not valid
        }
        // Else request is valid. Check order status
        if($arrRequest['order_status'] == self::RESPONSE_TYPE_SUCCESS){
            $DB = &\ADODB_Connection_Manager::GetConnection('customer');
            $DB->StartTrans();
            // 2. Add payment in table inv_payment
            $objPayment = new self();
            $objPayment->intPaymentMethodID =  $arrPaymentDetails['intPaymentMethodID'];
            $objPayment->intCustomerID = $arrPaymentDetails['intCustomerID'];
            $objPayment->intPaymentNumber  = $arrPaymentDetails['intPaymentNumber'];
            $objPayment->strReference =  $arrPaymentDetails['strReference'];
            $objPayment->decAmount =  $arrPaymentDetails['decAmount'];
            $rslt = $objPayment->Save();
            if($rslt){
                $intPaymentID = $objPayment->intID;
            }else{
                // Log failure
                self::createLog("INSERT", "{result:false, title:failuer, message: error inserting new payment in db}",'{}', -1);
                $DB->CompleteTrans(false);
                return false; // Internal server error happened
            }
            // 3. Add record in table inv_invoice_payment
            $objInvoice = new ClsBllInvoice();
            $rslt = $objInvoice->LoadByID($arrPaymentDetails['intInvoiceID']);
            if(!$rslt){
                // Log failure
                self::createLog("INSERT", "{result:false, title:failuer, message: error loading invoice from db}",'{}', -1);
                $DB->CompleteTrans(false);
                return false; // Internal server error happened
            }
            $rslt = $objInvoice->AddPayment($arrPaymentDetails['decAmount'], $intPaymentID);
            if(!$rslt){
                // Log failure
                self::createLog("INSERT", "{result:false, title:failuer, message: error inserting new invoice payment in db}",'{}' -1);
                $DB->CompleteTrans(false);
                return false; // Internal server error happened
            }
            self::createLog("INSERT", json_encode($objPayment->_data), $objPayment->_data['intPaymentNumber']);
        } 
        else if ($arrRequest['order_status'] == self::RESPONSE_TYPE_FAILURE) {
            // TODO
        }
        $DB->CompleteTrans();
        return true; // Call back handled successfully
    }

    static function Request($arrPayment){
        $intInvoiceID = $arrPayment['intInvoiceID'];
        $objOnlinePayment = new ClsBllCardPayment();

        // 0. Get invoice details
        $objInvoice = new ClsBllInvoice();
        $objInvoice->loadByID($intInvoiceID);
        $objOnlinePayment->strMerchentReferenceID = $objInvoice->_data['strEncryptedID'];

        // 1. Get payment method details (Type = card, id = 23)
        $intPaymentMethodID = self::PAYMENT_TYPE_ID;
        $objOnlinePayment->intPaymentMethodID = $intPaymentMethodID;
        $objPaymentMethod = new ClsBllPaymentMethod();
        $objPaymentMethod->LoadByID($intPaymentMethodID);
        $strPaymentDetails = $objPaymentMethod->_data['strDetails'];
        $strPaymentDetails = json_decode($strPaymentDetails);

        // 2. Get merchant code and secure hash
        $objOnlinePayment->strMerchantCode = $strPaymentDetails->merchant_code;
        $objOnlinePayment->strSecureHash = $strPaymentDetails->secure_hash;
        $objOnlinePayment->strCurrencyCode = $strPaymentDetails->currency_code;
        $objOnlinePayment->strPaymentMethod = $strPaymentDetails->payment_method;
        $objOnlinePayment->boolSaveCard = $strPaymentDetails->save_card;
        $objOnlinePayment->strPaymentType = $objPaymentMethod->_data['strType'];

        // 3. SET customer name, phone, id and email
        $intCustomerID = $objInvoice->objCustomer->intID;
        $objOnlinePayment->intCustomerID = $intCustomerID;
        // intCustomerMerchantProfileID is an encrypted value of customer id.
        $objOnlinePayment->intCustomerMerchantProfileID = $objInvoice->objCustomer->strEncryptedID;
        $objOnlinePayment->strCustomerName = $objInvoice->objCustomer->strName;
        $objOnlinePayment->strCustomerPhone = $objInvoice->objCustomer->strPhone;
        $objOnlinePayment->strCustomerEmail = $objInvoice->objCustomer->strEmail;

        // 4. Set payment number (merchent_reference_id) and invoice id
        $objOnlinePayment->intPaymentNumber = @parent::getNextSearialNumber();
        $objOnlinePayment->intInvoiceID = $intInvoiceID;
        // 5. Set data sent from customer form
        $objOnlinePayment->strCardNumber = $arrPayment['strCardNumber'];
        $objOnlinePayment->strExpiryYear = $arrPayment['strExpiryYear'];
        $objOnlinePayment->strExpiryMonth = $arrPayment['strExpiryMonth'];
        $objOnlinePayment->strCVV = $arrPayment['strCVV'];
        $objOnlinePayment->decAmount = $arrPayment['decAmount'];

        // 6. Get invoice description and charge items
        $objOnlinePayment->strDescription = $objInvoice->strReference; 
        $objInvoiceRow = new ClsBllInvoiceRow();
        $arrChargeItems = $objInvoiceRow->GetByInvoiceID($intInvoiceID);

        // Put it in this format:
        //[
        //    {
        //      "itemId": "",
        //      "description": "",
        //      "price": "",
        //      "quantity": ""
        //    }
        //  ]

        foreach($arrChargeItems as $item){
            $objItem = array();
            $objItem['itemId'] = $item['intItemID'];
            $objItem['description'] = $item['strDescription'];
            $objItem['price'] = $item['intUnitPrice'];
            $objItem['quantity'] = $item['intQuantity'];
            $arrItems[] = $objItem;
        }

        $objOnlinePayment->arrChargeItems = json_encode($arrItems);
        $objOnlinePayment->strSignature = $objOnlinePayment->createSignature();
        // 6. Request from API
        $arrRequest = [
            'merchant_code' => $objOnlinePayment->strMerchantCode,
            'merchant_reference_id' => $objOnlinePayment->strMerchentReferenceID,
            'signature' => $objOnlinePayment->strSignature,
            'card_number' => $objOnlinePayment->strCardNumber,
            'expiry_year' => $objOnlinePayment->strExpiryYear,
            'expiry_month' => $objOnlinePayment->strExpiryMonth,
            'cvv' => $objOnlinePayment->strCVV,
            'save_card' => $objOnlinePayment->boolSaveCard,
            'customer_name' => $objOnlinePayment->strCustomerName,
            'customer_email' => $objOnlinePayment->strCustomerEmail,
            'customer_mobile' => $objOnlinePayment->strCustomerPhone,
            'customer_merchant_profile_id' => $objOnlinePayment->intCustomerMerchantProfileID,
            'amount' => $objOnlinePayment->decAmount,
            'currency_code' => $objOnlinePayment->strCurrencyCode,
            'charge_items' => $objOnlinePayment->arrChargeItems,
            'payment_method' => $objOnlinePayment->strPaymentMethod,
            'description' => $objOnlinePayment->strDescription
        ];
        $arrJsonData = array(
            "result"=>true,
            "title"=>"success",
            "message"=>"Payment Request Sent successfully!",
            "object"=>json_encode($objOnlinePayment->_data)
        );
        // Log sending a request to cowpay
        $rsltLog = self::createLog("REQUEST TO COWPAY",json_encode($arrJsonData), $objOnlinePayment->_data['strMerchentReferenceID']);
        $objCURL = curl_init('https://cowpay.me/api/fawry/charge-request-cc');
        // Receive server response = TRUE.
        curl_setopt($objCURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($objCURL, CURLOPT_POSTFIELDS, ($arrRequest));
        // Execute.
        $strResponse = curl_exec($objCURL);
        // Close the connection.
        curl_close($objCURL);
        $arrResponse =(array)(json_decode($strResponse));
        if($arrResponse['success']) {
            $objOnlinePayment->intOrderNumber = $arrResponse['order'];
            $objOnlinePayment->intPaymentGatewayReferenceID = $arrResponse['payment_gateway_reference_id'];
            // 1. Add to logs
            $arrJsonData = array(
                "result"=>true,
                "title"=>"success",
                "message"=>"Cowpay Respnded with success message!",
                "object"=>json_encode($arrResponse)
            );
            $rsltLog = self::createLog("RESPONSE FROM COWPAY", json_encode($arrJsonData), $objOnlinePayment->_data['strMerchentReferenceID']);
            if(!$rsltLog){
                return false; // Error creating log
            }
        } else {
            $arrJsonData = array(
                "result"=>false,
                "title"=>"failure",
                "message"=>"Cowpay Respnded with failure message!!",
                "object"=>json_encode($arrResponse)
            ); 
            $rsltLog = self::createLog("RESPONSE FROM COWPAY", json_encode($arrResponse), $objOnlinePayment->_data['strMerchentReferenceID']);
            return false; // failed to request payment
        }
        $objOnlinePayment->CreateCardPayment();
        return true;

    }
}