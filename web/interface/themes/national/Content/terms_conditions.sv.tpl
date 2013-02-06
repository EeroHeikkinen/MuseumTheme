<!-- START of: Content/terms_conditions.sv.tpl -->

{assign var="title" value="Användarvillkor"}
{capture append="sections"}{literal}

<h2>Användningen av materialet i Finna</h2>

<p>I Finna kan användarna söka i arkivens, bibliotekens och museernas material. I Finna kan man: </p>

<p>Läsa beskrivningar av materialet, s.k. metadata. Alla kan fritt använda metadata som visas i samband med sökresultaten.</p>

<p>Om det material som beskrivs i metadatat finns i digital form på nätet, ger Finna länken till den organisation som förvaltar materialet. Materialet på organisationens webbplats kan omfattas av rättigheter eller begränsningar som regleras i lag eller avtal. Information om rättigheterna och begränsningarna finns på den förvaltande organisationens webbplats.</p>

<p>I samband med vissa sökträffar i Finna visas en bild av det material som beskrivs i metadatat, t.ex. en bild av museiföremålet, konstverket, fotot eller boken i fråga. Användningen av de här bilderna kan vara begränsad på samma sätt som användningen av materialet på de förvaltande organisationernas webbplatser.</p>

{/literal}{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}
<!-- END of: Content/terms_conditions.sv.tpl -->