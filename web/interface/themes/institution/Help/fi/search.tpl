<!-- START of: Help/fi/search.tpl -->

<h1>Hakuvinkit</h1>

<ul class="HelpMenu">
  <li><a href="#Wildcard Searches">Jokerimerkit</a></li>
  <li><a href="#Fuzzy Searches">Sumeat haut</a></li>
  <li><a href="#Proximity Searches">Etäisyyshaut</a></li>
  <li><a href="#Range Searches">Arvovälihaut</a></li>
  <li><a href="#Boosting a Term">Termin painottaminen</a></li>
  <li><a href="#Boolean operators">Boolean-hakuoperaattorit </a>
    <ul>
      <li><a href="#AND">AND</a></li>
      <li><a href="#+">+</a></li>
      <li><a href="#OR">OR</a></li>
      <li><a href="#NOT">NOT</a></li>
      <li><a href="#-">-</a></li>
    </ul>
  </li>
</ul>

<dl class="Content">
  <dt><a name="Wildcard Searches"></a>Jokerimerkit</dt>
  <dd>
    <p><strong>?</strong> korvaa yhden merkin hakutermistä.</p>
    <p>Esimerkki: termejä "text" ja "test" voidaan hakea samalla kyselyllä:</p>
    <pre class="code">te?t</pre>
    <p><strong>*</strong> korvaa 0, 1 tai useampia merkkejä hakutermistä.</p>
    <p>Esimerkki: termejä "test", "tests" ja "tester" voidaan hakea kyselyllä:</p>
    <pre class="code">test*</pre>
    <p>Jokerimerkkejä voi käyttää myös hakutermin keskellä:</p>
    <pre class="code">te*t</pre>
    <p>Huomio! Jokerimerkkejä <strong>?</strong> ja <strong>*</strong> ei voi käyttää 
       hakutermin ensimmäisenä merkkinä.</p>
  </dd>
  
  <dt><a name="Fuzzy Searches"></a>Sumeat haut</dt>
  <dd>
    <p><strong>~</strong> toteuttaa sumean haun yksisanaisen haun viimeisenä merkkinä.</p>
    <p>Esimerkki: sumea haku termille "roam":</p>
    <pre class="code">roam~</pre>
    <p>Tämä haku löytää esimerkiksi termit "foam" ja "roams".</p>
    <p>Haun samankaltaisuutta kantatermiin voidaan säädellä parametrilla, jonka arvo on 
       välillä 0 ja 1. Mitä lähempänä annettu arvo on lukua 1, sen samankaltaisempi 
       termin on oltava kantatermin kanssa.</p>
    <pre class="code">roam~0.8</pre>
    <p>Oletusarvona parametrille on 0.5, jos arvoa ei sumeassa haussa erikseen määritetä.</p>
  </dd>
  
  <dt><a name="Proximity Searches"></a>Etäisyyshaut</dt>
  <dd>
    <p><strong>~</strong> toteuttaa etäisyyshaun monitermisen hakulausekkeen lopussa 
       etäisyysarvon kanssa.</p>
    <p>Esimerkki: etsitään termejä "economics" ja "keynes" niiden esiintyessä korkeintaan 10 termin etäisyydellä toisistaan:</p>   
    <pre class="code">"economics Keynes"~10</pre>
  </dd>
  
  {literal}
  <dt><a name="Range Searches"></a>Arvovälihaut</dt>
  <dd>
    <p>Arvovälihaut tehdään käyttämällä joko aaltosulkeita <strong>{ }</strong> tai 
       hakasulkeita <strong>[ ]</strong>. Aaltosulkeita käytettäessä huomioidaan vain 
       arvot annettujen termien välillä pois lukien kyseiset termit. Hakasulkeet 
       puolestaan sisällyttävät myös annetut termit etsittävälle arvovälille.
    <p>Esimerkki: etsittäessä termiä, joka alkaa kirjaimella B tai C, voidaan käyttää kyselyä:</p>
    <pre class="code">{A TO D}</pre>
    <p>Esimerkki: etsittäessä arvoja 2002&mdash;2003 voidaan haku tehdä seuraavasti:</p>
    <pre class="code">[2002 TO 2003]</pre>
    <p>Huomio! Sana TO arvojen välillä kirjoitetaan ISOIN KIRJAIMIN.</p>
  </dd>
  {/literal}
  
  <dt><a name="Boosting a Term"></a>Termin painottaminen</dt>
  <dd>
    <p><strong>^</strong> nostaa termin painoarvoa kyselyssä.</p>
    <p>Esimerkki: haussa termin "Keynes" painoarvoa on nostettu:</p>
    <pre class="code">economics Keynes^5</pre>
  </dd>
  
  <dt><a name="Boolean operators"></a>Boolean-hakuoperaattorit</dt>
  <dd>
    <p>Termejä voi yhdistellä monimutkaisemmiksi kyselyiksi Boolean-hakuoperaattoreilla. 
       Seuraavat operaattorit ovat käytettävissä: <strong>AND</strong>, 
       <strong>+</strong>, <strong>OR</strong>, <strong>NOT</strong> ja <strong>-</strong>.
    </p>
    <p>Huomio! Boolean-hakuoperaattorit kirjoitetaan ISOIN KIRJAIMIN.</p>
    <dl>
      <dt><a name="AND"></a>AND</dt>
      <dd>
        <p><strong>AND</strong> eli konjunktio-operaattori on järjestelmän oletusarvoinen 
           operaattori monitermisille kyselyille, joihin ei ole sisällytetty mitään 
           operaattoria. <strong>AND</strong>-operaattoria käytettäessä kyselyn tuloksena saadaan tietueet, 
           joissa esiintyy kukin hakukentissä esiintyvistä termeistä.</p>
        <p>Esimerkki: etsitään tietueita, joissa esiintyy sekä "economics" että "Keynes":</p>
        <pre class="code">economics Keynes</pre>
        <p>tai</p>
        <pre class="code">economics AND Keynes</pre>
      </dd>
      <dt><a name="+"></a>+</dt>
      <dd>
        <p>Merkillä <strong>+</strong> voidaan ilmaista vaatimusta siitä, että termin on esiinnyttävä jokaisessa hakutuloksessa.</p>
        <p>Esimerkki: etsitään tietueita, joissa esiintyy ehdottomasti "economics" ja joissa voi lisäksi esiintyä "Keynes":</p>
        <pre class="code">+economics Keynes</pre>
      </dd>
      <dt><a name="OR"></a>OR</dt>
      <dd>
        <p><strong>OR</strong>-operaattorin käyttö haussa tuottaa tulokseksi tietueita, joissa 
           esiintyy yksi tai useampi operaattorin yhdistämistä termeistä.</p>
        <p>Esimerkki: etsitään tietueita, joissa esiintyy joko "economics Keynes" tai ainoastaan "Keynes":</p>
        <pre class="code">"economics Keynes" OR Keynes</pre>
      </dd>
      <dt><a name="NOT"></a>NOT / -</dt>
      <dd>
        <p><strong>NOT</strong>-operaattori poistaa hakutuloksista tietueet, joissa esiintyy kyselyssä 
           <strong>NOT</strong>-operaattoria seuraava termi.</p>
        <p>Esimerkki: etsitään tietueita, joissa on termi "economics" mutta ei termiä "Keynes":</p>
        <pre class="code">economics NOT Keynes</pre>
        <p>Huomio! NOT-operaattoria ei voi käyttää yksitermisissä kyselyissä.</p>
        <p>Esimerkki: seuraava kysely ei tuota lainkaan tuloksia:</p>
        <pre class="code">NOT economics</pre>
        <p><strong>NOT</strong>-operaattorin voi korvata operaattorilla <strong>-</strong>. </p>
      </dd>
    </dl>
  </dd>
</dl>

<!-- END of: Help/fi/search.tpl -->
