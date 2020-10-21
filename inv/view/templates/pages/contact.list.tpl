{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="Contact" 
{/block}
{block name=style}{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/contact.list.js?ver={$smarty.const.VERSION}"></script>
{/block}
{block name=dialog}
<div id="dlgEdit" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-edit"></i> {#LNG_6710#} {#LNG_6705#}</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-12" >
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Name</label>
                            <input type="text" class="form-control" placeholder="Name" autocomplete="off" data-ng-model="objEditContact.strName" data-ng-blur="ValidateUsername()" />
                        </div>

                        <div class="form-group">
                            <label class="control-label">Email</label>
                            <input type="email" class="form-control" placeholder="Email" data-ng-model="objEditContact.strEmail">
                        </div>

                        <div class="form-group">
                            <label class="control-label">Phone</label>
                            <input type="text" class="form-control" placeholder="Phone" data-ng-model="objEditContact.strPhone" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">Customer </label> 
                            <select  data-ng-model="objEditContact.intCustomerID" data-ng-options="customer.intID as customer.strName for customer in arrCustomers" class="form-control">
                            </select>
                        </div>
                        <div class="form-group"> 
                            <label class="control-label">Notes</label>
                            <textarea class="form-control rounded-0" placeholder="Notes" rows="5" data-ng-model="objEditContact.strNotes"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>   
    </div>
    <div class="modal-footer">
        <button type="button"  data-ng-click="editContact()" class="btn green-jungle" data-dismiss="modal">EDIT</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>

    </div>
</div>

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> Delete Contact</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-6" >
                <label class="control-label" >&nbsp;&nbsp; Do you want to delete this element?</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removeContact()" class="btn red-thunderbird" data-dismiss="modal">DELETE</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">CANCEL</button>
        </div>
    </div>
</div>
<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-user-plus"></i> Add Contact</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">Name*</label>
                        <input type="text" name="contactName" class="form-control" placeholder="Name" autocomplete="off" data-ng-model="objAddContact.strName" required />
                        <small class="help-block font-red" data-ng-show="addForm.contactName.$touched && addForm.contactName.$invalid">{#LNG_6717#}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Phone </label> 
                        <input type="text" name="contactPhone" class="form-control" placeholder="Phone"   data-ng-model="objAddContact.strPhone"  />

                    </div>
                    <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="email" name="contactEmail" class="form-control" placeholder="Email" data-ng-model="objAddContact.strEmail">
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="form-group"> 
                        <label class="control-label">Customer </label>             
                        <select  data-ng-model="objAddContact.intCustomerID" data-ng-options="customer.intID as customer.strName for customer in arrCustomers" class="form-control">
                            <option value="">Customer</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.city.$touched && addForm.city.$invalid">{#LNG_6610#}</small>
                    </div>

                    <div class="form-group">
                        <label class="control-label">Notes</label>
                        <textarea class="form-control rounded-0" placeholder="{#LNG_6711#}" rows="5" data-ng-model="objAddContact.strNotes"></textarea>
                    </div>
                </div>   
            </div> 
        </form>  
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="addContact()" class="btn green-jungle" data-dismiss="modal" ng-disabled="addForm.$invalid">ADD</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">CANCEL</button>
    </div>
</div>
{/block}
{block name=toolbar}
<!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_ADD,$_UserPermission)}{/if}-->
<div class="btn-group">
    <a data-toggle="modal" data-target="#dlgAdd" class="btn btn-fit-height green-jungle" title="{#LNG_6705#}">
        <i class="fa fa-plus"></i> 
        <span class="visible-lg-inline-block">Contact</span>
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
                                <input type="text" data-ng-enter="filter()" name="txtContactName" data-ng-model="objFilter.strName" placeholder="Contact Name" class="form-control"/>
                            </div>
                        </div>  
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" name="txtContactPhone" data-ng-model="objFilter.strPhone" placeholder="Contact Phone" class="form-control"/>
                            </div>
                        </div> 
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" name="txtContactEmail" data-ng-model="objFilter.strEmail" placeholder="Contact Email" class="form-control"/>
                            </div>
                        </div>                               
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <select data-ng-model="objFilter.intCustomerID" data-ng-options="customer.intID as customer.strName for customer in arrCustomers" class="form-control">
                                    <option value="">Customer</option>
                                </select>
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
                    <i style="font-size:15px" class=" font-blue fa fa-users"></i>&nbsp;Contacts
                </div>
                <pagination  data-ng-if="objContacts.arrData.length>0" data-total-items="objContacts.intTotal" data-items-per-page="objPaging.intPageSize" data-ng-model="objPaging.intPageNo" data-max-size="objPaging.intMaxSize" class="pagination-sm" data-boundary-links="true" data-ng-change="nextPage()"></pagination>
            </div>                                                               
            <div class="portlet-body">
                <div data-ng-if="objContacts.arrData.length > 0" class="table-responsive">
                    <table class="table table-condensed table-light table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th> 
                                <th>Customer</th> 
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="objContact in objContacts.arrData"><!--data-ng-class="{literal}{'striped':objUser.intDisabled == 1}{/literal}"--> 
                                <td>{{objContact.intID}}</td>
                                <td>{{objContact.strName}}</td>
                                <td>{{objContact.strPhone}}</td>
                                <td>{{objContact.strEmail}}</td>
                                <td>{{objContact.objCustomer.strName}} </td>
                                <!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_DELETE,$_UserPermission)}{/if}-->
                                <td class="icon">
                                    <a data-ng-click="edit(objContact ,$index)" data-toggle="modal" data-target="#dlgEdit" class="btn blue-dark btn-xs" title="Edit"><i class="fa fa-edit"></i> <span class="visible-lg-inline-block"></span></a>
                                    &nbsp;&nbsp;<a data-ng-click="remove(objContact.intID, $index)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="Delete"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="objContacts.arrData.length == 0" class="alert alert-warning">
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