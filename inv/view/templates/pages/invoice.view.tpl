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
<script type="text/javascript" src="{$smarty.const.PATH__JS}/invoice.view.js?ver={$smarty.const.VERSION}"></script>
{/block}

{block name=page_title}
<h1>Invoice Details</h1>
{/block}

{block name=dialog}
<div id="dlgGenrateLink" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="560">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px;" class=" font-blue fa fa-link"></i><span style="margin-left:12px;">{#LNG_6766#}</span></h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <a href="{{strGeneratedLink}}" style="text-decoration: underline; font-size:17px; word-wrap: break-word;">{{strGeneratedLink}}</a>   
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn red-flamingo pull-right" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>
<div id="dlgViewPayments" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-vimeo"></i>Payments</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <table class="table table-condensed table-light table-hover">
                    <thead>
                        <tr>
                            <th>Payment Number</th>
                            <th>Date</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr data-ng-repeat="payment in objInvoice.arrInvoicePayment">
                            <td>{{payment.intPaymentNumber}}</td>  
                            <td>{{payment.dtDate}}</td>
                            <td>{{payment.decAmount}}</td>
                        </tr>
                    </tbody>
                </table>    
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" class="btn red-flamingo" data-dismiss="modal">Cancel</button>
        </div>
    </div>

</div>

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> Delete Invoice</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this invoice?</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removeInvoice()" class="btn red-thunderbird" data-dismiss="modal">DELETE</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>
{/block}
{block name=toolbar}
<div class="btn-group">
    {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_EDIT,$_UserPermission)}
    <a href="index.php?module=inv&page=Invoice&action=Edit&invoice_id={{objInvoice.intID}}" 
        class="btn btn-fit-height blue-dark" style="cursor: pointer;" title="Edit" data-ng-if="objInvoice.objStatus.intID==1">
        <i class="fa fa-edit"></i> <span class="visible-lg-inline-block">Invoice</span></a> 
    {/if}
    {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_DELETE,$_UserPermission)}
    <a href="#" class="btn btn-fit-height red-thunderbird" style="cursor: pointer;" title="Delete" 
        data-ng-if="objInvoice.objStatus.intID==1" data-ng-click="remove(objInvoice.intID)" data-toggle="modal" 
        data-target="#dlgRemove"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block">Invoice</span></a>
    {/if}
    <div class="btn-group">
        <a data-toggle="modal" data-target="#dlgGenrateLink" class="btn btn-fit-height green-jungle" 
            title="{#LNG_6767#}{#LNG_6766#}" data-ng-click="generateLink(objInvoice.intID)" data-ng-disabled="!boolGenerateLink">
            <i class="fa fa-plus"></i> 
            <span class="visible-lg-inline-block">{#LNG_6767#} {#LNG_6766#}</span>
        </a>
    </div>
</div>
{/block}
{block name=content}
<div class="row">
<div class="col-md-12">
    <div class="portlet light">
        <div class="portlet-body">
            <div class="row">
                <div class="col-md-4"> 
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption"> 
                                <span class="caption-subject bold font-blue-hoki uppercase">
                                    {{objInvoice.strInvoiceNumber}} 
                                </span>
                                <span class="caption-helper">invoice info</span>
                            </div> 
                        </div>
                        <div class="portlet-body">
                            <div class="profile-usermenu" style="margin-top:0"> 
                                <ul class="nav"> 
                                    <li> 
                                        <a>
                                            Reference : 
                                            <span class="ng-binding text-info"> 
                                                {{objInvoice.strReference}}
                                            </span>
                                        </a>
                                    </li> 
                                    <li> 
                                        <a>
                                            Issue Date : 
                                            <span class="ng-binding text-info"> 
                                                {{objInvoice.dtIssueDate | date:'yyyy-MM-dd'}}
                                            </span>
                                        </a>
                                    </li>
                                    <li> 
                                        <a>
                                            Due Date : 
                                            <span class="ng-binding text-info"> 
                                                {{objInvoice.dtDueDate | date:'yyyy-MM-dd'}}
                                            </span>
                                        </a>
                                    </li>
                                    <li> 
                                        <a>
                                            Creation Time : 
                                            <span class="ng-binding text-info"> 
                                                {{objInvoice.tsCreatedOnTimestamp | date:'yyyy-MM-dd HH:mm:ss'}}
                                            </span>
                                        </a>
                                    </li>    
                                    <li> 
                                        <a>
                                            User : 
                                            <span class="ng-binding text-info"> 
                                                {{objInvoice.objUser.strDisplayName}} 
                                            </span>
                                        </a>
                                    </li>                           
                                    <li> 
                                        <a>
                                            Payment Term : 
                                            <span class="ng-binding text-info"> 
                                                {{objInvoice.objPaymentTerm.strDescription}} 
                                            </span>
                                        </a>
                                    </li>                               
                                    <li> 
                                        <a>
                                            Status : 
                                            <span class="ng-binding text-info"> 
                                                {{objLang.inv.CLS_BLL_INVOICE_STATUS[objInvoice.objStatus.strStatus]}} 
                                            </span>
                                        </a>
                                    </li> 
                                </ul> 
                            </div>
                        </div> 
                    </div> 
                </div> 
                <div class="col-md-8"> 
                    <div class="portlet light"> 
                        <div class="portlet-title"> 
                            <div class="caption"> 
                                <i class="fa fa-user font-blue-hoki"></i> 
                                <span class="caption-subject bold font-blue-hoki uppercase ng-binding">
                                    {{objInvoice.objCustomer.strName}}  
                                </span><br/> 
                                <div class="caption-helper" style="margin-top:10px;margin-left:20px">  
                                    {{objInvoice.objCustomer.strEmail}}<br/> 
                                    {{objInvoice.objCustomer.strAddress}}
                                </div>
                            </div> 
                        </div> 
                        <div class="portlet-body">
                        </div>
                    </div>
                    <div data-ng-if="objInvoice.arrInvoiceRow.length > 0" class="table-responsive">
                        <table class="table table-condensed table-light table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Type</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>TaxType</th> 
                                    <th>Unit Price</th>
                                    <th>Quantity</th> 
                                    <th>Total Price</th>
                                    <th>&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr data-ng-repeat="objRow in objInvoice.arrInvoiceRow"> 
                                    <td>{{$index + 1}} </td>
                                    <td>{{objLang.inv.CLS_BLL_INVOICE_ROW_TYPE[objRow.strType]}}</td>
                                    <td>{{objRow.objCatalogue.strName}}</td>
                                    <td>{{objRow.objCatalogue.strCode}}</td>
                                    <td>{{objRow.objTaxType.strTaxType}} </td>
                                    <td>{{objRow.intUnitPrice | number:2}}</td>  
                                    <td>{{objRow.intQuantity}}</td>  
                                    <td>{{objRow.intUnitPrice * objRow.intQuantity | number : 2}}</td>    
                                </tr>
                            </tbody>                            
                        </table>
                        <hr style="margin: 5px 0px 20px 5px;" />                       
                    </div>
                    <div class="tab-content">
                        <table class="table light">
                            <tbody>
                                <tr> 
                                    <th>Total Items</th> 
                                    <td class="text-right ng-binding">
                                        {{objInvoice.intItemCount}} 
                                    </td> 
                                    <th>
                                        Total Price
                                    </th>
                                    <td class="text-right ng-binding">
                                        {{objInvoice.decTotalAmount | number:2}} 
                                    </td> 
                                    <th>
                                        Total Discount
                                    </th> 
                                    <td class="text-right ng-binding">
                                        {{objInvoice.decTotalDiscount | number:2}} 
                                    </td>
                                </tr>
                                <tr> 
                                    <th>
                                        Net Amount
                                    </th> 
                                    <td class="text-right ng-binding">
                                        {{objInvoice.decNetAmount | number:2}} 
                                    </td> 
                                    <th>
                                        Total Tax
                                    </th> 
                                    <td class="text-right ng-binding">
                                        {{objInvoice.decTotalTax | number:2}} 
                                    </td> 
                                    <th>
                                        Gross Amount
                                    </th> 
                                    <td class="text-right ng-binding">
                                        {{objInvoice.decGrossAmount | number:2}} 
                                    </td> 
                                </tr>              
                                <tr> 
                                    <th>
                                        Total Payment
                                    </th> 
                                    <td class="text-right ng-binding">
                                        {{objInvoice.decTotalPayment | number:2}} 
                                    </td> 
                                    <th>
                                        Due Amount 
                                    </th> 
                                    <td class="text-right ng-binding">
                                        {{(objInvoice.decGrossAmount - objInvoice.decTotalPayment) | number:2}} 
                                    </td>
                                    <th>   </th> 
                                    <td class="ng-binding">
                                        <a data-target="#dlgViewPayments" data-toggle="modal" 
                                            data-ng-if="objInvoice.arrInvoicePayment.length>0" 
                                            class="btn yellow-saffron" style="cursor: pointer;" title="View Payments">
                                            <i class="fa fa-search-plus"></i> 
                                            <span class="visible-lg-inline-block"></span>
                                            View Payments
                                        </a>

                                    </td> 
                                </tr>
                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
            <div  class="alert alert-warning" data-ng-if="objInvoice==null">
                <p>No Invoice Found</p>
            </div>
        </div>
    </div>
</div>

{/block}