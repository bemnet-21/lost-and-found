const BASE_URL = 'https://lost-and-found-production-7e24.up.railway.app/';

export async function apiFetch(endpoint, options = {}) {
  const url = `${BASE_URL}${endpoint}`;
  
  const defaultOptions = {
    credentials: 'include', 
    headers: {
      'Accept': 'application/json',
    },
  };

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
