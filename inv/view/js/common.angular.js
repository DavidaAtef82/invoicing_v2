var _strModule = 'inv';
var app = angular.module('INV',['ui.bootstrap','xeditable','angularShamSpinner','bootstrap.wrapper','ui.select','ui.calendar','bootstrap.knobify','mgo-angular-wizard','ngSanitize','nl2br','highcharts-ng']);


app.value('psiSortableConfig', {
    placeholder: "placeholder",
    opacity: 0.8,
    axis: "y",
    helper: 'clone',
    forcePlaceholderSize: true
})
.directive("psiSortable", ['psiSortableConfig', '$log', function(psiSortableConfig, $log) {
    return {
        require: '?ngModel',
        link: function(scope, element, attrs, ngModel) {

            if(!ngModel) {
                $log.error('psiSortable needs a ng-model attribute!', element);
                return;
            }

            var opts = {};
            angular.extend(opts, psiSortableConfig);
            opts.update = update;

            // listen for changes on psiSortable attribute
            scope.$watch(attrs.psiSortable, function(newVal) {
                angular.forEach(newVal, function(value, key) {
                    element.sortable('option', key, value);
                });
                }, true);

            // store the sortable index
            scope.$watch(attrs.ngModel+'.length', function() {
                element.children().each(function(i, elem) {
                    jQuery(elem).attr('sortable-index', i);
                });
            });

            // jQuery sortable update callback
            function update(event, ui) {
                // get model
                var model = ngModel.$modelValue;
                // remember its length
                var modelLength = model.length;
                // rember html nodes
                var items = [];

                // loop through items in new order
                element.children().each(function(index) {
                    var item = jQuery(this);

                    // get old item index
                    var oldIndex = parseInt(item.attr("sortable-index"), 10);

                    // add item to the end of model
                    model.push(model[oldIndex]);

                    if(item.attr("sortable-index")) {
                        // items in original order to restore dom
                        items[oldIndex] = item;
                        // and remove item from dom
                        item.detach();
                    }
                });

                model.splice(0, modelLength);

                // restore original dom order, so angular does not get confused
                element.append.apply(element, items);

                // notify angular of the change
                scope.$digest();
            }

            element.sortable(opts);
        }
    };
}]);



app.run(function(editableOptions) {
    editableOptions.theme = 'bs3'; 
    $('body').show();
});

app.filter('newlines', function() {
    return function(text) {
        if(text != null && text != ""){
            return text.split(/\n/g);
        }
    };   
});
app.filter('capitalize', function() {
    return function(input, scope) {
        if (input!=null)
            input = input.toLowerCase();
        if (input==undefined){
            return input;
        }
        return input.substring(0,1).toUpperCase()+input.substring(1);
    }
}); 
app.filter('datetime', function() {
    return function(data, format){
        if (data == undefined || data == '' || format == undefined || format == '') {
            return data;
        }

        data = data.trim();
        var arrDateTime = data.match(/^(\d{4})\-(\d{2})\-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/);
        var arrDate = data.match(/^(\d{4})\-(\d{2})\-(\d{2})$/);
        var arrTime = data.match(/^(\d{2}):(\d{2}):(\d{2})$/);
        var arrTimeNoSeconds = data.match(/^(\d{2}):(\d{2})$/);
        var date = null;

        if (arrDateTime !== null) {
            // Datetime string
            var year = parseInt(arrDateTime[1], 10);
            var month = parseInt(arrDateTime[2], 10) - 1; // months are 0-11
            var day = parseInt(arrDateTime[3], 10);
            var hour = parseInt(arrDateTime[4], 10);
            var minute = parseInt(arrDateTime[5], 10);
            var second = parseInt(arrDateTime[6], 10);
            date = new Date(year, month, day, hour, minute, second);
        } else if (arrDate !== null) {
            // Date string
            var year = parseInt(arrDate[1], 10);
            var month = parseInt(arrDate[2], 10) - 1; // months are 0-11
            var day = parseInt(arrDate[3], 10);
            date = new Date(year, month, day);
        } else if (arrTime !== null) {
            var hour = parseInt(arrTime[1], 10);
            var minute = parseInt(arrTime[2], 10);
            var second = parseInt(arrTime[3], 10);
            date = new Date(1900, 1, 1, hour, minute, second);
        } else if (arrTimeNoSeconds !== null) {
            var hour = parseInt(arrTimeNoSeconds[1], 10);
            var minute = parseInt(arrTimeNoSeconds[2], 10);
            date = new Date(1900, 1, 1, hour, minute);
        } else {
            return data;
        }

        return dateFormat(date, format);
    }
});
app.filter('size', function() {
    return function(number) {
        var m = number;
        if (IsNumeric(number)){
            m = Math.round(number/(1024*1024),2);
            if (m==0){
                m = Math.round(number/(1024),2);
                return m+'KB';
            }else{
                return m+'MB';
            }

        }
        return number;

    };   
});
app.filter('numberFixedLen', function() {
    return function(a,b){
        return(1e4+a+"").slice(-b)
    }
});

