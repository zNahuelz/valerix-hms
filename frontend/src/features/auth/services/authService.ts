import { http } from '@/lib/httpWrapper';
import { LoginResponse as LoginResponseSchema, type LoginResponse } from '@/schemas/auth';

export const login = async (username: string, password: string): Promise<LoginResponse> => {
  const json = await http.post('auth/login', { json: { username, password } }).json();
  return LoginResponseSchema.parse(json);
};
