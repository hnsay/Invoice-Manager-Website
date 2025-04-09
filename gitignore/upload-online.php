
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Data Table</title>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/datetime/1.5.1/js/dataTables.dateTime.min.js"></script>
    <script src="https://momentjs.com/downloads/moment.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/searchpanes/2.2.0/js/dataTables.searchPanes.min.js"></script>
    <script src="https://cdn.datatables.net/select/1.7.0/js/dataTables.select.min.js"></script>
    <script src="https://gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/awesome-bootstrap-checkbox/1.0.4/awesome-bootstrap-checkbox.css"> 
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
  body{ font: 12px sans-serif; text-align: center; }
div.dtsp-title, div.dtsp-topRow {
  display: none;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th {
  background: #3F2765;
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
  vertical-align: middle;
}
.dataTable:focus {
  outline: 0 !important;
}




.toolbar {
  float: left;
}
</style>
</head>


<body>

<div style="padding-left: 50px;padding-right: 50px;margin-top: 50px;">
<table width="100%" class="table responsive" id="example" tabindex="0">
    <thead>
      <tr>
        <th data-priority="1" scope="col" style="width: 1%;"></th>
      <th data-priority="2" scope="col" style="width: 10%;">Supplier</th>
      <th data-priority="3" scope="col" style="color: #ffffff;" style="width: 10%;">Invoice No</th>
        <th data-priority="4" scope="col" style="width: 1%;">Assignee</th>
      <th data-priority="5" scope="col" style="width: 1%;">State</th>
      <th data-priority="6" scope="col" style="width: 6%;">
      <input type="text" id="min" name="min" style="margin-bottom: 5px;" value="Begin">
      <input type="text" id="max" name="max" value="End">
      </th>
      <th style="color: #ffffff;" scope="col" style="width: 1%;">Amount</th>
        <th scope="col" style="width: 1%;">Currency</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;">Comment</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;">Order No</th>
        <th style="color: #ffffff;" scope="col" style="width: 1%;">Details</th>
  </tr>


  </thead>

  <tbody>
    <tr>
      <td data-table-header="#"></td>
      <td data-table-header="Supplier"></td>
      <td data-table-header="Invoice No"></td>
      <td data-table-header="Assignee"></td>
      <td data-table-header="State"></td>
      <td data-table-header="Date"></td>
      <td data-table-header="Amount"></td>
      <td data-table-header="Comment"></td>
      <td data-table-header="Yorum"></td>
      <td data-table-header="Order No"></td>
      <td data-table-header="Details"></td>
    </tr>
  </tbody>
    
</table>
</div>

<script>
  const random_json = [
  {
    "supplier": "6583302eec5a31bd475a3cc6",
    "no": 0,
    "assignee": "e77c00d3-0521-4ef1-8530-1c19f2607dce",
    "state": true,
    "date": "2020-08-07T03:18:26 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 29,
    "comment": "brown",
    "order_no": "Jacobs Rosales",
    "details": "male"
  },
  {
    "supplier": "6583302e80e0cd2436e1f31b",
    "no": 1,
    "assignee": "2adf6fe8-82e2-42aa-9dcc-0ba9da9bb6be",
    "state": true,
    "date": "2021-05-26T10:19:14 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 30,
    "comment": "green",
    "order_no": "Fields Duncan",
    "details": "male"
  },
  {
    "supplier": "6583302e44e96a4ec7805a1f",
    "no": 2,
    "assignee": "6ca010d7-99e8-458e-8301-b557ed52cae1",
    "state": true,
    "date": "2018-08-10T04:40:03 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 24,
    "comment": "green",
    "order_no": "Stanley Hoffman",
    "details": "male"
  },
  {
    "supplier": "6583302eb1e17e4ba0f1a7a3",
    "no": 3,
    "assignee": "12126f1d-09eb-4099-ace6-b99a31cc939d",
    "state": false,
    "date": "2023-05-08T12:18:25 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 30,
    "comment": "blue",
    "order_no": "Alberta Everett",
    "details": "female"
  },
  {
    "supplier": "6583302e5b9bd62406c0a40d",
    "no": 4,
    "assignee": "09eaff9c-ede4-4535-b9f4-00c7a8f43009",
    "state": true,
    "date": "2021-06-29T10:14:18 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 38,
    "comment": "green",
    "order_no": "Lindsey Flores",
    "details": "female"
  },
  {
    "supplier": "6583302ee0d2519248921761",
    "no": 5,
    "assignee": "3caced8c-1ff3-403a-b961-3f8bbd848118",
    "state": true,
    "date": "2017-03-24T08:58:45 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 21,
    "comment": "brown",
    "order_no": "Robin Mcbride",
    "details": "female"
  },
  {
    "supplier": "6583302e628d4e6e8a481210",
    "no": 6,
    "assignee": "034e088b-2c71-44e2-a17d-7fb9ef1e70cc",
    "state": false,
    "date": "2019-11-30T09:21:08 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 30,
    "comment": "blue",
    "order_no": "Araceli Pena",
    "details": "female"
  },
  {
    "supplier": "6583302e3ef09cf023db5021",
    "no": 7,
    "assignee": "9932cd4b-7d28-46df-a1d4-b4fb57643ab7",
    "state": true,
    "date": "2015-05-25T04:15:02 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 35,
    "comment": "green",
    "order_no": "Celia Conway",
    "details": "female"
  },
  {
    "supplier": "6583302e556d911c0ef1b094",
    "no": 8,
    "assignee": "2f2160f4-6714-44bd-bb3f-beae763dde7c",
    "state": true,
    "date": "2017-05-11T11:51:29 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 22,
    "comment": "blue",
    "order_no": "Bertie Orr",
    "details": "female"
  },
  {
    "supplier": "6583302e1eeee043c88c6798",
    "no": 9,
    "assignee": "69cafac9-0a77-42e2-a32e-601f1574543c",
    "state": false,
    "date": "2017-05-16T12:54:56 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 34,
    "comment": "blue",
    "order_no": "Ebony Marks",
    "details": "female"
  },
  {
    "supplier": "6583302eb0b89d257ea0156c",
    "no": 10,
    "assignee": "9663036b-7b1b-40ee-9f57-286effc58826",
    "state": false,
    "date": "2023-11-06T10:30:13 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 29,
    "comment": "green",
    "order_no": "Hernandez Walker",
    "details": "male"
  },
  {
    "supplier": "6583302eafcab63f9350995b",
    "no": 11,
    "assignee": "d8ced5e0-77c9-4468-80ec-8e6bffa2fbe1",
    "state": false,
    "date": "2020-05-31T09:02:46 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 30,
    "comment": "blue",
    "order_no": "Verna Sexton",
    "details": "female"
  },
  {
    "supplier": "6583302e5b1d2bcfde2735a6",
    "no": 12,
    "assignee": "669daf9b-e96b-475e-886a-31a7d0242871",
    "state": false,
    "date": "2017-05-29T04:44:33 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 20,
    "comment": "green",
    "order_no": "Leanne Schultz",
    "details": "female"
  },
  {
    "supplier": "6583302e32257fb95fdcd974",
    "no": 13,
    "assignee": "65e6d847-31be-4320-b765-a018b7952711",
    "state": true,
    "date": "2020-12-25T02:51:23 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 38,
    "comment": "green",
    "order_no": "Wilson Solomon",
    "details": "male"
  },
  {
    "supplier": "6583302e8dece73a88aee6c9",
    "no": 14,
    "assignee": "d4c73451-d9a7-4f08-b1b3-104cebeff077",
    "state": true,
    "date": "2017-06-15T02:33:22 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 21,
    "comment": "brown",
    "order_no": "Hines Bean",
    "details": "male"
  },
  {
    "supplier": "6583302e1491ac7363b6daad",
    "no": 15,
    "assignee": "6d446e85-4b2c-4d29-b17e-80d36bf1a7ef",
    "state": true,
    "date": "2018-10-13T01:59:50 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 29,
    "comment": "blue",
    "order_no": "Stone James",
    "details": "male"
  },
  {
    "supplier": "6583302edac195a1b2b40f61",
    "no": 16,
    "assignee": "c086378f-f37c-4574-9853-f8ff425dd00a",
    "state": false,
    "date": "2018-05-06T04:41:33 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 40,
    "comment": "blue",
    "order_no": "Sargent Conley",
    "details": "male"
  },
  {
    "supplier": "6583302e8b3c7ca2edb08024",
    "no": 17,
    "assignee": "7f1ed76b-34f9-45b3-bd01-c7023d7c0775",
    "state": true,
    "date": "2016-01-13T11:42:26 -02:00",
    "amount": "http://placehold.it/32x32",
    "currency": 35,
    "comment": "green",
    "order_no": "Jimmie Irwin",
    "details": "female"
  },
  {
    "supplier": "6583302eec80749ed4f176a0",
    "no": 18,
    "assignee": "6d5a31fa-fceb-4a0f-9f3c-3f1b2d1eca27",
    "state": true,
    "date": "2015-02-14T07:16:12 -02:00",
    "amount": "http://placehold.it/32x32",
    "currency": 20,
    "comment": "blue",
    "order_no": "Mcgee Hendricks",
    "details": "male"
  },
  {
    "supplier": "6583302eab7e6c473a2da4d3",
    "no": 19,
    "assignee": "a3de2948-ccf7-41f8-ad1a-26ab7ba962a9",
    "state": false,
    "date": "2020-02-09T07:08:44 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 40,
    "comment": "blue",
    "order_no": "Zelma Armstrong",
    "details": "female"
  },
  {
    "supplier": "6583302e3c74873522043251",
    "no": 20,
    "assignee": "e859fc2b-447e-4e7a-9477-442b88b4b7da",
    "state": true,
    "date": "2020-03-05T11:34:08 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 39,
    "comment": "brown",
    "order_no": "Tammy Oneill",
    "details": "female"
  },
  {
    "supplier": "6583302e40ff88466a667ebf",
    "no": 21,
    "assignee": "def40a5b-ab8c-4c17-8c43-19b5f0dbe103",
    "state": false,
    "date": "2019-03-18T01:31:15 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 20,
    "comment": "blue",
    "order_no": "Eunice Rivera",
    "details": "female"
  },
  {
    "supplier": "6583302e36cee6436f90484e",
    "no": 22,
    "assignee": "4dbb16db-03bc-4b06-96a5-de18654c60bd",
    "state": true,
    "date": "2015-09-06T06:02:24 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 33,
    "comment": "blue",
    "order_no": "Ward Cole",
    "details": "male"
  },
  {
    "supplier": "6583302e83e4e20168932703",
    "no": 23,
    "assignee": "309b546a-869c-4eb9-8bfd-ef28ae70a6ab",
    "state": true,
    "date": "2018-07-14T08:48:55 -03:00",
    "amount": "http://placehold.it/32x32",
    "currency": 36,
    "comment": "brown",
    "order_no": "Sloan Sutton",
    "details": "male"
  }
];





const openRows = [];


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
});
maxDate = new DateTime('#max', {
    format: 'DD/MM/YYYY'
});