app.directive('applyUniform',function($timeout){
    return {
        restrict:'A',
        require: 'ngModel',
        link: function(scope, element, attr, ngModel) {
            element.uniform({useID: false});
            scope.$watch(function() {return ngModel.$modelValue}, function() {
                $timeout(jQuery.uniform.update, 0);
            } );
        }
    };
});
app.directive('file', function(){
    return { 
        scope: {
            file: '='
        },
        link: function(scope, el, attrs){
            el.bind('change', function(event){
                var files = event.target.files;
                var file = files[0];
                scope.file = file ? file : undefined;
                //scope.$apply();

                var reader = new FileReader();
                reader.onload = function(evntFile) {  
                    scope.file.content = evntFile.target.result;

                    scope.$apply();
                };
                if (scope.file==undefined){
                    return;
                }
                reader.readAsDataURL(scope.file)
            });
        }
    };
});
app.directive('ngEnter', function () {
    return function (scope, element, attrs) { 
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
});
app.directive('paginator', function($parse){
    return {
        require: 'ngModel',
        scope: {
            'paginator':'=', 
            'ngModel':'=' 
        },
        link: function(scope, el, attrs, ctrl){
            var dtStartDate;
            scope.$watch('ngModel', function(nVal) {
                if(nVal != undefined && nVal != ''){
                    dtStartDate = nVal;
                    showPaginator(); 
                } else {
                    if($('.paginator').length){
                        $('.paginator').datepaginator('remove');
                    }
                }
                //el.val(nVal); 
            }); 
            function showPaginator(){
                var myClass = 'paginator';
                el.addClass(myClass);
                var myClass = 'paginator';
                el.addClass(myClass);
                $('.paginator').datepaginator()
                var options = {
                    selectedDate: dtStartDate,
                    textSelected: 'YYYY-MM-DD',
                    text: 'DD',
                    size: 'small',
                    hint: 'YYYY-MM-DD' ,
                    squareEdges: true,
                    onSelectedDateChanged: function(event, date) {
                        var Day = date._d.getDate();
                        Day = (Day.length == 1) ? '0'+Day : Day;
                        var Month = date._d.getMonth()+1;
                        Month = (Month.length == 1) ? '0'+Month : Month;
                        var Year = date._d.getFullYear();
                        dtStartDate = Year+'-'+Month+'-'+Day;
                        /*AlertError(dtStartDate);
                        scope.paginator = dtStartDate;
                        x = el;
                        v = attrs;*/
                        $('.paginator').datepaginator('setSelectedDate', dtStartDate);


                        ctrl.$setViewValue(dtStartDate);
                        ctrl.$render();
                        //el.preventDefault();
                        scope.$apply(); // needed if other parts of the app need to be updated
                    }
                };
                $('.paginator').datepaginator(options);
            }
        }
    };
});
app.directive('multiSelectDropdown', function($parse){
    return {
        require: 'ngModel',
        scope: {
            'ngModel':'=', 
        },
        link: function(scope, el, attrs, ctrl){
            scope.$watch('ngModel', function(nVal) {
                if(Array.isArray(nVal)){
                    id= el.context.attributes.id.value;
                    value= el.context.attributes.nameattr.value;
                    init(nVal,id,value); 
                }
                //el.val(nVal); 
            }); 
            function init(arr, id,value){
                //attrs.$$element.addClass(myClass); 
                $('#'+id).multiselect({
                    enableClickableOptGroups: true,
                    nonSelectedText: value,
                    maxHeight: 100,
                    numberDisplayed: 1,
                    buttonWidth: '100%',
                    //buttonContainer: '<div />',
                    //includeSelectAllOption:true
                });
                if(arr.length == 0){
                    //$("input[name='multiselect']").parent().removeClass("checked");
                    //$('option', $('#'+id)).prop('selected', false);

                    $('option', $('#'+id)).each(function(element) {
                        $(this).removeAttr('selected').prop('selected', false);
                    });

                    $('li', $('#'+id).parent().find('ul')).each(function(element) {
                        $(this).find('span').removeClass("checked");
                    });

                    $('#'+id).parent()
                    $('#'+id).multiselect('refresh'); 
                    $("input[name='multiselect']").parent().removeClass("checked");
                    /*$('#'+id).multiselect('deselectAll', false);
                    $('#'+id).multiselect('updateButtonText'); */   
                }

                //$('#example-deselect').multiselect('deselect', ['1', '2', '4']);
                ctrl.$setViewValue(arr);
                ctrl.$render();
                //el.preventDefault();
                //scope.$apply(); // needed if other parts of the app need to be updated    
            }
        }
    };
});

app.service('SrvConfig', ['$http',function($http) {
    var service = {
        GetConfigurations: function(){
            var strUrl= 'service.php?'
            strUrl+= "module=cmn";
            strUrl+= "&service=Config";
            strUrl+= "&action=GetConfigurations";
            return $http.post(strUrl);
        }
    }
    return service;
}]);

app.service('SrvUser', ['$http',function($http) {
    var service = {
        ListAll: function(intDisabled){
            var strUrl = 'service.php?';
            strUrl+='module=cmn';
            strUrl+= "&service=User";
            strUrl+= "&action=ListAll";
            if(intDisabled != undefined){
                strUrl+= "&disabled="+intDisabled;
            }
            return $http.post(strUrl);
        },
    };
    return service;
}]);

app.service('SrvCustomer', ['$http',function($http) {
    var service = {
        List: function(objFilter, objPage){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Customer';
            strUrl+='&action=List';
            if (objFilter == undefined) {
                return $http.post(strUrl);
            } else {
                return $http.post(strUrl, {'objFilter':objFilter,'objPage':objPage});
            }
        },
        ListAll: function(){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Customer';
            strUrl+='&action=ListAll';
            return $http.post(strUrl);
        },
        View: function(intCustomerID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Customer';
            strUrl+='&action=View';
            strUrl+='&customer_id='+intCustomerID;               
            return $http.post(strUrl);
        },
        Add: function(objCustomer){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Customer';
            strUrl+='&action=Add';
            console.log(objCustomer);
            return $http.post(strUrl, {'objCustomer':objCustomer}); 
        },
        Update: function (objCustomer){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Customer';
            strUrl+='&action=Update';
            return $http.post(strUrl, {objCustomer:objCustomer});
        },
        Delete: function (intCustomerID){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Customer';
            strUrl+='&action=Delete';
            strUrl+='&customer_id='+intCustomerID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);

app.service('SrvContact', ['$http',function($http) {
    var service = {
        List: function(objFilter, objPage){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Contact';
            strUrl+='&action=List';
            if (objFilter == undefined) {
                return $http.post(strUrl);
            } else {
                return $http.post(strUrl, {'objFilter':objFilter,'objPage':objPage});
            }
        },
        Add: function(objContact){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Contact';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'objContact':objContact}); 
        },
        Update: function (objContact){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Contact';
            strUrl+='&action=Update';
            return $http.post(strUrl, {objContact:objContact});
        },
        Delete: function (intContactID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Contact';
            strUrl+='&action=Delete';
            strUrl+='&contact_id='+intContactID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);


app.service('SrvCountry', ['$rootScope','$http','$location',function($rootScope,$http) {
    var service = {
        List: function(){
            var strUrl = 'service.php?';
            strUrl+='module=cmn';
            strUrl+='&service=Country';
            strUrl+='&action=List';
            console.log(strUrl);
            return $http.post(strUrl);
        }
    }
    return service;
}]);


app.service('SrvCity', ['$rootScope','$http','$location',function($rootScope,$http) {
    var service = {
        List: function(){
            var strUrl = 'service.php?';
            strUrl+='module=cmn';
            strUrl+='&service=City';
            strUrl+='&action=List';
            return $http.post(strUrl);
        },
        ListByCountryID: function(intCountryID){
            var strUrl = 'service.php?';
            strUrl+='module=cmn';
            strUrl+='&service=City';
            strUrl+='&action=ListByCountryID';
            strUrl+='&country_id='+intCountryID;
            return $http.post(strUrl);
        }
    }
    return service;
}]);

app.service('SrvPaymentMethod', ['$http',function($http) {
    var service = {
        List: function(bolManual){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Paymentmethod';
            strUrl+='&action=List';
            strUrl+='&manual=';
            strUrl+=bolManual;
            return $http.post(strUrl);
        },
        Add: function(objMethod){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Paymentmethod';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'objMethod':objMethod}); 
        },
        Update: function (objMethod){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Paymentmethod';
            strUrl+='&action=Update';
            return $http.post(strUrl, {'objMethod':objMethod});
        },
        Delete: function (intMethodID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Paymentmethod';
            strUrl+='&action=Delete';
            strUrl+='&method_id='+intMethodID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);

app.service('SrvTaxType', ['$http',function($http) {
    var service = {
        List: function(){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Taxtype';
            strUrl+='&action=ListAll';
            return $http.post(strUrl);
        },
        Add: function(objTaxType){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Taxtype';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'objTaxType':objTaxType}); 
        },
        Update: function (objTaxType){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Taxtype';
            strUrl+='&action=Update';
            return $http.post(strUrl, {'objTaxType':objTaxType});
        },
        Delete: function (intTaxTypeID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Taxtype';
            strUrl+='&action=Delete';
            strUrl+='&tax_type_id='+intTaxTypeID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);

app.service('SrvPaymentTerm', ['$http',function($http) {
    var service = {
        ListAll: function(){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+= "&service=PaymentTerm";
            strUrl+= "&action=ListAll";
            return $http.post(strUrl);
        },    
        List: function(objFilter, objPage){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=PaymentTerm';
            strUrl+='&action=List';
            if (objFilter == undefined) {
                return $http.post(strUrl);
            } else {
                return $http.post(strUrl, {'objFilter':objFilter,'objPage':objPage});
            }
        },
        Add: function(objPaymentTerm){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=PaymentTerm';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'objPaymentTerm':objPaymentTerm}); 
        },
        Update: function (objPaymentTerm){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=PaymentTerm';
            strUrl+='&action=Update';
            return $http.post(strUrl, {objPaymentTerm:objPaymentTerm});
        },
        Delete: function (intPaymentTermID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=PaymentTerm';
            strUrl+='&action=Delete';
            strUrl+='&paymentterm_id='+intPaymentTermID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);

app.service('SrvCatalogue', ['$http',function($http) {
    var service = {
        ListAll: function(){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+= "&service=Catalogue";
            strUrl+= "&action=ListAll";
            return $http.post(strUrl);
        },
        List: function(objFilter, objPage){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Catalogue';
            strUrl+='&action=List';
            if (objFilter == undefined) {
                return $http.post(strUrl);
            } else {
                return $http.post(strUrl, {'objFilter':objFilter,'objPage':objPage});
            }
        },
        Add: function(objCatalogue){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Catalogue';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'objCatalogue':objCatalogue}); 
        },
        Update: function (objCatalogue){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Catalogue';
            strUrl+='&action=Update';
            return $http.post(strUrl, {objCatalogue:objCatalogue});
        },
        Delete: function (intCatalogueID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Catalogue';
            strUrl+='&action=Delete';
            strUrl+='&catalogue_id='+intCatalogueID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);

app.service('SrvInvoice', ['$http',function($http) {
    var service = {
        List: function(objFilter, objPage){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=List';
            if (objFilter == undefined) {
                return $http.post(strUrl);
            } else {
                return $http.post(strUrl, {'objFilter':objFilter,'objPage':objPage});
            }
        },
        ListAll: function(){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=ListAll';
            return $http.post(strUrl);
        },
        View: function(intInvoiceID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=View';
            strUrl+='&invoice_id='+intInvoiceID;               
            return $http.post(strUrl);
        },
        Add: function(objInvoice ,arrInvoiceRow){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Invoice';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'objInvoice':objInvoice,'arrInvoiceRow':arrInvoiceRow}); 
        },
        Update: function(objInvoice ,arrInvoiceRow){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=Update';
            return $http.post(strUrl, {'objInvoice':objInvoice,'arrInvoiceRow':arrInvoiceRow}); 
        },
        UpdateStatus: function(intInvoiceID ,intStatusID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=UpdateStatus';
            strUrl+='&invoice_id='+intInvoiceID;
            strUrl+='&status_id='+intStatusID; 
            return $http.post(strUrl);
        },
        Delete: function (intInvoiceID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=Delete';
            strUrl+='&invoice_id='+intInvoiceID;
            return $http.post(strUrl);  
        },
        GetNextInvoiceNumber: function(){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=GetNextInvoiceNumber';
            return $http.post(strUrl); 
        },
        GetByID: function (intInvoiceID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=GetByID';
            strUrl+='&invoice_id='+intInvoiceID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);

app.service('SrvInvoiceRow', ['$http',function($http) {
    var service = {
        Add: function(intInvoiceID ,arrInvoiceRow){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=InvoiceRow';
            strUrl+='&action=Add';
            return $http.post(strUrl, {'arrInvoiceRow':arrInvoiceRow}); 
        },
        UpdateOrder: function (intInvoiceRowID ,intOrder){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=InvoiceRow';
            strUrl+='&action=UpdateOrder';
            strUrl+= "&invoice_row_id="+intInvoiceRowID;
            strUrl+= "&order="+intOrder;
            return $http.post(strUrl);
        },
        Delete: function (intInvoiceID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Invoice';
            strUrl+='&action=Delete';
            strUrl+='&invoice_id='+intInvoiceID;
            return $http.post(strUrl);  
        }
    };
    return service;
}]);


app.service('SrvPayment', ['$http',function($http) {
    var service = {
        List: function(objFilter, objPage){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Payment';
            strUrl+='&action=List';
            if (objFilter == undefined) {
                return $http.post(strUrl, {'objPage':objPage});
            } else {
                return $http.post(strUrl, {'objFilter':objFilter,'objPage':objPage});
            }
        },
        Delete: function(intPaymentID){
            var strUrl = 'service.php?';
            strUrl+='module='+_strModule;
            strUrl+='&service=Payment';
            strUrl+='&action=Delete';
            strUrl+='&payment_id='+intPaymentID;
            return $http.post(strUrl);
        },
        Add: function(objPayment){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Payment';
            strUrl+='&action=Add';
            console.log("common");
            console.log(objPayment);
            return $http.post(strUrl, {'objPayment':objPayment}); 
        },
        ListInvoices: function(intPaymentID){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+='&service=Payment';
            strUrl+='&action=ListInvoices';
            strUrl+='&payment_id=';
            strUrl+=intPaymentID;
            return $http.post(strUrl);
        }
    };
    return service;
}]);
app.service('SrvStatus', ['$http',function($http) {
    var service = {
        ListAll: function(){
            var strUrl = 'service.php?';
            strUrl+='module=inv';
            strUrl+= "&service=Status";
            strUrl+= "&action=ListAll";
            return $http.post(strUrl);
        },
    };
    return service;
}]);
app.service('SrvOnlinePayment',['$http', function($http){
    var service = {
        Create: function(objPayment){
        var strURL = 'service.php?';
        strURL+= 'module=inv';
        strURL+= '&service=OnlinePayment';
        strURL+= '&action=Add';
        return $http.post(strURL, {'objPayment':objPayment}); 
        }
//        Create: function(objPayment){
//            var strURL = 'index.php?';
//            strURL+= 'module=inv';
//            strURL+= '&page=SubmitOnlinePayment';
//            strURL+= '&action=Submit';
//            console.log(strURL);
//            return $http.post(strURL, {'objPayment':objPayment}); 
//        }    
    }; 
    return service;
}]);
controllers.Common = ['$scope','$timeout','SrvConfig',function($scope,$timeout,SrvConfig){
    $scope.objLang = objLang;
    $scope.arrConfig = [];

    function init(){
        if (localStorage.length==0){
            SrvConfig.GetConfigurations().then(function(response){
                if (response.data.result){
                    var arr = response.data.object;
                    angular.forEach(arr, function(value, key){
                        localStorage[key] = value;
                    })
                    $scope.arrConfig = localStorage;
                    $timeout(function(){
                        $scope.$broadcast('configuration.ready');                
                    })
                }
            })
        }else{
            $scope.arrConfig = localStorage;
            $timeout(function(){
                $scope.$broadcast('configuration.ready');                
            })
        }                                          
    }
    init();
}]

String.prototype.replaceAll = function(target, replacement) {
    return this.split(target).join(replacement);
};