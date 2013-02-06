<!-- START of: Content/register_details.sv.tpl -->

{assign var="title" value="Registerbeskrivning"}
{capture append="sections"}{literal}

<h2>Registerbeskrivning för sökportalen Finnas kundregister</h2>
        
<p><strong>Registeransvarig:</strong><br />
Nationalbiblioteket<br />
Biblioteksnättjänster<br />
PB 26 (Industrigatan 23)<br />
00014 Helsingfors universitet<br />
Tfn 09 1911</p>

<p><strong>Ansvarsperson för registerärenden:</strong><br />
Aki Lassila, utvecklingschef<br />
Biblioteksnättjänster<br />
Nationalbiblioteket<br />
PB 26 (Industrigatan 23)<br />
00014 Helsingfors universitet<br />
Tfn 09 1911<br />
E-post: aki.lassila(at)helsinki.fi</p>

<p><strong>Regelmässiga informationskällor:</strong><br />
Personuppgifterna fås från kundernas hemorganisationers kundregister. Användarna sparar själva de uppgifter som behövs för de individualiserade funktionerna.</p>

<p><strong>Regelmässigt överlåtande av uppgifter:</strong><br />
Uppgifterna lämnas inte ut.</p>

<p><strong>Registrets namn:</strong><br />
Sökportalen Finnas kundregister.</p>

<p><strong>Grund för förande av registret:</strong><br />
Registret har grundats för att upprätthålla kunduppgifternas entydighet.</p>

<p><strong>Registrets ändamål:</strong><br />
Registret används för att identifiera användarna, i syfte att erbjuda dem individualiserade tjänster. Ett exempel på individualiserade tjänster är sökbevakningstjänsten, som skickar kunden e-post om nya sökträffar.</p>

<p><strong>Typ av uppgifter i registret:</strong><br />
Användarens basuppgifter: 
</p>
<ul>
  <li>Användarnamn</li>
  <li>Användarspråk</li>
  <li>Namn</li>
  <li>Relation till läroanstalten eller organisationen</li>
  <li>E-postadress</li>
  <li>Bibliotekskortens uppgifter</li>
</ul>

<p><strong>Uppgifter som sparas för de individualiserade funktionerna:</strong><br />
</p>
<ul>
  <li>Användarens personliga inställningar</li>
  <li>Favoriter</li>
  <li>Taggar</li>
  <li>Kommentarer</li>
  <li>Recensioner</li>
  <li>Reservationer</li>
  <li>Lån</li>
  <li>Avgifter</li>
  <li>Referenser som sparats bokhyllan</li>
  <li>Material som sparats i Mitt material</li>
  <li>Tidskrifter som sparats i Mina tidskrifter</li>
</ul>

<p><strong>Dataskyddsprinciper:</strong><br />
Uppgifterna sparas endast i elektronisk form. Endast administratörerna, som identifieras med hjälp av användarnamn och lösenord, har tillgång till uppgifterna.</p>

<p><strong>Personuppgifternas förvaringstid:</strong><br />
Personuppgifterna sparas i 12 månader efter den senaste inloggningen.</p>

<p><strong>Rätt till insyn:</strong><br />
Om en användare vill kontrollera vilka uppgifter om honom eller henne som har sparats i registret, ska han eller hon ta kontakt med ansvarspersonen för registerärenden.</p>

<p><strong>Korrigering av felaktiga uppgifter:</strong><br />
Om de personuppgifter som överförts från hemorganisationen är felaktiga, ska användaren kontakta sin hemorganisation. Vid behov kan användaren också kontakta ansvarspersonen för registerärenden.</p>


{/literal}{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}
<!-- END of: Content/register_details.sv.tpl -->
