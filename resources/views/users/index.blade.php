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
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Role</th>
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
                                <td class="font-weight-bold" id="user-id">1</td>
                            </tr>
                            <tr>
                                <td>Username</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="user-name">Super Admin</td>
                            </tr>
                            <tr>
                                <td>Email</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="user-email">superadmin@gmail.com</td>
                            </tr>
                            <tr>
                                <td>Role</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="user-role">Super Admin</td>
                            </tr>
                            <tr>
                                <td>Created At</td>
                                <td class="font-weight-bold">:</td>
                                <td class="font-weight-bold" id="user-created">01 August 2023</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade text-left" id="modal-form" tabindex="-1" role="dialog" aria-labelledby="modal-title"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal-title">Create New User</h4>
                    <button type="button" class="close mr-0 mt-0" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="form_data" class="form-data-validate" novalidate>
                    @csrf
                    <input type="hidden" name="id" id="id" value="">
                    <input type="hidden" id="select_blank" value="--- Select ---" />
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username:</label>
                            <input type="text" placeholder="Username" name="username" id="username" required
                                class="form-control" />
                            <div class="invalid-feedback username_error">Please enter username.</div>
                        </div>
                        <div class="form-group">
                            <label>Email:</label>
                            <input type="email" placeholder="Email" name="email" id="email" required
                                class="form-control" />
                            <div class="invalid-feedback email_error">Please enter user email.</div>
                        </div>
                        <div class="form-group">
                            <label>Password:</label>
                            <input type="password" placeholder="Password" name="password" id="password" required
                                class="form-control" />
                            <div class="invalid-feedback password_error">Please enter user password.</div>
                        </div>
                        <div class="form-group">
                            <label for="role_id">Role</label>
                            <select class="select2 form-control" id="role_id" name="role_id" required>
                                <option value="">--- Select ---</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback role_id_error"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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
        $(document).ready(function() {
            const dt_ajax_table = $("#datatables-ajax");
            if (dt_ajax_table.length) {
                const dt_ajax = dt_ajax_table.dataTable({
                    processing: true,
                    dom: '<"d-flex justify-content-between align-items-end mx-0 row"<"col"l><"col-md-auto"f><"col col-lg-2 p-0."<"#create.btn btn-md btn-primary">>>t<"d-flex justify-content-between mx-0 row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
                    ajax: "{{ url('users') }}",
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
                            data: 'username',
                            name: 'username'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'role_name',
                            name: 'role_name'
                        },
                    ],
                    columnDefs: [{
                        targets: 1,
                        className: 'text-center',
                    }]
                });
            }
            $("#create").html('Add a User');
            $("#create").attr('style', 'margin-bottom: 7px');
            $("#create").attr('data-toggle', 'modal');
            $("#create").attr('data-target', '#modal-form');
        });

        $(document).ready(function() {
            $('.select2').select2();
        })


        $(document).on('click', '#create', function(event) {
            $('#modal-title').text('Create New User');
            const idForm = $('form#form_edit_data').attr('id', 'form_data');
            const username = document.querySelector('#username');
            const email = document.querySelector('#email');
            const password = document.querySelector('#password');
            const role = document.querySelector('#role_id');
            const btnSubmit = $('#btnEdit').attr('id', 'submit');

            username.value = '';
            email.value = '';
            password.value = '';
            role.value = '';
            btnSubmit.checked = false;

            submit();
        });

        $(document).on('click', '#edit', async function(event) {
            $('#modal-form').modal('show');
            const btnEdit = $('#submit').attr('id', 'btnEdit');
            const id = $(this).data('id');
            const username = document.querySelector('#username');
            const email = document.querySelector('#email');
            const password = document.querySelector('#password');
            const role = document.querySelector('#role_id');
            const userId = document.querySelector('#id');

            $('#modal-title').text('Edit Post');
            const idForm = $('form#form_data').attr('id', 'form_edit_data');

            await fetch(`/users/${id}/edit`)
                .then(response => response.json())
                .then(response => {
                    userId.value = id;
                    username.value = response.data.username;
                    email.value = response.data.email;
                    password.value = '';
                    role.value = response.data.role_id;
                });
            submitEdit();
        });

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

        $(document).on('click', '#detail', async function(event) {
            $('#modal-detail').modal('show');
            const id = $(this).data('id');
            const userId = document.querySelector('#user-id');
            const username = document.querySelector('#user-name');
            const email = document.querySelector('#user-email');
            const role = document.querySelector('#user-role');
            const created = document.querySelector('#user-created');

            await fetch(`/users/${id}/edit`)
                .then(response => response.json())
                .then(response => {
                    userId.innerHTML = response.data.id;
                    username.innerHTML = response.data.username;
                    email.innerHTML = response.data.email;
                    role.innerHTML = response.data.role_name;
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
                        const form = new FormData();
                        form.append('_token', request.get('_token'));
                        form.append('username', request.get('username'));
                        form.append('email', request.get('email'));
                        form.append('password', request.get('password'));
                        form.append('role_id', request.get('role_id'));

                        const id = $('#id').val();
                        console.log(id)

                        fetch(`/users/${id}?_method=PUT`, {
                                method: 'POST',
                                headers: {
                                    // 'Content-Type': 'application/json',
                                },
                                body: form,
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Success:', data);
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

                                    $('#username, #email, #password, #role_id').removeClass([
                                        'is-invalid', 'invalid-more'
                                    ]);
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
        };

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
                        const form = new FormData();
                        form.append('_token', request.get('_token'));
                        form.append('username', request.get('username'));
                        form.append('email', request.get('email'));
                        form.append('password', request.get('password'));
                        form.append('role_id', request.get('role_id'));

                        fetch('/users', {
                                method: 'POST',
                                headers: {
                                    // 'Content-Type': 'multipart/form-data',
                                },
                                body: form,
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
                                    $('#username, #email, #password, #role_id').removeClass([
                                        'is-invalid', 'invalid-more'
                                    ]);
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
        };

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

                    fetch(`/users/${id}`, {
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
