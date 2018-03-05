<html>
    
<head>
    
    <!-- link to the ccs file -->
    <link rel="stylesheet" type="text/css" href="style.css">
    <!-- link to the icon library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <title>Muzika</title>
    
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
    ?>
    
    <!-- the top menu items -->
    <div class="topmenu">
        <a class="logo" href=""></a>
        <a class="" href="">Home</a>
        
        <!-- drop down menu for genre -->
        <div class="dropgenre">
            <a href="">Genre</a>
            <div class="dropgenrecontent">
                <a href="">Pop</a>
                <a href="">Metal</a>
                <a href="">Electronic</a>
            </div>
        </div>
        
        <a href="">Artists</a>
        <a href="">Library</a>
        
        <!-- the sign up button at top right, makes the sign up form display block -->
        <button class="signup" onclick="document.getElementById('idsignup').style.display='block'" style="width:auto;">Sign Up</button>
        <!-- the sign up button at top right, makes the sign up form display block -->
        <button class="login" onclick="document.getElementById('idlogin').style.display='block'" style="width:auto;">Log In</button>
        
        <!-- search bar -->
        <form action="/action_page.php">
            <button type="submit" class="searchbtn"></button>
            <div class="searchbar">
                <input type="text" autocomplete="off" id="idsearchbar" onkeyup="DisplayMatches()" placeholder="Search..." name="search">
                <ul id="idsearchlist">
                    <?php DisplaySearchListName(GetSearchListName()); ?>
                </ul>
            </div>
        </form>
    </div>
    
    <!-- script that filters the items in the list of songs when search matching each letter and hidding if nothing in the search bar -->
    <script>
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
            
            
            
            //for loop to display the list of songs
            for (i = 0; i < li.length; i++) {
                a = li[i].getElementsByTagName("a")[0];
                //if a song cotains the input character then display them
                if (a.innerHTML.toUpperCase().indexOf(filter) > -1) {
                    li[i].style.display = "block";
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
        
    </script>
    
    <!-- the sign up form that pops up when clicking the sign up button -->
    <div id="idsignup" class="signupwindow">
        <!-- little x on the top right to close the sign up form by making the display none -->
        <span onclick="document.getElementById('idsignup').style.display='none'" class="close" title="Close">x</span>
        <!-- the form which takes in a email and password, needing repeating password -->
        <form class="signup-content animate" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
            <div class="signupcontainer">
                <label><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>

                <label><b>Repeat Password</b></label>
                <input type="password" placeholder="Repeat Password" name="psw-repeat" required>
                <!-- terms and condition declaration -->
                <p>By creating an account you agree to our <a href="">Terms & Privacy</a>.</p>
                <!-- the submit and cancel buttons -->
                <div class="clearfix">
                    <button type="submit" class="signupbtn" name="signupsubmit">Sign Up</button>
                    <button type="button" onclick="document.getElementById('idsignup').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    
    <?php
        if (isset($_REQUEST['signupsubmit'])) {
            SignUp($_POST["email"],$_POST["psw"]);
        }
        elseif (isset($_REQUEST['select'])) {
            select();
        }
    
        function SignUp ($email, $password) {
            $conn = CreateConnection();
            //sql query thats exectued to get data from database
            $sql = "INSERT INTO user (user_id, user_email, user_password) VALUES ('','$email','$password')";
            try {
                $execute = mysqli_query($conn, $sql);
                //echo "Insert Successful";
            } catch (Exception $e){
                echo "Error: {$e}";
            }
        }
    ?>
    
    <!-- the sign up form that pops up when clicking the sign up button -->
    <div id="idlogin" class="loginwindow">
        <!-- little x on the top right to close the sign up form by making the display none -->
        <span onclick="document.getElementById('idlogin').style.display='none'" class="close" title="Close">x</span>
        <!-- the form which takes in a email and password, needing repeating password -->
        <form class="login-content animate" action="/action.php">
            <div class="logincontainer">
                <label><b>Email</b></label>
                <input type="text" placeholder="Enter Email" name="email" required>

                <label><b>Password</b></label>
                <input type="password" placeholder="Enter Password" name="psw" required>
                <!-- remember me checkbox -->
                <input type="checkbox" checked="checked"> Remember me
                <!-- the submit and cancel buttons -->
                <div class="clearfix">
                    <button type="submit" class="loginbtn">Log In</button>
                    <button type="button" onclick="document.getElementById('idlogin').style.display='none'" class="cancelbtn">Cancel</button>
                </div>
            </div>
        </form>
    </div>
    
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
    
    <!-- slide show showing new songs -->
    <div class="slidescontainer">
        <!-- containers for each slide with the fade class -->
        <div class="songslide fade">
            <div class="slidenumber">1 / 4</div>
            <img src="slideimages/u-kiss.jpg" style="width:100%">
            <div class="slidetext">U-KISS | Fly</div>
        </div>
        
        <div class="songslide fade">
            <div class="slidenumber">2 / 4</div>
            <img src="slideimages/incarnate.jpg" style="width:100%" style="height:280px">
            <div class="slidetext">Killswitch Engage | Falls on Me</div>
        </div>
        
        <div class="songslide fade">
            <div class="slidenumber">3 / 4</div>
            <img src="slideimages/thousandfootkrutch.jpg" style="width:100%">
            <div class="slidetext">Thousand Foot Krutch | Absolute</div>
        </div>
        
        <div class="songslide fade">
            <div class="slidenumber">4 / 4</div>
            <img src="slideimages/u-kiss2.jpg" style="width:100%">
            <div class="slidetext">U-KISS | Stalker</div>
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
    
    <div class="snackbarcontainer">
        <div id="idsqlsuccess">User Account Made.</div>
    </div>
    
    <script>
        function ShowSnackbar (id) {
            var msg = document.getElementById(id);
            msg.className = "show";
            setTimeout(function(){
                msg.className = msg.className.replace("show","");
            }, 3000);
        }
    </script>
</body>
    
</html>