<!-- START of: Content/terms_conditions.en-gb.tpl -->

{assign var="title" value="Terms & conditions"}
{capture append="sections"}{literal}

<h2>Using the Finna collections</h2>

<p>Finna users may search for information from the collections of archives, libraries and museums. Finna includes: </p>

<p>Searchable and browsable descriptive metadata texts on the available materials. This metadata is presented in search results and can be used freely by all.</p>

<p>If the material which the metadata describes is available online, Finna will include a link to the website of the organisation which controls the material in question. Statutory or contractual rights and restrictions may apply to materials available through such websites. Any rights and restrictions are specified on the websites.</p>

<p>For some search results, Finna includes an image of the material with the metadata, for example a picture of a museum piece, a work of art, a photograph or a book cover. Such preview images may be subject to use restrictions similar to those applicable to materials on the websites of participating organisations.</p>

{/literal}{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}
<!-- END of: Content/terms_conditions.en-gb.tpl -->