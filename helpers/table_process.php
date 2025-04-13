<?php
/**
 * Create a DataTable for allinvoices.php, pending.php and pending_finance.php.
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

if ($_SESSION["usertype"] != "superuser" && $_SESSION["usertype"] != "admin" ) {
      header("location: 403.php");
      exit;
}

require_once $_SERVER['DOCUMENT_ROOT'] . "/config/config.php";
require_once $_SERVER['DOCUMENT_ROOT'] . "/config/error_log.php"; 

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
        url: "/helpers/call.php",
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
    
    if (basename($_SERVER['PHP_SELF']) == 'allinvoices.php') {
        echo "/helpers/call.php";
    } else if (basename($_SERVER['PHP_SELF']) == 'pending.php') {
        echo "/helpers/call_pending.php";
    } else if (basename($_SERVER['PHP_SELF']) == 'pending_finance.php') {
        echo "/helpers/call_pending_finance.php";
    } else {
          header("location: 403.php");
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
        { data: 'assignee' },
        { data: 'state' },
        { data: 'date' },
        { data: 'amount' },
        { data: 'currency' },
        { data: 'comment' },
        { data: 'po_rfa' },
        { data: 'description' },
        { render: function ( data, type, row, meta ) {
            return '<form method="post" class="inline" action="/helpers/submit.php" target="_blank">'+
            '<input type="hidden"/>'+
            '<button type="submit" name="'+
            row.no+
            '" class="link-button">'+
            'İşlem Yap'+
            '</button></form>';
            }
        }
    ], 
    rowId: "no", <?php // use no for row ids ?>
<?php //---------------------------------------------------------------?>
<?php //------------------------General Settings-----------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
    select: {
          style: 'os',
          selector: 'td:nth-child(1)'
        },
    orderCellsTop: true,
    statesave: true,
    responsive: true,
    <?php //select: "true", ?>
    "oLanguage": {
      "sLengthMenu": "_MENU_ kayıt göster"
    },
    "aLengthMenu": [10, 50, 100,300],
    "pageLength": 50,
    autoWidth: true,
    <?php  //autoWidth: false,?>
    <?php //scrollY: true,?>
    <?php //scrollX: true,?>
    <?php    //dom: 'Bfltrip',?>
      dom: 'fl<"toolbar">trip',
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
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>

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
<?php //---------------------------------------------------------------?>
    columnDefs : [ 
            { "defaultContent": "-", "targets": "_all" },
        { "visible": false, "targets": [10]},
        <?php //{ "orderable": false, targets: [0, 8]},?>
        {
          "className": 'details-control',
          //"targets": 0,
          "targets": [0],
          'checkboxes': {
               'selectRow': true,
            }
        },
            {
          "className": 'details-dropdown',
          "targets": [1,3,4,5,6,7,8,9]
        },
          {targets: "_all", className: 'all'}, //always show column
        { type : 'Date', targets : [4]}, 
        { searchPanes: { show: true }, targets: [3,4,5] },
        { searchPanes: { show: false}, targets: [0,1,2,4,6,7,8,9,10,11] }
      ],
<?php //------------------------BUTTONS-----------------------?>
    buttons: [{ extend: 'copy',
      text: 'Kopyala',
      exportOptions: {
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
          }
      }
      },
      { extend: 'searchPanes', text: 'Filtrele', config: { cascadePanes: false, clear: false, collapse: false } },
        { extend: 'excel', 
        text: 'Seçilenleri İndir', 
        autoFilter: true,
        exportOptions: {
            trim: false,
          stripHtml: false,
          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ],
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
      { text: 'Açıklamaları Kapat',
        attr: { id: 'collapseButton' },
        className: 'close-child-rows',
        action: function ( e, dt, node, config ) {
          table.rows().every(function () {
              var row = this;
              if (row.child.isShown()) {
                row.child.hide();
                $(this.node()).removeClass('shown');
              }
          });
        }
      },
<?php /*{ 
            text: 'Assign',
            attr:  { id: 'selector' }, 
            action: function ( e, dt, node, config ) {
                    alert( 'Button activated' );
                }
      }*/
        //{ extend: 'selectAll', text: 'Tümünü Seç' },
    //{ text: 'Ata', action: sendData()}
    //'pdf',?>
    ],
<?php //------------------------BUTTONS END--------------------
    /*
    colResize: {
                    isEnabled: true,
                    hoverClass: 'dt-colresizable-hover',
                    hasBoundCheck: true,
                    minBoundClass: 'dt-colresizable-bound-min',
                    maxBoundClass: 'dt-colresizable-bound-max',
                    saveState: true,
                    //isResizable: function (column) {
                        //return column.idx !== 2;
                    //},
                    //onResize: function (column) {
                        //console.log('...resizing...');
                    //},
                    //onResizeEnd: function (column, columns) {
                        //console.log('I have been resized!');
                    //}
                },


    */
    //----------------colResize End---------------------?>

