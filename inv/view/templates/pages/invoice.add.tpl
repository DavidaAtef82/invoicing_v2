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
<script type="text/javascript" src="{$smarty.const.PATH__JS}/invoice.add.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/enum.js?ver={$smarty.const.VERSION}"></script>
{/block}

{block name=dialog}
<div id="dlgGetNewRowType" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="500" 
    style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-edit"></i> New Row</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="col-md-12" >
                    <div class="form-group">
                        <label class="control-label">Row Type </label> 
                        <select name="RowType" data-ng-model="strRowTypeFrm" 
                            data-ng-options="objLang.inv.CLS_BLL_INVOICE_ROW_TYPE[RowType] for RowType in arrRowTypes" 
                            class="form-control" required="required">
                            <option value="">Row Type</option>
                        </select>
                    </div> 
                    <div class="form-group"> 
                        <select data-ng-hide="strRowTypeFrm != objRowType.ROW_TYPE_ITEM"  
                            name="objCatalogueFrm" data-ng-model="objCatalogueFrm" 
                            data-ng-options="catalogue as catalogue.strName for catalogue in arrCatalogues"
                            class="form-control">
                            <option value="">Item</option>
                        </select> 
                        <select data-ng-hide="strRowTypeFrm != objRowType.ROW_TYPE_TAX" 
                            data-ng-model="objTaxTypeFrm" 
                            data-ng-options="tax_type as tax_type.strTaxType for tax_type in arrTaxTypes" 
                            class="form-control">
                            <option value="">Tax Type</option>
                        </select>
                    </div>           
                    <div data-ng-hide="arrRow.length==0" class="form-group">
                        <label class="control-label">Row Position </label> 
                        <select name="RowPosition" data-ng-model="strRowPosition" 
                            class="form-control">
                            <option value="">Row Position</option>
                            <option value="After">After</option>
                            <option value="Before">Before</option>
                        </select>
                    </div>
                </div>    
            </div>
        </form>
        <div class="modal-footer">
            <button type="button" data-ng-click="newRow(strRowTypeFrm ,objCatalogueFrm , objTaxTypeFrm ,strRowPosition)" 
                ng-disabled="addForm.$invalid || (objCatalogueFrm==null&&objTaxTypeFrm==null)" class="btn green-jungle" 
                data-dismiss="modal">ADD</button>
            <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>
{/block}

{block name=content}

