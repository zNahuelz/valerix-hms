import ky from 'ky';
import { getAuthData, clearAuth } from './auth';
import { redirect } from 'react-router';
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
      async (request, _options, response) => {
        const isLogin = request.url.endsWith('auth/login');
        if (isLogin) return;
        if (response.status === 401) {
          clearAuth();
          throw redirect('/?error=expired');
        }
        if (response.status === 403) {
          throw redirect('/dashboard?error=forbidden');
        }
      },
    ],
  },
});
