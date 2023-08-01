@extends('layouts.template')
@section('main')
    <div class="content-wrapper">
        <div class="content-body">
            <section id="ajax-datatable" class="p-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card p-5">
                            <div class="card-datatable table-responsive table-rounded">
                                <table class="table" id="datatables-ajax">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th class="text-center">Actions</th>
                                            <th>Title Article</th>
                                            <th>Category Name</th>
                                            <th>Created Date</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <div class="modal fade text-left" id="modal-form" tabindex="-1" aria-labelledby="modal-title" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Create New Role</h4>
                    <button type="button" class="close mr-0 mt-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form_data" class="form-data-validate" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="id" value="">
                    <div class="modal-body">
                        <label>Category Name:</label>
                        <div class="form-group">
                            <input type="text" placeholder="Category Name" name="name" id="name" required
                                class="form-control" />
                            <div class="valid-feedback">Looks good!</div>
                            <div class="invalid-feedback name_error">Please enter your name.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('page_js')
    <script src="{{ url('plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ url('plugins/sweetalert2/sweetalert2.all.min.js') }}"></script>
@endsection

@section('page_script')
    <script>
        const formatDate = (inputDate) => {
            const months = [
                "January", "February", "March", "April", "May", "June", "July",
                "August", "September", "October", "November", "December"
            ];

            const dateObject = new Date(inputDate);
            const day = dateObject.getUTCDate().toString().padStart(2, "0");
            const month = months[dateObject.getUTCMonth()];
            const year = dateObject.getUTCFullYear();

            return `${day} ${month} ${year}`;
        };

        const redirectCreate = () => {
            window.location.href = "/master-articles/create";
        };

        $(document).ready(function() {
            const dt_ajax_table = $("#datatables-ajax");
            if (dt_ajax_table.length) {
                const dt_ajax = dt_ajax_table.dataTable({
                    processing: true,
                    dom: '<"d-flex justify-content-between align-items-end mx-0 row"<"col"l><"col-md-auto"f><"col col-lg-2 p-0."<"#create.btn btn-md btn-primary">>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    ajax: "{{ url('master-articles') }}",
                    language: {
                        paginate: {
                            // remove previous & next text from pagination
                            previous: '&nbsp;',
                            next: '&nbsp;'
                        },
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'category_name',
                            name: 'category_name'
                        },
                        {
                            data: (data) => {
                                const months = [
                                    "January", "February", "March", "April", "May", "June",
                                    "July",
                                    "August", "September", "October", "November", "December"
                                ];

                                const dateObject = new Date(data.created_at);
                                const day = dateObject.getUTCDate().toString().padStart(2, "0");
                                const month = months[dateObject.getUTCMonth()];
                                const year = dateObject.getUTCFullYear();

                                return `${day} ${month} ${year}`;
                            },
                            name: 'created_at'
                        },
                    ],
                    columnDefs: [{
                        targets: 1,
                        className: 'text-center',
                    }]
                });
            }
            $("#create").html('Add New');
            $("#create").attr('style', 'margin-bottom: 7px');
            $("#create").attr('onClick', 'redirectCreate()');
        });

        const sweetConfirm = (id) => {
            event.preventDefault(); // prevent form submit
            const form = event.target.form; // storing the form
            Swal.fire({
                title: "Are you sure?",
                text: "But you will still be able to retrieve this file.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, archive it!",
                cancelButtonText: "No, cancel please!",
                closeOnConfirm: false,
                closeOnCancel: false
            }).then((result) => {
                if (result.value) {
                    const request = new FormData(document.getElementById('form_delete_data'));
                    const data = {
                        _token: request.get('_token'),
                    };

                    fetch(`/master-articles/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(data),
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('Success:', data);
                            setTimeout(function() {
                                $('#datatables-ajax').DataTable().ajax.reload();
                            }, 1000);

                            Swal.fire({
                                type: "success",
                                title: 'Success!',
                                text: data.message,
                                confirmButtonClass: 'btn btn-success',
                            });

                            const reset_form = $('#form_delete_data')[0];
                            $(reset_form).removeClass('was-validated');
                            reset_form.reset();
                            $('#modal-form').modal('hide');
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            Swal.fire({
                                type: "error",
                                title: 'Oops...',
                                text: error,
                                confirmButtonClass: 'btn btn-success',
                            });
                        });
                } else {
                    Swal.fire("Cancelled", "Your imaginary file is safe :)", "error");
                }
            });
        };
    </script>
@endsection
