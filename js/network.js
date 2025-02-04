const post = async (url, data) => {
  const response = await fetch(url, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json;charset=utf-8',
    },
    body: JSON.stringify(data)
  });
  return (response.ok) ? await response.json() : false;
};

const get = async (url, params, optionalHeaders = {}) => {
  if (params) url = url + queryParams(params);
  const headers = Object.assign({'Content-Type': 'application/json;charset=utf-8'}, optionalHeaders);
  const response = await fetch(url, {
    method: 'GET',
    headers,
  });
  return (response.ok) ? await response.json() : false;
};

const queryParams = paramsObj => {
  const queryString = Object.keys(paramsObj).map(key => key + '=' + encodeURIComponent(paramsObj[key])).join('&');
  return (queryString.length > 0) ? `?${queryString}` : '';
};

export { post, get, queryParams };