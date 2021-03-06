<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="utf-8" indent="yes"/>
<xsl:template match="positionList">
	<html>
 		<head>
 			<title>Jobs Positions</title>
			<style>
	<![CDATA[
	#jobs { position: relative; margin: 0 0 15px 0; background: #fff; border: 1px solid #19325a; font-family: Arial, Helvetica, Geneva, sans-serif; }
	.jobs-ad-header { height: 35px; background-color: #19325a; position: relative; }
	#jobs-ad-logo { position: absolute; left: 20px; top: 6px; display:none; }
	.jobs-ad-tabs { position: absolute; top: 10px; right: 0px; }
	.jobs-ad-tab { position: relative; display: block; width: 100px; height: 24px; float: left; background: #ff9900; margin: 0 0 0 5px; text-align: center; font-size: 11px; }
	.jobs-ad-tab a { line-height: 25px; color: #fff; }
	.jobs-lst { list-style-type: square; list-style-position: inside; padding: 5px 5px 7px 5px; margin: 0; font-size: 12px; position: relative; -moz-columns: 3; -webkit-columns: 3; columns: 3; }
	.jobs-lst li { padding: 5px 0 3px 13px; margin: 0 0 0 10px; line-height: 1.2em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
	.jobs-lst li a { color: #000; text-decoration: underline; }
	]]>
			</style>
		</head>
		<body>
			<div id="jobs">
				<div class="jobs-ad-box">
					<div class="jobs-ad-header">
						<div style="display: block;" id="jobs-ad-logo"><a href="%%__REDIRECT%%http://www.jobs.cz/?rps=320" target="_blank" class="jobs-ad-logo" title="Jobs.cz - aktuální nabídka práce"><img src="http://i.lmc.cz/title/images/logo-jobs.svg" alt="Jobs.cz - Inspirujeme k úspěchu - nabídka práce" height="23" width="55"/></a>
						</div>
						<div class="jobs-ad-tabs">
							<div id="jobs-ad-t2" class="jobs-ad-tab"><a href="%%__REDIRECT%%http://www.jobs.cz/prace/?rps=320" target="_blank">Další nabídky</a></div>
						</div>
					</div>
					<div class="jobs-ad-content">
						<div style="display: block;" id="jobs-prace">
							<ul class="jobs-lst">
								<xsl:apply-templates select="position[position() &gt; $offset and position() &lt;= ($offset+$jdsToGet)]"/>
							</ul>
			  			</div>
					</div>
				</div>
			</div>
			<xsl:comment>How Many: <xsl:value-of select="$jdsToGet"/> vs Which Offset: <xsl:value-of select="$offset"/> vs Data From: <xsl:value-of select="/positionList/@created"/></xsl:comment>
		</body>
	</html>
</xsl:template>

<xsl:template match="position">
	<li><a href="%%__REDIRECT%%{url}" target="_blank"><xsl:value-of select="positionName"/></a></li>
</xsl:template>

</xsl:stylesheet>
