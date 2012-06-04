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
        <h2>Mikä on Museonäkymä?</h2>
        <p>Museonäkymä on Kansallisen digitaalisen kirjaston sektorikohtainen käyttöliittymä, jossa voi selata ja tutkia Kantapuu-konsortion, Museoviraston sekä muiden suomalaisten museoiden digitoituja kokoelmia. Museonäkymässä on suomalaisiin museoihin tallennetun kulttuuriperinnön koko kirjo, esineitä, valokuvia, arkistoaineistoa ja julkaisuja, niiden viitetietoja ja kuvia. Museonäkymässä voi tilata kopion arkisto- tai esinekuvasta, kirjoittaa kommentteja ja antaa lisätietoa aineistosta sitä säilyttävään museoon.</p>
        <p><a href="about.html">Lue lisää</a> tai kokeile hakua!</p>
    </div>
    <div class="homeCustomCol1">
       <h2>Mikä on KDK?</h2>
       <p>Kansallisen digitaalisen kirjaston asiakasliittymä avaa pääsyn kirjastojen, arkistojen ja museoiden sähköisiin aineistoihin ja palveluihin. Se on tarkoitettu kaikille tietoa tarvitseville ja elämyksiä etsiville. Verkkopalvelun avulla pääsee helposti käsiksi valitsemaansa aihetta koskevaan aineistoon. Asiakasliittymän kansallisessa näkymässä kirjastojen, arkistojen ja museoiden aineistot muodostavat yhteisen kokonaisuuden. Käytettävissä on myös erilaisia sektoreiden tai organisaatioiden omia, temaattisia ja alueellisia hakuliittymiä.</p>
       <p><a href="http://vufind-fe-kktest.lib.helsinki.fi/institution/">Siirry kansalliseen näkymään</a></p>
    </div>

	
	
	
	
</div>

{* Search by browsing switched off for now.
   Instead of reversed condition with '!' it might be better to switch off in the settings *}

{if !$facetList}
  {include file="Search/browse.tpl"}
{/if}

<!-- END of: Search/home.tpl -->
