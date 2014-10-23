{extends file=$render->getLayoutPath('Page/Abstract/default.tpl')}

{block name='content-title'}{/block}

{block name='content-main'}
  <h1>Feed name</h1>

  <div class="box ">
    <div class="box-body">
      <p>sk fjskldfjsldkjflksdajf klsdajf lsadj flksajflksadj fklsajflasj flsk jfskld jf</p>
    </div>
  </div>

  <div class="box ">
    <div class="box-header">
      <h2><span class="icon icon-profile"></span> Entries</h2>
    </div>
    <div class="box-body">
      <table>
        <tr>
          <th>Id</th>
          <th>Data</th>
          <th></th>
        </tr>
        {for $foo=1 to 5}
          <tr class="entry" data-entry-id="4389738275">
            <td>4389738275</td>
            <td>dlighdjksghthe printing</td>
            <td class="actions">
              {button_link title={translate 'Delete'} icon='delete' iconConfirm='delete-confirm' class='warning deleteEntry' data=['click-confirmed' => true]}
            </td>
          </tr>
        {/for}
      </table>
    </div>
  </div>
{/block}
