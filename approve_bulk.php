<?php
/**
 * Page allowing the user to approve invoices in bulk.
 *
 * PHP version 8.2.12
 *
 * @category InvoiceTracker
 * @package  InvoiceTracker
 * @author   Halil Say <say@hnsay.com.tr>
 * @license  http://opensource.org/licenses/MIT MIT License
 * @link     invoices.com.tr
 */
// Initialize the session

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
      header("location: login.php");
      exit;
}

require_once "/config/config.php";
require_once "error_log.php"; 

//check user's mailgroup

$sql = "SELECT mailgroup FROM users WHERE username=". "'" . $_SESSION["username"] . "'";
$result = mysqli_query($link, $sql);
$mailgroup = mysqli_fetch_array($result)['mailgroup'];


/* No longer showing approved and pending on the main page as admins are already able to filter
if ($_SESSION["usertype"] == "superuser" || $_SESSION["usertype"] == "admin") {
    $sql = "SELECT * FROM invoices WHERE state='Approved' OR state='Rejected' OR (state='Pending' AND assignee = " . "'" . $_SESSION["username"] . "')";
  $result = mysqli_query($link, $sql);
} else {
    $sql = "SELECT * FROM invoices where state='Pending' AND assignee = " . "'" . $_SESSION["username"] . "'";
  $result = mysqli_query($link, $sql);
}
*/

if ($mailgroup === null) {
    $sql = "SELECT * FROM invoices where state='Bekliyor' AND assignee = " . "'" . $_SESSION["username"] . "'";
} else {
    $sql = "SELECT * FROM invoices where state='Bekliyor' AND (assignee = " . "'" . $_SESSION["username"] . "' OR assignee=". "'" . $mailgroup. "')";
}

$result = mysqli_query($link, $sql);

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Toplu Fatura İşleme</title>

    <script src="Datatables/datatables.min.js"></script>
    <script src="Datatables/moment.min.js"></script>
    <script src="Datatables/dataTables.checkboxes.min.js"></script>
    <script src="Datatables/jquery.dataTables.colResize.js"></script>
      <script src="Datatables/select2.min.js"></script>
    <?php //<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"> ?>
    <link rel="stylesheet" href="Datatables/datatables.css"/>
    <link rel="stylesheet" href="css/jquery.dataTables.css">
    <?php //<link rel="stylesheet" href="css/dataTables.checkboxes.css"> ?>
    <link rel="stylesheet" href="css/awesome-bootstrap-checkbox.css">
    <link rel="stylesheet" href="styles.css">
    <?php //<link rel="stylesheet" href="css/jquery.dataTables.colResize.css"> ?>
      <link rel="stylesheet" type="text/css" href="css/select2.min.css">

<style type="text/css">

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

.wrapper{ width: 600px; padding: 20px; text-align: left; }

</style>
</head>

<?php require 'navbar.php'; ?>

<body>
<h1>Toplu Fatura Onaylama</h1> <br>
<div class="wrapper" style="padding-left: 50px;">
<form action="approve_bulk_submit.php" method="post" id="submitBulkForm" onsubmit="sendData()">
            <div class="form-group <?php echo (!empty($comment_err)) ? 'has-error' : ''; ?>">
                <p>Açıklama:</p>
                <textarea style="margin-bottom:20px;" type="text" name="comment" class="form-control" rows="3" maxlength="250" id="first"></textarea>
                <p>Order RFA No:</p>
                <textarea style="width: 250px;margin-bottom:20px;" type="text" name="ponumber" class="form-control" rows="1" maxlength="28" id="second"></textarea>
                <span class="help-block" id="helpBlock"></span>
                <input class="form-check-input" type="checkbox" role="switch" id="checkBox" name="checkBox" onchange='checkAction(this);'>
                <label class="form-check-label" for="flexSwitchCheckDefault">Concur üzerinden işlenecek</label>
            </div>
            <div class="form-group">
                <input form="submitBulkForm" type="submit" class="btn btn-success" value="Onayla" name="approve" style="margin-right: 5px;" onclick="return approveButton()">
                <input form="submitBulkForm" type="submit" class="btn btn-danger" value="Reddet" name="reject" style="margin-right: 5px;" onclick="return rejectButton()">
            </div>
