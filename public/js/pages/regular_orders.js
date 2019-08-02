function filter_admin_orders_table() {
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_filtered_admin_orders_table',
        type: 'POST',
        data: {from_date: from_date, to_date: to_date},
        dataType: 'JSON',
        success: function ($filtered_orders_table) {
            const table_body = $("#orders_panel table.table tbody");
            table_body.empty();
            var tr_class = '';
            $.each($filtered_orders_table, function(i, row) {
                tr_class = i%2 === 0?'odd':'even';
                table_body.append(
                    "<tr  role='row' class='"+tr_class+"'>" +
                    "<td>" + (i+1) + "</td>" +

                    "<td style='display: none;' class='sorting_1'>" + row['id'] + "</td>" +
                    "<td style='display: none;'>" + row['status_hidden'] + "</td>" +
                    "<td style='text-align:center;'>" + (row['user'] == null?'':row['user']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['customer_name'] == null?'':row['customer_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['mobile'] == null?'':row['mobile']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['operator'] == null?'':row['operator']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['packet_name'] == null?'':row['packet_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['packet_type'] == null?'':row['packet_type']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['purchasing_price'] == null?'':row['purchasing_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['selling_price'] == null?'':row['selling_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['profit'] == null?'':row['profit']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['status'] == null?'':row['status']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['response_date'] == null?'':row['response_date']) + "</td>" +

                    "</tr>"
                );
            });
        }
    });
}

function filter_regular_orders_table() {
    var from_date = $('#from_date').val();
    var to_date = $('#to_date').val();
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/get_filtered_regular_orders_table',
        type: 'POST',
        data: {from_date: from_date, to_date: to_date},
        dataType: 'JSON',
        success: function ($filtered_orders_table) {
            const table_body = $("#orders_panel table.table tbody");
            table_body.empty();
            var tr_class = '';
            $.each($filtered_orders_table, function(i, row) {
                tr_class = i%2 === 0?'odd':'even';
                table_body.append(
                    "<tr  role='row' class='"+tr_class+"'>" +
                    "<td>" + (i+1) + "</td>" +

                    "<td style='display: none;' class='sorting_1'>" + row['id'] + "</td>" +
                    "<td style='display: none;'>" + row['status_hidden'] + "</td>" +
                    "<td style='text-align:center;'>" + (row['customer_name'] == null?'':row['customer_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['mobile'] == null?'':row['mobile']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['operator'] == null?'':row['operator']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['packet_name'] == null?'':row['packet_name']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['packet_type'] == null?'':row['packet_type']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['purchasing_price'] == null?'':row['purchasing_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['selling_price'] == null?'':row['selling_price']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['profit'] == null?'':row['profit']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['status'] == null?'':row['status']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['request_date'] == null?'':row['request_date']) + "</td>" +
                    "<td style='text-align:center;'>" + (row['response_date'] == null?'':row['response_date']) + "</td>" +

                    "</tr>"
                );
            });
        }
    });
}