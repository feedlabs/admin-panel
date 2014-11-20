<div class="bar clearfix">
  {if $viewer}
    {link icon="menu" class="navButton navigation showNavigation"}
  {else}
    {component name='AP_Component_SignIn'}
  {/if}
</div>
