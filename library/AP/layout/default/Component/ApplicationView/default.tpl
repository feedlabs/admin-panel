{box}
{box_header}
  <h2>Application name: {$application['name']}</h2>
{/box_header}
  <p>
    Description: <br>
  </p>

{button_link label='edit'}
{button_link label='delete'}
{/box}


{component name='AP_Component_ApplicationFeedList' application=$application}
