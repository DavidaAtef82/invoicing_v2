{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="Payment" 
{/block}
{block name=style}

{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/payment.list.js?ver={$smarty.const.VERSION}"></script>
{/block}
{block name=page_title}
<h1>{#LNG_6742#} <small>{#LNG_6743#}</small></h1>
{/block}
{block name=dialog}

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="560">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-red fa fa-trash"></i> {#LNG_6747#} {#LNG_6730#}</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-6" >
                <label class="control-label">{#LNG_6749#}</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removePayment()" class="btn red-thunderbird" data-dismiss="modal">{#LNG_6747#}</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">{#LNG_6748#}</button>
        </div>
    </div>
</div>

<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-plus"></i> {#LNG_6750#} {#LNG_6730#}</h3>
    </div>
    <div class="modal-body">
        <form name="addForm"> 
            <div class="row">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">{#LNG_6735#}</label>
                        <input type="text" name="ref" class="form-control" placeholder="{#LNG_6735#}" autocomplete="off" data-ng-model="objPaymentAdd.strReference"/>
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">{#LNG_6732#}*</label>
                        <input type="text" class="form-control" placeholder="{#LNG_6732#}" autocomplete="off" data-ng-model="objPaymentAdd.strCustomerName" data-ng-keyup="completeCustomerName(objPaymentAdd.strCustomerName)" required/>
                        <select name="customer" data-ng-if="customerList.length > 0" data-ng-change = "listInvoices(objPaymentAdd.intCustomerID)" data-ng-model="objPaymentAdd.intCustomerID" data-ng-options="customer.intID as customer.strName for customer in customerList" class="form-control" required>
                            <option value="">{#LNG_6755#}</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.customer.$touched && addForm.customer.$invalid">{#LNG_6753#}</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">{#LNG_6737#}*</label>
                        <input type="text" name="amount" class="form-control" placeholder="{#LNG_6737#}" autocomplete="off" data-ng-model="objPaymentAdd.decAmount" required/>
                        <small class="help-block font-red" data-ng-show="addForm.amount.$touched && addForm.amount.$invalid">{#LNG_6753#}</small>
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">{#LNG_6731#}*</label>
                        <select name="method" data-ng-model="objPaymentAdd.intPaymentMethodID" data-ng-options="paymentMethod.intID as paymentMethod.strType for paymentMethod in arrPaymentMethodAdd" class="form-control" required>
                            <option value="">{#LNG_6731#}</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.method.$touched && addForm.method.$invalid">{#LNG_6753#}</small>
                    </div> 
                </div>    
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">{#LNG_6734#}*</label>
                        <select name="user" data-ng-model="objPaymentAdd.intCreatedByUserID" data-ng-options="createdBy.intID as createdBy.strUserName for createdBy in arrUsers" class="form-control" required>
                            <option value="">{#LNG_6734#}</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.user.$touched && addForm.user.$invalid">{#LNG_6753#}</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">{#LNG_6736#}*</label>
                        <input type="text" class="form-control input-fixed input-daterange" datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" data-start-view="2" placeholder="{#LNG_6736#}" data-ng-model="objPaymentAdd.dtDate"> 
                    </div>
                    <small class="help-block font-red" data-ng-show="addForm.date.$touched && addForm.date.$invalid">{#LNG_6753#}</small>
                </div>
            </div>

        </form>  
    </div>
    <div class="modal-footer"> 
        <button type="button" data-ng-click="add()" class="btn green-jungle"  ng-disabled="addForm.$invalid">{#LNG_6751#}</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">{#LNG_6748#}</button>
    </div>  
</div>
<div id="dlgInvoices" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="560">
<div class="modal-header" >
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-money "></i> {#LNG_6759#} </h3>
</div>
<div class="modal-body" style="margin: 0 auto;">
    <div class="portlet-body">
        <div class="table-responsive">
            <table class="table table-condensed table-light table-hover">
                <thead>
                    <tr>
                        <th>{#LNG_6760#}</th>
                        <th>{#LNG_6763#}</th>
                        <th style ="text-align:right">{#LNG_6762#}</th> 
                        <th width="15%" ></th>
                    </tr>
                </thead>
                <tbody>
                    <tr data-ng-repeat="invoice in arrInvoice">
                        <td>{{invoice.fldInvoiceNumber}}</td>  
                        <td>{{invoice.fldDueDate}}</td>
                        <td><div class="pull-right">{{invoice.fldPaymentAmount}}</div></td>
                        <td class="icon" style ="text-align:right">
                            <a href="index.php?module=inv&page=Invoice&action=View&invoice_id={{invoice.pfInvoiceID}}" class="btn btn-xs yellow-saffron"  title="{#LNG_6764#}"><i class="fa fa-search-plus"></i> <span class="visible-lg-inline-block"></span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div> 
{/block}
{block name=toolbar}
<!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_ADD,$_UserPermission)}{/if}-->
<div class="btn-group">
    <a data-toggle="modal" data-target="#dlgAdd" class="btn btn-fit-height green-jungle" title="{#LNG_6730#}">
        <i class="fa fa-plus"></i> 
        <span class="visible-lg-inline-block">{#LNG_6730#}</span>
    </a>
</div>
{/block}
{block name=content}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue"><i class="fa fa-filter font-blue"></i> {#LNG_6740#}</div>
                <div class="tools"><a href="#" class="collapse"></a></div>
            </div>
            <div class="portlet-body" style="margin:0 auto!important">
                <form id="frmFilter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <label class="control-label">{#LNG_6732#}</label>
                                <input list="customers" type="text" class="form-control" placeholder="{#LNG_6732#}" autocomplete="off" data-ng-model="objFilter.strCustomerName"/></label>
                                <datalist id="customers">
                                    <option ng-repeat="customer in arrCustomer" value="{{customer.strName}}"></option>    
                                </datalist>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{#LNG_6733#}</label>
                                <input type="text" class="form-control" placeholder="{#LNG_6734#}" autocomplete="off" data-ng-model="objFilter.intPaymentNumber"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{#LNG_6735#}</label>
                                <input type="text" class="form-control" placeholder="{#LNG_6735#}" autocomplete="off" data-ng-model="objFilter.strReference"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{#LNG_6731#}</label>
                                <select data-ng-model="objFilter.intPaymentMethodID" data-ng-options="paymentMethod.intID as paymentMethod.strType for paymentMethod in arrPaymentMethod" data-ng-model="objFilter.intPaymentMethodID" class="form-control">
                                    <option value="">{#LNG_6731#}</option>
                                </select>
                            </div>
                        </div>                 
                    </div>
                    <div class="row">           
                        <div class="col-md-1">
                            <div class="form-group">
                                <label class="control-label">{#LNG_6737#}</label>
                                <select data-ng-model="objFilter.strAmountOperator" class="form-control">
                                    <option value=">">></option>
                                    <option value="<"><</option>
                                    <option value="=">=</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label">&nbsp;</label>
                                <input type="text" class="form-control" placeholder="{#LNG_6737#}" autocomplete="off" data-ng-model="objFilter.decAmount"/>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{#LNG_6734#}</label>
                                <select data-ng-model="objFilter.intCreatedByUserID" data-ng-options="createdBy.intID as createdBy.strUserName for createdBy in arrUsers" data-ng-model="objFilter.intCreatedByUserID" class="form-control">
                                    <option value="">{#LNG_6734#}</option>
                                </select>
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">{#LNG_6758#}</label>
                                <div class="hidden-print input-group input-fixed date-picker input-daterange">
                                    <input type="text" class="form-control input-daterange ng-isolate-scope" datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" data-start-view="2" placeholder="{#LNG_6736#} {#LNG_6744#}" data-ng-model="objFilter.dtDateBefore" style="cursor:context-menu"> 
                                    <span class="input-group-addon"></span>
                                    <input type="text" class="form-control input-daterange" datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" data-start-view="2" placeholder="{#LNG_6736#} {#LNG_6745#}" data-ng-model="objFilter.dtDateAfter" style="cursor:context-menu"> 
                                </div> 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group pull-right">
                                <label class="control-label">&nbsp;</label>
                                <div>
                                    <button type="button" class="btn blue" title="{#LNG_6740#}" data-ng-click="listPayments()"><i class="fa fa-filter"></i> {#LNG_6740#}</button>
                                    <button type="button" class="btn red-thunderbird" title="{#LNG_6741#}" data-ng-click="reset()"><i class="fa fa-refresh"></i> {#LNG_6741#}</button>
                                </div>
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
                    <i style="font-size:20px" class=" font-blue fa fa-money"></i>&nbsp;{#LNG_6742#}
                </div>
                <pagination  data-ng-if="objPayments.arrData.length>0" data-total-items="objPayments.intTotal" data-items-per-page="objPaging.intPageSize" data-ng-model="objPaging.intPageNo" data-max-size="objPaging.intMaxSize" class="pagination-sm" data-boundary-links="true" data-ng-change="nextPage()"></pagination>
            </div>                                                               
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-light table-hover">
                        <thead>
                            <tr>
                                <th>{#LNG_6733#}</th>
                                <th>{#LNG_6732#}</th>
                                <th>{#LNG_6735#}</th>
                                <th>{#LNG_6731#}</th>
                                <th>{#LNG_6734#}</th>
                                <th>{#LNG_6736#}</th>
                                <th style="text-align:right;">{#LNG_6737#}</th> 
                                <th width="10%"></th>
                                <th width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="payment in objPayments.arrData">
                                <td>{{payment.intPaymentNumber}}</td>  
                                <td>{{payment.objCustomer.strName}}</td>
                                <td>{{payment.strReference}}</td>
                                <td>{{payment.objMethod.strType}}</td>
                                <td>{{payment.objUser.strUserName}}</td>
                                <td>{{payment.dtDate}}</td>
                                <td><div style="text-align:right;">{{payment.decAmount}}</div></td>
                                <td class="icon pull-right">
                                    <a data-ng-click="getInvoices(payment.intID)" class="btn btn-xs yellow-saffron" title="{#LNG_6764#}" data-toggle="modal" data-target="#dlgInvoices"><i class="fa fa-search-plus"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                                <td>
                                    <a data-ng-click="remove(payment.intID, $index)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="{#LNG_6747#}"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="objPayments.arrData.length == 0" class="alert alert-warning">
                    <p>{#LNG_6754#}</p>
                </div>
            </div>
        </div>
    </div> 
</div>
{/block}