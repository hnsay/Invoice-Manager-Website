<?php
/**
 * Invoice auto assignment rules management.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <github@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once SESSION_HELPER;
require_once MODEL_USER;
require_once MODEL_INVOICE;
protectPage(['superuser']);

$sql = "SELECT * FROM rules";
$result = mysqli_query($link, $sql);

$rules = [];
while ($row = mysqli_fetch_assoc($result)) {
    $rules[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['identifier'])) {
        $identifier = trim($_POST['identifier']);
        switch ($identifier) {
        case 'from-create':
            if (!empty(trim($_POST['supplier'])) && !empty(trim($_POST['username']))) {
                if (findInArray($rules, 'supplier', $_POST['supplier'])) {
                    updateRule($link, $_POST['supplier'], $_POST['username']);
                } else {
                    createRule($link, $_POST['supplier'], $_POST['username']);
                }
            }
            break;
        case 'form-delete':
            if (!empty(trim($_POST['username']))) {
                deleteRule($link, $_POST['supplier']);
            }
            break;

        default:
            break;
        }
    }
}

$rules = getRules($link);
$users = getAllUsernames($link);
$suppliers = getSuppliers($link);
?>

<!DOCTYPE html>
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<html lang="en" style="background-image: url('/public/images/background.jpg');">
<head>
    <meta charset="UTF-8">
    <title>Manage Rules</title>
    <link rel="icon" type="image/x-icon" href="/public/icons/favicon.ico">

    <script src="/public/Datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="/public/Datatables/datatables.css"/>
    <link rel="stylesheet" href="/public/css/jquery.dataTables.css">
    <link rel="stylesheet" href="/public/css/styles.css">
  <style type="text/css">
select{ text-align: left;}

.wrapper{ 
  padding: 20px;
}

.trapper{
  width: 800px;
  padding: 20px;
  margin-left: 80px;
  text-align: left;
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

<body><div class="trapper">
        <h2>Create Rule</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">                
            <input type="hidden" name="identifier" value="from-create">        
            <div class="form-group">
                <label>Supplier</label>
                <select class="form-control" name="supplier">
                <?php foreach ($suppliers as $row) {
                    ?><option><?php echo $row['supplier'] ?></option>
                <?php }?>
                </select> 
            </div>
        
            <div class="form-group">
                <label>User</label>
                <select class="form-control" name="username">
                <?php foreach ($users as $row) {
                    ?><option><?php echo $row['username'] ?></option>
                <?php }?>
                </select> 
            </div>
              
            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Submit">
                <a class="btn btn-link" href="manageusers.php">Cancel </a>
            </div>
        </form>
        
        <h2 style="padding-top: 20px;">Delete Rule</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">                
            <input type="hidden" name="identifier" value="form-delete">        
            <div class="form-group">
            <label>Please select rule to delete</label>
            <select class="form-control" name="supplier" onchange="getUser(this);" id="selection">
                <?php foreach ($rules as $row) {
                    ?><option><?php echo $row['supplier'] ?></option>
                <?php }?>
            </select><span class="help-block"></span><br>
            </div>
        
            <div class="form-group">
                <label>User/Group Name</label>
                <input type="text" name="username" class="form-control" id="username">
            </div>
              
            <div class="form-group">
                <button type="submit" class="btn btn-danger" onclick="return confirm('You are about to delete the selected rule, are you sure?')">Delete</button>
                <a class="btn btn-link" href="manageusers.php">Cancel </a>
            </div>
        </form>
</div>
</div>
<div class="wrapper">
<div style="padding-left: 50px;padding-right: 50px;">
  <table class="table responsive" id="sorTable">
    <thead>
    <tr>
      <th data-priority="6" scope="col" style="width: 1%;">#</th>
            <th data-priority="1" scope="col">Supplier</th>
            <th data-priority="2" scope="col">User/Group Name</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rules as $row) { ?>
          <tr>
        <td data-table-header="#"></td>
              <td data-table-header="Supplier"><?php echo $row['supplier']; ?></td>
              <td data-table-header="User/Group Name"><?php echo $row['username']; ?></td>
        </tr>
    <?php } ?>
  </tbody>
</table>
</div>

<script>

       
       document.getElementById('username').readOnly = true
       
       var rules = <?php echo json_encode($rules); ?>;
       
       var user = rules.filter(obj => {return obj.supplier === document.getElementById("selection").value})[0].username;
       document.getElementById("username").value = user;
       
       function getUser(selection){
        var user = rules.filter(obj => {return obj.supplier === selection.value})[0].username;
        document.getElementById("username").value = user;
       }
       
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
    pageLength: 50,
    //--------------------------------------------------------
    responsive: false,
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
});


//-----------------------------------------------------------------------------
//--------------------------DATATABLE END------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
</script>

</body>
<html>


