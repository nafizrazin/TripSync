<?php
$con = mysqli_connect('localhost','root','','transport');
session_start();

// --- ADMIN LOGIN ---
if (isset($_POST['alogin'])) {
  $name = mysqli_real_escape_string($con, $_POST['uname']);
  $psw = mysqli_real_escape_string($con, $_POST['psw']);

  $sql = "SELECT * FROM admin WHERE name='$name' AND psw='$psw'";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) == 1) {
    $_SESSION['aname'] = $name;
    // FIX: Removed "../" because admin.php is in the same folder as action.php
    header('location:admin.php');
    exit();
  }
}

// --- USER SIGNUP ---
if (isset($_POST['signup'])) {
  $name = mysqli_real_escape_string($con, $_POST['name']);
  $uname = mysqli_real_escape_string($con, $_POST['uname']);
  $age = mysqli_real_escape_string($con, $_POST['age']);
  $aidno = mysqli_real_escape_string($con, $_POST['aidno']);
  $psw = mysqli_real_escape_string($con, $_POST['psw']);
  $email = mysqli_real_escape_string($con, $_POST['email']);

  $sql = "SELECT * FROM user_info WHERE uname='$uname'";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) > 0) {
    echo "<div class='alert alert-warning'>Username already exists. Please choose another.</div>";
  } else {
    $sql = "INSERT INTO `user_info` (`uid`, `name`, `uname`, `age`, `nid_no`, `psw`, `email`)
     VALUES (NULL, '$name', '$uname', '$age', '$aidno', '$psw', '$email')";
    $run = mysqli_query($con, $sql);
    if($run){
      $_SESSION['uid'] = mysqli_insert_id($con);
      $_SESSION['uname'] = $uname;
      header('location:login.php');
    }
  }
}

// --- USER LOGIN ---
if (isset($_POST['login'])) {
   $name = mysqli_real_escape_string($con, $_POST['uname']);
   $psw = mysqli_real_escape_string($con, $_POST['psw']);

   $sql = "SELECT * FROM user_info WHERE uname='$name' AND psw='$psw'";
   $run = mysqli_query($con, $sql);

   if (mysqli_num_rows($run) == 1) {
    $row = mysqli_fetch_array($run);
    $_SESSION['uid'] = $row['uid'];
    $_SESSION['uname'] = $name;

    // FIXED: Added "../" to go back to the main folder
    header('location:../profile.php');
    exit(); // Always good practice to exit after a header redirect
  }
}

// --- ADD BUS (ADMIN) ---
if (isset($_POST['bus'])) {
  $bname = mysqli_real_escape_string($con, $_POST['bname']);
  $bno = mysqli_real_escape_string($con, $_POST['bno']);
  $from = mysqli_real_escape_string($con, $_POST['from']);
  $to = mysqli_real_escape_string($con, $_POST['to']);
  $time = mysqli_real_escape_string($con, $_POST['time']);
  $seat = mysqli_real_escape_string($con, $_POST['sno']);
  $type = mysqli_real_escape_string($con, $_POST['rad']);
  $fare = mysqli_real_escape_string($con, $_POST['fare']);

  $sql = "SELECT * FROM bus_details WHERE bno='$bno' AND bfrom='$from'";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) > 0) {
    echo "<script>alert('Bus details already exist!'); window.location.href='admin.php';</script>";
  } else {
    $sql = "INSERT INTO `bus_details` (`bus_id`, `bname`, `bno`, `bfrom`, `bto`, `time`, `type`, `no_seat` ,`fare`)
    VALUES (NULL, '$bname', '$bno', '$from', '$to','$time', '$type', '$seat' ,'$fare')";
    $run = mysqli_query($con, $sql);
    if ($run) {
      header('location:admin.php');
    }
  }
}

