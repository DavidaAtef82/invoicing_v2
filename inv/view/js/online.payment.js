controllers.OnlinePayment = ['$scope','SrvOnlinePayment','SrvInvoice','$filter','$location','$window',function($scope,SrvOnlinePayment,SrvInvoice,$filter,$location,$window){
    $scope.formValid = false;
    $scope.objPayment = {
        intInvoiceID:'',
        strName:'',
        strPhone:'',
        strCardNumber:'',
        strCVV:'',
        strExpiryYear:'',
        strExpiryMonth:'',
        decAmount:''
    }
    $scope.objNotValid = {
        boolName:false,
        boolPhoneNumber:false,
        boolCVV:false,
        boolExpiryYear:false,
        boolExpiryMonth:false,
        boolCardNumber:false  
    }
    $scope.boolShowLoader = false;
    $scope.boolShowForm = true;
    $scope.boolShowSuccess = false;
    $scope.boolShowError = false;
    $scope.validateName = function(){
        var strRGX = "^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$";
        if($scope.objPayment.strName && $scope.objPayment.strName.match(strRGX)){
            $scope.objNotValid.boolName = false;
            $scope.formValid = true;
            return true;  
        }
        $scope.objNotValid.boolName = true;
        $scope.formValid = false;
        return false;
    }
    $scope.validatePhoneNumber = function(){
        var strRGX = /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/;
        if($scope.objPayment.strPhone && $scope.objPayment.strPhone.match(strRGX)){
            $scope.objNotValid.boolPhoneNumber = false;
            $scope.formValid = true;
            return true;  
        }
        $scope.objNotValid.boolPhoneNumber = true;
        $scope.formValid = false;
        return false;
    }
    $scope.validateExpiryYear = function(){
        // Get today's year in 2 digits format
        var dtDate = new Date();
        var intYear = dtDate.getYear()-100;
        // Max card expiration period is 19 yearss
        if(($scope.objPayment.strExpiryYear >= intYear) && ($scope.objPayment.strExpiryYear <= intYear + 20) && (!isNaN($scope.objPayment.strExpiryYear))){
            $scope.objNotValid.boolExpiryYear = false;
            $scope.formValid = true;
            return true;  
        }
        $scope.objNotValid.boolExpiryYear = true;
        $scope.formValid = false;
        return false;
    }
    $scope.validateExpiryMonth = function(){
        if(($scope.objPayment.strExpiryMonth >= 01) && ($scope.objPayment.strExpiryMonth <= 12) && (!isNaN($scope.objPayment.strExpiryMonth))){

            $scope.objNotValid.boolExpiryMonth = false;
            $scope.formValid = true;
            return true;  
        }
        $scope.objNotValid.boolExpiryMonth = true;
        $scope.formValid = false;
        return false;
    } 
    $scope.validateCVV = function(){
        if(($scope.objPayment.strCVV <= 99) || isNaN($scope.objPayment.strCVV)){ // Less than 2 digits
            $scope.objNotValid.boolCVV = true;
            $scope.formValid = false;
            return false;  
        }
        $scope.objNotValid.boolCVV = false;
        $scope.formValid = true;
        return true;

    }
    $scope.validateCardNumber = function(){
        if(($scope.objPayment.strCardNumber.toString().length < 13) || (isNaN($scope.objPayment.strCardNumber))){
            $scope.objNotValid.boolCardNumber = true;
            $scope.formValid = false;
            return true;  
        }
        $scope.objNotValid.boolCardNumber = false;
        $scope.formValid = true;
        return false;
    }
    $scope.addPayment = function(intInvoiceID){
        $scope.boolShowLoader = true;
        $scope.boolShowSuccess = false;
        $scope.boolShowError = false;
        $scope.boolShowForm = false;
        console.log($scope.boolShowLoader);
        document.getElementById("submit-button").disabled = true;
        $scope.objPayment.intInvoiceID = intInvoiceID;
        $scope.objPayment.decAmount = $scope.decTotalDueAmount;
        SrvOnlinePayment.Create($scope.objPayment).then(function(response){
            if (response.data.result){
                $scope.boolShowLoader = false;
                $scope.boolShowSuccess = true;
            }else{
                $scope.boolShowLoader = false;
                $scope.boolShowError = true;
            }
        })
    }
    $scope.getInvoiceDetails = function(intInvoiceID){
        $scope.decTotalDueAmount = intInvoiceID;
        SrvInvoice.GetByID(intInvoiceID).then(function(response){
            if (response.data.result){
                $scope.intInvoiceNumber = response.data.object.strInvoiceNumber;
                $scope.decTotalDueAmount = response.data.object.decGrossAmount-response.data.object.decTotalPayment;      
            }else{
                console.log(response.data.messege);
            }
        });
    }    
}];

app.controller(controllers);