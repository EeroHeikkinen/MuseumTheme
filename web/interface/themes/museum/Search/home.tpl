<!-- START of: Search/home.tpl -->

<div class="searchHome">
  <div class="searchHomeContent">
    {if $offlineMode == "ils-offline"}
      <div class="sysInfo">
      <h2>{translate text="ils_offline_title"}</h2>
      <p><strong>{translate text="ils_offline_status"}</strong></p>
      <p>{translate text="ils_offline_home_message"}</p>
      <p><a href="mailto:{$supportEmail}">{$supportEmail}</a></p>
      </div>
    {/if}
    <div class="searchHomeLogo">
      <img src="{$path}/interface/themes/museum/images/museum_logo.png" alt="MUSEUM - MUSEONÄKYMÄ" />
{* Slogan is not necessarily needed if it is integrated into the logo or not use at all *}
{*
      <h3 id="slogan">{translate text="searchbox_headline_text"}</h3>
*}
    </div>
    <div class="searchHomeForm">
      <div class="searchbox">
        {include file="Search/searchbox.tpl"}
      </div>
    </div>
  </div>
</div>

<div class="homeNavigation">
  <ul>
    <li><a href="">Tietoa palvelusta</a></li>
    <li><a href="">Mukana olevat organisaatiot</a></li>
    <li><a href="">Usein kysytyt kysymykset</a></li>
    <li><a href="">Yhteystiedot</a></li>
    <li><a href="">Palaute</a></li>
  </ul>
</div>

<div class="homeCustomContent">
	<div class="homeCustomCol1">
		<h2>Suomen kirjastojen, arkistojen ja museoiden aarteet yhdellä haulla</h2>
		<p>Keräsimme aineistotiedot useista Suomen kirjastoista, arkistoista ja museoista yhteen paikkaan. Yhdellä haulla saat tuloksia kaikista mukana olevista kokoelmista.</p>
		<p><a href="about.html">Lue lisää</a> tai kokeile hakua!</p>
	</div>
	
	<div class="homeCustomCol2">
		<h4>Haulla löydät:</h4>
		<ul>
			<li><span class="iconlabel formatbook">Kirjoja</span></li>
			<li><span class="iconlabel formatmusicalscore">Nuotteja</span></li>
			<li><span class="iconlabel formatserial">Lehtiä ja artikkeleita</span></li>
			<li><span class="iconlabel formatitem">Asiakirjoja</span></li>
			<li><span class="iconlabel formatjournal">Pienpainatteita</span></li>
			<li><span class="iconlabel formatmap">Karttoja</span></li>
			<li><span class="iconlabel formatslide">Kuvia</span></li>
			<li><span class="iconlabel formatkit">Esineitä</span></li>
			<li><span class="iconlabel formatsoundrecording">Äänitteitä</span></li>
			<li><span class="iconlabel formatebook">Tietokantoja</span></li>
			<li><span class="iconlabel formatvideo">Videoita</span></li>
	</div>
	
	<div class="homeCustomCol3">
	
	</div>
</div>

{* Search by browsing switched off for now.
   Instead of reversed condition with '!' it might be better to switch off in the settings *}

{if !$facetList}
  {include file="Search/browse.tpl"}
{/if}

<!-- END of: Search/home.tpl -->
