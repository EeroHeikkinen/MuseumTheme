<!-- START of: Help/fi/advsearch.tpl -->

<h1>Tarkennetun haun ohje</h1>

<ul class="HelpMenu">
  <li><a href="#Search Fields">Hakukentät</a></li>
  <li><a href="#Search Groups">Hakuryhmät</a></li>
</ul>

<dl class="Content">
  <dt><a name="Search Fields"></a>Hakukentät</dt>
  <dd>
    <p>Tarkennetun haun sivulla on useita hakukenttiä, joihin voi kirjoittaa 
       hakutermejä ja –lausekkeita sekä <a href="Home?topic=search">hakuoperaattoreita</a></p>.
    <p>Jokaisen hakukentän vieressä on alasvetovalikko, josta voi valita, mihin 
       tietueen kenttään haku kohdistetaan (otsikko, tekijä, ym.). Saman useita 
       termejä yhdistelevän haun voi tarvittaessa kohdistaa useampaan kenttään.</p>
    <p>Lisävalikko <strong>Hae</strong> määrittelee, miten useita hakukenttiä 
       sisältävä kysely käsitellään:</p>
    <ul>
      <li><strong>Kaikilla termeillä (AND)</strong> &mdash; Tuottaa tulokseksi tietueet, jotka täsmäävät 
          kaikkien hakukenttien sisältöön.</li>
      <li><strong>Millä tahansa termillä (OR)</strong> &mdash; Tuottaa tulokseksi tietueet, jotka 
          täsmäävät yhden tai useamman hakukentän sisältöön.</li>
      <li><strong>Ei millään termeistä (NOT)</strong> &mdash; Tuottaa tulokseksi tietueet, joissa ei 
          esiinny yhdenkään hakukentän sisältöä.</li>
    </ul>
    <p><strong>Lisää hakukenttä</strong> –painikkeella lomakkeelle pystyy lisäämään 
       halutun määrän hakukenttiä.</p>
  </dd>
  
  <dt><a name="Search Groups"></a>Hakuryhmät</dt>
  <dd>
    <p>Hakuryhmiä tarvitaan sellaisten kyselyiden laatimisessa, joissa pelkkien 
       hakukenttien yhdistely ei riitä. Jos haun kohteena on esimerkiksi Intian 
       tai Kiinan historia, tuottaa hakujen "Intia", "Kiina" ja "historia" 
       yhdistäminen <strong>Kaikilla termeillä (AND)</strong> –valinnalla 
       tuloksekseen vain kirjoja, joissa on käsitelty sekä Intiaa että Kiinaa. 
       Jos valintana on <strong>Millä tahansa termeistä (OR)</strong>, tulee 
       tulokseksi kaikki kirjat, joissa on käsitelty Kiinaa, Intiaa tai 
       historiaa.</p>
    <p>Hakuryhmien avulla voidaan määritellä hakukenttiä kokonaisuuksiksi ja 
       luoda kyselyitä näitä hyödyntäen. <strong>Lisää hakuryhmä</strong> –painike 
       lisää uuden ryhmän hakukenttiä, ja <strong>Poista hakuryhmä</strong> 
       –painikkeella ryhmiä voidaan poistaa. Hakuryhmien välisiä suhteita 
       määritellään käyttäen <strong>Kaikki ryhmät (AND)</strong> ja <strong>Mitkä 
       tahansa ryhmät (OR)</strong> –hakuoperaattoreita. Yllä olevan esimerkin 
       Intian tai Kiinan historiasta voi hakuryhmien avulla toteuttaa seuraavasti:</p>
    <ul>
      <li>Ensimmäisen hakuryhmän hakukenttiin lisätään termit "Intia" ja "Kiina" 
          ja määritellään hakukenttien välinen suhde <strong>Hae</strong>
          -alasvetovalikosta <strong>Millä tahansa termillä (OR)</strong>.</li>
      <li>Luodaan uusi hakuryhmä ja lisätään sen hakukenttään termi "historia". 
          Hakuryhmien väliseksi suhteeksi määritellään <strong>Kaikki ryhmät (AND)
          </strong>.</li>
    </ul>
  </dd>
</dl>

<!-- END of: Help/fi/advsearch.tpl -->
