<div class="form-application">
  <div class="toggleNext">Add new Application</div>
  <div class="toggleNext-content">
    {component name='AP_Component_ApplicationForm'}
  </div>
</div>

{foreach $applicationList as $application}
  {component name='AP_Component_ApplicationViewSmall' application=$application}
{/foreach}
