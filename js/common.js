// This will form the basis of our global references
const app = {
	debug: true
}; 

loader = `<div class="loading text-center mt-4"></div>`;
report_loader = `
  <div class="form-row mt-4">
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>

    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
    <div class="col-3"><div class="mb-2 loading">&nbsp;</div></div>
  </div>
`;

// These are parameters for scroll-to-top
offset = 220;
duration = 500;



if ('undefined' !== typeof Dropzone) Dropzone.autoDiscover = false;


Array.prototype.move = function (from, to) {
  this.splice(to, 0, this.splice(from, 1)[0]);
};

const defaultToastrOptions = {
	toast: true,
	position: 'top-end',
	timer: 3000,
	showConfirmButton: false,
}
const toastr = {
	warning: (text = '', title = '') => {
		Swal.fire({...defaultToastrOptions, title, html: text, icon: 'warning'});
	},
	error: (text = '', title = '') => {
		Swal.fire({...defaultToastrOptions, title, html: text, icon: 'error'});
	},
	success: (text = '', title = '') => {
		Swal.fire({...defaultToastrOptions, title, html: text, icon: 'success'});
	}
}


/**
 * Some helper functions!
 */
const int = (value, _default = 0) => {
	if (value === undefined) return _default;
	let returnValue = parseInt(value.toString().replace(/[^\-0-9$.]/g, ''));
	return (isNaN(returnValue)) ? _default : returnValue
}
const float = (value, _default = 0) => {
	let returnValue = parseFloat(value.toString().replace(/[^\-0-9$.]/g, ''));
	return (isNaN(returnValue)) ? _default : returnValue
}
const decimal = (value, _decimals = 2) => {
	if (value === undefined || value === null || isNaN(parseFloat(value))) return '';
	value = float(value);
	const thisFormatter = new Intl.NumberFormat('en-US', {
		style: 'decimal',
		minimumFractionDigits: _decimals,
		maximumFractionDigits: _decimals,
	});
	return thisFormatter.format(value);
}
const formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'ZAR',
  minimumFractionDigits: 2
});
const currency = value => formatter.format(value);
const clean = value => $.trim(value);
const cleanString = value => ($.trim(value)) ? $.trim(value) : '';
const cleanUpper = value => clean(value).toUpperCase();
const cleanLower = value => clean(value).toLowerCase();
const cleanProper = value => properCase(clean(value));
const cleanDigits = value => value.replace(/\D/g,'');
const cleanNumber = value => value.replace(/[^0-9\.-]+/g,'');
const cleanPhone = value => new libphonenumber.AsYouType('US').input(value);

/* The following functions take their value from the given input selector */
const val = selector => $(selector).val();
const cleanVal = selector => clean($(selector).val());
const cleanUpperVal = selector => cleanUpper($(selector).val());
const cleanLowerVal = selector => cleanLower($(selector).val());
const cleanProperVal = selector => cleanProper($(selector).val());
const cleanDigitsVal = selector => cleanDigits($(selector).val());
const cleanNumberVal = selector => cleanNumber($(selector).val());
const cleanPhoneVal = selector => cleanPhone($(selector).val());

const checked = selector => $(selector).is(':checked');

String.prototype.toJSON = function () {
	return JSON.parse(this, function(k, v) {
		if (v && typeof v === 'object' && !Array.isArray(v)) {
			return Object.assign(Object.create(null), v);
		}
		return v;
	});
}

const wait = ms => new Promise(resolve => setTimeout(resolve, ms));

const ask = async question => {
	const answer = await Swal.fire({
		title: 'Please confirm',
		html: question,
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
		customClass: {
			confirmButton: 'btn btn-primary',
			cancelButton: 'btn btn-outline-secondary'
		},
	});
	if (answer.value) return true;
	return false;
}

const input = async question => {
	const {value: answer} = await Swal.fire({
		html: question,
		input: 'text',
		icon: 'question',
		showCancelButton: true,
		customClass: {
			confirmButton: 'btn btn-primary',
			cancelButton: 'btn btn-outline-secondary'
		},
	});
	return answer;
}

const alertError = async (message, title = '') => {
	return await Swal.fire({
		title, html: message, icon: 'error',
		confirmButtonText: 'Got it',
		customClass: {
			confirmButton: 'btn btn-primary',
			cancelButton: 'btn btn-outline-secondary'
		},
	});
}

const alertSuccess = async (message, title = '', timer) => {
	const config = {
		title, text: message, icon: 'success',
		confirmButtonText: 'Okay, thanks!',
		customClass: {
			confirmButton: 'btn btn-primary',
			cancelButton: 'btn btn-outline-secondary'
		},
	}
	if (timer) config.timer = timer;
	return await Swal.fire(config);
}


/**
 * Use our favorite fetch method to post json data
 * @param {string} url - The url to post the data to
 * @param {json} data - The JSON object to post
 * @returns {promise} of type response (see fetch)
 */
