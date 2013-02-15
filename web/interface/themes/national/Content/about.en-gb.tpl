<!-- START of: Content/about.en-gb.tpl -->

{assign var="title" value="About Finna"}
{capture append="sections"}
<h2>The treasures of Finland’s archives, libraries and museums with a single search.</h2>

<p class="ingress">Finna is a user-friendly online service which provides access to the collections and services of archives, libraries and museums.</p>

<p>The test version of Finna was published in December 2012. Finna is under constant development, and new functionalities are being added. New organisations continue to join Finna in stages. The test version includes materials from the following organisations:</p>

<p>
	<ul>
	  <li>The Library of the University of Jyväskylä</li>
	  <li>The National Archives of Finland</li>
	  <li>The National Library of Finland</li>
	  <li>Lusto - the Finnish Forest Museum and the other museums participating in the Kantapuu database (Nurmes Town Museum, Pielinen Museum, the Ilomantsi Museum Foundation, the Forestry Museum of Lapland and the Verla Mill Museum.</li>
	  <li>National Board of Antiquities</li>
          <li>Tuusula Art Museum</li>
	  <li>The Finnish National Gallery</li>
	</ul>
</p>

<p>Finna is intended for all seekers of information and inspiration. Through the online service, you can easily search for material such as images, documents, newspapers, academic research articles, videos and sound recordings on any topic. The materials are all available through the same service, and the user does not have to know beforehand which organisation originally produced the material. In future, it will be possible to use the digital services of archives, libraries and museums through Finna.</p>

<p>The National Library of Finland established Finna in cooperation with archives, libraries and museums, and continues as its main administrator. Finna is built on VuFind and other open source programmes.</p>

<p>Finna is part of the National Digital Library (NDL) project of the Ministry of Education and Culture. For more information on the NDL project and Finna, please visit the project website at  <a href="http://www.kdk.fi/en">www.kdk.fi/en</a></p>
{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}

<!-- END of: Content/about.en-gb.tpl -->