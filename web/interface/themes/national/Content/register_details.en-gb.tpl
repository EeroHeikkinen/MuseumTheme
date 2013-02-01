<!-- START of: Content/register_details.en-gb.tpl -->

{assign var="title" value="Register Details"}
{capture append="sections"}{literal}

<h2>Description of the client register data file for the Finna search portal</h2>
        
<p><strong>Data file controller:</strong><br />
National Library of Finland<br />
Library Network Services<br />
P.O. Box 26 (Teollisuuskatu 23)<br />
00014 University of Helsinki<br />
Phone: 09 1911</p>

<p><strong>Data file contact person:</strong><br />
Aki Lassila, Head of Development<br />
Library Network Services<br />
National Library of Finland<br />
P.O. Box 26 (Teollisuuskatu 23)<br />
00014 University of Helsinki<br />
Phone: 09 1911<br />
Email: aki.lassila(at)helsinki.fi</p>

<p><strong>Regular sources of data:</strong><br />
Personal information is received from the registers of the clients’ home organisations. For personalised functions, the information must be recorded by the user.</p>

<p><strong>Regular disclosure of information:</strong><br />
The information will not be disclosed to third parties.</p>

<p><strong>Name of data file:</strong><br />
Client register for the Finna search portal.</p>

<p><strong>Reason for maintaining the data file:</strong><br />
This register has been established to maintain the unambiguity of client identities.</p>

<p><strong>Data file usage:</strong><br />
This register will be used to identify users in order to enable personalised services, such as the news alert service which will send an email to the client whenever new entries relating to a search are added to the database.</p>

<p><strong>Data types included in the file:</strong><br />
Basic user information: 
</p>
<ul>
  <li>Username</li>
  <li>Language</li>
  <li>Name</li>
  <li>Relationship to education institution or other organisation</li>
  <li>Email</li>
  <li>Library card information</li>
</ul>

<p><strong>Data collected for personalised services:</strong><br />
</p>
<ul>
  <li>Personal settings</li>
  <li>Favourites</li>
  <li>Tags</li>
  <li>Comments</li>
  <li>Reviews</li>
  <li>Reservations</li>
  <li>Loans</li>
  <li>Fees</li>
  <li>References added to bookshelf</li>
  <li>Material added to My materials</li>
  <li>Journal references added to My journals</li>
</ul>

<p><strong>Principles of data security:</strong><br />
The information is recorded exclusively in an electronic format. Only system administrators, authenticated by username and password, may access the information.</p>

<p><strong>Archiving period for personal information:</strong><br />
Personal information will be stored in the service until 12 months have passed from the last login.</p>

<p><strong>Right to inspect the data file:</strong><br />
Users may contact the data file contact person to inspect their personal information stored on the server.</p>

<p><strong>Rectifying inaccurate information:</strong><br />
If the information retrieved from the user’s home organisation is inaccurate, the user should contact the organisation in question. If necessary, the user may also contact the data file contact person.</p>


{/literal}{/capture}
{include file="$module/content.tpl" title=$title sections=$sections}
<!-- END of: Content/register_details.en-gb.tpl -->