</form>
</div>
<div style="padding-left: 50px;padding-right: 50px;">
  <table class="table responsive" id="sorTable">
    <thead>
    <tr>
      <th data-priority="8" scope="col" style="width: 1%;">#</th>
            <th data-priority="5" scope="col" style="width: 25%;">Tedarikçi</th>
            <th data-priority="1" scope="col" style="width: 15%;">Fatura No</th>
            <th data-priority="2" scope="col" style="width: 1%;">Durum</th>
            <th data-priority="4" scope="col" style="width: 10%;">Tarih</th>
            <th data-priority="6" scope="col" style="width: 1%;">Tutar</th>
      <th data-priority="7" scope="col" style="width: 1%;">PB</th>
      <th scope="col" style="width: 1%;">Açıklama</th>
      <th data-priority="3" scope="col" style="width: 1%;">Atanan</th>
    </tr>
  </thead>
  <tbody>
        <?php while($rows = mysqli_fetch_array($result)): ?>
    <tr>
      <td data-table-header="#"></td>
      <td data-table-header="Tedarikçi"><?php echo $rows['supplier']; ?></td>
      <td data-table-header="Fatura No">
                <?php echo '<a href="invoice.php?'.$rows['no'].'=" target="_blank">'.$rows['no'].'</a>'; ?>
            </td>
      <td data-table-header="Durum"><?php echo $rows['state']; ?></td>
      <td data-table-header="Tarih"><?php echo $rows['date']; ?></td>
      <td data-table-header="Tutar"><?php echo $rows['amount']; ?></td>
      <td data-table-header="PB"><?php echo $rows['currency']; ?></td>
      <td data-table-header="Açıklama"><?php 
                    echo strip_tags($rows['description']);?>
      </td>
      <td data-table-header="Atanan"><?php 
                    echo $rows['assignee'];?>
      </td>
    </tr>

        <?php endwhile; ?>
  </tbody>
</table>
</div>

<script>
const openRows = [];
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
<?php //------------DATATABLES STARTING HERE---------------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>

