<!-- START of: Search/home-content.fi.tpl -->

<div class="home-section first columns clear">
  <div class="content">
    <div>
      <h2 class="color-finnaBlue">For seekers of information and inspiration</h2>
      <p class="big">Finna is a new kind of information search service for all users of archives, libraries and museums.</p>
      <p class="big">Finna is currently in test use. Try the search, <a href="{$path}/Feedback/Home">give feedback</a> or <a class="color-violet" href="{$path}/Content/about">read more</a> about the service!</p>
    </div>
    <div>
      <h2>From Finna you can find...</h2>
      <ul>
        <li><span class="iconlabel formatdocument"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FDocument"'>Documents</a></span></li>
        <li><span class="iconlabel formatphysicalobject"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FPhysicalObject"'>Physical objects</a></span></li>
        <li><span class="iconlabel formatmap"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMap"'>Maps</a></span></li>
        <li><span class="iconlabel formatbook"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FBook"'>Books</a></span></li>
        <li><span class="iconlabel formatimage"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FImage"'>Images</a></span></li>
        <li><span class="iconlabel formatjournal"><a class="twoLiner" href='{$url}/Search/Results?filter[]=format%3A"0%2FJournal"'>Journals and Articles</a></span></li>
      </ul>
      <ul>
        <li><span class="iconlabel formatmusicalscore"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMusicalScore"'>Musical scores</a></span></li>
        <li><span class="iconlabel formatthesis"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FThesis"'>Theses</a></span></li>
        <li><span class="iconlabel formatworkofart"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FWorkOfArt"'>Works of Art</a></span></li>
        <li><span class="iconlabel formatdatabase"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FDatabase"'>Databases</a></span></li>
        <li><span class="iconlabel formatvideo"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FVideo"'>Videos</a></span></li>
        <li><span class="iconlabel formatsound"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FSound"'>Sound Recordings</a></span></li>
      </ul>
    </div>
  </div>
</div>
<div class="home-section second clear">
  <div class="content">
    <div id="carousel">
      {include file="Search/home-carousel.$userLang.tpl"}
    </div>
  </div>
</div>
<div class="home-section third columns clear">
  <div class="content">
    <div class="popularSearchesWrap">
      <h2 class="color-finnaBlue">10 most popular searches</h2>
      <div id="popularSearches" class="recent-searches"><div class="loading"></div></div>
      {include file="AJAX/loadPopularSearches.tpl"}
    </div>
    <div>
      <div class="mapSearchHome">
        <h2>Try the map search</h2>
        <p>You can also refine your search to a specific area on the map. The map search function currently encompasses some 12,630 items.</p>
        <a class="button" href="{$url}/Search/Advanced">Map search</a>
      </div>
    </div>
  </div>
</div>
    
<!-- END of: Search/home-content.en-gb.tpl -->