// --- SEARCH BUS ---
if (isset($_POST['search'])) {
  $from = mysqli_real_escape_string($con, $_POST['from']);
  $to = mysqli_real_escape_string($con, $_POST['to']);
  $date = mysqli_real_escape_string($con, $_POST['date']);

  // Logic 1: Check Booking Details (Specific Date)
  $sql = "SELECT * FROM booking_det WHERE jdate='$date' AND bfrom='$from' AND bto='$to' AND vacant>0";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) > 0) {
    while ($row = mysqli_fetch_array($run)) {
      $bus_id = $row['bus_id'];
      $vacant = $row['vacant'];

      $sql1 = "SELECT * FROM bus_details WHERE bus_id='$bus_id'";
      $run1 = mysqli_query($con, $sql1);
      $rows = mysqli_fetch_array($run1);

      $bname = $rows['bname'];
      $bno = $rows['bno'];
      $time = $rows['time'];
      $type = $rows['type'];
      $fare = $rows['fare'];

      echo "
      <div class='card mb-3 shadow-sm border-0'>
        <div class='card-header bg-primary text-white d-flex justify-content-between align-items-center'>
            <h5 class='mb-0'><i class='fa fa-bus me-2'></i> $bname</h5>
            <span class='badge bg-light text-primary'>$type</span>
        </div>
        <div class='card-body'>
            <div class='row g-3'>
                <div class='col-md-3'>
                    <small class='text-muted'>Bus Number</small>
                    <div class='fw-bold'>$bno</div>
                </div>
                <div class='col-md-3'>
                    <small class='text-muted'>Route</small>
                    <div class='fw-bold'>$from <i class='fa fa-arrow-right mx-1 text-primary'></i> $to</div>
                </div>
                <div class='col-md-2'>
                    <small class='text-muted'>Time</small>
                    <div class='fw-bold'>$time</div>
                </div>
                <div class='col-md-2'>
                    <small class='text-muted'>Fare</small>
                    <div class='fw-bold text-success'>$$fare</div>
                </div>
                <div class='col-md-2'>
                     <small class='text-muted'>Seats</small>
                     <div class='fw-bold'>$vacant Available</div>
                </div>
            </div>
        </div>
        <div class='card-footer bg-white border-top-0 text-end'>
            <button class='btn btn-primary btn-sm px-4 book' bid='$bus_id' jid='$date' seat='$vacant'>
                Book Now
            </button>
        </div>
      </div>";
    }
  } else {
    // Logic 2: Fallback to Bus Details (General Route)
    $sql = "SELECT * FROM bus_details WHERE bfrom='$from' AND bto='$to'";
    $run = mysqli_query($con, $sql);

    if (mysqli_num_rows($run) > 0) {
      while ($row = mysqli_fetch_array($run)) {
        $bus_id = $row['bus_id'];
        $bname = $row['bname'];
        $bno = $row['bno'];
        $time = $row['time'];
        $type = $row['type'];
        $fare = $row['fare'];
        $seat = $row['no_seat'];

        echo "
        <div class='card mb-3 shadow-sm border-0'>
            <div class='card-header bg-primary text-white d-flex justify-content-between align-items-center'>
                <h5 class='mb-0'><i class='fa fa-bus me-2'></i> $bname</h5>
                <span class='badge bg-light text-primary'>$type</span>
            </div>
            <div class='card-body'>
                <div class='row g-3'>
                    <div class='col-md-3'>
                        <small class='text-muted'>Bus Number</small>
                        <div class='fw-bold'>$bno</div>
                    </div>
                    <div class='col-md-3'>
                        <small class='text-muted'>Route</small>
                        <div class='fw-bold'>$from <i class='fa fa-arrow-right mx-1 text-primary'></i> $to</div>
                    </div>
                    <div class='col-md-2'>
                        <small class='text-muted'>Time</small>
                        <div class='fw-bold'>$time</div>
                    </div>
                    <div class='col-md-2'>
                        <small class='text-muted'>Fare</small>
                        <div class='fw-bold text-success'>$$fare</div>
                    </div>
                    <div class='col-md-2'>
                        <small class='text-muted'>Total Seats</small>
                        <div class='fw-bold'>$seat</div>
                    </div>
                </div>
            </div>
            <div class='card-footer bg-white border-top-0 text-end'>
                <button class='btn btn-primary btn-sm px-4 book' bid='$bus_id' jid='$date' seat='$seat'>
                    Book Now
                </button>
            </div>
        </div>";
      }
    } else {
      echo "<div class='alert alert-danger text-center mt-4'>Oops! No buses found for this route.</div>";
    }
  }
}

