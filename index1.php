
<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "movie_booking3";
$conn = mysqli_connect($servername, $username, $password);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if (!mysqli_query($conn, $sql)) {
    die("Error creating database: " . mysqli_error($conn));
}


mysqli_select_db($conn, $dbname);


$sql = "CREATE TABLE if not exists bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
         movie VARCHAR(255) NOT NULL,
         showtime VARCHAR(50) NOT NULL,
         tickets INT NOT NULL,
         name VARCHAR(100) NOT NULL,
         email VARCHAR(100) NOT NULL
     )";
if (!mysqli_query($conn, $sql)) {
    die("Error creating table: " . mysqli_error($conn));
}

    mysqli_close($conn);


// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Validate and sanitize inputs
    $movie = mysqli_real_escape_string($conn, $_POST['movie']);
    $showtime = mysqli_real_escape_string($conn, $_POST['showtime']);
    $tickets = intval($_POST['tickets']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if ($email === false) {
        $error = "Invalid email format!";
    } elseif ($tickets <= 0 || $tickets > 10) {
        $error = "Invalid number of tickets. Must be between 1 and 10.";
    } else {
        // Insert data into database
        $sql = "INSERT INTO bookings (movie, showtime, tickets, name, email)
                VALUES ('$movie', '$showtime', $tickets, '$name', '$email')";

        if (mysqli_query($conn, $sql)) {
            $success = "Booking successful!";
        } else {
            $error = "Booking Error: " . mysqli_error($conn);
        }
    }

    // Close connection
    mysqli_close($conn);
}

