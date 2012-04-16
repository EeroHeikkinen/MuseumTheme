<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">

    <xsl:param name="collectionURL"/>
    <xsl:param name="recordURL"/>
    <xsl:param name="titleText"/>
    <xsl:param name="collectionID"/>
    <xsl:param name="recordID"/>

    <xsl:template match="/">
        <div id="treeList">
        <xsl:apply-templates select="//root">
        </xsl:apply-templates>
        </div>
    </xsl:template>

    <xsl:template match="item">
        <ul>
        <xsl:variable name="id" select="@id" />
        <li>
          <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
          <xsl:if test="$recordID = $id">
            <xsl:attribute name="class">currentRecord</xsl:attribute>
          </xsl:if>
          <a href="{$collectionURL}/{$collectionID}/ArchivalTree?recordID={$id}#tabnav" title="{$titleText}">
              <xsl:value-of select="./content/name" />
          </a>
          <xsl:apply-templates select="item"/>
      </li>
      </ul>
    </xsl:template>

</xsl:stylesheet>
