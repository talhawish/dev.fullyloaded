<script>

function deleteWithAjax(id,variable){
	
	Swal.fire({
	  title: "Do You Really Want to Delete?",
	  text: "You won't be able to revert this!",
	  icon: 'warning',
	  showCancelButton: true,
	   confirmButtonColor: '#d33',
	  confirmButtonText: 'Delete',
	  cancelButtonColor: '#3085d6'
	}).then((result) => {
	  if (result.value) {
		$.ajax({
		 type: "get",
		url: variable+"/delete/"+id,
		data: {deleteDataID:id},
		success: function(data){
			console.log(data);
			if(data=="success")
			{ 
				document.getElementById(variable+"_"+id).remove();
				 Swal.fire(
				  'Deleted!',
				  'Deleted Successfully.',
				  'success'
				)
			}	
			else if(data=="unsuccessful")
			{
				 Swal.fire(
				  'Error!',
				  'Could Not be deleted.',
				  'error'
				)
			}
			else if(data=="superUser")
			{
				Swal.fire(
				  'Permission Error!',
				  'Super Profile Cannot Not be deleted.',
				  'error'
				)
			}
		},
		error: function(err)
		{
			console.log(err);
				Swal.fire(
				  'Error!',
				  'Internal Server Error',
				  'error'
				);
		}
	})
	}	
})
}

</script>