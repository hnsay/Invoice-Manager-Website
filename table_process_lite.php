<?php
/**
 * Create a DataTable for process_bulk.php.
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
    header("Location:login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

require_once "/config/config.php";
require_once "error_log.php"; 

?>

<script>
    const openRows = [];

<?php //Date Picker Init data[5]?>

let minDate, maxDate;

DataTable.ext.search.push(function (settings, data, dataIndex) {
    let min = minDate.val();
    let max = maxDate.val();
    let date = new Date(data[5]);
 
    if (
        (min === null && max === null) ||
        (min === null && date <= max) ||
        (min <= date && max === null) ||
        (min <= date && date <= max)
    ) {
        return true;
    }
    return false;
});

minDate = new DateTime('#min', {
    format: 'DD/MM/YYYY',
    i18n: {
            previous: 'Geri',
            next: 'İleri',
            months: [
                'Ocak',
                'Şubat',
                'Mart',
                'Nisan',
                'Mayıs',
                'Haziran',
                'Temmuz',
                'Ağustos',
                'Eylül',
                'Ekim',
                'Kasım',
                'Aralık'
            ],
            weekdays: [ 'Paz', 'Pzt', 'Sal', 'Çar', 'Per', 'Cum', 'Cmt' ]
    }
});
maxDate = new DateTime('#max', {
    format: 'DD/MM/YYYY'
});


<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
<?php //------------DATATABLES STARTING HERE---------------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>


$(document).ready( function () {
  var table = $('#sorTable').DataTable({

<?php /*
    ajax:  {
        url: "call.php",
        data: function(d){
            d.supplier = "supplier";
            d.no = "supplier";
            d.assignee = "supplier";
            d.state = "supplier";
            d.date = "supplier";
            d.amount = "supplier";
            d.currency = "supplier";
            d.po_rfa = "supplier";
            d.description = "supplier";
        }
    },*/
?>   
    ajax:  { url: "<?php

    
    if (basename($_SERVER['PHP_SELF']) == 'process_bulk.php') {
        echo "call_pending_finance.php";
    } else if (basename($_SERVER['PHP_SELF']) == 'approve_bulk.php') {
        echo "call_assigned_invoices.php";
    } else {
          header("location: 404.php");
    }?>" },<?php
              /*dataSrc: function ( json ) {
                for ( var i=0, ien=json.data.length ; i<ien ; i++ ) {
                  json.data[i][0] = '<a href="/message/'+json.data[i][0]+'>View message</a>';
                }
              return json.data;
              }*/?>          
    columns: [
        {},
        { data: 'supplier' },
        { data: 'no',
          render: function ( data, type, row, meta ) {
            return '<a href="invoice.php?'+data+'=" target="_blank">'+data+'</a>';
            }
        },
        { data: 'state' },
        { data: 'date' },
        { data: 'amount' },
        { data: 'currency' },
        { data: 'description' },
        { data: 'assignee' }
    ], 
    rowId: "no", <?php // use no for row ids ?>
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

   function childRow (d, rowIndex) {
    return '<table cellpadding="5" cellspacing="0" border="0"  style="width:100%;">'+
      '<tr>'+
      '<td colspan="2" style="border:0px;" id="td'+ rowIndex +'">'+
      '<form target="_blank" data-rowindex="'+ rowIndex +'" '+
      'onsubmit="sendOneRow(event,this)" action="assign.php" method="post" style="display: inline;">'+
      '</form>'+
      '</td>'+'</tr>'+
      '<tr>'+
      '<td style="width:20%">Açıklama:</td>'+
      '<td colspan="2"><b>' + d['description'] + '</b></td>'+
<?php //'<td colspan="2"><b>' + d[10] + '</b></td>'+ ?>
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
    var textArea = document.getElementById('textArea').value.trim();
    var helpBlock = document.getElementById('helpBlock');

    if ( textArea === '') {
        helpBlock.textContent = "Lütfen bir açıklama giriniz.";
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
    
      var textArea = document.getElementById('textArea').value.trim();
      var helpBlock = document.getElementById('helpBlock');
      
      if (textArea === '') {
          helpBlock.textContent = "Lütfen bir açıklama giriniz.";
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

      var warningText = "Seçtiğiniz " + cellTexts.length + " fatura işlenecek, devam edilsin mi?\n\n";

      warningText = warningText + "\n";

        for (var i = 0; i < cellTexts.length; i++) {
        warningText = warningText + "\n" + cellTexts[i];
      }
      return confirm(warningText);
    }
}

function rejectButton () {
    
      var textArea = document.getElementById('textArea').value.trim();
      var helpBlock = document.getElementById('helpBlock');
      
      if (textArea === '') {
          helpBlock.textContent = "Lütfen bir açıklama giriniz.";
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

      var warningText = "Seçtiğiniz " + cellTexts.length + " fatura geri gönderilecek, devam edilsin mi?\n\n";

      warningText = warningText + "\n";

        for (var i = 0; i < cellTexts.length; i++) {
        warningText = warningText + "\n" + cellTexts[i];
      }
      return confirm(warningText);
    }
}

$.fn.dataTable.ext.errMode = 'none';

</script>