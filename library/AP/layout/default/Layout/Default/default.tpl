{extends file=$render->getLayoutPath('Layout/Abstract/default.tpl', 'CM')}

{block name='body-start'}
  {component name='AP_Component_Alerts'}
{/block}

{block name='body'}
  <div id="headerWrapper">
    <header id="header" class="{if !$viewer}no-fixed{else}hasChat{/if}">
      {block name='header'}
        {component name='AP_Component_HeaderBar'}
      {/block}
    </header>
  </div>
  <div id="navigation">
    {component name="AP_Component_Navigation"}
  </div>
  <div id="middle" class="{if $viewer}hasChat{/if}">
    <main id="page">
      {$renderAdapter->fetchPage()}
    </main>
  </div>
{/block}