$(document).ready( function () {
  var table = $('#sorTable').DataTable({
<?php //---------------------------------------------------------------?>
<?php //------------------------General Settings-----------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
    select: {
          style: 'os'
        },
    orderCellsTop: true,
    statesave: true,
    responsive: true,
    <?php //select: "true", ?>
    "oLanguage": {
      "sLengthMenu": "_MENU_ kayıt göster"
    },
    "aLengthMenu": [10, 20, 50],
    "pageLength": 10,
    <?php  //autoWidth: false,?>
    <?php //scrollY: true,?>
    <?php //scrollX: true,?>
    <?php    //dom: 'Bfltrip',?>
      dom: 'fl<"toolbar">trip',
    //dom: 'Bfl<"toolbar">trip',
<?php    /*
        l - show count
        f - search
        t - table
        i - info
        p - pages
        r - processing messages
        
        B - Buttons
        R - ColReorder
        S - Scroller
        P - SearchPanes
        Q - SearchBuilder        
    */
?>
    

<?php //------------------------Language-----------------------?>
    language: {       
            info: "Toplam _TOTAL_ kaydın _START_ - _END_ arasındakileri gösteriliyor",
<?php //------------------------searchPanes Language-----------------------?>
            searchPanes: {
                title: 'Aktif Filtreler - %d',
                copySuccess: {
                    "1": "1 satır panoya kopyalandı",
                    "_": "%ds satır panoya kopyalandı"
                  },
                name: 'filter',
                copyTitle: 'test',
                header: '',
                count: '{total}',
                countFiltered: '{shown} ({total})',
                clearMessage: 'Seçimleri Kaldır',
                showMessage: 'test2',
                collapseMessage: 'test',
                show: 'test2',
                emptyMessage: 'Atanmamış',
                collapse: {0: 'Filtrele', _: 'Filtrelenen (%d)'},
                i18n: {
                  title: {
                          _: 'Seçilen Filtreler - %d',
                          0: 'Filtre seçilmedi',
                          1: 'Bir Filtre Seçildi'
                  },
                },             
            },
<?php //------------------------Buttons Language-----------------------?>
            buttons: {
                copyTitle: '',
                copyKeys: 'Tablodaki veriyi kopyalamak için CTRL veya u2318 + C tuşlarına basınız. İptal etmek için bu mesaja tıklayın veya escape tuşuna basın.',
                copySuccess: {
                    "1": "1 satır kopyalandı",
                    "_": "%ds satır kopyalandı"
                  },
            },
<?php //------------------------Select Language-----------------------?>
            select: {
              rows: {
                _: "%d satır seçildi",
                0: "",
                1: "1 satır seçildi"
              }
            }
<?php //---------------------------------------------------------------?>
        },
<?php //------------------------Language-End-----------------------?>

columnDefs : [ 
            { "defaultContent": "-", "targets": "_all" },
        { "visible": false, "targets": [7]},
        {
          "className": 'details-control',
          "targets": [0],
          'checkboxes': {
               'selectRow': true,
            }
        },
            {
          "className": 'details-dropdown',
          "targets": [1,3,4,5,6,8]
        },
          {targets: "_all", className: 'all'}, //always show column
        { type : 'Date', targets : [4]}, 
        { searchPanes: { show: true }, targets: [1,8] },
        { searchPanes: { show: false}, targets: [0,2,3,4,5,6,7] }
      ],

    //-----------------buttons--------------------------------
      buttons: [ 
        { extend: 'searchPanes', text: 'Filtrele', config: { cascadePanes: false, clear: false, collapse: false } }
    ],
    <?php //------------------------BUTTONS END--------------------?>

    //--------------buttons-end--------------------------

<?php //------------------------INITCOMPLETE BEGIN---------------------?>
<?php //------------------------INITCOMPLETE BEGIN---------------------?>
<?php //------------------------INITCOMPLETE BEGIN---------------------?>
    initComplete: function () {
      var api = this.api();
      count = 0;
      
      this.api().columns().every( function () {
          if ( this.index() == 1 ||  this.index() == 4 || this.index() == 8) {
                var title = $(this.header()).html();
<?php           //replace spaces with dashes?>
                var column = this;
                var select = $('<select id="' + title + '" class="select2" ></select>')
                    .appendTo( $(column.header()).empty() )
                    .on( 'change', function () {
<?php                 //Get the "text" property from each selected data?>
<?php                 //regex escape the value and store in array?>
                      var data = $.map( $(this).select2('data'), function( value, key ) {
                        return value.text ? '^' + $.fn.dataTable.util.escapeRegex(value.text) + '$' : null;
                                 });
                      
                      //if no data selected use ""
                      if (data.length === 0) {
                        data = [""];
                      }
                      
                      //join array into string with regex or (|)
                      var val = data.join('|');
                      
                      //search for the option(s) selected
                      column
                            .search( val ? val : '', true, false )
                            .draw();
                    } );

                        column.data().unique().sort().each( function ( d, j ) {
                        select.append( '<option value="'+d+'">'+d+'</option>' );                    
                    } );
                    
                
              //use column title as selector and placeholder
              $('#' + title).select2({
                multiple: true,
                closeOnSelect: false,
                placeholder: title,
                        minimumResultsForSearch: -1,
                        allowClear: true
              });
              
              //initially clear select otherwise first option is selected
              $('.select2').val(null).trigger('change');
            }
        });
        
    }
<?php //------------------------INITCOMPLETE END---------------------?>

  });

  //-----------------------------------------------------------------------------
  //------------------Datatable definition end----------------------------------------------
  function collapseAll ( e, dt, node, config ) {
              table.rows().every(function () {
                  var row = this;
                  if (row.child.isShown()) {
                    row.child.hide();
                    $(this.node()).removeClass('shown');
                  }
              });
  }

  <?php //--------------------HOVER FUNCTION-----------------------?>
    table.on("mouseenter", "td", function() {        
        if ($(this).hasClass('details-dropdown')) {
            $(this).attr('title', table.cell(table.row(this.closest('tr')).node()._DT_RowIndex, 7).data());
        }        
    });

<?php //---------------DROPDOWN CHILD ROWS-----------------------?>
    table.on('click', 'td.details-dropdown', function () {
    let tr = event.target.closest('tr');
    let row = table.row(tr);
    var rowIndex = row.node()._DT_RowIndex;
    var idx = openRows.indexOf(tr.id);
 
    if (row.child.isShown()) {
        tr.classList.remove('shown');
        row.child.hide();
 
<?php   // Remove from the 'open' array ?>
        openRows.splice(idx, 1);
    } else if (row.child() && row.child().length) {
        row.child(childRow(row.data(), rowIndex )).show();
        
        if (idx === -1) {
          openRows.push(tr.id);
        }
    } else {
        tr.classList.add('shown');

        row.child(childRow(row.data(), rowIndex )).show();

        var collapseButtonClone = $('#collapseButton').clone();
        collapseButtonClone.on('click', collapseAll);
        collapseButtonClone.appendTo($('#td'+ rowIndex));

        // Add to the 'open' array
        if (idx === -1) {
          openRows.push(tr.id);
        }
    }
    });

});

