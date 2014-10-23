{extends file=$render->getLayoutPath('Layout/Default/default.tpl', 'AP')}

{block name='body'}
  <main id="page">
    {$renderAdapter->fetchPage()}
  </main>
{/block}
