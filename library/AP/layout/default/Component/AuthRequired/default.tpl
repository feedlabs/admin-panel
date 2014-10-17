<ul class="tabs menu-tabs">
  <li class="hasIcon hasLabel {if !$hadViewer}active{/if}">
    {link icon="edit" label={translate 'Sign Up'}}
  </li>
  <li class="hasIcon hasLabel {if $hadViewer}active{/if}">
    {link icon="profile" label={translate 'Login'}}
  </li>
</ul>
<div class="tabs-content">
  <div>
    {component name='AP_Component_SignUp'}
  </div>
  <div>
    {component name='AP_Component_SignIn'}
  </div>
</div>
