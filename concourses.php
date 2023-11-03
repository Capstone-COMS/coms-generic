<!-- ******************** -->
<!-- ***START SESSION**** -->
<!-- ******************** -->
<?php
   session_name("user_session");
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
require('includes/dbconnection.php');
?>
<!-- ******************** -->
<!-- ***** PHP CODE ***** -->
<!-- ******************** -->
<?php
// Check if the user is logged in
if (!isset($_SESSION['uid'])) {
    header('Location: login.php');
    exit();
}

$uid = $_SESSION['uid'];
$utype = $_SESSION['utype'];

// $mappp = $_SESSION['approved_concourse'];

// Check if there are approved concourse details in the session

// **********************
// ***USER VERIFY********
// **********************
// Check the status in the user_verification table
$verificationStatus = "Not approved"; // Default status
$verificationQuery = "SELECT status, first_name, last_name, address, gender, birthday FROM user_verification WHERE user_id = $uid";
$verificationResult = mysqli_query($con, $verificationQuery);

if ($verificationResult && mysqli_num_rows($verificationResult) > 0) {
    $verificationData = mysqli_fetch_assoc($verificationResult);
    $verificationStatus = $verificationData['status'];
}


// **********************
// ***MAP VERIFY*********
// **********************


// Check the status in the user_verification table
// $mapStatus = "Not approved"; // Default status
// // $mapStatusApprove = "approve"; // Default status
// $mapQuery = "SELECT * FROM concourse_verification WHERE status = $mapStatus";
// $mapResult = mysqli_query($con, $mapQuery);

// if ($mapResult && mysqli_num_rows($mapResult) > 0) {
//     $mapData = mysqli_fetch_assoc($mapResult);
//     $mapStatus = $mapData['status'];

// }

// **************************************

$approvedMapQuery = "SELECT * FROM concourse_verification WHERE status = 'approved'";
$approvedMapResult = mysqli_query($con, $approvedMapQuery);

?>
<!-- ******************** -->
<!-- **** START HTML **** -->
<!-- ******************** -->
<?php
include('includes/header.php');

include('includes/nav.php');
?>
<section style= "margin-top:90px;">
   <?php
   //    echo 'Hi, ' . $_SESSION['uname'] . ' (' . $_SESSION['utype'] . ')';
   // echo $utype;
?>
   <div class="container-fluid">
      <!-- ********************************************************************** -->
      <!-- **** CTA BUTTON DISPLAY DEPENDING ON USER TYPE AND ACCOUNT STATUS **** -->
      <!-- ********************************************************************** -->
      <!-- OWNER -->
      <?php if ($verificationStatus === 'approved' && $utype === 'Owner'): ?>
      <h3>Your Concourse</h3>
      <button id="openAddConcourseModal" class="btn-sm btn btn-success">Add a Concourse</button>
      <!-- <a href="concourse_add.php">
         <button class="btn-sm btn btn-success">Add a Concourse</button>
         </a> -->
      <?php elseif ($verificationStatus === 'rejected' && $utype === 'Owner'): ?>
      <div id="verificationModal" class="prompt-modal">
         <div class="modal-content">
            <span class="close">&times;</span>
            <p>Verify your account to add concourse.</p>
            <a href="verification_account.php" class="btn-sm btn btn-success">Verify Account</a>
         </div>
      </div>
      <!-- TENANT -->
      <?php elseif ($verificationStatus === 'approved' && $utype === 'Tenant'): ?>
      <a href="tenant-apply-space.php">
      <button class="btn-sm btn btn-success">Apply For Space</button>
      </a>
      <?php else: ?>
      <div id="verificationModal" class="prompt-modal">
         <div class="modal-content">
            <span class="close">&times;</span>
            <p>Verify your account to apply for space.</p>
            <a href="verification_account.php" class="btn-sm btn btn-success">Verify Account</a>
         </div>
      </div>
      <?php endif; ?>
  
      <?php
      if ($approvedMapResult && mysqli_num_rows($approvedMapResult) > 0) {
          echo '<h3>Approved Maps</h3>';
          echo '<table>';
          echo '<tr>';
          echo '<th>Concourse ID</th>';
          echo '<th>Owner ID</th>';
          echo '<th>Concourse Name</th>';
          echo '<th>Concourse Map</th>';
          echo '</tr>';

          while ($mapData = mysqli_fetch_assoc($approvedMapResult)) {
              echo '<tr>';
              echo '<td>' . $mapData['concourse_id'] . '</td>';
              echo '<td>' . $mapData['owner_id'] . '</td>';
              echo '<td>' . $mapData['concourse_name'] . '</td>';
              echo '<td><a href="../uploads/' . $mapData['concourse_map'] . '">View Map</a></td>';
              echo '</tr>';
          }

          echo '</table>';
      } else {
          echo 'No approved maps found.';
      }

