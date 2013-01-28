<!-- START of: Content/register_details.fi.tpl -->

{assign var="title" value="Rekisteriseloste"}
{capture append="sections"}{literal}

<h2>Finnan asiakasrekisterin seloste</h2>
        
<p><strong>Rekisterin ylläpitäjä:</strong><br />
Kansalliskirjasto<br />
Kirjastoverkkopalvelut<br />
PL 26 (Teollisuuskatu 23)<br />
00014 Helsingin yliopisto<br />
Puhelin: 09 1911</p>

<p><strong>Rekisteriasioita hoitava henkilö:</strong><br />
Aki Lassila, kehittämispäällikkö<br />
Kirjastoverkkopalvelut<br />
Kansalliskirjasto<br />
PL 26 (Teollisuuskatu 23)<br />
00014 Helsingin yliopisto<br />
Puhelin: 09 1911<br />
Sähköposti: aki.lassila(at)helsinki.fi</p>

<p><strong>Säännönmukaiset tietolähteet:</strong><br />
Henkilötiedot saadaan asiakkaiden kotiorganisaatioiden asiakasrekistereistä. Personoitujen toimintojen tiedot tallentaa käyttäjä itse.</p>

<p><strong>Säännönmukainen tietojen luovutus:</strong><br />
Tietoja ei luovuteta eteenpäin.</p>

<p><strong>Rekisterin nimi:</strong><br />
Finna-tiedonhakuportaalin asiakasrekisteri.</p>

<p><strong>Rekisterin pitämisen peruste:</strong><br />
Rekisteri on perustettu asiakkaiden yksikäsitteisyyden ylläpitämiseksi.</p>

<p><strong>Rekisterin käyttötarkoitus:</strong><br />
Rekisteriä käytetään käyttäjien tunnistamiseen, jotta voidaan tarjota personoituja palveluita. Esimerkkinä uutuusvahtipalvelu, jolla asiakas saa sähköpostitse tiedot hakuun tulleista uusista viitteistä.</p>

<p><strong>Rekisterin sisältämät tietotyypit:</strong><br />
Käyttäjän perustiedot:
</p>
<ul>
  <li>Käyttäjätunnus</li>
  <li>Käyttökieli</li>
  <li>Nimi</li>
  <li>Suhde oppilaitokseen tai organisaatioon</li>
  <li>Sähköpostiosoite</li>
  <li>Kirjastokorttien tiedot</li>
</ul>

<p><strong>Personoituja toimintoja varten kerättävät tiedot:</strong><br />
</p>
<ul>
  <li>Käyttäjän henkilökohtaiset asetukset</li>
  <li>Suosikit</li>
  <li>Tagit</li>
  <li>Kommentit</li>
  <li>Arviot</li>
  <li>Varaukset</li>
  <li>Lainat</li>
  <li>Maksut</li>
  <li>Kirjahyllyyn lisätyt viitteet</li>
  <li>Omiin aineistoihin lisätyt aineistot</li>
  <li>Omiin lehtiin lisätyt lehtiviitteet</li>
</ul>

<p><strong>Rekisterin suojausperiaatteet:</strong><br />
Tietoja säilytetään ainoastaan sähköisessä muodossa. Tietoihin pääsevät käsiksi ainoastaan järjestelmän ylläpitäjät, jotka tunnistetaan käyttäjätunnuksella ja salasanalla.</p>

<p><strong>Henkilötietojen säilytysaika:</strong><br />
Henkilötietoja säilytetään palvelussa, kunnes viimeisestä kirjautumisesta on kulunut 12 kuukautta.</p>

<p><strong>Rekisteröidyn tarkistusoikeus:</strong><br />
Tarkistaaksesi, mitä henkilötietoja käyttäjästä on kerätty palvelimeen, käyttäjän tulee ottaa yhteys rekisteriasioita hoitavaan henkilöön.</p>

<p><strong>Virheellisten tietojen oikaisu:</strong><br />
Mikäli käyttäjän kotiorganisaatiosta noudettavissa henkilötiedoissa on virheita, käyttäjän tulee ottaa yhteys kotiorganisaatioonsa. 
Tarvittaessa käyttäjä voi ottaa yhteyttä rekisteriasioita hoitavaan henkilöön.</p>


{/literal}{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}
<!-- END of: Content/register_details.fi.tpl -->
