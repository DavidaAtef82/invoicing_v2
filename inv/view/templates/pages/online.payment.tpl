{config_load file=$_LanguageFile section="AUX_MENU"}
{config_load file=$_LanguageFile section=$_LanguageSection}
<!DOCTYPE html>
<html lang="{$_Language}" class="no-js" dir="{$_Direction}" data-ng-app="INV">
    <head>
        <meta charset="utf-8"/>
        <title>Online Payment</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <meta content="" name="description"/>
        <meta content="" name="author"/>
        <meta name="MobileOptimized" content="320">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {include file="{$smarty.const.LOCAL__THEME}/template/masters/master1/styles.tpl"}
        <link rel="stylesheet" type="text/css" href="{$smarty.const.WEB__THEME}/css/pages/login3.css"/>
        <link rel="shortcut icon" href="{$smarty.const.WEB__THEME}/images/{$smarty.const.THEME_FAV_LOGO}?ver={$smarty.const.VERSION}" type="image/png"/>
        {include file="{$smarty.const.LOCAL__THEME}/template/masters/master1/scripts.tpl"}
        <script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
        <script type="text/javascript" src="{$smarty.const.PATH__JS}/online.payment.js?ver={$smarty.const.VERSION}"></script>
        <style>
            .submit-loader {
            border: 16px solid #f3f3f3;
            border-radius: 50%;
            border-top: 16px solid #3498db;
            width: 120px;
            height: 120px;
            margin:0 auto;
            margin-top:10%;
            -webkit-animation: spin 2s linear infinite; 
            }
            @-webkit-keyframes spin {
            0% { -webkit-transform: rotate(0deg); }
            100% { -webkit-transform: rotate(360deg); }
            }

            @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body style="background-color:#4d5b69" data-ng-controller="Common">
        <div class="login" style="background-color:#4d5b69!important" data-ng-controller="OnlinePayment"  data-ng-init="getInvoiceDetails({$invoice_id})"> 
            <div class="content" style="width:500px; margin-top:45px;"data-ng-show="boolShowForm">
            <form name="frmPay" class="login-form" method="post" data-ng-submit="AddPayment()" novalidate="novalidate" >
                <div class="logo" style=" margin-top:0;">
                    <img src="{$smarty.const.WEB__THEME}/images/{$smarty.const.THEME_LOGIN_LOGO}" style="width:200px;" alt=""/>            
                    <span style="font-size:13px">Online Payment</span>
                </div>
                <div class="form-group">                    
                    <label class="control-label">Name on Card</label>
                    <div class="input-icon">
                        <i class="fa fa-user" style="font-size:19px; margin-top:14px"></i>
                        <input type="text" data-ng-keyup="validateName()" name="strName" class="form-control placeholder-no-fix" placeholder="Name" required="required" focus="true" data-ng-model="objPayment.strName" style="height:40px"/>

                    </div>
                    <p class="help-block" style="color: #9B9FA4;font-size: 12px;" ng-show="objNotValid.boolName">Invalid name</p>
                </div>
                <div class="form-group">                    
                    <label class="control-label">Phone Number</label>
                    <div class="input-icon">
                        <i class="fa fa-phone" style="font-size:19px; margin-top:14px"></i>
                        <input type="text" data-ng-keyup="validatePhoneNumber()" name="strPhoneNumber" class="form-control placeholder-no-fix" placeholder="Phone Number" required="required" focus="true" data-ng-model="objPayment.strPhone" style="height:40px"/>
                    </div>
                    <p class="help-block" style="color: #9B9FA4;font-size: 12px;" ng-show="objNotValid.boolPhoneNumber">Invalid phone number</p>
                </div>
                <div class="form-group">                    
                    <label class="control-label">Card Number</label>
                    <div class="input-icon">
                        <i class="fa fa-credit-card" style="font-size:19px; margin-top:14px"></i>
                        <input type="text" maxlength="16" data-ng-keyup="validateCardNumber()" name="strCardNumber" class="form-control placeholder-no-fix" placeholder="Card Number" required="required" focus="true" data-ng-model="objPayment.strCardNumber" style="height:40px"/>
                    </div>
                    <p class="help-block" style="color:#9B9FA4 ;font-size: 12px;" ng-show="objNotValid.boolCardNumber">Invalid card number</p>                
                </div>
                <div class="form-group">
                    <label class="control-label">CVV</label>
                    <div class="input-icon">
                        <i class="fa fa-check" style="font-size:19px; margin-top:14px"></i>
                        <input type="text" data-ng-keyup="validateCVV()" name="intCVV" maxlength="4" class="form-control placeholder-no-fix" placeholder="CVV of 3 or 4 digits" required="required" focus="true" data-ng-model="objPayment.strCVV" style="height:40px"/>
                    </div>
                    <p class="help-block" style="color: #9B9FA4;font-size: 12px;" data-ng-show="objNotValid.boolCVV">CVV must be integer with 3 or 4 digits</p>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            <label class="control-label">Expiry Year</label>
                            <div class="input-icon">
                                <i class="fa fa-table" style="font-size:19px; margin-top:14px"></i>
                                <input type="text" data-ng-keyup="validateExpiryYear()" name="intYear" maxlength="2" class="form-control placeholder-no-fix" placeholder="YY" required="required" focus="true" data-ng-model="objPayment.strExpiryYear" style="height:40px"/>
                            </div>
                            <p class="help-block" style="color: #9B9FA4;font-size: 12px;" ng-show="objNotValid.boolExpiryYear">Invalid expiry year</p>
                        </div>
                        <div class="col-md-6">
                            <label class="control-label">Expiry Month</label>
                            <div class="input-icon">
                                <i class="fa fa-table" style="font-size:19px; margin-top:14px"></i>
                                <input type="text"  data-ng-keyup="validateExpiryMonth()" name="intMonth" maxlength="2" class="form-control placeholder-no-fix" placeholder="MM" required="required" focus="true" data-ng-model="objPayment.strExpiryMonth" style="height:40px"/>
                            </div>
                            <p class="help-block" style="color: #9B9FA4;font-size: 12px;" ng-show="objNotValid.boolExpiryMonth">Invalid expiry month</p>
                        </div>
                    </div>
                </div>
                <div class="form-group" style="width:100%; height:2px; background-color:#44B6AE">
                </div>
                <div class="form-group">
                    <div class="box" style="display:flex;align-items:center;">
                        <img src="http://localhost:80/ks_invoicing/modules/inv/view/images/card_2.png" style="width:40px; margin-right:10px">
                        <h4 style="font-weight:bold">Total</h4>
                        <h4 style="margin-left:250px; font-weight:bold">EGP{{decTotalDueAmount}}</h4>
                    </div>
                </div>
                <div class="form-group" style="height:50px">
                    <button id="submit-button" type="submit" class="btn pull-right" data-ng-click="addPayment({$invoice_id})" data-ng-disabled="frmPay.$invalid || formValid==false" style="background-color:#44B6AE; color:white">Finish and Pay</button>
                </div>
            </form>
            </div>

            <div class="response" style="width:900px; background-color:#F9FBFE; margin:0 auto; margin-top:140px; border-radius:7px" data-ng-show="boolShowSuccess">
                <div class="row">
                    <div class="col-md-2">
                        <img src="http://localhost:80/ks_invoicing/modules/inv/view/images/checked.png" style="width:100px; margin-left: 20px; margin-top: 50px;">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <h1 style="font-size=45px">Successful Payment Attempt!</h1>
                        </div>
                        <div class="row" style="display:flex;align-items:center;">
                            <h3 style="font-size=30px!important" style="margin-top:0"> Thank You for Using our Services!</h3>
                            <img src="http://localhost:80/ks_invoicing/modules/inv/view/images/like.png" style="width:15px; margin-top: 10px; margin-left: 6px;">
                        </div>
                        <div class="row" style="height:1px; background-color:#88FA0E; width:700px">
                        </div> 
                        <div class="row" style="font-size:19px; margin-top:10px; margin-bottom:20px">
                            <div class="row" style="">
                                <div class="col-md-5">
                                    Invoice Number :
                                </div>
                                <div class="col-md-7 ">
                                    <span style="font-weight:bold">{{intInvoiceNumber}}</span>
                                </div>                            
                            </div>
                            <div>
                                <div class="row">
                                    <div class="col-md-5">
                                        Amount Paid: 
                                    </div>
                                    <div class="col-md-7">
                                        <span style="font-weight:bold">{{decTotalDueAmount}}</span>
                                    </div>
                                </div>  
                            </div>                      
                        </div>
                    </div>
                </div>
            </div>
            <div class="response" style="width:900px; background-color:#F9FBFE; margin:0 auto; margin-top:140px; border-radius:7px" data-ng-show="boolShowError">
                <div class="row">
                    <div class="col-md-2">
                        <img src="http://localhost:80/ks_invoicing/modules/inv/view/images/error.png" style="width:100px; margin-left: 20px; margin-top: 50px;">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <h1 style="font-size=45px">Unsusessful Payment Attempt!</h1>
                        </div>
                        <div class="row">
                            <h3 style="font-size=30px!important"> Please Try Again Later.</h3>
                        </div>
                        <div class="row" style="height:1px; background-color:#F93A3A; width:700px">
                        </div> 
                        <div class="row" style="font-size:19px; margin-top:10px; margin-bottom:20px">
                            <div class="row">
                                <div class="col-md-5">
                                    Invoice Number :
                                </div>
                                <div class="col-md-7 ">
                                    <span style="font-weight:bold">{{intInvoiceNumber}}</span>
                                </div>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row" style="color:white; text-align:center" data-ng-show="boolShowForm">
                <div>
                    Modulus {$smarty.const.VERSION} | 2015 - {'Y'|date} &copy;
                </div>
            </div>
            <div data-ng-show="boolShowLoader">
                <div class="submit-loader"></div>
                <div style="font-size:20px; color:white; width:380px; margin:0 auto; margin-top:8px">Your Payment is in Progress, Please wait!</div>
            </div>
        </div>
    </body>
</html>