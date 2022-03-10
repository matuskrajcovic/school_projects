<?xml version="1.0"?>
<xsl:stylesheet 
	version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns="http://www.w3.org/2000/svg" 
	xmlns:xlink="http://www.w3.org/1999/xlink">


	<xsl:output 
		method="xml" 
		version="1.0" 
		encoding="UTF-8" 
		indent="yes" 
		standalone="no" 
		doctype-public="-//W3C//DTD SVG 1.1//EN" 
		doctype-system="http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"/>


	<!--global color variables-->
	<xsl:variable name="main-color" select="'#555'"/>
	<xsl:variable name="secondary-color" select="'#777'"/>
	<xsl:variable name="highlight-color" select="'#222'"/>
	<xsl:variable name="rating-bar-color" select="'#004c80'"/>
	<xsl:variable name="genre-bar-color" select="'#007d53'"/>


	<!--main template-->
	<xsl:template match="/">
		
		<!--svg element with 1200 800 viewbox-->
		<svg viewBox="0 0 1200 800" role="img" version="1.1" baseProfile="full">

			<title id="title">
				Music gallery
			</title>
			<desc id="desc">
				Number of song occurences in the music library according to song's rating and genre.
			</desc>

			<!--background-->
			<rect x="10" y="10" width="1180" height="780" fill-opacity="0.04" rx="60" ry="60" fill="$secondary-color"/>

			<!--main group moved 100 units and with defualt font, font size and font color-->
			<g transform="translate(110, 90)" font-family="Segoe UI" font-size="16">
				<xsl:attribute name="font-color">
					<xsl:value-of select="$highlight-color"/>
				</xsl:attribute>

				<!--group for all lines in the chart, with default stroke color and width-->
				<g stroke-width="1">
					<xsl:attribute name="stroke">
						<xsl:value-of select="$main-color"/>
					</xsl:attribute>
					
					<!--main axes-->
					<g stroke-width="2">
						<line x1="0" x2="0" y1="0" y2="600"></line>
						<line x1="0" x2="1000" y1="600" y2="600"></line>
					</g>

					<!--horizontal helping lines-->
					<g stroke-dasharray="10 10">
						<xsl:attribute name="stroke">
							<xsl:value-of select="$secondary-color"/>
						</xsl:attribute>

						<line x1="0" x2="1000" y1="40" y2="40"></line>
						<line x1="0" x2="1000" y1="120" y2="120"></line>
						<line x1="0" x2="1000" y1="200" y2="200"></line>
						<line x1="0" x2="1000" y1="280" y2="280"></line>
						<line x1="0" x2="1000" y1="360" y2="360"></line>
						<line x1="0" x2="1000" y1="440" y2="440"></line>
						<line x1="0" x2="1000" y1="520" y2="520"></line>
					</g>

					<!--Y axis ticks-->
					<g>
						<line x1="-10" x2="0" y1="40" y2="40"></line>
						<line x1="-10" x2="0" y1="120" y2="120"></line>
						<line x1="-10" x2="0" y1="200" y2="200"></line>
						<line x1="-10" x2="0" y1="280" y2="280"></line>
						<line x1="-10" x2="0" y1="360" y2="360"></line>
						<line x1="-10" x2="0" y1="440" y2="440"></line>
						<line x1="-10" x2="0" y1="520" y2="520"></line>
					</g>

					<!--X axis ticks-->
					<g>
						<line x1="60" x2="60" y1="600" y2="610"></line>
						<line x1="160" x2="160" y1="600" y2="610"></line>
						<line x1="260" x2="260" y1="600" y2="610"></line>
						<line x1="360" x2="360" y1="600" y2="610"></line>
						<line x1="460" x2="460" y1="600" y2="610"></line>
						<line x1="600" x2="600" y1="600" y2="610"></line>
						<line x1="700" x2="700" y1="600" y2="610"></line>
						<line x1="800" x2="800" y1="600" y2="610"></line>
						<line x1="900" x2="900" y1="600" y2="610"></line>
					</g>
				</g>

				<!--axis arrows with default fill and stroke color-->
				<g>
					<xsl:attribute name="stroke">
						<xsl:value-of select="$main-color"/>
					</xsl:attribute>
					<xsl:attribute name="fill">
						<xsl:value-of select="$main-color"/>
					</xsl:attribute>

					<polygon points="-5,5 5,5 0,-5"/>
					<polygon points="995,595 995,605 1005,600"/>
				</g>

				<!--group with all text elements with default anchor and baseline-->
				<g text-anchor="middle" dominant-baseline="middle">

					<!--axis names-->
					<g font-weight="bold">
						<text x="-50" y="300" transform="rotate(-90, -50, 300)">Number of songs in gallery</text>
						<text x="260" y="660">Rating</text>
						<text x="750" y="660">Genres</text>
					</g>
					
					<!--Y axis labels-->
					<g text-anchor="end">
						<text x="-15" y="40">7</text>
						<text x="-15" y="120">6</text>
						<text x="-15" y="200">5</text>
						<text x="-15" y="280">4</text>
						<text x="-15" y="360">3</text>
						<text x="-15" y="440">2</text>
						<text x="-15" y="520">1</text>
					</g>

					<!--X axis labels-->
					<g dominant-baseline="hanging">
						<text x="60" y="615" transform="rotate(-10, 60, 615)">1 stars</text>
						<text x="160" y="615" transform="rotate(-10, 160, 615)">2 stars</text>
						<text x="260" y="615" transform="rotate(-10, 260, 615)">3 stars</text>
						<text x="360" y="615" transform="rotate(-10, 360, 615)">4 stars</text>
						<text x="460" y="615" transform="rotate(-10, 460, 615)">5 stars</text>
						<text x="600" y="615" transform="rotate(-10, 600, 615)">pop</text>
						<text x="700" y="615" transform="rotate(-10, 700, 615)">dance</text>
						<text x="800" y="615" transform="rotate(-10, 800, 615)">electropop</text>
						<text x="900" y="615" transform="rotate(-10, 900, 615)">soul</text>
					</g>
				</g>

				<!--dynamic bars called with templates-->
				<g text-anchor="middle" dominant-baseline="middle" font-weight="bold">

					<!--rating bars, sending number of stars as a parameter-->
					<xsl:call-template name="rating_bar">
						<xsl:with-param name="stars"><xsl:value-of select="1"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="rating_bar">
						<xsl:with-param name="stars"><xsl:value-of select="2"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="rating_bar">
						<xsl:with-param name="stars"><xsl:value-of select="3"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="rating_bar">
						<xsl:with-param name="stars"><xsl:value-of select="4"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="rating_bar">
						<xsl:with-param name="stars"><xsl:value-of select="5"/></xsl:with-param>
					</xsl:call-template>

					<!--genre bars, sending genre and x coordinate as parameters (can't calculate x coord from genre)-->
					<xsl:call-template name="genre_bar">
						<xsl:with-param name="genre"><xsl:value-of select="'pop'"/></xsl:with-param>
						<xsl:with-param name="x"><xsl:value-of select="600"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="genre_bar">
						<xsl:with-param name="genre"><xsl:value-of select="'dance'"/></xsl:with-param>
						<xsl:with-param name="x"><xsl:value-of select="700"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="genre_bar">
						<xsl:with-param name="genre"><xsl:value-of select="'electropop'"/></xsl:with-param>
						<xsl:with-param name="x"><xsl:value-of select="800"/></xsl:with-param>
					</xsl:call-template>
					<xsl:call-template name="genre_bar">
						<xsl:with-param name="genre"><xsl:value-of select="'soul'"/></xsl:with-param>
						<xsl:with-param name="x"><xsl:value-of select="900"/></xsl:with-param>
					</xsl:call-template>

				</g>
			</g>
		</svg> 
	</xsl:template>


	<!--template for rating bars with custom color-->
	<xsl:template name="rating_bar">
		<xsl:param name="stars"/>

		<!--sending calculated x coordinate from stars, custom color and count of songs with given rating to the graph_bar template-->
		<xsl:call-template name="graph_bar">
			<xsl:with-param name="x"><xsl:value-of select="40 + ($stars - 1) * 100"/></xsl:with-param>
			<xsl:with-param name="count"><xsl:value-of select="count(//song[rating/@stars= $stars ])"/></xsl:with-param>
			<xsl:with-param name="color"><xsl:value-of select="$rating-bar-color"/></xsl:with-param>
		</xsl:call-template>

	</xsl:template>


	<!--template for genre bars with custom color-->
	<xsl:template name="genre_bar">
		<xsl:param name="genre"/>
		<xsl:param name="x"/>

		<!--sending x coordinate, custom color and count of songs with given genre to the graph_bar template (their genre starts with given genre name)-->
		<xsl:call-template name="graph_bar">
			<xsl:with-param name="x"><xsl:value-of select="$x - 20"/></xsl:with-param>
			<xsl:with-param name="count"><xsl:value-of select="count(//song[starts-with(string(./genre), $genre)])"/></xsl:with-param>
			<xsl:with-param name="color"><xsl:value-of select="$genre-bar-color"/></xsl:with-param>
		</xsl:call-template>

	</xsl:template>


	<!--template for printing bars into the graph-->
	<xsl:template name="graph_bar">
		<xsl:param name="x"/>
		<xsl:param name="count"/>
		<xsl:param name="color"/>

		<!--y coordinate of the bar-->
		<!--if it excedes maximum value of 7, we make it little bit higher than 7's helping line-->
		<xsl:variable name="y">
			<xsl:choose>
				<xsl:when test="7 >= $count">
					<xsl:value-of select="40 + 80 * (7 - $count)"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="20"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		
		<!--height of the bar-->
		<!--fills place between y coordinate and bottom x axis-->
		<xsl:variable name="height">
			<xsl:choose>
				<xsl:when test="7 >= $count">
					<xsl:value-of select="$count * 80"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="580"/>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>

		<!--rectangle itself-->
		<!--we give it attributes according to calculated variables-->
		<rect width="40">
			<xsl:attribute name="fill">
				<xsl:value-of select="$color"/>
			</xsl:attribute>
			<xsl:attribute name="x">
				<xsl:value-of select="$x"/>
			</xsl:attribute>
			<xsl:attribute name="y">
				<xsl:value-of select="$y"/>
			</xsl:attribute>
			<xsl:attribute name="height">
				<xsl:value-of select="$height"/>
			</xsl:attribute>
		</rect>

		<!--if there is a bar, we print it's value at the top of it-->
		<xsl:if test="$count > 0">
			<text>
				<xsl:attribute name="x">
					<xsl:choose>
						<xsl:when test="$count > 7">
							<xsl:value-of select="$x + 12"/>
						</xsl:when>
						<xsl:otherwise>
							<xsl:value-of select="$x + 20"/>
						</xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<xsl:attribute name="y">
					<xsl:value-of select="$y - 15"/>
				</xsl:attribute>
				<xsl:value-of select="$count"/>
			</text>
		</xsl:if>

		<!--if value exceeds 7, we print a special arrow signifying it-->
		<xsl:if test="$count > 7">
			<polygon>
				<xsl:attribute name="fill">
					<xsl:value-of select="$main-color"/>
				</xsl:attribute>

				<xsl:attribute name="points">
					<xsl:value-of select="$x + 23"/>
					<xsl:text>,</xsl:text>
					<xsl:value-of select="$y - 11"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="$x + 33"/>
					<xsl:text>,</xsl:text>
					<xsl:value-of select="$y - 11"/>
					<xsl:text> </xsl:text>
					<xsl:value-of select="$x + 28"/>
					<xsl:text>,</xsl:text>
					<xsl:value-of select="$y - 21"/>
				</xsl:attribute>
			</polygon>
		</xsl:if>
		
	</xsl:template>
	
</xsl:stylesheet>