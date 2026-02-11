import ky from 'ky';
import { getAuthData, clearAuth } from './auth';
const API_URL = import.meta.env.VITE_API_URL;

export const http = ky.create({
  prefixUrl: API_URL,
  hooks: {
    beforeRequest: [
      (request) => {
        const { token } = getAuthData();
        if (token) request.headers.set('Authorization', `Bearer ${token}`);
        request.headers.set('Accept', 'application/json');
        request.headers.set('Content-Type', 'application/json');
      },
    ],
    afterResponse: [
      async (_request, _options, response) => {
        if (response.status === 401 || response.status === 403) {
          clearAuth();
          window.location.href = '/?expired=true';
        }
      },
    ],
  },
});
