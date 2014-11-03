{box}
{box_header}
  <span class="box-info">created: {$application['createStamp']|date_format}</span>
  <h2>{$application['name']}</h2>
{/box_header}
  <div class="application" data-application-id="{$application['id']}">
    <p>
      Description: {$application['description']}<br>
    </p>
    {button_link page='AP_Page_Application' application=$application['id'] label='view'}
    {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteApplication' data=['click-confirmed' => true]}
  </div>
{/box}
