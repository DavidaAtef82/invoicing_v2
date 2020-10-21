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
<h1>{#LNG_6680#} <small>{#LNG_6681#}</small></h1>
{/block}
{block name=toolbar}
{/block}

{block name=content}
<div class="row"> 
</div>
{/block}