{box}
{box_header}
  <h2>FeedEntryList</h2>
{/box_header}
  <table>
    <tr>
      <th>Id</th>
      <th>Data</th>
      <th></th>
    </tr>
    {for $foo=1 to 25}
      <tr class="entry" data-entry-id="4389738275">
        <td>4389738275</td>
        <td>dlighdjksghthe printing</td>
        <td class="actions">
          {button_link title='Delete' icon='delete' iconConfirm='delete-confirm' class='warning deleteEntry' data=['click-confirmed' => true]}
        </td>
      </tr>
    {/for}
  </table>
{/box}
