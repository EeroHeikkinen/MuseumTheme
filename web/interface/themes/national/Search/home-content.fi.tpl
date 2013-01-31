<!-- START of: Search/home-content.fi.tpl -->

<div class="home-section first columns clear">
  <div class="content">
    <div>
      <h2 class="color-finnaBlue">Tietoa tarvitseville ja elämyksiä etsiville</h2>
      <p class="big">Finna on uudenlainen tiedonhakupalvelu kaikille arkistojen, kirjastojen ja museoiden palveluiden käyttäjille.</p>
      <p class="big">Finna on nyt testikäytössä, kokeile hakua, <a href="{$path}/Feedback/Home">anna palautetta</a> tai <a class="color-violet" href="{$path}/Content/about">lue lisää</a> palvelusta!</p>
    </div>
    <div>
      <h2>Haulla löydät...</h2>
      <ul>
        <li><span class="iconlabel formatdocument"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FDocument"'>Asiakirjoja</a></span></li>
        <li><span class="iconlabel formatphysicalobject"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FPhysicalObject"'>Esineitä</a></span></li>
        <li><span class="iconlabel formatmap"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMap"'>Karttoja</a></span></li>
        <li><span class="iconlabel formatbook"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FBook"'>Kirjoja</a></span></li>
        <li><span class="iconlabel formatimage"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FImage"'>Kuvia</a></span></li>
        <li><span class="iconlabel formatjournal"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FJournal"'>Lehtiä&nbsp;ja&nbsp;artikkeleita</a></span></li>
      </ul>
      <ul>
        <li><span class="iconlabel formatmusicalscore"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FMusicalScore"'>Nuotteja</a></span></li>
        <li><span class="iconlabel formatthesis"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FThesis"'>Opinnäytteitä</a></span></li>
        <li><span class="iconlabel formatworkofart"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FWorkOfArt"'>Taideteoksia</a></span></li>
        <li><span class="iconlabel formatdatabase"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FDatabase"'>Tietokantoja</a></span></li>
        <li><span class="iconlabel formatvideo"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FVideo"'>Videoita</a></span></li>
        <li><span class="iconlabel formatsoundrecording"><a href='{$url}/Search/Results?filter[]=format%3A"0%2FSound"'>Äänitteitä</a></span></li>
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
      <div class="mapSearchHome">
        <h2>Kokeile karttahakua</h2>
        <p>Voit rajata hakuasi myös kartalla. Karttarajauksen piirissä on tällä hetkellä noin 7200 aineistotietoa.</p>
        <a class="button" href="">Karttahakuun</a>
      </div>
    </div>
  </div>
</div>
    
<!-- END of: Search/home-content.fi.tpl -->
