<!-- START of: Help/sv/search.tpl -->

<h1>Guide för sökoperatorer</h1>

<ul class="HelpMenu">
  <li><a href="#Wildcard Searches">Jokertecken</a></li>
  <li><a href="#Fuzzy Searches">Suddig sökning</a></li>
  <li><a href="#Proximity Searches">Avståndssökning</a></li>
  <li><a href="#Range Searches">Intervallsökning</a></li>
  <li><a href="#Boosting a Term">Viktade sökord</a></li>
  <li><a href="#Boolean operators">Boleska operatorer</a>
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
  <dt><a name="Wildcard Searches"></a>Jokertecken</dt>
  <dd>
    <p>Frågetecken <strong>?</strong> ersätter exakt ett tecken i sökordet.</p>
    <p><i>Exempel: Sök för båda ”ahlqvist” och ”ahlkvist” med</i></p>
    <pre class="code"><i>te?t</i></pre>
    <p>Asterisk <strong>*</strong> ersätter 0, 1 eller flera tecken i sökordet.</p>
    <p><i>Exempel: Ord ”testning”, ”testningen”, ”testningar” och ”testningarna” kan sökas med</i></p>
    <pre class="code"><i>test*</i></pre>
    <p>Asterisken kan användas även inom ordet:</p>
    <p><i>Exempel: Man hittar båda "huvud" och "hufvud" med</i></p>
    <pre class="code"><i>hu*vud</i></pre>
    <p>Obs! Jokertecknena <strong>?</strong> och <strong>*</strong> kan inte vara det första tecken i ordet.</p>
  </dd>
  
  <dt><a name="Fuzzy Searches"></a>Suddig sökning</dt>
  <dd>
    <p>Lägg till ett tildetecken <strong>~</strong> direkt efter ett enkelt ord för att göra en suddigt sökning på det.</p>
    <p><i>Exempel: Suddig sökning med ordet "petterson":</i></p>
    <pre class="code"><i>petterson~</i></pre>
    <p><i>hittar även ord "peterson" och  "petersen".</i></p>
    <p>Suddigheten kan justeras med en parameter, som kan vara mellan 0 och 1. Ju närmare 1 siffran är, desto mera lika måste termer vara.</p>
    <p><i>Exempel:</i></p>
    <pre class="code">petterson~0.8</pre>
    <p>Antaget värde är 0.5.</p>
  </dd>
  
  <dt><a name="Proximity Searches"></a>Avståndssökning</dt>
  <dd>
    <p>Lägg till ett tildetecken <strong>~</strong> och maximiantal mellanstående ord efter en ordgupp inom citationstecken.</p>
    <p><i>Exempel:</i></p>   
    <pre class="code"><i>"economics Keynes"~10</i></pre>
    <p><i>hittar en post där ord "economics" och "keynes" förekommer med 10 eller färre ord mellan dem.</i></p>
  </dd>
  
  {literal}
  <dt><a name="Range Searches"></a>Intervallsökning</dt>
  <dd>
    <p>För att söka inom intervallet mellan två värden, kan man använda klammerparenteser <strong>{ }</strong> eller 
       hakparenteser <strong>[ ]</strong> och ordet TO mellan värdena.</p>
    <p>Klammerparenteser söker mellan värden, men lämnar bort själva värdena. Hakparenteser inkluderar värdena i sökningen.</p> 
    <p><i>Exempel: Sök upphovsmän efter Saarinen men före Saaristo:</i></p>
    <pre class="code"><i>{saarinen TO saaristo}</i></pre>
    <p><i>I resultat finns t ex Saario och Saarisalo.</i></p>
    <p><i>Exempel: Sök inom år 2002&mdash;2003:</i></p>
    <pre class="code"><i>[2002 TO 2003]</i></pre>
    <p>Obs! Ordet TO mellan siffrona måste skrivas med STORA BOKSTÄVER.</p>
  </dd>
  {/literal}
  
  <dt><a name="Boosting a Term"></a>Viktade sökord</dt>
  <dd>
    <p>Fästa mera vikt på en sökord genom att efter ett ord lägga till insättningstecken <strong>^</strong> (circumflex) och en siffra.</p>
    <p><i>Exempel:</i></p>
    <pre class="code"><i>Friedman Keynes^5</i></pre>
  </dd>
  
  <dt><a name="Boolean operators"></a>Boleska operatorer</dt>
  <dd>
    <p>Boleska operatorer kopplar söktermer till mera komplicerade sökfrågor.  
       Du kan använda operatorer <strong>AND</strong>, 
       <strong>+</strong>, <strong>OR</strong>, <strong>NOT</strong> och <strong>-</strong>.
    </p>
    <p>Obs! Du måste skriva boleska operatorer med STORA BOKSTÄVER.</p>
    <dl>
      <dt><a name="AND"></a>AND</dt>
      <dd>
        <p><strong>AND</strong> är i Finna en standardoperator: då ingen operator skrivs mellan 
           två ord, antas att en <strong>AND</strong> står mellan dem. And är en s k konjugerande 
           operator. Båda sökord måste finnas i en post för en träff.</p>
        <p><i>Exempel: Sök efter poster som innehåller båda "economics" och "Keynes":</i></p>
        <pre class="code"><i>economics Keynes</i></pre>
        <p><i>eller</i></p>
        <pre class="code"><i>economics AND Keynes</i></pre>
      </dd>
      <dt><a name="+"></a>PLUSTECKEN +</dt>
      <dd>
        <p>Med plustecknet <strong>+</strong> kan man märka ett sökord, som måste ovillkorligt förekomma i sökresultat.</p>
        <p><i>Exempel: Varje post i sökresultat måste innehålla "economics"; Keynes kan förekomma, och posterna med "Keynes" får högre relevans i resultatlistan:</i></p>
        <pre class="code"><i>+economics Keynes</i></pre>
      </dd>
      <dt><a name="OR"></a>OR</dt>
      <dd>
        <p>Med <strong>OR</strong>--operatorn hittar man poster, där ett (eller flera) av sökord hittas.</p>
        <p><i>Exempel: Sök efter resurser, som handlar Österbotten eller Västerbotten:</i></p>
        <pre class="code"><i>österbotten OR västerbotten</i></pre>
      </dd>
      <dt><a name="NOT"></a>NOT / MINUSTECKEN -</dt>
      <dd>
        <p><strong>NOT</strong>-operatorn utestängar poster, där följande sökord förekommer.</p>
        <p><i>Exempel: Sök efter poster med ord "Turing" men utan ord "machine":</i></p>
        <pre class="code"><i>turing NOT machine</i></pre>
        <p>Obs! Not-operatorn kan inte användas med bara ett ord.</p>
        <p><i>Exempel: Följande sökning vill hitta ingenting:</i></p>
        <pre class="code"><i>NOT sibelius</i></pre>
        <p>Minustecken kan användas i stället för <strong>NOT</strong>.</p>
        <p><i>Exempel:</i></p>
        <pre class="code"><i>turing -machine</i></pre>      
      </dd>
    </dl>
  </dd>
</dl>

<!-- END of: Help/sv/search.tpl -->
