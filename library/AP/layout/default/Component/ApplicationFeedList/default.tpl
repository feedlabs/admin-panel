{box}
{box_header}
  <span class="box-info">Amount: %%%123%%%</span>
  <h2>Feed List | ApplicationName: {$application['name']}</h2>
{/box_header}

<table>
  <tr>
    <th>Id</th>
    <th>Name</th>
    <th></th>
  </tr>
  {for $foo=1 to 5}
    <tr class="feed" data-feed-id="4389738275">
      <td>4389738275</td>
      <td>dlighdjksghthe printing</td>
      <td class="actions">
        {button_link page='AP_Page_Feed' feed=123 label='view'}
        {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteFeed' data=['click-confirmed' => true]}
      </td>
    </tr>
  {/for}
</table>

<div class="form-feedAdd">
  <div class="toggleNext">Add new Feed</div>
  <div class="toggleNext-content">
    {form name='AP_Form_Feed' application='345678'}
    {formField name='name' placeholder='Feed name' class='noLabel' prepend="{button action='Create' theme="highlight" label='Create'}"}
    {/form}
  </div>
</div>

{/box}
