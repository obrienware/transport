
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

const getText = async question => {
	const { value: text } = await Swal.fire({
		input: "textarea",
		inputLabel: question,
		inputPlaceholder: "Type here...",
		inputAttributes: {
			"aria-label": "Type here"
		},
		showCancelButton: true,
		customClass: {
			confirmButton: 'btn btn-primary',
			cancelButton: 'btn btn-outline-secondary'
		},
	});
	return text;
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

const alertWarning = async (message, title = '', timer) => {
	const config = {
		title, text: message, icon: 'warning',
		confirmButtonText: 'OK',
		customClass: {
			confirmButton: 'btn btn-primary',
			cancelButton: 'btn btn-outline-secondary'
		},
	}
	if (timer) config.timer = timer;
	return await Swal.fire(config);
}
