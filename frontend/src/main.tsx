import { createRoot } from 'react-dom/client';
import { RouterProvider } from 'react-router';
import { router } from './router/index.ts';
import './index.css';
import { TooltipProvider } from './components/ui/tooltip.tsx';
import { ThemeProvider } from './components/ThemeProvider.tsx';
import i18n from './lib/i18n.ts';
import { Toaster } from './components/ui/sonner.tsx';
import { HelmetProvider } from '@dr.pogodin/react-helmet';
import { AuthProvider } from './context/AuthContext.tsx';

createRoot(document.getElementById('root')!).render(
  <HelmetProvider>
    <AuthProvider>
      <ThemeProvider defaultTheme='light' storageKey='app-theme'>
        <TooltipProvider delayDuration={0}>
          <Toaster></Toaster>
          <RouterProvider router={router}></RouterProvider>
        </TooltipProvider>
      </ThemeProvider>
    </AuthProvider>
  </HelmetProvider>
);
