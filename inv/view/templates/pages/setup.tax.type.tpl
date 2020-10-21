{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}

{block name=title}Tax Types{/block}
{block name=lang}{/block}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="TaxType" 
{/block}
{block name=style}
<link rel="stylesheet" href="{$smarty.const.PATH__CSS}/setup.tax.type.css?ver={$smarty.const.VERSION}"></link>
{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/enum.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/setup.tax.type.js?ver={$smarty.const.VERSION}"></script>
{/block}
{block name=dialog}
<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important;width:500px!important ">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-plus"></i> {#LNG_6774#} {#LNG_6775#} </h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">

                        <label class="control-label">{#LNG_6776#}*</label>
                        <select name="taxType" data-ng-model="objTaxAdd.strType" 
                            data-ng-options="objLang.inv.CLS_BLL_TAX_TYPE[type] for type in arrTaxType" 
                            class="form-control" required>
                            <option value="">{#LNG_6776#}*</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.taxType.$touched && addForm.taxType.$invalid">*{#LNG_6777#}</small>  
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label">{#LNG_6775#}*</label>   
                        <input type="text" name="taxType" class="form-control" placeholder="{#LNG_6775#}" autocomplete="off" data-ng-model="objTaxAdd.strTaxType" required />
                        <small class="help-block font-red" data-ng-show="addForm.taxType.$touched && addForm.taxType.$invalid">*Field Required</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12">
                        <label class="control-label">Value*</label>
                        <input type="text" name="inTaxValue" class="form-control" placeholder="" autocomplete="off" data-ng-model="objTaxAdd.intValue" required />
                        <small class="help-block font-red" data-ng-show="addForm.inTaxValue.$touched && addForm.inTaxValue.$invalid">*Field Required</small>
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
<div id="dlgEdit" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-edit"></i>Edit Tax Type</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    <label class="control-label">Type*</label>
                    <!--<input type="text" name="taxType" class="form-control" autocomplete="off" data-ng-model="objTaxEdit.strType" />-->
                    <select name="taxType" data-ng-model="objTaxEdit.strType" 
                        data-ng-options="objLang.inv.CLS_BLL_TAX_TYPE[type] for type in arrTaxType" class="form-control" required>
                        <option value="">Tax Type</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    <label class="control-label">Tax Type*</label>   
                    <input type="text" name="textTaxType" class="form-control" autocomplete="off" data-ng-model="objTaxEdit.strTaxType" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group">
                <div class="col-md-12">
                    <label class="control-label">Value*</label>
                    <input type="text" name="inTaxValue" class="form-control" placeholder="" autocomplete="off" data-ng-model="objTaxEdit.intValue" />
                </div>
            </div>
        </div>
    </div> 
    <div class="modal-footer">
        <button type="button" data-ng-click="editTax()" class="btn green-jungle" data-dismiss="modal">Save</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">Cancel</button>
    </div>  
</div>
<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class="font-red-thunderbird fa fa-trash"></i> Delete Tax Type</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-6" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this tax type?</label>
            </div>    
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="removeTax()" class="btn red-thunderbird" data-dismiss="modal">Delete</button>
        <button type="button" class="btn green-jungle" data-dismiss="modal">Cancel</button>
    </div>
</div>
{/block}

{block name=page_title}
<h1>Tax Types <small>Management</small></h1>
{/block}

{block name=toolbar}
<div class="btn-group pull-right">      
    <a class="btn green-jungle btn-fit-height" data-toggle="modal" data-target="#dlgAdd">
        <i class="fa fa-plus"></i> <span class="visible-lg-inline-block">Tax Type</span>
    </a>
</div>
{/block}

{block name=content}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue">
                    <span class="fa fa-money"></span> Tax Types
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-responsive">
                    <table class="table table-condensed table-light table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>    
                                <th>Tax Type</th>
                                <th>Type</th>    
                                <th>Value</th>
                                <th></th>    
                            </tr>    
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="objTax in arrTaxTypes">
                                <td>{{objTax.intID}}</td>
                                <td>{{objTax.strTaxType}}</td>                                 
                                <td>{{objLang.inv.CLS_BLL_TAX_TYPE[objTax.strType]}}</td>                            
                                <td>{{objTax.intValue}}</td>                                 
                                <td width="10%" class="icon">
                                    <a data-ng-click="edit(objTax ,$index)" data-toggle="modal" data-target="#dlgEdit" class="btn blue-dark btn-xs" title="Edit"><i class="fa fa-edit"></i></a>
                                    <a data-ng-click="remove(objTax.intID)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="Delete"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="note note-info" data-ng-if="mode==0">
                    No terms found!
                </div>                
            </div>
        </div>
    </div>
</div>
    
{/block}