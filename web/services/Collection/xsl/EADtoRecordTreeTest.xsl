<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
   
<!--   <xsl:param name="baseURL"><xsl:value-of select="php:function('getBaseURL', 'recordTree')"/></xsl:param>
   <xsl:param name="titleText"><xsl:value-of select="php:function('getTitleText')"/></xsl:param>
-->   
    <xsl:template match="/">
        <root>
        <xsl:apply-templates select="//archdesc/dsc">
        </xsl:apply-templates>
        </root>
    </xsl:template>

    <xsl:template match="c | c01 | c02">
        <xsl:variable name="id"><xsl:value-of select="./did/unitid"/></xsl:variable>
        <item><xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
          <content>
              <name href="baseURL/{$id}/Archivaltree#tabnav" title="titleText">
                  <xsl:value-of select="./did/unittitle"/>
              </name>
          </content>
          <xsl:apply-templates select="c | c01 | c02"/>
      </item>
    </xsl:template>
    
</xsl:stylesheet>