// Fetch existing bookings
$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn) {
    $booking_query = "SELECT * FROM bookings ORDER BY id DESC LIMIT 10";
    $booking_result = mysqli_query($conn, $booking_query);
    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jaypee Cinema - Movie Booking</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
            url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ9wc1vPDWvBhZEdnFluTxulLfNBtq37a--8Q&s');
            background-size: cover;
            background-position: center;
            color: white;
            line-height: 1.6;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .movie-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .movie-card {
            background: rgba(255,255,255,0.1);
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            backdrop-filter: blur(10px);
            transition: transform 0.3s;
        }
        .movie-card:hover {
            transform: scale(1.05);
        }
        .book-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            margin-top: 15px;
            cursor: pointer;
            border-radius: 5px;
        }
        .booking-form {
            display: none;
            background: rgba(255,255,255,0.2);
            padding: 30px;
            border-radius: 10px;
            max-width: 500px;
            margin: 20px auto;
            backdrop-filter: blur(10px);
        }
        .booking-form input, 
        .booking-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .recent-bookings {
            background: rgba(255,255,255,0.1);
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            backdrop-filter: blur(10px);
        }
        .message {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .success {
            background-color: rgba(0,255,0,0.2);
            color: #4CAF50;
        }
        .error {
            background-color: rgba(255,0,0,0.2);
            color: #f44336;
        }
        .movie-details {
            background: rgba(255,255,255,0.2);
            padding: 15px;
            margin-top: 10px;
            border-radius: 5px;
            display: none;
        }
.footer {
    background: rgba(255,255,255,0.1);
    margin-top: 40px;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    backdrop-filter: blur(10px);
}

.footer h2, .footer h3 {
    color: #4CAF50;
    margin-bottom: 10px;
}

.footer p, .footer ul {
    color: white;
    font-size: 1.1em;
    line-height: 1.5;
}

.footer ul {
    list-style: none;
    padding: 0;
}

.footer ul li {
    margin: 5px 0;
}

    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Jaypee Cinema</h1>
            <?php 
            if(isset($success)) {
                echo "<div class='message success'>$success</div>";
            }
            if(isset($error)) {
                echo "<div class='message error'>$error</div>";
            }
            ?>
        </div>

        <div class="movie-grid">
            <div class="movie-card">
                <h2>Avengers: Endgame</h2>
                <p>Action | 3 hours</p>
                <button class="book-btn" onclick="showBookingForm('avengers')">Book Now</button>
                <button class="book-btn" onclick="toggleMovieDetails('avengers')">Movie Details</button>
                <div id="avengersDetails" class="movie-details">
                    Epic conclusion to the Avengers saga. The remaining Avengers assemble once more to reverse Thanos' actions and restore the universe.
                </div>
            </div>

            <div class="movie-card">
                <h2>Spider-Man: No Way Home</h2>
                <p>Action | 2.5 hours</p>
                <button class="book-btn" onclick="showBookingForm('spiderman')">Book Now</button>
                <button class="book-btn" onclick="toggleMovieDetails('spiderman')">Movie Details</button>
                <div id="spidermanDetails" class="movie-details">
                    Peter Parker seeks help from Doctor Strange to make his identity a secret again, leading to multiverse chaos.
                </div>
            </div>

            <div class="movie-card">
                <h2>Inception</h2>
                <p>Sci-Fi | 2.5 hours</p>
                <button class="book-btn" onclick="showBookingForm('inception')">Book Now</button>
                <button class="book-btn" onclick="toggleMovieDetails('inception')">Movie Details</button>
                <div id="inceptionDetails" class="movie-details">
                    A skilled thief who commits corporate espionage by infiltrating the subconscious of his targets while they are dreaming.
                </div>
            </div>

            <div class="movie-card">
                <h2>The Dark Knight</h2>
                <p>Action | 2.5 hours</p>
                <button class="book-btn" onclick="showBookingForm('darkknight')">Book Now</button>
                <button class="book-btn" onclick="toggleMovieDetails('darkknight')">Movie Details</button>
                <div id="darkknightDetails" class="movie-details">
                    Batman faces his greatest challenge yet as the Joker wreaks havoc on Gotham City, testing the limits of his moral code.
                </div>
            </div>
        </div>

        <!-- Add Booking Button -->
        <div style="text-align: center; margin: 20px;">
            <button onclick="toggleRecentBookings()" class="book-btn">Show Recent Bookings</button>
        </div>

        <!-- Booking Forms -->
        <div id="avengersBooking" class="booking-form">
            <h3>Book Avengers: Endgame</h3>
            <form method="post" onsubmit="return validateForm(this)">
                <input type="hidden" name="movie" value="Avengers: Endgame">
                <select name="showtime" required>
                    <option>10:00 AM</option>
                    <option>2:00 PM</option>
                    <option>6:00 PM</option>
                </select>
                <input type="number" name="tickets" placeholder="Number of Tickets" min="1" max="10" required>
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <button type="submit" class="book-btn">Confirm Booking</button>
                <button type="button" onclick="closeBookingForm('avengers')">Close</button>
            </form>
        </div>

        <div id="spidermanBooking" class="booking-form">
            <h3>Book Spider-Man: No Way Home</h3>
            <form method="post" onsubmit="return validateForm(this)">
                <input type="hidden" name="movie" value="Spider-Man: No Way Home">
                <select name="showtime" required>
                    <option>11:30 AM</option>
                    <option>3:30 PM</option>
                    <option>7:30 PM</option>
                </select>
                <input type="number" name="tickets" placeholder="Number of Tickets" min="1" max="10" required>
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <button type="submit" class="book-btn">Confirm Booking</button>
                <button type="button" onclick="closeBookingForm('spiderman')">Close</button>
            </form>
        </div>

        <div id="inceptionBooking" class="booking-form">
            <h3>Book Inception</h3>
            <form method="post" onsubmit="return validateForm(this)">
                <input type="hidden" name="movie" value="Inception">
                <select name="showtime" required>
                    <option>12:00 PM</option>
                    <option>4:00 PM</option>
                    <option>8:00 PM</option>
                </select>
                <input type="number" name="tickets" placeholder="Number of Tickets" min="1" max="10" required>
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <button type="submit" class="book-btn">Confirm Booking</button>
                <button type="button" onclick="closeBookingForm('inception')">Close</button>
            </form>
        </div>

        <div id="darkknightBooking" class="booking-form">
            <h3>Book The Dark Knight</h3>
            <form method="post" onsubmit="return validateForm(this)">
                <input type="hidden" name="movie" value="The Dark Knight">
                <select name="showtime" required>
                    <option>1:00 PM</option>
                    <option>5:00 PM</option>
                    <option>9:00 PM</option>
                </select>
                <input type="number" name="tickets" placeholder="Number of Tickets" min="1" max="10" required>
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <button type="submit" class="book-btn">Confirm Booking</button>
                <button type="button" onclick="closeBookingForm('darkknight')">Close</button>
            </form>
        </div>
<div style="text-align: center; margin: 20px;">
<button onclick="window.location.href='images.html'" class="book-btn">View Images</button>
<button onclick="window.location.href='upcoming-movies.html'" class="book-btn">Upcoming Movies</button>
<button onclick="window.location.href='trailers.html'" class="book-btn">trailers</button>
</div>
</div>



<!-- Replace the existing Recent Bookings div with this code -->
<div id="recentBookings" class="recent-bookings" style="display: none;">
    <h3>Recent Bookings</h3>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: rgba(255,255,255,0.2);">
                <th style="padding: 10px; text-align: left;">Movie</th>
                <th style="padding: 10px; text-align: left;">Showtime</th>
                <th style="padding: 10px; text-align: left;">Tickets</th>
                <th style="padding: 10px; text-align: left;">Name</th>
                <th style="padding: 10px; text-align: left;">Email</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($booking_result->num_rows > 0) {
                while($row = $booking_result->fetch_assoc()) {
                    echo "<tr style='border-bottom: 1px solid rgba(255,255,255,0.1);'>";
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($row['movie']) . "</td>";
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($row['showtime']) . "</td>";
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($row['tickets']) . "</td>";
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($row['name']) . "</td>";
                    echo "<td style='padding: 10px;'>" . htmlspecialchars($row['email']) . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align: center; padding: 10px;'>No recent bookings found.</td></tr>";
            } ?>
        </tbody>
    </table>
</div>

    <script>
        function showBookingForm(movie) {
            document.getElementById(movie + 'Booking').style.display = 'block';
        }

        function closeBookingForm(movie) {
            document.getElementById(movie + 'Booking').style.display = 'none';
        }

        function toggleRecentBookings() {
            var x = document.getElementById('recentBookings');
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        }

        function toggleMovieDetails(movie) {
            var detailsDiv = document.getElementById(movie + 'Details');
            if (detailsDiv.style.display === "block") {
                detailsDiv.style.display = "none";
            } else {
                detailsDiv.style.display = "block";
            }
        }

        function validateForm(form) {
            var tickets = form.tickets.value;
            if (tickets <= 0 || tickets > 10) {
                alert("Please select a valid number of tickets (1-10).");
                return false;
            }
            return true;
        }
    </script><div class="container">
    <!-- Existing content -->

    <!-- Add a new section for Jaypee Cinema details -->
    <div class="footer">
        <h2>About Jaypee Cinema</h2>
        <p>Welcome to Jaypee Cinema! This project is brought to you by our dedicated team under the guidance of our mentor.</p>
        
        <h3>Mentor</h3>
        <p>Anubhuti Roda Mohindra</p>
        
        <h3>Our Team</h3>
        <ul>
            <li>Divyans Pratap singh  </li>
            <li>Avi srivastav</li>
            <li>Aniket kumar rai </li>
            
        </ul>
    </div>
</div>

</body>
</html>