<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue">
                    <i style="font-size:15px" class=" font-blue fa fa-paperclip"></i>&nbsp;Add New Invoice
                </div>
            </div> 
            <div class="portlet-body">
                <div class="modal-body" style="margin: 0 auto;">
                    <form name="frmInvoice">
                        <div class="row">
                            <div class="col-md-4" >           
                                <div class="form-group" > 
                                    <input type="checkbox" data-ng-model="isAuto" data-ng-change="getNextInvoiceNumber()">  
                                    <label class="control-label" data-ng-init="isAuto=0"> Auto Invoice Number</label> 
                                    <input type="text" name="Invoice Number" class="form-control" 
                                        placeholder="Invoice Number" data-ng-model="objInvoice.strInvoiceNumber" 
                                        data-ng-disabled="isAuto" autocomplete="off" />   
                                </div>
                                <div class="form-group"> 
                                    <label class="control-label"> Payment Term </label> 
                                    <select  data-ng-model="objInvoice.objPaymentTerm" 
                                        data-ng-options="paymentterm as paymentterm.strName for paymentterm in arrPaymentTerms"
                                        class="form-control" required>
                                        <option value="">Payment Term</option>
                                    </select>
                                    <div data-ng-if="objInvoice.objPaymentTerm" class="portlet light portlet-title">
                                        {{objInvoice.objPaymentTerm.strDescription}}</div>
                                </div> 
                            </div>  
                            <div class="col-md-4" >
                                <div class="form-group"> 
                                    <label class="control-label">Customer </label>             
                                    <input id="txtCustomer" data-ng-enter="filter()" type="text" 
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
                                        placeholder="Issue Date" data-ng-model="objInvoice.dtIssueDate" autocomplete="off" required>
                                </div>
                            </div> 
                            <div class="col-md-4">

                                <div class="form-group">
                                    <label class="control-label">Reference </label> 
                                    <input type="text" name="Reference" class="form-control" 
                                        placeholder="Reference"   data-ng-model="objInvoice.strReference" autocomplete="off" />
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
                        <div data-ng-if="arrRow.length>0"  class="table-responsive">
                            <table class="table table-condensed table-light table-hover">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Type</th>
                                        <th></th> 
                                        <th>Amount</th> 
                                        <th>QTY</th> 
                                        <th>Total Amount</th> 
                                        <th>Description</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody data-ng-repeat="objRow in arrRow">
                                    <tr>
                                        <td>
                                            <button data-toggle="modal" data-target="#dlgGetNewRowType" data-ng-click="setCurrentRowIndex($index)" class="btn btn-fit-height blue" title="New Row">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </td>
                                        <td class="input-xxs">{{objLang.inv.CLS_BLL_INVOICE_ROW_TYPE[objRow.strType]}}</td>
                                        <td class="input-small">
                                            <b data-ng-if="objRow.strType==objRowType.ROW_TYPE_ITEM">
                                                {{objRow.objCatalogue.strName}}</b>

                                            <b data-ng-if="objRow.strType==objRowType.ROW_TYPE_TAX">
                                                {{objRow.objTaxType.strTaxType}}</b>
                                        </td>
                                        <td>
                                            <input type="text" min="0" name="Price" class="form-control" 
                                                placeholder="Price" data-ng-model="objRow.intUnitPrice | number:2" 
                                                autocomplete="off" required="required"/>
                                        </td>
                                        <td>
                                            <input type="number" min="0" name="Quantity" class="form-control" 
                                                placeholder="QTY" data-ng-model="objRow.intQuantity" 
                                                autocomplete="off" required/>
                                        </td>
                                        <td class="input-xsmall">
                                            <input data-ng-disabled="true" type="text" 
                                                name="TotalPrice" 
                                                class="form-control" placeholder="Total$" 
                                                data-ng-value="objRow.intUnitPrice*objRow.intQuantity" required/>
                                        </td>
                                        <td class="input-xlarge">            
                                            <input type="text" name="Description" class="form-control input-fixed" 
                                                placeholder="Description" data-ng-model="objRow.strDescription"  
                                                autocomplete="off"/>
                                        </td>
                                        <td class="input-group-btn">
                                            <a data-ng-click="newSubRow($index)" 
                                                data-toggle="modal" class="btn green-jungle btn-xs" 
                                                title="New Sub Row"
                                                data-ng-disabled="objRow.strType != objRowType.ROW_TYPE_ITEM" responsive>
                                                <i class="fa fa-plus"></i> 
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            <button type="button"  data-ng-click="deleteRow($index)" 
                                                data-toggle="modal" class="btn red-thunderbird btn-xs" title="Delete Row">
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                            <a  data-ng-click="moveRow($index,'UP')" 
                                                ng-disabled="arrRow.length==1" data-toggle="modal" 
                                                class="btn grey-cascade btn-xs" 
                                                title="Move Up">
                                                <i class="fa fa-arrow-up"></i>
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            <a  data-ng-click="moveRow($index,'DOWN')" 
                                                ng-disabled="arrRow.length==1"
                                                data-toggle="modal" class="btn grey-cascade btn-xs" 
                                                title="Move Down" responsive>
                                                <i class="fa fa-arrow-down"></i> 
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- sub row  -->
                                    <tr data-ng-repeat="objSubRow in objRow.arrSubRows">
                                        <td></td>
                                        <td class="input-small">
                                            <select name="SubRowType" data-ng-init="objSubRow.strType=objRowType.ROW_TYPE_TAX" 
                                                data-ng-model="objSubRow.strType" 
                                                class="form-control" required> 
                                                <option data-ng-value="objRowType.ROW_TYPE_TAX" ng-selected="true">Tax</option>
                                                <option data-ng-value="objRowType.ROW_TYPE_DISCOUNT">Discount</option>
                                            </select>
                                        </td>
                                        <td class="input-small">
                                            <select data-ng-if="objSubRow.strType == objRowType.ROW_TYPE_TAX" 
                                                data-ng-model="objSubRow.objTaxType" 
                                                data-ng-options="tax_type as tax_type.strTaxType for tax_type in arrTaxTypes" 
                                                data-ng-change="getSubRowInfo($parent.$parent.$index,$parent.$index)"
                                                class="form-control">
                                                <option value="">{{objSubRow.objTaxType.strTaxType}}</option>
                                            </select>
                                            <input data-ng-if="objSubRow.strType == objRowType.ROW_TYPE_DISCOUNT"
                                            type="text"class="form-control" placeholder="Discount" 
                                            data-ng-disabled="true" autocomplete="off" required="required"/>
                                        </td>
                                        <td class="input-xsmall">
                                            <input type="text" name="Price" class="form-control" 
                                                placeholder="Price" data-ng-model="objSubRow.intUnitPrice | number:2" 
                                                autocomplete="off" required="required"/>
                                        </td>
                                        <td class="input-xsmall">
                                            <input type="number" name="Quantity" class="form-control" 
                                                placeholder="QTY" data-ng-model="objSubRow.intQuantity" 
                                                autocomplete="off" required="required"/>
                                        </td>
                                        <td class="input-xsmall">
                                            <input type="number" name="TotalPrice" data-ng-disabled="true"
                                                class="form-control" placeholder="Total$" 
                                                data-ng-value="objSubRow.intUnitPrice*objSubRow.intQuantity" 
                                                required="required"/>
                                        </td>
                                        <td class="input-xlarge">            
                                            <input type="text" name="Description" class="form-control input-fixed" 
                                                placeholder="Description" data-ng-model="objSubRow.strDescription"  
                                                autocomplete="off"/>
                                        </td>
                                        <td class="input-group-btn">
                                            <a data-ng-click="deleteSubRow($parent.$index,$index)" 
                                                data-toggle="modal" class="btn red-thunderbird btn-xs" title="Delete Sub Row">
                                                <i class="fa fa-trash-o"></i>
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            <a data-ng-click="moveSubRow($parent.$index,$index,'UP')" 
                                                ng-disabled="objRow.arrSubRows.length==1" data-toggle="modal" 
                                                class="btn grey-cascade btn-xs" 
                                                title="Move Up">
                                                <i class="fa fa-arrow-up"></i>
                                                <span class="visible-lg-inline-block"></span>
                                            </a>
                                            <a data-ng-click="moveSubRow($parent.$index,$index,'DOWN')" 
                                                ng-disabled="objRow.arrSubRows.length==1"
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

                        <div data-ng-if="arrRow.length==0" class="alert alert-info font-lg">
                            <strong>Good to Go<i class="fa fa-exclamation"></i></strong>
                            <br/>Let's add an invoice element.
                            <button class="btn btn-info btn-fit-height" data-toggle="modal" data-ng-click="setCurrentRowIndex(0)" data-target="#dlgGetNewRowType">Add Element</button>
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
                                        <td>{{getTotalCalculations('Item') | number:2}}</td>
                                        <td>{{getTotalCalculations('Price') | number:2}}</td>
                                        <td>{{getTotalCalculations('Discount') | number:2}}</td>
                                        <td>{{getTotalCalculations('Net') | number:2}}</td>
                                        <td>{{getTotalCalculations('Tax') | number:2}}</td>
                                        <td >{{getTotalCalculations('Gross') | number:2}}</td>                 
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <hr style="margin: 5px 0px 20px 5px;" />

                        <div data-ng-if="getTotalCalculations('Item')<=0" class="alert-warning">
                            <p>An invoice should have at least one item.</p> 
                        </div>
                        <div data-ng-if="getTotalCalculations('Net')<0" class="alert-warning">
                            <p>Invoice net amount should be a positive number.</p> 
                        </div>
                        <div data-ng-if="getTotalCalculations('Gross')<0" class="alert-warning">
                            <p>Invoice gross amount should be positive number.</p> 
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="pull-right">
                                        <button type="button" name="btnSave" class="btn btn-success" 
                                            data-ng-disabled="frmInvoice.$invalid || !validate()" 
                                            data-ng-click="addInvoice(false)">
                                            ADD</button>
                                        <button type="button" name="btnSave" class="btn btn-success" 
                                            data-ng-disabled="frmInvoice.$invalid || !validate()" 
                                            data-ng-click="addInvoice(true)">
                                            ADD & CONTINUE</button>
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