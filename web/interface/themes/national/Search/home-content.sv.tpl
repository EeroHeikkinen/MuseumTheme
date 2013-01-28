<!-- START of: Search/home-content.sv.tpl -->

<div class="home-section first columns clear">
  <div class="content">
    <div>
      <h2 class="color-finnaBlue">Tietoa tarvitseville ja elämyksiä etsiville</h2>
      <p class="big">Tack vare kundgränssnittet utgör bibliotekens, arkivens och museernas material en överskådlig helhet. Du kan hitta inte enbart den information du söker, utan också annan information som anknyter till området.</p>
      <p class="big">Finna är i testbruk, prova sökningen, <a href="{$path}/Feedback/Home">ge respons</a> eller <a class="color-violet" href="{$path}/Content/about">läs mer</a> om tjänsten!</p>
    </div>
    <div>
      <h2>Här kan du hitta...</h2>
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
      <li><span class="iconlabel formatsoundrecording"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FSound"'>Ljudinspelningar</a></span></li>
      <li><span class="iconlabel formatmusicalscore"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMusicalScore"'>Musikalier</a></span></li>
      <li><span class="iconlabel formatjournal"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FJournal"'>Tidskrifter och artiklar</a></span></li>
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
      <h2 class="color-finnaBlue">10 suosituinta hakua</h2>
      <div id="popularSearches" class="recent-searches"><div class="loading"></div></div>
      {include file="AJAX/loadPopularSearches.tpl"}
    </div>
    <div>
      {if $user}
        <div class="mapSearchHome">
          <h2>Kokeile karttahakua</h2>
          <p>Voit rajata hakuasi myös kartalla. Karttarajauksen piirissä on tällä hetkellä noin 7200 aineistotietoa.</p>
          <a class="button" href="">Karttahakuun</a>
            
        </div>
      {else}
        <div class="loginBoxHome">
          <h2>Kirjautumalla voit...</h2>
          <p class="big">Varata aineistoja, tallentaa haut, arvioida ja kommentoida sekä tehdä suosikkilistoja.</p>
          <p class="small">Finna tunnukseesi voit yhdistää useiden organisaatioiden tunnuksia. <a class="color-violet" href="{$path}/Content/about">Lue lisää</a>
        </div>
      {/if}
    </div>
  </div>
</div>
    
<!-- END of: Search/home-content.sv.tpl -->
