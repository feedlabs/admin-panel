<h1 class="signUpHeading">{translate 'Join the Community for Free.'}</h1>

{component name='*_Component_SignUpForm' signUpParams=$signUpParams}

<p class="agreement">{translate 'By submitting this form you certify you are 18 years or older, you agree to our <a href="{$urlPrivacy}">privacy policy</a>, <a href="{$urlTerms}">terms & conditions</a>, the use and nature of <a href="{$urlTerms}">Cupids</a>, and understand that this site is for adult entertainment purposes only.' urlPrivacy={linkUrl page='AP_Page_About_Privacy'} urlTerms={linkUrl page='AP_Page_About_Terms'}}</p>
