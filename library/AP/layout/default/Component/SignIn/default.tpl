{form name='AP_Form_SignIn'}
{formField name='login' class="noLabel" tabindex=1 placeholder={translate 'Username/Email'} autocorrect="off" autocapitalize="off" append="<div class='small'>{input name='remember_me' tabindex=3 text={translate 'Remember Me'}}</div>"}
{formField class="noLabel" name='password' tabindex=2 placeholder={translate 'Password'} append="<a href='javascript:;' class='small forgotPW'>{translate 'Forgot Password?'}</a>"}
{formAction action='Process' theme='highlight' label={translate 'Login'}}
{/form}
<div class="visitor">
  <h2>{translate 'Not a Member yet?'}</h2>
  {button_link theme='highlight' class="signup" label="{translate 'Join Free!'}"}
</div>
