{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * GetFinancing configuration page
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<table cellspacing="1" cellpadding="5" class="settings-table">

  <tr>
    <td colspan="2" class="note">
      {t(#Get Financing#)}
<ul>
  <li><a href="https://partner.getfinancing.com/partner/portal" target="_blank">{t(#GetFinancing Backoffice Login#)}</a></li>
  <li><a href="http://www.getfinancing.com/docs/" target="_blank">{t(#Documentation#)}</a></li>
</ul>
    </td>
  </tr>

  <tr>
    <td class="setting-name">
    <label for="settings_username">{t(#Username#)}</label>
    </td>
    <td>
    <input type="text" id="settings_username" name="settings[username]" value="{paymentMethod.getSetting(#username#)}" class="validate[required,maxSize[255]]" />
    </td>
  </tr>

  <tr>
    <td class="setting-name">
    <label for="settings_merchant_id">{t(#Merchant Id#)}</label>
    </td>
    <td>
    <input type="text" id="settings_merchant_id" name="settings[merchant_id]" value="{paymentMethod.getSetting(#merchant_id#)}" class="validate[required,maxSize[255]]" />
    </td>
  </tr>

  <tr>
    <td class="setting-name">
    <label for="settings_password">{t(#Password#)}</label>
    </td>
    <td>
    <input type="text" id="settings_password" name="settings[password]" value="{paymentMethod.getSetting(#password#)}" class="validate[required,maxSize[255]]" />
    </td>
  </tr>


  <tr>
    <td class="setting-name">
    <label for="settings_test">{t(#Test/Live mode#)}</label>
    </td>
    <td>
    <select id="settings_test" name="settings[test]">
      <option value="1" selected="{isSelected(paymentMethod.getSetting(#test#),#1#)}">{t(#Test mode: Test#)}</option>
      <option value="0" selected="{isSelected(paymentMethod.getSetting(#test#),#0#)}">{t(#Test mode: Live#)}</option>
    </select>
    </td>
  </tr>

</table>
