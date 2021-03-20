@extends('layouts.master')
@section('content')
      <div class="container mt-5 content">
          <!-- student list -->
         <div class="row">
            <div class="col-lg-12 margin-tb">
               <div class="pull-left">
                    <h2><i class="fa fa-users" aria-hidden="true"></i>&nbsp;Students List</h2>
               </div>
               <div class="mb-3" style="float: right;">
                  <a class="btn btn-info" onClick="addStudent()" href="javascript:void(0)"><i class="fa fa-user" aria-hidden="true"></i>&nbsp;New Student</a>
                  &nbsp;<a href="{{url('scores')}}">Go to Score List</a>
               </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="list-student">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Reporting Teacher</th>
                        <th>Action</th>
                    </tr>
                </thead>
                </table>
            </div>
         </div>
      </div>

      <!-- boostrap student model -->
      <div class="modal fade" id="student-modal" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="StudentModal"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form action="javascript:void(0)" id="studentForm" name="studentForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                     <input type="hidden" name="id" id="id">
                     <div class="form-group">
                     <label for="name" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-12">
                           <input type="text" class="form-control" id="name" name="name"  maxlength="50" required="">
                        </div>
                     </div>
                     <div class="form-group">
                     <label for="age" class="col-sm-2 control-label">Age</label>
                        <div class="col-sm-12">
                           <input type="number" class="form-control" id="age" name="age" maxlength="2" required="">
                        </div>
                     </div>
                     <div class="form-group">
                        <label for="gender" class="col-sm-2 control-label">Gender</label>
                        <div class="col-sm-12">
                           <input type="radio" name="gender" id="Male" value="Male" checked/>&nbsp;Male
                           <input type="radio" name="gender" id="Female" value="Female" />&nbsp;Female
                        </div>
                     </div>
                     <div class="form-group">
                     <label for="teacher_id" class="col-sm-2 control-label">Teacher</label>
                        <div class="col-sm-12">
                           <select class="form-control" name="teacher_id" id="teacher_id">
                                <option value="">--select--</option>
                                @foreach($teachers as $ky => $teacher)
                                <option value="{{$ky}}">{{$teacher}}</option>
                                @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" class="btn btn-primary" id="btn-save">Save</button>
                        <button type="reset" class="btn btn-warning" id="btn-save">Reset</button>
                     </div>
                  </form>
               </div>
               <div class="modal-footer"></div>
            </div>
         </div>
      </div>
      <!-- end bootstrap model -->

   <script type="text/javascript">
   $(document).ready( function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function addStudent(){
        $('#studentForm').trigger("reset");
        $('#StudentModal').html("New Student");
        $('#student-modal').modal('show');
        $('#id').val('');
    }

    function editFunc(id){
        $.ajax({
            type:"POST",
            url: "{{ url('edit-student') }}",
            data: {'id':id},
            dataType: 'json',
            success: function(res){
                $('#StudentModal').html("Edit Student");
                $('#student-modal').modal('show');
                $('#id').val(res.id);
                $('#name').val(res.name);
                $('#age').val(res.age);
                $("#"+res.gender).prop("checked", true);
                $('#teacher_id').val(res.teacher_id);
            }
        });
    }

    function deleteFunc(id){
        var id = id;
        swal({
            title: "Are you sure?",
            text: "Your will not be able to undo this operation!",
            type: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            closeOnConfirm: false
        }).then(function() {
            $.ajax({
                type:"POST",
                url: "{{ url('delete-student') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){
                    if(res.success) {
                        swal('Success', 'Operation Successfully completed', 'success');
                        var oTable = $('#list-student').dataTable();
                        oTable.fnDraw(false);
                    }else{
                        swal('Error', res.message, 'error');
                    }
                }
            });
        }).catch(swal.noop);
    }

    $('#list-student').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('list-student') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'name', name: 'name' },
            { data: 'age', name: 'age' },
            { data: null, name: 'gender', render:function(o) {
                    return o.gender? ((o.gender == "Male") ?'M':'F'): '';
                },
                orderable: false
            },
            { data: null, name: 'teacher_id', render:function(o) {
                    return o.teacher? o.teacher.name: '';
                },
                orderable: false
            },
            { data: 'action', name: 'action', orderable: false },
        ],
        order: [[0, 'ASC']],
        'columnDefs': [
         {
               "targets": [0, 2],
               "className": "text-center",
         }],
        "bFilter": false,
    });

    function validateForm() {
       //validate input values
       if($('#name').val() == "" || $('#name').val() == undefined || $('#name').val() == null) {
         swal('Error', "Please enter a name", 'error');
         return false;
        }else if($('#age').val() == "" || $('#age').val() == undefined || $('#age').val() == null) {
         swal('Error', "Please enter age", 'error');
         return false;
        }else if($('#teacher_id').val() == "" || $('#teacher_id').val() == undefined || $('#teacher_id').val() == null) {
         swal('Error', "Please choose any teacher", 'error');
         return false;
        }else{
            return true;
        }
    }

    $('#studentForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if(validateForm()){
            swal({
                title: "Are you sure?",
                text: "Your want to save the student details",
                type: "success",
                showCancelButton: true,
                confirmButtonClass: "btn-success",
                confirmButtonText: "Yes, do it!",
                closeOnConfirm: false
            }).then(function() {
                $.ajax({
                    type:'POST',
                    url: "{{ url('store-student')}}",
                    data: formData,
                    cache:false,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                    if(res.success) {
                            swal('Success', 'Operation Successfully completed', 'success');
                            $("#student-modal").modal('hide');
                            var oTable = $('#list-student').dataTable();
                            oTable.fnDraw(false);
                            $("#btn-save").html('Submit');
                            $("#btn-save"). attr("disabled", false);
                        }else{
                            swal('Error', res.message, 'error');
                        }
                    }
                });
            }).catch(swal.noop);
        }
    });
   </script>
@endsection
