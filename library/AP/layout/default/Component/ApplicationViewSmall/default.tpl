{box}
{box_header}
  <span class="box-info">created: {$application->getCreated()|date_format}</span>
  <h2>{$application->getName()}</h2>
{/box_header}
  <div class="actions">
    {button_link page='AP_Page_Application' application=$application->getId() label='view'}
    {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteApplication' data=['application-id'=>$application->getId(), 'click-confirmed' => true]}
  </div>
  <br>
{/box}
