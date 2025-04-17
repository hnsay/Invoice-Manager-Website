<?php
/**
 * User Management.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once SESSION_HELPER;
require_once MODEL_USER;

protectPage(['superuser']);

$users = getAllUsers($link);
?>

<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <script src="/public/Datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="/public/Datatables/datatables.css"/>
    <link rel="stylesheet" href="/public/css/jquery.dataTables.css">
    <link rel="stylesheet" href="/public/css/styles.css">
  <style type="text/css">
select{ text-align: left;}

.wrapper{ 
  padding: 50px; 
  text-align: left;
}

.wrapper a {
  margin-left: 10px;
}


    body{ font: 12px sans-serif; text-align: center; }
div.dtsp-title, div.dtsp-topRow {
  display: none;
  display: none;
}
:root {
--dt-row-selected: 250, 150, 3;
}
table {
  width: 100%;
  border-collapse: collapse;
}

.navbar {
      margin-bottom: 0;
      border-radius: 0;
}

span {
  font-style: italic
}

th {
  background: #9F2725;
  color: #ffffff;
}

th {
  border: 1px solid #ccc;
}

td {
  border: 1px solid #ccc;
}
table.dataTable tbody td {
  vertical-align: middle;
}

table.dataTable thead th {
  text-align: center;
}

</style>
</head>

<?php require 'navbar.php'; ?>

<body>
  
<div class="wrapper">
    <?php if (isset($_GET['status']) && isset($_GET['user'])) : ?>
        <?php if ($_GET['status'] === 'deleted') : ?>
            <h3 style="color: green;">User "<?php echo htmlspecialchars($_GET['user']); ?>" has been deleted successfully.</h3>
        <?php elseif ($_GET['status'] === 'error') : ?>
            <h2 style="color: red;">Could not delete user "<?php echo htmlspecialchars($_GET['user']); ?>". Please try again.</h3>
        <?php endif; ?>
    <?php endif; ?>
  <h2>Manage Users And Groups</h2><br>
  <form class="form-group" id="myform"><br>
    <a href="register.php" class="btn btn-primary" style="margin-left:0px;">Create User</a>
    <a href="createmailgroup.php" class="btn btn-primary">Create Group</a>
    <a class="btn btn-primary" href="reset-user.php">Change User Password</a>
    <a class="btn btn-primary" href="editusers.php">Edit Users</a>
    <a class="btn btn-primary" href="deleteuser.php">Remove Users & Groups</a>
    <a class="btn btn-primary" href="rules.php">Assingment Rules</a>
  </form>
</div>

<div style="padding-left: 50px;padding-right: 50px;">
  <table class="table responsive" id="sorTable">
    <thead>
    <tr>
      <th data-priority="6" scope="col" style="width: 1%;">#</th>
            <th data-priority="1" scope="col">Kullanıcı Adı</th>
            <th data-priority="5" scope="col">Oluşturulma Tarihi</th>
            <th data-priority="2" scope="col">Email</th>
            <th data-priority="3" scope="col">Tür</th>
      <th data-priority="4" scope="col">Grup</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
          <tr>
        <td data-table-header="#"></td>
              <td data-table-header="Kullanıcı Adı"><?php echo $user['username']; ?></td>
              <td data-table-header="Oluşturulma Tarihi"><?php echo $user['created_at']; ?></td>
              <td data-table-header="Email"><?php echo $user['email']; ?></td>
        <td data-table-header="Tür"><?php echo $user['usertype']; ?></td>
        <td data-table-header="Grup"><?php echo $user['mailgroup']; ?></td>
        </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<script>
//-----------------------------------------------------------------------------
//-----------------------DATATABLE START--------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------

$(document).ready( function () {
  var table = $('#sorTable').DataTable({
    //--------------------------------------------------------
    dom: 'Bfltrip',
    //------------------------language-----------------------
    language: {    
      info: "Toplam _TOTAL_ kaydın _START_ - _END_ arasındakileri gösteriliyor",
            //----------------------Buttons-----------------------
            buttons: {
                copyTitle: '',
                copyKeys: 'Tablodaki veriyi kopyalamak için CTRL veya u2318 + C tuşlarına basınız. İptal etmek için bu mesaja tıklayın veya escape tuşuna basın.',
                copySuccess: {
                    "1": "1 satır kopyalandı",
                    "_": "%ds satır kopyalandı"
                  },
            },
         //---------------------Select----------------------------
            select: {
              rows: {
                _: "%d satır seçildi",
                0: "",
                1: "1 satır seçildi"
              }
            }
            //-------------------------------------------------------
        },
        //---------------------language-end---------------------------
    select: false,
    //--------------------------------------------------------
    responsive: true,
    //--------------------------------------------------------
    columnDefs : [  
        //{ "visible": false, "targets": 7 },
        //{
          //"className":      'details-control',
          //"targets": "_all"
          //"targets": [0,1,3,4,5,6,8]
        //}, 
        { type : 'Date', targets : [2]}],
    //-----------------buttons--------------------------------
      buttons: [{ extend: 'excel', text: 'Liste İndir' },
    //{ text: 'Ata', action: sendData()}
    //'pdf',
        ],

    //--------------buttons-end--------------------------

    "oLanguage": {
      "sLengthMenu": "_MENU_ kayıt göster",
    } 
  });

  //-----------------------------------------------------------------------------
  //------------------Datatable definition end----------------------------------------------


  table
    .on('order.dt search.dt', function () {
        let i = 1;
 
        table
            .cells(null, 0, { search: 'applied', order: 'applied' })
            .every(function (cell) {
                this.data(i++);
            });
    })
    .draw();

    table.on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );

      if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
      }
      else {
      // Open this row
      row.child( format(row.data()) ).show();
      tr.addClass('shown');
      }
      });

});


//-----------------------------------------------------------------------------
//--------------------------DATATABLE END------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
</script>

</body>
</html>

