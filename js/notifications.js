
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

const Notice = Swal.mixin({
	buttonsStyling: false,
	customClass: {
		confirmButton: 'btn btn-primary px-4 mx-2',
		cancelButton: 'btn btn-outline-secondary px-4 mx-2',
		input: 'form-control w-auto mt-0',
		inputLabel: 'form-label'
	},
});

const ask = async question => {
	const answer = await Notice.fire({
		title: 'Please confirm',
		html: question,
		icon: 'question',
		showCancelButton: true,
		confirmButtonText: 'Yes',
		cancelButtonText: 'No',
	});
	if (answer.value) return true;
	return false;
}

const input = async question => {
	const {value: answer} = await Notice.fire({
		html: question,
		input: 'text',
		icon: 'question',
		showCancelButton: true,
	});
	return answer;
}

const getText = async question => {
	const { value: text } = await Notice.fire({
		input: "textarea",
		inputLabel: question,
		inputPlaceholder: "Type here...",
		inputAttributes: {
			"aria-label": "Type here"
		},
		showCancelButton: true,
	});
	return text;
}

const getFile = async title => {
	const { value: file } = await Notice.fire({
		inputLabel: title,
		input: "file",
		inputAttributes: {
			"accept": "image/*",
			"aria-label": "Upload picture"
		},
		showCancelButton: true,
		confirmButtonText: 'Upload',
		cancelButtonText: 'Cancel',
	});
	return file;
}

const getNumber = async prompt => {
	const { value: number } = await Notice.fire({
		input: 'number',
		inputLabel: prompt,
		// inputPlaceholder: 'Type here...',
		showCancelButton: true,
	});
	return number;
}

const alertError = async (message, title = '') => {
	return await Notice.fire({
		title, html: message, icon: 'error',
		confirmButtonText: 'Got it',
	});
}

const alertSuccess = async (message, title = '', timer) => {
	const config = {
		title, text: message, icon: 'success',
		confirmButtonText: 'Okay, thanks!',
	}
	if (timer) config.timer = timer;
	return await Notice.fire(config);
}

const alertWarning = async (message, title = '', timer) => {
	const config = {
		title, text: message, icon: 'warning',
		confirmButtonText: 'OK',
	}
	if (timer) config.timer = timer;
	return await Notice.fire(config);
}


export { toastr, ask, input, getText, getFile, getNumber, alertError, alertSuccess, alertWarning };