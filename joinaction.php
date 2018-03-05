<!DOCTYPE html PUBLIC>
<html lang="en">
    <head>
        <title></title>
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
        
            function ArtistJoin ($name, $artist) {
                $conn = CreateConnection();
                //sql query thats exectued to get data from database
                $sql = "INSERT INTO song_artist(song_id, artist_id) VALUES ('$name','$artist')";
                try {
                    $execute = mysqli_query($conn, $sql);
                    echo "song_artist Join Successful <br/>";
                } catch (Exception $e){
                    echo "Error: {$e}";
                }
                //close connection when done
                mysqli_close($conn);
            }
            
             function AlbumJoin ($name, $album) {
                $conn = CreateConnection();
                //sql query thats exectued to get data from database
                $sql = "INSERT INTO song_album(song_id, album_id) VALUES ('$name','$album')";
                try {
                    $execute = mysqli_query($conn, $sql);
                    echo "song_album Join Successful";
                } catch (Exception $e){
                    echo "Error: {$e}";
                }
                //close connection when done
                mysqli_close($conn);
            }
        ?>
    </head>
    <body>
        <?php
            echo "Song ID: " . $_POST["songname"] . "<br/>";
            echo "Artist ID: " . $_POST["songartist"] . "<br/>";
            echo "Album ID: " . $_POST["songalbum"] . "<br/>";
        
            ArtistJoin($_POST["songname"], $_POST["songartist"]);
            AlbumJoin($_POST["songname"], $_POST["songalbum"]);
        ?>
        <br/>
        <a href="insertform.php">Go Back</a>
    </body>
</html>