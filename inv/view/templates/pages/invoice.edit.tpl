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
                                <div class="form-group" > 
                                    <input type="checkbox" data-ng-model="isAuto">  
                                    <label class="control-label" data-ng-init="isAuto=0"> Auto Invoice Number &nbsp </label> 
                                    <input type="text" name="Invoice Number" class="form-control" 
                                        placeholder="Invoice Number" data-ng-model="objInvoice.strInvoiceNumber" 
                                        data-ng-disabled="isAuto" autocomplete="off" />   
                                </div>
                                <div class="form-group"> 
                                    <label class="control-label"> Payment Term </label> 
                                    <select  data-ng-model="objInvoice.objPaymentTerm" 
                                        data-ng-options="objPaymentTerm as objPaymentTerm.strName for objPaymentTerm in arrPaymentTerms"
                                        class="form-control" required>
                                        <option value="">{{objInvoice.objPaymentTerm.strName}}</option>
                                    </select>
                                    <div data-ng-if="objInvoice.objPaymentTerm" class="portlet light portlet-title">
                                        {{objInvoice.objPaymentTerm.strDescription}}</div>
                                </div> 
                            </div>  
                            <div class="col-md-4" >
                                <div class="form-group"> 
                                    <label class="control-label">Customer </label>             
                                    <input id='txtCustomer' data-ng-enter="filter()" type="text" 
                                        data-ng-model="objInvoice.objCustomer" placeholder="Customer Name" 
                                        typeahead="customer as customer.strName for customer in arrCustomers | filter:$viewValue"
                                        class="form-control" autocomplete="off" required>
                                    <div data-ng-if="objInvoice.objCustomer.intID==null&&objInvoice.objCustomer!=null" class="alert-warning">
                                        <p>Customer must select from the list</p> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Issue Date</label>
                                    <input type="date" name="IssueDate"   class="form-control" 
                                       data-ng-disabled="true" placeholder="Issue Date" data-ng-model="objInvoice.dtIssueDate"
                                        autocomplete="off" required/>
                                </div>
                            </div> 
                            <div class="col-md-4">

                                <div class="form-group">
                                    <label class="control-label">Reference </label> 
                                    <input type="text" name="Reference" class="form-control" 
                                        placeholder="Reference" data-ng-model="objInvoice.strReference" autocomplete="off" />
                                </div> 
                                <div class="form-group">
                                    <label class="control-label">Due Date</label>
                                    <input type="date" name="DueDate" class="form-control" 
                                        placeholder="Due Date" data-ng-model="objInvoice.dtDueDate" 
                                        data-ng-disabled="objInvoice.dtIssueDate==''"
                                        autocomplete="off" required>
                                    <div data-ng-if="objInvoice.dtDueDate<objInvoice.dtIssueDate" class="alert-warning">
                                        <p>Due should be later than or same to issue date.</p> 
                                    </div>
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
                                        <th>QTY</th> 
                                        <th>Total Amount</th> 
                                        <th>Description</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr data-ng-repeat="objRow in arrRow">
                                        <td class='input-small'>
                                            <select data-ng-model="objRow.strType" 
                                                data-ng-options="objLang.inv.CLS_BLL_INVOICE_ROW_TYPE[row_type] for row_type in arrRowTypes" 
                                                class="form-control" required>
                                                <option value="">Row Type</option>
                                            </select>
                                        </td>
                                        <td class='input-small'>
                                            <select data-ng-disabled="objRow.strType ==''"  
                                                data-ng-model="objRow.objCatalogue.intID" 
                                                data-ng-options="catalogue.intID as catalogue.strName for catalogue in arrCatalogues" 
                                                class="form-control" data-ng-change="getCatalogueInfo($index)">
                                                <option value="">Catalogue</option>
                                            </select>
                                        </td>
                                        <td class='input-small'>
                                            <select data-ng-disabled="objRow.strType!='LNG_ENUM_TAX'" 
                                                data-ng-model="objRow.objTaxType.intID" 
                                                data-ng-options="tax_type.intID as tax_type.strTaxType+' : '+tax_type.intValue for tax_type in arrTaxTypes" 
                                                class="form-control">
                                                <option value="">Tax Type</option>
                                            </select>
                                        </td>
                                        <td class='input-xsmall'>
                                            <input type="text" min='0' step="0.01" name="Price" class="form-control" 
                                                placeholder="Price" data-ng-model="objRow.intUnitPrice" 
                                                data-ng-disabled="objRow.strType =='LNG_ENUM_ITEM' && objRow.objCatalogue ==null" 
                                                autocomplete="off" required/>
                                        </td>
                                        <td class='input-xsmall'>
                                            <input type="text" min='0' name="Quantity" class="form-control" 
                                            placeholder="QTY" data-ng-model="objRow.intQuantity" 
                                            data-ng-disabled="objRow.strType =='LNG_ENUM_ITEM' && objRow.objCatalogue ==null" 
                                            autocomplete="off"required/>
                                        </td>
                                        <td class='input-xsmall'>
                                            <input data-ng-disabled='true' type="text" 
                                                name="TotalPrice" 
                                                class="form-control" placeholder="Total$" 
                                                data-ng-value="objRow.intUnitPrice*objRow.intQuantity" required/>
                                        </td>
                                        <td class='input-xlarge'>            
                                            <input type="text" name="Description" class="form-control input-fixed" 
                                                placeholder="Description" data-ng-model="objRow.strDescription"  
                                                autocomplete="off"/>
                                        </td>
                                        <td class="input-small" style="float:right;">
                                            <a style="margin-top:12px!important" data-ng-click="newRow()" 
                                                data-toggle="modal" class="btn btn-fit-height green-jungle btn-xs" 
                                                title="New Service" responsive>
                                                <i class="fa fa-plus"></i> 
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a style="margin-top:12px!important" data-ng-click="moveRow($index,'UP')" 
                                                ng-disabled="arrRow.length==1" data-toggle="modal" 
                                                class="btn grey-cascade btn-xs" 
                                                title="Move Up">
                                                <i class="fa fa-arrow-up"></i>
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a style="margin-top:12px!important" data-ng-click="deleteRow($index)" 
                                                ng-disabled="arrRow.length==1" 
                                                data-toggle="modal" class="btn red-thunderbird btn-xs" title="Delete Service">
                                                <i class="fa fa-trash-o"></i>
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            &nbsp;&nbsp;
                                            <a style="margin-top:12px!important" data-ng-click="moveRow($index,'DOWN')" 
                                                ng-disabled="arrRow.length==1"
                                                data-toggle="modal" class="btn grey-cascade btn-xs" 
                                                title="Move Down" responsive>
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
                                        <th>Gross Amount</th> 
                                    </tr>
                                </thead>
                                <tbody> 
                                    <tr>
                                        <td>{{getTotalCalculations('Item')}}</td>
                                        <td>{{getTotalCalculations('Price')}}</td>
                                        <td>{{getTotalCalculations('Discount')}}</td>
                                        <td>{{getTotalCalculations('Net')}}</td>
                                        <td>{{getTotalCalculations('Tax')}}</td>
                                        <td>{{getTotalCalculations('Gross')}}</td>                 
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
                                        data-ng-disabled="frmInvoice.$invalid || !validate()" 
                                        data-ng-click="editInvoice()">Save</button>
                                        <button type="button" class="btn btn-danger" data-dismiss="modal" 
                                        data-ng-click="cancelEditInvoice()">Cancel</button>
                                    </div>
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