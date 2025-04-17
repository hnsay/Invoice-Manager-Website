<?php
/**
 * Default page for every user.
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


$sql = "SELECT mailgroup FROM users WHERE username=". "'" . $_SESSION["username"] . "'";
$result = mysqli_query($link, $sql);
$mailgroup = mysqli_fetch_array($result)['mailgroup'];


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
    <title>Anasayfa</title>
    <script src="/public/Datatables/datatables.min.js"></script>
    <script src="/public/Datatables/dataTables.checkboxes.min.js"></script>
    <link rel="stylesheet" href="/public/Datatables/datatables.css"/>
    <link rel="stylesheet" href="/public/css/jquery.dataTables.css">
    <link rel="stylesheet" href="/public/css/styles.css">
    <link rel="stylesheet" href="/public/css/awesome-bootstrap-checkbox.css">




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

</style>
</head>

<?php require 'navbar.php'; ?>

<body>

<div style="padding-left: 50px;padding-right: 50px;">
  <h1>Size Atanmış Bekleyen Faturalar</h1> <br>
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
      <th data-priority="9" scope="col" style="width: 1%;"></th>
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

      <td data-table-header="" style="vertical-align: middle;">
        <form method="post" class="inline" action="submit.php">  
                    <input type="hidden"/>
                        <button type="submit" name="<?php echo $rows['no'];?>" class="btn btn-success">
                                İşlem Yap
                        </button>
                </form> 
      </td>
    </tr>

        <?php endwhile; ?>
  </tbody>
</table>
</div>
<div style="font-size:20px;padding-bottom:50px;margin-:50px;">
    <a href="https://outlook.office.com/mail/options/mail/junkEmail" target="_blank">Email Spam/Junk Adres Yönetimi</a><br>
    <?php //<img src="images/sender-unblock.png"> ?>
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
      dom: 'Bfl<"toolbar">trip',
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
        { searchPanes: { show: false}, targets: [0,2,3,4,5,6,7,9] }
      ],

    //-----------------buttons--------------------------------
      buttons: [ 
        { extend: 'excel', 
        text: 'Seçilenleri İndir', 
        autoFilter: true,
        exportOptions: {
            trim: false,
          stripHtml: false,
          columns: [ 1, 2, 3, 4, 5, 6, 7, 8],
            format: {
              header: function ( html, colIdx ) { //rowIdx
                if ( colIdx == 1 ) {
                  return "Tedarikçi";
                }
                if ( colIdx == 3 ) {
                  return "Atanan";
                }
                if ( colIdx == 4 ) {
                  return "Durum";
                }
                if ( colIdx == 5 ) {
                  return "Tarih";
                }
                if ( colIdx == 7 ) {
                  return "PB";
                }
                return html;
              },
              body: function ( data, row, column, node ) {
                return column == 1 ? data.replace(/<\/?[^>]+(>|$)/g, "") : data;
              }
            }},
        }, 
        { extend: 'searchPanes', text: 'Filtrele', config: { cascadePanes: false, clear: false, collapse: false } }
    ]
    <?php //------------------------BUTTONS END--------------------?>

    //--------------buttons-end--------------------------
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

</script>
</body>
</html>
