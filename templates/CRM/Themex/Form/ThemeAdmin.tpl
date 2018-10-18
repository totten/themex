<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

{if $config->userSystem->is_drupal EQ '1'}

  <div class="crm-section">
    <div class="label">{ts}Theme{/ts} {help id="theme"}</div>
    <div class="content">{$form.theme_backend.html}</div>
    <div class="clear"></div>
  </div>

{else}

  <div class="crm-section">
    <div class="label">{$form.theme_backend.label} {help id="theme_backend"}</div>
    <div class="content">{$form.theme_backend.html}</div>
    <div class="clear"></div>
  </div>
  <div class="crm-section">
    <div class="label">{$form.theme_frontend.label} {help id="theme_frontend"}</div>
    <div class="content">{$form.theme_frontend.html}</div>
    <div class="clear"></div>
  </div>

{/if}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
