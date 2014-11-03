{extends file=$render->getLayoutPath('Page/Abstract/default.tpl')}

{block name='content-main'}

  {box}
  {box_header}
    <h2>List of Users</h2>
  {/box_header}
    <ul>
      <li>
        ms@example.com
        {button_link title='Send invite' icon='send'}
        {button_link title='Send invite' icon='delete'}
      </li>
      <li>
        ms@example.com
        {button_link title='Send invite' icon='send'}
        {button_link title='Send invite' icon='delete'}
      </li>
      <li>
        ms@example.com
        {button_link title='Send invite' icon='send'}
        {button_link title='Send invite' icon='delete'}
      </li>

    </ul>
  {button_link label='Add new user'}
  {/box}

  {box}
  {box_header}
    <h2>User roles</h2>
  {/box_header}
    <table>
      <tr>
        <th></th>
        <th colspan="3" style="text-align: center;border-left: 1px solid #e2e2e2;">Application</th>
        <th colspan="3" style="text-align: center;border-left: 1px solid #e2e2e2;">Feed</th>
        <th colspan="3" style="text-align: center; border-right: 1px solid #e2e2e2; border-left: 1px solid #e2e2e2;">User</th>
      </tr>
      <tr>
        <th>User</th>
        <th style="text-align: center;border-left: 1px solid #e2e2e2;">View</th>
        <th style="text-align: center;">Edit</th>
        <th style="text-align: center;">Delete</th>
        <th style="text-align: center;border-left: 1px solid #e2e2e2;">View</th>
        <th style="text-align: center;">Edit</th>
        <th style="text-align: center;">Delete</th>
        <th style="text-align: center;border-left: 1px solid #e2e2e2;">Add</th>
        <th style="text-align: center;">Edit</th>
        <th style="text-align: center;border-right: 1px solid #e2e2e2;">Delete</th>
      </tr>
      <tr>
        {form name='AP_Form_UserPermissions' user='user123'}
          <td>ms@example.com</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="application_view" class='noLabel' display='switch'}</td>
          <td>{input name="application_edit" class='noLabel' display='switch'}</td>
          <td>{input name="application_delete" class='noLabel' display='switch'}</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="feed_view" class='noLabel' display='switch'}</td>
          <td>{input name="feed_edit" class='noLabel' display='switch'}</td>
          <td>{input name="feed_delete" class='noLabel' display='switch'}</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="user_view" class='noLabel' display='switch'}</td>
          <td>{input name="user_edit" class='noLabel' display='switch'}</td>
          <td style="border-right: 1px solid #e2e2e2;">{input name="user_delete" class='noLabel' display='switch'}</td>
        {/form}
      </tr>
      <tr>
        {form name='AP_Form_UserPermissions' user='user123'}
          <td>ms@example.com</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="application_view" class='noLabel' display='switch'}</td>
          <td>{input name="application_edit" class='noLabel' display='switch'}</td>
          <td>{input name="application_delete" class='noLabel' display='switch'}</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="feed_view" class='noLabel' display='switch'}</td>
          <td>{input name="feed_edit" class='noLabel' display='switch'}</td>
          <td>{input name="feed_delete" class='noLabel' display='switch'}</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="user_view" class='noLabel' display='switch'}</td>
          <td>{input name="user_edit" class='noLabel' display='switch'}</td>
          <td style="border-right: 1px solid #e2e2e2;">{input name="user_delete" class='noLabel' display='switch'}</td>
        {/form}
      </tr>
      <tr>
        {form name='AP_Form_UserPermissions' user='user123'}
          <td>ms@example.com</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="application_view" class='noLabel' display='switch'}</td>
          <td>{input name="application_edit" class='noLabel' display='switch'}</td>
          <td>{input name="application_delete" class='noLabel' display='switch'}</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="feed_view" class='noLabel' display='switch'}</td>
          <td>{input name="feed_edit" class='noLabel' display='switch'}</td>
          <td>{input name="feed_delete" class='noLabel' display='switch'}</td>
          <td style="border-left: 1px solid #e2e2e2;">{input name="user_view" class='noLabel' display='switch'}</td>
          <td>{input name="user_edit" class='noLabel' display='switch'}</td>
          <td style="border-right: 1px solid #e2e2e2;">{input name="user_delete" class='noLabel' display='switch'}</td>
        {/form}
      </tr>
    </table>
  {/box}
{/block}
