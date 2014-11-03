<div class="form-applicationAdd">
  <div class="toggleNext">Add new Application</div>
  <div class="toggleNext-content">
    {form name='AP_Form_Application'}
    {formField name='name' label='Name'}
    {formField name='description' label='Description'}
    {formAction action='Create' label='Create' theme='highlight'}
    {/form}
  </div>
</div>

{foreach $applicationList as $application}
  {component name='AP_Component_ApplicationViewSmall' application=$application}
{/foreach}