//-----------------------------------------------------------------------------
//--------------------------DATATABLE END------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------
//-----------------------------------------------------------------------------

function format (d) {

return '<table cellpadding="5" cellspacing="0" border="0"  style="width:100%">'+
  '<tr>'+
  '<td>Açıklama:</td>'+
  '<td>' + d[7] + '</td>'+
  '</tr>'+
  '</table>';
}

function childRow (d, rowIndex) {
    return '<table cellpadding="5" cellspacing="0" border="0"  style="width:100%;">'+
      '<tr>'+
      '<td colspan="2" style="border:0px;" id="td'+ rowIndex +'">'+
      '</td>'+'</tr>'+
      '<tr>'+
      '<td style="width:20%">Açıklama:</td>'+
      '<td colspan="2"><b>' + d[7] + '</b></td>'+
      '</tr>'+  
      '</table>';
  }

//var checkbox = document.getElementById("checkBox");
//
//if (checkbox.checked == true) {
//    document.getElementById("first").value = "Concur üzerinden işlenecek";
//    document.getElementById("first").readOnly = true;
//    document.getElementById("second").readOnly = true;
//    document.getElementById("reject").disabled = true;
//}else {
//    document.getElementById("first").readOnly = false;
//    document.getElementById("second").readOnly = false;
//    document.getElementById("reject").disabled = false;
//}

window.onload = function() {
    var checkBox = document.getElementById("checkBox");
    checkAction(checkBox); // Call the function to set the initial state
}

function checkAction(checkBox) {
    if (checkBox.checked == true) {
        document.getElementById("first").value = "Concur üzerinden işlenecek";
        document.getElementById("first").readOnly = true;
        document.getElementById("second").readOnly = true;
        document.getElementById("reject").disabled = true;
    }else {
        document.getElementById("first").readOnly = false;
        document.getElementById("second").readOnly = false;
        document.getElementById("reject").disabled = false;
   }
}

