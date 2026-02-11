import Cookies from 'js-cookie';

const TOKEN_KEY = 'AUTH_TOKEN';
const USER_KEY = 'AUTH_USER';

export const saveAuthData = (token: string, user: any, rememberMe: boolean) => {
  const expires = rememberMe ? 7 : undefined;
  Cookies.set(TOKEN_KEY, token, { expires, secure: true, sameSite: 'strict' });
  Cookies.set(USER_KEY, JSON.stringify(user), { expires, secure: true, sameSite: 'strict' });
};

export const getAuthData = () => {
  const token = Cookies.get(TOKEN_KEY);
  const userRaw = Cookies.get(USER_KEY);
  return { token, user: userRaw ? JSON.parse(userRaw) : null };
};

export const clearAuth = () => {
  Cookies.remove(TOKEN_KEY);
  Cookies.remove(USER_KEY);
};
