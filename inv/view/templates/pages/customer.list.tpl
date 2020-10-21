{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
data-ng-controller="Customer" 
{/block}
{block name=style}{/block}
{block name=script}
<script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
<script type="text/javascript" src="{$smarty.const.PATH__JS}/customer.list.js?ver={$smarty.const.VERSION}"></script>
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
                            <label class="control-label">{#LNG_6697#}</label>
                            <input type="text" class="form-control" placeholder="{#LNG_6697#}" autocomplete="off" data-ng-model="objEditCustomer.strName" data-ng-blur="ValidateUsername()" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{#LNG_6704#}</label>
                            <input type="text" class="form-control" placeholder="{#LNG_6704#}" data-ng-model="objEditCustomer.strPhone" />
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{#LNG_6692#} </label>
                            <select  data-ng-model="intEditCountry" data-ng-change="GetCities(intEditCountry)" data-ng-options="country.intID as country.strCountry for country in arrCountries" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{#LNG_6718#}</label>
                            <input type="email" class="form-control" placeholder="{#LNG_6718#}" data-ng-model="objAddCustomer.strEmail">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{#LNG_6693#} </label> 
                            <select  data-ng-model="intEditCity" data-ng-options="city.intID as city.strCity for city in arrCities" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{#LNG_6703#}</label>
                            <input type="text" class="form-control" placeholder="{#LNG_6703#}" autocomplete="off" data-ng-model="objEditCustomer.strAddress" />
                        </div>
                    </div> 
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label">{#LNG_6711#}</label>
                            <textarea class="form-control rounded-0" placeholder="{#LNG_6711#}" rows="5" data-ng-model="objEditCustomer.strNotes"></textarea>
                        </div>
                    </div>    
                </div>
            </div>
        </div>   
    </div>
    <div class="modal-footer">
        <button type="button"  data-ng-click="editCustomer()" class="btn green-jungle" data-dismiss="modal">{#LNG_6710#}</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">{#LNG_6713#}</button>

    </div>
</div>

<div id="dlgRemove" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header" >
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title"><i style="font-size:24px" class=" font-blue fa fa-trash"></i> {#LNG_6712#} {#LNG_6705#}</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <div class="row">
            <div class="col-md-6" >
                <label class="control-label" >&nbsp;&nbsp;{#LNG_6719#}</label>
            </div>    
        </div>
        <div class="modal-footer">
            <button type="button" data-ng-click="removeCustomer()" class="btn red-thunderbird" data-dismiss="modal">{#LNG_6712#}</button>
            <button type="button" class="btn green-jungle" data-dismiss="modal">{#LNG_6713#}</button>
        </div>
    </div>
</div>
<div id="dlgAdd" class="modal fade modal-scroll modal-dialog" tabindex="-1" data-width="760" style="display: none;height:auto!important">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 class="modal-title">
        <i style="font-size:24px" class=" font-blue fa fa-user-plus"></i> 
        {#LNG_6706#} {#LNG_6705#}</h3>
    </div>
    <div class="modal-body" style="margin: 0 auto;">
        <form name="addForm">
            <div class="row">
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">{#LNG_6697#}*</label>
                        <input type="text" name="customerName" class="form-control" placeholder="{#LNG_6697#}" autocomplete="off" data-ng-model="objAddCustomer.strName" required />
                        <small class="help-block font-red" data-ng-show="addForm.customerName.$touched && addForm.customerName.$invalid">{#LNG_6717#}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{#LNG_6692#}* </label> 
                        <select name="country" data-ng-model="intAddCountry" data-ng-change="GetCities(intAddCountry)" data-ng-options="country.intID as country.strCountry for country in arrCountries" class="form-control" required>
                            <option value="">{#LNG_6692#}</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.country.$touched && addForm.country.$invalid">{#LNG_6610#}</small>

                    </div>
                    <div class="form-group">
                        <label class="control-label">{#LNG_6693#}* </label>             
                        <select name="city" data-ng-model="intAddCity" data-ng-options="city.intID as city.strCity for city in arrCities" class="form-control" required>
                            <option value="">{#LNG_6693#}</option>
                        </select>
                        <small class="help-block font-red" data-ng-show="addForm.city.$touched && addForm.city.$invalid">{#LNG_6610#}</small>
                    </div>
                    <div class="form-group">
                        <label class="control-label">{#LNG_6703#}</label>
                        <textarea type="text" class="form-control rounded-0" rows="2" placeholder="{#LNG_6703#}" autocomplete="off" data-ng-model="objAddCustomer.strAddress"/></textarea>
                    </div>
                </div>
                <div class="col-md-6" >
                    <div class="form-group">
                        <label class="control-label">{#LNG_6704#}</label>
                        <input type="text" class="form-control" placeholder="{#LNG_6704#}" data-ng-model="objAddCustomer.strPhone">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{#LNG_6718#}</label>
                        <input type="email" name="customerEmail" class="form-control" placeholder="{#LNG_6718#}" data-ng-model="objAddCustomer.strEmail">
                    </div>

                    <div class="form-group">
                        <label class="control-label">{#LNG_6711#}</label>
                        <textarea class="form-control rounded-0" placeholder="{#LNG_6711#}" rows="6" data-ng-model="objAddCustomer.strNotes"></textarea>
                    </div>
                </div>   
            </div> 
        </form>  
    </div>
    <div class="modal-footer">
        <button type="button" data-ng-click="addCustomer()" class="btn green-jungle" data-dismiss="modal" ng-disabled="addForm.$invalid">{#LNG_6706#}</button>
        <button type="button" class="btn red-thunderbird" data-dismiss="modal">{#LNG_6713#}</button>
    </div>
</div>
{/block}
{block name=toolbar}
<!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_ADD,$_UserPermission)}{/if}-->
<div class="btn-group">
    <a data-toggle="modal" data-target="#dlgAdd" class="btn btn-fit-height green-jungle" title="{#LNG_6705#}">
        <i class="fa fa-plus"></i> 
        <span class="visible-lg-inline-block">{#LNG_6705#}</span>
    </a>
</div>
{/block}
{block name=content}
<div class="row">
    <div class="col-md-12">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption font-blue"><i class="fa fa-filter font-blue"></i> {#LNG_6690#}</div>
                <div class="tools"><a href="#" class="collapse"></a></div>
            </div>
            <div class="portlet-body">
                <form id="frmFilter">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="text" data-ng-enter="filter()" name="txtEmployeeID" data-ng-model="objFilter.strName" placeholder="{#LNG_6691#}" class="form-control"/>
                            </div>
                        </div>                
                        <div class="col-md-3">
                            <div class="form-group">
                                <select data-ng-model="objFilter.intCountryID" data-ng-change="GetCities(objFilter.intCountryID)"  data-ng-options="country.intID as country.strCountry for country in arrCountries" class="form-control">
                                    <option value="">{#LNG_6692#}</option>
                                </select>
                            </div>
                        </div>                 
                        <div class="col-md-3">
                            <div class="form-group"> 
                                <select data-ng-model="objFilter.intCityID" data-ng-options="city.intID as city.strCity for city in arrCities" class="form-control">
                                    <option value="">{#LNG_6693#}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select placeholder="{#LNG_6702#}" data-ng-enter="filter()" data-ng-model="objFilter.strStatus" data-ng-options="obj.intID as obj.strName for obj in arrLocations" class="form-control">
                                    <option value="">{#LNG_6702#}</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group pull-right">
                                <button type="button" class="btn blue" title="{#LNG_6690#}" data-ng-click="filter()"><i class="fa fa-filter"></i> {#LNG_6690#}</button>
                                <button type="button" class="btn red-thunderbird" title="{#LNG_6694#}" data-ng-click="reset()"><i class="fa fa-refresh"></i> {#LNG_6694#}</button>
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
                    <i style="font-size:15px" class=" font-blue fa fa-users"></i>&nbsp;{#LNG_6688#}
                </div>
                <pagination  data-ng-if="objCustomers.arrData.length>20" data-total-items="objCustomer.intTotal" data-items-per-page="objPaging.intPageSize" data-ng-model="objPaging.intPageNo" data-max-size="objPaging.intMaxSize" class="pagination-sm" data-boundary-links="true" data-ng-change="nextPage()"></pagination>
            </div>                                                               
            <div class="portlet-body">
                <div data-ng-if="objCustomers.arrData.length > 0" class="table-responsive">
                    <table class="table table-condensed table-light table-hover">
                        <thead>
                            <tr>
                                <th>{#LNG_6698#}</th>
                                <th>{#LNG_6697#}</th>
                                <th>{#LNG_6703#}</th>
                                <th>{#LNG_6704#}</th> 
                                <th>{#LNG_6718#}</th>
                                <th>{#LNG_6707#}</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr data-ng-repeat="objCustomer in objCustomers.arrData"><!--data-ng-class="{literal}{'striped':objUser.intDisabled == 1}{/literal}"--> 
                                <td>{{objCustomer.intID}}</td>
                                <td>{{objCustomer.strName}}</td>
                                <td>{{objCustomer.strAddress}}</td>
                                <td>{{objCustomer.strPhone}}</td>
                                <td>{{objCustomer.strEmail}}</td>
                                <td>{{objCustomer.objCity.objCountry.strCountry}}, {{objCustomer.objCity.strCity}} </td>
                                <!--{if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_DELETE,$_UserPermission)}{/if}-->
                                <td class="icon">
                                    {if in_array(\NsCMN\ClsBllUserPermission::PERMISSION_VIEW,$_UserPermission)}
                                    <a 
                                    href="index.php?module=inv&page=Customer&action=View&customer_id={{objCustomer.intID}}"
                                        class="btn btn-xs yellow-saffron" style="cursor: pointer;" 
                                        title="View"><i class="fa fa-search-plus"></i> 
                                        <span class="visible-lg-inline-block"></span></a>
                                    {/if}
                                    <a data-ng-click="edit(objCustomer ,$index)" data-toggle="modal" data-target="#dlgEdit" class="btn blue-dark btn-xs" title="Edit">
                                        <i class="fa fa-edit"></i> 
                                        <span class="visible-lg-inline-block"></span></a>
                                    <a data-ng-click="remove(objCustomer.intID, $index)" data-toggle="modal" data-target="#dlgRemove" class="btn red-thunderbird btn-xs" title="Delete"><i class="fa fa-trash-o"></i> <span class="visible-lg-inline-block"></span></a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div data-ng-if="objCustomers.arrData.length == 0" class="alert alert-warning">
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