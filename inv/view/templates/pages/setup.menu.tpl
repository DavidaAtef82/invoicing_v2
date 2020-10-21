{extends file="{$smarty.const.LOCAL__THEME}/template/masters/master1/master.tpl"}
{block name=app}
data-ng-app="INV"
{/block}
{block name=controller}
{/block}
{block name=style}{/block}
{block name=script}
    <script type="text/javascript" src="{$smarty.const.PATH__JS}/common.angular.js?ver={$smarty.const.VERSION}"></script>
    <script type="text/javascript">
        app.controller(controllers);
    </script>
{/block}

{block name=dialog}

{/block}
{block name=page_title}
<h1>{#LNG_6060#} <small>{#LNG_6057#}</small></h1>
{/block}
{block name=toolbar}
{/block}

{block name=content}
<div class="row"> 
    {foreach from=$arrMenuItems item=obj key=index}
        {if in_array($index, $_User->arrActionsIDs)}                                                                
            <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                <a class="dashboard-stat dashboard-stat-v2 {$obj.strColor}" href="index.php?module={$smarty.const.MODULE_NAME}&amp;page={$obj.page}&amp;action={$obj.action}">
                    <div class="visual">
                        <i style="margin-left: -10px;margin-top: -30px;" class="{$obj.strIcon}"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup" data-value=""></span>
                        </div>
                        <div class="desc"> <h2 style="width:200px">{$obj.strName}</h2> </div>
                    </div>
                </a>
            </div>
        {/if}
    {/foreach}
</div>

{/block}