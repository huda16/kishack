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

    <div class="modal fade text-left" id="modal-detail" tabindex="-1" role="dialog" aria-labelledby="modal-title"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Detail User</h4>
                    <button type="button" class="close mr-0 mt-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="p-3 mx-auto">
                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td>ID</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="category-id">1</td>
                            </tr>
                            <tr>
                                <td>Name</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="category-name">Super Admin</td>
                            </tr>
                            <tr>
                                <td>Log Info</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="category-created">01 August 2023</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
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

        $(document).ready(function() {
            const dt_ajax_table = $("#datatables-ajax");
            if (dt_ajax_table.length) {
                const dt_ajax = dt_ajax_table.dataTable({
                    processing: true,
                    dom: '<"d-flex justify-content-between align-items-end mx-0 row"<"col"l><"col-md-auto"f><"col col-lg-2 p-0."<"#create.btn btn-md btn-primary">>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    ajax: "{{ url('master-categories') }}",
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
                            data: 'name',
                            name: 'name'
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
            $("#create").html('Add a Category');
            $("#create").attr('style', 'margin-bottom: 7px');
            $("#create").attr('data-toggle', 'modal');
            $("#create").attr('data-target', '#modal-form');
        });

        $(document).on('click', '#create', function(event) {
            $('#modal-title').text('Create New Category');
            const idForm = $('form#form_edit_data').attr('id', 'form_data');
            const name = document.querySelector('#name');
            const btnSubmit = $('#btnEdit').attr('id', 'submit');
            name.value = '';
            submit();
        });

        $(document).on('click', '#edit', async function(event) {
            $('#modal-form').modal('show');
            const btnEdit = $('#submit').attr('id', 'btnEdit');
            const id = $(this).data('id');
            const name = document.querySelector('#name');
            const categoryId = document.querySelector('#id');
            $('#modal-title').text('Edit Category');
            const idForm = $('form#form_data').attr('id', 'form_edit_data');

            await fetch(`/master-categories/${id}/edit`)
                .then(response => response.json())
                .then(response => {
                    name.value = response.data.name;
                    categoryId.value = id;
                });
            submitEdit();
        });

        $(document).on('click', '#detail', async function(event) {
            $('#modal-detail').modal('show');
            const id = $(this).data('id');
            const userId = document.querySelector('#category-id');
            const username = document.querySelector('#category-name');
            const created = document.querySelector('#category-created');

            await fetch(`/master-categories/${id}/edit`)
                .then(response => response.json())
                .then(response => {
                    userId.innerHTML = response.data.id;
                    username.innerHTML = response.data.name;
                    created.innerHTML = formatDate(response.data.created_at);
                });
        });

        const submitEdit = () => {
            Array.prototype.filter.call($('#form_edit_data'), function(form) {
                $('#btnEdit').unbind().on('click', function(event) {
                    if (form.checkValidity() === false) {
                        form.classList.add('invalid');
                    }
                    form.classList.add('was-validated');
                    event.preventDefault();
                    const formEditData = document.querySelector('#form_edit_data');
                    if (formEditData) {
                        const request = new FormData(formEditData);

                        const data = {
                            _token: request.get('_token'),
                            name: request.get('name'),
                        };

                        const id = $('#id').val();

                        fetch(`/master-categories/${id}`, {
                                method: 'PUT',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(data),
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Success:', data);
                                if (data.error) {
                                    if (data.statusCode != 400) {
                                        throw data.error
                                    }

                                    $.each(data.message, (prefix, val) => {
                                        const resetForm = $('#form_edit_data')[0];
                                        $(resetForm).removeClass('was-validated');
                                        $(`#${prefix}`).addClass('is-invalid');
                                        $(`#${prefix}`).addClass('invalid-more');
                                        $(`.${prefix}_error`).text(val[0]);
                                    });
                                } else {
                                    setTimeout(function() {
                                        $('#datatables-ajax').DataTable().ajax.reload();
                                    }, 1000);

                                    Swal.fire({
                                        type: "success",
                                        title: 'Success!',
                                        text: data.message,
                                        confirmButtonClass: 'btn btn-success',
                                    });

                                    $('#name').removeClass(['is-invalid', 'invalid-more']);
                                    const reset_form = $('#form_edit_data')[0];
                                    $(reset_form).removeClass('was-validated');
                                    reset_form.reset();
                                    $('#modal-form').modal('hide');
                                }
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
                        submit();
                    }
                });
            });
        }

        const submit = () => {
            Array.prototype.filter.call($('#form_data'), function(form) {
                $('#submit').unbind().on('click', function(event) {
                    if (form.checkValidity() === false) {
                        form.classList.add('invalid');
                    }

                    form.classList.add('was-validated');
                    event.preventDefault();
                    const formData = document.querySelector('#form_data');
                    if (formData) {
                        const request = new FormData(formData);

                        const data = {
                            _token: request.get('_token'),
                            name: request.get('name'),
                        };

                        fetch('/master-categories', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(data),
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Success:', data.statusCode);
                                if (data.error) {
                                    if (data.statusCode != 400) {
                                        throw data.error
                                    }

                                    $.each(data.message, (prefix, val) => {
                                        const resetForm = $('#form_data')[0];
                                        $(resetForm).removeClass('was-validated');
                                        $(`#${prefix}`).addClass('is-invalid');
                                        $(`#${prefix}`).addClass('invalid-more');
                                        $(`.${prefix}_error`).text(val[0]);
                                    });
                                } else {
                                    setTimeout(function() {
                                        $('#datatables-ajax').DataTable().ajax.reload();
                                    }, 1000);

                                    Swal.fire({
                                        type: "success",
                                        title: 'Success!',
                                        text: data.message,
                                        confirmButtonClass: 'btn btn-success',
                                    });

                                    const reset_form = $('#form_data')[0];
                                    $(reset_form).removeClass('was-validated');
                                    reset_form.reset();
                                    $('#name').removeClass(['is-invalid', 'invalid-more']);
                                    $('#modal-form').modal('hide');
                                }
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
                        submitEdit();
                    }
                });
            });
        }

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

                    fetch(`/master-categories/${id}`, {
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
