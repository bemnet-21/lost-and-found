const BASE_URL = 'http://localhost/lost-and-found/server/api';

export async function apiFetch(endpoint, options = {}) {
  const url = `${BASE_URL}${endpoint}`;
  
  const defaultOptions = {
    credentials: 'include', // Important for sending/receiving session cookies
    headers: {
      'Accept': 'application/json',
    },
  };

  // Only set Content-Type to application/json if we're not sending FormData
  if (!(options.body instanceof FormData)) {
    defaultOptions.headers['Content-Type'] = 'application/json';
  }

  const finalOptions = {
    ...defaultOptions,
    ...options,
    headers: {
      ...defaultOptions.headers,
      ...options.headers,
    },
  };

  try {
    const response = await fetch(url, finalOptions);
    const data = await response.json();
    return { status: response.status, data };
  } catch (error) {
    console.error('API Fetch Error:', error);
    throw error;
  }
}
