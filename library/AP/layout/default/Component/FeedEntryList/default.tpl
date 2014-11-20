{box}
{box_header}
  <span class="box-info">Amount: {$entryList->getCount()}</span>
  <h2>Entry List | FeedName: {$feed->getName()}</h2>
{/box_header}
  <ul>
    {foreach $entryList as $entry}
      <li class="feed" data-feed-id="{$entry->getId()}">
        {$entry->getId()} |
        {$entry->getData()} |
        {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteEntry' data=['entry-id'=>$entry->getId(), 'feed-id'=>$feed->getId(), 'application-id'=>$application->getId(), 'click-confirmed' => true]}
      </li>
    {/foreach}
  </ul>
{/box}
