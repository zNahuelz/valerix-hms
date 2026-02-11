import { createContext, useCallback, useContext, useState } from 'react';
import { getAuthData, saveAuthData, clearAuth } from '@/lib/auth';
import { useNavigate } from 'react-router';

const AuthContext = createContext<any>(null);

export const AuthProvider = ({ children }: { children: React.ReactNode }) => {
  const [auth, setAuth] = useState(getAuthData());

  const login = useCallback((data: any, remember: boolean) => {
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
      value={{ user: auth.user, token: auth.token, isAuthenticated: !!auth.token, login, logout }}
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
