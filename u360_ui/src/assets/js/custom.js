jQuery(document).ready(function () {
  /*===== select-color =======*/

  jQuery('select.form-control').css('color', '#BDBDBD');
  jQuery('select.form-control').change(function () {
    var current = jQuery('select.form-control').val();
    if (current != 'null') {
      jQuery(this).css('color', '#363853');
    } else {
      jQuery(this).css('color', '#BDBDBD');
    }
  });
});



/*================================================ window-load ================================================*/


jQuery(window).on('load', function () {

  /*===== select-color =======*/
  jQuery('select.form-control').css('color', '#BDBDBD');
  jQuery('select.form-control').change(function () {
    var current = jQuery('select.form-control').val();
    if (current != 'null') {
      jQuery(this).css('color', '#363853');
    } else {
      jQuery(this).css('color', '#BDBDBD');
    }

  });

  /*===== filter-dropdown =======*/

  // jQuery(".filter-icon").on("click", function () {
  //   jQuery(".filter-select").slideToggle(500);
  // });

  // jQuery(document).on("click",function(){
  //   jQuery(".filter-select").slideUp(500);
  // });

  // jQuery(".filter-icon, .filter-select").on('click', function (e) {
  //   e.stopPropagation();
  // });

  // $("#datepicker").datepicker( {
  //   format: "mm-yyyy",
  //   viewMode: "months", 
  //   minViewMode: "months"
  // });
  // $('.expire_date').datepicker({
  //   format: 'mm/yyyy',
  //   autoclose: true,
  //   minViewMode: 'months',
  //   startDate: 'y',
  //   endDate: '+10y',
  //   onSelect: function(dateText, inst) {
  //     debugger;
  //     // Update the form control value
  //     //this.myForm.controls['date'].setValue(dateText);
  
  //     // Trigger an input change event
  //     $(inst.input).trigger('change');
  //   }
  // }).on('change', function(e) {
  //   // Get the selected date
  //   debugger;
  //   var selectedDate = $(this).datepicker('getDate');
  //   var selectedDate = $(this).datepicker('getDate');
  //   var formattedDate = (selectedDate.getMonth() + 1) + '/' + selectedDate.getFullYear();
  //   $('.expire_date').val();
  //   // Do something with the selected date
  //   console.log(formattedDate);
  // });
});
















