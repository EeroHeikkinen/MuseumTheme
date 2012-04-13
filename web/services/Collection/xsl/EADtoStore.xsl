<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
   
    <xsl:template match="/">
        <root>
            <item>
                <xsl:attribute name="id">
                <xsl:value-of select="php:functionString('convertID', .//archdesc/did/unitid)" />
                </xsl:attribute>
                <content>
                  <name>
                      <xsl:value-of select="//archdesc/did/unittitle"></xsl:value-of>
                  </name>
              </content>
            <xsl:apply-templates select="//archdesc/dsc">
            </xsl:apply-templates>
            </item>
        </root>
    </xsl:template>

    <xsl:template match="c">
        <xsl:variable name="id">
            <xsl:value-of select="php:functionString('convertID', ./did/unitid)" />
        </xsl:variable>
        <item><xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
          <content>
              <name>
                  <xsl:value-of select="./did/unittitle" />
              </name>
          </content>
          <xsl:apply-templates select="c"/>
      </item>
    </xsl:template>
    
</xsl:stylesheet>
