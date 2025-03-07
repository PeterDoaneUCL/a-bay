
<?php
require_once ("../../private/initialise.php");

// TODO: if the bid is == the buy it now price, automatically assign winning and set listing to inactive despite timer
// Remove buy it now button and just set a note in the bid modal about buy it now

$latest_bid_amount = $_POST['latest_bid_amount'];
$listing_id = $_POST['listing_id'];
$is_active_listing = $_POST['is_active_listing'];

$query = "SELECT * FROM bid where bid_on_fk = $listing_id AND bid_amount = $latest_bid_amount";
$query_res = mysqli_query($db, $query);

if ($query_res -> num_rows > 0 && $is_active_listing == TRUE) { // If currently active
    $winning_bid = $query_res -> fetch_assoc();

    // Store bidder id
    $bidder_id = $winning_bid['bidder_fk'];

    // Find winning bid
    $winning_bid_id = $winning_bid['bid_id'];
    $bid_on_fk = $winning_bid['bid_on_fk'];

    // Get listing details, update the winning bid on the listing
    $query = "UPDATE listing SET winning_bid = $winning_bid_id WHERE listing_id=$bid_on_fk";
    $query_res = mysqli_query($db, $query);

    // Set listing to inactive
    $query = "UPDATE listing SET is_active_listing = FALSE WHERE listing_id=$bid_on_fk";
    mysqli_query($db, $query);

    // Get bidder's details
    $query = "SELECT * FROM account where user_id = $bidder_id";
    $bidder = mysqli_query($db, $query);
    $bidder = $bidder -> fetch_assoc();

    // Send email to winning bidder
    $bidder_email = $bidder['email'];
    $email = "jasontest797@gmail.com";

    $headers = 'From: ' .$email . "\r\n".
        'Reply-To: ' . $email. "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    $to =  $bidder_email = $bidder['email'];
    $subject = "You just won an auction! A-bay";
    $message = "Congratulations, you just had the highest bid on an auction. Check your a-bay account to see it.";


    // TODO: Email seller to notify that their item sold

    if (!mail($to, $subject, $message, $headers)) {
        echo "Mail returned false";
        $errorMessage = error_get_last()['message'];
    }

    echo "Set listing to inactive, updated and emailed winning bidder";
} else { // Listing already inactive
    echo "Listing already inactive";
}

