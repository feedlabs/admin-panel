{form name='AP_Form_Application' application=$application}
{formField name='name' label='Name'}
{formField name='description' label='Description'}
{if $application}
  {formAction action='Save' label='Save' theme='highlight'}
{else}
  {formAction action='Create' label='Create' theme='highlight'}
{/if}
{/form}
