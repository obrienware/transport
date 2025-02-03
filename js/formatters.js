const int = (value, _default = 0) => {
  if (value === undefined) return _default;
  let returnValue = parseInt(value.toString().replace(/[^\-0-9$.]/g, ''));
  return (isNaN(returnValue)) ? _default : returnValue;
};

const float = (value, _default = 0) => {
  let returnValue = parseFloat(value.toString().replace(/[^\-0-9$.]/g, ''));
  return (isNaN(returnValue)) ? _default : returnValue;
};

const decimal = (value, _decimals = 2) => {
  if (value === undefined || value === null || isNaN(parseFloat(value))) return '';
  value = float(value);
  const thisFormatter = new Intl.NumberFormat('en-US', {
    style: 'decimal',
    minimumFractionDigits: _decimals,
    maximumFractionDigits: _decimals,
  });
  return thisFormatter.format(value);
};

const formatter = new Intl.NumberFormat('en-US', {
  style: 'currency',
  currency: 'ZAR',
  minimumFractionDigits: 2
});

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


export { 
  int, 
  float, 
  decimal, 
  currency, 
  clean, 
  cleanString, 
  cleanUpper, 
  cleanLower, 
  cleanProper, 
  cleanDigits, 
  cleanNumber, 
  cleanPhone, 
  val, 
  cleanVal, 
  cleanUpperVal, 
  cleanLowerVal, 
  cleanProperVal, 
  cleanDigitsVal, 
  cleanNumberVal, 
  cleanPhoneVal, 
  checked 
};