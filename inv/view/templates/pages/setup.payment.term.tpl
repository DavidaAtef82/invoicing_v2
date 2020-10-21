{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="PaymentTerm" 
{/block}
{block name=style}{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/setup.payment.term.js?ver={$smarty.const.VERSION}"></script>
{/block}
{block name=dialog}
<div id="dlgEdit" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title">
            <i style="font-size:24px" class=" font-blue fa fa-edit"></i> Edit Payment Term</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="editForm">
            <div class="row">
                <div class="col-md-12" >
                    <div class="form-group">
                        <label class="control-label">Name*</label>
                        <input type="text" name="paymenttermName" class="form-control" placeholder="Name" 
                            autocomplete="off" data-ng-model="objEditPaymentTerm.strName" required />
                        <small class="help-block font-red" 
                            data-ng-show="editForm.paymenttermName.$touched && editForm.paymenttermName.$invalid">
                            Name is required
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <textarea style=" width: 100%;" rows="10" name="paymenttermDescription"
                            data-ng-model="objEditPaymentTerm.strDescription">
                        </textarea>
                    </div>
                </div>
            </div>
        </form>   
    </div>
    <div class="modal-footer">
        <button type="button"  data-ng-click="editPaymentTerm()" class="btn green-jungle" 
            data-dismiss="modal" data-ng-disabled="editForm.$invalid">EDIT</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>

    </div>
</div>

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> Delete Payment Term</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this element?</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removePaymentTerm()" class="btn red-thunderbird" data-dismiss="modal">DELETE</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>
<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-user-plus"></i> Add Payment Term</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="col-md-12" >
                    <div class="form-group">
                        <label class="control-label">Name*</label>
                        <input type="text" name="paymenttermName" class="form-control" placeholder="Name" 
                            autocomplete="off" data-ng-model="objAddPaymentTerm.strName" required />
                        <small class="help-block font-red" 
                            data-ng-show="addForm.paymenttermName.$touched && addForm.paymenttermName.$invalid">
                            Name is required
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Description</label>
                        <textarea style=" width: 100%;" rows="10" name="paymenttermDescription"
                            data-ng-model="objAddPaymentTerm.strDescription">
                        </textarea>
                    </div>
                </div>
            </div> 
        </form>  
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="addPaymentTerm()" class="btn green-jungle" 
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
        <span class="visible-lg-inline-block">Payment Term</span>
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
                                <input type="text" data-ng-enter="filter()" name="txtPaymentTermName" 
                                    data-ng-model="objFilter.strName" placeholder="PaymentTerm Name" class="form-control"/>
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
                    <i style="font-size:15px" class=" font-blue fa fa-users"></i>&nbsp;Payment Terms
                </div>
                <pagination  data-ng-if="objPaymentTerms.arrData.length>20" data-total-items="objPaymentTerms.intTotal" 
                data-items-per-page="objPaging.intPageSize" data-ng-model="objPaging.intPageNo" 
                data-max-size="objPaging.intMaxSize" class="pagination-sm" data-boundary-links="true" 
                data-ng-change="nextPage()"></pagination>
            </div>                                                               
            <div class="portlet-body">
                <div data-ng-if="objPaymentTerms.arrData.length > 0" class="table-responsive">
                    <table class="table  table-light table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="objPaymentTerm in objPaymentTerms.arrData"><!--data-ng-class="{literal}{'striped':objUser.intDisabled == 1}{/literal}"--> 
                                <td>{{objPaymentTerm.intID}}</td>
                                <td>{{objPaymentTerm.strName}}</td>
                                <td>{{objPaymentTerm.strDescription}}</td>
                                <!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_DELETE,$_UserPermission)}{/if}-->
                                <td class="icon">
                                    <a data-ng-disabled='objPaymentTerm.boolIsReserved' data-ng-click="edit(objPaymentTerm ,$index)" 
                                    data-toggle="modal" data-target="#dlgEdit" class="btn blue-dark btn-xs" 
                                    title="Edit"><i class="fa fa-edit"></i> <span class="visible-lg-inline-block">
                                    </span></a>
                                    &nbsp;&nbsp;<a data-ng-click="remove(objPaymentTerm.intID, $index)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="Delete"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="objPaymentTerms.arrData.length == 0" class="alert alert-warning">
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