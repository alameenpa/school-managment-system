@extends('layouts.master')
@section('content')
      <div class="container mt-5 content">
          <!-- score list -->
         @if(!empty($subjects))
         <div class="row">
            <div class="col-lg-12 margin-tb">
               <div class="pull-left">
                  <h2><i class="fa fa-bookmark" aria-hidden="true"></i>&nbsp;Score Details</h2>
               </div>
               <div class="mb-3" style="float: right;">
                  <a class="btn btn-info" onClick="addScore()" href="javascript:void(0)"><i class="fa fa-bookmark-o" aria-hidden="true"></i>&nbsp;New Score</a>
                  &nbsp;<a href="{{url('/')}}">Go to Students List</a>
               </div>
            </div>
            <div class="card-body">
                <table class="table table-bordered" id="list-score">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>
                           <table class="table">
                              <tr>
                        @foreach($subjects as $subject)
                            <th style="width:100px;">{{$subject}}</th>
                        @endforeach
                        </tr>
                        </table>
                        </th>
                        <th>Term</th>
                        <th>Total Marks</th>
                        <th>Created On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                </table>
            </div>
         </div>
         @endif
      </div>

      <!-- boostrap score model -->
      <div class="modal fade" id="score-modal" aria-hidden="true">
         <div class="modal-dialog modal-lg">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="ScoreModal"></h4>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <form action="javascript:void(0)" id="scoreForm" name="scoreForm" class="form-horizontal" method="POST" enctype="multipart/form-data">
                     <input type="hidden" name="score_id" id="score_id">
                     <div class="form-group">
                     <label for="student_id" class="col-sm-2 control-label">Student</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="student_id" id="student_id" required="">
                                <option value="">--select--</option>
                                @foreach($students as $ky => $student)
                                <option value="{{$ky}}">{{$student}}</option>
                                @endforeach
                           </select>
                        </div>
                     </div>
                     <div class="form-group">
                     <label for="term_id" class="col-sm-2 control-label">Term</label>
                        <div class="col-sm-12">
                            <select class="form-control" name="term_id" id="term_id" required="">
                                <option value="">--select--</option>
                                @foreach($terms as $ky => $term)
                                <option value="{{$ky}}">{{$term}}</option>
                                @endforeach
                            </select>
                        </div>
                     </div>
                     <div class="form-group">
                     <fieldset>
                        <legend><label class="col-sm-12 control-label">Score Details</label></legend>
                        <div class="col-sm-11 pull-right">
                           <table  class="table table-borderless">
                                 @foreach($subjects as $ky => $subject)
                                       <tr><td>{{$subject}}</td><td><input type="number" class="form-control score" name="scores[{{$ky}}]" id="score_{{$ky}}" min="0" max="100" required=""/></td></tr>
                                 @endforeach
                           </table>
                        </div>
                        </fieldset>
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
   </body>

   <script type="text/javascript">
   $(document).ready( function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function addScore() {
        $('#scoreForm').trigger("reset");
        $('#ScoreModal').html("New Score");
        $('#score-modal').modal('show');
        $('#score_id').val('');
    }

    $('#list-score').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ url('list-score') }}",
        columns: [
            {
               data: 'id',
               name: 'id'
            },
            {
               data: null,
               name: 'student_id',
               render:function(o) {
                    return o.student? o.student.name: '';
               }
            },
            {
               data: null,
               name: null,
               orderable: false,
               render:function(o) {
                  let html = "<table class='table'><tr>";
                  $.each(o.score_details, function(key, value) {
                     html += "<td class='text-center' style='width:100px;'>"+((value.mark)? value.mark: 0)+ "</td>";
                  });
                  return html + "</tr></table>";
               }
            },
            {
               data: null,
               name: 'term_id',
               render:function(o) {
                    return o.term? o.term.name: '';
               }
            },
            {
               data: null,
               name: 'Total Marks',
               orderable: false,
               render:function(o) {
                  let total = 0;
                  $.each(o.score_details, function(key, value) {
                     total += value.mark;
                  });
                  return total;
               }
            },
            {
               data: null,
               name: 'created_at',
               render:function(o) {
                  return o.created_at? moment(o.created_at).format('MMM DD, YYYY hh:mm A'): '';
               }
            },
            {
               data: 'action',
               name: 'action',
               orderable: false
            },
        ],
        'columnDefs': [
         {
               "targets": [0, 2, 3,4],
               "className": "text-center",
         }],
         "bFilter": false,
        order: [[0, 'ASC']]
    });

    function validateForm() {
       //validate input values
       if($('#student_id').val() == "" || $('#student_id').val() == undefined || $('#student_id').val() == null) {
         swal('Error', "Please choose any student", 'error');
         return false;
        }else if($('#term_id').val() == "" || $('#term_id').val() == undefined || $('#term_id').val() == null) {
         swal('Error', "Please choose any term", 'error');
         return false;
        }

        var reqlength = $('.score').length;
        var value = $('.score').filter(function () {
            return this.value != '';
         });

         if (value.length>=0 && (value.length !== reqlength)) {
            swal('Error', "Please fill score details", 'error');
            return false;
         } else {
            return true;
         }
    }

    $('#scoreForm').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        if(validateForm()){
            swal({
                  title: "Are you sure?",
                  text: "Your want to save the score details",
                  type: "success",
                  showCancelButton: true,
                  confirmButtonClass: "btn-success",
                  confirmButtonText: "Yes, do it!",
                  closeOnConfirm: false
            }).then(function() {
                  $.ajax({
                        type:'POST',
                        url: "{{ url('store-score')}}",
                        data: formData,
                        cache:false,
                        contentType: false,
                        processData: false,
                        success: function(res) {
                           if(res.success) {
                              swal('Success', 'Operation Successfully completed', 'success');
                              $("#score-modal").modal('hide');
                              var oTable = $('#list-score').dataTable();
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


    function editFunc(id){
        $.ajax({
            type:"POST",
            url: "{{ url('edit-score') }}",
            data: {'id':id},
            dataType: 'json',
            success: function(res){
                $('#ScoreModal').html("Edit Score");
                $('#score-modal').modal('show');
                $('#score_id').val(res.id);
                $('#student_id').val(res.student_id);
                $('#term_id').val(res.term_id);
                $.each(res.score_details, function(key, value) {
                  $('#score_' + value.subject_id).val(value.mark);
                });
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
                url: "{{ url('delete-score') }}",
                data: { id: id },
                dataType: 'json',
                success: function(res){
                  if(res.success) {
                     swal('Success', 'Operation Successfully completed', 'success');
                     var oTable = $('#list-score').dataTable();
                     oTable.fnDraw(false);
                  }else{
                     swal('Error', res.message, 'error');
                  }
                }
            });
         }).catch(swal.noop);
    }
   </script>
@endsection
