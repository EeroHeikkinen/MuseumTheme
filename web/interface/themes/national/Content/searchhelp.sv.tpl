<!-- START of: Content/searchhelp.sv.tpl -->

{assign var="title" value="Söktips"}
{capture append="sections"}
  {literal}
  <h2 title="Wildcard Searches">Jokertecken</h2>
  <p>Frågetecken <strong>?</strong> ersätter exakt ett tecken i sökordet.</p>
  <p><i>Exempel: Sök för båda ”ahlqvist” och ”ahlkvist” med</i></p>
  <pre class="code"><i>te?t</i></pre>
  <p>Asterisk <strong>*</strong> ersätter 0, 1 eller flera tecken i sökordet.
  </p>
  <p><i>Exempel: Ord ”testning”, ”testningen”, ”testningar” och ”testningarna” kan sökas med</i>
  </p>
  <pre class="code"><i>test*</i></pre>
  <p>Asterisken kan användas även inom ordet:</p>
  <p><i>Exempel: Man hittar båda "huvud" och "hufvud" med</i></p>
  <pre class="code"><i>hu*vud</i></pre>
  <p>Obs! Jokertecknena <strong>?</strong> och <strong>*</strong> kan inte vara det första tecken i ordet.
  </p>
  {/literal}
{/capture}
{capture append="sections"}
  {literal}
  <h2 title="Fuzzy Searches">Oskarp sökning</h2>
  <p>Lägg till ett tildetecken <strong>~</strong> direkt efter ett enkelt ord för att göra en oskarp sökning på det.
  </p>
  <p><i>Exempel: Oskarp sökning med ordet "petterson":</i></p>
  <pre class="code"><i>petterson~</i></pre>
  <p><i>hittar även ord "peterson" och  "petersen".</i></p>
  <p>Den oskarpa sökningen kan justeras med en parameter, som kan vara mellan 0 och 1. Ju närmare 1 siffran är, desto mera lika måste termerna vara.
  </p>
  <p><i>Exempel:</i></p>
  <pre class="code">petterson~0.8</pre>
  <p>Antaget värde är 0.5.</p>
  {/literal}
{/capture}
{capture append="sections"}
  {literal}
  <h2 title="Proximity Searches">Avståndssökning</h2>
  <p>Lägg till ett tildetecken <strong>~</strong> och maximiantal mellanstående ord efter en ordgupp inom citationstecken.
  </p>
  <p><i>Exempel:</i></p>   
  <pre class="code"><i>"economics Keynes"~10</i></pre>
  <p><i>hittar en post där ord "economics" och "keynes" förekommer med 10 eller färre ord mellan dem.</i>
  </p>
  {/literal}
{/capture}
{capture append="sections"}
  {literal}
  <h2 title="Range Searches">Intervallsökning</h2>
  <p>För att söka inom intervallet mellan två värden, kan man använda klammerparenteser <strong>{ }</strong> eller hakparenteser <strong>[ ]</strong> och ordet TO mellan värdena.
  </p>
  <p>Klammerparenteser söker mellan värden, men lämnar bort själva värdena. Hakparenteser inkluderar värdena i sökningen.
  </p> 
  <p><i>Exempel: Sök upphovsmän efter Saarinen men före Saaristo:</i></p>
  <pre class="code"><i>{saarinen TO saaristo}</i></pre>
  <p><i>I resultat finns t ex Saario och Saarisalo.</i></p>
  <p><i>Exempel: Sök inom år 2002&mdash;2003:</i></p>
  <pre class="code"><i>[2002 TO 2003]</i></pre>
  <p>Obs! Ordet TO mellan siffrona måste skrivas med STORA BOKSTÄVER.</p>
  {/literal}
{/capture}
{capture append="sections"}
  {literal}
  <h2 title="Boosting a Term">Vikt av sökord</h2>
  <p>Fäst mera vikt på ett sökord genom att efter ordet lägga till insättningstecken <strong>^</strong> (circumflex) och en siffra.
  </p>
  <p><i>Exempel:</i></p>
  <pre class="code"><i>Friedman Keynes^5</i></pre>
  {/literal}
{/capture}
{capture append="sections"}
  {literal}
  <h2 title="Boolean operators">Booleska operatorer</h2>
  <p>Booleska operatorer kopplar ihop söktermer till mera komplicerade sökfrågor. Du kan använda operatorer <strong>AND</strong>, <strong>+</strong>, <strong>OR</strong>, <strong>NOT</strong> och <strong>-</strong>.
  </p>
  <p>Obs! Du måste skriva booleska operatorer med STORA BOKSTÄVER.</p>
  <h3 title="AND">AND</h3>
  <p><strong>AND</strong> är i Finna en standardoperator: då ingen operator skrivs mellan två ord, antas att <strong>AND</strong> står mellan dem. And är en s.k. konjugerande operator. Båda sökorden måste finnas i en post för en träff.
  </p>
  <p><i>Exempel: Sök efter poster som innehåller både "economics" och "Keynes":</i>
  </p>
  <pre class="code"><i>economics Keynes</i></pre>
  <p><i>eller</i></p>
  <pre class="code"><i>economics AND Keynes</i></pre>
  <h3 title="+">PLUSTECKEN +</h3>
  <p>Med plustecknet <strong>+</strong> kan man märka ett sökord, som ovillkorligen måste förekomma i sökresultaten.
  </p>
  <p><i>Exempel: Varje post i sökresultat måste innehålla "economics"; Keynes kan förekomma, och posterna med "Keynes" får högre relevans i resultatlistan:</i>
  </p>
  <pre class="code"><i>+economics Keynes</i></pre>
  <h3 title="OR">OR</h3>
  <p>Med <strong>OR</strong>-operatorn hittar man poster, där ett (eller flera) av sökorden hittas.
  </p>
  <p><i>Exempel: Sök efter resurser, som handlar Österbotten eller Västerbotten:</i>
  </p>
  <pre class="code"><i>österbotten OR västerbotten</i></pre>
  <h3 title="NOT">NOT / MINUSTECKEN -</h3>
  <p><strong>NOT</strong>-operatorn utesluter poster, där följande sökord förekommer.
  </p>
  <p><i>Exempel: Sök efter poster med ord "Turing" men utan ordet "machine":</i>
  </p>
  <pre class="code"><i>turing NOT machine</i></pre>
  <p>Obs! Not-operatorn kan inte användas med endast ett ord.</p>
  <p><i>Exempel: Följande sökning hittar ingenting:</i></p>
  <pre class="code"><i>NOT sibelius</i></pre>
  <p>Minustecken kan användas i stället för <strong>NOT</strong>.</p>
  <p><i>Exempel:</i></p>
  <pre class="code"><i>turing -machine</i></pre>      
  {/literal}
{/capture}
{capture append="sections"}
  {literal}
  <h2>Guide för utökad sökning</h2>
  <h3 title="Search Fields">Sökfält</h3>
  <p>När du öppnar den utökade sökningen, ser du en sökgrupp med flera sökfält till reds. Du kan fylla i sökord och sökoperatorer i ett, flera eller alla av dessa fält.
  </p>
  <p>Vid varje sökfält finns en rullgardinsmeny ur vilken kan du välja ett visst fält i posten (titel, upphovsman osv.). Då begränsas sökningen till att bara gälla dessa data i en post. Varje sökfält kan begränsas självständigt.
  </p>
  <p><i>Exempel: Ordet "Helsingfors" kan ofta förekomma i bokens data som tryckningsort. Om du söker information om Helsingfors, lönar det sig att söka "helsingfors" och begränsa sökningen till bara titel eller ämne.</i>
  </p>
  <p>I rullgardinsmenyn <strong>Sök</strong> kan du bestämma, hur en sökning med flera sökfält skall hanteras:
  </p>
  <ul>
    <li><strong>Alla söktermer (AND)</strong> &mdash; Resultat måste uppfylla villkoren i varje sökfält.
    </li>
    <li><strong>Vilka söktermer som helst (OR)</strong> &mdash; Resultat måste uppfylla villkoren bara i ett av sökfälten.
    </li>
    <li><strong>Ingen sökterm (NOT)</strong> &mdash; Visar poster som inte uppfyller villkor i något av sökfälten.
    </li>
  </ul>
  <p>Med <strong>Lägg till ett sökfält</strong> –knappen kan du skapa flera sökfält.
  </p>
  
  <h3 title="Search Groups">Sökgrupp</h3>
  <p>Med hjälp av sökgrupp kan du bygga mera avancerade sökningar.</p>
  <p><i>Exempel: Du är intresserad av Norge och Danmark i relation till andra världskriget. Med att kombinera sökord "Norge", "Danmark" och "andra världskriget" och söka med <strong>Alla söktermer (AND)</strong> hittar du endast resurser, som handlar om Danmark, Norge och andra världskriget på samma gång. Både "Danmark" och "Norge" måste alltså förekomma i samma resurs, och en bok som berättar bara om Norge, hittas inte.</i>
  </p>
  <p><i>Söker du med justeringen <strong>Vilka söktermer som helst (OR)</strong>, hittar du allt som handlar om Danmark, allt som handlar Norge och allt som handlar om andra världskriget.</i>
  </p>
  <p>Man måste alltså först gruppera söktermer på rätt sätt med hjälp av sökgrupp.
  </p>
  <p>Med knappar <strong>Lägg till ett sökgrupp</strong> och <strong>Radera sökgruppen</strong> kan du skapa grupper och radera dem.
  </p>
  <p>Sökgrupper kan kombineras på två sätt med rullgardinmenyn <strong>Sök</strong>: sökresultat måste träffa <strong>Alla grupper (AND)</strong> eller <strong>vilken som helst grupp (OR)</strong>.
  </p>
  <p><i>Exempel: Om Danmark och Norge i förhållande till andra världskriget kan man söka så här:</i>
  </p>
  <p><i>I första sökgruppen fyller man i "Danmark" och "Norge" i det andra fältet. Inom gruppen, välj sök stilen <strong>Vilka söktermer som helst (OR)</strong>.</i>
  </p>
  <p><i>Lägg till en sökgrupp. Fyll i sökord "andra världskriget".</i>
  </p>
  <p><i>Kombinera sökgrupper med <strong>Alla grupper (AND)</strong>.</i></p>
  {/literal}
{/capture}
{include file="$module/content.tpl" title=$title sections=$sections menu=true}

<!-- END of: Content/searchhelp.sv.tpl -->
