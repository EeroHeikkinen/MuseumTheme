<!-- START of: footer.tpl -->

<div class="footerWrapper">
  <div id="footerCol1">
    <h4>{translate text='navigation_about'}</h4>
    <ul>
      <li><a href="{$path}/Content/about">{translate text='navigation_about_finna'}</a></li>
      <li><a href="{$path}/Content/terms_conditions">{translate text='navigation_terms_conditions'}</a></li>
      <li><a href="{$path}/Content/register_details">{translate text='navigation_register_details'}</a></li>
    </ul>
  </div>

  <div id="footerCol2">
    <h4>{translate text='navigation_search'}</h4>
    <ul>
      <li><a href="{$path}/Search/History">{translate text='Search History'}</a></li>
      <li><a href="{$path}/Search/Advanced">{translate text='Advanced Search'}</a></li>
      <li><a href="{$path}/Browse/Home">{translate text='Browse the Catalog'}</a></li>
      {* <li><a href="{$path}/Browse/Home">{translate text='Browse the Catalog'}</a></li>
      <li><a href="{$path}/AlphaBrowse/Home">{translate text='Browse Alphabetically'}</a></li>
      <li><a href="{$path}/Search/TagCloud">{translate text='Browse by Tag'}</a></li>
      <li><a href="{$path}/Search/Reserves">{translate text='Course Reserves'}</a></li> not used
      <li><a href="{$path}/Search/NewItem">{translate text='New Items'}</a></li> not used *}
    </ul>
  </div>

  <div id="footerCol3">
    <h4>{translate text='navigation_help'}</h4>
    <ul>
      <li><a href="{$path}/Content/searchhelp">{translate text='Search Tips'}</a></li>
      <li><a href="{$path}/Feedback/Home">{translate text='navigation_feedback'}</a></li>
      {*<li><a href="#">{translate text='Ask a Librarian'}</a></li>
      <li><a href="#">{translate text='FAQs'}</a></li>*}
    </ul>
  </div>

  <div class="clear"></div>
</div>
  
<div class="footerWrapper">
  <div id="footerCol4">
      <a href="{$url}" class="footerLogo">{image src="finna_logo_small.png" alt="Home"}</a>
  </div>

  <div id="footerCol5">
    {if $userLang=='en-gb'}
    <a href="http://www.kdk.fi/en" class="footerLogo">{image src="kdk_logo_en.png" alt="NDL-logo"}</a>
    {/if}
    {if $userLang=='sv'}
    <a href="http://www.kdk.fi/sv" class="footerLogo">{image src="kdk_logo_sv.png" alt="logo"}</a>
    {/if}
    {if $userLang=='fi'}
      <a href="http://www.kdk.fi" class="footerLogo">{image src="kdk_logo_fi.png" alt="KDK-logo"}</a>
    {/if}
  </div>

  <div id="footerCol6">
    <a href="http://www.vufind.org" class="footerLogo">{image src="vufind_logo_small.png" alt="vufind-logo"}</a>

    {* Comply with Serials Solutions terms of service -- this is intentionally left untranslated. *}
    {if $module == "Summon"}
    <br /><p>Powered by Summon™ from Serials Solutions, a division of ProQuest.
    </p>
    {/if}
  </div>
  <div class="clear"></div>
</div>

{literal}
<script type="text/javascript">   
  $(document).ready(function(){
    $('.toggleHeader').parent().next().hide();
    $('.toggleHeader').click(function(){
      $(this).parent().next().toggle('fast');
      return false;
    });
  });
</script>
{/literal}

<!-- END of: footer.tpl -->
