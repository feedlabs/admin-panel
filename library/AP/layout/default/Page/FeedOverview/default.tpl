{extends file=$render->getLayoutPath('Page/Abstract/default.tpl')}

{block name='content-main'}
  <div class="form-feedAdd">
    <div class="toggleNext">Add new Feed</div>
    <div class="toggleNext-content">
      {form name='AP_Form_Feed'}
      {formField name='application' label='Application'}
      {formField name='name' label='Name'}
      {button action='Create' theme="highlight" label='Create'}
      {/form}
    </div>
  </div>
  <div class="box ">
    <div class="box-header">
      <h2><span class="icon icon-profile"></span> Application: 234567654345678</h2>
    </div>
    <div class="box-body">
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
              {button_link page='AP_Page_Feed' feed=123 label="{translate 'view'}"}
              {button_link title={translate 'Delete'} icon='delete' iconConfirm='delete-confirm' class='warning deleteFeed' data=['click-confirmed' => true]}
            </td>
          </tr>
        {/for}
      </table>
    </div>
  </div>
{/block}