?>
  
    <!-- <h3>Approved Concourse Details</h3>
    <table>
        <tr>
            <th>Concourse ID</th>
            <th>Owner ID</th>
            <th>Owner Name</th>
            <th>Concourse Name</th>
            <th>Concourse Map</th>
            <th>Spaces</th>
            <th>Status</th>
        </tr>
        <tr>
            <td><?= $approvedConcourseDetails['concourse_id'] ?></td>
            <td><?= $approvedConcourseDetails['owner_id'] ?></td>
            <td><?= $approvedConcourseDetails['owner_name'] ?></td>
            <td><?= $approvedConcourseDetails['concourse_name'] ?></td>
            <td><a href="../uploads/<?= $approvedConcourseDetails['concourse_map'] ?>">View Map</a></td>
            <td><?= $approvedConcourseDetails['spaces'] ?></td>
            <td><?= $approvedConcourseDetails['status'] ?></td>
        </tr>
    </table> -->
 
   </div>

   <!-- **************************************** -->
   <!-- ******DISPLAYED FEATURED CONCOURSE****** -->
   <!-- **************************************** -->
   <div class= "container-fluid">
      <h3>Concourses</h3>

   </div>


   <!-- **************************************** -->
   <!-- *****END DISPLAYED FEATURED CONCOURSE*** -->
   <!-- **************************************** -->

   <!-- //////////////////////////////////////// -->

   <!-- **************************************** -->
   <!-- ********ADD CONCOURSE MODAL************* -->
   <!-- **************************************** -->
   <div id="addConcourseModal" class="modal">
      <div class="modal-content">
         <span class="close" id="closeAddConcourseModal">&times;</span>
         <h2>Add a Concourse</h2>
         <!-- <form id="concourseForm" method="POST" action="verification_concourse_process.php"> -->
         <form id="concourseForm" method="POST" action="verification_concourse_process.php" enctype="multipart/form-data">

            <label for="concourseName">Concourse Name:</label>
            <input type="text" id="concourseName" name="concourseName" required>
            <label for="concourseAddress">Concourse Address:</label>
            <input type="text" id="concourseAddress" name="concourseAddress" required>
            <label for="concourseImage">Concourse Image:</label>
            <input type="file" id="concourseImage" name="concourseImage" required>
            <label for="concourseSpaces">Spaces:</label>
            <!-- <textarea id="concourseSpaces" name="concourseSpaces" required></textarea> -->
            <input type="number" id="concourseSpaces" name="concourseSpaces" required>
            <button type="submit" class="btn btn-success"name="submit_concourse" >Submit</button>
         </form>
      </div>
   </div>
   <!-- **************************************** -->
   <!-- ********END OF ADD CONCOURSE MODAL****** -->
   <!-- **************************************** -->
</section>
<?php
// echo $_SESSION['uemail'];
// echo $_SESSION['approved_concourse'];
?>
<?php include('includes/footer.php'); ?>