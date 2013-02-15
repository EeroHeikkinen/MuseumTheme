<!-- START of: Content/about.sv.tpl -->

{assign var="title" value="Om Finna"}
{capture append="sections"}
<h2>Arkivens, bibliotekens och museernas skatter i samma portal</h2>

<p class="ingress">Finna är en lättanvänd webbtjänst som ger tillgång till de material och tjänster som olika arkiv, bibliotek och museer tillhandahåller.</p>

<p>Testversionen av Finna lanserades i december 2012. Finna utvecklas hela tiden och nya funktioner läggs till i tjänsten. Nya organisationer kommer med stegvis. Testversionen innehåller material av följande organisationer:</p>

<ul>
  <li>Jyväskylä universitetsbibliotek</li>
  <li>Riksarkivet</li>
  <li>Nationalbiblioteket</li>
  <li>Lusto – Finlands skogsmuseum och andra s.k. Kantapuu-museer (Nurmes museum, Pielinens museum, Ilomantsin museosäätiö, Lapplands skogsmuseum och Verla fabriksmuseum) </li>
  <li>Museiverket</li>
  <li>Tusby konstmuseum</li>
  <li>Statens konstmuseum</li>
</ul>

<p>Finna riktar sig till alla som söker information och intressanta upplevelser. I webbtjänsten hittar man lätt olika typer av material om det ämne man söker information om, t.ex. bilder, dokument, tidningar, forskning, videor och ljudinspelningar. Allt material finns i samma webbtjänst och man behöver inte på förhand veta vilken organisation som har producerat informationen. I framtiden kommer man också att samtidigt kunna använda arkivens, bibliotekens och museernas digitala tjänster i Finna. </p>

<p>Finna administreras av Nationalbiblioteket, som har utvecklat webbtjänsten i samarbete med arkiv, bibliotek och museer. Finna bygger på programmet VuFind och andra program med öppen källkod.</p>

<p>Finna är en del av undervisnings- och kulturministeriets projekt Det nationella digitala biblioteket (NDB). Mer information om NDB och Finna finns på webbplatsen <a href="http://www.kdk.fi/sv">www.kdk.fi</a></p>
{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}

<!-- END of: Content/about.sv.tpl -->