const post = async (url, data) => {
	const response = await fetch(url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json;charset=utf-8',
			// 'Authorization': `Bearer ${window.token}`
		},
		body: JSON.stringify(data)
	});
	return (response.ok) ? await response.json() : false;
}
const get = async (url, params, optionalHeaders = {}) => {
	if (params) url = url + queryParams(params);
  const headers = Object.assign({'Content-Type': 'application/json;charset=utf-8'}, optionalHeaders);
	const response = await fetch(url, {
		method: 'GET',
		headers,
	});
	return (response.ok) ? await response.json() : false;
}
const queryParams = paramsObj => {
	const queryString = Object.keys(paramsObj).map(key => key + '=' + encodeURIComponent(paramsObj[key])).join('&');
	return (queryString.length > 0) ? `?${queryString}` : '';
}

const calendarFormats = {
  sameDay: '[Today]',
  nextDay: '[Tomorrow]',
  nextWeek: 'dddd',
  lastDay: '[Yesterday]',
  lastWeek: '[Last] dddd',
  sameElse: 'M/D/YYYY'
}

const bindPopovers = ƒ => {
  $('.pop').popover({trigger: 'manual' , html: true, animation:false})
    .on('mouseenter', ƒ => {
      const _this = ƒ.currentTarget;
      $(_this).popover('show');
      $('.popover').on('mouseleave', ƒ => $(_this).popover('hide'));
    }).on('mouseleave', ƒ => {
			const _this = ƒ.currentTarget;
			if (!$('.popover:hover').length) $(_this).popover('hide');
      // setTimeout(ƒ => {
      //   if (!$('.popover:hover').length) {
      //     $(_this).popover('hide');
      //   }
      // }, 300);
		});
}

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
String.prototype.toProperCase = function () {
	return this.toLowerCase().replace(/\b((m)(a?c))?(\w)/g,
		function ($1, $2, $3, $4, $5) {
			if ($2) {
				return $3.toUpperCase() + $4 + $5.toUpperCase();
			}
			return $1.toUpperCase();
		}
	);
}

String.prototype.toSentenceCase = function () {
	// Split the string into sentences
  const sentences = this.split(/([.!?]\s*)/);

  // Convert each sentence to sentence case
  for (let i = 0; i < sentences.length; i += 2) {
    sentences[i] = sentences[i].charAt(0).toUpperCase() + sentences[i].slice(1).toLowerCase();
  }

  // Join the sentences back together
  return sentences.join('');
}

//pads left
String.prototype.lpad = function(padString, length) {
  var str = this;
  while (str.length < length)
    str = padString + str;
  return str;
}

//pads right
String.prototype.rpad = function(padString, length) {
  var str = this;
  var paddingCount = length - str.length;
  if (paddingCount > 0) {
    for (var i=0; i<paddingCount; i++) {
      str = str + padString;
    }
  }
  return str;
}


const ago = seconds => {
  let interval = seconds / 31536000;
  if (interval > 1) {
    const returnValue = Math.floor(interval);
    return returnValue + ((returnValue === 1) ? ' year':' years');
  }
  interval = seconds / 2592000;
  if (interval > 1) {
    const returnValue = Math.floor(interval);
    return returnValue + ((returnValue === 1) ? ' month':' months');
  }
  interval = seconds / 86400;
  if (interval > 1) {
    const returnValue = Math.floor(interval);
    return returnValue + ((returnValue === 1) ? ' day':' days');
  }
  interval = seconds / 3600;
  if (interval > 1) {
    const returnValue = Math.floor(interval);
    return returnValue + ((returnValue === 1) ? ' hour':' hours');
  }
  interval = seconds / 60;
  if (interval > 1) {
    const returnValue = Math.floor(interval);
    return returnValue + ((returnValue === 1) ? ' minute':' minutes');
  }
  const returnValue = Math.floor(interval);
  return returnValue + ((returnValue === 1) ? ' second':' seconds');
}



/**
* This should work with associative array (like filter works with regular arrays)
*/
const objFilter = (obj, func) => {
  const tmpArray = Object.entries(obj);
  let newObject = {};
  tmpArray.map(arr => {
    if (func(arr[1])) newObject[arr[0]] = arr[1];
  });
  return {...newObject};
}
/**
* This should work with associative array (like reduce works with regular arrays), but only for string results at this time.
*/
const objReduce = (obj, func, accumulator) => {
  let returnValue = '';
  for (key in obj) {
    returnValue += func(accumulator, obj[key]);
  }
  return returnValue;
}
const objSort = obj => {
  let keys = [];
  for (key in obj) keys.push(key);
  keys.sort();
  let returnObject = {}
  keys.forEach(key => returnObject[key] = obj[key]);
  return {...returnObject}
}

