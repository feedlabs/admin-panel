{if !empty($menu_entries)}
  {strip}
    <ul class="{$menu_class}">
      {foreach from=$menu_entries item=entry}
        <li class="{$entry->getPageName()} {$entry->getClass()} {if $entry->getIcon()}hasIcon {/if}hasLabel {if $entry->isActive($activePath, $activeParams)}active{/if}" data-menu-entry-hash="{$entry->getHash()}">
          <a href="{linkUrl page=$entry->getPageName() params=$entry->getParams()}">
            {if $entry->getIcon()}<span class="icon icon-{$entry->getIcon()}"></span>{/if}<span class="label">{$entry->getLabel()}</span>
          </a>
          {block name='item-after'}{/block}
        </li>
      {/foreach}
    </ul>
  {/strip}
{/if}
