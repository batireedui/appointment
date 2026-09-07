<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        let table = $('#datalist').DataTable({
            sDom: "B<'row'><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-4'i>><'row'<'#colvis'>p>",
            buttons: [{
                    extend: 'copy',
                    className: 'btn btn-outline-primary',
                    text: '<span><i class="fa-solid fa-copy mx-1"></i>Хуулах</span>'
                },
                {
                    extend: 'excel',
                    className: 'btn btn-outline-success',
                    text: '<span><i class="fa-solid fa-file-excel mx-1"></i>Excel</span>'
                },
                {
                    extend: 'pdf',
                    className: 'btn btn-outline-danger',
                    text: '<span><i class="fa-solid fa-file-pdf mx-1"></i>Pdf</span>'
                },
                {
                    extend: 'print',
                    className: 'btn btn-outline-secondary',
                    text: '<span><i class="fa-solid fa-print mx-1"></i>Хэвлэх</span>',
                    exportOptions: {
                        columns: [<?php
                            for($i=0; $i <= $columnNumber-1; $i++)
                            {    
                                echo $i;
                                echo $i == $columnNumber-1 ? "" : ", ";
                            }
                        ?>]
                    }
                },
            ],
            columnDefs: [{
                "targets": 0,
                "searchable": false
            }],
            oLanguage: {
                "sLengthMenu": "Дэлгэцэнд _MENU_ бичлэгээр",
                "sSearch": "Хайлт хийх:",
                "oPaginate": {
                    "sNext": "Дараах",
                    "sPrevious": "Өмнөх"
                },
                "sInfo": "Нийт _TOTAL_ бүртгэл (_START_ / _END_)",
                "sInfoFiltered": " - хайлт хийсэн _MAX_ -н бүртгэл"
            },
            displayLength: 50,
            lengthMenu: [50, 100, 150, 200, 500, 1000],
        });
        let coln = table.columns().header().length;
        
        $('div.dataTables_filter input').addClass('form-control');
        $('div.dataTables_length select').addClass('form-control');

    });
</script>