$(document).ready( function () {
  var table = $('#example').DataTable({
    data:  random_json,          
    columns: [
        {},
        { data: 'supplier' },
        { data: 'no',
          render: function ( data, type, row, meta ) {
            return '<a href="anotherpage.php?'+data+'=">'+data+'</a>';
            }
        },
        { data: 'assignee' },
        { data: 'state' },
        { data: 'date' },
        { data: 'amount' },
        { data: 'currency' },
        { data: 'comment' },
        { data: 'order_no' },
        { data: 'details' }
    ],


    rowId: "no",

    orderCellsTop: true,
    statesave: true,
    
      dom: 'fl<"toolbar">trip',
        select: {
          style: 'multi'
        },
    responsive: true,
    columnDefs : [
      { defaultContent: "-", targets: "_all"},
        { "visible": false, "targets": [10]},
        { "orderable": false, targets: [0, 8]},
        {
          "className": 'details-control',
          "targets": 0,
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
        { searchPanes: { show: false}, targets: [0,1,2,4,6,7,8,9,10] }
      ],
    buttons: [{ extend: 'copy',
        exportOptions: {
          format: {
            header: function ( html, colIdx ) { //rowIdx
              if ( colIdx == 1 ) {
                return "Supplier";
              }
              if ( colIdx == 3 ) {
                return "Assignee";
              }
              if ( colIdx == 4 ) {
                return "State";
              }
              if ( colIdx == 5 ) {
                return "Date";
              }
              if ( colIdx == 7 ) {
                return "Currency";
              }
              return html;
            },
            body: function ( data, row, column, node ) {
              return column == 1 ? data.replace(/<\/?[^>]+(>|$)/g, "") : data;
            }
          }
        }
      },
      { extend: 'searchPanes', config: { cascadePanes: false, clear: false, collapse: false } },
        { extend: 'excel',
        autoFilter: true,
        exportOptions: {
            trim: false,
          stripHtml: false,
          columns: [ 1, 2, 3, 4, 5, 6, 7, 8, 9, 10 ],
            format: {
              header: function ( html, colIdx ) { //rowIdx
                if ( colIdx == 1 ) {
                  return "Supplier";
                }
                if ( colIdx == 3 ) {
                  return "Assignee";
                }
                if ( colIdx == 4 ) {
                  return "State";
                }
                if ( colIdx == 5 ) {
                  return "Date";
                }
                if ( colIdx == 7 ) {
                  return "Currency";
                }
                return html;
              },
              body: function ( data, row, column, node ) {
                return column == 1 ? data.replace(/<\/?[^>]+(>|$)/g, "") : data;
              }
            }
        },
      },      
      { text: 'Collapse All',
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
    ],
    aLengthMenu: [10, 50, 100,300],
    pageLength: 50,
    initComplete: function () {
      var api = this.api();
      count = 0;
            $myform = $('<form id ="myform" target="_blank" onsubmit="sendData()" action="somepage.php" method="post" style="margin-left:20px;margin-right:30px;display: inline;"></form>');
            var selector = document.createElement('select');
            selector.id = "selector";
      selector.name = "username";
            selector.setAttribute('class',"form-control");
            selector.style.marginRight = "10px";
      var option = document.createElement("option");
      option.value = "Unassign";
      option.text = "Unassign";
      selector.add(option);

        option = document.createElement("option");
        option.value = "person1";
        option.text = "person1";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person2";
        option.text = "person2";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person3";
        option.text = "person3";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person4";
        option.text = "person4";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person5";
        option.text = "person5";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person6";
        option.text = "person6";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person7";
        option.text = "person7";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person8";
        option.text = "person8";       
        selector.add(option);
        option = document.createElement("option");
        option.value = "person9";
        option.text = "person9";       
        selector.add(option);
      
      this.api().columns().every( function () {
          if ( this.index() == 1 || this.index() == 3 || this.index() == 4 || this.index() == 7) {
                var title = this.header();
                title = $(title).html();
                var column = this;
                var select = $('<select id="' + title + '" class="select2" ></select>')
                    .appendTo( $(column.header()).empty() )
                    .on( 'change', function () {
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
                    
                    
                    $myform.append(selector);        
              
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
        x.innerHTML = "Assign";
        x.setAttribute('class', "btn btn-primary");
        x.setAttribute('type', "submit" );
        x.onclick = function () { confirm('Assign?'); };
        x.setAttribute('form', "myform");
        x.id = "submit-button";

        //var selector = document.getElementById('selector');

        //myform.append('<input onsubmit="sendData()">');

        $myform.append(x);
        $('div.toolbar').append($myform);
        $('div.toolbar').css("padding-bottom", "10px");
        
        //document.querySelector("div.toolbar").style.paddingBottom = "20px;"; not working
        api.buttons().container().appendTo($('div.toolbar'));
        }
  });
  function collapseAll ( e, dt, node, config ) {
              table.rows().every(function () {
                  var row = this;
                  if (row.child.isShown()) {
                    row.child.hide();
                    $(this.node()).removeClass('shown');
                  }
              });
            }

    table.on('order.dt search.dt', function () {
        let i = 1;
 
        table
            .cells(null, 0, { search: 'applied', order: 'applied' })
            .every(function (cell) {
                this.data(i++);
            });
    })
    .draw();

    table.on("mouseenter", "td", function() {        
        if ($(this).hasClass('details-dropdown')) {
            $(this).attr('title', table.cell(table.row(this.closest('tr')).node()._DT_RowIndex, 10).data());
        }        
    });

    table.on('click', 'td.details-dropdown', function () {
    let tr = event.target.closest('tr');
    let row = table.row(tr);
    var rowIndex = row.node()._DT_RowIndex;
    var idx = openRows.indexOf(tr.id);
 
    if (row.child.isShown()) {
        tr.classList.remove('shown');
        row.child.hide();
 
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

    table.on('draw', () => {
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
    });
    
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
   document.querySelectorAll('#min, #max').forEach((el) => {
    el.addEventListener('change', () => table.draw());
  });
});




function sendData() {
  tableReload();
}


function sendOneRow(test,form) {
  tableReload();
}
 
   function childRow (d, rowIndex) {
    return '<table cellpadding="5" cellspacing="0" border="0"  style="width:100%;">'+
      '<tr>'+
      '<td colspan="2" style="border:0px;" id="td'+ rowIndex +'">'+
      '<form target="_blank" data-rowindex="'+ rowIndex +'" '+
      'onsubmit="sendOneRow(event,this)" action="somepage.php" method="post" style="display: inline;">'+
      '<select class="form-control" name = "username" style = "margin-right:30px;">' + $('#selector').html() + '</select>'+
      '<button class="btn btn-primary" type="submit" id="submit-button" style="margin-right: 30px;">Assign</button>'+
      '</form>'+
      '</td>'+'</tr>'+
      '<tr>'+
      '<td style="width:20%">Details:</td>'+
      '<td colspan="2"><b>' + d['details'] + '</b></td>'+
      '</tr>'+  
      '</table>';
  }
  
  $(function(){
  $('.example').keydown(function (e) {
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
  var table = $('#example').DataTable();

  await sleep(1500);

  table.ajax.reload(null, false);
}

(function() {
    var url = 'https://debug.datatables.net/bookmarklet/DT_Debug.js';
    if (typeof DT_Debug != 'undefined') {
        if (DT_Debug.instance !== null) {
            DT_Debug.close();
        } else {
            new DT_Debug();
        }
    } else {
        var n = document.createElement('script');
        n.setAttribute('language', 'JavaScript');
        n.setAttribute('src', url + '?rand=' + new Date().getTime());
        document.body.appendChild(n);
    }
})();
</script>
</body>
</html>
