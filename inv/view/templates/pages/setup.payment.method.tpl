{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=lang}{/block}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="PaymentMethod" 
{/block}
{block name=style}
{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/enum.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/setup.payment.method.js?ver={$smarty.const.VERSION}"></script>
{/block}
{block name=dialog}
<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="460" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-plus"></i> Add Method</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="col-md-12" >
                    <div class="form-group">
                        <label class="control-label">Type*</label>
                        <input type="text" name="methodType" class="form-control" placeholder="Type" autocomplete="off" data-ng-model="objMethodAdd.strType" required />
                        <small class="help-block font-red" data-ng-show="addForm.methodType.$touched && addForm.methodType.$invalid">*Field Required</small>
                    </div>
                </div>   
            </div>
            <div class="row">
                <div class="col-md-12" >
                    <div class="form-group">
                        <label class="control-label">Details*</label>
                        <textarea type="text" rows="4" name="methodDetails" class="form-control" placeholder="Details" autocomplete="off" data-ng-model="objMethodAdd.strDetails" required /></textarea>
                        <small class="help-block font-red" data-ng-show="addForm.methodDetails.$touched && addForm.methodDetails.$invalid">*Field Required</small>
                    </div>
                </div> 
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" data-ng-model="objMethodAdd.boolManual">
                        <label class="form-check-label">Manual</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check pull-right">
                        <input type="checkbox" class="form-check-input" data-ng-model="objMethodAdd.boolDisabled">
                        <label class="form-check-label">Disabled</label>
                    </div>
                </div>
            </div>
        </form>  
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="add()" class="btn green-jungle" data-dismiss="modal" ng-disabled="addForm.$invalid">ADD</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>
    </div>
</div>
<div id="dlgEdit" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="460" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-edit"></i>Edit Payment Method</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Type</label>
                            <input type="text" class="form-control" placeholder="Type" autocomplete="off" data-ng-model="objMethodEdit.strType"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label">Details</label>
                            <textarea type="text" rows="4" class="form-control" placeholder="Type" autocomplete="off" data-ng-model="objMethodEdit.strDetails"/></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" data-ng-model="objMethodEdit.boolManual">
                            <label class="form-check-label">Manual</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" data-ng-model="objMethodEdit.boolDisabled">
                            <label class="form-check-label">Disabled</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <div class="modal-footer">
        <button type="button"  data-ng-click="editMethod()" class="btn green-jungle" data-dismiss="modal">Save</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">Cancel</button>
    </div>  
</div>
<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="460" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> Delete Method</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-6" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this paymebt method?</label>
            </div>    
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="removeMethod()" class="btn red-thunderbird" data-dismiss="modal">Delete</button>
        <button type="button" class="btn green-jungle" data-dismiss="modal">Cancel</button>
    </div>
</div>
<div id="dlgDetails" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="460" style="display: none;height:auto!important">
    <div class="modal-header">
        <h3 class="modal-title">
                <span style="color:#3598dc!important;" class="glyphicon glyphicon-share-alt">
        </span>
            {{strType}}
        </h3>

    </div>
    <div class="modal-body" style="margin: 0 auto;">
        {{strDetails}}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>
    </div>
</div>
{/block}
{block name=page_title}
<h1>{#LNG_6684#} <small>{#LNG_6685#}</small></h1>
{/block}
{block name=toolbar}
<div class="btn-group pull-right">      
    <a class="btn green-jungle btn-fit-height" data-toggle="modal" data-target="#dlgAdd">
        <i class="fa fa-plus"></i> <span class="visible-lg-inline-block">Add Method</span>
    </a>
</div>
{/block}

{block name=content}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue">
                    <i style="font-size:15px" class=" font-blue fa fa-share-alt"></i>&nbsp;Payment Methods
                </div>
            </div>                                                              
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-light table-hover">
                        <thead>
                            <tr>
                                <th>Payment ID</th>    
                                <th>Payment Type</th>
                                <th>Manual</th>
                                <th>Disabled</th>
                                <th></th>    
                            </tr> 
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="objPayMethod in arrPayMethods"> 
                                <td>{{objPayMethod.intID}}</td>
                                <td>{{objPayMethod.strType}}</td>
                                <td>{{objPayMethod.boolManual}}</td>
                                <td>{{objPayMethod.boolDisabled}}</td>    
                                <td class="icon">
                                    <a data-ng-click="details(objPayMethod.strType ,objPayMethod.strDetails)" data-toggle="modal" data-target="#dlgDetails" class="btn btn-xs yellow-saffron" title="details"><i class="fa fa-search-plus"></i> <span class="visible-lg-inline-block"></span></a>
                                    &nbsp;&nbsp;
                                    <a data-ng-click="edit(objPayMethod ,$index)" data-toggle="modal" data-target="#dlgEdit" class="btn blue-dark btn-xs" title="Edit"><i class="fa fa-edit"></i> <span class="visible-lg-inline-block"></span></a>
                                    &nbsp;&nbsp;
                                    <a data-ng-click="remove(objPayMethod.intID)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="Delete"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="arrPayMethods.length == 0" class="alert alert-warning">
                    <p>No payment methods found!</p>
                </div>
            </div>
        </div>
    </div>
</div>

{/block}