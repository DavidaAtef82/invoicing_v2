{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"} {block name=app} data-ng-app="INV" {/block} {block name=controller} data-ng-controller="Customer" {/block} {block name=style}{/block} {block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/customer.view.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/enum.js?ver={$smarty.const.VERSION}"></script>
{/block} 
{block name=page_title}
<h1>Customer Details</h1>
{/block}  
{block name=content}

<div class="row">

    <div data-ng-if="true" class="table-responsive">
        <div class="col-md-12">
            <div class="row profile">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title">
                            <div class="caption ng-binding">
                                {{objCustomer.strName}}
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <table class="table light">
                                    <tbody>
                                        <tr>
                                            <td rowspan="3" style="min-width:100px; max-width:150px"></td>
                                            <th>Customer ID</th>
                                            <td class="ng-binding">{{objCustomer.intID}}</td>
                                            <th> Address </th>
                                            <td class="ng-binding"> {{objCustomer.strAddress}}</td>
                                            <th width="12%"> Phone </th>
                                            <td class="ng-binding"> {{objCustomer.strPhone}} </td>
                                            <td rowspan="3" style="min-width:150px; max-width:150px"> </td>
                                        </tr>
                                        <tr>
                                            <th> Email</th>
                                            <td width="13%" class="ng-binding">{{objCustomer.strEmail}}</td>
                                            <th>City </th>
                                            <td class="ng-binding">{{objCustomer.objCity.strCity}} </td>
                                            <th>Country</th>
                                            <td class="ng-binding">{{objCustomer.objCity.objCountry.strCountry}}</td>
                                        </tr>
                                        <tr class="alert-info">
                                            <th> Total Transactions</th>
                                            <td width="13%" class="ng-binding">{{intTotalTransactions | number:2}}</td>
                                            <th>Total Payments </th>
                                            <td class="ng-binding">{{intTotalPayments | number:2}} </td>
                                            <th>Total Remaining</th>
                                            <td class="ng-binding">{{intTotalRemaining | number:2}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="col-md-12">
                <div class="portlet light tabbable">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-tabs">
                            <li class="">
                                <a href="#tbInvoices" data-toggle="tab">Invoices</a>
                            </li>
                            <li class="">
                                <a href="#tbPayments" data-toggle="tab">Payments  </a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">

                            <div class="tab-pane ng-scope" id="tbInvoices">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Invoices</h4>
                                        <div class="table-responsive ng-scope">
                                            <table class="table table-condensed table-light table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Invoice# </th>
                                                        <th>IssueDate</th>
                                                        <th>DueDate</th>
                                                        <th class="text-right ng-binding">Total Amount</th>
                                                        <th class="text-right ng-binding">Total Payment</th>
                                                        <th class="text-right ng-binding">Remaining</th>
                                                        <th>Status</th>
                                                        <th>&nbsp;</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr data-ng-repeat="objInvoice in objCustomer.arrInvoice">
                                                        <td>{{objInvoice.strInvoiceNumber}}</td>
                                                        <td>{{objInvoice.dtIssueDate | date: "yyyy-MM-dd"}}</td>
                                                        <td>{{objInvoice.dtDueDate | date: "yyyy-MM-dd"}}</td>
                                                        <td class="text-right ng-binding">{{objInvoice.decTotalAmount -- objInvoice.decTotalTax - objInvoice.decTotalDiscount | number:2}} </td>
                                                        <td class="text-right ng-binding">{{objInvoice.decTotalPayment| number:2}} </td>
                                                        <td class="text-right ng-binding">
                                                            {{objInvoice.decTotalAmount -- objInvoice.decTotalTax - objInvoice.decTotalDiscount - objInvoice.decTotalPayment| number:2}}
                                                        </td>
                                                        <td>
                                                            <span>
                                                                {{objLang.inv.CLS_BLL_INVOICE_STATUS[objInvoice.objStatus.strStatus]}} 
                                                            </span>
                                                        </td>
                                                        <td class="icon">
                                                            {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_VIEW,$_UserPermission)}
                                                            <a href="index.php?module=inv&page=Invoice&action=View&invoice_id={{objInvoice.intID}}" class="btn btn-xs yellow-saffron" style="cursor: pointer;" title="View"><i class="fa fa-search-plus"></i> 
                                                                <span class="visible-lg-inline-block"></span></a> {/if} {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_EDIT,$_UserPermission)}
                                                            {/if} 
                                                        </td>
                                                    </tr>

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane ng-scope" id="tbPayments">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Payments</h4>
                                        <div class="table-responsive ng-scope">
                                            <table class="table table-condensed table-light table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Payment#</th>
                                                        <th>Reference#</th>
                                                        <th>Payment Method</th>
                                                        <th>Date</th>
                                                        <th style="width:5%">Amount</th> 
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr data-ng-repeat="payment in objCustomer.arrPayment">
                                                        <td>{{payment.intPaymentNumber}}</td>  
                                                        <td>{{payment.strReference}}</td>
                                                        <td>{{payment.objMethod.strType}}</td>
                                                        <td>{{payment.dtDate}}</td>
                                                        <td class="text-right ng-binding" style="width:5%">{{payment.decAmount}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div data-ng-if="false" class="alert alert-warning">
        <p>No Customer Found</p>
    </div>

</div>
{/block}