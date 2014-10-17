{form name='AP_Form_SignUp' site=$render->getSite() values=$signUpParams}
{block name='fieldSex'}
  {formField name='sex' label="{translate 'I am'}" translate=true}
{/block}
{formField name='match_sex' label="{translate 'I am Looking For'}" translate=true}
{formField name='birthdate' label="{translate 'Birthday'}"}
{formField name='location' label="{translate 'City/ZIP'}"}
{formField name='email' label="{translate 'Your Email'}"}
{formField name='username' label="{translate 'Username'}" autocorrect="off" autocapitalize="off"}
{formField name='password' label="{translate 'Password'}"}
{formAction action='Create' label="{translate 'Sign Up'}" theme='highlight' class="button-large"}
{/form}
