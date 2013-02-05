<!-- START of: header-menu.en-gb.tpl -->

<li class="menuAbout"><a href="{$path}/Content/about"><span>{translate text='navigation_about'}</span></a></li>

<li class="menuSearch"><a href="#"><span>{translate text='navigation_search'}</span></a>
  <ul class="subMenu">
    <li>
      <a href="{$path}/Search/History">
        <span>Search history</span>
        <span>Your session-specific search history. To save your searches, please log in.</span>
      </a>    
      </li>
    <li>
      <a href="{$path}/Search/Advanced">
        <span>Advanced search</span>
        <span>More refined search terms and map search.</span>
      </a>
    </li>
    <li>
      <a href="{$path}/Browse/Home">
        <span>Browse the catalogue</span>
        <span>Browse by author, topic, genre, area, era or tags.</span>
      </a>
    </li>
  </ul>
</li>

<li class="menuHelp"><a href="#"><span>{translate text='navigation_help'}</span></a>
  <ul class="subMenu">
    <li>
      <a href="{$path}/Content/searchhelp">
        <span>Search tips</span>
        <span>Detailed search instructions.</span>
      </a>
    </li>
  </ul> 
</li>

<li class="menuFeedback"><a href="{$path}/Feedback/Home"><span>{translate text='navigation_feedback'}</span></a></li>

{include file="login-element.tpl"}

<!-- END of: header-menu.en-gb.tpl -->