if ($.fn?.dataTable?.defaults) {
  /* Set defaults for DataTables */
  $.extend(true, $.fn.dataTable.defaults, {
  	// dom: "<'d-flex flex-wrap'<'mr-2'f><l><'ml-auto'B>><'row my-2'<'col-sm-12'tr>><'d-flex flex-wrap'<i><'ml-auto'p>>",
  	// dom: "<'d-flex flex-wrap'<'me-2'f><l><'ms-auto'B>><'row my-0'<'col'tr>><'d-flex flex-wrap'<><'ms-auto'>>",
		layout: {
			topStart: ['search','pageLength'],
			topEnd: []
		},
    stateSave: true,
  	language: {
			search: 'Filter records', 
			searchPlaceholder: 'Text to filter',
			lengthMenu:
            'Display <select class="form-select form-select-sm">' +
            '<option value="5">5</option>' +
            '<option value="10">10</option>' +
            '<option value="20">20</option>' +
            '<option value="30">30</option>' +
            '<option value="40">40</option>' +
            '<option value="50">50</option>' +
            '<option value="-1">All</option>' +
            '</select> records'
		},
  	buttons: [],
  	columnDefs: [{
  		targets: 'no-sort',
  		searchable: false,
  		orderable: false,
  	}, {
  		targets: 'no-search',
  		searchable: false,
  	}],
  	paging: false
  });
  // $.fn.dataTable.moment('YYYY-M-D');
  // $.fn.dataTable.moment('YYYY-M-D h:mm a');
}


$(async ƒ => {

	/**
	 * Reformat the date in a table cell with a ".date" class
	 */
	$('td.date').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD');
		if (value.isValid()) {
			$(item).html(value.format('M/D/YYYY'));
		}
	});

	/**
	 * Reformat the date/time in a table cell with a ".datetime" class
	 */
	$('td.datetime, a.datetime').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
		if (value.isValid()) {
			$(item).html(value.format('D/M/YY h:mm a'));
		}
	});

	/**
	 * Reformat the time in a table cell with a ".time" class
	 */
	$('td.time,span.time').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
		if (value.isValid()) {
			$(item).html(value.format('h:mma'));
		}
	});

	let pollTimeoutErrors = 0;

  bindPopovers();

  /**
   * The following makes the multi-level dropdown menus work
   */
  $('.dropdown-menu [data-toggle="dropdown"]').on('click', ƒ => {
    ƒ.preventDefault();
    ƒ.stopPropagation();
		const self = ƒ.currentTarget;

		$('.dropdown-submenu.show').removeClass('show');
		$(self).siblings().toggleClass('show');

    if (!$(self).next().hasClass('show')) {
      $(self).parents('.dropdown-menu').first().find('.show').removeClass('show');
    }

		$(self).parents('.dropdown-menu.show').prev().on('hidden.bs.dropdown', ƒ => {
      $('.dropdown-menu.show').removeClass('show');
    });
  });


  // setInterval(async ƒ => {
  //   const resp = await post(`ajax/ping.php`);
  //   if (resp.result == false) return location.reload();
  // }, 60 *1000);

	$(document).ajaxStop(reFormat);

});

function reFormat() {
	$('[data-bs-toggle="tooltip"]').tooltip();

	/**
	 * Reformat the date in a table cell with a ".date" class
	 */
	$('td.date:not(.short):not(.formatted)').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD');
		if (value.isValid()) {
			$(item).addClass('formatted').html(value.format('M/D/YYYY'));
		}
	});

	/**
	 * Reformat the date/time in a table cell with a ".datetime" class
	 */
	$('td.datetime:not(.short):not(.formatted), a.datetime:not(.short):not(.formatted)').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
		if (value.isValid()) {
			$(item).addClass('formatted').html(value.format('D/M/YY h:mm a'));
		}
	});

	/**
	 * Reformat the time in a table cell with a ".time" class
	 */
	$('td.time:not(.short):not(.formatted),span.time:not(.short):not(.formatted)').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
		if (value.isValid()) {
			$(item).addClass('formatted').html(value.format('h:mma'));
		}
	});


	$('td.datetime.short:not(.formatted)').toArray().forEach(item => {
		const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
		if (value.isValid()) {
			$(item).addClass('formatted').html(value.format('M/D h:mma'));
		}
	});

}

function uuidv4() {
  return "10000000-1000-4000-8000-100000000000".replace(/[018]/g, c =>
    (+c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> +c / 4).toString(16)
  );
}

// The function returns the calculated luminance value, which ranges from 0 (black) to 1 (white).
function luminance(r, g, b) {
  // Convert RGB values to the range 0-1
  r /= 255;
  g /= 255;
  b /= 255;

  // Apply the luminance formula
  return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}

function hexToRgba(hex, alpha) {
  // Remove the hash at the start if it's there
  hex = hex.replace(/^#/, '');

  // Parse the r, g, b values
  let r = parseInt(hex.substring(0, 2), 16);
  let g = parseInt(hex.substring(2, 4), 16);
  let b = parseInt(hex.substring(4, 6), 16);

  // Return the RGBA color
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}


