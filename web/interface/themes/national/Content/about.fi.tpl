<!-- START of: Content/about.fi.tpl -->

{assign var="title" value="Tietoa Finnasta"}
{capture append="sections"}
<h2>Suomen arkistojen, kirjastojen ja museoiden aarteet samalla haulla.</h2>

<p class="ingress">Finna on helppokäyttöinen verkkopalvelu, joka tarjoaa pääsyn arkistojen, kirjastojen ja museoiden aineistoihin ja palveluihin.</p>

<p>Finnan testiversio julkaistiin joulukuussa 2012. Finnaa kehitetään jatkuvasti ja palveluun lisätään uusia toiminnallisuuksia. Uusia organisaatioita liittyy mukaan vaiheittain. Testiversiossa ovat mukana seuraavien organisaatioiden aineistot:</p>
<ul>
  <li>Jyväskylän yliopiston kirjasto</li>
  <li>Kansallisarkisto</li>
  <li>Kansalliskirjasto</li>
  <li>Lusto – Suomen metsämuseo ja muut nk. Kantapuu-museot (Nurmeksen museo, Pielisen museo, Ilomantsin museosäätiö, Lapin metsämuseo ja Verlan tehdasmuseo)</li>
  <li>Museovirasto</li>
  <li>Tuusulan taidemuseo</li>
  <li>Valtion taidemuseo</li>
</ul>

<p>Finna on tarkoitettu kaikille tietoa tarvitseville ja elämyksiä etsiville. Verkkopalvelun avulla voi helposti hakea valitsemaansa aihetta koskevaa aineistoa, 
    kuten kuvia, asiakirjoja, sanomalehtiä, tutkimuksia, videoita ja äänitallenteita. Aineistot ovat saatavissa samasta verkkopalvelusta, eikä tiedonhakijan tarvitse etukäteen 
    tietää, mikä organisaatio tiedon on tuottanut. Tulevaisuudessa Finnassa on mahdollista käyttää samalla arkistojen, kirjastojen ja museoiden digitaalisia palveluita. </p>

<p>Finnaa ylläpitää Kansalliskirjasto. Kansalliskirjasto on toteuttanut verkkopalvelun yhteistyössä arkistojen, kirjastojen ja museoiden kanssa. Finna on rakennettu 
    VuFindin ja muiden avoimen lähdekoodin ohjelmistojen pohjalta.</p>

<p>Finna on osa opetus- ja kulttuuriministeriön Kansallinen digitaalinen kirjasto -hanketta (KDK). Lisätietoa KDK-hankkeesta ja Finnasta on hankkeen 
    verkkosivuilla osoitteessa  <a href="http://www.kdk.fi">www.kdk.fi</a></p>
{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}

<!-- END of: Content/about.fi.tpl -->
