{* vim: set ts=2 sw=2 sts=2 et: *}

{**
 * Payment template
 *
 * @author    Qualiteam software Ltd <info@x-cart.com>
 * @copyright Copyright (c) 2011-2016 Qualiteam software Ltd <info@x-cart.com>. All rights reserved
 * @license   http://www.x-cart.com/license-agreement.html X-Cart 5 License Agreement
 * @link      http://www.x-cart.com/
 *}

<widget class="XLite\Module\GetFinancing\GetFinancing\View\Payment" />

<script>
//<![CDATA[
        var script = document.createElement('script');
        script.src = "https://cdn.getfinancing.com/libs/1.0/getfinancing.js";
        setTimeout(function(){
        document.body.appendChild(script);
        },2000);  // 2000 is the delay in milliseconds
//]]>
</script>
