{box}
{box_header}
  <span class="box-info">created: {$feed->getCreated()|date_format}</span>
  <h2>Feed name: {$feed->getName()}</h2>
{/box_header}
  <div class="actions">
    {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteFeed' data=['application-id'=>$application->getId(), 'feed-id'=>$feed->getId(), 'click-confirmed' => true]}
  </div>
<p>{$feed->getChannelId()}</p>
<p>{$feed->getDescription()}</p>
  <div class="form-feed">
    <div class="toggleNext">Edit Feed</div>
    <div class="toggleNext-content">
      {component name='AP_Component_FeedForm' application=$application feed=$feed}
    </div>
  </div>
{/box}

{component name='AP_Component_FeedEntryList' application=$application feed=$feed}
