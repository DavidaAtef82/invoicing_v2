{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="Catalogue" 
{/block}
{block name=style}{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/setup.catalogue.js?ver={$smarty.const.VERSION}"></script>
{/block}
{block name=dialog}

<div id="dlgEdit" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-edit"></i> {#LNG_6710#} {#LNG_6705#}</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="editForm">
            <div class="row">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">Code* </label> 
                        <input type="text" name="CatalogueCode" class="form-control" placeholder="Code"   
                            data-ng-model="objEditCatalogue.strCode" required/>
                        <small class="help-block font-red" 
                            data-ng-show="editForm.CatalogueCode.$touched && editForm.CatalogueCode.$invalid">
                            Code is required
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Name*</label>
                        <input type="text" name="CatalogueName" class="form-control" 
                            placeholder="Name" autocomplete="off" data-ng-model="objEditCatalogue.strName" required />
                        <small class="help-block font-red" 
                            data-ng-show="editForm.CatalogueName.$touched && editForm.CatalogueName.$invalid">
                            Name is required
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <input type="Description" name="CatalogueDescription" class="form-control" 
                            placeholder="Description" data-ng-model="objEditCatalogue.strDescription">
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="form-group"> 
                        <label class="control-label">TaxType </label>             
                        <select  data-ng-model="objEditCatalogue.intTaxTypeID" 
                            data-ng-options="TaxType.intID as TaxType.strTaxType for TaxType in arrTaxTypes" 
                            class="form-control">
                            <option value="">TaxType</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Price</label>
                        <input type="text" class="form-control" placeholder="Price" 
                            data-ng-model="objEditCatalogue.floatPrice"/>
                    </div>
                </div>   
            </div> 
        </form>    
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-disabled="editForm.$invalid" data-ng-click="editCatalogue()" 
            class="btn green-jungle">EDIT</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>

    </div>
</div>

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> Delete Catalogue</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-6" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this element?</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removeCatalogue()" class="btn red-thunderbird" data-dismiss="modal">DELETE</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>

<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-user-plus"></i> Add Catalogue</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">Code* </label> 
                        <input type="text" name="CatalogueCode" class="form-control" placeholder="Code"   
                            data-ng-model="objAddCatalogue.strCode" required/>
                        <small class="help-block font-red" 
                            data-ng-show="addForm.CatalogueCode.$touched && addForm.CatalogueCode.$invalid">
                            Code is required
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Name*</label>
                        <input type="text" name="CatalogueName" class="form-control" 
                            placeholder="Name" autocomplete="off" data-ng-model="objAddCatalogue.strName" required />
                        <small class="help-block font-red" 
                            data-ng-show="addForm.CatalogueName.$touched && addForm.CatalogueName.$invalid">
                            Name is required
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <input type="Description" name="CatalogueDescription" class="form-control" 
                            placeholder="Description" data-ng-model="objAddCatalogue.strDescription">
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="form-group"> 
                        <label class="control-label">TaxType </label>             
                        <select  data-ng-model="objAddCatalogue.intTaxTypeID" 
                            data-ng-options="TaxType.intID as TaxType.strTaxType for TaxType in arrTaxTypes" 
                            class="form-control">
                            <option value="">TaxType</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Price</label>
                        <input type="text" class="form-control" placeholder="Price" 
                            data-ng-model="objAddCatalogue.floatPrice"/>
                    </div>
                </div>   
            </div> 
        </form>  
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="addCatalogue()" class="btn green-jungle" 
            data-ng-disabled="addForm.$invalid">ADD</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>
    </div>
</div>

{/block}
{block name=toolbar}
<!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_ADD,$_UserPermission)}{/if}-->
<div class="btn-group">
    <a data-toggle="modal" data-target="#dlgAdd" class="btn btn-fit-height green-jungle" title="{#LNG_6705#}">
        <i class="fa fa-plus"></i> 
        <span class="visible-lg-inline-block">Catalogue</span>
    </a>
</div>
{/block}
{block name=content}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue"><i class="fa fa-filter font-blue"></i> Filter</div>
                <div class="tools"><a href="#" class="collapse"></a></div>
            </div>
            <div class="portlet-body">
                <form id="frmFilter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" name="txtCatalogueName" data-ng-model="objFilter.strName" placeholder="Catalogue Name" class="form-control"/>
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" name="txtCatalogueCode" data-ng-model="objFilter.strCode" placeholder="Catalogue Code" class="form-control"/>
                            </div>
                        </div>                              
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <select data-ng-model="objFilter.intTaxTypeID" data-ng-options="TaxType.intID as TaxType.strTaxType for TaxType in arrTaxTypes" class="form-control">
                                    <option value="">TaxType</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group pull-right">
                                <button type="button" class="btn blue" title="{#LNG_6690#}" data-ng-click="filter()"><i class="fa fa-filter"></i> Filter</button>
                                <button type="button" class="btn red-thunderbird" title="{#LNG_6694#}" data-ng-click="reset()"><i class="fa fa-refresh"></i> Reset</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue">
                    <i style="font-size:15px" class=" font-blue fa fa-users"></i>&nbsp;Catalogues
                </div>
                <pagination  data-ng-if="objCatalogues.arrData.length>20" 
                    data-total-items="objCatalogues.intTotal" data-items-per-page="objPaging.intPageSize" 
                    data-ng-model="objPaging.intPageNo" data-max-size="objPaging.intMaxSize" class="pagination-sm"
                    data-boundary-links="true" data-ng-change="nextPage()"></pagination>
            </div>                                                               
            <div class="portlet-body">
                <div data-ng-if="objCatalogues.arrData.length > 0" class="table-responsive">
                    <table class="table table-condensed table-light table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Price</th> 
                                <th>TaxType</th>
                                <th>Description</th>  
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="objCatalogue in objCatalogues.arrData"><!--data-ng-class="{literal}{'striped':objUser.intDisabled == 1}{/literal}"--> 
                                <td>{{objCatalogue.intID}}</td>
                                <td>{{objCatalogue.strCode}}</td>
                                <td>{{objCatalogue.strName}}</td>
                                <td>{{objCatalogue.floatPrice}}</td>    
                                <td>{{objCatalogue.objTaxType.strTaxType}} </td>
                                <td>{{objCatalogue.strDescription}} </td>
                                <!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_DELETE,$_UserPermission)}{/if}-->
                                <td class="icon">
                                    <a data-ng-click="edit(objCatalogue ,$index)" data-toggle="modal" data-target="#dlgEdit" class="btn blue-dark btn-xs" title="Edit"><i class="fa fa-edit"></i> <span class="visible-lg-inline-block"></span></a>
                                    &nbsp;&nbsp;<a data-ng-click="remove(objCatalogue.intID, $index)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="Delete"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="objCatalogues.arrData.length == 0" class="alert alert-warning">
                    <p>{#LNG_6708#}</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Temp delet form until solcing 2 modals in the same page problem-->
<!--End temp form-->
<!---temp add form-->
{/block}