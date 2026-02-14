import { createContext, useCallback, useContext, useState } from 'react';
import { getAuthData, saveAuthData, clearAuth } from '@/lib/auth';
import { useNavigate } from 'react-router';
import type { LoginResponse, User } from '@/schemas/auth';

interface AuthContextType {
  user: User | null;
  token: string | undefined;
  isAuthenticated: boolean;
  login: (data: LoginResponse, remember: boolean) => void;
  logout: () => void;
}

const AuthContext = createContext<AuthContextType | null>(null);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [auth, setAuth] = useState(getAuthData());

  const loginAction = useCallback((data: LoginResponse, remember: boolean) => {
    saveAuthData(data.token, data.user, remember);
    setAuth({ token: data.token, user: data.user });
  }, []);

  const logout = useCallback(() => {
    clearAuth();
    setAuth({ token: undefined, user: null });
    window.location.href = '/';
  }, []);

  return (
    <AuthContext.Provider
      value={{
        user: auth.user,
        token: auth.token,
        isAuthenticated: !!auth.token,
        login: loginAction,
        logout,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) throw new Error('useAuth must be used within AuthProvider');
  return context;
};