// --- SHOW BOOKING FORM ---
if (isset($_POST['book'])) {
  $jid = $_POST['jid'];
  $bid = $_POST['bid'];
  $seat = $_POST['seat'];

  echo "
  <div class='card shadow-sm border-0 mt-4'>
      <div class='card-header bg-success text-white'>
          <h5 class='mb-0'>Complete Booking</h5>
      </div>
      <div class='card-body'>
          <form id='signup-form' class='signup-form'>
              <div class='mb-3'>
                  <label class='form-label'>Passenger Name</label>
                  <input type='text' class='form-control' id='name' placeholder='Enter Full Name' required />
              </div>
              <div class='mb-3'>
                  <label class='form-label'>Number of Passengers</label>
                  <input type='number' class='form-control' id='no' placeholder='Ex: 1' min='1' max='$seat' required />
                  <small class='text-muted'>Max available: $seat</small>
              </div>
              <div class='d-grid'>
                <a href='#s' class='btn btn-success booking' bid='$bid' jid='$jid' seat='$seat'>Confirm Booking</a>
              </div>
          </form>
      </div>
  </div>";
}

// --- PROCESS BOOKING ---
if (isset($_POST['booking'])) {
  $jid = mysqli_real_escape_string($con, $_POST['jid']);
  $bid = mysqli_real_escape_string($con, $_POST['bid']);
  $seat = (int)$_POST['seat'];
  $name = mysqli_real_escape_string($con, $_POST['name']);
  $no_p = (int)$_POST['no_p'];
  $uid = $_SESSION['uid'];
  $date = date("Y-m-d");

  $vacant = $seat - $no_p;

  // Check if booking details exist for this date
  $sql = "SELECT * FROM booking_det WHERE bus_id='$bid' AND jdate='$jid'";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) > 0) {
    // Details exist, update vacancy
    $sql = "UPDATE booking_det SET vacant='$vacant' WHERE bus_id='$bid' AND jdate='$jid'";
    $run = mysqli_query($con, $sql);
    if ($run) {
        $sql = "SELECT * FROM bus_details WHERE bus_id='$bid'";
        $run = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($run);

        $tseat = $row['no_seat'];
        $seatno = $tseat - $seat; // Current booked count
        $tseatno = "";

        for ($i=0; $i < $no_p; $i++) {
            ++$seatno;
            $tseatno = $tseatno." ".$seatno;
        }

        $sql = "SELECT * FROM ticket WHERE uid='$uid' AND pname='$name' AND jdate='$jid' AND bus_id='$bid'";
        $run = mysqli_query($con, $sql);

        if (mysqli_num_rows($run) > 0) {
           echo "<div class='alert alert-warning'>You have already booked this ticket! Please check 'Your Tickets'.</div>";
        } else {
           $sql = "INSERT INTO `ticket` (`tid`, `bus_id`, `uid`, `seat_no`, `no_seat`, `ticket_status`, `jdate`, `booking_date`, `pname`) VALUES (NULL, '$bid', '$uid', '$tseatno', '$no_p', 'Confirmed', '$jid', '$date', '$name')";
           $run = mysqli_query($con, $sql);
           if ($run) {
             echo "<div class='alert alert-success'>Booking Confirmed! Thank you.</div>";
           }
        }
    }
  } else {
    // First booking for this date, insert new booking details
    $sql = "SELECT * FROM bus_details WHERE bus_id='$bid'";
    $run = mysqli_query($con, $sql);
    $row = mysqli_fetch_array($run);

    $tseat = $row['no_seat'];
    $from = $row['bfrom'];
    $to = $row['bto'];

    $sql = "INSERT INTO `booking_det` (`bus_id`, `vacant`, `jdate`, `bfrom`, `bto`) VALUES ('$bid', '$vacant', '$jid', '$from', '$to')";
    $run = mysqli_query($con, $sql);

    if ($run) {
        $seatno = $tseat - $seat;
        $tseatno = "";
        for ($i=0; $i < $no_p; $i++) {
            ++$seatno;
            $tseatno = $tseatno." ".$seatno;
        }

        $sql = "SELECT * FROM ticket WHERE uid='$uid' AND pname='$name' AND jdate='$jid' AND bus_id='$bid'";
        $run = mysqli_query($con, $sql);

        if (mysqli_num_rows($run) > 0) {
           echo "<div class='alert alert-warning'>You have already booked this ticket!</div>";
        } else {
           $sql = "INSERT INTO `ticket` (`tid`, `bus_id`, `uid`, `seat_no`, `no_seat`, `ticket_status`, `jdate`, `booking_date`, `pname`) VALUES (NULL, '$bid', '$uid', '$tseatno', '$no_p', 'Confirmed', '$jid', '$date', '$name')";
           $run = mysqli_query($con, $sql);
           if ($run) {
              echo "<div class='alert alert-success'>Booking Confirmed! Thank you.</div>";
           }
        }
    }
  }
}