function validateForm() {
    var firstTextarea = document.getElementById('first').value.trim();
    var secondTextarea = document.getElementById('second').value.trim();
    var helpBlock = document.getElementById('helpBlock');

    if (firstTextarea === '' && secondTextarea === '') {
        helpBlock.textContent = "Lütfen bir açıklama veya PO/CO/RFA/SO numarası giriniz.";
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}



function sendData() {

  var table = $('#sorTable').DataTable();
  var cellTexts = [];
  var rows = table.rows( {selected: true} ).indexes();
  var data = table.cells(rows, 2).nodes().to$();
  $.each( data, function( key, value ) {
    //cellTexts.push($(value).text().replace(/\t|\n|\r|:|\s/g,'')); old useless
    cellTexts.push($(value).text());
    });
    
    
  
  
  var frm = document.getElementById("submitBulkForm");

    
    
  for (var i = 0; i < cellTexts.length; i++) {
  
    var inp = document.createElement("input");
    inp.setAttribute('type', "text");
    inp.setAttribute('name', "array[ ]");
    inp.setAttribute('value', cellTexts[i]);
    inp.style.display = 'none';
    inp.hidden = 'true';
    frm.appendChild(inp);
  }
}

  
  $(function(){
  $('.sorTable').keydown(function (e) {
    switch(event.keyCode)
    {
      //arrow down
      case 40:
      var thisRow = $(this).DataTable().row( { selected: true } ).node();
      var nextRow = $(thisRow).next('tr');
      var nextRowIdx = $(nextRow).index();
      var rowCount = $(this).DataTable().rows().count();
 
      if (!(nextRowIdx > rowCount)) {
        if ($(nextRow).hasClass('group')) nextRow = $(nextRow).next('tr');
        $(this).DataTable().row(nextRow).select();
 
        var nextRowID = $(nextRow).attr('id');
        document.getElementById(nextRowID).scrollIntoViewIfNeeded(false);
      }
 
      e.preventDefault();
      break;
      //arrow up
      case 38:
      var thisRow = $(this).DataTable().row( { selected: true } ).node();
      var lastRow = $(thisRow).prev('tr');
      var lastRowIdx = $(lastRow).index();
 
      if (!(lastRowIdx < 0)) {
        if ($(lastRow).hasClass('group')) lastRow = $(lastRow).prev('tr');
        $(this).DataTable().row(lastRow).select();
 
        var lastRowID = $(lastRow).attr('id');
        document.getElementById(lastRowID).scrollIntoViewIfNeeded(false);
      }
 
      e.preventDefault();
      break;
    }
  });
  $('.dataTable').focus();
});

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

function approveButton () {
    
      var firstTextarea = document.getElementById('first').value.trim();
      var secondTextarea = document.getElementById('second').value.trim();
      var helpBlock = document.getElementById('helpBlock');
      
      if (firstTextarea === '' && secondTextarea === '') {
          helpBlock.textContent = "Lütfen bir açıklama veya PO/CO/RFA/SO numarası giriniz.";
          return false; // Prevent form submission
      }
    
      var table = $('#sorTable').DataTable();
      var cellTexts = [];
      var rows = table.rows( {selected: true} ).indexes();
      var data = table.cells(rows, 2).nodes().to$();
      $.each( data, function( key, value ) 
      {
        cellTexts.push($(value).text());
      });
    
    if (cellTexts.length == 0) {
      alert("Lütfen fatura seçiniz!");
      return false;
    } else {

      var warningText = "Seçtiğiniz " + cellTexts.length + " fatura onaylanacak, devam edilsin mi?\n\n";

      warningText = warningText + "\n";

        for (var i = 0; i < cellTexts.length; i++) {
        warningText = warningText + "\n" + cellTexts[i];
      }
      return confirm(warningText);
    }
}

function rejectButton () {
    
      var firstTextarea = document.getElementById('first').value.trim();
      var secondTextarea = document.getElementById('second').value.trim();
      var helpBlock = document.getElementById('helpBlock');
      
      if (firstTextarea === '' && secondTextarea === '') {
          helpBlock.textContent = "Lütfen bir açıklama veya PO/CO/RFA/SO numarası giriniz.";
          return false; // Prevent form submission
      }
    
      var table = $('#sorTable').DataTable();
      var cellTexts = [];
      var rows = table.rows( {selected: true} ).indexes();
      var data = table.cells(rows, 2).nodes().to$();
      $.each( data, function( key, value ) 
      {
        cellTexts.push($(value).text());
      });
    
    if (cellTexts.length == 0) {
      alert("Lütfen fatura seçiniz!");
      return false;
    } else {

      var warningText = "Seçtiğiniz " + cellTexts.length + " fatura reddedilecek, devam edilsin mi?\n\n";

      warningText = warningText + "\n";

        for (var i = 0; i < cellTexts.length; i++) {
        warningText = warningText + "\n" + cellTexts[i];
      }
      return confirm(warningText);
    }
}

$.fn.dataTable.ext.errMode = 'none';

</script>
</body>
</html>
