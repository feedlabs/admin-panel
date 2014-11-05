{extends file=$render->getLayoutPath('Page/Abstract/default.tpl')}

{block name='content-main'}
  {component name='AP_Component_FeedView' application=$application feed=$feed}
{/block}
