{if !empty($menu_entries)}
  {strip}
    {foreach from=$menu_entries item=entry}
      <li class="{if $entry->isActive($activePath, $activeParams)}active{/if}">
        <a href="{linkUrl page=$entry->getPageName() params=$entry->getParams()}">{translate $entry->getLabel()}</a>
      </li>
    {/foreach}
  {/strip}
{/if}
