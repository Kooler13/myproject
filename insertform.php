<!DOCTYPE html PUBLIC>
<html lang="en">
    <head>
        <style>
            form {
                width: 260px;
                border: 1px solid;
                padding: 10px;
            }
            input, select {
                float: right;
                width: 200px;
            }
        </style>
        <title>insert song</title>
    </head>
    <body>
        <?php
        
            //create a connection to database
            function CreateConnection () {
                //sql connection login details
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "musicdatabase";
                // Create connection
                $conn = new mysqli($servername, $username, $password, $dbname);
                // Check connection
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                return $conn;
            }
            
            function ArtistSelect () {
                $conn = CreateConnection();
                $arArtists = array();
                //sql query thats exectued to get data from database
                $sql = "SELECT * FROM artist";
                $result = mysqli_query($conn, $sql);
                //see if there are rows and populate the arrays
                if (mysqli_num_rows($result) > 0) {
                    // add each row of data to a single song array
                    while($row = mysqli_fetch_assoc($result)) {
                        $arArtist = array("id" => $row["artist_id"],
                                "name" => $row["artist_name"]);
                        //then put the single song into another array
                        array_push($arArtists,$arArtist);
                        $arArtist=null; //empty this array
                    }
                } else {
                    //display error if no records found
                    echo "Error: 0 results";
                }
                //close connection when done
                mysqli_close($conn);
                
                foreach ($arArtists as $artist){
                    echo "<option value='{$artist["id"]}'>{$artist["name"]}</option>";
                }
            }
        
            function AlbumSelect () {
                $conn = CreateConnection();
                $arAlbums = array();
                //sql query thats exectued to get data from database
                $sql = "SELECT * FROM album";
                $result = mysqli_query($conn, $sql);
                //see if there are rows and populate the arrays
                if (mysqli_num_rows($result) > 0) {
                    // add each row of data to a single song array
                    while($row = mysqli_fetch_assoc($result)) {
                        $arArtist = array("id" => $row["album_id"],
                                "name" => $row["album_name"]);
                        //then put the single song into another array
                        array_push($arAlbums,$arArtist);
                        $arArtist=null; //empty this array
                    }
                } else {
                    //display error if no records found
                    echo "Error: 0 results";
                }
                //close connection when done
                mysqli_close($conn);
                
                foreach ($arAlbums as $album){
                    echo "<option value='{$album["id"]}'>{$album["name"]}</option>";
                }
            }
        
            function SongSelect () {
                $conn = CreateConnection();
                $arSongs = array();
                //sql query thats exectued to get data from database
                $sql = "SELECT * FROM song";
                $result = mysqli_query($conn, $sql);
                //see if there are rows and populate the arrays
                if (mysqli_num_rows($result) > 0) {
                    // add each row of data to a single song array
                    while($row = mysqli_fetch_assoc($result)) {
                        $arSong = array("id" => $row["song_id"],
                                "name" => $row["song_name"]);
                        //then put the single song into another array
                        array_push($arSongs,$arSong);
                        $arSong=null; //empty this array
                    }
                } else {
                    //display error if no records found
                    echo "Error: 0 results";
                }
                //close connection when done
                mysqli_close($conn);
                
                foreach ($arSongs as $song){
                    echo "<option value='{$song["id"]}'>{$song["name"]}</option>";
                }
            }

        ?>
        
        <form id="insert" action="insertaction.php" method="post">
            <h2>Insert Song</h2>

            <p><label><b>Name: </b></label>
            <input type="text" placeholder="enter name" name="songname" required></p>

            <p><label><b>Price: </b></label>
            <input type="text" placeholder="enter price" name="songprice" required></p>

            <!-- the submit and cancel buttons -->
            <button type="submit" class="insertbtn">Insert</button>
            <!--<button type="button" class="clearbtn">Clear</button>-->
        </form>
        
        <form id="join" action="joinaction.php" method="post">
            <h2>Join Song to Other Tables</h2>
            
            <p><label><b>Song: </b></label>
            <select form="join" name="songname">
                <?php SongSelect(); ?>
            </select></p>
            
            <p><label><b>Artist: </b></label>
            <select form="join" name="songartist">
                <?php ArtistSelect(); ?>
            </select></p>

            <p><label><b>Album: </b></label>
            <select form="join" name="songalbum">
                <?php AlbumSelect(); ?>
            </select></p>
            
            <button type="submit">Join</button>
        </form>
        
        
    </body>
</html>