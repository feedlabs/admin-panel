{box}
{box_header}
  <span class="box-info">created: {$application->getCreated()|date_format}</span>
  <h2>Application name: {$application->getName()}</h2>
{/box_header}
  <div class="actions">
    {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteApplication' data=['application-id'=>$application->getId(), 'click-confirmed' => true]}
  </div>
{$application->getDescription()}
  <div class="form-application">
    <div class="toggleNext">Edit Application</div>
    <div class="toggleNext-content">
      {component name='AP_Component_ApplicationForm' application=$application}
    </div>
  </div>
{/box}

{component name='AP_Component_ApplicationFeedList' application=$application}
