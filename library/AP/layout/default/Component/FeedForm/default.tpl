{form name='AP_Form_Feed' application=$application feed=$feed}
{formField name='name' label='Name'}
{formField name='description' label='Description'}
{if $feed}
  {formAction action='Save' label='Save' theme='highlight'}
{else}
  {formAction action='Create' label='Create' theme='highlight'}
{/if}
{/form}
