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
<script type="text/javascript" src="{$smarty.const.PATH__JS}/invoice.edit.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/enum.js?ver={$smarty.const.VERSION}"></script>
{/block}

{block name=content}

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue">
                    <i style="font-size:15px" class=" font-blue fa fa-paperclip"></i>&nbsp;Edit Invoice
                </div>
            </div> 
            <div class="portlet-body">
                <div class="modal-body" style="margin: 0 auto;">
                    <form name="frmInvoice">
                        <div class="row">
                            <div class="col-md-4" >
                                <div class="form-group">
                                    <label class="control-label">Reference </label> 
                                    <input type="text" name="Reference" class="form-control" 
                                        placeholder="Reference"   data-ng-model="objInvoice.strReference"  />
                                </div>            
                                <div class="form-group">
                                    <label class="control-label">Issue Date</label>
                                    <input type="date" name="IssueDate"   class="form-control" 
                                        placeholder="Issue Date" data-ng-model="objInvoice.dtIssueDate">
                                </div>
                            </div>  
                            <div class="col-md-4" >
                                <div class="form-group"> 
                                    <label class="control-label">Customer </label>             
                                    <input id='txtCustomer' data-ng-enter="filter()" type="text" 
                                        ng-model="objInvoice.objCustomer" placeholder="Customer Name" 
                                        typeahead="customer as customer.strName for customer in arrCustomers | filter:$viewValue" 
                                        class="form-control" autocomplete="off">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Due Date</label>
                                    <input type="date" name="DueDate" class="form-control" placeholder="Due Date" data-ng-model="objInvoice.dtDueDate">
                                </div>  
                            </div> 
                            <div class="col-md-4" >   
                                <div class="form-group"> 
                                    <label class="control-label"> Payment Term </label> 
                                    <select  data-ng-model="objInvoice.intPaymentTermID" 
                                        data-ng-options="paymentterm.intID as paymentterm.strName for paymentterm in arrPaymentTerms"
                                        class="form-control">
                                        <option value="">Payment Term</option>
                                    </select>
                                </div>
                            </div>   
                        </div>
                        <hr style="margin: 5px 0px 20px 5px;" />
                        <div class="table-responsive">
                            <table  class="table table-condensed table-light table-hover">
                                <thead>
                                    <tr>
                                        <th>Row Type</th>
                                        <th>Catalogue</th> 
                                        <th>Tax Type</th>
                                        <th>Amount</th> 
                                        <th>Quantity</th> 
                                        <th>Total Amount</th> 
                                        <th>Description</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-ng-repeat="objRow in arrRows">

                                        <td class='input-medium'>
                                            <select data-ng-model="objRow.strType" 
                                                data-ng-options="objLang.inv.CLS_BLL_INVOICE_ROW_TYPE[row_type] for row_type in arrRowTypes" 
                                                class="form-control" required>        
                                            </select>
                                        </td>
                                        <td class='input-small'>
                                            <select  data-ng-model="objRow.objCatalogue" 
                                                data-ng-options="catalogue as catalogue.strName for catalogue in arrCatalogues" 
                                                class="form-control" data-ng-change="getCatalogueInfo($index)">
                                                <option  value="">{{objRow.objCatalogue.strName}}</option>
                                            </select>
                                        </td>
                                        <td class='input-small'>
                                            <select data-ng-disabled="objRow.strType!='LNG_ENUM_TAX'" 
                                                data-ng-model="objRow.objTaxType.intID" 
                                                data-ng-options="tax_type.intID as tax_type.strTaxType+' => '+tax_type.intValue for tax_type in arrTaxTypes" 
                                                class="form-control">
                                                <option value="">Tax Type</option>
                                            </select>
                                        </td>
                                        <td class='input-xsmall'>
                                            <input type="text" name="Price" class="form-control" 
                                                placeholder="Price" data-ng-model="objRow.intUnitPrice"  />
                                        </td>
                                        <td class='input-xsmall'>
                                            <input type="text" name="Quantity" class="form-control" 
                                                placeholder="Quantity" data-ng-model="objRow.intQuantity"  />          
                                        </td>
                                        <td class='input-xsmall'>
                                            <input data-ng-disabled='true' type="text" name="TotalPrice" 
                                                class="form-control" placeholder="Total$" 
                                                data-ng-value="objRow.intUnitPrice*objRow.intQuantity"  />
                                        </td>
                                        <td class='input-xlarge'>            
                                            <input type="text" name="Description" class="form-control input-fixed" 
                                                placeholder="Description" data-ng-model="objRow.strDescription"  />
                                        </td>
                                        <td class='input-small' style="float:right;">
                                            <a style="margin-top:12px!important" data-ng-click="newRow()" 
                                                data-toggle="modal" class="btn btn-fit-height green-jungle btn-xs" 
                                                title="New Service">
                                                <i class="fa fa-plus"></i> 
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            &nbsp;&nbsp <a style="margin-top:12px!important" 
                                                data-ng-click="moveRow($index,'UP')" 
                                                ng-disabled="arrRows.length==1" data-toggle="modal" 
                                                class="btn grey-cascade btn-xs" title="Move Up">
                                                <i class="fa fa-arrow-up"></i>
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            &nbsp;&nbsp;<a style="margin-top:12px!important" 
                                                data-ng-click="deleteRow($index)" 
                                                ng-disabled="arrRows.length==1" 
                                                data-toggle="modal" class="btn red-thunderbird btn-xs" 
                                                title="Delete Service">
                                                <i class="fa fa-trash-o"></i> 
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a style="margin-top:12px!important" data-ng-click="moveRow($index,'DOWN')" 
                                                data-toggle="modal" ng-disabled="arrRows.length==1" 
                                                class="btn grey-cascade btn-xs" title="Move Down" responsive>
                                                <i class="fa fa-arrow-down"></i> 
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr style="margin: 5px 0px 20px 5px;" /> 
                        <div class="portlet light portlet-title">
                            <table class="table table-condensed table-light table-hover">
                                <thead>
                                    <tr>
                                        <th>Total Items</th>
                                        <th>Total Price</th> 
                                        <th>Total Discount</th>
                                        <th>Net Amount</th> 
                                        <th>Total Tax</th> 
                                        <th>Cross Amount</th> 
                                    </tr>
                                </thead>
                                <tbody> 
                                    <tr>
                                        <td>{{getTotalCalculations('Item')}}</td>
                                        <td>{{getTotalCalculations('Price')}}</td>
                                        <td>{{getTotalCalculations('Discount')}}</td>
                                        <td>{{getTotalCalculations('Net')}}</td>
                                        <td>{{getTotalCalculations('Tax')}}</td>
                                        <td>{{getTotalCalculations('Grand')}}</td>                 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr style="margin: 5px 0px 20px 5px;" />
                        <div data-ng-if="getTotalCalculations('Gross')<0" class="alert-warning">
                            <p>Invoice gross amount should be positive number.</p> 
                        </div>
                        <div data-ng-if="getTotalCalculations('Net')<0" class="alert-warning">
                            <p>Invoice net amount should be a positive number.</p> 
                        </div>
                        <div data-ng-if="getTotalCalculations('Item')<=0" class="alert-warning">
                            <p>An invoice should have at least one item.</p> 
                        </div>
                        <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="pull-right">
                                    <button type="button" name="btnSave" class="btn btn-success" 
                                        data-ng-disabled="frmInvoice.$invalid || !validate()" " 
                                        data-ng-click="editInvoice()">Save</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal" 
                                        data-ng-click="cancelEditInvoice()">Cancel</button>
                                </div>
                            </div>
                        </div>


                    </form>  
                </div>

            </div>
        </div>
    </div>
</div>

{/block}