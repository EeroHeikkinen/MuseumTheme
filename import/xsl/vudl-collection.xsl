<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    xmlns:xlink="http://www.w3.org/2001/XMLSchema-instance">
    <xsl:output method="xml" indent="yes" encoding="utf-8"/>
    <xsl:param name="institution">My University</xsl:param>
    <xsl:param name="collection">Digital Library</xsl:param>
    <xsl:template match="hierarchy">
        <add>
            <doc>
                <!-- ID -->
                <!-- Important: This relies on an <identifier> tag being injected by the OAI-PMH harvester. -->
                <field name="id">
                    <xsl:value-of select="//collection-hash"/>
                </field>

                <!-- RECORDTYPE -->
                <field name="recordtype">vudlcollection</field>

                <!-- ALLFIELDS -->
                <field name="allfields">
                    <xsl:value-of select="normalize-space(string(//hierarchy))"/>
                </field>

                <!-- INSTITUTION -->
                <field name="institution">
                    <xsl:value-of select="$institution" />
                </field>

                <!-- COLLECTION -->
                <field name="collection">
                    <xsl:value-of select="$collection" />
                </field>

                <!-- FORMAT -->
                <field name="format">Online</field>

                <!-- TITLE -->
                <xsl:if test="//collection-name[normalize-space()]">
                    <field name="title">
                        <xsl:value-of select="//collection-name[normalize-space()]"/>
                    </field>
                    <field name="title_short">
                        <xsl:value-of select="//collection-name[normalize-space()]"/>
                    </field>
                    <field name="title_full">
                        <xsl:value-of select="//collection-name[normalize-space()]"/>
                    </field>
                    <field name="title_sort">
                        <xsl:value-of select="php:function('VuFind::stripArticles', string(//collection-name[normalize-space()]))"/>
                    </field>
                </xsl:if>

                <field name="description">
                    <xsl:value-of select="//description[normalize-space()]"/>
                </field>

                <field name="thumbnail">
                    <xsl:value-of select="//thumbnail[normalize-space()]"/>
                </field>

                <field name="hierarchy_top_id">
                    <xsl:value-of select="//top-hash[normalize-space()]" />
                </field>

                <field name="hierarchy_top_title">
                    <xsl:value-of select="//top-name[normalize-space()]" />
                </field>

                <xsl:if test="//parent-hash[normalize-space()][string-length() > 0]">
                    <field name="hierarchy_parent_id">
                        <xsl:value-of select="//parent-hash[normalize-space()]"/>
                    </field>

                    <field name="hierarchy_parent_title">
                        <xsl:value-of select="//parent-name[normalize-space()]"/>
                    </field>

                    <field name="hierarchy_sequence">
                        <xsl:value-of select="php:function('VuFind::padZeroes', string(//position-in-collection[normalize-space()]), 15, 'c')"/>
                    </field>
                </xsl:if>

                <field name="is_hierarchy_id">
                    <xsl:value-of select="//collection-hash[normalize-space()]"/>
                </field>

                <field name="is_hierarchy_title">
                    <xsl:value-of select="//collection-name[normalize-space()]"/>
                </field>
            </doc>
        </add>
    </xsl:template>
</xsl:stylesheet>
