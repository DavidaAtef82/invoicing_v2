controllers.Catalogue = ['$scope','SrvCatalogue' ,'SrvTaxType' , '$filter','$location','$window', function($scope,SrvCatalogue ,SrvTaxType , $filter,$location,$window){ 
    $scope.objCatalogues = {arrData: [], intTotal: 0};
    $scope.objPaging = {
        intPageNo: 1,
        intPageSize: $scope.$parent.arrConfig.ListingPageSize,
        intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
    };
    $scope.objFilter = {
        strName: '',
        strDescription: '',
        floatPrice: '',
        strCode:'',
        intTaxTypeID: -1,
    };
    $scope.objAddCatalogue = {
        strName: '',
        strDescription: '',
        floatPrice: '',
        strCode:'',
        intTaxTypeID: -1,
    };
    $scope.intRemoveCatalogue = -1;
    $scope.objCurrentCatalogue = $scope.objFilter;
    $scope.okDelete = false;

    $scope.arrTaxTypes = [];

    $scope.filter = function(){
        if($scope.objFilter.intTaxTypeID == null ||$scope.objFilter.intTaxTypeID == undefined){
            $scope.objFilter.intTaxTypeID = -1;
        }
        $scope.objPaging.intPageNo = 1;
        SrvCatalogue.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objCatalogues = response.data.object;
            }else{
                $scope.objCatalogues = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.reset = function(){
        $scope.objPaging = {intPageNo: 1,intPageSize: $scope.$parent.arrConfig.ListingPageSize,intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber};
        $scope.objFilter = {
            strName: '',
            strDescription: '',
            floatPrice: '',
            strCode:'',
            intTaxTypeID: -1,
        };      
        SrvCatalogue.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objCatalogues = response.data.object;
            }else{
                $scope.objCatalogues = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })    
    }

    $scope.addCatalogue = function(){
        SrvCatalogue.Add($scope.objAddCatalogue).then(function(response){
            if (response.data.result){ 
                $scope.objCatalogues.arrData.push(response.data.object);
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.edit = function(objCatalogue ,index){
        var myJSON = JSON.stringify(objCatalogue);
        $scope.objEditCatalogue = JSON.parse(myJSON);
        $scope.objEditIndex = index;
    }
    $scope.editCatalogue = function() {
        SrvCatalogue.Update($scope.objEditCatalogue).then(function(response){
            if (response.data.result){ 
                $scope.objCatalogues.arrData[$scope.objEditIndex] = response.data.object;
                $scope.objEditCatalogue = '';
                $scope.objEditIndex = '' ;
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.remove = function(intCatalogueID, index){
        $scope.intRemoveCatalogue =  intCatalogueID;
        $scope.intRemoveIndex =  index;
    }
    $scope.removeCatalogue = function(){
        SrvCatalogue.Delete($scope.intRemoveCatalogue).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                listCatalogues();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function listCatalogues(){
        SrvCatalogue.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objCatalogues = response.data.object;
            }else{
                $scope.objCatalogues = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });  
    }
    function init(){            
        listCatalogues();
        GetTaxTypes();
    };
    function GetTaxTypes() {
        SrvTaxType.List().then(function(response){
            if (response.data.result){
                $scope.arrTaxTypes = response.data.object;
            }else{
                $scope.arrTaxTypes = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    init();
}];

app.controller(controllers);