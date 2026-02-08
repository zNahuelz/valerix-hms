import LoginView from '@/features/auth/views/LoginView';
import DashboardLayout from '@/layouts/DashboardLayout';
import Test from '@/test';

export const Routes = [
  {
    path: '/',
    Component: LoginView,
    handle: { title: 'Index' },
  },
  {
    path: '/test',
    Component: DashboardLayout,
    children: [{ index: true, Component: Test }],
  },
];
