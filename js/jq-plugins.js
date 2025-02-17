(function ($) {

  const properCase = s => {
    return s.toLowerCase().replace(/\b((m)(a?c))?(\w)/g,
      function ($1, $2, $3, $4, $5) {
        if ($2) {
          return $3.toUpperCase() + $4 + $5.toUpperCase();
        }
        return $1.toUpperCase();
      }
    );
  }  

  $.fn.cleanVal = function (newValue) {
    if (newValue === undefined) return $.trim(this.val());
    return this.each(function () {
      $(this).val(newValue);
    });
  };

  $.fn.cleanUpperVal = function (newValue) {
    if (newValue === undefined) return $.trim(this.val()).toUpperCase();
    return this.each(function () {
      $(this).val(newValue);
    });
  };

  $.fn.cleanLowerVal = function (newValue) {
    if (newValue === undefined) return $.trim(this.val()).toLowerCase();
    return this.each(function () {
      $(this).val(newValue);
    });
  };

  $.fn.cleanProperVal = function (newValue) {
    if (newValue === undefined) return properCase($.trim(this.val()));
    return this.each(function () {
      $(this).val(newValue);
    });
  };

  $.fn.cleanDigitsVal = function (newValue) {
    if (newValue === undefined) return $.trim(this.val()).replace(/\D/g, '');
    return this.each(function () {
      $(this).val(newValue);
    });
  };

  $.fn.cleanNumberVal = function (newValue) {
    if (newValue === undefined) return $.trim(this.val().replace(/[^0-9.-]/g, ''));
    return this.each(function () {
      $(this).val(newValue);
    });
  }

  $.fn.floatVal = function (newValue) {
    if (newValue === undefined) return parseFloat($.trim(this.val().replace(/[^0-9.-]/g, '')));
    return this.each(function () {
      $(this).val(newValue);
    });
  }
  $.fn.intVal = function (newValue) {
    if (newValue === undefined) return parseInt($.trim(this.val().replace(/[^0-9.-]/g, '')), 10);
    return this.each(function () {
      $(this).val(newValue);
    });
  }
  $.fn.isChecked = function (newValue) {
    if (newValue === undefined) return this.prop('checked');
    return this.each(function () {
      $(this).prop('checked', newValue);
    });
  }

})(jQuery);