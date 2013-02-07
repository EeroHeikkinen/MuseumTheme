<!-- START of: Search/home-content.sv.tpl -->

<div class="home-section first columns clear">
  <div class="content">
    <div>
      <h2 class="color-finnaBlue">För dig som söker information och intressanta upplevelser</h2>
      <p class="big">Finna är en ny sökportal för alla som använder arkivens, bibliotekens och museernas tjänster. </p>
      <p class="big">Testa sökfunktionen, <a href="{$path}/Feedback/Home">ge respons</a> eller <a class="color-violet" href="{$path}/Content/about">läs mer</a> om tjänsten i testversionen av Finna!</p>
    </div>
    <div>
      <h2>Du kan söka efter...</h2>
    <ul>
      <li><span class="iconlabel formatthesis"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FThesis"'>Avhandlingar</a></span></li>
      <li><span class="iconlabel formatimage"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FImage"'>Bilder</a></span></li>
      <li><span class="iconlabel formatbook"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FBook"'>Böcker</a></span></li>
      <li><span class="iconlabel formatdocument"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FDocument"'>Dokument</a></span></li>
      <li><span class="iconlabel formatdatabase"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FDatabase"'>Databaser</a></span></li>
      <li><span class="iconlabel formatphysicalobject"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FPhysicalObject"'>Föremål</a></span></li>
    </ul>
    <ul>
      <li><span class="iconlabel formatmap"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMap"'>Kartor</a></span></li>
      <li><span class="iconlabel formatworkofart"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FWorkOfArt"'>Konstverken</a></span></li>
      <li><span class="iconlabel formatsound"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FSound"'>Ljudinspelningar</a></span></li>
      <li><span class="iconlabel formatmusicalscore"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMusicalScore"'>Musikalier</a></span></li>
      <li><span class="iconlabel formatjournal"><a class="twoLiner" href='{$url}/Search/Results?filter[]=format%3A"0%2FJournal"'>Tidskrifter och artiklar</a></span></li>
      <li><span class="iconlabel formatvideo"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FVideo"'>Videoklipp</a></span></li>
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
      <h2 class="color-finnaBlue">De 10 populäraste sökningarna</h2>
      <div id="popularSearches" class="recent-searches"><div class="loading"></div></div>
      {include file="AJAX/loadPopularSearches.tpl"}
    </div>
    <div>
      <div class="mapSearchHome">
        <h2>Pröva kartsökningen</h2>
        <p>Du kan också begränsa sökningen på kartan. För närvarande ingår ca 7 200 poster i kartsökningen.</p>
        <a class="button" href="">Till kartsökningen</a>
      </div>
    </div>
  </div>
</div>
    
<!-- END of: Search/home-content.sv.tpl -->
