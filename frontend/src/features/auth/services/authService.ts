import { http } from '@/lib/httpWrapper';

export const loginRequest = async (username: string, password: string) => {
  return await http.post('auth/login', { json: { username, password } }).json<any>();
};
