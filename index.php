<html>
    
<head>
    
    <!-- link to the ccs file -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- link to the icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Muzika</title>
    <link rel="icon" 
      type="image/png" 
      href="icons/cd.png">
</head>

<body>
    <?php
    
        session_start();

        $displayName = "";
        if (isset($_SESSION["currentUser"])) {
            $displayName = ucfirst(strstr($_SESSION["currentUser"][1], "@", true));
        }

        if (!isset($_SESSION["cart"])){
            $_SESSION["cart"] = array();
        }
    ?>
    
    <div class="snackbarconatiner">
        <div id="success">User Account Created.</div>
        <div id="error">User Email Already Exists or Invalid Email.</div>
        <div id="loginsuccess">Welcome <?php  echo $displayName; ?></div>
        <div id="logout">Bye, have a nice day.</div>
    </div>

    <script>
        function ShowSnackbar (id) {
            var x = document.getElementById(id);
            x.className="show";
            setTimeout(function(){
                x.className = x.className.replace("show", "");
            }, 3000);
        }
        
        if (localStorage.getItem("cart") != null){
            var storedCart = localStorage.getItem("cart");
            var cart = JSON.parse(storedCart);
        } else {
            var cart = new Array();
            sessionStorage.setItem("cart", JSON.stringify(cart));
        }
        
        function AddCart (id) {
            cart.push(id);
            localStorage.setItem("cart", JSON.stringify(cart));
            document.getElementsByClassName("cart")[0].innerHTML = '('+cart.length+')';
            if (document.getElementsByClassName("cart")[0].style.display == 'none') {
                document.getElementsByClassName("cart")[0].style.display = 'block';
            }
        }
        
    </script>
    
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
    
        define("CUR_AUD", 1.00);
        define("CUR_EUR", 0.60);
        define("CUR_CNY", 5.00);
        define("CUR_USD", 0.70);
        define("CUR_GBP", 0.55);
        define("CUR_JPY", 80);
    
        define("SYM_DOLLAR", "$");
        define("SYM_EURO", "€");
        define("SYM_YUAN", "¥");
        define("SYM_POUND", "£");
    
        $arAud = array ("id" => "AUD", "sym" => SYM_DOLLAR, "cur" => CUR_AUD);
        $arEur = array ("id" => "EUR", "sym" =>SYM_EURO, "cur" => CUR_EUR);
        $arCny = array ("id" => "CNY", "sym" =>SYM_YUAN, "cur" => CUR_CNY);
        $arUsd = array ("id" => "USD", "sym" =>SYM_DOLLAR, "cur" => CUR_USD);
        $arGbp = array ("id" => "GBP", "sym" =>SYM_POUND, "cur" => CUR_GBP);
        $arJpy = array ("id" => "JPY", "sym" =>SYM_YUAN, "cur" => CUR_JPY);
        
        $arCur = array ($arAud, $arEur, $arCny, $arUsd, $arGbp, $arJpy);
        
        if (!isset($_SESSION["currentCur"])){
                $_SESSION["currentCur"] = $arAud;
            }
    
        //get the records from song table and put them into array and return the array
        function GetFeatureSongs () {
            $conn = CreateConnection();
            
            $arSongs = array();
            //sql query thats exectued to get data from database
            //$sql = "SELECT song_id, song_name, song_artist, song_image, song_price FROM song";
            $sql = "SELECT song.song_id, song_name, artist_name, album_image, song_price FROM song_artist, song, artist, song_album, album WHERE song.song_id = song_artist.song_id AND artist.artist_id = song_artist.artist_id AND song.song_id = song_album.song_id AND album.album_id = song_album.album_id";
            $result = mysqli_query($conn, $sql);
            
            //see if there are rows and populate the arrays
            if (mysqli_num_rows($result) > 0) {
                // add each row of data to a single song array
                while($row = mysqli_fetch_assoc($result)) {
                    
                    if ($_SESSION["currentCur"]["id"] == "JPY"){
                        $roundPrice = round($row["song_price"]*$_SESSION["currentCur"]["cur"]);
                    }
                    else {
                        $roundPrice = round($row["song_price"]*$_SESSION["currentCur"]["cur"],2);
                    }
                    $arSong = array("id" => $row["song_id"],
                            "name" => $row["song_name"],
                            "artist" => $row["artist_name"],
                            "image" => "albumimages/" . $row["album_image"],
                            "price" => $roundPrice);
                    
                    //then put the single song into another array
                    array_push($arSongs,$arSong);
                    $arSong=null; //empty this array
                }
            }
            else {
            //display error if no records found
            echo "Error: 0 results";
            }
            //close connection when done
            mysqli_close($conn);
            shuffle ($arSongs);
            while (sizeof($arSongs) > 12){
                array_pop($arSongs);
            }
            return $arSongs;
        }
        
        //takes in the array and echos each array element inside in a row
        function DisplaySong ($ar){
            foreach ($ar as $song){
                echo 
                    "<div class='songcontainer'>
                        <div class='songlistitem'>
                            <img class='songimage'
                            src='{$song["image"]}'><br/>
                                {$song["name"]}<br/>
                                <div class='songartist''>{$song["artist"]}</div>
                        </div>
                        <div class='middle'>
                            <button class='price' onclick='AddCart({$song["id"]})'>{$_SESSION["currentCur"]["sym"]}{$song["price"]}</button>
                        </div>
                    </div>";
            }
        }
        
        //get records from song table and out them in array for the search bar list   
        function GetSearchListName () {
            $conn = CreateConnection();
            $arSongs = array();
            //sql query thats exectued to get data from database
            //$sql = "SELECT song_id, song_name, song_artist, album_name FROM song, album WHERE song.song_id =  ORDER BY song_name ASC";
            
            $sql = "SELECT song.song_id, song_name, artist_name, album_name FROM song_artist, song, artist, song_album, album WHERE song.song_id = song_artist.song_id AND artist.artist_id = song_artist.artist_id AND song.song_id = song_album.song_id AND album.album_id = song_album.album_id";
            $result = mysqli_query($conn, $sql);
            //see if there are rows and populate the arrays
            if (mysqli_num_rows($result) > 0) {
                // add each row of data to a single song array
                while($row = mysqli_fetch_assoc($result)) {
                    $arSong = array("id" => $row["song_id"],
                                    "name" => $row["song_name"],
                                   "artist" => $row["artist_name"],
                                   "album" => $row["album_name"]);
                    //then put the single song into another array
                    array_push($arSongs,$arSong);
                    $arSong=null; //empty this array
                }
            }
            else {
            //display error if no records found
            echo "Error: 0 results";
            }
            //close connection when done
            mysqli_close($conn);
            return $arSongs;
        }
        
        //echo the list of songs for the search bar
        function DisplaySearchListName ($ar) {
            foreach ($ar as $song){
                echo 
                    "<li><a href='#'>{$song["name"]}<br/>
                                    {$song["artist"]} - {$song["album"]}</a></li>";
            }
        }
    
        function SignUp ($email, $password) {
            $conn = CreateConnection();
            //sql query thats exectued to get data from database
            $sql = "INSERT INTO user (user_id, user_email, user_password) VALUES ('','$email','$password')";
            $execute = mysqli_query($conn, $sql);
            mysqli_close($conn);
            if ($execute) {
                return true;
            } else{
                return false;
            }
        }
    
        function LogIn ($email, $password) {
            $conn = CreateConnection();
            $sql = "SELECT user_id, user_email, user_password FROM user WHERE user_email = '$email' AND user_password = '$password'";
            $execute = mysqli_query($conn, $sql);
            if ($execute) {
                $row = mysqli_fetch_assoc($execute);
                $_SESSION["currentUser"] = array ($row["user_id"], $row["user_email"], $row["user_password"]);
                return true;
                mysqli_close($conn);
            } else{
                return false;
                mysqli_close($conn);
            }
        }
    
        function LogOut () {
            $_SESSION["currentUser"] = null;
            header("Location:http://localhost/edin/musicshop/index.php?result=logout");
        }
    
        function inputTest ($input) {
            $input = trim($input);
            $input = stripslashes($input);
            $input = htmlspecialchars($input);
            return $input;
        }
    
        $email= $password= $repeat="";
        $emailEr= $passwordEr= $repeatEr="";
        $pass = true;
    
        if (isset($_REQUEST['signupsubmit'])) {
            
            $email = inputTest($_POST["email"]);
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $emailEr = "Invalid Email!";
                $pass = false;
            }
            
            $password = $_POST["psw"];
            if (preg_match('/\s/', $password)) {
                $passwordEr = "Password cannot have spaces!";
                $pass = false;
            }
            if (strlen($password)<6){
                $passwordEr = "Password must be atleast 6 characters!";
                $pass = false;
            }
            
            $repeat = $_POST["psw-repeat"];
            if ($repeat != $password){
                $repeatEr = "Passwords must match!";
                $pass = false;
            }
            
            if ($pass){
                $userCreated = SignUp($_POST["email"],$_POST["psw"]);
                 if ($userCreated) {
                    header("Location:http://localhost/edin/musicshop/index.php?result=success");
                } else {
                    header("Location:http://localhost/edin/musicshop/index.php?result=error");
                }
            }
        }
    
        elseif (isset($_REQUEST['loginsubmit'])) {
            $email = $_POST["email"];
            $password = $_POST["psw"];
            $userLogged = LogIn($email,$password);
            
            if ($userLogged) {
                header("Location:http://localhost/edin/musicshop/index.php?result=loginsuccess");
            } else {
                header("Location:http://localhost/edin/musicshop/index.php?result=loginerror");
            }
            
        }
        
        if (isset($_GET['result'])){
            $resultStr = $_GET['result'];
            if ($resultStr=='loguserout'){
                LogOut();
            }
            echo "<script type='text/javascript'> ShowSnackbar('$resultStr'); </script>";
        }
    
    ?> 
    
    
    <div class="cart" onclick="localStorage.clear()">
        <a href=""></a>
    </div>
    
    <div id="pagecontainer">
    <div class="topmenu">
        <!-- the top menu items -->
        <a class="logo" href="http://localhost/edin/musicshop"></a>
        <a id="home" class="" href="http://localhost/edin/musicshop">Home</a>
        
        <!-- drop down menu for genre -->
        <div class="dropgenre">
            <a href="">Genre</a>
            <div class="dropgenrecontent">
                <a href="">Electronic</a>
                <a href="">K-Pop</a>
                <a href="">Metal</a>
                <a href="">Rock</a>
            </div>
        </div>
        
        <a id="artists" class="" href="http://localhost/edin/musicshop?a=artists">Artists</a>
        <a class="" href="http://localhost/edin/musicshop?a=library">Library</a>
        
        <div>
            <form id="idchangecur" method="post">
            <select class="curselect" name="changecur" onchange="this.form.submit()">
                <option value="AUD">&nbsp;&nbsp;&nbsp;&nbsp;AUD</option>
                <option value="EUR">&nbsp;&nbsp;&nbsp;&nbsp;EUR</option>
                <option value="CNY">&nbsp;&nbsp;&nbsp;&nbsp;CNY</option>
                <option value="USD">&nbsp;&nbsp;&nbsp;&nbsp;USD</option>
                <option value="GBP">&nbsp;&nbsp;&nbsp;&nbsp;GBP</option>
                <option value="JPY">&nbsp;&nbsp;&nbsp;&nbsp;JPY</option>
            </select>
            </form>
        </div>
        
        <?php
            if (isset($_POST['changecur'])){
                $curStr = $_POST['changecur'];
                $i=0;
                foreach ($arCur as $cur) {
                    if ($cur["id"]==$curStr){
                        $_SESSION["currentCur"] = $cur;
                        echo "<script> document.getElementsByClassName('curselect')[0].selectedIndex = '{$i}';";
                        echo "document.getElementsByClassName('curselect')[0].style.backgroundImage = 'url(icons/{$cur["id"]}.png)'; </script>";
                        break;
                    }
                    $i++;
                }
            }
        
            if (isset($_SESSION["currentCur"])){
                $curStr = $_SESSION["currentCur"]["id"];
                $i=0;
                foreach ($arCur as $cur) {
                    if ($cur["id"]==$curStr){
                        echo "<script> document.getElementsByClassName('curselect')[0].selectedIndex = '{$i}';";
                        echo "document.getElementsByClassName('curselect')[0].style.backgroundImage = 'url(icons/{$cur["id"]}.png)'; </script>";
                        break;
                    }
                $i++;
                }
            }
        ?>
        
        <!-- the sign up button at top right, makes the sign up form display block -->
        <button class="signup" onclick="document.getElementById('idsignup').style.display='block'" style="width:auto;">Sign Up</button>
        <!-- the sign up button at top right, makes the sign up form display block -->
        <button class="login" onclick="document.getElementById('idlogin').style.display='block'" style="width:auto;">Log In</button>
        
        <button class="logoutuser" onclick="JLogOut()" style="width:auto" type="submit" name="logout">Log Out</button>
        
        <button class="loguser" onclick="" style="width:auto"><?php  echo $displayName; ?></button>
        
        <script>
            function JLogOut() {
                window.location.href="http://localhost/edin/musicshop/index.php?result=loguserout";
            }
        </script>
        
        
        <!-- search bar -->
        <form id='myform' action="/action_page.php">
            <button type="submit" class="searchbtn"></button>
            <div class="searchbar">
                <input type="text" autocomplete="off" id="idsearchbar" onkeyup="DisplayMatches()" placeholder="Search..." name="search">
                <ul id="idsearchlist">
                    <?php DisplaySearchListName(GetSearchListName()); ?>
                </ul>
            </div>
        </form>
    </div>
    
    <!-- the log in form that pops up when clicking the sign up button -->
    <div id="idlogin" class="loginwindow">
        <!-- little x on the top right to close the sign up form by making the display none -->
        <span onclick="document.getElementById('idlogin').style.display='none'" class="close" title="Close">x</span>
        <!-- the form which takes in a email and password, needing repeating password -->
        <form class="login-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="logincontainer">
                <label><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>
                <!-- remember me checkbox -->
                <input type="checkbox" checked="checked"> Remember me
                <!-- the submit and cancel buttons -->
                <div class="clearfix">
                    <button type="submit" class="loginbtn" name="loginsubmit">Log In</button>
                    <button type="button" onclick="document.getElementById('idlogin').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    
    <!-- the sign up form that pops up when clicking the sign up button -->
    <div id="idsignup" class="signupwindow">
        <!-- little x on the top right to close the sign up form by making the display none -->
        <span onclick="document.getElementById('idsignup').style.display='none'" class="close" title="Close">x</span>
        <!-- the form which takes in a email and password, needing repeating password -->
        <form class="signup-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="signupcontainer">
                <label><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>
                <span class=error> <?php echo $emailEr."<br>"; ?> </span>

                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>
                <span class=error> <?php echo $passwordEr."<br>"; ?> </span>

                <label><b>Repeat Password</b></label>
                <input type="password" placeholder="Repeat Password" name="psw-repeat" required>
                <span class=error> <?php echo $repeatEr."<br>"; ?> </span>
                
                <!-- terms and condition declaration -->
                <p>By creating an account you agree to our <a href="">Terms & Privacy</a>.</p>
                <!-- the submit and cancel buttons -->
                <div class="clearfix">
                    <button type="submit" class="signupbtn" name="signupsubmit" onclick="">Sign Up</button>
                    <button type="button" onclick="document.getElementById('idsignup').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </div>
        </form>
        <?php 
            if (!$pass){
                ?><script>document.getElementById('idsignup').style.display='block';</script><?php
            }
        ?>
    
        <!-- script to make the form close when clicking outside the form window -->
        <script>
            var signup = document.getElementById('idsignup');
            var login = document.getElementById('idlogin');

            window.onclick = function(event) {
                if (event.target == signup || event.target == login) {
                    signup.style.display = "none";
                    login.style.display = "none";
                }
            }
        </script>
    </div>
    
    <div class="contentcontainer">
        <div id="homepage">

        <!-- slide show showing new songs -->
        <div class="slidescontainer">
            <!-- containers for each slide with the fade class -->
            <div class="songslide fade">
                <div class="slidenumber">New Arrival</div>
                <img src="slideimages/u-kiss.jpg" style="width:100%">
                <div class="slidetext">U-KISS | Fly</div>
            </div>

            <div class="songslide fade">
                <div class="slidenumber">New Arrival</div>
                <img src="slideimages/incarnate.jpg" style="width:100%" style="height:280px">
                <div class="slidetext">Killswitch Engage | Falls on Me</div>
            </div>

            <div class="songslide fade">
                <div class="slidenumber">New Arrival</div>
                <img src="slideimages/thousandfootkrutch.jpg" style="width:100%">
                <div class="slidetext">Thousand Foot Krutch | Absolute</div>
            </div>

            <div class="songslide fade">
                <div class="slidenumber">New Arrival</div>
                <img src="slideimages/siyoon.jpg" style="width:100%">
                <div class="slidetext">Siyoon | Nothing To You</div>
            </div>

            <!-- arrows to change slide -->
            <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
            <a class="next" onclick="plusSlides(1)">&#10095;</a>

        </div>

        <!-- indicator dots for the slides -->
        <div class="dotcontainer" style="text-align:center">
            <span class="dot" onclick="currentSlide(1)"></span>
            <span class="dot" onclick="currentSlide(2)"></span>
            <span class="dot" onclick="currentSlide(3)"></span>
            <span class="dot" onclick="currentSlide(4)"></span>
        </div>

        <script>
            var slideIndex = 0;
            var timeOut;
            var timeOutTime = 4000;
            var slides = document.getElementsByClassName("songslide");
            var dots = document.getElementsByClassName("dot");


            showSlides(slideIndex);

            function plusSlides(n) {
                showSlides(slideIndex += n);
                if (timeOut != null){
                    clearTimeout(timeOut);
                    timeOut = setTimeout(carousel, timeOutTime);
                }
            }

            function currentSlide(n) {
                showSlides(slideIndex = n);
                if (timeOut != null){
                    clearTimeout(timeOut);
                    timeOut = setTimeout(carousel, timeOutTime);
                }
            }


            function showSlides(n) {
                var i;
                if (n > slides.length) {
                    slideIndex = 1;
                }
                if (n < 1) {
                    slideIndex = slides.length;
                }
                for (i=0; i < slides.length; i++) {
                    slides[i].style.display = "none";
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }

                slides[slideIndex-1].style.display = "block";
                dots[slideIndex-1].className += " active";
            }

            carousel();

            function carousel() {
                var i;
                for (i = 0; i < slides.length; i++) {
                  slides[i].style.display = "none"; 
                }
                slideIndex++;

                if (slideIndex > slides.length) {
                    slideIndex = 1
                }
                for (i = 0; i < dots.length; i++) {
                    dots[i].className = dots[i].className.replace(" active", "");
                }

                slides[slideIndex-1].style.display = "block";
                dots[slideIndex-1].className += " active";
                timeOut = setTimeout(carousel, timeOutTime); // Change image every 4 seconds
            }
        </script>

        <!-- the featured song list which runs the function to get the song data and echo them in php -->
        <div class="featured">
            <h3>Featured Songs</h3>
            <a class="scrlright" onclick="scrollFunction(44)">&#10095;</a>
            <a class="scrlleft" onclick="scrollFunction(-44)">&#10094;</a>
            <div class="inside">
                <div class=songlist>
                    <?php DisplaySong(GetFeatureSongs()); ?>
                </div>
            </div>
        </div>

        <script>
            var listItem = document.getElementsByClassName("songcontainer");
            var listBlock = document.getElementsByClassName("songlist")[0];
            var listWidth = 145;

            var w_width=(listItem.length * listWidth).toString();
            listBlock.style.width = w_width+"px";

            var listScroll = document.getElementsByClassName("inside")[0];

            var id;
            function scrollFunction (n) {
                clearInterval(id);

                id = setInterval(frame, 10);
                var scrollWidth = 0;

                function frame() {
                    if (scrollWidth >= 920 || scrollWidth <= -920) {
                        clearInterval(id);
                    } else {
                        scrollWidth += n;
                        listScroll.scrollLeft += n;
                        n *= 0.951;
                    }
                }
            }

        </script>
        </div>

        
        <div id="artistspage">
            <div class="fartists">
                <div class="discoverartists">
                    <div class="discovertitle">Discover</div>
                    
                    <div class="artistbig" style="background-image: url(artistimages/u-kiss.jpg)">
                        <div class="artistbigtext">U-KISS</div>
                    </div>
                    
                    <div class="artistmed" style="background-image: url(artistimages/siyoon.jpg)">
                        <div class="artistmedtext">Siyoon</div>
                    </div>
                    
                    <div class="artistmed" style="background-image: url(artistimages/exid.jpg)">
                        <div class="artistmedtext">EXID</div>
                    </div>
                    
                    <div class="artistsmall" style="background-image: url(artistimages/killswitchengage.jpg)">
                        <div class="artistsmalltext">Killswitch Engage</div>
                    </div>
                    
                    <div class="artistsmall" style="background-image: url(artistimages/thousandfootkrutch.jpg)">
                        <div class="artistsmalltext">Thousand Foot Krutch</div>
                    </div>
                    
                    <div class="artistsmall" style="background-image: url(artistimages/gfriend.jpg)">
                        <div class="artistsmalltext">GFRIEND</div>
                    </div>
                </div>
            </div>
            
            <div class="bartists">
                <div class="browsetitle">Browse</div>
                <div class="browseartists">
                    <ul class="browsealpha">
                        <li>A</li>
                        <li>B</li>
                        <li>C</li>
                        <li>D</li>
                        <li>E</li>
                        <li>F</li>
                        <li>G</li>
                        <li>H</li>
                        <li>I</li>
                        <li>J</li>
                        <li>K</li>
                        <li>L</li>
                        <li>M</li>
                        <li>N</li>
                        <li>O</li>
                        <li>P</li>
                        <li>Q</li>
                        <li>R</li>
                        <li>S</li>
                        <li>T</li>
                        <li>U</li>
                        <li>V</li>
                        <li>W</li>
                        <li>X</li>
                        <li>Y</li>
                        <li>Z</li>
                    </ul>
                </div>
                <div class="browseresults">
                    
                </div>
            </div>
        </div>
    </div>
    
    <!-- the footer menu at bottom of page -->
    <div class="footermenu">
        <div class="footercontainer">
            <ul class="linklist">
                 <li><a href="">Privacy Policy</a></li>
                 <li><a href="">Terms of Use</a></li>
                 <li><a href="">Contact</a></li>
            </ul>
            <!-- Add font awesome icons -->
            <div class="socialmedia">
                <a href="#" class="fa fa-instagram"></a>
                <a href="#" class="fa fa-google"></a>
                <a href="#" class="fa fa-twitter"></a>
                <a href="#" class="fa fa-facebook"></a>
            </div>
        </div>
    </div>
    </div>
    <!-- script that filters the items in the list of songs when search matching each letter and hidding if nothing in the search bar -->
    <script>
        
        if (cart.length <= 0) {
            document.getElementsByClassName("cart")[0].style.display = 'none';
        }
        
        document.getElementsByClassName("cart")[0].innerHTML = '('+cart.length+')';
        //declaring the variables for the lists
        var input, filter, ul, li, a, i;
        input = document.getElementById("idsearchbar");
        filter = input.value.toUpperCase();
        ul = document.getElementById("idsearchlist");
        li = ul.getElementsByTagName("li");
        
        for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                li[i].style.display = "none";
        }
        
        function DisplayMatches () {
            //declaring the variables for the lists
            var input, filter, ul, li, a, i;
            input = document.getElementById("idsearchbar");
            filter = input.value.toUpperCase();
            ul = document.getElementById("idsearchlist");
            li = ul.getElementsByTagName("li");
            var count = 0;
            //for loop to display the list of songs
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                //if a song cotains the input character then display them
                if (a.innerHTML.toUpperCase().indexOf(filter) > -1 && count<10) {
                    li[i].style.display = "block";
                    count++;
                }
                //if not matching then hide them
                else {
                    li[i].style.display = "none";
                }
                //if nothing in search bar hide everything
                if (input.value == "") {
                    li[i].style.display = "none";
                }
            }
        }
        
        function ChangePage (page) {
            if (page=='artists') {
                document.getElementById('homepage').style.display = 'none';
                document.getElementById('home').className.replace("active", "");
                
                document.getElementById('artistspage').style.display = 'block';
                document.getElementById('artists').className = 'active';
            
            } else {
                document.getElementById('artistspage').style.display = 'none';
                document.getElementById('artists').className.replace("active", "");
                
                document.getElementById('home').className='active';
            }
        }

    </script>
    
    
    <?php
        
        if (isset($_GET['a'])){
            $pageStr = $_GET['a'];
            echo "<script> ChangePage('$pageStr'); </script>";
        } else {
            echo "<script> ChangePage('none'); </script>";
        }
    
        if (isset($_SESSION["currentUser"])) {
            ?>
            <script>
                document.getElementsByClassName('signup')[0].style.display = 'none';
                document.getElementsByClassName('login')[0].style.display = 'none';
                document.getElementsByClassName('loguser')[0].style.display = 'block';
                document.getElementsByClassName('logoutuser')[0].style.display = 'block';
            </script>
            <?php
        } else {
            ?>
            <script>
                document.getElementsByClassName('signup')[0].style.display = 'block';
                document.getElementsByClassName('login')[0].style.display = 'block';
                document.getElementsByClassName('loguser')[0].style.display = 'none';
                document.getElementsByClassName('logoutuser')[0].style.display = 'none';
            </script>
            <?php
        }
    ?>
</body>
    
</html>