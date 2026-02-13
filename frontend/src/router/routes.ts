import LoginView from '@/features/auth/views/LoginView';
import DashboardLayout from '@/layouts/DashboardLayout';
import { guestLoader, protectedLoader } from './loaders';
import DashboardView from '@/features/shared/views/DashboardView';
import SupplierIndexView from '@/features/supplier/views/SupplierIndexView';
import SupplierStoreView from '@/features/supplier/views/SupplierStoreView';

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
    children: [
      {
        children: [
          {
            children: [{ index: true, Component: DashboardView }],
          },
          {
            path: 'supplier',
            children: [
              {
                children: [
                  {
                    index: true,
                    Component: SupplierIndexView,
                    loader: protectedLoader(['supplier:index', 'sys:admin']),
                  },
                ],
              },
              {
                path: 'store',
                children: [
                  {
                    index: true,
                    Component: SupplierStoreView,
                    loader: protectedLoader(['supplier:store', 'sys:admin']),
                  },
                ],
              },
            ],
          },
        ],
      },
    ],
  },
];
