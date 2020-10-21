{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="Invoice" 
{/block}
{block name=style}{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/invoice.list.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/enum.js?ver={$smarty.const.VERSION}"></script>
{/block}

{block name=page_title}
<h1>Invoices</h1>
{/block}

{block name=dialog}

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" 
    data-width="500" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> Delete Invoice</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this element?</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removeInvoice()" class="btn red-thunderbird" 
                data-dismiss="modal">DELETE</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>

<div id="dlgEditStatus" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" 
    style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-edit"></i> Change Status</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <div class="form-group"> 
                    <label class="control-label" >Are you sure you want to change the status to 
                        <b>{{objLang.inv.CLS_BLL_INVOICE_STATUS[objNewStatus.Status]}}</b> ?</label>
                </div>

            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="updateStatus()" class="btn green-jungle" 
                data-dismiss="modal">Change</button>
            <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>
{/block}

{block name=toolbar}
{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_ADD,$_UserPermission)}
<div class="btn-group">
    <a href="index.php?module=inv&page=Invoice&action=Add" class="btn btn-fit-height green-jungle" 
        title="Add Invoice">
        <i class="fa fa-plus"></i> 
        <span class="visible-lg-inline-block">Invoice</span>
    </a>
</div>
{/if}
{/block}
{block name=content}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue">
                    <i class="fa fa-filter font-blue"></i> 
                    Filter
                </div>
                <div class="tools"><a href="#" class="collapse"></a></div>
            </div>
            <div class="portlet-body">
                <form id="frmFilter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" 
                                    name="txtInvoiceNumber" data-ng-model="objFilter.strInvoiceNumber" 
                                    placeholder="Invoice Number" class="form-control"/>
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" name="txtReference" 
                                    data-ng-model="objFilter.strReference" placeholder="Invoice Reference" 
                                    class="form-control"/>
                            </div>
                        </div>                            
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <input list="customers" type="text" data-ng-enter="filter()" 
                                    name="txtCustomer" data-ng-model="objFilter.strCustomerName" 
                                    placeholder="Customer Name" class="form-control"/>

                                <datalist id="customers">
                                    <option ng-repeat="customer in arrCustomers" value="{{customer.strName}}"></option>
                                </datalist>
                            </div>
                        </div>    
                        <div class="col-md-3 form-group">
                            <div   class="hidden-print input-group input-fixed date-picker input-daterange">
                                <input type="text" id="txtFilterDateFrom" name="txtFilterDateFrom" 
                                    class="form-control input-daterange ng-pristine ng-isolate-scope ng-invalid ng-invalid-required ng-touched" 
                                    datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" 
                                    data-start-view="2" placeholder="Issue Date From" 
                                    data-ng-model="objFilter.dtIssueDateFrom" required="required"> 
                                <span class="input-group-addon">to</span> 
                                <input type="text" id="txtFilterDateTo" name="txtFilterDateTo" 
                                    class="form-control input-daterange ng-pristine ng-untouched ng-isolate-scope ng-invalid ng-invalid-required" datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" 
                                    data-start-view="2" placeholder="Issue Date To" 
                                    data-ng-model="objFilter.dtIssueDateTo" required="required">
                            </div> 
                        </div>
                    </div>
                    <div class='row'>
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <select data-ng-model="objFilter.intPaymentTermID" 
                                    data-ng-options="paymentterm.intID as paymentterm.strName for paymentterm in arrPaymentTerms" 
                                    class="form-control">
                                    <option value="">Payment Term</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <select data-ng-model="objFilter.intStatusID" 
                                    data-ng-options="status.intID as objLang.inv.CLS_BLL_INVOICE_STATUS[status.strStatus] for status in arrStatus" 
                                    class="form-control">
                                    <option value="">Status</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <select data-ng-model="objFilter.intCreatedByUserID" 
                                    data-ng-options="user.intID as user.strDisplayName for user in arrUsers" 
                                    class="form-control">
                                    <option value="">User</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 form-group">
                            <div class="hidden-print input-group input-fixed date-picker input-daterange">
                                <input type="text" id="txtFilterDateFrom" name="txtFilterDateFrom" 
                                    class="form-control input-daterange ng-pristine ng-isolate-scope ng-invalid ng-invalid-required ng-touched" 
                                    datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" data-start-view="2" placeholder="Due Date From" data-ng-model="objFilter.dtDueDateFrom" required="required"> 
                                <span class="input-group-addon">to</span> 
                                <input type="text" id="txtFilterDateTo" name="txtFilterDateTo" 
                                    class="form-control input-daterange ng-pristine ng-untouched ng-isolate-scope ng-invalid ng-invalid-required" 
                                    datetimepicker="" data-date-format="yyyy-mm-dd" data-min-view="2" data-start-view="2" 
                                    placeholder="Due Date To" data-ng-model="objFilter.dtDueDateTo" required="required">
                            </div> 
                        </div>
                    </div>       
                    <div class="row">

                        <div class="col-md-12">
                            <div classclass="col-md-3 "> 
                                <input type="checkbox" data-ng-model="objFilter.boolShowOverdue" 
                                    data-ng-checked="objFilter.boolShowOverdue"/> 
                                <span>Show Due date Invoices</span>
                            </div>
                            <div class="form-group pull-right">
                                <button type="button" class="btn blue" title="{#LNG_6690#}" 
                                    data-ng-click="filter()"><i class="fa fa-filter"></i> Filter</button>
                                <button type="button" class="btn red-thunderbird" title="{#LNG_6694#}"
                                    data-ng-click="reset()"><i class="fa fa-refresh"></i> Reset</button>
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
                    <i style="font-size:15px" class=" font-blue fa fa-paperclip"></i>&nbsp;Invoices
                </div>
                <pagination  data-ng-if="objInvoices.intTotal>20" data-total-items="objInvoices.intTotal"
                    data-items-per-page="objPaging.intPageSize" data-ng-model="objPaging.intPageNo" 
                    data-max-size="objPaging.intMaxSize"
                    class="pagination-sm" data-boundary-links="true" data-ng-change="nextPage()"></pagination>
            </div>                                                               
            <div class="portlet-body">
                <div data-ng-if="objInvoices.intTotal > 0" class="table-responsive">
                    <table class="table table-condensed table-hover">
                        <thead>
                            <tr>
                                <th>Invoice#</th>
                                <th>IssueDate</th> 
                                <th>DueDate</th> 
                                <th>Customer</th>
                                <th>Gross Amount</th>
                                <th>Total Payment</th>  
                                <th>Remaining</th> 
                                <th>User</th>
                                <th>Status</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr  style="color:'#37434c'"
                                {literal}
                                data-ng-style="
                                objInvoice.objStatus.strStatus == objEnumStatus.STATUS_VOID &&
                                {'text-decoration':'line-through'}
                                ||objInvoice.objStatus.strStatus == objEnumStatus.STATUS_WRITE_OFF && 
                                {'text-decoration':'line-through'} 
                                ||objInvoice.boolOverDue && 
                                {'background-color':'rgb(247, 79, 95)'}
                                ||objInvoice.objStatus.strStatus == objEnumStatus.STATUS_DRAFT && 
                                {'background-color':'rgb(193, 174, 178)'}  
                                ||objInvoice.objStatus.strStatus == objEnumStatus.STATUS_PAID && 
                                {'background-color':'rgb(159, 247, 156)'} 
                                "                        
                                {/literal} 
                                data-ng-repeat="objInvoice in objInvoices.arrData">
                                <td>{{objInvoice.strInvoiceNumber}}</td>
                                <td>{{objInvoice.dtIssueDate | date: "yyyy-MM-dd"}}</td>
                                <td>{{objInvoice.dtDueDate | date: "yyyy-MM-dd"}}</td>
                                <td>{{objInvoice.objCustomer.strName}} </td>
                                <td class="text-right">{{objInvoice.decGrossAmount | number:2}} </td>
                                <td class="text-right">{{objInvoice.decTotalPayment| number:2}} </td>
                                <td class="text-right">
                                    {{objInvoice.decGrossAmount - objInvoice.decTotalPayment| number:2}}
                                </td>
                                <td>{{objInvoice.objUser.strDisplayName}} </td>
                                <td>
                                    <span data-ng-if="isStatusDisabled(objInvoice.objStatus.strStatus)">
                                        <i class="fa fa-lock"></i> 
                                        {{objLang.inv.CLS_BLL_INVOICE_STATUS[objInvoice.objStatus.strStatus]}} 
                                    </span>
                                    <a href="#" data-ng-if="!isStatusDisabled(objInvoice.objStatus.strStatus)"
                                        data-ng-click="changeStatus($index)" data-toggle="modal" data-target="#dlgEditStatus"  title="Change Status">
                                        <i  class="fa fa-unlock"></i>
                                        {{objLang.inv.CLS_BLL_INVOICE_STATUS[objInvoice.objStatus.strStatus]}} 
                                    </a>
                                </td>
                                <td class="icon">
                                    {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_VIEW,$_UserPermission)}
                                    <a href="index.php?module=inv&page=Invoice&action=View&invoice_id={{objInvoice.intID}}"
                                        class="btn btn-xs yellow-saffron" style="cursor: pointer;" 
                                        title="View"><i class="fa fa-search-plus"></i> 
                                        <span class="visible-lg-inline-block"></span></a>
                                    {/if}
                                    {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_EDIT,$_UserPermission)}
                                    <a href="index.php?module=inv&page=Invoice&action=Edit&invoice_id={{objInvoice.intID}}"
                                        class="btn btn-xs blue-dark" style="cursor: pointer;" 
                                        title="Edit" data-ng-disabled="objInvoice.objStatus.intID!=1">
                                        <i class="fa fa-edit"></i> 
                                        <span class="visible-lg-inline-block"></span></a> 
                                    {/if}
                                    {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_DELETE,$_UserPermission)}
                                    <a href="#" class="btn btn-xs red-thunderbird" style="cursor: pointer;"
                                        title="Delete" data-ng-disabled="objInvoice.objStatus.intID!=1" 
                                        data-ng-click="remove(objInvoice.intID, $index)" data-toggle="modal" 
                                        data-target="#dlgRemove"><i class="fa fa-trash-o"></i> 
                                        <span class="visible-lg-inline-block"></span></a>
                                    {/if}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="objInvoices.intTotal == 0" class="alert alert-warning">
                    <p>No Invoices Found</p>
                </div>
            </div>
        </div>
    </div>
</div>
{/block}