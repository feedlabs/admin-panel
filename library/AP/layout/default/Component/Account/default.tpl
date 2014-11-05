{box}
{box_header}
  <h2>User account</h2>
{/box_header}
{form name='AP_Form_AccountUser'}
{formField name='email' label='Your Email'}
{formField label='Password' prepend="{button_link label='Change Password' class="changePassword"}"}
{formAction action='Save' label='Save' theme='highlight'}
{/form}
{/box}

{box}
{box_header}
  <h2>Tokens</h2>
{/box_header}
{component name='AP_Component_TokenList'}
{/box}

{*{box}*}
{*{box_header}*}
  {*<h2>Company account</h2>*}
{*{/box_header}*}
  {*// ADD form*}
  {*<br>*}
  {*<br>*}
  {*<ul>*}
    {*<li>company name</li>*}
    {*<li>Address</li>*}
    {*<li>business</li>*}
    {*<li>...</li>*}
  {*</ul>*}
{*{/box}*}