<?php //------------------------INITCOMPLETE BEGIN---------------------?>
<?php //------------------------INITCOMPLETE BEGIN---------------------?>
<?php //------------------------INITCOMPLETE BEGIN---------------------?>
    initComplete: function () {
      var api = this.api();
      count = 0;
            $myform = $('<form id ="myform" target="_blank" onsubmit="sendData()" action="/helpers/assign.php" method="post" style="margin-left:20px;margin-right:30px;display: inline;"></form>');
            var selector = document.createElement('select');
            selector.id = "selector";
      selector.name = "username";
            selector.setAttribute('class',"form-control");
            selector.style.marginRight = "10px";
      var option = document.createElement("option");
      option.value = "Atamayı Kaldır";
      option.text = "Atamayı Kaldır";
      selector.add(option);

      <?php while($rows = mysqli_fetch_array($result2)): ?>
        option = document.createElement("option");
        option.value = "<?php echo $rows['username'] ?>";
        option.text = "<?php echo $rows['username'] ?>";       
        selector.add(option);
      <?php endwhile; ?>
      
        $myform.append(selector);
          // create second selector        
            var selector = document.createElement('select');
            selector.id = "selector2";
            selector.name = "state";
            selector.setAttribute('class',"form-control");
            selector.style.marginRight = "10px";
            var option = document.createElement("option");
            option.value = "Varsayılan";
            option.text = "Varsayılan";
            selector.add(option);
            var option = document.createElement("option");
            option.value = "Bekliyor";
            option.text = "Bekliyor";
            selector.add(option);
            var option = document.createElement("option");
            option.value = "Onaylanmış";
            option.text = "Onaylanmış";
            selector.add(option);
            var option = document.createElement("option");
            option.value = "Reddedilmiş";
            option.text = "Reddedilmiş";
            selector.add(option);
            var option = document.createElement("option");
            option.value = "İşlenmiş";
            option.text = "İşlenmiş";
            selector.add(option);
            var option = document.createElement("option");
            option.value = "Concur";
            option.text = "Concur";
            selector.add(option);
            $myform.append(selector);
      var selector = document.createElement('select');
            selector.id = "selector3";
            selector.name = "comment";
            selector.setAttribute('class',"form-control");
            selector.style.marginRight = "10px";
            var option = document.createElement("option");
            option.value = "Yorumları Koru";
            option.text = "Yorumları Koru";
            selector.add(option);
            var option = document.createElement("option");
            option.value = "Yorumları Kaldır";
            option.text = "Yorumları Kaldır";
            selector.add(option);
            $myform.append(selector);

      this.api().columns().every( function () {
          if ( this.index() == 1 || this.index() == 3 || this.index() == 4 || this.index() == 7) {
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
        
        
        var x = document.createElement('button');
        x.innerHTML = "Atama Yap";
        x.setAttribute('class', "btn btn-primary");
        x.setAttribute('type', "submit" );
        x.onclick = function ()
    { 
      var table = $('#sorTable').DataTable();
      var cellTexts = [];
      var rows = table.rows( {selected: true} ).indexes();
      var data = table.cells(rows, 2).nodes().to$();
      $.each( data, function( key, value ) {
        cellTexts.push($(value).text());
      });
    
    if (cellTexts.length == 0) {
      alert("Lütfen fatura seçiniz!");
      return false;
    } else {
      var firstSelector = document.getElementById('selector').value;
      var secondSelector = document.getElementById('selector2').value;
      //var thirdSelector = document.getElementById('selector3').value; no need since already defined above

      var warningText = "Seçtiğiniz " + cellTexts.length + " Faturada Aşağıdaki İşlemler Yapılacak, EMİN MİSİNİZ?\n\n";

      if (firstSelector == "Atamayı Kaldır") {
        warningText = warningText + "Atamalar Kaldırılacak!\n";
      }
      else {
        warningText = warningText + firstSelector + " Kullanıcısına Atanacak!\n";
      }
      if (selector.value == "Yorumları Kaldır") {
        warningText = warningText + "Yorumlar Kaldırılacak!\n";
      }
    
      if (secondSelector != "Varsayılan") {
        warningText = warningText + "Durum " + secondSelector + " olarak değiştirilecek!\n";
      }

      warningText = warningText + "\n";

        for (var i = 0; i < cellTexts.length; i++) {
        warningText = warningText + "\n" + cellTexts[i];
      }
      return confirm(warningText);
    }
    };

        x.setAttribute('form', "myform");
        x.id = "submit-button";

        //var selector = document.getElementById('selector');

        //myform.append('<input onsubmit="sendData()">');

        $myform.append(x);
        $('div.toolbar').append($myform);
        $('div.toolbar').css("padding-bottom", "10px");
        
        //document.querySelector("div.toolbar").style.paddingBottom = "20px;"; not working
        api.buttons().container().appendTo($('div.toolbar'));
        //table.buttons().container().appendTo($('div.toolbar'));
    }
<?php //------------------------INITCOMPLETE END---------------------?>

  });
<?php //--------------DATATABLE DEFINITION END----------------?>
<?php //--------------DATATABLE DEFINITION END----------------?>
<?php //--------------DATATABLE DEFINITION END----------------?>
<?php //--------------DATATABLE DEFINITION END----------------?>
  function collapseAll ( e, dt, node, config ) {
              table.rows().every(function () {
                  var row = this;
                  if (row.child.isShown()) {
                    row.child.hide();
                    $(this.node()).removeClass('shown');
                  }
              });
            }

<?php //--------------NUMBER COLUMN----------------?>
    table.on('order.dt search.dt', function () {
        let i = 1;
 
        table
            .cells(null, 0, { search: 'applied', order: 'applied' })
            .every(function (cell) {
                this.data(i++);
            });
    })
    .draw();

<?php //--------------------HOVER FUNCTION-----------------------?>
    table.on("mouseenter", "td", function() {        
        if ($(this).hasClass('details-dropdown')) {
            $(this).attr('title', table.cell(table.row(this.closest('tr')).node()._DT_RowIndex, 10).data());
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

    /*table.on('draw', () => {
      openRows.forEach((id, i) => {
        let el = document.querySelector('#' + id + ' td.details-dropdown');
        //console.log(el.innerHTML);
        if (el) {
            console.log('#' + id + ' td.details-dropdown');
            el.dispatchEvent(new Event('click', { bubbles: true }));
        }
        else {
            console.log('#' + id + ' td.details-dropdown this failed');
        }
      });
    });*/
    
/*
    table.on('click', 'td.details-dropdown', function () {
      var tr = $(this).closest('tr');
      var row = table.row( tr );
      var rowIndex = row.node()._DT_RowIndex;

      if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
      }
      else if (row.child() && row.child().length) {
      // Open this row
        row.child.show();
      }
      else {          
        row.child( childRow(row.data(), rowIndex )).show();
        var collapseButtonClone = $('#collapseButton').clone();
        collapseButtonClone.on('click', collapseAll);
        collapseButtonClone.appendTo($('#td'+ rowIndex));
      }
          
      tr.addClass('shown');
      
      //row.child.append(document.getElementById("submit-button"));
      
      });*/

    //-------------------------------------------------------------------------------------

    /*table.on('submit', function(e){
      var form = this;

      var rows_selected = table.column(0).checkboxes.selected();

      // Iterate over all selected checkboxes
      $.each(rows_selected, function(index, rowId){
         // Create a hidden element
         $(form).append(
             $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'id[]')
                .val(rowId)
         );
      });
   });*/
<?php //-------------------DATE FILTER-----------------------------?>
   document.querySelectorAll('#min, #max').forEach((el) => {
    el.addEventListener('change', () => table.draw());
  });
});


<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>
<?php //------------DATATABLES ENDS HERE-------------------------------?>
<?php //---------------------------------------------------------------?>
<?php //---------------------------------------------------------------?>


function sendData() {
    
  var table = $('#sorTable').DataTable();
  var cellTexts = [];
  var rows = table.rows( {selected: true} ).indexes();
  var data = table.cells(rows, 2).nodes().to$();
  $.each( data, function( key, value ) {
    //cellTexts.push($(value).text().replace(/\t|\n|\r|:|\s/g,'')); old useless
    cellTexts.push($(value).text());
    });

  <?php //------------Remove Old Inputs-------------------------------?>
  $('#myform').find('input[name="array[ ]"]').remove();
    
  var frm = document.getElementById("myform");

  for (var i = 0; i < cellTexts.length; i++) {
  
      var inp = document.createElement("input");
      inp.setAttribute('type', "text");
      inp.setAttribute('name', "array[ ]");
      inp.setAttribute('value', cellTexts[i]);
      inp.style.display = 'none';
      inp.hidden = 'true';
      frm.appendChild(inp);
  }

  tableReload();
}

<?php /* Old Function
function sendOneRow(test,form) {
  var myTable = $('#sorTable').DataTable();
  var rowNumber = $(form).closest('tr').parents('tr').prev('tr')[0]._DT_RowIndex;
  var no = myTable.cell(rowNumber,2).data().replace(/<\/?[^>]+(>|$)/g, "");

  var inp = document.createElement("input");
    inp.setAttribute('type', "text");
    inp.setAttribute('name', "array[ ]");
    inp.setAttribute('value', no);
    inp.style.display = 'none';
    inp.hidden = 'true';
    form.appendChild(inp);
}*/?>

function sendOneRow(event, form) {
  var table = $('#sorTable').DataTable();
  var rowNumber = form.getAttribute("data-rowindex");
  var no = table.cell(rowNumber,2).data().replace(/<\/?[^>]+(>|$)/g, "");

  var inp = document.createElement("input");
    inp.setAttribute('type', "text");
    inp.setAttribute('name', "array[ ]");
    inp.setAttribute('value', no);
    inp.style.display = 'none';
    inp.hidden = 'true';
    form.appendChild(inp);

<?php /*  $.ajax({
      type: "POST",
      url: "/helpers/submit.php",
      data: $(form).serialize()
  });*/?>

  tableReload();
    //$('#sorTable').dataTable( ).api().ajax.reload();
}
<?php /* TUTAR PARA BIRIMI VE AÇIKLAMA
  function format (d) {

    return '<table cellpadding="5" cellspacing="0" border="0"  style="width:100%">'+
      '<tr>'+
      '<td style="width:5%">Tutar:</td>'+
      '<td style="width:10%">' + d[6] + ' ' + d[7] + '</td>'+
      '<td style="width:75%"></td>'+
      '</tr>'+
      '<tr>'+
      '<td>Açıklama:</td>'+
      '<td colspan="2">' + d[10] + '</td>'+
      '</tr>'+
      '</table>';
  } */?> 
   function childRow (d, rowIndex) {
    return '<table cellpadding="5" cellspacing="0" border="0"  style="width:100%;">'+
      '<tr>'+
      '<td colspan="2" style="border:0px;" id="td'+ rowIndex +'">'+
      '<form target="_blank" data-rowindex="'+ rowIndex +'" '+
      'onsubmit="sendOneRow(event,this)" action="/helpers/assign.php" method="post" style="display: inline;">'+
      '<select class="form-control" name = "username" style = "margin-right:30px;">' + $('#selector').html() + '</select>'+
      '<button class="btn btn-primary" type="submit" id="submit-button" style="margin-right: 30px;">Atama Yap</button>'+
      '</form>'+
      '</td>'+'</tr>'+
      '<tr>'+
      '<td style="width:20%">Açıklama:</td>'+
      '<td colspan="2"><b>' + d['description'] + '</b></td>'+
<?php //'<td colspan="2"><b>' + d[10] + '</b></td>'+ ?>
      '</tr>'+  
      '</table>';
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

async function tableReload () {
  var table = $('#sorTable').DataTable();
  await sleep(1500);
  table.rows().deselect();
<?php
  //table.ajax.reload( function () {
  //      if ( row.child.isShown() ) {
  //          // This row is already open - close it
  //          row.child.hide();
  //          tr.removeClass('shown');
  //      }
  //      else {         
  //          if (tr.hasClass('shown')) {
  //              // Open this row
  //              row.child( format(row.data()) ).show();
  //              tr.addClass('shown');                      
  //          }
  //      }
  //  }, false)
?>

  var column3 = table.column ( 3 );
  var column4 = table.column ( 4 );
  var colCompare3 = column3.data().unique().toArray();
  var colCompare4 = column4.data().unique().toArray();

  table.ajax.reload(null, false);
  table.responsive.recalc();
  await sleep(1500);
  selectReload("Atanan", column3, colCompare3);
  selectReload("Durum", column4, colCompare4);
  resetSelections();
}

function selectReload (title, column, compare) {
  var table = $('#sorTable').DataTable();
<?php           //replace spaces with dashes?>
  var select = $('#' + title);
  column.data().unique().sort().each( function ( d, j ) {
  if (!compare.includes(d)) {
    select.append( '<option value="'+d+'">'+d+'</option>' );
    }                
  });
}

function resetSelections () {
    $("#selector option:selected").prop("selected", false);
    $("#selector option:first").prop("selected", "selected");
    
    $("#selector2 option:selected").prop("selected", false);
    $("#selector2 option:first").prop("selected", "selected");
    
    $("#selector3 option:selected").prop("selected", false);
    $("#selector3 option:first").prop("selected", "selected");
}

$.fn.dataTable.ext.errMode = 'none';
</script>