<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	
    <xsl:output method="html" encoding="UTF-8" indent="yes" />
	

    <!--main template for the basic document structure with songs, albums and artists-->
    <!--contains table of songs, table of albums and list of artists and lyrics-->
    <xsl:template match="/">

        <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
        <html>

            <head>
                <title>My Library</title>
                <!--we import two css files, for viewing on a screen and for printing-->
                <!--including the viewport meta tag for responsiveness on mobile devices-->
                <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                <link rel="stylesheet" type="text/css" media="screen" href="music-library-screen.css"/>
                <link rel="stylesheet" type="text/css" media="print" href="music-library-print.css"/>

                <!--javascript function for toggling between dark and light mode-->
                <script>
                    function myFunction() { document.documentElement.classList.toggle("dark-mode");}
                </script>
            </head>

            <body>
                <h1>My music library</h1>

                <!--toggle dark mode button-->
                <button onclick="myFunction()" class="toggle">Dark mode</button>
                
                <!--table of songs-->
                <xsl:comment>table of songs</xsl:comment>
                <h2>Songs</h2>
                <div class="table_wrapper">
                    <table class="wide">

                        <!--we copy the table header from variables-->
                        <xsl:copy-of select="$songs_table_header"/>
                        <tbody>
                        
                            <!--table of songs sorted by song's name-->
                            <xsl:apply-templates select="music-library/songs/song">
                                <xsl:sort select="name"/>
                            </xsl:apply-templates>
                        </tbody>
                    </table>
                </div>

                <!--table of albums-->
                <h2>Albums</h2>
                <xsl:comment>table of albums</xsl:comment>
                <div class="table_wrapper">
                    <xsl:comment>table of albums</xsl:comment>
                    <table class="wide">
                        <xsl:copy-of select="$albums_table_header"/>
                        <tbody>
                            <!--table of albums in the library-->
                            <xsl:apply-templates select="music-library/albums/album"/>
                        </tbody>
                    </table>
                </div>
                                
                <!--list of artists and lyrics-->
                <div>
                    <xsl:comment>list of artists</xsl:comment>
                    <div id="artists">
                        <h2>Artists</h2>
                        <xsl:apply-templates select="music-library/artists/artist"/>
                    </div>
                    <xsl:comment>list of lyrics</xsl:comment>
                    <div id="lyrics">
                        <h2>Lyrics</h2>
                        <xsl:apply-templates select="//song[./lyrics]" mode="lyrics"/>
                    </div>

                    <!--clr div after float divs-->
                    <div class="clr"/>
                </div>

                <!--source visible when printing the document-->
                <div class="print">
                    Source: <a href="https://wikipedia.org">Wikipedia</a>
                </div>
            </body>
        </html>
    </xsl:template>
	

    <!--template for entries in songs table-->
    <xsl:template match="song">

        <!--variables for songs references to their artist or album-->
        <xsl:variable name="ref_artist" select="@artist-ref"></xsl:variable>
        <xsl:variable name="ref_album" select="@album-ref"></xsl:variable>

        <!--table rows to be inserted-->
        <tr>
            <td label="name">
                <xsl:value-of select="name"/>
            </td>

            <!--artists name with embedded link from the template-->
            <td label="artist">
                <xsl:apply-templates select="//artist[@id=$ref_artist]" mode="artist_only_link"/>
            </td>

            <!--find album name, if there is no album, it is a single-->
            <td label="album">
                <xsl:choose>
                    <xsl:when test="//album[@id=$ref_album]">
                        <xsl:value-of select="//album[@id=$ref_album]/name"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <span class="generated">single</span>
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <!--other information such as year, rating, genre or length of the song-->
            <td label="year">
                <xsl:value-of select="year"/>
            </td>
            <td label="genre">
                <xsl:value-of select="genre"/>
            </td>
            <td label="rating">
                <xsl:value-of select="rating/@stars"/>
                <xsl:text>/5</xsl:text>
            </td>
            <td label="length">
                <xsl:value-of select="track-info/length"/>
            </td>

            <!--find lyrics, if found, print "yes" with embedded link to the lyrics at the bottom, else no-->
            <td label="lyrics">
                <xsl:choose>
                    <xsl:when test="lyrics">
                        <xsl:element name="a">
                            <xsl:attribute name="href">
                                <xsl:text>#</xsl:text>
                                <xsl:value-of select="@id"/>
                                <xsl:text>_lyrics</xsl:text>
                            </xsl:attribute>
                            <xsl:text>yes</xsl:text>
                        </xsl:element>
                    </xsl:when>
                    <xsl:otherwise>
                        <span class="generated">no</span>
                    </xsl:otherwise>
                </xsl:choose>
            </td>

        </tr>
    </xsl:template>


    <!--returns artists name with embedded link from his id, this is used in songs table-->
    <xsl:template match="artist" mode="artist_only_link">
        <xsl:element name="a">
            <xsl:attribute name="href">
                <xsl:text>#</xsl:text>
                <xsl:value-of select="@id"/>
            </xsl:attribute>
            <xsl:value-of select="name"/>
        </xsl:element>
    </xsl:template>


    <!--template for entries in album table with information about the album-->
    <xsl:template match="album">

        <!--variable for current album artist-->
        <xsl:variable name="ref_artist" select="@artist-ref"/>
        
        <tr>
            <!--table entries with albums information (name, it's artist, number of tracks,...)-->
            <td label="name">
                <xsl:value-of select="name"/>
            </td>
            <td label="artist">
                <xsl:apply-templates select="//artist[@id=$ref_artist]" mode="artist_only_link"/>
            </td>
            <td label="tracks">
                <xsl:value-of select="tracks"/>
            </td>
            <td label="tracks in library">
                <xsl:value-of select="count(//song[@artist-ref=$ref_artist])"/>
            </td>

            <!--length of the album, if not found, print "-"-->
            <td label="length">
                <xsl:choose>
                    <xsl:when test="length">
                        <xsl:value-of select="length"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>-</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>

            <td label="rating">
                <xsl:value-of select="rating/@stars"/>
                <xsl:text>/5</xsl:text>
            </td>

            <!--print yes if albums rating is above the average, else no-->
            <td label="above average?">
                <xsl:choose>
                    <xsl:when test="./rating/@stars > sum(//album/rating/@stars) div count(//album)">
                        <xsl:text>yes</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>no</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </td>

        </tr>
    </xsl:template>


    <!--template for artist list below the tables-->
    <xsl:template match="artist">

        <!--variable of artists id to use in xpath queries-->
        <xsl:variable name="ref_artist" select="@id"/>
        
        <div class="artist">

            <!--h3 heading with id, previous table entries reference this id-->
            <h3>
                <xsl:attribute name="id">
                    <xsl:value-of select="@id"/>
                </xsl:attribute>
                <xsl:value-of select="name"/>
            </h3>
            <xsl:value-of select="origin"/><br/>

            <!--if members element is found (it is a band), print all of the members in a table-->
            <xsl:if test="members">
                <h4>Members:</h4>
                <table class="members">
                    
                    <!--copy of the global variable with members table header-->
                    <xsl:copy-of select="$members_table_header" />
                    <tbody>
                        <xsl:for-each select="members/member">
                            <tr>
                                <td><xsl:value-of select="name"/></td>
                                <td><xsl:value-of select="role"/></td>
                            </tr>
                        </xsl:for-each>
                    </tbody>
                </table>
            </xsl:if>
            
            <!--if there is a biography, print it-->
            <xsl:if test ="biography">
                <h4>Biography:</h4>
                <div class="biography">

                    <!--copy the biography's <p> elements-->
                    <!--another way: <xsl:copy-of select="biography"/>-->
                    <xsl:for-each select="biography/p">
                        <xsl:copy>
                            <xsl:apply-templates/>
                        </xsl:copy>
                    </xsl:for-each>
                    
                </div>
            </xsl:if>
            
            <!--send artist reference to best-song template to print highest rated song-->
            <xsl:call-template name="best-song">
                <xsl:with-param name="ref_artist">
                    <xsl:value-of select="@id"/>
                </xsl:with-param>
            </xsl:call-template>

            <!--send the newest album from current artist to the new-album template-->
            <xsl:apply-templates select="//album[@artist-ref=$ref_artist and not(preceding-sibling::album[@artist-ref=$ref_artist]/year > ./year) and not(following-sibling::album[@artist-ref=$ref_artist]/year > ./year)]" mode="newest-album"/>

        </div>
    </xsl:template>


    <!--template for best song, used by main artist template-->
    <xsl:template name="best-song">

        <!--parameter sent by parent template-->
        <xsl:param name="ref_artist"/>

        <!--variable for the best song name-->
        <xsl:variable name="song" select="//song[@artist-ref=$ref_artist and not(preceding-sibling::song/rating/@stars > ./rating/@stars) and not(following-sibling::song/rating/@stars > ./rating/@stars)]"/>

        <!--if there is a song in the variable, print it-->
        <xsl:if test="$song">
            <h4>Best song:</h4>
            <xsl:value-of select="$song/name"/>
            <xsl:text> (</xsl:text>
            <xsl:value-of select="$song/year"/>
            <xsl:text>)</xsl:text>
        </xsl:if>

    </xsl:template>


    <!--template for newest album used by main artist template-->
    <xsl:template match="album" mode="newest-album">
        <h4>Newest album: </h4>
        <xsl:value-of select="name"/>
        <xsl:text> (</xsl:text>
        <xsl:value-of select="year"/>
        <xsl:text>) </xsl:text>
    </xsl:template>

    
    <!--template for printing lyrics content-->
    <xsl:template match="song" mode="lyrics">
        <xsl:variable name="ref_artist" select="@artist-ref"/>

        <div class="lyric">

            <!--heading with id attribute to be referenced by previous table entries-->
            <h4>
                <xsl:attribute name="id">
                    <xsl:value-of select="@id"/>
                    <xsl:text>_lyrics</xsl:text>
                </xsl:attribute>
                <xsl:value-of select="name"/>
                <xsl:text> (</xsl:text>
                <xsl:value-of select="//artist[@id=$ref_artist]/name"/>
                <xsl:text>)</xsl:text>
            </h4>

            <!--lyrics itself, which contain <br> tags in the xml file that we won't escape-->
            <xsl:value-of select="lyrics" disable-output-escaping='yes'/>
            
        </div>
    </xsl:template>


    <!--global variables with headers for all tables in the document-->
    <xsl:variable name="songs_table_header">
        <thead>
            <tr>
                <th>Name</th>
                <th>Artist</th>
                <th>Album</th>
                <th>Year</th>
                <th>Genre</th>
                <th>Rating</th>
                <th>Length</th>
                <th>Lyrics</th>
            </tr>
        </thead>
    </xsl:variable>

    <xsl:variable name="albums_table_header">
        <thead>
            <tr>
                <th>Name</th>
                <th>Artist</th>
                <th>Tracks</th>
                <th>Tracks in library</th>
                <th>Length</th>
                <th>Rating</th>
                <th>Above average?</th>
            </tr>
        </thead>
    </xsl:variable>

    <xsl:variable name="members_table_header">
        <thead>
        <tr>
            <th>Name</th>
            <th>Role</th>
        </tr>
        </thead>
    </xsl:variable>

</xsl:stylesheet>