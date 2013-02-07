<!-- START of: Content/searchhelp.fi.tpl -->

{assign var="title" value="Search Tips"}
{capture append="sections"}{literal}
  <h2 name="Wildcard Searches">Wildcard Searches</h2>
  <p>To perform a single character wildcard search use the <strong>?</strong> symbol.</p>
  <p>For example, to search for "text" or "test" you can use the search:</p>
  <pre class="code">te?t</pre>
  <p>To perform a multiple character, 0 or more, wildcard search use the <strong>*</strong> symbol.</p>
  <p>For example, to search for test, tests or tester, you can use the search: </p>
  <pre class="code">test*</pre>
  <p>You can also use the wildcard searches in the middle of a term.</p>
  <pre class="code">te*t</pre>
  <p>Note: You cannot use a * or ? symbol as the first character of a search.</p>
{/literal}{/capture}{capture append="sections"}{literal}
  <h2 name="Fuzzy Searches">Fuzzy Searches</h2>
  <p>Use the tilde <strong>~</strong> symbol at the end of a <strong>Single</strong> word Term. For example to search for a term similar in spelling to "roam" use the fuzzy search: </p>
  <pre class="code">roam~</pre>
  <p>This search will find terms like foam and roams.</p>
  <p>An additional parameter can specify the required similarity. The value is between 0 and 1, with a value closer to 1 only terms with a higher similarity will be matched. For example:</p>
  <pre class="code">roam~0.8</pre>
  <p>The default that is used if the parameter is not given is 0.5.</p>
{/literal}{/capture}{capture append="sections"}{literal}
  <h2 name="Proximity Searches">Proximity Searches</h2>
  <p>
    Use the tilde <strong>~</strong> symbol at the end of a <strong>Multiple</strong> word Term.
    For example, to search for economics and keynes that are within 10 words apart:
  </p>
  <pre class="code">"economics Keynes"~10</pre>
{/literal}{/capture}{capture append="sections"}{literal}
  <h2 name="Range Searches">Range Searches</h2>
  <p>
    To perform a range search you can use either the <strong>{ }</strong> or the <strong>[ ]</strong> characters. 
    The <strong>{ }</strong> characters are exclusive and the <strong>[ ]</strong> characters are inclusive of the
    upper and lower bounds.
    For example to search for a term that starts with either B, or C:
  </p>
  <pre class="code">{A TO D}</pre>
  <p>
    The searches can be done with numeric fields such as the Year:
  </p>
  <pre class="code">[2002 TO 2003]</pre>
{/literal}{/capture}{capture append="sections"}{literal} 
  <h2 name="Boosting a Term">Boosting a Term</h2>
  <p>
    To apply more value to a term, you can use the <strong>^</strong> character.
    For example, you can try the following search:
  </p>
  <pre class="code">economics Keynes^5</pre>
  <p>Which will give more value to the term "Keynes"</p>
{/literal}{/capture}{capture append="sections"}{literal}
  <h2 name="Boolean operators">Boolean Operators</h2>
  <p>
    Boolean operators allow terms to be combined with logic operators.
    The following operators are allowed: <strong>AND</strong>, <strong>+</strong>, <strong>OR</strong>, <strong>NOT</strong> and <strong>-</strong>.
  </p>
  <p>Note: Boolean operators must be ALL CAPS</p>
  
  <h3 name="AND">AND</h3>
  <p>The <strong>AND</strong> operator is the default conjunction operator. This means that if there is no Boolean operator between two terms, the AND operator is used. The AND operator matches records where both terms exist anywhere in the field of a record.</p>
  <p>To search for records that contain "economics" and "Keynes" use the query: </p>
  <pre class="code">economics Keynes</pre>
  <p>or</p>
  <pre class="code">economics AND Keynes</pre>
  
  <h3 name="+">+</h3>
  <p>The "+" or required operator requires that the term after the "+" symbol exist somewhere in the field of a record.</p>
  <p>To search for records that must contain "economics" and may contain "Keynes" use the query:</p>
  <pre class="code">+economics Keynes</pre>
  
  <h3 name="OR">OR</h3>
  <p>The OR operator links two terms and finds a matching record if either of the terms exist in a record.</p>
  <p>To search for documents that contain either "economics Keynes" or just "Keynes" use the query:</p>
  <pre class="code">"economics Keynes" OR Keynes</pre>
  
  <h3 name="NOT">NOT</h3>
  <p>The NOT operator excludes records that contain the term after NOT.</p>
  <p>To search for documents that contain "economics" but not "Keynes" use the query: </p>
  <pre class="code">economics NOT Keynes</pre>
  <p>Note: The NOT operator cannot be used with just one term. For example, the following search will return no results:</p>
  <pre class="code">NOT economics</pre>
  
  <h3 name="-">-</h3>
  <p>The <Strong>-</strong> or prohibit operator excludes documents that contain the term after the "-" symbol.</p>
  <p>To search for documents that contain "economics" but not "Keynes" use the query: </p>
  <pre class="code">economics -Keynes</pre>
  
  <h3 name="Phrase searches">Phrase searches</h3>
  <p>Search term or terms enclosed in quotation marks will be used literally.</p>
  <p>To search for records containing the exact phrase "ancient history" and not e.g. "history in the ancient times":</p>
  <pre class="code">"ancient history"</pre>
  <p>Also single words can be enclosed in quotation marks to use the term literally, ignoring different conjugations.</p>
{/literal}{/capture}{capture append="sections"}{literal}
  <h2>Advanced Searching Tips</h2>

  <h3 name="Search Fields">Search Fields</h3>
  <p>When you first visit the Advanced Search page, you are presented with 
     several search fields.  In each field, you can type the keywords you 
     want to search for. Search operators are allowed.</p>
  <p>Each field is accompanied by a drop-down that lets you specify the type 
     of data (title, author, etc.) you are searching for.  You can mix and
     match search types however you like.</p>
  <p>The "Match" setting lets you specify how multiple search fields should
     be handled.</p>
  <ul>
    <li>ALL Terms - Return only records that match every search field.</li>
    <li>ANY Terms - Return any records that match at least one search field.</li>
    <li>NO Terms -- Return all records EXCEPT those that match search fields.</li>
  </ul>
  <p>The "Add Search Field" button may be used to add additional search fields
       to the form.  You may use as many search fields as you wish.</p>
  <h3 name="Search Groups">Search Groups</h3>
  <p>For certain complex searches, a single set of search fields may not be 
     enough.  For example, suppose you want to find books about the history of
     China or India.  If you did an ALL Terms search for China, India, and 
     History, you would only get books about China AND India.  If you did an
     ANY Terms search, you would get books about history that had nothing to
     do with China or India.</p>
  <p>Search Groups provide a way to build searches from multiple groups of
     search fields.  Every time you click the "Add Search Group" button, a new
     group of fields is added.  Once you have multiple search groups, you can
     remove unwanted groups with the "Remove Search Group" button, and you can
     specify whether you want to match on ANY or ALL search groups.</p>
  <p>In the history of China or India example described above, you could solve
     the problem using search groups like this:</p>
  <ul>
    <li>In the first search group, enter "India" and "China" and make sure that
        the "Match" setting is "ANY Terms."</li>
    <li>Add a second search group and enter "history."</li>
    <li>Make sure the match setting next to the Search Groups header is set to
        "ALL Groups."</li>
  </ul>
{/literal}{/capture}
{include file="$module/content.tpl" title=$title sections=$sections menu=true}
<!-- END of: Content/searchhelp.fi.tpl -->