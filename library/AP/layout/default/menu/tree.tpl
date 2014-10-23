{function printMenu}
  {if !empty($menu_entries)}
    <ul class="{$menu_class}">
      {foreach from=$menu_entries item=entry}
        <li class="{$entry->getPageName()} {$entry->getClass()} {if $entry->getIcon()}hasIcon {/if} hasLabel {if $entry->isActive($activePath, $activeParams)}active{/if}" data-menu-entry-hash="{$entry->getHash()}">
          <a href="{linkUrl page=$entry->getPageName() params=$entry->getParams()}">
            {if $entry->getIndication()}<span class="indication">{$entry->getIndication()}</span>{/if}
            {if $entry->getIcon()}<span class="icon icon-{$entry->getIcon()}"></span>{/if}<span class="label">{translate $entry->getLabel()}</span>
          </a>
          {if $entry->hasChildren()}
            {$menu_entries=$entry->getChildren()->getEntries($render->getEnvironment())}
            {printMenu}
          {/if}
        </li>
      {/foreach}
    </ul>
  {/if}
{/function}

{printMenu}
