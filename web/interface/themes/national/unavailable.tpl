{capture name=errorContent assign=errorContent}
  
  <div class="contentHeader noResultHeader"><div class="content"><h1>{translate text="System Unavailable"}</h1></div></div>
  <div class="error unavailable content">
  <p>
    {translate text="The system is currently unavailable due to system maintenance"}.
    {translate text="Please check back soon"}.
  </p>
  <p>
    {translate text="Please contact the Library Reference Department for assistance"}.
    <br/>
    <a href="mailto:{$supportEmail}">{$supportEmail}</a>
  </p>
  </div>
  
{/capture}

{include file="layout.tpl" errorPage=true errorContent=$errorContent userLang = $site.language}