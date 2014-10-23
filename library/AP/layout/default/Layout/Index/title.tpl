{extends file=$render->getLayoutPath('Layout/Abstract/title.tpl', 'CM')}

{block name='title' prepend}{$render->getSiteName()}{if strlen($pageTitle)} - {/if}{/block}
