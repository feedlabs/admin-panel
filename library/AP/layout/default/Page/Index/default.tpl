{extends file=$render->getLayoutPath('Page/Abstract/default.tpl')}

{block name='content-main'}
  Index
  {component name='AP_Component_SignIn'}
  {component name='AP_Component_SignUp'}
{/block}
