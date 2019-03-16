$(document).ready(function(){
    var table = tableInit();

    highlightingColsRows(table);

    indexFormat(table);

    formatCheckGroup();
    selectHiddenCols(table);

    bootstrapFormat();

    if(!$('#is_guest').val())
        rowSelection(table);    
});

//------------------------------------Functions--------------------------------//

//DataTable init
function tableInit(){
    var buttonsNum = $('table.table').data('btns-num');
    var colsNum = $('table.table').data('cols-num');
    var table = $('table.table').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                if (column[0][0] != 0 && column[0][0] != 1 && (column[0][0] < colsNum  || !buttonsNum)){
                    var select = $('<select class="custom-select"><option value="">' + LANGS['DATA_TABLE']['noFilter'] + '</option></select>')
                        .appendTo( $(column.footer()).empty() )
                        .on( 'change', function () {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
    
                            column
                                .search( val ? '^'+val+'$' : '', true, false )
                                .draw();
                        } );
                
                        column.data().unique().sort().each( function ( d, j ) {
                            select.append( '<option value="'+d+'">'+d+'</option>'  )
                        } );
                }
            } );
            $(this.api().table().body()).css({
                'border-radius': '25px'
                });
        },
        "scrollX": true,
        "language": {
            "decimal": LANGS['DATA_TABLE']['decimal'],
            "thousands": LANGS['DATA_TABLE']['thousands'],
            "lengthMenu": LANGS['DATA_TABLE']['lengthMenu'] ,
            "zeroRecords": LANGS['DATA_TABLE']['zeroRecords'],
            "info": LANGS['DATA_TABLE']['info'],
            "infoEmpty": LANGS['DATA_TABLE']['infoEmpty'],
            "infoFiltered": LANGS['DATA_TABLE']['infoFiltered'],
            'search': LANGS['DATA_TABLE']['search'],
            'paginate': {
                'next': LANGS['DATA_TABLE']['next'],
                'previous' : LANGS['DATA_TABLE']['previous']
              }
        },
        "columnDefs": [ {
            "searchable": false,
            "orderable": false,
            "targets": 0
        } ],
        "order": [[ 1, 'asc' ]]
    } );
    return table;
}


//Highlighting rows and columns
function highlightingColsRows(table){
    $('table.table tbody').on( 'mouseenter', 'td', function () {
        var colIdx = table.cell(this).index().column;

        $( table.cells().nodes() ).removeClass( 'highlight' );
        $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
    } );
}

//Add index column
function indexFormat(table){
    table.on( 'order.dt search.dt', function () {
        table.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        } );
    } ).draw();
}

//Hide & Show columns
function selectHiddenCols(table){
    $('a.toggle-vis').on( 'click', function (e) {
        e.preventDefault();
        var column = table.column( $(this).attr('data-value') );
        column.visible( ! column.visible() );
    } );
}
function formatCheckGroup(){
    var options = [];
    $( '#showHideCols .dropdown-menu a' ).on( 'click', function( event ) {
        var $target = $( event.currentTarget ),
            val = $target.attr( 'data-value' ),
            $inp = $target.find( 'input' ),
            idx;

        if ( ( idx = options.indexOf( val ) ) > -1 ) {
            options.splice( idx, 1 );
            setTimeout( function() { $inp.prop( 'checked', false ) }, 0);
        } else {
            options.push( val );
            setTimeout( function() { $inp.prop( 'checked', true ) }, 0);
        }

        $( event.target ).blur();
        // console.log( options );
        return false;
    });

    //put select button side of search input
    $( $('#showHideCols')[0] ).insertAfter( "#example_filter input" ).removeClass('hide');
}

// Add Bootstrap to datatable's elements
function bootstrapFormat(){
    if(document.getElementsByName('example_length')[0] != null){
        document.getElementsByName('example_length')[0].classList.add('custom-select');
        $('#example_wrapper label').addClass('font-weight-bold');
        $("#example_filter [aria-controls=example]")[0].classList.add('form-control');
    }
}

// Select row in table
function rowSelection(table){
    $('table.table tbody').on( 'click', 'tr', function () {
        $('table.table tbody tr').removeClass('selected');
        $(this).toggleClass('selected');
    } );
}

// Select multiple rows in table
function rowMultiSelection(table){
    $('table.table tbody').on( 'click', 'tr', function () {
        $(this).toggleClass('selected');
    } );
    $('#btn_edit, #btn_del').on( 'click', function () {
        alert( table.rows('.selected').data().length +' row(s) selected' );
    } );
}