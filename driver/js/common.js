window.app = {};
app.debug = true;

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
