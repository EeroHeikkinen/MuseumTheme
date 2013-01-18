<!-- START of: Content/about.fi.tpl -->

{assign var="title" value="Tietoa Finnasta"}
{capture append="sections"}
<h2>Suomen arkistojen, kirjastojen ja museoiden aarteet samalla haulla.</h2>

<p class="ingress">Finna on arkistojen, kirjastojen ja museoiden yhteinen helppokäyttöinen verkkopalvelu, joka tulee tarjoamaan pääsyn muistiorganisaatioiden kaikkiin aineistoihin ja palveluihin.</p>

<p>Finna on tarkoitettu kaikille tietoa tarvitseville ja elämyksiä etsiville. Verkkopalvelun avulla voi helposti hakea valitsemaansa aihetta koskevaa aineistoa, kuten kuvia, asiakirjoja, sanomalehtiä, tutkimuksia, videoita ja äänitallenteita. Samalla voi käyttää arkistojen, kirjastojen ja museoiden digitaalisia palveluita. Finnalla pyritään korvaamaan nykyisiä käyttöliittymiä siten, että palvelun käyttäjä saavuttaa tarvitsemansa tiedon yhden käyttöliittymän kautta riippumatta siitä, mikä organisaatio on tiedon tuottanut.</p>

<p>Finnan testiversio julkaistiin joulukuussa 2012. Testiversiossa ovat mukana seuraavat organisaatiot aineistoineen ja palveluineen:</p>

<p>
	<ul>
	  <li>Jyväskylän yliopiston kirjasto</li>
	  <li>Kansallisarkisto</li>
	  <li>Kansalliskirjasto</li>
	  <li>Lusto – Suomen metsämuseo ja muut nk. Kantapuu-museot</li>
	  <li>Tuusulan taidemuseo</li>
	  <li>Valtion taidemuseo</li>
	</ul>
</p>

<p>Finnan kehittämistyö jatkuu vuonna 2013, ja palveluun lisätään uusia toiminnallisuuksia. Uusia organisaatioita liittyy mukaan vaiheittain.</p>

<p>Verkkopalvelua ylläpitää Kansalliskirjasto. Finna on toteutettu avoimen lähdekoodin ohjelmisto VuFindin pohjalta yhteistyössä arkistojen, kirjastojen ja museoiden kanssa.</p>

<p>Lisätietoa Finnasta on Kansallinen digitaalinen kirjasto -hankkeen verkkosivuilla osoitteessa <a href="http://www.kdk.fi">www.kdk.fi</a></p>
{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}

<!-- END of: Content/about.fi.tpl -->