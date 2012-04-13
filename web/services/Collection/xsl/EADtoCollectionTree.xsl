<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
   
   <xsl:param name="baseURL"><xsl:value-of select="php:function('getBaseURL', 'collectionTree')"/></xsl:param>
   <xsl:param name="collectionCode"><xsl:value-of select="php:function('getcollectionCode')"/></xsl:param>
   <xsl:param name="treeCode"><xsl:value-of select="php:function('getTreeCode')"/></xsl:param>
   <xsl:param name="titleText"><xsl:value-of select="php:function('getTitleText')"/></xsl:param>
   
    <xsl:template match="/">
        <root>
        <xsl:apply-templates select="//archdesc/dsc">
        </xsl:apply-templates>
        </root>
    </xsl:template>

    <xsl:template match="c">
        <xsl:variable name="id"><xsl:value-of select="php:functionString('getRecordId', ./did/unitid)"/></xsl:variable>
        <item><xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
          <content>
              <name href="{$baseURL}/{$collectionCode}?tree={$treeCode}/id={$id}" title="{$titleText}">
                  <xsl:value-of select="php:functionString('getTitle', ./did/unittitle)"/>
              </name>
          </content>
          <xsl:apply-templates select="c"/>
      </item>
    </xsl:template>
    
</xsl:stylesheet>
