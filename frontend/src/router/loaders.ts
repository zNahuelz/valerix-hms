import { redirect } from 'react-router';
import { getAuthData } from '@/lib/auth';

export const protectedLoader = (requiredPermissions?: string | string[]) => {
  return () => {
    const { token, user } = getAuthData();

    if (!token || !user) throw redirect('/');

    if (requiredPermissions) {
      const permsArray = Array.isArray(requiredPermissions)
        ? requiredPermissions
        : [requiredPermissions];

      const hasAnyPermission = permsArray.some((p) => user.permissions?.includes(p));

      if (!hasAnyPermission) {
        throw redirect('/unauthorized');
      }
    }

    return { user };
  };
};

export const guestLoader = () => {
  return () => {
    const { token, user } = getAuthData();
    if (token) {
      throw redirect('/dashboard');
    }
    return null;
  };
};
