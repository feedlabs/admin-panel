{extends file=$render->getLayoutPath('Page/Abstract/default.tpl', 'CM')}

{block name='content'}
  <div class="columnContent">
    {block name='content-title'}<h1>{$pageTitle|escape}</h1>{/block}
    {block name='content-main'}{/block}
  </div>
{/block}

{block name='after'}
  <footer id="footer" class="clearfix">
    <p class="copyright">© {$render->getSiteName()} {$smarty.now|dateyear}</p>
    {menu name='about' template='tree'}
    {block name='footer-after'}{/block}
  </footer>
{/block}