// --- VIEW TICKET (USER) ---
if (isset($_POST['ticket'])) {
  $uid = $_SESSION['uid'];
  $sql = "SELECT * FROM ticket WHERE uid='$uid'";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) == 0) {
    echo "<div class='alert alert-info'>No bookings found. Go book a ticket!</div>";
  } else {
    while ($row = mysqli_fetch_array($run)) {
       $bid = $row['bus_id'];
       $uid = $row['uid'];
       $seat_no = $row['seat_no'];
       $no_seat = $row['no_seat'];
       $ticket_status = $row['ticket_status'];
       $jdate = $row['jdate'];
       $booking_date = $row['booking_date'];
       $pname = $row['pname'];

       $sql1 = "SELECT * FROM user_info WHERE uid='$uid'";
       $run1 = mysqli_query($con, $sql1);
       $rows = mysqli_fetch_array($run1);
       $age = $rows['age'];
       $nid_no = $rows['nid_no'];
       $email = $rows['email'];

       $sql2 = "SELECT * FROM bus_details WHERE bus_id='$bid'";
       $run2 = mysqli_query($con, $sql2);
       $row1 = mysqli_fetch_array($run2);
       $bname = $row1['bname'];
       $bno = $row1['bno'];
       $bfrom = $row1['bfrom'];
       $bto = $row1['bto'];
       $time = $row1['time'];
       $fare = $row1['fare'];
       $Tfare = $fare * $no_seat;

       echo "
       <div class='card mb-4 shadow border-0'>
         <div class='card-header bg-primary text-white text-center'>
            <h4 class='mb-0'>E-TICKET</h4>
         </div>
         <div class='card-body bg-white text-dark'>

            <div class='row mb-3'>
                <div class='col-12 border-bottom pb-2 mb-2'>
                    <h5 class='text-primary'><i class='fa fa-user me-2'></i>Passenger Details</h5>
                </div>
                <div class='col-md-3'><strong>Name:</strong><br>$pname</div>
                <div class='col-md-3'><strong>NID:</strong><br>$nid_no</div>
                <div class='col-md-3'><strong>Age:</strong><br>$age</div>
                <div class='col-md-3'><strong>Email:</strong><br>$email</div>
            </div>

            <div class='row mb-3'>
                <div class='col-12 border-bottom pb-2 mb-2'>
                    <h5 class='text-primary'><i class='fa fa-bus me-2'></i>Trip Details</h5>
                </div>
                <div class='col-md-3'><strong>Bus Name:</strong><br>$bname</div>
                <div class='col-md-3'><strong>Bus No:</strong><br>$bno</div>
                <div class='col-md-3'><strong>Route:</strong><br>$bfrom to $bto</div>
                <div class='col-md-3'><strong>Departure:</strong><br>$time</div>
            </div>

            <div class='row bg-light p-3 rounded'>
                <div class='col-md-4'>
                    <small class='text-muted'>Journey Date</small>
                    <div class='fw-bold'>$jdate</div>
                </div>
                 <div class='col-md-4'>
                    <small class='text-muted'>Seat Numbers</small>
                    <div class='fw-bold'>$seat_no ($no_seat Seats)</div>
                </div>
                 <div class='col-md-4'>
                    <small class='text-muted'>Total Fare</small>
                    <div class='fw-bold text-success fs-5'>$$Tfare</div>
                </div>
            </div>

         </div>
         <div class='card-footer bg-white text-center'>
            <small class='text-muted'>Booked on: $booking_date | Status: <span class='badge bg-success'>$ticket_status</span></small>
         </div>
       </div>";
    }
  }
}

