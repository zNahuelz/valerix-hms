import type { LoginResponse, User } from '@/schemas/auth';
import Cookies from 'js-cookie';

const TOKEN_KEY = 'AUTH_TOKEN';
const USER_KEY = 'AUTH_USER';

export const saveAuthData = (token: string, user: User, rememberMe: boolean) => {
  const expires = rememberMe ? 7 : undefined;
  const options = { expires, secure: true, sameSite: 'strict' as const };

  Cookies.set(TOKEN_KEY, token, options);
  Cookies.set(USER_KEY, JSON.stringify(user), options);
};

export const getAuthData = () => {
  const token = Cookies.get(TOKEN_KEY);
  const userRaw = Cookies.get(USER_KEY);
  return {
    token,
    user: userRaw ? (JSON.parse(userRaw) as User) : null,
  };
};

export const clearAuth = () => {
  Cookies.remove(TOKEN_KEY);
  Cookies.remove(USER_KEY);
};
