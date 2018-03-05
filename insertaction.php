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
        
            function SongInsert ($name, $price) {
                $conn = CreateConnection();
                //sql query thats exectued to get data from database
                $sql = "INSERT INTO song (song_id, song_name, song_price) VALUES ('','$name', '$price')";
                try {
                    $execute = mysqli_query($conn, $sql);
                    echo "Insert Successful";
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
            echo "Name: " . $_POST["songname"] . "<br/>";
            echo "Price: $" . $_POST["songprice"] . "<br/>";
        
            SongInsert($_POST["songname"], $_POST["songprice"]);
        ?>
        <br/>
        <a href="insertform.php">Go Back</a>
    </body>
</html>