// --- ADMIN REPORT 1 (Bus Status) ---
if (isset($_POST['bkd'])) {
  $bid = mysqli_real_escape_string($con, $_POST['bid']);
  $sql2 = "SELECT * FROM bus_details WHERE bus_id='$bid'";
  $run2 = mysqli_query($con, $sql2);
  $row1 = mysqli_fetch_array($run2);

  if($row1){
    $bname = $row1['bname'];
    $seat = $row1['no_seat'];
    $time = $row1['time'];
  }

  $sql = "SELECT * FROM booking_det WHERE bus_id='$bid'";
  $run = mysqli_query($con, $sql);

  if (mysqli_num_rows($run) == 0) {
    echo "<h5 class='text-center text-muted'>No booking history found for this bus.</h5>";
  } else {
    while($row = mysqli_fetch_array($run)){
        $vacant = $row['vacant'];
        $booked = $seat - $vacant;
        $jdate = $row['jdate'];
        $bfrom = $row['bfrom'];
        $bto = $row['bto'];

        echo "
        <div class='row border-bottom py-2'>
          <div class='col-md-2'>$bname</div>
          <div class='col-md-2 fw-bold text-danger'>$booked Booked</div>
          <div class='col-md-2 fw-bold text-success'>$vacant Free</div>
          <div class='col-md-2'>$bfrom</div>
          <div class='col-md-2'>$bto</div>
          <div class='col-md-2'>$jdate</div>
        </div>";
    }
  }
}

// --- ADMIN REPORT 2 (Passenger List) ---
if (isset($_POST['tbkd'])) {
   $bid = mysqli_real_escape_string($con, $_POST['bid']);
   $pdate = mysqli_real_escape_string($con, $_POST['date']);

   $sql2 = "SELECT * FROM bus_details WHERE bus_id='$bid'";
   $run2 = mysqli_query($con, $sql2);
   $row1 = mysqli_fetch_array($run2);

   $fare = ($row1) ? $row1['fare'] : 0;

   $sql = "SELECT * FROM ticket WHERE bus_id='$bid' AND jdate='$pdate'";
   $run = mysqli_query($con, $sql);

   if (mysqli_num_rows($run) == 0) {
     echo "<h5 class='text-center text-muted'>No passengers found for this date.</h5>";
   } else {
     while ($row = mysqli_fetch_array($run)) {
        $pname = $row['pname'];
        $jdate = $row['jdate'];
        $seat_no = $row['seat_no'];
        $no_seat = $row['no_seat'];
        $status = $row['ticket_status'];
        $tfare = $fare * $no_seat;

        echo "
        <div class='row border-bottom py-2'>
          <div class='col-md-2 fw-bold'>$pname</div>
          <div class='col-md-2'>Seat: $seat_no</div>
          <div class='col-md-2'>Qty: $no_seat</div>
          <div class='col-md-2 text-success'>$$tfare</div>
          <div class='col-md-2'><span class='badge bg-success'>$status</span></div>
          <div class='col-md-2'>$jdate</div>
        </div>";
    }
  }
}
?>