{box}
{box_header}
  <span class="box-info">Amount: {$feedList->getCount()}</span>
  <h2>Feed List | ApplicationName: {$application->getName()}</h2>
{/box_header}
  <ul>
    {foreach $feedList as $feed}
      <li class="feed" data-feed-id="{$feed->getId()}">
        {$feed->getId()} |
        {$feed->getName()} |
        {$feed->getChannelId()} |
        {button_link page='AP_Page_Feed' application=$application->getId() feed=$feed->getId() label='view'}
        {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteFeed' data=['feed-id'=>$feed->getId(), 'application-id'=>$application->getId(), 'click-confirmed' => true]}
      </li>
    {/foreach}
  </ul>
  <div class="form-feedAdd">
    <div class="toggleNext">Add new Feed</div>
    <div class="toggleNext-content">
      {component name='AP_Component_FeedForm' application=$application}
    </div>
  </div>
{/box}
