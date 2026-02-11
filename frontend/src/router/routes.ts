import LoginView from '@/features/auth/views/LoginView';
import DashboardLayout from '@/layouts/DashboardLayout';
import Test from '@/test';
import { guestLoader, protectedLoader } from './loaders';

export const Routes = [
  {
    path: '/',
    Component: LoginView,
    loader: guestLoader(),
  },
  {
    path: '/dashboard',
    Component: DashboardLayout,
    loader: protectedLoader(),
    children: [{ index: true, Component: Test }],
  },
];
